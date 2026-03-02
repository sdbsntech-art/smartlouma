{{-- resources/views/emails/layout.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width">
  <title>@yield('subject')</title>
  <style>
    body{margin:0;padding:0;background:#F0EDE8;font-family:'Helvetica Neue',Arial,sans-serif}
    .wrap{max-width:600px;margin:0 auto;padding:2rem 1rem}
    .card{background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.08)}
    .header{background:linear-gradient(135deg,#1B4332,#2D6A4F);padding:2rem 2.5rem;color:#fff}
    .header h1{font-size:1.4rem;margin:0;font-weight:700}
    .header p{margin:.4rem 0 0;opacity:.8;font-size:.9rem}
    .body{padding:2.5rem}
    .body p{margin:0 0 1rem;line-height:1.7;color:#374151;font-size:.95rem}
    .btn{display:inline-block;background:#2D6A4F;color:#fff !important;padding:.85rem 2rem;border-radius:50px;text-decoration:none;font-weight:700;font-size:.9rem;margin:1.25rem 0}
    .info-row{display:flex;padding:.6rem 0;border-bottom:1px solid #F3F4F6;font-size:.88rem}
    .info-row .label{color:#6B7280;width:140px;flex-shrink:0}
    .info-row .val{font-weight:600;color:#1A1A18}
    .footer-mail{padding:1.5rem 2.5rem;background:#F8F4EF;border-top:1px solid #E5E7EB;font-size:.8rem;color:#9CA3AF;text-align:center}
    .tag{display:inline-block;background:#D8F3DC;color:#2D6A4F;padding:.25rem .75rem;border-radius:50px;font-size:.78rem;font-weight:700;margin-bottom:1rem}
    .warning-box{background:#FEF3C7;border-left:3px solid #E9820C;border-radius:8px;padding:1rem;margin:1rem 0;font-size:.88rem;color:#92400E}
  </style>
</head>
<body>
<div class="wrap">
  <div style="text-align:center;margin-bottom:1.5rem;padding:.75rem">
    <span style="font-size:1.1rem;font-weight:700;color:#2D6A4F">🌿 SMART-LOUMA</span>
  </div>
  <div class="card">
    <div class="header">
      @yield('header')
    </div>
    <div class="body">
      @yield('body')
    </div>
    <div class="footer-mail">
      © {{ date('Y') }} SMART-LOUMA · Dakar, Sénégal · <a href="mailto:seydoubakhayokho1@gmail.com" style="color:#2D6A4F">seydoubakhayokho1@gmail.com</a><br>
      Cet email a été envoyé automatiquement depuis la plateforme SMART-LOUMA.
    </div>
  </div>
</div>
</body>
</html>
