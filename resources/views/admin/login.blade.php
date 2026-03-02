<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion Admin — SMART-LOUMA</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#1B4332 0%,#2D6A4F 60%,#40916C 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
    .login-box{background:#fff;border-radius:20px;padding:3rem;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.25)}
    .logo{display:flex;align-items:center;gap:.75rem;margin-bottom:2rem}
    .logo-badge{width:48px;height:48px;border-radius:12px;background:#2D6A4F;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem}
    h1{font-family:'Playfair Display',serif;font-size:1.5rem;color:#1A1A18;margin-bottom:.25rem}
    p{color:#6B7280;font-size:.88rem;margin-bottom:2rem}
    .form-group{margin-bottom:1.25rem}
    label{display:block;font-size:.83rem;font-weight:600;margin-bottom:.4rem;color:#1A1A18}
    input{width:100%;padding:.75rem 1rem;border:2px solid #E5E7EB;border-radius:10px;font-size:.9rem;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .2s}
    input:focus{border-color:#2D6A4F}
    button{width:100%;padding:.85rem;background:#2D6A4F;color:#fff;border:none;border-radius:50px;font-size:.95rem;font-weight:700;cursor:pointer;font-family:'DM Sans',sans-serif;transition:background .2s;margin-top:.5rem}
    button:hover{background:#1B4332}
    .error{background:#FEE2E2;color:#DC2626;border-radius:10px;padding:.75rem 1rem;font-size:.84rem;margin-bottom:1.25rem;border-left:3px solid #DC2626}
    .hint{background:#D8F3DC;color:#2D6A4F;border-radius:10px;padding:.75rem 1rem;font-size:.82rem;margin-top:1.25rem}
  </style>
</head>
<body>
<div class="login-box">
  <div class="logo">
    <div class="logo-badge">SL</div>
    <div>
      <div style="font-family:'Playfair Display',serif;font-weight:700;font-size:1.1rem">SMART-LOUMA</div>
      <div style="font-size:.72rem;color:#6B7280">Administration</div>
    </div>
  </div>

  <h1>Connexion Admin</h1>
  <p>Accès réservé à l'administrateur de la plateforme.</p>

  @if($errors->any())
    <div class="error"><i class="fas fa-times-circle"></i> {{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.login.post') }}">
    @csrf
    <div class="form-group">
      <label>Email administrateur</label>
      <input type="email" name="email" value="{{ old('email') }}" placeholder="seydoubakhayokho1@gmail.com" required autofocus>
    </div>
    <div class="form-group">
      <label>Mot de passe</label>
      <input type="password" name="password" placeholder="••••••••" required>
    </div>
    <button type="submit"><i class="fas fa-sign-in-alt"></i> Accéder au dashboard</button>
  </form>

  <div class="hint">
    <i class="fas fa-info-circle"></i>
    <strong>Rappel :</strong> Email : <code>seydoubakhayokho1@gmail.com</code> — Mot de passe : <code>louma</code>
  </div>
</div>
</body>
</html>
