@extends('layouts.admin')
@section('title', 'Messages de contact')
@section('page-title', 'Messages de contact')

@section('topbar-actions')
  <span style="background:#EDE9FE;color:var(--purple);padding:.35rem .9rem;border-radius:50px;font-size:.8rem;font-weight:700">
    <i class="fas fa-envelope"></i> {{ $unreadCount }} non lu{{ $unreadCount > 1 ? 's' : '' }}
  </span>
@endsection

@section('content')

{{-- FILTRES --}}
<div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap">
  @foreach(['all' => 'Tous', 'unread' => 'Non lus', 'read' => 'Lus', 'replied' => 'Répondus'] as $val => $lbl)
    <a href="{{ route('admin.contacts', ['status' => $val]) }}"
       class="btn {{ $status === $val ? 'btn-primary' : 'btn-outline' }} btn-sm">
      {{ $lbl }}
    </a>
  @endforeach
</div>

<div class="card">
  <div class="card-header">
    <h3><i class="fas fa-inbox" style="color:var(--purple);margin-right:.4rem"></i>
      Formulaires reçus ({{ $messages->total() }})
    </h3>
    <small style="color:var(--gray)">Tous les messages envoyés depuis le site</small>
  </div>
  <table>
    <thead>
      <tr>
        <th>Statut</th><th>Expéditeur</th><th>Email</th><th>Sujet</th>
        <th>Message</th><th>Reçu le</th><th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($messages as $msg)
        <tr style="{{ $msg->status === 'unread' ? 'font-weight:600;background:#FAFFF8' : '' }}">
          <td>
            @if($msg->status === 'unread')
              <span class="badge badge-purple"><i class="fas fa-circle" style="font-size:.5rem"></i> Non lu</span>
            @elseif($msg->status === 'read')
              <span class="badge badge-gray">Lu</span>
            @else
              <span class="badge badge-green">Répondu</span>
            @endif
          </td>
          <td><strong>{{ $msg->name }}</strong>{{ $msg->phone ? '<br><small style="color:var(--gray);font-weight:400">'.$msg->phone.'</small>' : '' }}</td>
          <td><a href="mailto:{{ $msg->email }}" style="color:var(--blue)">{{ $msg->email }}</a></td>
          <td>{{ $msg->subject ?? '—' }}</td>
          <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
            {{ Str::limit($msg->message, 60) }}
          </td>
          <td style="white-space:nowrap">
            <div>{{ $msg->created_at->format('d/m/Y') }}</div>
            <small style="color:var(--gray)">{{ $msg->created_at->format('H:i') }}</small>
          </td>
          <td>
            <div style="display:flex;gap:.3rem">
              <a href="{{ route('admin.contacts.show', $msg) }}" class="btn btn-primary btn-xs">
                <i class="fas fa-eye"></i> Lire
              </a>
              <a href="mailto:{{ $msg->email }}?subject=Re: {{ $msg->subject }}" class="btn btn-xs btn-amber">
                <i class="fas fa-reply"></i>
              </a>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7">
            <div class="empty-state">
              <i class="fas fa-inbox"></i>
              <h4>Aucun message</h4>
              <p>Les formulaires de contact apparaîtront ici.</p>
            </div>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
  <div class="pagination">{{ $messages->links() }}</div>
</div>

@endsection
