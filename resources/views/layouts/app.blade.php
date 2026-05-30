<!DOCTYPE html>
@php
  $userTema = 'system';
  if(auth()->check()){
    $t = strtolower((string)(auth()->user()->tema ?? 'system'));
    $userTema = in_array($t, ['system','dark','light'], true) ? $t : 'system';
  }
@endphp
<html lang="pt-br" data-theme="{{ $userTema }}">
<head>
  <meta charset="utf-8">

  <script>
    (function () {
      try {
        const html = document.documentElement;
        const serverTheme = (html.getAttribute('data-theme') || 'system').toLowerCase();
        const saved = (localStorage.getItem('grr_theme') || '').toLowerCase();
        const isFixed = (v) => v === 'dark' || v === 'light';

        if (isFixed(saved)) {
          html.setAttribute('data-theme', saved);
          return;
        }

        if (isFixed(serverTheme)) {
          html.setAttribute('data-theme', serverTheme);
          return;
        }
      } catch (e) {}
    })();
  </script>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GRR • PRF — Sistema Operacional</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">

  <style>
    :root{
      --bg: #f5f7fb;
      --surface: rgba(255,255,255,.92);
      --surface2: rgba(255,255,255,.75);
      --surface3: #ffffff;
      --text: #0f172a;
      --muted: rgba(15,23,42,.65);
      --border: rgba(2,6,23,.10);
      --shadow: 0 16px 50px rgba(2,6,23,.10);

      --topbar: #071a2f;
      --topbarText: #ffffff;
      --topbarSoft: rgba(255,255,255,.08);

      --sidebar: #08131f;
      --sidebarText: rgba(255,255,255,.92);

      --inputBg: #ffffff;
      --inputText: #0f172a;
      --inputBorder: rgba(2,6,23,.14);

      --accent: #0d6efd;
      --accentHover: #0b5ed7;
      --accentSoft: rgba(13,110,253,.14);
      --link: #0d6efd;
      --focusRing: rgba(13,110,253,.28);

      --gold: #d7aa45;
      --goldSoft: rgba(215,170,69,.16);

      --sidebarWidth: 290px;
      --headerHeight: 74px;
    }

    html[data-theme="dark"]{
      --bg: #000000;
      --surface: #0b0f14;
      --surface2: #0f1621;
      --surface3: #0d141d;

      --text: #e7edf6;
      --muted: rgba(231,237,246,.72);
      --border: rgba(255,255,255,.10);
      --shadow: 0 22px 70px rgba(0,0,0,.68);

      --topbar: linear-gradient(90deg, #02060c 0%, #051227 55%, #071931 100%);
      --topbarText: rgba(231,237,246,.94);
      --topbarSoft: rgba(255,255,255,.06);

      --sidebar: linear-gradient(180deg, #010101 0%, #04070c 100%);
      --sidebarText: rgba(231,237,246,.88);

      --inputBg: rgba(14,19,28,.92);
      --inputText: rgba(231,237,246,.92);
      --inputBorder: rgba(255,255,255,.14);

      --accent: #5aa2ff;
      --accentHover: #428fff;
      --accentSoft: rgba(90,162,255,.16);
      --link: #8bbcff;
      --focusRing: rgba(90,162,255,.24);

      --gold: #d7aa45;
      --goldSoft: rgba(215,170,69,.14);
    }

    @media (prefers-color-scheme: dark){
      html[data-theme="system"]{
        --bg: #000000;
        --surface: #0b0f14;
        --surface2: #0f1621;
        --surface3: #0d141d;

        --text: #e7edf6;
        --muted: rgba(231,237,246,.72);
        --border: rgba(255,255,255,.10);
        --shadow: 0 22px 70px rgba(0,0,0,.68);

        --topbar: linear-gradient(90deg, #02060c 0%, #051227 55%, #071931 100%);
        --topbarText: rgba(231,237,246,.94);
        --topbarSoft: rgba(255,255,255,.06);

        --sidebar: linear-gradient(180deg, #010101 0%, #04070c 100%);
        --sidebarText: rgba(231,237,246,.88);

        --inputBg: rgba(14,19,28,.92);
        --inputText: rgba(231,237,246,.92);
        --inputBorder: rgba(255,255,255,.14);

        --accent: #5aa2ff;
        --accentHover: #428fff;
        --accentSoft: rgba(90,162,255,.16);
        --link: #8bbcff;
        --focusRing: rgba(90,162,255,.24);

        --gold: #d7aa45;
        --goldSoft: rgba(215,170,69,.14);
      }
    }

    html[data-theme="light"]{
      --bg: #f5f7fb;
    }

    html[data-theme="dark"]{
      --bs-body-bg: var(--bg);
      --bs-body-color: var(--text);
      --bs-border-color: var(--border);
      --bs-link-color: var(--link);
      --bs-link-hover-color: var(--link);
      --bs-primary: var(--accent);
      --bs-primary-rgb: 90,162,255;
      --bs-secondary-color: var(--muted);
      --bs-tertiary-color: var(--muted);
      --bs-card-bg: var(--surface);
      --bs-card-border-color: var(--border);
      --bs-input-bg: var(--inputBg);
      --bs-input-color: var(--inputText);
      --bs-input-border-color: var(--inputBorder);
      --bs-focus-ring-color: var(--focusRing);
    }

    @media (prefers-color-scheme: dark){
      html[data-theme="system"]{
        --bs-body-bg: var(--bg);
        --bs-body-color: var(--text);
        --bs-border-color: var(--border);
        --bs-link-color: var(--link);
        --bs-link-hover-color: var(--link);
        --bs-primary: var(--accent);
        --bs-primary-rgb: 90,162,255;
        --bs-secondary-color: var(--muted);
        --bs-tertiary-color: var(--muted);
        --bs-card-bg: var(--surface);
        --bs-card-border-color: var(--border);
        --bs-input-bg: var(--inputBg);
        --bs-input-color: var(--inputText);
        --bs-input-border-color: var(--inputBorder);
        --bs-focus-ring-color: var(--focusRing);
      }
    }

    *{
      scrollbar-width: thin;
      scrollbar-color: rgba(255,255,255,.18) transparent;
    }

    *::-webkit-scrollbar{
      width: 10px;
      height: 10px;
    }

    *::-webkit-scrollbar-thumb{
      background: rgba(255,255,255,.14);
      border-radius: 999px;
    }

    body.gov-body{
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
    }

    .gov-restricted-bar,
    .gov-topbar{
      color: var(--topbarText);
    }

    .gov-restricted-bar{
      background:
        linear-gradient(90deg, rgba(8,12,18,.98), rgba(8,12,18,.98)),
        var(--topbar);
      border-bottom: 1px solid rgba(255,255,255,.06);
      font-size: 11px;
      letter-spacing: .14em;
      text-transform: uppercase;
    }

    .gov-restricted-inner{
      min-height: 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }

    .gov-restricted-left,
    .gov-restricted-right{
      display: flex;
      align-items: center;
      gap: 8px;
      color: rgba(255,255,255,.74);
      white-space: nowrap;
    }

    .gov-dot{
      width: 7px;
      height: 7px;
      border-radius: 999px;
      background: var(--gold);
      box-shadow: 0 0 0 4px rgba(215,170,69,.10);
      flex: 0 0 auto;
    }

    .gov-sep{
      opacity: .38;
    }

    .gov-topbar{
      position: sticky;
      top: 0;
      z-index: 1035;
      background: var(--topbar);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255,255,255,.06);
      box-shadow: 0 10px 30px rgba(0,0,0,.18);
    }

    .gov-topbar-inner{
      min-height: var(--headerHeight);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      padding-top: 10px;
      padding-bottom: 10px;
    }

    .gov-brand{
      display: flex;
      align-items: center;
      gap: 14px;
      min-width: 0;
    }

    .gov-emblem{
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: grid;
      place-items: center;
      background:
        radial-gradient(circle at 30% 30%, rgba(255,255,255,.18), transparent 55%),
        linear-gradient(180deg, rgba(255,255,255,.14), rgba(255,255,255,.04));
      border: 1px solid rgba(255,255,255,.10);
      box-shadow:
        inset 0 1px 0 rgba(255,255,255,.06),
        0 10px 26px rgba(0,0,0,.26);
      flex: 0 0 auto;
    }

    .gov-emblem-dot{
      width: 16px;
      height: 16px;
      border-radius: 999px;
      background: linear-gradient(180deg, #395d86, #1b334f);
      border: 3px solid rgba(215,170,69,.90);
      box-shadow: 0 0 0 4px rgba(255,255,255,.05);
      display: block;
    }

    .gov-brand-block{
      min-width: 0;
    }

    .gov-org-kicker{
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: .14em;
      color: rgba(255,255,255,.58);
      margin-bottom: 3px;
    }

    .gov-org-title{
      font-size: 1.18rem;
      font-weight: 900;
      letter-spacing: .01em;
      color: #fff;
      line-height: 1.05;
      margin: 0;
    }

    .gov-org-subtitle{
      color: rgba(231,237,246,.74);
      font-size: .94rem;
      font-weight: 500;
      line-height: 1.15;
      margin-top: 2px;
    }

    .gov-topbar-center{
      flex: 1 1 auto;
      display: flex;
      justify-content: center;
      min-width: 0;
      padding: 0 14px;
    }

    .gov-module-chip{
      display: inline-flex;
      align-items: center;
      gap: 10px;
      max-width: 100%;
      padding: 10px 16px;
      border-radius: 999px;
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.08);
      color: rgba(255,255,255,.82);
      box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      font-size: 12px;
      letter-spacing: .08em;
      text-transform: uppercase;
      font-weight: 800;
    }

    .gov-module-chip i{
      color: var(--gold);
      font-size: .95rem;
    }

    .gov-topbar-actions{
      display: flex;
      justify-content: flex-end;
      align-items: center;
      gap: 12px;
      flex: 0 0 auto;
    }

    .gov-notify-btn{
      width: 46px;
      height: 46px;
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      position: relative;
      border: 1px solid rgba(255,255,255,.12) !important;
      background: rgba(255,255,255,.04) !important;
      color: rgba(255,255,255,.90) !important;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
      transition: .2s ease;
    }

    .gov-notify-btn:hover{
      transform: translateY(-1px);
      background: rgba(255,255,255,.08) !important;
      border-color: rgba(255,255,255,.18) !important;
    }

    .gov-userbox{
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 14px;
      border-radius: 18px;
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.08);
      min-width: 0;
      max-width: 380px;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
    }

    .gov-user-avatar{
      width: 42px;
      height: 42px;
      border-radius: 14px;
      display: grid;
      place-items: center;
      flex: 0 0 auto;
      font-weight: 900;
      color: #fff;
      background:
        radial-gradient(circle at top left, rgba(255,255,255,.20), transparent 42%),
        linear-gradient(135deg, rgba(90,162,255,.35), rgba(9,23,42,.9));
      border: 1px solid rgba(255,255,255,.10);
      box-shadow: 0 10px 24px rgba(0,0,0,.24);
    }

    .gov-user-meta{
      min-width: 0;
      line-height: 1.15;
    }

    .gov-user-name{
      color: #fff;
      font-weight: 800;
      font-size: .96rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .gov-user-role{
      margin-top: 2px;
      color: rgba(231,237,246,.72);
      font-size: .82rem;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .gov-user-rg{
      margin-top: 4px;
      font-size: .74rem;
      color: rgba(231,237,246,.56);
      text-transform: uppercase;
      letter-spacing: .06em;
    }

    .gov-topbar-strip{
      height: 2px;
      background: linear-gradient(90deg, rgba(215,170,69,.95), rgba(215,170,69,.18), transparent 80%);
    }

    .gov-shell{
      display: flex;
      min-height: calc(100vh - 106px);
    }

    .gov-sidebar{
      width: var(--sidebarWidth);
      flex: 0 0 var(--sidebarWidth);
      background: var(--sidebar);
      color: var(--sidebarText);
      border-right: 1px solid rgba(255,255,255,.06);
      box-shadow: inset -1px 0 0 rgba(255,255,255,.02);
      position: sticky;
      top: calc(30px + var(--headerHeight));
      height: calc(100vh - 30px - var(--headerHeight));
      overflow-y: auto;
    }

    .gov-main{
      min-width: 0;
      flex: 1 1 auto;
      display: flex;
      flex-direction: column;
    }

    .gov-content{
      color: var(--text);
      padding-top: 22px;
      padding-bottom: 28px;
      flex: 1 1 auto;
    }

    .card, .dash-card, .rep-wrap, .fit-card{
      background: var(--surface);
      border-color: var(--border);
      box-shadow: var(--shadow);
    }

    .text-muted, .small.text-muted{
      color: var(--muted) !important;
    }

    a{
      color: var(--link);
    }

    a:hover{
      opacity: .92;
    }

    .form-control, .form-select, textarea{
      background: var(--inputBg) !important;
      color: var(--inputText) !important;
      border-color: var(--inputBorder) !important;
    }

    .form-control::placeholder, textarea::placeholder{
      color: rgba(15,23,42,.45);
    }

    html[data-theme="dark"] .form-control::placeholder,
    html[data-theme="dark"] textarea::placeholder{
      color: rgba(231,237,246,.42);
    }

    @media (prefers-color-scheme: dark){
      html[data-theme="system"] .form-control::placeholder,
      html[data-theme="system"] textarea::placeholder{
        color: rgba(231,237,246,.42);
      }
    }

    .form-control:focus, .form-select:focus, textarea:focus{
      border-color: rgba(90,162,255,.45) !important;
      box-shadow: 0 0 0 .20rem var(--focusRing) !important;
    }

    .dropdown-menu{
      background: var(--surface3);
      border-color: var(--border);
      color: var(--text);
      border-radius: 18px;
      box-shadow: 0 24px 70px rgba(0,0,0,.28);
    }

    .dropdown-item{
      color: var(--text);
      border-radius: 12px;
    }

    .dropdown-item:hover{
      background: var(--accentSoft);
    }

    .table{
      color: var(--text);
    }

    .table thead th{
      color: var(--muted);
      border-color: var(--border);
    }

    .table td, .table th{
      border-color: var(--border);
    }

    .gov-footer{
      background: transparent;
      border-top: 1px solid var(--border);
      color: var(--muted);
      padding: 14px 0;
    }

    .btn-primary{
      background: var(--accent) !important;
      border-color: var(--accent) !important;
      color: #071018 !important;
      font-weight: 800;
    }

    .btn-primary:hover{
      background: var(--accentHover) !important;
      border-color: var(--accentHover) !important;
      color: #050c12 !important;
    }

    html[data-theme="dark"] .btn-outline-primary{
      border-color: rgba(90,162,255,.55) !important;
      color: rgba(210,228,255,.92) !important;
    }

    html[data-theme="dark"] .btn-outline-primary:hover{
      background: var(--accentSoft) !important;
    }

    @media (prefers-color-scheme: dark){
      html[data-theme="system"] .btn-outline-primary{
        border-color: rgba(90,162,255,.55) !important;
        color: rgba(210,228,255,.92) !important;
      }

      html[data-theme="system"] .btn-outline-primary:hover{
        background: var(--accentSoft) !important;
      }
    }

    html[data-theme="dark"] .btn-outline-light{
      border-color: rgba(255,255,255,.22);
      color: rgba(231,237,246,.92);
    }

    @media (prefers-color-scheme: dark){
      html[data-theme="system"] .btn-outline-light{
        border-color: rgba(255,255,255,.22);
        color: rgba(231,237,246,.92);
      }
    }

    .nav-pills .nav-link{
      color: var(--muted);
      border: 1px solid var(--border);
      background: transparent;
      font-weight: 800;
    }

    .nav-pills .nav-link:hover{
      background: rgba(255,255,255,.04);
    }

    html[data-theme="light"] .nav-pills .nav-link:hover{
      background: rgba(2,6,23,.04);
    }

    .nav-pills .nav-link.active{
      color: var(--text);
      background: var(--accentSoft);
      border-color: rgba(90,162,255,.45);
    }

    html[data-theme="dark"] .form-label{
      color: rgba(231,237,246,.86) !important;
    }

    @media (prefers-color-scheme: dark){
      html[data-theme="system"] .form-label{
        color: rgba(231,237,246,.86) !important;
      }
    }

    .gov-title{
      color: var(--text) !important;
      font-weight: 900;
      letter-spacing: .2px;
    }

    html[data-theme="dark"] .gov-title{
      color: rgba(255,255,255,.94) !important;
      text-shadow: 0 1px 0 rgba(0,0,0,.55);
    }

    @media (prefers-color-scheme: dark){
      html[data-theme="system"] .gov-title{
        color: rgba(255,255,255,.94) !important;
        text-shadow: 0 1px 0 rgba(0,0,0,.55);
      }
    }

    html[data-theme="dark"] input[type="file"].form-control,
    @media (prefers-color-scheme: dark){ html[data-theme="system"] input[type="file"].form-control{
      color: rgba(231,237,246,.88) !important;
    } }

    html[data-theme="dark"] input[type="file"].form-control::file-selector-button,
    @media (prefers-color-scheme: dark){ html[data-theme="system"] input[type="file"].form-control::file-selector-button{
      background: rgba(255,255,255,.08) !important;
      color: rgba(231,237,246,.92) !important;
      border: 1px solid rgba(255,255,255,.14) !important;
      border-radius: 10px !important;
      margin-right: 10px;
    } }

    html[data-theme="dark"] input[type="file"].form-control:hover::file-selector-button,
    @media (prefers-color-scheme: dark){ html[data-theme="system"] input[type="file"].form-control:hover::file-selector-button{
      background: rgba(255,255,255,.12) !important;
    } }

    @media (max-width: 1199.98px){
      .gov-topbar-center{
        display: none;
      }

      .gov-userbox{
        max-width: 290px;
      }
    }

    @media (max-width: 991.98px){
      .gov-shell{
        display: block;
      }

      .gov-sidebar{
        width: 100%;
        height: auto;
        position: relative;
        top: 0;
      }

      .gov-topbar-inner{
        flex-wrap: wrap;
      }

      .gov-topbar-actions{
        width: 100%;
        justify-content: space-between;
      }

      .gov-userbox{
        max-width: none;
        flex: 1 1 auto;
      }

      .gov-restricted-inner{
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        padding-top: 6px;
        padding-bottom: 6px;
      }
    }

    @media (max-width: 575.98px){
      .gov-org-title{
        font-size: 1rem;
      }

      .gov-org-subtitle{
        font-size: .82rem;
      }

      .gov-notify-btn{
        width: 42px;
        height: 42px;
      }

      .gov-user-avatar{
        width: 38px;
        height: 38px;
        border-radius: 12px;
      }

      .gov-user-name{
        font-size: .9rem;
      }
    }
  </style>
</head>

<body class="gov-body">
  <div class="gov-restricted-bar">
    <div class="container-fluid gov-restricted-inner">
      <div class="gov-restricted-left">
        <span class="gov-dot"></span>
        <span>Acesso Restrito</span>
        <span class="gov-sep">•</span>
        <span>Brasil Capital</span>
        <span class="gov-sep">•</span>
        <span>Auditoria Ativa</span>
      </div>

      <div class="gov-restricted-right">
        <span>Sistema Oficial</span>
        <span class="gov-sep">•</span>
        <span>{{ now()->format('d/m/Y H:i') }}</span>
      </div>
    </div>
  </div>

  <header class="gov-topbar">
    <div class="container-fluid gov-topbar-inner">
      <div class="gov-brand">
        <div class="gov-emblem" aria-hidden="true">
          <span class="gov-emblem-dot"></span>
        </div>

        <div class="gov-brand-block">
          <div class="gov-org-kicker">
            <i class="bi bi-shield-lock-fill"></i>
            <span>Ambiente institucional</span>
          </div>
          <div class="gov-org-title">GRR • PRF</div>
          <div class="gov-org-subtitle">Sistema Operacional Interno</div>
        </div>
      </div>

      <div class="gov-topbar-center">
        <div class="gov-module-chip">
          <i class="bi bi-buildings-fill"></i>
          <span>Painel Administrativo • Plataforma GRR 3.0</span>
        </div>
      </div>

      <div class="gov-topbar-actions">
        @auth
          @php
            $unread = auth()->user()->unreadNotifications()->count();
            $latestNotifs = auth()->user()->notifications()->latest()->limit(6)->get();

            $parts = preg_split('/\s+/', trim((string) auth()->user()->name));
            $initials = '';
            foreach(array_slice($parts, 0, 2) as $p){
              $initials .= mb_strtoupper(mb_substr($p, 0, 1));
            }
            $initials = $initials ?: 'GR';
          @endphp

          <div class="dropdown">
            <button
              class="gov-notify-btn"
              data-bs-toggle="dropdown"
              aria-expanded="false"
              title="Notificações">
              <i class="bi bi-bell-fill"></i>

              @if($unread > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger">
                  {{ $unread }}
                </span>
              @endif
            </button>

            <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 380px;">
              <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                <div class="fw-bold">Notificações</div>

                @if($unread > 0)
                  <form method="POST" action="{{ route('notifications.read_all') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary">Ler todas</button>
                  </form>
                @endif
              </div>

              @forelse($latestNotifs as $n)
                @php
                  $data  = $n->data ?? [];
                  $url   = $data['url'] ?? '#';
                  $title = $data['title'] ?? 'Notificação';
                  $body  = $data['body'] ?? '';
                @endphp

                <div class="border rounded-4 p-3 mb-2 {{ $n->read_at ? 'opacity-75' : '' }}">
                  <div class="d-flex justify-content-between gap-2">
                    <div style="min-width:0;">
                      <div class="fw-semibold text-truncate">{{ $title }}</div>
                      <div class="small text-muted" style="white-space: normal;">
                        {{ $body }}
                      </div>
                      <div class="small text-muted mt-1">
                        {{ optional($n->created_at)->format('d/m/Y H:i') }}
                      </div>
                    </div>

                    <div class="d-flex flex-column gap-1">
                      <a class="btn btn-sm btn-primary" href="{{ $url }}">Abrir</a>

                      @if(!$n->read_at)
                        <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                          @csrf
                          <button class="btn btn-sm btn-outline-secondary">Marcar lida</button>
                        </form>
                      @endif
                    </div>
                  </div>
                </div>
              @empty
                <div class="text-muted small p-2">Sem notificações.</div>
              @endforelse
            </div>
          </div>

          <div class="gov-userbox">
            <div class="gov-user-avatar">
              {{ $initials }}
            </div>

            <div class="gov-user-meta">
              <div class="gov-user-name">{{ auth()->user()->name }}</div>
              <div class="gov-user-role">{{ auth()->user()->cargo ?? 'Cargo não informado' }}</div>
              <div class="gov-user-rg">RG {{ auth()->user()->rg ?? '—' }}</div>
            </div>
          </div>
        @else
          <div class="gov-userbox">
            <div class="gov-user-avatar">
              <i class="bi bi-shield-lock-fill"></i>
            </div>

            <div class="gov-user-meta">
              <div class="gov-user-name">Ambiente restrito</div>
              <div class="gov-user-role">Autenticação obrigatória</div>
              <div class="gov-user-rg">Sistema protegido</div>
            </div>
          </div>
        @endauth
      </div>
    </div>

    <div class="gov-topbar-strip"></div>
  </header>

  <div class="gov-shell">
    @auth
      <aside class="gov-sidebar">
        @include('partials.sidebar')
      </aside>
    @endauth

    <main class="gov-main">
      <div class="container-fluid gov-content">
        @yield('content')
      </div>

      <footer class="gov-footer">
        <div class="container-fluid d-flex justify-content-between flex-wrap gap-2">
          <span>© {{ date('Y') }} GRR • PRF</span>
          <span class="text-muted">Uso restrito • Registro e auditoria habilitados</span>
        </div>
      </footer>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>