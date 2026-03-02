<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // ── Liste publique (marketplace) ───────────────────────────
    public function index(Request $request): JsonResponse
    {
        $q = Product::with('producer:id,name,company,zone')
            ->available();

        if ($s = $request->search) {
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")
                   ->orWhere('description', 'like', "%$s%");
            });
        }

        if ($cat = $request->category) $q->where('category', $cat);
        if ($zone = $request->zone)    $q->where('zone', $zone);
        if ($letter = $request->letter) $q->where('name', 'like', "$letter%");

        $perPage = min((int) ($request->per_page ?? 8), 200);
        $products = $q->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'data'  => $products->items(),
            'total' => $products->total(),
            'pages' => $products->lastPage(),
            'page'  => $products->currentPage(),
        ]);
    }

    // ── Détail produit ─────────────────────────────────────────
    public function show(Product $product): JsonResponse
    {
        $product->load('producer:id,name,company,zone');
        return response()->json($product);
    }

    // ── Créer (admin/producteur) ───────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user || !$user->canManageProducts()) {
            return response()->json(['error' => 'Accès refusé.'], 403);
        }

        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'category'     => 'required|in:Légumes,Fruits,Tubercules,Herbes,Épices',
            'quantity'     => 'required|integer|min:0|max:50',
            'price'        => 'required|integer|min:100',
            'zone'         => 'required|string',
            'harvest_date' => 'nullable|date',
            'description'  => 'nullable|string',
            'image'        => 'nullable|string',
            'available'    => 'boolean',
        ]);

        $data['producer_id'] = $user->isAdmin() ? ($request->producer_id ?? $user->id) : $user->id;

        if (empty($data['image'])) {
            $data['image'] = Product::defaultPhotoFor($data['name']);
        }

        $product = Product::create($data);
        $product->load('producer:id,name,company,zone');

        return response()->json($product, 201);
    }

    // ── Modifier ───────────────────────────────────────────────
    public function update(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        if (!$user) return response()->json(['error' => 'Non authentifié.'], 401);
        if (!$user->isAdmin() && $product->producer_id !== $user->id) {
            return response()->json(['error' => 'Vous ne pouvez modifier que vos propres produits.'], 403);
        }

        $data = $request->validate([
            'name'         => 'sometimes|string|max:100',
            'category'     => 'sometimes|in:Légumes,Fruits,Tubercules,Herbes,Épices',
            'quantity'     => 'sometimes|integer|min:0|max:50',
            'price'        => 'sometimes|integer|min:100',
            'zone'         => 'sometimes|string',
            'harvest_date' => 'nullable|date',
            'description'  => 'nullable|string',
            'image'        => 'nullable|string',
            'available'    => 'boolean',
        ]);

        $product->update($data);
        return response()->json($product);
    }

    // ── Supprimer ──────────────────────────────────────────────
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();

        if (!$user) return response()->json(['error' => 'Non authentifié.'], 401);
        if (!$user->isAdmin() && $product->producer_id !== $user->id) {
            return response()->json(['error' => 'Accès refusé.'], 403);
        }

        $product->delete();
        return response()->json(['message' => 'Produit supprimé.']);
    }

    // ── Toggle disponibilité ───────────────────────────────────
    public function toggleAvailability(Request $request, Product $product): JsonResponse
    {
        $user = $request->user();
        if (!$user || (!$user->isAdmin() && $product->producer_id !== $user->id)) {
            return response()->json(['error' => 'Accès refusé.'], 403);
        }

        $product->update(['available' => !$product->available]);
        return response()->json([
            'available' => $product->available,
            'message'   => $product->name . ' : ' . ($product->available ? 'visible' : 'masqué'),
        ]);
    }

    // ── Mes produits (producteur connecté) ─────────────────────
    public function myProducts(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Non authentifié.'], 401);

        $products = $user->isAdmin()
            ? Product::with('producer:id,name,company')->latest()->get()
            : $user->products()->latest()->get();

        return response()->json($products);
    }
}
