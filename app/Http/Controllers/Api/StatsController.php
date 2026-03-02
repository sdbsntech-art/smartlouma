<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    // ── Stats publiques (homepage) ─────────────────────────────
    public function public(): JsonResponse
    {
        $producers  = User::producers()->active()->count();
        $consumers  = User::consumers()->active()->count();
        $totalKg    = OrderItem::sum('quantity');
        $products   = Product::available()->count();

        return response()->json([
            'producers'  => $producers,
            'consumers'  => $consumers,
            'total_kg'   => $totalKg,
            'products'   => $products,
        ]);
    }

    // ── Stats admin (dashboard) ────────────────────────────────
    public function admin(): JsonResponse
    {
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');
        $totalOrders  = Order::count();
        $totalKg      = OrderItem::sum('quantity');

        // Ventes 6 derniers mois
        $monthlyData = collect(range(5, 0))->map(function ($offset) {
            $date = now()->subMonths($offset);

            $orders = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', '!=', 'cancelled');

            $qty = OrderItem::whereHas('order', function ($q) use ($date) {
                $q->whereYear('created_at', $date->year)
                  ->whereMonth('created_at', $date->month)
                  ->where('status', '!=', 'cancelled');
            })->sum('quantity');

            return [
                'label'   => $date->locale('fr')->isoFormat('MMM'),
                'orders'  => (clone $orders)->count(),
                'revenue' => (clone $orders)->sum('total'),
                'qty'     => $qty,
            ];
        });

        // Top produits vendus
        $topProducts = Product::select('products.*')
            ->selectRaw('COALESCE(SUM(oi.quantity), 0) as total_sold')
            ->leftJoin('order_items as oi', 'products.id', '=', 'oi.product_id')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Répartition par catégorie
        $categoryBreakdown = Product::select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get();

        return response()->json([
            'total_producers'  => User::producers()->active()->count(),
            'pending_producers'=> User::producers()->pending()->count(),
            'total_consumers'  => User::consumers()->count(),
            'total_products'   => Product::count(),
            'available_products' => Product::available()->count(),
            'total_orders'     => $totalOrders,
            'total_revenue'    => $totalRevenue,
            'total_kg_sold'    => $totalKg,
            'monthly_sales'    => $monthlyData,
            'top_products'     => $topProducts,
            'category_breakdown' => $categoryBreakdown,
        ]);
    }
}
