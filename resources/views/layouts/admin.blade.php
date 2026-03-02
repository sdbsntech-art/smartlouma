<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard') — SMART-LOUMA Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --green:#2D6A4F; --green-dark:#1B4332; --green-light:#74C69D; --green-pale:#D8F3DC;
      --amber:#E9820C; --red:#DC2626; --blue:#1D4ED8; --purple:#7C3AED;
      --cream:#F8F4EF; --dark:#1A1A18; --gray:#6B7280; --white:#fff;
      --sidebar-w:260px; --shadow:0 4px 20px rgba(0,0,0,.08); --radius:12px; --tr:all .25s ease;
    }
    *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'DM Sans',sans-serif;background:#F0EDE8;color:var(--dark);display:flex;min-height:100vh}
    a{text-decoration:none;color:inherit}

    /* SIDEBAR */
    .sidebar{width:var(--sidebar-w);flex-shrink:0;background:var(--green-dark);color:#fff;
      display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100;overflow-y:auto}
    .sb-brand{padding:1.75rem 1.5rem 1.5rem;display:flex;align-items:center;gap:.75rem;border-bottom:1px solid rgba(255,255,255,.1)}
    .sb-badge{width:38px;height:38px;border-radius:10px;background:var(--green);display:flex;align-items:center;justify-content:center;font-weight:700}
    .sb-brand h2{font-family:'Playfair Display',serif;font-size:1.2rem}
    .sb-section{padding:.75rem 1rem .25rem;font-size:.68rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;opacity:.4;margin-top:.5rem}
    .sb-link{display:flex;align-items:center;gap:.75rem;padding:.7rem 1.5rem;font-size:.88rem;font-weight:500;
      transition:var(--tr);cursor:pointer;border:none;background:none;color:#fff;width:100%;text-align:left}
    .sb-link i{width:18px;opacity:.7}
    .sb-link:hover{background:rgba(255,255,255,.08)}
    .sb-link.active{background:rgba(116,198,157,.2);color:var(--green-light)}
    .sb-link .badge-count{margin-left:auto;background:var(--amber);color:#fff;font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:50px}
    .sb-footer{margin-top:auto;padding:1.25rem 1.5rem;border-top:1px solid rgba(255,255,255,.1)}
    .sb-footer small{opacity:.6;font-size:.75rem}

    /* MAIN */
    .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh}
    .topbar{background:var(--white);border-bottom:1px solid #E5E7EB;padding:1rem 2rem;
      display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:50}
    .topbar h1{font-size:1.2rem;font-family:'Playfair Display',serif}
    .content{padding:2rem;flex:1}

    /* CARDS */
    .kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.25rem;margin-bottom:2rem}
    .kpi{background:var(--white);border-radius:var(--radius);padding:1.5rem;box-shadow:var(--shadow);position:relative;overflow:hidden;border-top:3px solid var(--accent)}
    .kpi-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.5rem}
    .kpi-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem}
    .kpi h3{font-size:1.8rem;font-family:'Playfair Display',serif;margin-bottom:.2rem}
    .kpi p{font-size:.82rem;color:var(--gray)}

    /* TABLE */
    .card{background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;margin-bottom:1.5rem}
    .card-header{padding:1.25rem 1.5rem;border-bottom:1px solid #F3F4F6;display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap}
    .card-header h3{font-size:1rem;font-weight:700}
    .card-body{padding:1.5rem}
    table{width:100%;border-collapse:collapse}
    thead th{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--gray);
      padding:.75rem 1rem;border-bottom:2px solid #F3F4F6;text-align:left;white-space:nowrap}
    tbody td{padding:.85rem 1rem;border-bottom:1px solid #F9FAFB;font-size:.86rem;vertical-align:middle}
    tbody tr:hover td{background:#FAFAF9}
    tbody tr:last-child td{border:none}

    /* BADGES */
    .badge{display:inline-flex;align-items:center;gap:.3rem;padding:.3rem .75rem;border-radius:50px;font-size:.72rem;font-weight:700}
    .badge-green{background:var(--green-pale);color:var(--green)}
    .badge-amber{background:#FEF3C7;color:#92400E}
    .badge-red{background:#FEE2E2;color:var(--red)}
    .badge-blue{background:#DBEAFE;color:var(--blue)}
    .badge-gray{background:#F3F4F6;color:var(--gray)}
    .badge-purple{background:#EDE9FE;color:var(--purple)}

    /* BUTTONS */
    .btn{display:inline-flex;align-items:center;gap:.4rem;padding:.55rem 1.2rem;border-radius:50px;
      font-size:.84rem;font-weight:600;cursor:pointer;border:2px solid transparent;transition:var(--tr);font-family:'DM Sans',sans-serif}
    .btn-primary{background:var(--green);color:#fff;border-color:var(--green)}
    .btn-primary:hover{background:var(--green-dark)}
    .btn-danger{background:var(--red);color:#fff}
    .btn-amber{background:var(--amber);color:#fff}
    .btn-outline{color:var(--green);border-color:var(--green);background:transparent}
    .btn-sm{padding:.35rem .85rem;font-size:.78rem}
    .btn-xs{padding:.25rem .6rem;font-size:.72rem}
    .icon-btn{background:none;border:none;cursor:pointer;padding:.35rem;border-radius:6px;color:var(--gray);transition:var(--tr)}
    .icon-btn:hover{background:#F3F4F6;color:var(--dark)}
    .icon-btn.danger:hover{color:var(--red);background:#FEE2E2}
    .icon-btn.success:hover{color:var(--green);background:var(--green-pale)}

    /* FORMS */
    .form-control,.form-select,.form-textarea{width:100%;padding:.65rem 1rem;border:2px solid #E5E7EB;
      border-radius:10px;font-size:.88rem;font-family:'DM Sans',sans-serif;transition:var(--tr);outline:none;background:#fff}
    .form-control:focus,.form-select:focus{border-color:var(--green)}
    .form-label{display:block;font-size:.83rem;font-weight:600;margin-bottom:.4rem}

    /* ALERTS */
    .alert{padding:.9rem 1.25rem;border-radius:10px;margin-bottom:1rem;font-size:.88rem}
    .alert-success{background:#D8F3DC;color:var(--green);border-left:3px solid var(--green)}
    .alert-danger{background:#FEE2E2;color:var(--red);border-left:3px solid var(--red)}
    .alert-warning{background:#FEF3C7;color:#92400E;border-left:3px solid var(--amber)}

    /* PROD THUMB */
    .prod-thumb{width:44px;height:44px;border-radius:8px;object-fit:cover}

    /* TOGGLE */
    .toggle-sw{width:38px;height:20px;border-radius:10px;position:relative;display:inline-block;cursor:pointer}
    .toggle-sw input{display:none}
    .toggle-sw .slider{position:absolute;inset:0;background:#D1D5DB;border-radius:10px;transition:.3s}
    .toggle-sw .slider::after{content:'';position:absolute;width:16px;height:16px;background:#fff;border-radius:50%;top:2px;left:2px;transition:.3s}
    .toggle-sw input:checked + .slider{background:var(--green)}
    .toggle-sw input:checked + .slider::after{left:20px}

    /* PAGINATION */
    .pagination{display:flex;gap:.3rem;align-items:center;padding:1rem 1.5rem;border-top:1px solid #F3F4F6}
    .page-item a,.page-item span{padding:.45rem .85rem;border-radius:8px;font-size:.84rem;display:block}
    .page-item a:hover{background:#F3F4F6}
    .page-item.active span{background:var(--green);color:#fff}

    /* EMPTY */
    .empty-state{text-align:center;padding:3.5rem;color:var(--gray)}
    .empty-state i{font-size:2.5rem;color:#D1D5DB;display:block;margin-bottom:.75rem}
    .empty-state h4{margin-bottom:.5rem;font-size:1rem}

    /* CHART */
    .chart-wrap{position:relative;height:260px}

    /* SEARCH */
    .search-wrap{position:relative}
    .search-wrap input{padding-left:2.4rem}
    .search-wrap i{position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--gray);font-size:.82rem}

    @media(max-width:1100px){.kpi-grid{grid-template-columns:repeat(2,1fr)}}
  </style>
  @yield('head')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sb-brand">
    <div class="sb-badge">SL</div>
    <div>
      <h2>SMART-LOUMA</h2>
      <small style="font-size:.72rem;opacity:.6">Administration</small>
    </div>
  </div>

  <div class="sb-section">Tableau de bord</div>
  <a href="{{ route('admin.dashboard') }}" class="sb-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <i class="fas fa-tachometer-alt"></i> Vue d'ensemble
  </a>

  <div class="sb-section">Gestion</div>
  <a href="{{ route('admin.products') }}" class="sb-link {{ request()->routeIs('admin.products') ? 'active' : '' }}">
    <i class="fas fa-box"></i> Produits
  </a>
  <a href="{{ route('admin.users') }}" class="sb-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
    <i class="fas fa-users"></i> Utilisateurs
  </a>
  <a href="{{ route('admin.orders') }}" class="sb-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
    <i class="fas fa-shopping-bag"></i> Commandes
  </a>
  <a href="{{ route('admin.producers') }}" class="sb-link {{ request()->routeIs('admin.producers') ? 'active' : '' }}">
    <i class="fas fa-tractor"></i> Producteurs
    @if(($pendingCount ?? 0) > 0)
      <span class="badge-count">{{ $pendingCount }}</span>
    @endif
  </a>
  <a href="{{ route('admin.contacts') }}" class="sb-link {{ request()->routeIs('admin.contacts*') ? 'active' : '' }}">
    <i class="fas fa-envelope"></i> Messages
    @php $unread = \App\Models\ContactMessage::unread()->count(); @endphp
    @if($unread > 0)
      <span class="badge-count">{{ $unread }}</span>
    @endif
  </a>

  <div class="sb-section">Analytics</div>
  <a href="{{ route('admin.dashboard') }}#stats" class="sb-link">
    <i class="fas fa-chart-line"></i> Statistiques
  </a>

  <div class="sb-section">Système</div>
  <a href="/" class="sb-link" target="_blank">
    <i class="fas fa-globe"></i> Voir le site
  </a>

  <div class="sb-footer">
    <div style="font-weight:600;color:#fff;margin-bottom:.2rem">{{ Auth::user()->name }}</div>
    <small>Administrateur principal</small><br>
    <small>{{ Auth::user()->email }}</small>
    <form method="POST" action="{{ route('admin.logout') }}" style="margin-top:.75rem">
      @csrf
      <button type="submit" style="background:rgba(255,255,255,.1);border:none;color:#fff;padding:.45rem 1rem;border-radius:6px;cursor:pointer;font-size:.8rem;width:100%;text-align:left">
        <i class="fas fa-sign-out-alt"></i> Déconnexion
      </button>
    </form>
  </div>
</aside>

<!-- MAIN -->
<main class="main">
  <div class="topbar">
    <h1>@yield('page-title', 'Dashboard')</h1>
    <div style="display:flex;gap:.75rem;align-items:center">
      <span style="font-size:.84rem;color:var(--gray)">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
      @yield('topbar-actions')
    </div>
  </div>

  <div class="content">
    @if(session('success'))
      <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger"><i class="fas fa-times-circle"></i> {{ session('error') }}</div>
    @endif

    @yield('content')
  </div>
</main>

@yield('scripts')
</body>
</html>
