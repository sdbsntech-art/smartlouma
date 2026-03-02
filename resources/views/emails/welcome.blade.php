@extends('emails.layout')

@section('header')
  <h1>🎉 Bienvenue sur SMART-LOUMA !</h1>
  <p>Votre compte a été créé avec succès</p>
@endsection

@section('body')
  <span class="tag">✅ Compte activé</span>
  <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
  <p>Bienvenue sur <strong>SMART-LOUMA</strong>, la plateforme AgriTech B2B qui connecte directement les producteurs des Niayes aux restaurateurs de Dakar.</p>

  @if($user->isConsumer())
    <p>En tant que <strong>restaurateur</strong>, vous pouvez dès maintenant :</p>
    <ul style="color:#374151;font-size:.95rem;line-height:2;padding-left:1.5rem;margin:0 0 1rem">
      <li>Parcourir notre <strong>marketplace</strong> de produits frais</li>
      <li>Commander directement auprès des producteurs</li>
      <li>Recevoir votre livraison le lendemain matin</li>
      <li>Payer à la livraison (espèces, Wave, Orange Money)</li>
    </ul>
  @endif

  <a href="{{ config('app.url') }}" class="btn">🌿 Accéder à la plateforme</a>

  <p style="font-size:.85rem;color:#9CA3AF;margin-top:1.5rem">
    Une question ? Contactez-nous sur WhatsApp au <strong>+221 77 777 77 77</strong> ou répondez à cet email.
  </p>
@endsection
