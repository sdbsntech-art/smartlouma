@extends('layouts.admin')
@section('title', 'Produits')
@section('page-title', 'Gestion des Produits')

@section('content')
<div class="card">
  <div class="card-header">
    <h3>Catalogue produits ({{ \App\Models\Product::count() }})</h3>
    <small style="color:var(--gray)">Les produits sont ajoutés via l'API (espace producteur du site)</small>
  </div>
  <table>
    <thead>
      <tr><th>Photo</th><th>Produit</th><th>Catégorie</th><th>Stock</th><th>Prix/kg</th><th>Zone</th><th>Producteur</th><th>Disponible</th><th>Vendu</th><th>Actions</th></tr>
    </thead>
    <tbody>
      @forelse($products as $product)
        <tr>
          <td><img src="{{ $product->image_url }}" class="prod-thumb" alt="{{ $product->name }}" onerror="this.style.display='none'"></td>
          <td><strong>{{ $product->name }}</strong></td>
          <td><span class="badge badge-green">{{ $product->category }}</span></td>
          <td><strong>{{ $product->quantity }} kg</strong></td>
          <td>{{ number_format($product->price, 0, ',', ' ') }} F</td>
          <td>{{ $product->zone }}</td>
          <td>{{ $product->producer->name ?? 'Admin' }}</td>
          <td>
            <form method="POST" action="{{ route('admin.products.toggle', $product) }}">
              @csrf
              <button type="submit" style="background:none;border:none;cursor:pointer">
                <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.8rem;font-weight:600;color:{{ $product->available ? 'var(--green)' : 'var(--gray)' }}">
                  <span style="width:34px;height:18px;border-radius:9px;background:{{ $product->available ? 'var(--green)' : '#D1D5DB' }};position:relative;display:inline-block">
                    <span style="position:absolute;width:14px;height:14px;background:#fff;border-radius:50%;top:2px;{{ $product->available ? 'left:18px' : 'left:2px' }}"></span>
                  </span>
                  {{ $product->available ? 'Visible' : 'Masqué' }}
                </span>
              </button>
            </form>
          </td>
          <td>{{ $product->sold_qty }} kg</td>
          <td>
            <form method="POST" action="{{ route('admin.products.delete', $product) }}">
              @csrf @method('DELETE')
              <button class="icon-btn danger" onclick="return confirm('Supprimer {{ $product->name }} ?')">
                <i class="fas fa-trash"></i>
              </button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="10"><div class="empty-state"><i class="fas fa-box"></i><h4>Aucun produit</h4><p>Ajoutez des produits via l'espace producteur du site.</p></div></td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination">{{ $products->links() }}</div>
</div>
@endsection
