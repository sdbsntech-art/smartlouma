@extends('emails.layout')
@section('header')
  <h1>🛒 Commande confirmée !</h1>
  <p>{{ $order->reference }} — Livraison demain matin</p>
@endsection
@section('body')
  <p>Bonjour <strong>{{ $order->buyer->name }}</strong>,</p>
  <p>Votre commande <strong>{{ $order->reference }}</strong> a été enregistrée avec succès. Elle sera livrée <strong>demain matin</strong>.</p>

  <h3 style="margin:1.5rem 0 .75rem;font-size:1rem">🧾 Récapitulatif</h3>
  <table style="width:100%;border-collapse:collapse;font-size:.88rem">
    <thead>
      <tr style="background:#F8F4EF">
        <th style="padding:.6rem .8rem;text-align:left;color:#6B7280;font-weight:600">Produit</th>
        <th style="padding:.6rem .8rem;text-align:right;color:#6B7280;font-weight:600">Qté</th>
        <th style="padding:.6rem .8rem;text-align:right;color:#6B7280;font-weight:600">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($order->items as $item)
        <tr style="border-bottom:1px solid #F3F4F6">
          <td style="padding:.6rem .8rem">{{ $item->product_name }}</td>
          <td style="padding:.6rem .8rem;text-align:right">{{ $item->quantity }} kg</td>
          <td style="padding:.6rem .8rem;text-align:right;font-weight:600">{{ number_format($item->total_price, 0, ',', ' ') }} F</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr><td colspan="2" style="padding:.6rem .8rem;text-align:right;color:#6B7280">Livraison</td>
        <td style="padding:.6rem .8rem;text-align:right;color:{{ $order->delivery_fee==0 ? '#2D6A4F' : '#1A1A18' }};font-weight:600">
          {{ $order->delivery_fee == 0 ? 'Gratuite ✓' : number_format($order->delivery_fee, 0, ',', ' ').' FCFA' }}
        </td>
      </tr>
      <tr style="background:#F8F4EF">
        <td colspan="2" style="padding:.8rem;text-align:right;font-weight:700">TOTAL À PAYER</td>
        <td style="padding:.8rem;text-align:right;font-weight:700;color:#2D6A4F;font-size:1.05rem">{{ number_format($order->total, 0, ',', ' ') }} FCFA</td>
      </tr>
    </tfoot>
  </table>

  <div class="warning-box" style="margin-top:1.5rem">
    <strong>💰 Paiement à la livraison</strong> — Préparez <strong>{{ number_format($order->total, 0, ',', ' ') }} FCFA</strong> en espèces, Wave ou Orange Money.
  </div>
@endsection
