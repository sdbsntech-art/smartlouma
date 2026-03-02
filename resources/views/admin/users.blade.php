@extends('layouts.admin')
@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<form method="GET" style="display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap">
  <div class="search-wrap" style="flex:1;max-width:300px">
    <i class="fas fa-search"></i>
    <input class="form-control" name="search" value="{{ $search }}" placeholder="Rechercher…" style="padding-left:2.4rem">
  </div>
  <select name="role" class="form-select" style="width:auto;padding:.55rem 1rem;border-radius:50px">
    <option value="all" {{ $role==='all'?'selected':'' }}>Tous les rôles</option>
    <option value="consumer" {{ $role==='consumer'?'selected':'' }}>Restaurateurs</option>
    <option value="producer" {{ $role==='producer'?'selected':'' }}>Producteurs</option>
  </select>
  <select name="status" class="form-select" style="width:auto;padding:.55rem 1rem;border-radius:50px">
    <option value="all" {{ $status==='all'?'selected':'' }}>Tous les statuts</option>
    <option value="active" {{ $status==='active'?'selected':'' }}>Actifs</option>
    <option value="pending" {{ $status==='pending'?'selected':'' }}>En attente</option>
    <option value="suspended" {{ $status==='suspended'?'selected':'' }}>Suspendus</option>
  </select>
  <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Filtrer</button>
</form>

<div class="card">
  <table>
    <thead>
      <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Produits</th><th>Commandes</th><th>Inscrit</th><th>Actions</th></tr>
    </thead>
    <tbody>
      @forelse($users as $user)
        <tr>
          <td><strong>{{ $user->name }}</strong></td>
          <td>{{ $user->email }}</td>
          <td>
            <span class="badge {{ $user->role==='producer' ? 'badge-green' : ($user->role==='admin' ? 'badge-red' : 'badge-blue') }}">
              {{ $user->roleLabel() }}
            </span>
          </td>
          <td>
            <span class="badge {{ $user->status==='active' ? 'badge-green' : ($user->status==='pending' ? 'badge-amber' : 'badge-red') }}">
              {{ $user->status }}
            </span>
          </td>
          <td>{{ $user->products_count }}</td>
          <td>{{ $user->orders_count }}</td>
          <td>{{ $user->created_at->format('d/m/Y') }}</td>
          <td>
            <div style="display:flex;gap:.3rem">
              @if($user->status === 'active' && !$user->isAdmin())
                <form method="POST" action="{{ route('admin.users.suspend', $user) }}">
                  @csrf
                  <button class="btn btn-xs" style="background:#FEF3C7;color:#92400E;border:none"><i class="fas fa-pause"></i></button>
                </form>
              @endif
              @if($user->status === 'suspended')
                <form method="POST" action="{{ route('admin.users.reactivate', $user) }}">
                  @csrf
                  <button class="btn btn-primary btn-xs"><i class="fas fa-play"></i></button>
                </form>
              @endif
              @if(!$user->isAdmin())
                <form method="POST" action="{{ route('admin.users.delete', $user) }}">
                  @csrf @method('DELETE')
                  <button class="icon-btn danger" onclick="return confirm('Supprimer ?')"><i class="fas fa-trash"></i></button>
                </form>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="8"><div class="empty-state"><i class="fas fa-users"></i><h4>Aucun utilisateur</h4></div></td></tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination">{{ $users->links() }}</div>
</div>
@endsection
