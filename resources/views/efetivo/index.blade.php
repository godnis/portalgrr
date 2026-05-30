@extends('layouts.app')

@section('content')
@php
  $stats = $stats ?? ['total'=>0,'ativos'=>0,'suspensos'=>0,'desligados'=>0];

  $cargos = $cargos ?? [
    1  => 'Aluno',
    2  => 'Agente de 3º Classe',
    3  => 'Agente de 2º Classe',
    4  => 'Agente de 1º Classe',
    5  => 'Agente Especial',
    6  => 'Inspetor',
    7  => 'Superintendente',
    8  => 'Coordenador',
    9  => 'Vice Diretor',
    10 => 'Diretor',
  ];

  $auth = auth()->user();
  $authNivel = (int)($auth->nivel ?? 0);

  $canManage  = $auth && $authNivel >= 9;
  $canPromote = $auth && $authNivel >= 8;
  $isDiretor  = $auth && $authNivel >= 10;

  $qVal = (string) request('q', '');
  $statusVal = (string) request('status', '');
  $nivelVal = (string) request('nivel', '');
  $ppVal = (int) request('per_page', 25);
  $ppVal = in_array($ppVal, [10,25,50,100], true) ? $ppVal : 25;

  $nv = (int) $nivelVal;
  $nivelLabel = ($nv >= 1 && $nv <= 10) ? ($cargos[$nv] ?? '—') : null;

  $totalGeral = method_exists($users, 'total') ? (int) $users->total() : (is_countable($users) ? count($users) : 0);
  $itensPagina = method_exists($users, 'count') ? (int) $users->count() : (is_countable($users) ? count($users) : 0);
  $filtrosAtivos = filled($qVal) || filled($statusVal) || filled($nivelVal);
@endphp

<style>
  .sel-col { display: none; }
  .promocao-aberta .sel-col { display: table-cell !important; }

  .ef-wrap{
    --ef-primary: #0d6efd;
    --ef-primary-soft: rgba(13,110,253,.10);
    --ef-success: #198754;
    --ef-success-soft: rgba(25,135,84,.12);
    --ef-warning: #f59f00;
    --ef-warning-soft: rgba(245,159,0,.14);
    --ef-danger: #dc3545;
    --ef-danger-soft: rgba(220,53,69,.12);

    --ef-text: #0f172a;
    --ef-muted: #64748b;
    --ef-border: rgba(15,23,42,.08);
    --ef-border-strong: rgba(15,23,42,.12);
    --ef-bg: #ffffff;
    --ef-bg-soft: #f8fafc;
    --ef-bg-soft-2: #f1f5f9;
    --ef-row: #ffffff;
    --ef-row-alt: #fbfdff;
    --ef-table-head: #f8fafc;

    --ef-shadow-sm: 0 8px 24px rgba(15,23,42,.06);
    --ef-shadow-md: 0 18px 48px rgba(15,23,42,.10);

    --ef-radius-xl: 24px;
    --ef-radius-lg: 18px;
    --ef-radius-md: 14px;
  }

  .ef-page{
    max-width: 1320px;
    margin: 0 auto;
  }

  .ef-hero{
    position: relative;
    overflow: hidden;
    padding: 28px 28px 24px;
    border: 1px solid rgba(13,110,253,.10);
    background:
      radial-gradient(circle at top right, rgba(13,110,253,.12), transparent 30%),
      radial-gradient(circle at left bottom, rgba(245,159,0,.10), transparent 24%),
      linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    border-radius: 28px;
    box-shadow: var(--ef-shadow-md);
  }

  .ef-hero__kicker{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: #0b5ed7;
    background: rgba(13,110,253,.10);
    border: 1px solid rgba(13,110,253,.12);
  }

  .ef-hero__title{
    font-size: clamp(1.7rem, 2.3vw, 2.3rem);
    line-height: 1.05;
    font-weight: 900;
    color: var(--ef-text);
    margin: 14px 0 8px;
  }

  .ef-hero__sub{
    color: var(--ef-muted);
    font-weight: 600;
    max-width: 860px;
  }

  .ef-hero__actions{
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .ef-hero__meta{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
  }

  .ef-chip{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 999px;
    border: 1px solid var(--ef-border);
    background: rgba(255,255,255,.90);
    color: var(--ef-text);
    font-size: 13px;
    font-weight: 800;
  }

  .ef-stat{
    position: relative;
    overflow: hidden;
    height: 100%;
    border-radius: 22px;
    border: 1px solid var(--ef-border);
    background: linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,.98));
    box-shadow: var(--ef-shadow-sm);
    transition: .2s ease;
  }

  .ef-stat:hover{
    transform: translateY(-2px);
    box-shadow: 0 16px 34px rgba(15,23,42,.10);
  }

  .ef-stat__body{ padding: 20px; }

  .ef-stat__label{
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .12em;
    font-weight: 900;
    color: var(--ef-muted);
    margin-bottom: 8px;
  }

  .ef-stat__value{
    font-size: 2rem;
    line-height: 1;
    font-weight: 900;
    color: var(--ef-text);
    margin-bottom: 8px;
  }

  .ef-stat__line{
    width: 56px;
    height: 4px;
    border-radius: 999px;
    background: currentColor;
    opacity: .16;
  }

  .ef-stat--total{ color: var(--ef-primary); }
  .ef-stat--ativos{ color: var(--ef-success); }
  .ef-stat--suspensos{ color: var(--ef-warning); }
  .ef-stat--desligados{ color: var(--ef-danger); }

  .ef-section-title{
    font-size: 13px;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--ef-muted);
    margin-bottom: 14px;
  }

  .ef-filter-card,
  .ef-promo-card,
  .ef-table-card{
    border: 1px solid var(--ef-border);
    border-radius: 24px;
    background: var(--ef-bg);
    box-shadow: var(--ef-shadow-sm);
  }

  .ef-filter-card .card-body,
  .ef-promo-card .card-body{
    padding: 22px;
  }

  .ef-label{
    display: block;
    margin-bottom: 7px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .10em;
    font-weight: 900;
    color: var(--ef-muted);
  }

  .ef-input,
  .ef-select{
    border-radius: 14px !important;
    min-height: 48px;
    border: 1px solid var(--ef-border-strong) !important;
    background: #fff !important;
    box-shadow: none !important;
    font-weight: 700;
    color: var(--ef-text) !important;
  }

  .ef-input::placeholder{ color: #94a3b8; }

  .ef-input:focus,
  .ef-select:focus{
    border-color: rgba(13,110,253,.35) !important;
    box-shadow: 0 0 0 .2rem rgba(13,110,253,.10) !important;
  }

  .ef-btn{
    min-height: 44px;
    border-radius: 14px !important;
    font-weight: 900 !important;
    padding-inline: 16px;
  }

  .ef-btn-primary{
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    border: none !important;
  }

  .ef-btn-soft{
    border: 1px solid var(--ef-border-strong) !important;
    background: #fff !important;
    color: var(--ef-text) !important;
  }

  .ef-alert{
    border: 1px solid rgba(245,159,0,.18);
    background: linear-gradient(180deg, rgba(255,248,230,.95), rgba(255,252,242,.98));
    color: #7a5600;
    border-radius: 18px;
  }

  .ef-collapse-btn{
    width: 100%;
    border: 0;
    background: transparent;
    padding: 22px;
    text-align: left;
    color: inherit;
  }

  .ef-collapse-btn:hover{
    background: rgba(15,23,42,.02);
  }

  .ef-table-card{
    overflow: hidden;
  }

  .ef-table-top{
    padding: 20px 22px;
    border-bottom: 1px solid var(--ef-border);
    background:
      radial-gradient(circle at top right, rgba(13,110,253,.06), transparent 22%),
      linear-gradient(180deg, rgba(248,250,252,.96), rgba(255,255,255,.98));
  }

  .ef-table-title{
    font-weight: 900;
    color: var(--ef-text);
    margin-bottom: 3px;
  }

  .ef-table-sub{
    color: var(--ef-muted);
    font-size: 13px;
    font-weight: 600;
  }

  .ef-table-meta{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .ef-mini-chip{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 38px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid var(--ef-border);
    background: rgba(255,255,255,.82);
    color: var(--ef-text);
    font-size: 12px;
    font-weight: 800;
  }

  .ef-table-wrap{
    background: var(--ef-bg);
  }

  .ef-table{
    width: 100%;
    margin-bottom: 0;
    color: var(--ef-text) !important;
    background: transparent !important;
  }

  .ef-table thead th{
    position: sticky;
    top: 0;
    z-index: 2;
    background: var(--ef-table-head) !important;
    color: #64748b !important;
    border-bottom: 1px solid var(--ef-border) !important;
    letter-spacing: .12em;
    text-transform: uppercase;
    font-size: 11px;
    font-weight: 900;
    padding: 15px 14px;
    white-space: nowrap;
  }

  .ef-table tbody tr{
    background: var(--ef-row) !important;
  }

  .ef-table tbody tr:nth-child(even){
    background: var(--ef-row-alt) !important;
  }

  .ef-table tbody td{
    padding: 16px 14px;
    border-top: 1px solid rgba(15,23,42,.06) !important;
    vertical-align: middle;
    background: transparent !important;
    color: var(--ef-text) !important;
  }

  .ef-table tbody tr:hover{
    background: rgba(13,110,253,.035) !important;
  }

  .ef-table tbody tr:hover td{
    background: transparent !important;
  }

  .ef-user{
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 230px;
  }

  .ef-avatar{
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 42px;
    font-size: 13px;
    font-weight: 900;
    color: #0b5ed7;
    background: linear-gradient(135deg, rgba(13,110,253,.14), rgba(13,110,253,.05));
    border: 1px solid rgba(13,110,253,.14);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.40);
  }

  .ef-user__body{
    min-width: 0;
  }

  .ef-name{
    font-weight: 900;
    color: var(--ef-text);
    line-height: 1.1;
    margin-bottom: 4px;
  }

  .ef-email{
    color: var(--ef-muted);
    font-size: 12.5px;
    font-weight: 700;
    line-height: 1.1;
    word-break: break-word;
  }

  .ef-pill-you{
    display: inline-flex;
    align-items: center;
    margin-left: 8px;
    border-radius: 999px;
    background: #0f172a;
    color: #fff;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .05em;
  }

  .ef-rg,
  .ef-cargo,
  .ef-nivel{
    font-weight: 800;
    color: var(--ef-text);
  }

  .ef-status{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 34px;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .06em;
  }

  .ef-actions{
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    min-width: 210px;
  }

  .ef-actions .btn{
    border-radius: 12px !important;
    font-weight: 900 !important;
    min-height: 36px;
    box-shadow: none !important;
  }

  .ef-pagination{
    padding: 18px 22px;
    border-top: 1px solid var(--ef-border);
    background: linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.98));
  }

  .ef-empty{
    padding: 46px 16px !important;
    color: var(--ef-muted) !important;
    font-weight: 700;
    background: transparent !important;
  }

  .ef-modal .modal-content{
    border: 1px solid var(--ef-border);
    border-radius: 22px;
    box-shadow: var(--ef-shadow-md);
    overflow: hidden;
    background: var(--ef-bg);
  }

  .ef-modal .modal-header{
    border-bottom: 1px solid var(--ef-border);
    background:
      radial-gradient(circle at top right, rgba(13,110,253,.08), transparent 30%),
      linear-gradient(180deg, rgba(248,250,252,.95), rgba(255,255,255,.98));
  }

  .ef-modal .modal-footer{
    border-top: 1px solid var(--ef-border);
    background: #fbfdff;
  }

  .ef-helper{
    font-size: 12px;
    color: var(--ef-muted);
    font-weight: 700;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-wrap{
    --ef-text: rgba(231,237,246,.95);
    --ef-muted: rgba(231,237,246,.66);
    --ef-border: rgba(255,255,255,.09);
    --ef-border-strong: rgba(255,255,255,.12);
    --ef-bg: rgba(10,14,20,.92);
    --ef-bg-soft: rgba(15,20,28,.88);
    --ef-bg-soft-2: rgba(18,24,33,.92);
    --ef-row: rgba(8,12,18,.76);
    --ef-row-alt: rgba(11,16,23,.90);
    --ef-table-head: rgba(20,27,39,.95);
    --ef-shadow-sm: 0 14px 40px rgba(0,0,0,.32);
    --ef-shadow-md: 0 24px 60px rgba(0,0,0,.42);
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-hero{
    border-color: rgba(255,255,255,.08);
    background:
      radial-gradient(circle at top right, rgba(13,110,253,.16), transparent 32%),
      radial-gradient(circle at left bottom, rgba(245,159,0,.10), transparent 28%),
      linear-gradient(135deg, rgba(10,14,20,.96), rgba(15,20,28,.94));
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-chip,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-stat,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-filter-card,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-promo-card,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-table-card,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-mini-chip{
    background: rgba(15,20,28,.86) !important;
    border-color: var(--ef-border) !important;
    color: var(--ef-text) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-input,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-select{
    background: rgba(15,20,28,.82) !important;
    color: var(--ef-text) !important;
    border-color: var(--ef-border) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-input::placeholder{
    color: rgba(231,237,246,.34);
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-table-wrap{
    background: linear-gradient(180deg, rgba(10,14,20,.96), rgba(12,17,25,.96)) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-table{
    color: var(--ef-text) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-table thead th{
    background: var(--ef-table-head) !important;
    color: rgba(231,237,246,.62) !important;
    border-bottom-color: var(--ef-border) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-table tbody td{
    border-top-color: rgba(255,255,255,.06) !important;
    color: var(--ef-text) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-table tbody tr:hover{
    background: rgba(255,255,255,.035) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-table-top,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-pagination,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-modal .modal-header,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-modal .modal-footer{
    background: rgba(15,20,28,.82) !important;
    border-color: var(--ef-border) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-btn-soft{
    background: rgba(15,20,28,.82) !important;
    color: var(--ef-text) !important;
    border-color: var(--ef-border) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-avatar{
    color: #7db4ff;
    background: linear-gradient(135deg, rgba(13,110,253,.18), rgba(13,110,253,.05));
    border-color: rgba(125,180,255,.16);
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .ef-wrap .pagination .page-link{
    background: rgba(15,20,28,.78) !important;
    border-color: rgba(255,255,255,.12) !important;
    color: rgba(231,237,246,.88) !important;
  }

  @media (max-width: 991.98px){
    .ef-actions{
      min-width: 0;
    }
  }

  @media (max-width: 767.98px){
    .ef-hero{ padding: 20px; }

    .ef-hero__actions{
      width: 100%;
    }

    .ef-hero__actions .btn{
      flex: 1 1 auto;
    }

    .ef-stat__value{
      font-size: 1.7rem;
    }

    .ef-filter-card .card-body,
    .ef-promo-card .card-body,
    .ef-collapse-btn,
    .ef-table-top,
    .ef-pagination{
      padding: 16px;
    }

    .ef-user{
      min-width: 200px;
    }

    .ef-avatar{
      width: 38px;
      height: 38px;
      flex-basis: 38px;
      border-radius: 12px;
      font-size: 12px;
    }
  }
</style>

<div class="container-fluid py-3 ef-wrap">
  <div class="ef-page">

    {{-- HERO --}}
    <div class="ef-hero mb-4">
      <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
        <div>
          <div class="ef-hero__kicker">GRR • PRF • Gestão de Efetivo</div>
          <h1 class="ef-hero__title">Efetivo operacional</h1>
          <div class="ef-hero__sub">
            @if($canManage)
              Painel central para gestão de oficiais cadastrados, promoções, organização hierárquica e acompanhamento administrativo do efetivo.
            @else
              Visualize o efetivo, filtre oficiais por nome, RG, cargo, nível e acompanhe a estrutura hierárquica da corporação.
            @endif
          </div>

          <div class="ef-hero__meta">
            <span class="ef-chip">Nível atual: <strong>{{ $authNivel }}</strong></span>
            <span class="ef-chip">Promoções rápidas: <strong>{{ $canPromote ? 'Liberado' : 'Indisponível' }}</strong></span>
            <span class="ef-chip">Gestão administrativa: <strong>{{ $canManage ? 'Liberada' : 'Restrita' }}</strong></span>
          </div>
        </div>

        
        {{-- OBRIGAR A ADICIONAR POLICIAL APENAS PELA SOLICITAÇÃO DE ACESSO DO BOT PARA PODER ENVIAR NOTIFICAÇÃO DEPOIS.
        @if($canManage)
          <div class="ef-hero__actions">
            <a href="{{ route('efetivo.create') }}" class="btn btn-primary ef-btn ef-btn-primary">
              + Adicionar Oficial
            </a>
          </div>
        @endif --}}
      </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
      <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    {{-- STATS --}}
    <div class="row g-3 mb-4">
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="ef-stat ef-stat--total">
          <div class="ef-stat__body">
            <div class="ef-stat__label">Total de oficiais</div>
            <div class="ef-stat__value">{{ $stats['total'] ?? 0 }}</div>
            <div class="ef-stat__line"></div>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3">
        <div class="ef-stat ef-stat--ativos">
          <div class="ef-stat__body">
            <div class="ef-stat__label">Ativos</div>
            <div class="ef-stat__value">{{ $stats['ativos'] ?? 0 }}</div>
            <div class="ef-stat__line"></div>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3">
        <div class="ef-stat ef-stat--suspensos">
          <div class="ef-stat__body">
            <div class="ef-stat__label">Suspensos</div>
            <div class="ef-stat__value">{{ $stats['suspensos'] ?? 0 }}</div>
            <div class="ef-stat__line"></div>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-xl-3">
        <div class="ef-stat ef-stat--desligados">
          <div class="ef-stat__body">
            <div class="ef-stat__label">Desligados</div>
            <div class="ef-stat__value">{{ $stats['desligados'] ?? 0 }}</div>
            <div class="ef-stat__line"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- FILTROS --}}
    <div class="card ef-filter-card mb-4">
      <div class="card-body">
        <div class="ef-section-title">Filtros de busca</div>

        <form method="GET" class="row g-3 align-items-end">
          <div class="col-md-5">
            <label class="ef-label">Busca</label>
            <input
              name="q"
              class="form-control ef-input"
              value="{{ $qVal }}"
              placeholder="Ex.: Administrador, 123, Diretor..."
            >
          </div>

          <div class="col-md-3">
            <label class="ef-label">Status</label>
            <select name="status" class="form-select ef-select">
              <option value="">Todos</option>
              <option value="ativo" @selected($statusVal==='ativo')>Ativo</option>
              <option value="suspenso" @selected($statusVal==='suspenso')>Suspenso</option>
              <option value="desligado" @selected($statusVal==='desligado')>Desligado</option>
            </select>
          </div>

          <div class="col-md-2">
            <label class="ef-label">Nível</label>
            <input
              type="number"
              min="1"
              max="10"
              name="nivel"
              class="form-control ef-input"
              value="{{ $nivelVal }}"
              placeholder="Ex.: 7"
            >
            <div class="text-muted small mt-2 fw-semibold">
              @if($nivelLabel)
                {{ $nv }} — {{ $nivelLabel }}
              @endif
            </div>
          </div>

          <div class="col-md-2">
            <label class="ef-label">Por página</label>
            <select name="per_page" class="form-select ef-select">
              @foreach([10,25,50,100] as $n)
                <option value="{{ $n }}" @selected($ppVal===$n)>{{ $n }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 d-flex gap-2 flex-wrap pt-1">
            <a href="{{ route('efetivo.index') }}" class="btn ef-btn ef-btn-soft">
              Limpar
            </a>
            <button class="btn btn-primary ef-btn ef-btn-primary">
              Filtrar
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- PROMOÇÃO RÁPIDA --}}
    @if($canPromote)
      <div class="card ef-promo-card mb-4">
        <div class="card-body p-0">
          <button
            class="ef-collapse-btn"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#promoCollapse"
            aria-expanded="false"
            aria-controls="promoCollapse"
            id="btnTogglePromo"
          >
            <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
              <div>
                <div class="fw-black fs-5 mb-1">Promoção rápida</div>
                <div class="text-muted small fw-semibold">
                  Clique para abrir e selecionar oficiais. Hierarquia, restrições de nível e auditoria continuam sendo respeitadas.
                </div>
              </div>

              <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="badge rounded-pill text-bg-dark px-3 py-2" style="font-weight:900;">
                  Seu nível: {{ $authNivel }}
                </span>
                <div class="text-muted fw-semibold small">
                  Selecionados: <span class="fw-black" id="countSelecionados">0</span>
                </div>
              </div>
            </div>
          </button>

          <div class="collapse" id="promoCollapse">
            <div class="px-3 px-md-4 pb-4">

              <form method="POST" action="{{ route('efetivo.promover_massa') }}" id="formPromocaoMassa" class="mt-2">
                @csrf

                <div class="row g-3 align-items-start">
                  <div class="col-md-3">
                    <label class="ef-label">Ação</label>
                    <select name="modo" id="modoMassa" class="form-select ef-select" required>
                      <option value="up">Promover +1 nível</option>
                      <option value="set">Definir nível</option>
                    </select>
                    <div class="text-muted small mt-2 fw-semibold">Use “Definir nível” para reorganizações rápidas.</div>
                  </div>

                  <div class="col-md-3" id="wrapNivelMassa" style="display:none;">
                    <label class="ef-label">Nível alvo</label>
                    <select name="nivel" id="nivelMassa" class="form-select ef-select">
                      @foreach($cargos as $nivel => $cargo)
                        @php $nivel = (int)$nivel; @endphp
                        @continue($nivel < 1 || $nivel > 10)
                        @if(!$isDiretor && $nivel >= 9) @continue @endif
                        @if($nivel > $authNivel) @continue @endif
                        <option value="{{ $nivel }}">{{ $nivel }} — {{ $cargo }}</option>
                      @endforeach
                    </select>
                    <div class="text-muted small mt-2 fw-semibold">Respeita seu nível máximo.</div>
                  </div>

                  <div class="col-md-6">
                    <label class="ef-label">Motivo</label>
                    <input
                      name="motivo"
                      class="form-control ef-input"
                      maxlength="200"
                      required
                      placeholder="Ex.: mérito, desempenho, tempo de serviço, correção administrativa..."
                    >
                    <div class="text-muted small mt-2 fw-semibold">
                      O motivo será registrado na auditoria para cada oficial alterado.
                    </div>
                  </div>

                  <div class="col-12 d-flex gap-2 flex-wrap">
                    <button class="btn btn-primary ef-btn ef-btn-primary" id="btnAplicarMassa" disabled>
                      Aplicar
                    </button>

                    <button type="button" class="btn ef-btn ef-btn-soft" id="btnSelecionarTodos">
                      Selecionar visíveis
                    </button>

                    <button type="button" class="btn ef-btn ef-btn-soft" id="btnLimparSelecao">
                      Limpar seleção
                    </button>
                  </div>

                  <div class="col-12">
                    <div class="alert ef-alert mb-0 small">
                      <b>Importante:</b> serão ignorados automaticamente:
                      <b>Suspensos/Desligados</b>, <b>você mesmo</b>, <b>níveis acima do seu</b>,
                      e, caso você não seja nível 10, qualquer mudança envolvendo <b>9/10</b>.
                    </div>
                  </div>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    @endif

    {{-- TABELA --}}
    <div class="card ef-table-card">
      <div class="ef-table-top d-flex align-items-start justify-content-between gap-3 flex-wrap">
        <div>
          <div class="ef-table-title">Lista do efetivo</div>
          <div class="ef-table-sub">Consulte oficiais, cargos, níveis, status e ações disponíveis conforme sua permissão.</div>
        </div>

        <div class="ef-table-meta">
          <span class="ef-mini-chip">Total geral: <strong>{{ $totalGeral }}</strong></span>
          <span class="ef-mini-chip">Nesta página: <strong>{{ $itensPagina }}</strong></span>
          <span class="ef-mini-chip">Filtros: <strong>{{ $filtrosAtivos ? 'ativos' : 'nenhum' }}</strong></span>
        </div>
      </div>

      <div class="table-responsive ef-table-wrap" id="wrapTabelaEfetivo">
        <table class="table table-sm align-middle ef-table">
          <thead>
            <tr>
              @if($canPromote)
                <th class="sel-col text-center" style="width:44px;">
                  <input type="checkbox" class="form-check-input" id="chkAllPage">
                </th>
              @endif

              <th style="width:70px;">ID</th>
              <th>Oficial</th>
              <th style="width:120px;">RG</th>
              <th style="width:210px;">Cargo</th>
              <th style="width:90px;">Nível</th>
              <th style="width:90px;">Discord</th>
              <th style="width:120px;">Status</th>

              @if($canManage)
                <th style="width:280px;">Ações</th>
              @endif
            </tr>
          </thead>

          <tbody>
            @forelse($users as $u)
              @php
                $nivelU = (int)($u->nivel ?? 0);
                $cargoShow = $u->cargo ?: ($cargos[$nivelU] ?? '—');

                $badge = match($u->status){
                  'ativo' => 'text-bg-success',
                  'suspenso' => 'text-bg-warning',
                  'desligado' => 'text-bg-secondary',
                  default => 'text-bg-light',
                };

                $isSelfRow = $auth && ((int)$auth->id === (int)$u->id);
                $selectable = $canPromote && ((string)$u->status === 'ativo') && !$isSelfRow;

                $partesNome = preg_split('/\s+/', trim((string) $u->name));
                $iniciais = '';
                foreach (array_slice($partesNome, 0, 2) as $parte) {
                  $iniciais .= mb_strtoupper(mb_substr($parte, 0, 1));
                }
                $iniciais = $iniciais ?: 'OF';
              @endphp

              <tr>
                @if($canPromote)
                  <td class="sel-col text-center">
                    <input
                      type="checkbox"
                      class="form-check-input chkUser"
                      value="{{ $u->id }}"
                      @disabled(!$selectable)
                    >
                  </td>
                @endif

                <td class="fw-bold">{{ $u->id }}</td>

                <td>
                  <div class="ef-user">
                    <div class="ef-avatar">{{ $iniciais }}</div>

                    <div class="ef-user__body">
                      <div class="ef-name">
                        {{ $u->name }}
                        @if($isSelfRow)
                          <span class="ef-pill-you">VOCÊ</span>
                        @endif
                      </div>
                      <div class="ef-email">{{ $u->email }}</div>
                    </div>
                  </div>
                </td>

                <td class="ef-rg">{{ $u->rg }}</td>
                <td class="ef-cargo">{{ $cargoShow }}</td>
                <td class="ef-nivel">{{ $u->nivel }}</td>

                <td>
                  <span class="badge rounded-pill ef-status {{ $u->discord ? 'bg-success' : 'bg-danger' }}">
                    {{ $u->discord ? 'Vinculado' : 'Não vinculado' }}
                  </span>
                </td>

                <td>
                  <span class="badge rounded-pill ef-status {{ $badge }}">
                    {{ strtoupper($u->status) }}
                  </span>
                </td>

                @if($canManage)
                  <td>
                    <div class="ef-actions">
                      <a href="{{ route('efetivo.show', $u) }}" class="btn btn-sm btn-outline-secondary">
                        Ver
                      </a>

                      <a href="{{ route('efetivo.edit', $u) }}" class="btn btn-sm btn-primary">
                        Editar
                      </a>

                      @if($canPromote)
                        <button
                          type="button"
                          class="btn btn-sm btn-outline-success btnPromover"
                          data-user-id="{{ $u->id }}"
                          data-user-name="{{ e($u->name) }}"
                          @disabled(!$selectable)
                        >
                          +1 nível
                        </button>
                      @endif
                    </div>
                  </td>
                @endif
              </tr>
            @empty
              <tr>
                @php
                  $colspan = 7;
                  if ($canPromote) $colspan++;
                  if ($canManage) $colspan++;
                @endphp
                <td colspan="{{ $colspan }}" class="text-center ef-empty">
                  Nenhum oficial encontrado.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="ef-pagination">
        {{ $users->links('pagination::bootstrap-5') }}
      </div>
    </div>

  </div>
</div>

@if($canPromote)
  <div class="modal fade ef-modal" id="modalPromover" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-black">Promover oficial</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <form method="POST" id="formPromover" action="">
          @csrf
          <div class="modal-body p-4">
            <div class="text-muted fw-semibold small mb-2">
              Você está promovendo: <span class="fw-black" id="promoverNome">—</span>
            </div>

            <label class="ef-label">Motivo</label>
            <input
              name="motivo"
              class="form-control ef-input"
              maxlength="200"
              required
              placeholder="Ex.: mérito, desempenho, correção administrativa..."
            >

            <div class="alert ef-alert mt-3 mb-0 small">
              A promoção respeita hierarquia, travas 9/10 e só funciona para status <b>ATIVO</b>.
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn ef-btn ef-btn-soft" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-success ef-btn">Confirmar +1</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const canPromote = @json($canPromote);

    const wrapTabela = document.getElementById('wrapTabelaEfetivo');
    const promoCollapse = document.getElementById('promoCollapse');

    const chkAllPage = document.getElementById('chkAllPage');
    const btnAplicarMassa = document.getElementById('btnAplicarMassa');
    const countSelecionados = document.getElementById('countSelecionados');

    const btnSelecionarTodos = document.getElementById('btnSelecionarTodos');
    const btnLimparSelecao = document.getElementById('btnLimparSelecao');

    const getChecks = () => Array.from(document.querySelectorAll('.chkUser'));

    const updateCount = () => {
      if (!canPromote) return;
      const selected = getChecks().filter(c => c.checked);
      if (countSelecionados) countSelecionados.textContent = String(selected.length);
      if (btnAplicarMassa) btnAplicarMassa.disabled = selected.length === 0;
    };

    const clearSelection = () => {
      getChecks().forEach(c => c.checked = false);
      if (chkAllPage) chkAllPage.checked = false;
      updateCount();
    };

    const setPromoOpen = (open) => {
      if (!wrapTabela) return;

      if (open) {
        wrapTabela.classList.add('promocao-aberta');
      } else {
        wrapTabela.classList.remove('promocao-aberta');
        clearSelection();
      }
    };

    setPromoOpen(false);

    if (promoCollapse) {
      promoCollapse.addEventListener('shown.bs.collapse', () => setPromoOpen(true));
      promoCollapse.addEventListener('hidden.bs.collapse', () => setPromoOpen(false));
    }

    const modoMassa = document.getElementById('modoMassa');
    const wrapNivelMassa = document.getElementById('wrapNivelMassa');

    if (modoMassa && wrapNivelMassa) {
      const syncModo = () => {
        wrapNivelMassa.style.display = (modoMassa.value === 'set') ? '' : 'none';
      };
      modoMassa.addEventListener('change', syncModo);
      syncModo();
    }

    if (chkAllPage) {
      chkAllPage.addEventListener('change', () => {
        getChecks().forEach(c => {
          if (c.disabled) return;
          c.checked = chkAllPage.checked;
        });
        updateCount();
      });
    }

    if (btnSelecionarTodos) {
      btnSelecionarTodos.addEventListener('click', () => {
        getChecks().forEach(c => {
          if (!c.disabled) c.checked = true;
        });
        if (chkAllPage) chkAllPage.checked = true;
        updateCount();
      });
    }

    if (btnLimparSelecao) {
      btnLimparSelecao.addEventListener('click', clearSelection);
    }

    getChecks().forEach(c => c.addEventListener('change', updateCount));
    updateCount();

    const formMassa = document.getElementById('formPromocaoMassa');
    if (formMassa) {
      formMassa.addEventListener('submit', (e) => {
        formMassa.querySelectorAll('input[name="ids[]"]._dyn').forEach(x => x.remove());

        const selectedIds = getChecks().filter(c => c.checked).map(c => c.value);

        if (selectedIds.length === 0) {
          e.preventDefault();
          return;
        }

        selectedIds.forEach(id => {
          const inp = document.createElement('input');
          inp.type = 'hidden';
          inp.name = 'ids[]';
          inp.value = id;
          inp.className = '_dyn';
          formMassa.appendChild(inp);
        });

        if (btnAplicarMassa) {
          btnAplicarMassa.disabled = true;
          btnAplicarMassa.innerText = 'Aplicando...';
        }
      });
    }

    const btnsPromover = Array.from(document.querySelectorAll('.btnPromover'));
    const modalEl = document.getElementById('modalPromover');
    const nomeEl = document.getElementById('promoverNome');
    const formPromover = document.getElementById('formPromover');

    if (btnsPromover.length && modalEl && formPromover) {
      const modal = new bootstrap.Modal(modalEl);

      btnsPromover.forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-user-id');
          const nome = btn.getAttribute('data-user-name') || '—';

          if (nomeEl) nomeEl.textContent = nome;

          formPromover.action = "{{ url('/efetivo') }}/" + id + "/promover";

          const motivo = formPromover.querySelector('input[name="motivo"]');
          if (motivo) motivo.value = '';

          modal.show();
        });
      });

      formPromover.addEventListener('submit', () => {
        const submitBtn = formPromover.querySelector('button.btn-success');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerText = 'Confirmando...';
        }
      });
    }
  });
</script>
@endsection