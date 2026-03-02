{{-- contact-received.blade.php : confirmation à l'expéditeur --}}
@extends('emails.layout')
@section('header')
  <h1>✉️ Message bien reçu !</h1>
  <p>Nous vous répondrons dans les 24 heures</p>
@endsection
@section('body')
  <p>Bonjour <strong>{{ $contactMessage->name }}</strong>,</p>
  <p>Nous avons bien reçu votre message sur <strong>SMART-LOUMA</strong>. Notre équipe vous répondra dans les <strong>24 heures ouvrables</strong>.</p>
  <div style="background:#F8F4EF;border-radius:12px;padding:1.25rem;margin:1rem 0;border-left:3px solid #2D6A4F">
    <p style="font-size:.8rem;color:#6B7280;margin-bottom:.5rem;font-weight:600">VOTRE MESSAGE</p>
    <p style="margin:0;font-style:italic;color:#374151">{{ Str::limit($contactMessage->message, 300) }}</p>
  </div>
  <p>Pour toute urgence, contactez-nous directement :</p>
  <p>📞 <strong>+221 77 777 77 77</strong> (WhatsApp disponible)<br>
  ✉️ <strong>seydoubakhayokho1@gmail.com</strong></p>
@endsection
