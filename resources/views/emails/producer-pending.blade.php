{{-- producer-pending.blade.php --}}
@extends('emails.layout')
@section('header')
  <h1>⚠️ Nouveau producteur en attente</h1>
  <p>Une demande d'inscription producteur requiert votre approbation</p>
@endsection
@section('body')
  <span class="tag">Action requise</span>
  <p>Bonjour <strong>{{ config('app.admin_name') }}</strong>,</p>
  <p>Un nouveau producteur vient de s'inscrire sur SMART-LOUMA et attend votre approbation :</p>
  <div style="background:#F8F4EF;border-radius:12px;padding:1.25rem;margin:1rem 0">
    <div class="info-row"><span class="label">Nom</span><span class="val">{{ $user->name }}</span></div>
    <div class="info-row"><span class="label">Email</span><span class="val">{{ $user->email }}</span></div>
    <div class="info-row"><span class="label">Téléphone</span><span class="val">{{ $user->phone ?? '—' }}</span></div>
    <div class="info-row"><span class="label">Entreprise</span><span class="val">{{ $user->company ?? '—' }}</span></div>
    <div class="info-row"><span class="label">Zone</span><span class="val">{{ $user->zone ?? '—' }}</span></div>
    <div class="info-row" style="border:none"><span class="label">Inscrit le</span><span class="val">{{ $user->created_at->format('d/m/Y à H:i') }}</span></div>
  </div>
  <a href="{{ route('admin.producers') }}" class="btn">✅ Gérer les demandes d'approbation</a>
@endsection
