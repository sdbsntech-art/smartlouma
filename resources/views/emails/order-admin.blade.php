{{-- order-admin.blade.php --}}
@extends('emails.layout')
@section('header')
  <h1>🛍️ Nouvelle commande reçue</h1>
  <p>{{ $order->reference }} — {{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
@endsection
@section('body')
  <p>Une nouvelle commande vient d'être passée sur SMART-LOUMA.</p>
  <div style="background:#F8F4EF;border-radius:12px;padding:1.25rem;margin:1rem 0">
    <div class="info-row"><span class="label">Référence</span><span class="val">{{ $order->reference }}</span></div>
    <div class="info-row"><span class="label">Client</span><span class="val">{{ $order->buyer->name }}</span></div>
    <div class="info-row"><span class="label">Email client</span><span class="val">{{ $order->buyer->email }}</span></div>
    <div class="info-row"><span class="label">Articles</span><span class="val">{{ $order->items->count() }} produit(s)</span></div>
    <div class="info-row"><span class="label">Sous-total</span><span class="val">{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span></div>
    <div class="info-row"><span class="label">Livraison</span><span class="val">{{ $order->delivery_fee == 0 ? 'Gratuite' : number_format($order->delivery_fee, 0, ',', ' ').' FCFA' }}</span></div>
    <div class="info-row" style="border:none"><span class="label">TOTAL</span><span class="val" style="color:#2D6A4F;font-size:1.05rem">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span></div>
  </div>
  <a href="{{ route('admin.orders') }}" class="btn">📦 Gérer les commandes</a>
@endsection
