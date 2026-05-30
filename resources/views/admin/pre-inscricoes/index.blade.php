@extends('layouts.app')

@section('content')
<style>
  /* =========================================================
     GRR 3.0 — PRÉ-INSCRIÇÕES
     Visual premium com foco em DARK, sem quebrar LIGHT
  ========================================================= */

  .pre-page{
    max-width: 1280px;
    margin: 0 auto;
  }

  .pre-kicker{
    letter-spacing: .16em;
    text-transform: uppercase;
    font-weight: 800;
    font-size: .74rem;
    opacity: .78;
  }

  .pre-hero{
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    padding: 24px;
    margin-bottom: 20px;
    border: 1px solid rgba(15,23,42,.08);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.10), transparent 28%),
      radial-gradient(circle at left bottom, rgba(16,185,129,.08), transparent 24%),
      linear-gradient(180deg, #ffffff, #f8fafc);
    box-shadow: 0 18px 50px rgba(15,23,42,.08);
  }

  .pre-hero__glow{
    position:absolute;
    border-radius:999px;
    filter: blur(50px);
    pointer-events:none;
    opacity:.55;
  }
  .pre-hero__glow--a{
    width:180px; height:180px;
    right:-30px; top:-30px;
    background: rgba(59,130,246,.18);
  }
  .pre-hero__glow--b{
    width:180px; height:180px;
    left:-35px; bottom:-55px;
    background: rgba(16,185,129,.14);
  }

  .pre-hero__content{
    position: relative;
    z-index: 2;
  }

  .pre-hero__title{
    margin: 0;
    font-size: clamp(1.35rem, 2vw, 1.9rem);
    font-weight: 900;
    letter-spacing: -.02em;
    color: #0f172a;
  }

  .pre-hero__desc{
    margin: 8px 0 0;
    max-width: 780px;
    color: #475569;
    font-size: .97rem;
  }

  .pre-hero__badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    border-radius:999px;
    padding:10px 14px;
    font-size:.8rem;
    font-weight:800;
    background: rgba(255,255,255,.75);
    border:1px solid rgba(148,163,184,.22);
    color:#0f172a;
    white-space: nowrap;
  }

  .pre-card{
    border: 1px solid rgba(15,23,42,.08);
    border-radius: 22px;
    background: linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,.98));
    box-shadow: 0 14px 40px rgba(15,23,42,.06);
  }

  .pre-card .card-body{
    padding: 20px;
  }

  .pre-section-title{
    font-size: .95rem;
    font-weight: 900;
    color: #0f172a;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .pre-section-dot{
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: linear-gradient(135deg, #2563eb, #60a5fa);
    box-shadow: 0 0 0 5px rgba(37,99,235,.10);
    flex: 0 0 auto;
  }

  .pre-label{
    font-size: .78rem;
    font-weight: 800;
    color: #475569;
    margin-bottom: 7px;
  }

  .pre-input,
  .pre-select{
    min-height: 46px;
    border-radius: 14px !important;
    border: 1px solid rgba(148,163,184,.25) !important;
    background: #fff !important;
    box-shadow: none !important;
  }

  .pre-input:focus,
  .pre-select:focus{
    border-color: rgba(37,99,235,.45) !important;
    box-shadow: 0 0 0 .22rem rgba(37,99,235,.12) !important;
  }

  .pre-actions{
    display:flex;
    gap:10px;
  }

  .pre-btn{
    min-height: 46px;
    border-radius: 14px !important;
    font-weight: 800 !important;
  }

  .pre-toolbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding: 18px 20px 0;
    flex-wrap: wrap;
  }

  .pre-toolbar__title{
    font-size: 1rem;
    font-weight: 900;
    color: #0f172a;
    margin: 0;
  }

  .pre-toolbar__meta{
    color: #64748b;
    font-size: .88rem;
    margin-top: 4px;
  }

  .pre-count{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 14px;
    border-radius:999px;
    font-size:.8rem;
    font-weight:800;
    background:#f8fafc;
    border:1px solid rgba(148,163,184,.20);
    color:#334155;
  }

  .pre-table-wrap{
    padding: 12px 20px 0;
  }

  .pre-table{
    margin-bottom: 0;
    --bs-table-bg: transparent;
  }

  .pre-table thead th{
    border-bottom: none !important;
    padding: 15px 14px;
    font-size: .76rem;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 900;
    color: #64748b;
    background: #f8fafc;
    white-space: nowrap;
  }

  .pre-table thead th:first-child{
    border-top-left-radius: 16px;
    border-bottom-left-radius: 16px;
  }

  .pre-table thead th:last-child{
    border-top-right-radius: 16px;
    border-bottom-right-radius: 16px;
  }

  .pre-table tbody td{
    padding: 16px 14px;
    vertical-align: middle;
    border-color: rgba(148,163,184,.14) !important;
    color: #0f172a;
  }

  .pre-table tbody tr{
    transition: .18s ease;
  }

  .pre-table tbody tr:hover{
    background: rgba(37,99,235,.035);
  }

  .pre-id-main{
    font-weight: 800;
    color: #0f172a;
    line-height: 1.15;
  }

  .pre-id-sub{
    margin-top: 4px;
    font-size: .82rem;
    color: #64748b;
  }

  .pre-muted{
    color: #64748b !important;
  }

  .pre-status{
    display:inline-flex;
    align-items:center;
    gap:8px;
    border-radius:999px;
    padding:8px 12px;
    font-size:.76rem;
    font-weight:900;
    letter-spacing:.02em;
  }

  .pre-status::before{
    content:"";
    width:8px;
    height:8px;
    border-radius:999px;
    flex:0 0 auto;
    background: currentColor;
    box-shadow: 0 0 0 4px rgba(255,255,255,.12);
  }

  .pre-status--pendente{
    background: rgba(245,158,11,.16);
    color: #f59e0b;
    border: 1px solid rgba(245,158,11,.22);
  }

  .pre-status--aprovado{
    background: rgba(16,185,129,.16);
    color: #10b981;
    border: 1px solid rgba(16,185,129,.22);
  }

  .pre-status--reprovado{
    background: rgba(239,68,68,.14);
    color: #ef4444;
    border: 1px solid rgba(239,68,68,.22);
  }

  .pre-table .btn{
    border-radius: 12px !important;
    font-weight: 800 !important;
  }

  .pre-empty{
    padding: 34px 20px;
    text-align: center;
    color: #64748b;
  }

  .pre-footer{
    padding: 18px 20px 20px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    flex-wrap:wrap;
  }

  .pre-footer__info{
    color:#64748b;
    font-size:.9rem;
  }

  .pre-footer__info b{
    color:#0f172a;
  }

  .pre-wrap .alert{
    border: none;
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(15,23,42,.06);
  }

  .pre-wrap .pagination{
    margin-bottom: 0;
  }

  .pre-wrap .pagination .page-link{
    border-radius: 10px !important;
    margin: 0 2px;
    border-color: rgba(148,163,184,.22);
    color: #334155;
    font-weight: 700;
  }

  .pre-wrap .pagination .page-item.active .page-link{
    background: #2563eb;
    border-color: #2563eb;
    color: #fff;
  }

  /* =========================================================
     DARK MODE
  ========================================================= */

  body.theme-dark .pre-hero,
  html.theme-dark .pre-hero,
  [data-theme="dark"] .pre-hero,
  body.dark .pre-hero,
  html.dark .pre-hero{
    border-color: rgba(148,163,184,.16);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.16), transparent 28%),
      radial-gradient(circle at left bottom, rgba(16,185,129,.10), transparent 24%),
      linear-gradient(180deg, rgba(2,6,23,.94), rgba(15,23,42,.88));
    box-shadow: 0 18px 50px rgba(0,0,0,.28);
  }

  body.theme-dark .pre-hero__title,
  html.theme-dark .pre-hero__title,
  [data-theme="dark"] .pre-hero__title,
  body.dark .pre-hero__title,
  html.dark .pre-hero__title{
    color: #f8fafc;
  }

  body.theme-dark .pre-hero__desc,
  html.theme-dark .pre-hero__desc,
  [data-theme="dark"] .pre-hero__desc,
  body.dark .pre-hero__desc,
  html.dark .pre-hero__desc{
    color: rgba(226,232,240,.72);
  }

  body.theme-dark .pre-hero__badge,
  html.theme-dark .pre-hero__badge,
  [data-theme="dark"] .pre-hero__badge,
  body.dark .pre-hero__badge,
  html.dark .pre-hero__badge{
    background: rgba(15,23,42,.62);
    border-color: rgba(148,163,184,.22);
    color: #e2e8f0;
  }

  body.theme-dark .pre-card,
  html.theme-dark .pre-card,
  [data-theme="dark"] .pre-card,
  body.dark .pre-card,
  html.dark .pre-card{
    background: linear-gradient(180deg, rgba(2,6,23,.82), rgba(15,23,42,.78));
    border-color: rgba(148,163,184,.16);
    box-shadow: 0 18px 45px rgba(0,0,0,.28);
  }

  body.theme-dark .pre-section-title,
  body.theme-dark .pre-toolbar__title,
  html.theme-dark .pre-section-title,
  html.theme-dark .pre-toolbar__title,
  [data-theme="dark"] .pre-section-title,
  [data-theme="dark"] .pre-toolbar__title,
  body.dark .pre-section-title,
  body.dark .pre-toolbar__title,
  html.dark .pre-section-title,
  html.dark .pre-toolbar__title{
    color: #f8fafc;
  }

  body.theme-dark .pre-toolbar__meta,
  body.theme-dark .pre-count,
  html.theme-dark .pre-toolbar__meta,
  html.theme-dark .pre-count,
  [data-theme="dark"] .pre-toolbar__meta,
  [data-theme="dark"] .pre-count,
  body.dark .pre-toolbar__meta,
  body.dark .pre-count,
  html.dark .pre-toolbar__meta,
  html.dark .pre-count{
    color: rgba(226,232,240,.70);
  }

  body.theme-dark .pre-count,
  html.theme-dark .pre-count,
  [data-theme="dark"] .pre-count,
  body.dark .pre-count,
  html.dark .pre-count{
    background: rgba(15,23,42,.56);
    border-color: rgba(148,163,184,.18);
  }

  body.theme-dark .pre-label,
  html.theme-dark .pre-label,
  [data-theme="dark"] .pre-label,
  body.dark .pre-label,
  html.dark .pre-label{
    color: rgba(226,232,240,.76);
  }

  body.theme-dark .pre-input,
  body.theme-dark .pre-select,
  html.theme-dark .pre-input,
  html.theme-dark .pre-select,
  [data-theme="dark"] .pre-input,
  [data-theme="dark"] .pre-select,
  body.dark .pre-input,
  body.dark .pre-select,
  html.dark .pre-input,
  html.dark .pre-select{
    background: rgba(15,23,42,.72) !important;
    border-color: rgba(148,163,184,.22) !important;
    color: #e2e8f0 !important;
  }

  body.theme-dark .pre-input::placeholder,
  html.theme-dark .pre-input::placeholder,
  [data-theme="dark"] .pre-input::placeholder,
  body.dark .pre-input::placeholder,
  html.dark .pre-input::placeholder{
    color: rgba(226,232,240,.42) !important;
  }

  body.theme-dark .pre-select option,
  html.theme-dark .pre-select option,
  [data-theme="dark"] .pre-select option,
  body.dark .pre-select option,
  html.dark .pre-select option{
    background: #0b1220;
    color: #e2e8f0;
  }

  body.theme-dark .pre-table thead th,
  html.theme-dark .pre-table thead th,
  [data-theme="dark"] .pre-table thead th,
  body.dark .pre-table thead th,
  html.dark .pre-table thead th{
    background: #0b1220;
    color: rgba(226,232,240,.72);
  }

  body.theme-dark .pre-table tbody td,
  html.theme-dark .pre-table tbody td,
  [data-theme="dark"] .pre-table tbody td,
  body.dark .pre-table tbody td,
  html.dark .pre-table tbody td{
    color: rgba(226,232,240,.92);
    border-color: rgba(148,163,184,.12) !important;
  }

  body.theme-dark .pre-table tbody tr:hover,
  html.theme-dark .pre-table tbody tr:hover,
  [data-theme="dark"] .pre-table tbody tr:hover,
  body.dark .pre-table tbody tr:hover,
  html.dark .pre-table tbody tr:hover{
    background: rgba(59,130,246,.08);
  }

  body.theme-dark .pre-id-main,
  html.theme-dark .pre-id-main,
  [data-theme="dark"] .pre-id-main,
  body.dark .pre-id-main,
  html.dark .pre-id-main{
    color: #f8fafc;
  }

  body.theme-dark .pre-id-sub,
  body.theme-dark .pre-muted,
  body.theme-dark .pre-empty,
  body.theme-dark .pre-footer__info,
  html.theme-dark .pre-id-sub,
  html.theme-dark .pre-muted,
  html.theme-dark .pre-empty,
  html.theme-dark .pre-footer__info,
  [data-theme="dark"] .pre-id-sub,
  [data-theme="dark"] .pre-muted,
  [data-theme="dark"] .pre-empty,
  [data-theme="dark"] .pre-footer__info,
  body.dark .pre-id-sub,
  body.dark .pre-muted,
  body.dark .pre-empty,
  body.dark .pre-footer__info,
  html.dark .pre-id-sub,
  html.dark .pre-muted,
  html.dark .pre-empty,
  html.dark .pre-footer__info{
    color: rgba(226,232,240,.68) !important;
  }

  body.theme-dark .pre-footer__info b,
  html.theme-dark .pre-footer__info b,
  [data-theme="dark"] .pre-footer__info b,
  body.dark .pre-footer__info b,
  html.dark .pre-footer__info b{
    color: #f8fafc;
  }

  body.theme-dark .pre-wrap .btn-outline-secondary,
  html.theme-dark .pre-wrap .btn-outline-secondary,
  [data-theme="dark"] .pre-wrap .btn-outline-secondary,
  body.dark .pre-wrap .btn-outline-secondary,
  html.dark .pre-wrap .btn-outline-secondary{
    border-color: rgba(148,163,184,.25) !important;
    color: rgba(226,232,240,.88) !important;
  }

  body.theme-dark .pre-wrap .btn-outline-primary,
  html.theme-dark .pre-wrap .btn-outline-primary,
  [data-theme="dark"] .pre-wrap .btn-outline-primary,
  body.dark .pre-wrap .btn-outline-primary,
  html.dark .pre-wrap .btn-outline-primary{
    border-color: rgba(59,130,246,.52) !important;
    color: rgba(191,219,254,.95) !important;
  }

  body.theme-dark .pre-wrap .btn-outline-primary:hover,
  html.theme-dark .pre-wrap .btn-outline-primary:hover,
  [data-theme="dark"] .pre-wrap .btn-outline-primary:hover,
  body.dark .pre-wrap .btn-outline-primary:hover,
  html.dark .pre-wrap .btn-outline-primary:hover{
    background: rgba(59,130,246,.14) !important;
  }

  body.theme-dark .pre-wrap .pagination .page-link,
  html.theme-dark .pre-wrap .pagination .page-link,
  [data-theme="dark"] .pre-wrap .pagination .page-link,
  body.dark .pre-wrap .pagination .page-link,
  html.dark .pre-wrap .pagination .page-link{
    background: rgba(15,23,42,.72);
    border-color: rgba(148,163,184,.18);
    color: rgba(226,232,240,.88);
  }

  body.theme-dark .pre-wrap .pagination .page-item.active .page-link,
  html.theme-dark .pre-wrap .pagination .page-item.active .page-link,
  [data-theme="dark"] .pre-wrap .pagination .page-item.active .page-link,
  body.dark .pre-wrap .pagination .page-item.active .page-link,
  html.dark .pre-wrap .pagination .page-item.active .page-link{
    background: #3b82f6;
    border-color: #3b82f6;
    color: #08111f;
  }

  @media (max-width: 767.98px){
    .pre-hero{
      padding: 20px;
      border-radius: 20px;
    }

    .pre-card{
      border-radius: 18px;
    }

    .pre-card .card-body,
    .pre-table-wrap,
    .pre-toolbar,
    .pre-footer{
      padding-left: 16px;
      padding-right: 16px;
    }

    .pre-actions{
      width: 100%;
    }

    .pre-actions .btn{
      flex: 1 1 auto;
    }
  }
</style>

<div class="container-fluid py-3 pre-wrap">
  <div class="pre-page">

    {{-- HERO --}}
    <div class="pre-hero">
      <div class="pre-hero__glow pre-hero__glow--a"></div>
      <div class="pre-hero__glow pre-hero__glow--b"></div>

      <div class="pre-hero__content">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
          <div>
            <div class="pre-kicker text-secondary mb-2">GRR • PRF • Administrativo</div>
            <h1 class="pre-hero__title">Painel de Pré-inscrições</h1>
            <p class="pre-hero__desc">
              Gerencie, filtre e revise as solicitações recebidas com uma visualização mais clara, organizada e profissional.
            </p>
          </div>

          <div class="pre-hero__badge">
            <span>🔐</span>
            <span>Acesso restrito • Nível 9+</span>
          </div>
        </div>
      </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
      <div class="alert alert-success mb-3">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger mb-3">
        {{ session('error') }}
      </div>
    @endif

    {{-- FILTROS --}}
    <div class="card pre-card mb-3">
      <div class="card-body">
        <div class="pre-section-title">
          <span class="pre-section-dot"></span>
          <span>Filtros de consulta</span>
        </div>

        <form class="row g-3 align-items-end" method="GET" action="{{ route('admin.preinscricoes.index') }}">
          <div class="col-12 col-lg-5">
            <label class="pre-label">Busca geral</label>
            <input
              type="text"
              name="q"
              value="{{ request('q') }}"
              class="form-control pre-input"
              placeholder="Pesquisar por QRA, RG, Discord ID, IP..."
            >
          </div>

          <div class="col-12 col-md-6 col-lg-2">
            <label class="pre-label">Status</label>
            <select name="status" class="form-select pre-select">
              <option value="">Todos</option>
              <option value="pendente" @selected(request('status') === 'pendente')>Pendente</option>
              <option value="aprovado" @selected(request('status') === 'aprovado')>Aprovado</option>
              <option value="reprovado" @selected(request('status') === 'reprovado')>Reprovado</option>
            </select>
          </div>

          <div class="col-12 col-md-6 col-lg-2">
            <label class="pre-label">Ordenação</label>
            <select name="ord" class="form-select pre-select">
              <option value="desc" @selected(request('ord', 'desc') === 'desc')>Mais recentes</option>
              <option value="asc" @selected(request('ord') === 'asc')>Mais antigas</option>
            </select>
          </div>

          <div class="col-12 col-lg-3">
            <label class="pre-label d-none d-lg-block">&nbsp;</label>
            <div class="pre-actions">
              <button class="btn btn-primary pre-btn w-100" type="submit">
                Filtrar
              </button>
              <a class="btn btn-outline-secondary pre-btn w-100" href="{{ route('admin.preinscricoes.index') }}">
                Limpar
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    {{-- LISTA --}}
    <div class="card pre-card">
      <div class="pre-toolbar">
        <div>
          <h2 class="pre-toolbar__title">Solicitações registradas</h2>
          <div class="pre-toolbar__meta">
            Acompanhe o status e acesse rapidamente os detalhes de cada pré-inscrição.
          </div>
        </div>

        <div class="pre-count">
          <span>📋</span>
          <span>Total encontrado: <strong>{{ $preInscricoes->total() ?? 0 }}</strong></span>
        </div>
      </div>

      <div class="pre-table-wrap">
        <div class="table-responsive">
          <table class="table pre-table table-hover align-middle">
            <thead>
              <tr>
                <th style="width:140px;">Situação</th>
                <th>Identificação</th>
                <th style="width:210px;">Discord</th>
                <th style="width:180px;">Recebido em</th>
                <th style="width:130px;" class="text-end">Ações</th>
              </tr>
            </thead>
            <tbody>
              @forelse($preInscricoes as $p)
                @php
                  $s = (string)($p->status ?? 'pendente');

                  $statusClass = match($s){
                    'aprovado' => 'pre-status--aprovado',
                    'reprovado' => 'pre-status--reprovado',
                    default => 'pre-status--pendente',
                  };

                  $statusLabel = match($s){
                    'aprovado' => 'APROVADO',
                    'reprovado' => 'REPROVADO',
                    default => 'PENDENTE',
                  };
                @endphp

                <tr>
                  <td>
                    <span class="pre-status {{ $statusClass }}">
                      {{ $statusLabel }}
                    </span>
                  </td>

                  <td>
                    <div class="pre-id-main">{{ $p->qra_rg }}</div>
                    <div class="pre-id-sub">
                      Registro #{{ $p->id }}
                    </div>
                  </td>

                  <td class="pre-muted">
                    {{ $p->discord_id ?: '—' }}
                  </td>

                  <td class="pre-muted">
                    {{ optional($p->created_at)->format('d/m/Y H:i') ?: '—' }}
                  </td>

                  <td class="text-end">
                    <a
                      class="btn btn-sm btn-outline-primary"
                      href="{{ route('admin.preinscricoes.show', $p->id) }}"
                    >
                      Ver
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="pre-empty">
                    Nenhuma pré-inscrição encontrada no momento.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="pre-footer">
        <div class="pre-footer__info">
          Exibindo
          <b>{{ $preInscricoes->firstItem() ?? 0 }}</b>–<b>{{ $preInscricoes->lastItem() ?? 0 }}</b>
          de <b>{{ $preInscricoes->total() ?? 0 }}</b> registros
        </div>

        <div>
          {{ $preInscricoes->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>

  </div>
</div>
@endsection