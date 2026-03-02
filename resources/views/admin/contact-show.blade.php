@extends('layouts.admin')
@section('title', 'Message de ' . $message->name)
@section('page-title', 'Message de ' . $message->name)

@section('topbar-actions')
  <a href="{{ route('admin.contacts') }}" class="btn btn-outline btn-sm">
    <i class="fas fa-arrow-left"></i> Retour
  </a>
  <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="btn btn-primary btn-sm">
    <i class="fas fa-reply"></i> Répondre par email
  </a>
@endsection

@section('content')
<div style="max-width:800px">
  <div class="card">
    <div class="card-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
        <div>
          <p style="font-size:.8rem;color:var(--gray);margin-bottom:.25rem">Expéditeur</p>
          <p style="font-weight:700;font-size:1.1rem">{{ $message->name }}</p>
        </div>
        <div>
          <p style="font-size:.8rem;color:var(--gray);margin-bottom:.25rem">Email</p>
          <a href="mailto:{{ $message->email }}" style="color:var(--blue);font-weight:600">{{ $message->email }}</a>
        </div>
        @if($message->phone)
        <div>
          <p style="font-size:.8rem;color:var(--gray);margin-bottom:.25rem">Téléphone</p>
          <p style="font-weight:600">{{ $message->phone }}</p>
        </div>
        @endif
        <div>
          <p style="font-size:.8rem;color:var(--gray);margin-bottom:.25rem">Reçu le</p>
          <p style="font-weight:600">{{ $message->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        @if($message->subject)
        <div style="grid-column:1/-1">
          <p style="font-size:.8rem;color:var(--gray);margin-bottom:.25rem">Sujet</p>
          <p style="font-weight:700;font-size:1.05rem">{{ $message->subject }}</p>
        </div>
        @endif
      </div>

      <div style="background:#F8F4EF;border-radius:12px;padding:1.5rem;border-left:3px solid var(--green)">
        <p style="font-size:.8rem;color:var(--gray);margin-bottom:.75rem;font-weight:600">MESSAGE</p>
        <p style="line-height:1.8;white-space:pre-wrap">{{ $message->message }}</p>
      </div>

      <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #F3F4F6;display:flex;gap:1rem">
        <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject ?? 'Votre message SMART-LOUMA' }}&body=Bonjour {{ $message->name }},%0D%0A%0D%0AMerci pour votre message.%0D%0A%0D%0A"
           class="btn btn-primary">
          <i class="fas fa-reply"></i> Répondre à {{ $message->name }}
        </a>
        <a href="{{ route('admin.contacts') }}" class="btn btn-outline">
          <i class="fas fa-arrow-left"></i> Retour aux messages
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
