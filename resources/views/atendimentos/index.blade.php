@extends('layouts.app')

@section('content')
@php
  $statusMap = [
    'aberto'      => ['label' => 'ABERTO',      'class' => 'is-open'],
    'em_analise'  => ['label' => 'EM ANÁLISE',  'class' => 'is-review'],
    'resolvido'   => ['label' => 'RESOLVIDO',   'class' => 'is-done'],
    'arquivado'   => ['label' => 'ARQUIVADO',   'class' => 'is-archived'],
  ];

  $tipoMap = [
    'Denúncia'    => 'is-denuncia',
    'Solicitação' => 'is-solicitacao',
    'Sugestão'    => 'is-sugestao',
    'Elogio'      => 'is-elogio',
  ];
@endphp

<style>
  /* =========================================================
     GRR 3.0 — CANAIS DE ATENDIMENTO
     ========================================================= */
  .att-page{
    --att-bg: linear-gradient(180deg, rgba(248,250,252,1) 0%, rgba(241,245,249,1) 100%);
    --att-card: rgba(255,255,255,.88);
    --att-card-border: rgba(15,23,42,.08);
    --att-text: #0f172a;
    --att-muted: #64748b;
    --att-soft: #e2e8f0;
    --att-primary: #2563eb;
    --att-primary-2: #1d4ed8;
    --att-gold: #d4af37;
    --att-success: #16a34a;
    --att-warning: #f59e0b;
    --att-danger: #dc2626;
    --att-dark: #334155;
    --att-shadow: 0 20px 60px rgba(15,23,42,.08);
    --att-radius-xl: 24px;
    --att-radius-lg: 18px;
    --att-radius-md: 14px;
    color: var(--att-text);
  }

  body.theme-dark .att-page,
  html.theme-dark .att-page,
  [data-theme="dark"] .att-page,
  body.dark .att-page,
  html.dark .att-page{
    --att-bg: radial-gradient(circle at top left, rgba(37,99,235,.14), transparent 28%),
              radial-gradient(circle at top right, rgba(212,175,55,.10), transparent 24%),
              linear-gradient(180deg, #07101d 0%, #0b1220 100%);
    --att-card: rgba(2,6,23,.70);
    --att-card-border: rgba(148,163,184,.16);
    --att-text: #e2e8f0;
    --att-muted: rgba(226,232,240,.68);
    --att-soft: rgba(148,163,184,.18);
    --att-shadow: 0 25px 70px rgba(0,0,0,.35);
  }

  .att-page{
    background: var(--att-bg);
    border-radius: 28px;
    padding: 8px;
  }

  .att-wrap{
    max-width: 1280px;
    margin: 0 auto;
  }

  .att-kicker{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    letter-spacing: .16em;
    text-transform: uppercase;
    font-weight: 900;
    font-size: .74rem;
    color: var(--att-primary);
    margin-bottom: 10px;
  }

  .att-kicker::before{
    content: "";
    width: 30px;
    height: 2px;
    border-radius: 999px;
    background: currentColor;
    opacity: .9;
  }

  .att-hero{
    position: relative;
    overflow: hidden;
    background: var(--att-card);
    border: 1px solid var(--att-card-border);
    box-shadow: var(--att-shadow);
    border-radius: var(--att-radius-xl);
    padding: 24px;
    margin-bottom: 20px;
    backdrop-filter: blur(14px);
  }

  .att-hero::before{
    content: "";
    position: absolute;
    inset: 0;
    background:
      radial-gradient(circle at 12% 18%, rgba(37,99,235,.12), transparent 26%),
      radial-gradient(circle at 88% 10%, rgba(212,175,55,.10), transparent 22%);
    pointer-events: none;
  }

  .att-hero__inner{
    position: relative;
    z-index: 1;
  }

  .att-hero__title{
    font-size: clamp(1.5rem, 2.4vw, 2.4rem);
    font-weight: 900;
    line-height: 1.05;
    margin-bottom: 8px;
    color: var(--att-text);
  }

  .att-hero__sub{
    max-width: 760px;
    color: var(--att-muted);
    font-size: .98rem;
    margin-bottom: 0;
  }

  .att-hero__stats{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
  }

  .att-chip{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 999px;
    background: rgba(37,99,235,.08);
    border: 1px solid rgba(37,99,235,.14);
    color: var(--att-text);
    font-weight: 800;
    font-size: .85rem;
  }

  body.theme-dark .att-chip,
  html.theme-dark .att-chip,
  [data-theme="dark"] .att-chip,
  body.dark .att-chip,
  html.dark .att-chip{
    background: rgba(37,99,235,.12);
    border-color: rgba(96,165,250,.18);
  }

  .att-panel{
    background: var(--att-card);
    border: 1px solid var(--att-card-border);
    box-shadow: var(--att-shadow);
    border-radius: var(--att-radius-xl);
    backdrop-filter: blur(14px);
  }

  .att-panel__head{
    padding: 20px 22px 0;
  }

  .att-panel__title{
    font-size: 1.02rem;
    font-weight: 900;
    margin: 0;
    color: var(--att-text);
  }

  .att-panel__desc{
    margin-top: 4px;
    color: var(--att-muted);
    font-size: .92rem;
  }

  .att-panel__body{
    padding: 20px 22px 22px;
  }

  .att-form-label{
    font-size: .83rem;
    font-weight: 900;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--att-muted);
    margin-bottom: 8px;
  }

  .att-page .form-control,
  .att-page .form-select{
    min-height: 48px;
    border-radius: 14px;
    border: 1px solid var(--att-card-border);
    background: rgba(255,255,255,.84);
    color: var(--att-text);
    box-shadow: none !important;
  }

  .att-page .form-control::placeholder{
    color: #94a3b8;
  }

  body.theme-dark .att-page .form-control,
  body.theme-dark .att-page .form-select,
  html.theme-dark .att-page .form-control,
  html.theme-dark .att-page .form-select,
  [data-theme="dark"] .att-page .form-control,
  [data-theme="dark"] .att-page .form-select,
  body.dark .att-page .form-control,
  body.dark .att-page .form-select,
  html.dark .att-page .form-control,
  html.dark .att-page .form-select{
    background: rgba(15,23,42,.68);
    border-color: rgba(148,163,184,.22);
    color: #e2e8f0;
  }

  body.theme-dark .att-page .form-control::placeholder,
  html.theme-dark .att-page .form-control::placeholder,
  [data-theme="dark"] .att-page .form-control::placeholder,
  body.dark .att-page .form-control::placeholder,
  html.dark .att-page .form-control::placeholder{
    color: rgba(226,232,240,.42);
  }

  body.theme-dark .att-page .form-select option,
  html.theme-dark .att-page .form-select option,
  [data-theme="dark"] .att-page .form-select option,
  body.dark .att-page .form-select option,
  html.dark .att-page .form-select option{
    background: #0b1220;
    color: #e2e8f0;
  }

  .att-page .btn{
    min-height: 46px;
    border-radius: 14px;
    font-weight: 900;
    letter-spacing: .01em;
  }

  .att-page .btn-primary{
    background: linear-gradient(135deg, var(--att-primary), var(--att-primary-2));
    border: none;
    box-shadow: 0 14px 28px rgba(37,99,235,.24);
  }

  .att-page .btn-primary:hover{
    transform: translateY(-1px);
  }

  .att-page .btn-outline-secondary{
    border-color: rgba(148,163,184,.35);
  }

  body.theme-dark .att-page .btn-outline-secondary,
  html.theme-dark .att-page .btn-outline-secondary,
  [data-theme="dark"] .att-page .btn-outline-secondary,
  body.dark .att-page .btn-outline-secondary,
  html.dark .att-page .btn-outline-secondary{
    border-color: rgba(148,163,184,.25);
    color: #e2e8f0;
    background: rgba(15,23,42,.35);
  }

  .att-table-wrap{
    overflow: hidden;
    border-radius: 0 0 var(--att-radius-xl) var(--att-radius-xl);
  }

  .att-page .table{
    margin-bottom: 0;
    color: var(--att-text);
  }

  .att-page .table thead th{
    font-size: .79rem;
    text-transform: uppercase;
    letter-spacing: .06em;
    font-weight: 900;
    color: var(--att-muted);
    background: rgba(148,163,184,.10);
    border-bottom: 1px solid var(--att-card-border);
    padding-top: 16px;
    padding-bottom: 16px;
    white-space: nowrap;
  }

  body.theme-dark .att-page .table thead th,
  html.theme-dark .att-page .table thead th,
  [data-theme="dark"] .att-page .table thead th,
  body.dark .att-page .table thead th,
  html.dark .att-page .table thead th{
    background: rgba(15,23,42,.55);
  }

  .att-page .table tbody tr{
    border-color: var(--att-card-border);
    transition: .18s ease;
  }

  .att-page .table tbody td{
    padding-top: 16px;
    padding-bottom: 16px;
    vertical-align: middle;
  }

  .att-page .table-hover tbody tr:hover{
    background: rgba(37,99,235,.045);
  }

  body.theme-dark .att-page .table-hover tbody tr:hover,
  html.theme-dark .att-page .table-hover tbody tr:hover,
  [data-theme="dark"] .att-page .table-hover tbody tr:hover,
  body.dark .att-page .table-hover tbody tr:hover,
  html.dark .att-page .table-hover tbody tr:hover{
    background: rgba(59,130,246,.08);
  }

  .att-id{
    font-weight: 900;
    color: var(--att-muted);
  }

  .att-subject{
    font-weight: 800;
    color: var(--att-text);
    line-height: 1.2;
    margin-bottom: 4px;
  }

  .att-message{
    color: var(--att-muted);
    font-size: .9rem;
    line-height: 1.45;
    max-width: 720px;
  }

  .att-badge{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: .76rem;
    font-weight: 900;
    line-height: 1;
    border: 1px solid transparent;
    text-transform: uppercase;
    letter-spacing: .04em;
    white-space: nowrap;
  }

  .att-badge--tipo.is-denuncia{
    background: rgba(220,38,38,.10);
    color: #b91c1c;
    border-color: rgba(220,38,38,.14);
  }
  .att-badge--tipo.is-solicitacao{
    background: rgba(37,99,235,.10);
    color: #1d4ed8;
    border-color: rgba(37,99,235,.14);
  }
  .att-badge--tipo.is-sugestao{
    background: rgba(245,158,11,.12);
    color: #b45309;
    border-color: rgba(245,158,11,.18);
  }
  .att-badge--tipo.is-elogio{
    background: rgba(22,163,74,.10);
    color: #15803d;
    border-color: rgba(22,163,74,.14);
  }

  .att-badge--status.is-open{
    background: rgba(220,38,38,.10);
    color: #b91c1c;
    border-color: rgba(220,38,38,.16);
  }
  .att-badge--status.is-review{
    background: rgba(245,158,11,.12);
    color: #b45309;
    border-color: rgba(245,158,11,.18);
  }
  .att-badge--status.is-done{
    background: rgba(22,163,74,.10);
    color: #15803d;
    border-color: rgba(22,163,74,.14);
  }
  .att-badge--status.is-archived{
    background: rgba(100,116,139,.14);
    color: #475569;
    border-color: rgba(100,116,139,.18);
  }

  body.theme-dark .att-badge--tipo.is-denuncia,
  html.theme-dark .att-badge--tipo.is-denuncia,
  [data-theme="dark"] .att-badge--tipo.is-denuncia,
  body.dark .att-badge--tipo.is-denuncia,
  html.dark .att-badge--tipo.is-denuncia{
    background: rgba(239,68,68,.14);
    color: #fecaca;
    border-color: rgba(239,68,68,.16);
  }
  body.theme-dark .att-badge--tipo.is-solicitacao,
  html.theme-dark .att-badge--tipo.is-solicitacao,
  [data-theme="dark"] .att-badge--tipo.is-solicitacao,
  body.dark .att-badge--tipo.is-solicitacao,
  html.dark .att-badge--tipo.is-solicitacao{
    background: rgba(59,130,246,.16);
    color: #bfdbfe;
    border-color: rgba(96,165,250,.18);
  }
  body.theme-dark .att-badge--tipo.is-sugestao,
  html.theme-dark .att-badge--tipo.is-sugestao,
  [data-theme="dark"] .att-badge--tipo.is-sugestao,
  body.dark .att-badge--tipo.is-sugestao,
  html.dark .att-badge--tipo.is-sugestao{
    background: rgba(245,158,11,.16);
    color: #fde68a;
    border-color: rgba(245,158,11,.18);
  }
  body.theme-dark .att-badge--tipo.is-elogio,
  html.theme-dark .att-badge--tipo.is-elogio,
  [data-theme="dark"] .att-badge--tipo.is-elogio,
  body.dark .att-badge--tipo.is-elogio,
  html.dark .att-badge--tipo.is-elogio{
    background: rgba(34,197,94,.16);
    color: #bbf7d0;
    border-color: rgba(34,197,94,.18);
  }

  body.theme-dark .att-badge--status.is-open,
  html.theme-dark .att-badge--status.is-open,
  [data-theme="dark"] .att-badge--status.is-open,
  body.dark .att-badge--status.is-open,
  html.dark .att-badge--status.is-open{
    background: rgba(239,68,68,.14);
    color: #fecaca;
    border-color: rgba(239,68,68,.16);
  }
  body.theme-dark .att-badge--status.is-review,
  html.theme-dark .att-badge--status.is-review,
  [data-theme="dark"] .att-badge--status.is-review,
  body.dark .att-badge--status.is-review,
  html.dark .att-badge--status.is-review{
    background: rgba(245,158,11,.16);
    color: #fde68a;
    border-color: rgba(245,158,11,.18);
  }
  body.theme-dark .att-badge--status.is-done,
  html.theme-dark .att-badge--status.is-done,
  [data-theme="dark"] .att-badge--status.is-done,
  body.dark .att-badge--status.is-done,
  html.dark .att-badge--status.is-done{
    background: rgba(34,197,94,.16);
    color: #bbf7d0;
    border-color: rgba(34,197,94,.18);
  }
  body.theme-dark .att-badge--status.is-archived,
  html.theme-dark .att-badge--status.is-archived,
  [data-theme="dark"] .att-badge--status.is-archived,
  body.dark .att-badge--status.is-archived,
  html.dark .att-badge--status.is-archived{
    background: rgba(148,163,184,.14);
    color: #cbd5e1;
    border-color: rgba(148,163,184,.18);
  }

  .att-actions{
    display: flex;
    justify-content: flex-end;
  }

  .att-empty{
    text-align: center;
    padding: 44px 20px;
  }

  .att-empty__icon{
    width: 72px;
    height: 72px;
    margin: 0 auto 14px;
    border-radius: 22px;
    display: grid;
    place-items: center;
    font-size: 1.8rem;
    background: rgba(148,163,184,.10);
    border: 1px solid var(--att-card-border);
  }

  .att-empty__title{
    font-size: 1.05rem;
    font-weight: 900;
    color: var(--att-text);
    margin-bottom: 6px;
  }

  .att-empty__text{
    color: var(--att-muted);
    max-width: 520px;
    margin: 0 auto;
  }

  .att-page .alert{
    border-radius: 18px;
    border: 1px solid transparent;
    box-shadow: 0 10px 30px rgba(15,23,42,.06);
  }

  .att-page .pagination{
    gap: 6px;
    flex-wrap: wrap;
  }

  .att-page .pagination .page-link{
    border-radius: 12px !important;
    min-width: 42px;
    text-align: center;
    border-color: var(--att-card-border);
    color: var(--att-text);
    background: var(--att-card);
  }

  body.theme-dark .att-page .pagination .page-link,
  html.theme-dark .att-page .pagination .page-link,
  [data-theme="dark"] .att-page .pagination .page-link,
  body.dark .att-page .pagination .page-link,
  html.dark .att-page .pagination .page-link{
    background: rgba(15,23,42,.68);
    border-color: rgba(148,163,184,.22);
    color: #e2e8f0;
  }

  .att-page .pagination .page-item.active .page-link{
    background: linear-gradient(135deg, var(--att-primary), var(--att-primary-2));
    border-color: transparent;
    color: #fff;
    font-weight: 900;
    box-shadow: 0 10px 24px rgba(37,99,235,.25);
  }

  @media (max-width: 991.98px){
    .att-hero{
      padding: 20px;
    }

    .att-panel__head,
    .att-panel__body{
      padding-left: 16px;
      padding-right: 16px;
    }

    .att-message{
      max-width: 100%;
    }
  }

  @media (max-width: 767.98px){
    .att-page{
      border-radius: 22px;
      padding: 4px;
    }

    .att-hero__stats{
      gap: 8px;
    }

    .att-chip{
      width: 100%;
      justify-content: center;
    }

    .att-actions{
      justify-content: flex-start;
    }
  }
</style>

<div class="container-fluid py-3 att-page">
  <div class="att-wrap">

    {{-- HERO --}}
    <div class="att-hero">
      <div class="att-hero__inner d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
        <div>
          <div class="att-kicker">GRR • PRF</div>
          <h1 class="att-hero__title">Canais de Atendimento</h1>
          <p class="att-hero__sub">
            Central de acompanhamento de manifestações recebidas pela instituição, reunindo denúncias, solicitações, sugestões e elogios em um painel mais claro, organizado e profissional.
          </p>

          <div class="att-hero__stats">
            <span class="att-chip">Denúncia</span>
            <span class="att-chip">Solicitação</span>
            <span class="att-chip">Sugestão</span>
            <span class="att-chip">Elogio</span>
          </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
          <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4">
            Voltar ao Dashboard
          </a>
        </div>
      </div>
    </div>

    {{-- ALERTAS --}}
    @if (session('success'))
      <div class="alert alert-success d-flex justify-content-between align-items-center mb-3">
        <div class="fw-semibold">{{ session('success') }}</div>
        <button class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger mb-3">
        <div class="fw-bold mb-2">Ops! Verifique os campos abaixo:</div>
        <ul class="mb-0 ps-3">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- FILTROS --}}
    <div class="att-panel mb-3">
      <div class="att-panel__head">
        <h2 class="att-panel__title">Filtros de consulta</h2>
        <div class="att-panel__desc">
          Refine a visualização por texto, tipo de manifestação ou status do atendimento.
        </div>
      </div>

      <div class="att-panel__body">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('atendimentos.index') }}">
          <div class="col-12 col-lg-5">
            <label class="att-form-label">Busca</label>
            <input
              type="text"
              class="form-control"
              name="busca"
              value="{{ request('busca') }}"
              placeholder="Assunto, mensagem, nome, contato..."
            >
          </div>

          <div class="col-12 col-md-6 col-lg-3">
            <label class="att-form-label">Tipo</label>
            <select name="tipo" class="form-select">
              <option value="">Todos</option>
              @foreach (['Denúncia','Solicitação','Sugestão','Elogio'] as $t)
                <option value="{{ $t }}" @selected(request('tipo') === $t)>{{ $t }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-6 col-lg-2">
            <label class="att-form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">Todos</option>
              <option value="aberto" @selected(request('status') === 'aberto')>Aberto</option>
              <option value="em_analise" @selected(request('status') === 'em_analise')>Em análise</option>
              <option value="resolvido" @selected(request('status') === 'resolvido')>Resolvido</option>
              <option value="arquivado" @selected(request('status') === 'arquivado')>Arquivado</option>
            </select>
          </div>

          <div class="col-12 col-lg-2 d-grid">
            <button class="btn btn-primary">
              Filtrar resultados
            </button>
          </div>

          <div class="col-12">
            <a href="{{ route('atendimentos.index') }}" class="btn btn-outline-secondary btn-sm px-3">
              Limpar filtros
            </a>
          </div>
        </form>
      </div>
    </div>

    {{-- LISTAGEM --}}
    <div class="att-panel">
      <div class="att-panel__head">
        <h2 class="att-panel__title">Registros de atendimento</h2>
        <div class="att-panel__desc">
          Visualização dos atendimentos cadastrados com status, classificação e data de abertura.
        </div>
      </div>

      <div class="att-table-wrap table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th style="width: 90px;">Protocolo</th>
              <th style="width: 170px;">Tipo</th>
              <th>Assunto</th>
              <th style="width: 180px;">Status</th>
              <th style="width: 180px;">Data</th>
              <th style="width: 130px;" class="text-end">Ações</th>
            </tr>
          </thead>

          <tbody>
            @forelse($atendimentos as $a)
              @php
                $statusInfo = $statusMap[$a->status] ?? ['label' => strtoupper(str_replace('_', ' ', $a->status)), 'class' => 'is-archived'];
                $tipoClass = $tipoMap[$a->tipo] ?? 'is-solicitacao';
              @endphp

              <tr>
                <td>
                  <span class="att-id">#{{ $a->id }}</span>
                </td>

                <td>
                  <span class="att-badge att-badge--tipo {{ $tipoClass }}">
                    {{ $a->tipo }}
                  </span>
                </td>

                <td>
                  <div class="att-subject">{{ $a->assunto }}</div>
                  <div class="att-message text-truncate">
                    {{ $a->mensagem }}
                  </div>
                </td>

                <td>
                  <span class="att-badge att-badge--status {{ $statusInfo['class'] }}">
                    {{ $statusInfo['label'] }}
                  </span>
                </td>

                <td class="text-muted">
                  {{ optional($a->created_at)->format('d/m/Y H:i') }}
                </td>

                <td>
                  <div class="att-actions">
                    <a class="btn btn-sm btn-outline-primary px-3"
                       href="{{ route('atendimentos.show', $a) }}">
                      Ver
                    </a>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6">
                  <div class="att-empty">
                    <div class="att-empty__icon">📭</div>
                    <div class="att-empty__title">Nenhum atendimento encontrado</div>
                    <div class="att-empty__text">
                      Não existem registros compatíveis com os filtros aplicados no momento. Ajuste os critérios de busca para visualizar outros resultados.
                    </div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- PAGINAÇÃO --}}
    <div class="mt-3">
      {{ $atendimentos->appends(request()->query())->links() }}
    </div>
  </div>
</div>
@endsection