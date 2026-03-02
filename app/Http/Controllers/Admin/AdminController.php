<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ContactMessage;
use App\Mail\ProducerApprovedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // ── Middleware : accès admin uniquement ────────────────────
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || !Auth::user()->isAdmin()) {
                return redirect()->route('admin.login');
            }
            return $next($request);
        });
    }

    // ── Dashboard principal ────────────────────────────────────
    public function dashboard()
    {
        $stats = $this->getStats();
        $recentOrders   = Order::with('buyer:id,name,email')->latest()->limit(5)->get();
        $recentContacts = ContactMessage::latest()->limit(5)->get();
        $pendingProducers = User::producers()->pending()->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentContacts', 'pendingProducers'));
    }

    // ── Utilisateurs ───────────────────────────────────────────
    public function users(Request $request)
    {
        $role   = $request->role ?? 'all';
        $status = $request->status ?? 'all';
        $search = $request->search;

        $q = User::query();
        if ($role   !== 'all') $q->where('role', $role);
        if ($status !== 'all') $q->where('status', $status);
        if ($search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name', 'like', "%$search%")
                   ->orWhere('email', 'like', "%$search%");
            });
        }
        $users = $q->withCount('products', 'orders')->latest()->paginate(20);

        return view('admin.users', compact('users', 'role', 'status', 'search'));
    }

    // ── Produits ───────────────────────────────────────────────
    public function products(Request $request)
    {
        $q = Product::with('producer:id,name,company');
        if ($cat  = $request->category) $q->where('category', $cat);
        if ($zone = $request->zone)     $q->where('zone', $zone);
        if ($s    = $request->search)   $q->where('name', 'like', "%$s%");

        $products  = $q->latest()->paginate(20);
        $producers = User::producers()->active()->get(['id','name','company']);

        return view('admin.products', compact('products', 'producers'));
    }

    // ── Commandes ──────────────────────────────────────────────
    public function orders(Request $request)
    {
        $q = Order::with(['buyer:id,name,email', 'items']);
        if ($status = $request->status) $q->where('status', $status);

        $orders = $q->latest()->paginate(20);
        return view('admin.orders', compact('orders'));
    }

    // ── Messages de contact ────────────────────────────────────
    public function contacts(Request $request)
    {
        $status = $request->status ?? 'all';
        $q = ContactMessage::query();
        if ($status !== 'all') $q->where('status', $status);

        $messages    = $q->latest()->paginate(20);
        $unreadCount = ContactMessage::unread()->count();

        return view('admin.contacts', compact('messages', 'unreadCount', 'status'));
    }

    // ── Voir un message ────────────────────────────────────────
    public function showContact(ContactMessage $message)
    {
        $message->markAsRead();
        return view('admin.contact-show', compact('message'));
    }

    // ── Producteurs (approbation) ──────────────────────────────
    public function producers(Request $request)
    {
        $status   = $request->status ?? 'pending';
        $producers = User::producers()
            ->where('status', $status)
            ->withCount('products')
            ->latest()
            ->paginate(20);

        $pendingCount = User::producers()->pending()->count();

        return view('admin.producers', compact('producers', 'status', 'pendingCount'));
    }

    // ── Actions utilisateurs ───────────────────────────────────
    public function approveProducer(User $user)
    {
        $user->update(['status' => 'active', 'approved_at' => now()]);

        try {
            Mail::to($user->email)->send(new ProducerApprovedMail($user));
        } catch (\Exception $e) {
            logger('Producer approved mail failed: ' . $e->getMessage());
        }

        return back()->with('success', "Producteur {$user->name} approuvé avec succès.");
    }

    public function suspendUser(User $user)
    {
        $user->update(['status' => 'suspended']);
        return back()->with('success', "Compte suspendu.");
    }

    public function reactivateUser(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', "Compte réactivé.");
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return back()->with('success', "Compte supprimé.");
    }

    // ── Actions commandes ──────────────────────────────────────
    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,delivered,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Statut de la commande mis à jour.');
    }

    // ── Toggle disponibilité produit ───────────────────────────
    public function toggleProduct(Product $product)
    {
        $product->update(['available' => !$product->available]);
        return back()->with('success', $product->name . ' : disponibilité mise à jour.');
    }

    // ── Delete produit ─────────────────────────────────────────
    public function deleteProduct(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Produit supprimé.');
    }

    // ── Stats pour dashboard ───────────────────────────────────
    private function getStats(): array
    {
        $monthlyRevenue = [];
        $monthlyOrders  = [];
        for ($i = 5; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $label = $date->locale('fr')->isoFormat('MMM');
            $rev   = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', '!=', 'cancelled')
                ->sum('total');
            $ord = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyRevenue[] = ['label' => $label, 'value' => $rev];
            $monthlyOrders[]  = ['label' => $label, 'value' => $ord];
        }

        return [
            'producers'         => User::producers()->active()->count(),
            'pending_producers' => User::producers()->pending()->count(),
            'consumers'         => User::consumers()->count(),
            'products'          => Product::count(),
            'available_products'=> Product::available()->count(),
            'orders'            => Order::count(),
            'revenue'           => Order::where('status', '!=', 'cancelled')->sum('total'),
            'kg_sold'           => OrderItem::sum('quantity'),
            'unread_contacts'   => ContactMessage::unread()->count(),
            'monthly_revenue'   => $monthlyRevenue,
            'monthly_orders'    => $monthlyOrders,
            'category_data'     => Product::selectRaw('category, COUNT(*) as count')->groupBy('category')->pluck('count', 'category'),
        ];
    }

    // ── Connexion admin ────────────────────────────────────────
    public function loginForm()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            if (Auth::user()->isAdmin()) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
            Auth::logout();
        }

        return back()->withErrors(['email' => 'Identifiants admin incorrects.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('admin.login');
    }
}
