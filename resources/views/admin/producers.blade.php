@extends('layouts.admin')
@section('title', 'Producteurs')
@section('page-title', 'Gestion des Producteurs')

@section('content')
<div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap">
  @foreach(['pending' => '⏳ En attente ('.$pendingCount.')', 'active' => '✅ Actifs', 'suspended' => '🚫 Suspendus'] as $val => $lbl)
    <a href="{{ route('admin.producers', ['status' => $val]) }}"
       class="btn {{ $status === $val ? 'btn-primary' : 'btn-outline' }} btn-sm">{{ $lbl }}</a>
  @endforeach
</div>

<div class="card">
  <table>
    <thead>
      <tr><th>Nom</th><th>Email</th><th>Entreprise</th><th>Zone</th><th>Produits</th><th>Inscrit le</th><th>Actions</th></tr>
    </thead>
    <tbody>
      @forelse($producers as $producer)
        <tr>
          <td><strong>{{ $producer->name }}</strong></td>
          <td><a href="mailto:{{ $producer->email }}" style="color:var(--blue)">{{ $producer->email }}</a></td>
          <td>{{ $producer->company ?? '—' }}</td>
          <td>{{ $producer->zone ?? '—' }}</td>
          <td>{{ $producer->products_count }}</td>
          <td>{{ $producer->created_at->format('d/m/Y') }}</td>
          <td>
            <div style="display:flex;gap:.3rem;flex-wrap:wrap">
              @if($status === 'pending')
                <form method="POST" action="{{ route('admin.producers.approve', $producer) }}">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-xs"><i class="fas fa-check"></i> Approuver</button>
                </form>
                <form method="POST" action="{{ route('admin.users.delete', $producer) }}">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Rejeter et supprimer ?')">
                    <i class="fas fa-times"></i> Rejeter
                  </button>
                </form>
              @endif
              @if($status === 'active')
                <form method="POST" action="{{ route('admin.users.suspend', $producer) }}">
                  @csrf
                  <button type="submit" class="btn btn-xs" style="background:#FEF3C7;color:#92400E;border:none">
                    <i class="fas fa-pause"></i> Suspendre
                  </button>
                </form>
              @endif
              @if($status === 'suspended')
                <form method="POST" action="{{ route('admin.users.reactivate', $producer) }}">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-xs"><i class="fas fa-play"></i> Réactiver</button>
                </form>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="7"><div class="empty-state"><i class="fas fa-tractor"></i><h4>Aucun producteur dans cette catégorie</h4></div></td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination">{{ $producers->links() }}</div>
</div>
@endsection
