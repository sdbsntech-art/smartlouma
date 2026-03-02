<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Mail\OrderConfirmationMail;
use App\Mail\NewOrderAdminMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    // ── Passer une commande ────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Connectez-vous pour commander.'], 401);
        if (!$user->isConsumer() && !$user->isAdmin()) {
            return response()->json(['error' => 'Seuls les restaurateurs peuvent commander.'], 403);
        }

        $data = $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.id'       => 'required|integer|exists:products,id',
            'items.*.qty'      => 'required|integer|min:1|max:50',
            'delivery_address' => 'nullable|string',
        ]);

        // Paramètres livraison
        $freeThreshold = 20000;
        $deliveryFee   = 5000;

        try {
            DB::beginTransaction();

            $subtotal  = 0;
            $itemsData = [];

            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['id']);

                if (!$product || !$product->available) {
                    DB::rollBack();
                    return response()->json([
                        'error' => "Le produit '{$product?->name}' n'est plus disponible.",
                    ], 422);
                }

                if ($product->quantity < $item['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'error' => "Stock insuffisant pour {$product->name} (disponible: {$product->quantity} kg).",
                    ], 422);
                }

                $lineTotal = $product->price * $item['qty'];
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product'    => $product,
                    'qty'        => $item['qty'],
                    'unit_price' => $product->price,
                    'total'      => $lineTotal,
                ];
            }

            $delivery = $subtotal >= $freeThreshold ? 0 : $deliveryFee;
            $total    = $subtotal + $delivery;

            // Créer la commande
            $order = Order::create([
                'reference'        => Order::generateReference(),
                'buyer_id'         => $user->id,
                'subtotal'         => $subtotal,
                'delivery_fee'     => $delivery,
                'total'            => $total,
                'delivery_address' => $data['delivery_address'] ?? null,
            ]);

            // Créer les lignes + déduire le stock
            foreach ($itemsData as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'quantity'     => $item['qty'],
                    'unit_price'   => $item['unit_price'],
                    'total_price'  => $item['total'],
                ]);

                $item['product']->decrement('quantity', $item['qty']);
                $item['product']->increment('sold_qty', $item['qty']);
            }

            DB::commit();

            // Emails
            $order->load('buyer', 'items');
            try {
                Mail::to($user->email)->send(new OrderConfirmationMail($order));
                Mail::to(config('app.admin_email'))->send(new NewOrderAdminMail($order));
            } catch (\Exception $e) {
                logger('Order mail failed: ' . $e->getMessage());
            }

            return response()->json([
                'message'   => 'Commande confirmée ! Livraison demain matin.',
                'reference' => $order->reference,
                'total'     => $order->total,
                'order'     => $order,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            logger('Order creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la commande. Réessayez.'], 500);
        }
    }

    // ── Mes commandes ──────────────────────────────────────────
    public function myOrders(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Non authentifié.'], 401);

        $orders = Order::with('items')
            ->where('buyer_id', $user->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    // ── Toutes les commandes (admin) ───────────────────────────
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user?->isAdmin()) return response()->json(['error' => 'Accès refusé.'], 403);

        $orders = Order::with(['buyer:id,name,email', 'items'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return response()->json($orders);
    }

    // ── Mettre à jour le statut (admin) ───────────────────────
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();
        if (!$user?->isAdmin()) return response()->json(['error' => 'Accès refusé.'], 403);

        $data = $request->validate([
            'status' => 'required|in:pending,confirmed,delivered,cancelled',
        ]);

        $order->update($data);

        return response()->json([
            'message' => 'Statut mis à jour.',
            'status'  => $order->status,
        ]);
    }
}
