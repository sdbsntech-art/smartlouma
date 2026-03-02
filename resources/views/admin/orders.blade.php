@extends('layouts.admin')
@section('title', 'Commandes')
@section('page-title', 'Gestion des Commandes')

@section('content')
<div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap">
  @foreach(['' => 'Toutes', 'pending' => '⏳ En attente', 'confirmed' => '✅ Confirmées', 'delivered' => '📦 Livrées', 'cancelled' => '❌ Annulées'] as $val => $lbl)
    <a href="{{ route('admin.orders', ['status' => $val]) }}"
       class="btn {{ request('status') === $val ? 'btn-primary' : 'btn-outline' }} btn-sm">{{ $lbl }}</a>
  @endforeach
</div>

<div class="card">
  <table>
    <thead>
      <tr><th>Référence</th><th>Client</th><th>Articles</th><th>Total</th><th>Livraison</th><th>Statut</th><th>Date</th><th>Action</th></tr>
    </thead>
    <tbody>
      @forelse($orders as $order)
        <tr>
          <td><code style="font-size:.8rem;background:#F3F4F6;padding:.2rem .5rem;border-radius:4px">{{ $order->reference }}</code></td>
          <td>
            <strong>{{ $order->buyer->name ?? '—' }}</strong><br>
            <small style="color:var(--gray)">{{ $order->buyer->email ?? '' }}</small>
          </td>
          <td>{{ $order->items->count() }} article(s)<br>
            <small style="color:var(--gray)">{{ $order->items->sum('quantity') }} kg</small>
          </td>
          <td><strong style="color:var(--green)">{{ number_format($order->total, 0, ',', ' ') }} F</strong></td>
          <td>{{ $order->delivery_fee == 0 ? '<span style="color:var(--green);font-size:.78rem">Gratuite ✓</span>' : number_format($order->delivery_fee, 0, ',', ' ').' F' }}</td>
          <td>
            <span class="badge {{ $order->status==='delivered'?'badge-green':($order->status==='pending'?'badge-amber':($order->status==='cancelled'?'badge-red':'badge-blue')) }}">
              {{ $order->statusLabel() }}
            </span>
          </td>
          <td>{{ $order->created_at->format('d/m/Y') }}</td>
          <td>
            <form method="POST" action="{{ route('admin.orders.status', $order) }}">
              @csrf
              <select name="status" class="form-select" style="padding:.3rem .6rem;border-radius:6px;font-size:.78rem;width:auto" onchange="this.form.submit()">
                @foreach(['pending'=>'En attente','confirmed'=>'Confirmée','delivered'=>'Livrée','cancelled'=>'Annulée'] as $v => $l)
                  <option value="{{ $v }}" {{ $order->status===$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
              </select>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="8"><div class="empty-state"><i class="fas fa-shopping-bag"></i><h4>Aucune commande</h4><p>Les commandes apparaîtront ici avec les premières ventes.</p></div></td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination">{{ $orders->links() }}</div>
</div>
@endsection
