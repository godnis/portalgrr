@extends('layouts.app')

@section('content')
@php
  use App\Models\Ticket;

  $first = method_exists($tickets, 'firstItem') ? $tickets->firstItem() : null;
  $last  = method_exists($tickets, 'lastItem')  ? $tickets->lastItem()  : null;
  $total = method_exists($tickets, 'total')     ? $tickets->total()     : (is_countable($tickets) ? count($tickets) : null);

  $showCount = !is_null($first) && !is_null($last) && !is_null($total);
@endphp

<style>
  /* =========================================================
     GRR 3.0 • TICKETS ADMIN INDEX
  ========================================================= */
  .tadm-wrap{
    max-width: 1440px;
    margin: 0 auto;
    padding: 18px 14px 28px;
  }

  /* HERO */
  .tadm-hero{
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    border: 1px solid rgba(2,6,23,.10);
    background: linear-gradient(135deg, rgba(8,14,28,.98), rgba(15,23,42,.94));
    box-shadow:
      0 24px 70px rgba(2,6,23,.18),
      inset 0 1px 0 rgba(255,255,255,.05);
  }

  .tadm-hero__bg{
    position: absolute;
    inset: -60px;
    pointer-events: none;
    opacity: .95;
    background:
      radial-gradient(900px 340px at 12% 18%, rgba(59,130,246,.22), transparent 60%),
      radial-gradient(760px 320px at 88% 22%, rgba(16,185,129,.15), transparent 58%),
      radial-gradient(760px 320px at 50% 115%, rgba(168,85,247,.14), transparent 62%);
    filter: blur(14px);
  }

  .tadm-hero__content{
    position: relative;
    z-index: 1;
    padding: 22px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    flex-wrap: wrap;
  }

  .tadm-kicker{
    color: rgba(226,232,240,.68);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .18em;
    text-transform: uppercase;
    margin-bottom: 10px;
  }

  .tadm-title{
    margin: 0;
    color: #f8fafc;
    font-size: clamp(1.55rem, 2.2vw, 2.2rem);
    line-height: 1.08;
    font-weight: 950;
    letter-spacing: -.02em;
  }

  .tadm-sub{
    margin-top: 10px;
    color: rgba(226,232,240,.78);
    font-size: .96rem;
    line-height: 1.6;
    font-weight: 500;
  }

  .tadm-hero__right{
    display: flex;
    align-items: flex-start;
    gap: 10px;
    flex-wrap: wrap;
  }

  .tadm-chip{
    display: inline-flex;
    align-items: center;
    min-height: 42px;
    padding: 0 14px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.08);
    color: #f8fafc;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
  }

  /* ALERTAS */
  .tadm-alert{
    border-radius: 18px;
    padding: 15px 16px;
    box-shadow: 0 12px 32px rgba(15,23,42,.06);
  }

  /* CARD */
  .tadm-card{
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(15,23,42,.08);
    background: #ffffff;
    box-shadow: 0 18px 50px rgba(15,23,42,.07);
  }

  .tadm-card__head{
    padding: 18px 20px;
    border-bottom: 1px solid rgba(15,23,42,.08);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.06), transparent 30%),
      linear-gradient(180deg, rgba(248,250,252,1), rgba(255,255,255,1));
  }

  .tadm-card__title{
    margin: 0;
    color: #0f172a;
    font-size: 1.05rem;
    font-weight: 900;
  }

  .tadm-card__sub{
    margin-top: 4px;
    color: rgba(15,23,42,.52);
    font-size: .82rem;
    font-weight: 700;
  }

  .tadm-card__body{
    padding: 20px;
  }

  .tadm-card__footer{
    padding: 14px 18px;
    border-top: 1px solid rgba(15,23,42,.08);
    background: #fafbfc;
  }

  /* FILTROS */
  .tadm-label{
    display: block;
    margin-bottom: 8px;
    color: #0f172a;
    font-size: .82rem;
    font-weight: 900;
  }

  .tadm-input{
    min-height: 46px;
    border-radius: 14px;
    border: 1px solid rgba(15,23,42,.10);
    box-shadow: none !important;
    font-weight: 600;
  }

  .tadm-input:focus{
    border-color: rgba(13,110,253,.42);
    box-shadow: 0 0 0 .22rem rgba(13,110,253,.10) !important;
  }

  .tadm-btn,
  .tadm-btn-outline{
    min-height: 46px;
    border-radius: 14px;
    font-weight: 900;
    padding-inline: 18px;
  }

  .tadm-btn{
    box-shadow: 0 10px 24px rgba(13,110,253,.18);
  }

  /* TABELA */
  .tadm-table-wrap{
    overflow-x: auto;
  }

  .tadm-table{
    margin: 0;
    color: #0f172a;
  }

  .tadm-table thead th{
    padding: 14px 16px;
    background: #f8fafc;
    color: rgba(15,23,42,.56);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    border-bottom: 1px solid rgba(15,23,42,.08) !important;
    white-space: nowrap;
  }

  .tadm-table tbody td{
    padding: 16px;
    border-top: 1px solid rgba(15,23,42,.07) !important;
    vertical-align: middle;
    background: transparent !important;
  }

  .tadm-table tbody tr{
    transition: .18s ease;
  }

  .tadm-table tbody tr:hover td{
    background: rgba(15,23,42,.02) !important;
  }

  .tadm-row--new td{
    background: linear-gradient(90deg, rgba(245,158,11,.10), rgba(245,158,11,0) 55%) !important;
  }

  .tadm-muted{
    color: rgba(15,23,42,.58) !important;
  }

  .tadm-maintext{
    color: #0f172a;
    font-weight: 800;
  }

  .tadm-subtext{
    margin-top: 3px;
    color: rgba(15,23,42,.48);
    font-size: 12px;
    font-weight: 600;
  }

  .tadm-status{
    display: inline-flex;
    align-items: center;
    min-height: 32px;
    padding: 0 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
    border: 1px solid transparent;
  }

  .tadm-status--aberto{
    background: rgba(245,159,0,.12);
    color: #b45309;
    border-color: rgba(245,159,0,.22);
  }

  .tadm-status--andamento,
  .tadm-status--em_andamento{
    background: rgba(13,110,253,.10);
    color: #1d4ed8;
    border-color: rgba(13,110,253,.18);
  }

  .tadm-status--aguardando_usuario{
    background: rgba(100,116,139,.10);
    color: #475569;
    border-color: rgba(100,116,139,.18);
  }

  .tadm-status--resolvido,
  .tadm-status--fechado{
    background: rgba(25,135,84,.12);
    color: #047857;
    border-color: rgba(25,135,84,.18);
  }

  .tadm-status--default{
    background: rgba(100,116,139,.10);
    color: #475569;
    border-color: rgba(100,116,139,.18);
  }

  .tadm-badge-new{
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 10px;
    border-radius: 999px;
    background: rgba(220,53,69,.12);
    color: #b42318;
    border: 1px solid rgba(220,53,69,.18);
    font-size: 10px;
    font-weight: 950;
    letter-spacing: .10em;
    text-transform: uppercase;
  }

  .tadm-badge-mine{
    display: inline-flex;
    align-items: center;
    min-height: 26px;
    padding: 0 9px;
    border-radius: 999px;
    background: rgba(13,202,240,.12);
    color: #0c8599;
    border: 1px solid rgba(13,202,240,.18);
    font-size: 10px;
    font-weight: 950;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .tadm-action{
    border-radius: 12px;
    font-weight: 900;
    padding-inline: 14px;
  }

  .tadm-empty{
    padding: 44px 20px 48px;
    text-align: center;
    color: rgba(15,23,42,.58);
    font-weight: 600;
  }

  .tadm-pagination-meta{
    color: rgba(15,23,42,.58);
    font-size: .88rem;
    font-weight: 600;
  }

  /* DARK MODE */
  html[data-theme="dark"] .tadm-hero{
    border-color: rgba(255,255,255,.09);
    box-shadow:
      0 26px 70px rgba(0,0,0,.45),
      inset 0 1px 0 rgba(255,255,255,.04);
  }

  html[data-theme="dark"] .tadm-card{
    background: rgba(10,14,20,.78);
    border-color: rgba(255,255,255,.08);
    box-shadow: 0 18px 50px rgba(0,0,0,.35);
  }

  html[data-theme="dark"] .tadm-card__head{
    border-bottom-color: rgba(255,255,255,.08);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.08), transparent 30%),
      linear-gradient(180deg, rgba(15,20,28,.96), rgba(10,14,20,.92));
  }

  html[data-theme="dark"] .tadm-card__title,
  html[data-theme="dark"] .tadm-maintext,
  html[data-theme="dark"] .tadm-label{
    color: rgba(248,250,252,.95);
  }

  html[data-theme="dark"] .tadm-card__sub,
  html[data-theme="dark"] .tadm-muted,
  html[data-theme="dark"] .tadm-subtext,
  html[data-theme="dark"] .tadm-pagination-meta{
    color: rgba(226,232,240,.58) !important;
  }

  html[data-theme="dark"] .tadm-input{
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.10);
    color: rgba(248,250,252,.94);
  }

  html[data-theme="dark"] .tadm-input::placeholder{
    color: rgba(226,232,240,.42);
  }

  html[data-theme="dark"] .tadm-input:focus{
    border-color: rgba(147,197,253,.45);
    box-shadow: 0 0 0 .22rem rgba(59,130,246,.12) !important;
    background: rgba(255,255,255,.05);
    color: rgba(248,250,252,.96);
  }

  html[data-theme="dark"] .tadm-input option{
    background: #0b1220;
    color: rgba(248,250,252,.96);
  }

  html[data-theme="dark"] .tadm-table{
    color: rgba(248,250,252,.94);
  }

  html[data-theme="dark"] .tadm-table thead,
  html[data-theme="dark"] .tadm-table thead tr,
  html[data-theme="dark"] .tadm-table thead th{
    background: #000000 !important;
    color: #ffffff !important;
  }

  html[data-theme="dark"] .tadm-table thead th{
    border-bottom: 1px solid rgba(255,255,255,.14) !important;
  }

  html[data-theme="dark"] .tadm-table tbody td{
    border-top-color: rgba(255,255,255,.07) !important;
  }

  html[data-theme="dark"] .tadm-table tbody tr:hover td{
    background: rgba(255,255,255,.03) !important;
  }

  html[data-theme="dark"] .tadm-row--new td{
    background: linear-gradient(90deg, rgba(245,158,11,.08), rgba(245,158,11,0) 55%) !important;
  }

  html[data-theme="dark"] .tadm-status--aberto{
    background: rgba(245,159,0,.14);
    color: #fbbf24;
    border-color: rgba(245,159,0,.24);
  }

  html[data-theme="dark"] .tadm-status--andamento,
  html[data-theme="dark"] .tadm-status--em_andamento{
    background: rgba(13,110,253,.14);
    color: #93c5fd;
    border-color: rgba(13,110,253,.24);
  }

  html[data-theme="dark"] .tadm-status--aguardando_usuario{
    background: rgba(148,163,184,.10);
    color: #cbd5e1;
    border-color: rgba(148,163,184,.18);
  }

  html[data-theme="dark"] .tadm-status--resolvido,
  html[data-theme="dark"] .tadm-status--fechado{
    background: rgba(25,135,84,.14);
    color: #6ee7b7;
    border-color: rgba(25,135,84,.22);
  }

  html[data-theme="dark"] .tadm-status--default{
    background: rgba(148,163,184,.10);
    color: #cbd5e1;
    border-color: rgba(148,163,184,.18);
  }

  html[data-theme="dark"] .tadm-badge-new{
    background: rgba(220,53,69,.14);
    color: #fda4af;
    border-color: rgba(220,53,69,.22);
  }

  html[data-theme="dark"] .tadm-badge-mine{
    background: rgba(13,202,240,.14);
    color: #67e8f9;
    border-color: rgba(13,202,240,.22);
  }

  html[data-theme="dark"] .tadm-card__footer{
    background: rgba(15,20,28,.72);
    border-top-color: rgba(255,255,255,.08);
  }

  html[data-theme="dark"] .pagination .page-link{
    background: rgba(15,23,42,.65) !important;
    border-color: rgba(148,163,184,.25) !important;
    color: rgba(226,232,240,.85) !important;
  }

  html[data-theme="dark"] .pagination .page-item.active .page-link{
    background: rgba(59,130,246,.85) !important;
    border-color: rgba(59,130,246,.85) !important;
    color: #0b1220 !important;
    font-weight: 900 !important;
  }

  /* MOBILE */
  @media (max-width: 767.98px){
    .tadm-wrap{
      padding: 14px 10px 24px;
    }

    .tadm-hero__content,
    .tadm-card__head,
    .tadm-card__body,
    .tadm-card__footer{
      padding-left: 16px;
      padding-right: 16px;
    }

    .tadm-btn,
    .tadm-btn-outline{
      width: 100%;
      justify-content: center;
    }

    .tadm-card__footer{
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
  }
</style>

<div class="tadm-wrap">

  {{-- HERO --}}
  <section class="tadm-hero">
    <div class="tadm-hero__bg"></div>

    <div class="tadm-hero__content">
      <div>
        <div class="tadm-kicker">GRR • PRF — Painel Administrativo</div>
        <h1 class="tadm-title">Tickets — Administrativo</h1>
        <div class="tadm-sub">
          Gerencie os tickets, acompanhe novas respostas e organize os atendimentos da equipe administrativa.
        </div>
      </div>

      <div class="tadm-hero__right">
        <span class="tadm-chip">Nível 7+</span>
      </div>
    </div>
  </section>

  @if(session('success'))
    <div class="alert alert-success tadm-alert mt-3 mb-0">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger tadm-alert mt-3 mb-0">
      {{ session('error') }}
    </div>
  @endif

  {{-- FILTROS --}}
  <section class="tadm-card mt-3">
    <div class="tadm-card__head">
      <div>
        <h2 class="tadm-card__title">Filtros de pesquisa</h2>
        <div class="tadm-card__sub">Localize tickets por assunto, status, categoria, prioridade e responsável</div>
      </div>
    </div>

    <div class="tadm-card__body">
      <form method="GET" action="{{ route('admin.tickets.index') }}" class="row g-3 align-items-end">
        <div class="col-12 col-md-4">
          <label class="tadm-label">Busca</label>
          <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            class="form-control tadm-input"
            placeholder="#ID, título ou autor…"
          >
        </div>

        <div class="col-12 col-md-2">
          <label class="tadm-label">Status</label>
          <select class="form-select tadm-input" name="status">
            <option value="">Todos</option>
            @foreach(Ticket::STATUSES as $s)
              <option value="{{ $s }}" @selected(request('status')===$s)>
                {{ Ticket::STATUS_LABELS[$s] ?? $s }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-2">
          <label class="tadm-label">Categoria</label>
          <select class="form-select tadm-input" name="categoria">
            <option value="">Todas</option>
            @foreach(Ticket::CATEGORIES as $c)
              <option value="{{ $c }}" @selected(request('categoria')===$c)>
                {{ Ticket::CATEGORY_LABELS[$c] ?? $c }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-2">
          <label class="tadm-label">Prioridade</label>
          <select class="form-select tadm-input" name="prioridade">
            <option value="">Todas</option>
            @foreach(Ticket::PRIORITIES as $p)
              <option value="{{ $p }}" @selected(request('prioridade')===$p)>
                {{ Ticket::PRIORITY_LABELS[$p] ?? $p }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-2">
          <label class="tadm-label">Responsável</label>
          <select class="form-select tadm-input" name="assigned">
            <option value="">Todos</option>
            <option value="me" @selected(request('assigned')==='me')>Meus tickets</option>
            <option value="none" @selected(request('assigned')==='none')>Sem responsável</option>
          </select>
        </div>

        <div class="col-12 d-flex flex-wrap gap-2">
          <button class="btn btn-primary tadm-btn" type="submit">Filtrar</button>
          <a class="btn btn-outline-secondary tadm-btn-outline" href="{{ route('admin.tickets.index') }}">Limpar</a>
        </div>
      </form>
    </div>
  </section>

  {{-- TABELA --}}
  <section class="tadm-card mt-3">
    <div class="tadm-card__head">
      <div>
        <h2 class="tadm-card__title">Lista de tickets</h2>
        <div class="tadm-card__sub">Acompanhamento geral dos chamados administrativos</div>
      </div>
    </div>

    <div class="tadm-table-wrap">
      <table class="table table-sm table-hover align-middle mb-0 tadm-table">
        <thead>
          <tr>
            <th style="width:80px;">#</th>
            <th style="width:170px;">Situação</th>
            <th style="width:170px;">Categoria</th>
            <th>Assunto</th>
            <th style="width:220px;">Aberto por</th>
            <th style="width:190px;">Responsável</th>
            <th style="width:180px;">Última atividade</th>
            <th style="width:120px;" class="text-end">Opções</th>
          </tr>
        </thead>

        <tbody>
          @forelse($tickets as $t)
            @php
              $hasNewForAdmin = $t->adminHasUnread();
              $isMine = (int)($t->assigned_to ?? 0) === (int)auth()->id();
              $last = $t->lastActivityAt();

              $statusKey = (string)($t->status ?? '');
              $statusClass = match($statusKey) {
                'aberto'             => 'tadm-status tadm-status--aberto',
                'andamento'          => 'tadm-status tadm-status--andamento',
                'em_andamento'       => 'tadm-status tadm-status--em_andamento',
                'aguardando_usuario' => 'tadm-status tadm-status--aguardando_usuario',
                'resolvido'          => 'tadm-status tadm-status--resolvido',
                'fechado'            => 'tadm-status tadm-status--fechado',
                default              => 'tadm-status tadm-status--default',
              };
            @endphp

            <tr class="{{ $hasNewForAdmin ? 'tadm-row--new' : '' }}">
              <td class="tadm-muted">#{{ $t->id }}</td>

              <td>
                <div class="d-flex align-items-center flex-wrap gap-2">
                  <span class="{{ $statusClass }}">
                    {{ strtoupper($t->statusLabel()) }}
                  </span>

                  @if($hasNewForAdmin)
                    <span class="tadm-badge-new">NOVO</span>
                  @endif
                </div>
              </td>

              <td class="tadm-muted">
                {{ $t->categoriaLabel() }}
              </td>

              <td>
                <div class="tadm-maintext">
                  {{ $t->titulo }}
                </div>

                @if($isMine)
                  <div class="mt-1">
                    <span class="tadm-badge-mine">MEU</span>
                  </div>
                @endif
              </td>

              <td>
                <div class="tadm-maintext">
                  {{ $t->user->name ?? '—' }}
                </div>
                <div class="tadm-subtext">ID: {{ $t->user_id }}</div>
              </td>

              <td class="tadm-muted">
                {{ $t->responsavel->name ?? '—' }}
              </td>

              <td class="tadm-muted">
                {{ $last ? $last->format('d/m/Y H:i') : '—' }}
              </td>

              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary tadm-action" href="{{ route('admin.tickets.show', $t) }}">
                  Ver
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="tadm-empty">
                Sem tickets encontrados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="tadm-card__footer d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div class="tadm-pagination-meta">
        @if($showCount)
          Mostrando <b>{{ $first }}</b> a <b>{{ $last }}</b> de <b>{{ $total }}</b> resultados
        @else
          Resultados: <b>{{ is_null($total) ? '—' : $total }}</b>
        @endif
      </div>

      <div>
        {{ $tickets->appends(request()->query())->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </section>

</div>
@endsection