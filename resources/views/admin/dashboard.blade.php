@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Vue d\'ensemble')

@section('content')

{{-- KPI GRID --}}
<div class="kpi-grid">
  <div class="kpi" style="--accent:var(--green)">
    <div class="kpi-top">
      <div>
        <p style="font-size:.8rem;color:var(--gray);margin-bottom:.3rem">Producteurs actifs</p>
        <h3>{{ $stats['producers'] }}</h3>
        @if($stats['pending_producers'] > 0)
          <p style="color:var(--amber);font-size:.75rem;font-weight:600"><i class="fas fa-clock"></i> {{ $stats['pending_producers'] }} en attente</p>
        @else
          <p>/ {{ $stats['producers'] + $stats['pending_producers'] }} total</p>
        @endif
      </div>
      <div class="kpi-icon" style="background:var(--green-pale);color:var(--green)"><i class="fas fa-tractor"></i></div>
    </div>
  </div>
  <div class="kpi" style="--accent:var(--amber)">
    <div class="kpi-top">
      <div>
        <p style="font-size:.8rem;color:var(--gray);margin-bottom:.3rem">Restaurateurs inscrits</p>
        <h3>{{ $stats['consumers'] }}</h3>
        <p>utilisateurs actifs</p>
      </div>
      <div class="kpi-icon" style="background:#FEF3C7;color:#92400E"><i class="fas fa-utensils"></i></div>
    </div>
  </div>
  <div class="kpi" style="--accent:var(--blue)">
    <div class="kpi-top">
      <div>
        <p style="font-size:.8rem;color:var(--gray);margin-bottom:.3rem">Commandes totales</p>
        <h3>{{ $stats['orders'] }}</h3>
        <p>{{ $stats['available_products'] }} produits disponibles</p>
      </div>
      <div class="kpi-icon" style="background:#DBEAFE;color:var(--blue)"><i class="fas fa-shopping-bag"></i></div>
    </div>
  </div>
  <div class="kpi" style="--accent:var(--purple)">
    <div class="kpi-top">
      <div>
        <p style="font-size:.8rem;color:var(--gray);margin-bottom:.3rem">Chiffre d'affaires</p>
        <h3>{{ number_format($stats['revenue'], 0, ',', ' ') }}</h3>
        <p style="font-size:.72rem">FCFA • {{ number_format($stats['kg_sold'], 0, ',', ' ') }} kg échangés</p>
      </div>
      <div class="kpi-icon" style="background:#EDE9FE;color:var(--purple)"><i class="fas fa-coins"></i></div>
    </div>
  </div>
</div>

{{-- CHARTS ROW --}}
<div id="stats" style="display:grid;grid-template-columns:1.6fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
  <div class="card">
    <div class="card-header">
      <h3><i class="fas fa-chart-bar" style="color:var(--green);margin-right:.4rem"></i>Revenus & Commandes — 6 derniers mois</h3>
    </div>
    <div class="card-body">
      <div class="chart-wrap"><canvas id="revenueChart"></canvas></div>
      @if($stats['revenue'] == 0)
        <p style="text-align:center;font-size:.8rem;color:var(--gray);margin-top:.75rem;padding:.6rem;background:var(--cream-pale, #F8F4EF);border-radius:8px">
          <i class="fas fa-seedling" style="color:var(--green)"></i>
          <strong>Nouveau projet !</strong> Les courbes évolueront avec les premières commandes.
        </p>
      @endif
    </div>
  </div>
  <div class="card">
    <div class="card-header"><h3><i class="fas fa-layer-group" style="color:var(--amber);margin-right:.4rem"></i>Par catégorie</h3></div>
    <div class="card-body"><div class="chart-wrap"><canvas id="catChart"></canvas></div></div>
  </div>
</div>

{{-- DERNIÈRES ACTIVITÉS --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem">

  {{-- Dernières commandes --}}
  <div class="card">
    <div class="card-header">
      <h3><i class="fas fa-shopping-bag" style="color:var(--blue);margin-right:.4rem"></i>Dernières commandes</h3>
      <a href="{{ route('admin.orders') }}" class="btn btn-outline btn-xs">Toutes</a>
    </div>
    <div class="card-body" style="padding:0">
      @forelse($recentOrders as $order)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.85rem 1.25rem;border-bottom:1px solid #F9FAFB">
          <div>
            <div style="font-size:.84rem;font-weight:600">{{ $order->reference }}</div>
            <div style="font-size:.74rem;color:var(--gray)">{{ $order->buyer->name ?? '—' }}</div>
          </div>
          <div style="text-align:right">
            <div style="font-size:.84rem;font-weight:700;color:var(--green)">{{ number_format($order->total, 0, ',', ' ') }} F</div>
            <span class="badge {{ $order->status === 'delivered' ? 'badge-green' : ($order->status === 'pending' ? 'badge-amber' : 'badge-blue') }}">{{ $order->statusLabel() }}</span>
          </div>
        </div>
      @empty
        <div class="empty-state" style="padding:2rem"><i class="fas fa-shopping-bag"></i><p>Aucune commande</p></div>
      @endforelse
    </div>
  </div>

  {{-- Producteurs en attente --}}
  <div class="card">
    <div class="card-header">
      <h3><i class="fas fa-tractor" style="color:var(--amber);margin-right:.4rem"></i>Producteurs en attente</h3>
      <a href="{{ route('admin.producers') }}" class="btn btn-outline btn-xs">Gérer</a>
    </div>
    <div class="card-body" style="padding:0">
      @forelse($pendingProducers as $producer)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.85rem 1.25rem;border-bottom:1px solid #F9FAFB">
          <div>
            <div style="font-size:.84rem;font-weight:600">{{ $producer->name }}</div>
            <div style="font-size:.74rem;color:var(--gray)">{{ $producer->company ?? $producer->zone }}</div>
          </div>
          <form method="POST" action="{{ route('admin.producers.approve', $producer) }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-xs"><i class="fas fa-check"></i> Approuver</button>
          </form>
        </div>
      @empty
        <div class="empty-state" style="padding:2rem"><i class="fas fa-check-circle" style="color:var(--green)"></i><p>Aucune demande en attente</p></div>
      @endforelse
    </div>
  </div>

  {{-- Messages de contact --}}
  <div class="card">
    <div class="card-header">
      <h3><i class="fas fa-envelope" style="color:var(--purple);margin-right:.4rem"></i>Messages reçus</h3>
      <a href="{{ route('admin.contacts') }}" class="btn btn-outline btn-xs">Tous</a>
    </div>
    <div class="card-body" style="padding:0">
      @forelse($recentContacts as $msg)
        <a href="{{ route('admin.contacts.show', $msg) }}" style="display:block;padding:.85rem 1.25rem;border-bottom:1px solid #F9FAFB;transition:background .2s" onmouseover="this.style.background='#FAFAF9'" onmouseout="this.style.background=''">
          <div style="display:flex;justify-content:space-between;align-items:flex-start">
            <div>
              <div style="font-size:.84rem;font-weight:{{ $msg->status === 'unread' ? '700' : '500' }}">
                {{ $msg->status === 'unread' ? '🔵 ' : '' }}{{ $msg->name }}
              </div>
              <div style="font-size:.74rem;color:var(--gray);margin-top:.1rem">{{ Str::limit($msg->subject ?? $msg->message, 40) }}</div>
            </div>
            <div style="font-size:.72rem;color:var(--gray);white-space:nowrap;margin-left:.5rem">{{ $msg->created_at->diffForHumans() }}</div>
          </div>
        </a>
      @empty
        <div class="empty-state" style="padding:2rem"><i class="fas fa-envelope-open"></i><p>Aucun message</p></div>
      @endforelse
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
const revData   = @json($stats['monthly_revenue']);
const ordData   = @json($stats['monthly_orders']);
const catData   = @json($stats['category_data']);
const labels    = revData.map(d => d.label);
const revenues  = revData.map(d => d.value);
const ordCounts = ordData.map(d => d.value);

// Revenue Chart
new Chart(document.getElementById('revenueChart'), {
  type: 'line',
  data: {
    labels,
    datasets: [
      { label:'Revenus (FCFA)', data:revenues, borderColor:'#2D6A4F', backgroundColor:'rgba(45,106,79,.08)',
        borderWidth:2.5, pointBackgroundColor:'#2D6A4F', pointRadius:5, fill:true, tension:.4, yAxisID:'y' },
      { label:'Commandes', data:ordCounts, borderColor:'#E9820C', backgroundColor:'rgba(233,130,12,.06)',
        borderWidth:2, pointBackgroundColor:'#E9820C', pointRadius:4, fill:true, tension:.4, yAxisID:'y1' }
    ]
  },
  options: {
    responsive:true, maintainAspectRatio:false,
    plugins:{ legend:{ position:'bottom', labels:{ font:{ family:'DM Sans' } } } },
    scales:{
      y:  { grid:{color:'#F3F4F6'}, ticks:{font:{family:'DM Sans'}} },
      y1: { position:'right', grid:{display:false}, ticks:{font:{family:'DM Sans'}} },
      x:  { grid:{display:false}, ticks:{font:{family:'DM Sans'}} }
    }
  }
});

// Category Doughnut
const catColors = ['#2D6A4F','#E9820C','#1D4ED8','#7C3AED','#DC2626'];
new Chart(document.getElementById('catChart'), {
  type: 'doughnut',
  data: {
    labels: Object.keys(catData).length ? Object.keys(catData) : ['Aucun produit'],
    datasets: [{ data: Object.values(catData).length ? Object.values(catData) : [1], backgroundColor:catColors, borderWidth:0 }]
  },
  options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'bottom', labels:{ font:{ family:'DM Sans' } } } }, cutout:'65%' }
});
</script>
@endsection
