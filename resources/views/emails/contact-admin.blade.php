{{-- contact-admin.blade.php : notification admin --}}
@extends('emails.layout')
@section('header')
  <h1>📩 Nouveau message de contact</h1>
  <p>De {{ $contactMessage->name }} — {{ $contactMessage->subject ?? 'Sans sujet' }}</p>
@endsection
@section('body')
  <p>Vous avez reçu un nouveau message depuis le formulaire de contact de SMART-LOUMA.</p>
  <div style="background:#F8F4EF;border-radius:12px;padding:1.25rem;margin:1rem 0">
    <div class="info-row"><span class="label">Expéditeur</span><span class="val">{{ $contactMessage->name }}</span></div>
    <div class="info-row"><span class="label">Email</span><span class="val">{{ $contactMessage->email }}</span></div>
    @if($contactMessage->phone)
    <div class="info-row"><span class="label">Téléphone</span><span class="val">{{ $contactMessage->phone }}</span></div>
    @endif
    <div class="info-row"><span class="label">Sujet</span><span class="val">{{ $contactMessage->subject ?? '—' }}</span></div>
    <div class="info-row"><span class="label">Reçu le</span><span class="val">{{ $contactMessage->created_at->format('d/m/Y à H:i') }}</span></div>
  </div>
  <div style="background:#fff;border:2px solid #E5E7EB;border-radius:12px;padding:1.25rem;margin:1rem 0">
    <p style="font-size:.8rem;color:#6B7280;margin-bottom:.5rem;font-weight:600">MESSAGE COMPLET</p>
    <p style="margin:0;line-height:1.8;white-space:pre-wrap">{{ $contactMessage->message }}</p>
  </div>
  <div style="display:flex;gap:1rem;flex-wrap:wrap">
    <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ $contactMessage->subject ?? 'Votre message SMART-LOUMA' }}" class="btn">
      ↩️ Répondre à {{ $contactMessage->name }}
    </a>
    <a href="{{ route('admin.contacts') }}" style="display:inline-block;background:#F8F4EF;color:#2D6A4F;padding:.85rem 2rem;border-radius:50px;text-decoration:none;font-weight:700;font-size:.9rem;margin:1.25rem 0">
      📋 Voir dans le dashboard
    </a>
  </div>
@endsection
