@extends('emails.layout')
@section('header')
  <h1>✅ Votre compte est approuvé !</h1>
  <p>Vous pouvez maintenant vendre sur SMART-LOUMA</p>
@endsection
@section('body')
  <span class="tag" style="background:#D8F3DC;color:#2D6A4F">✅ Compte approuvé</span>
  <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
  <p>Excellente nouvelle ! Votre compte producteur sur <strong>SMART-LOUMA</strong> vient d'être approuvé par l'administrateur.</p>
  <p>Vous pouvez dès maintenant :</p>
  <ul style="color:#374151;font-size:.95rem;line-height:2;padding-left:1.5rem;margin:0 0 1rem">
    <li>Vous connecter à votre espace producteur</li>
    <li>Ajouter vos produits au catalogue (50 kg max par produit)</li>
    <li>Définir vos prix et votre disponibilité</li>
    <li>Recevoir des commandes des restaurateurs de Dakar</li>
  </ul>
  <a href="{{ config('app.url') }}" class="btn">🌿 Accéder à mon espace producteur</a>
  <div class="warning-box">
    <strong>💡 Conseil :</strong> Ajoutez des photos attractives et des descriptions détaillées pour vendre plus rapidement !
  </div>
@endsection
