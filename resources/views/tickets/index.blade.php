@extends('layouts.app')

@section('content')
@php
  // ============================================
  // BASE / COMPATIBILIDADE
  // ============================================
  $isPaginator = $tickets instanceof \Illuminate\Contracts\Pagination\Paginator
              || $tickets instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;

  $countPage = method_exists($tickets, 'count') ? $tickets->count() : (is_countable($tickets) ? count($tickets) : 0);
  $totalAll  = method_exists($tickets, 'total') ? $tickets->total() : $countPage;

  $itemsForStats = collect($isPaginator ? $tickets->items() : $tickets);

  $statusCounts = $itemsForStats
    ->groupBy(fn($t) => (string)($t->status ?? '—'))
    ->map(fn($g) => $g->count());

  $nNovo = 0;
  foreach ($itemsForStats as $t) {
    try {
      if ($t->userHasUnread()) $nNovo++;
    } catch (\Throwable $e) {}
  }

  $stOpen  = (int)($statusCounts['aberto'] ?? 0);
  $stWip   = (int)($statusCounts['andamento'] ?? 0);
  $stClose = (int)($statusCounts['fechado'] ?? 0);

  $pill = function(string $label, string $value, string $tone = 'default', ?string $icon = null) {
    $toneClass = match($tone) {
      'info' => 'pill--info',
      'warn' => 'pill--warn',
      'ok'   => 'pill--ok',
      'bad'  => 'pill--bad',
      default => 'pill--default',
    };

    return '
      <span class="pill '.$toneClass.'">
        '.($icon ? '<span class="pill__icon">'.$icon.'</span>' : '').'
        <span class="pill__txt">
          <span class="pill__k">'.e($label).'</span>
          <span class="pill__v">'.e($value).'</span>
        </span>
      </span>
    ';
  };
@endphp

<div class="tk-wrap">

  {{-- HERO --}}
  <section class="tk-hero">
    <div class="tk-hero__bg"></div>
    <div class="tk-hero__grid">
      <div class="tk-hero__main">
        <div class="tk-kicker">GRR • PRF — CENTRAL DE SUPORTE</div>
        <h1 class="tk-title">Meus Tickets</h1>
        <p class="tk-sub">
          Acompanhe seus chamados, visualize novas respostas e mantenha a comunicação com o suporte de forma organizada.
        </p>

        <div class="tk-pills">
          {!! $pill('Nesta página', (string)$countPage, 'default', '📄') !!}
          {!! $pill('Total', (string)$totalAll, 'info', '📚') !!}
          {!! $pill('Novas respostas', (string)$nNovo, $nNovo ? 'warn' : 'default', '🔔') !!}
          {!! $pill('Abertos', (string)$stOpen, $stOpen ? 'info' : 'default', '📬') !!}
          {!! $pill('Em andamento', (string)$stWip, $stWip ? 'info' : 'default', '🛠️') !!}
          {!! $pill('Fechados', (string)$stClose, $stClose ? 'ok' : 'default', '✅') !!}
        </div>
      </div>

      <div class="tk-hero__side">
        <div class="tk-sidecard">
          <div class="tk-sidecard__label">Ação rápida</div>
          <div class="tk-sidecard__title">Abrir novo atendimento</div>
          <div class="tk-sidecard__text">
            Envie sua solicitação com detalhes para agilizar a resposta do setor responsável.
          </div>

          <a class="btn btn-primary tk-btn" href="{{ route('tickets.create') }}">
            <span>＋</span> Novo Ticket
          </a>
        </div>
      </div>
    </div>
  </section>

  @if(session('success'))
    <div class="alert alert-success tk-alert-success mt-3 mb-0">
      {{ session('success') }}
    </div>
  @endif

  {{-- LISTA --}}
  <section class="tk-card mt-3">
    <div class="tk-card__head">
      <div>
        <div class="tk-card__eyebrow">Painel de tickets</div>
        <h2 class="tk-card__title">Chamados cadastrados</h2>
      </div>

      <div class="tk-card__meta">
        <span class="tk-mini-chip">{{ $countPage }} exibido(s)</span>
        <span class="tk-mini-chip">{{ $totalAll }} total</span>
      </div>
    </div>

    @if($countPage > 0)
      <div class="table-responsive">
        <table class="table align-middle mb-0 tk-table">
          <thead>
            <tr>
              <th style="width:90px;">#</th>
              <th style="width:170px;">Status</th>
              <th style="width:200px;">Categoria</th>
              <th>Título</th>
              <th style="width:190px;">Última atividade</th>
              <th style="width:200px;">Responsável</th>
              <th style="width:130px;" class="text-end">Ações</th>
            </tr>
          </thead>

          <tbody>
            @foreach($tickets as $t)
              @php
                $hasNewReply = false;
                try { $hasNewReply = (bool) $t->userHasUnread(); } catch(\Throwable $e) {}

                $last = null;
                try { $last = $t->lastActivityAt(); } catch(\Throwable $e) {}

                $badge = 'secondary';
                $label = '—';
                try { $badge = $t->statusBadge(); } catch(\Throwable $e) {}
                try { $label = strtoupper($t->statusLabel()); } catch(\Throwable $e) {}

                $cat = '—';
                try { $cat = $t->categoriaLabel(); } catch(\Throwable $e) {}

                $resp = $t->responsavel->name ?? 'Não definido';

                $rowClass = $hasNewReply ? 'tk-row--new' : '';

                $statusClass = match((string)($t->status ?? '')) {
                  'aberto'     => 'tk-status tk-status--open',
                  'andamento'  => 'tk-status tk-status--progress',
                  'fechado'    => 'tk-status tk-status--closed',
                  default      => 'tk-status tk-status--default',
                };
              @endphp

              <tr class="{{ $rowClass }}">
                <td>
                  <div class="tk-id">#{{ $t->id }}</div>
                </td>

                <td>
                  <div class="d-flex align-items-center flex-wrap gap-2">
                    <span class="{{ $statusClass }}">{{ $label }}</span>

                    @if($hasNewReply)
                      <span class="tk-badge-new">NOVO</span>
                    @endif
                  </div>
                </td>

                <td>
                  <span class="tk-category">{{ $cat }}</span>
                </td>

                <td>
                  <div class="tk-titlecell">
                    <a href="{{ route('tickets.show', $t) }}" class="tk-link">
                      {{ $t->titulo }}
                    </a>
                    <div class="tk-titlecell__sub">
                      Ticket #{{ $t->id }} • Atendimento GRR
                    </div>
                  </div>
                </td>

                <td>
                  <div class="tk-muted">
                    {{ $last ? $last->format('d/m/Y') : '—' }}
                  </div>
                  <div class="tk-time">
                    {{ $last ? $last->format('H:i') : 'Sem registro' }}
                  </div>
                </td>

                <td>
                  <div class="tk-resp">{{ $resp }}</div>
                  <div class="tk-muted">Setor responsável</div>
                </td>

                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary tk-action" href="{{ route('tickets.show', $t) }}">
                    Ver detalhes
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="tk-empty">
        <div class="tk-empty__icon">🎫</div>
        <h3 class="tk-empty__title">Nenhum ticket encontrado</h3>
        <p class="tk-empty__text">
          Você ainda não abriu nenhum ticket. Quando precisar de suporte, atendimento ou retorno de algum setor, crie seu primeiro chamado.
        </p>

        <a class="btn btn-primary tk-btn" href="{{ route('tickets.create') }}">
          Abrir primeiro ticket
        </a>
      </div>
    @endif

    @if($isPaginator && method_exists($tickets, 'links'))
      <div class="tk-footer">
        {{ $tickets->links() }}
      </div>
    @endif
  </section>

</div>

<style>
  /* =========================================================
     GRR 3.0 • TICKETS INDEX
  ========================================================= */
  .tk-wrap{
    max-width: 1280px;
    margin: 0 auto;
    padding: 18px 14px 26px;
  }

  /* HERO */
  .tk-hero{
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    border: 1px solid rgba(2,6,23,.10);
    background:
      linear-gradient(135deg, rgba(8,14,28,.98), rgba(15,23,42,.94));
    box-shadow:
      0 24px 70px rgba(2,6,23,.18),
      inset 0 1px 0 rgba(255,255,255,.05);
  }

  .tk-hero__bg{
    position: absolute;
    inset: -60px;
    pointer-events: none;
    opacity: .95;
    background:
      radial-gradient(900px 360px at 10% 15%, rgba(59,130,246,.24), transparent 60%),
      radial-gradient(700px 300px at 92% 20%, rgba(16,185,129,.18), transparent 56%),
      radial-gradient(780px 340px at 50% 110%, rgba(168,85,247,.16), transparent 62%);
    filter: blur(14px);
  }

  .tk-hero__grid{
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1.5fr 340px;
    gap: 18px;
    padding: 22px;
  }

  .tk-kicker{
    color: rgba(226,232,240,.68);
    font-size: 11px;
    line-height: 1;
    font-weight: 900;
    letter-spacing: .18em;
    text-transform: uppercase;
    margin-bottom: 10px;
  }

  .tk-title{
    margin: 0;
    color: #f8fafc;
    font-size: clamp(1.8rem, 2.5vw, 2.5rem);
    line-height: 1.04;
    font-weight: 950;
    letter-spacing: -.02em;
  }

  .tk-sub{
    margin: 10px 0 0;
    max-width: 760px;
    color: rgba(226,232,240,.78);
    font-size: .98rem;
    line-height: 1.6;
    font-weight: 500;
  }

  .tk-pills{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
  }

  .pill{
    display: inline-flex;
    align-items: center;
    gap: 10px;
    min-height: 48px;
    padding: 8px 12px;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,.10);
    background: rgba(255,255,255,.07);
    color: #e2e8f0;
    backdrop-filter: blur(10px);
  }

  .pill__icon{
    width: 28px;
    height: 28px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,.08);
    font-size: 13px;
    flex: 0 0 28px;
  }

  .pill__txt{
    display: flex;
    flex-direction: column;
    line-height: 1.1;
  }

  .pill__k{
    color: rgba(226,232,240,.66);
    font-size: 10px;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .pill__v{
    color: #f8fafc;
    font-size: 15px;
    font-weight: 950;
    margin-top: 3px;
  }

  .pill--default{}
  .pill--info{
    background: rgba(59,130,246,.12);
    border-color: rgba(59,130,246,.24);
  }
  .pill--warn{
    background: rgba(245,158,11,.14);
    border-color: rgba(245,158,11,.28);
  }
  .pill--ok{
    background: rgba(16,185,129,.14);
    border-color: rgba(16,185,129,.28);
  }
  .pill--bad{
    background: rgba(239,68,68,.14);
    border-color: rgba(239,68,68,.28);
  }

  .tk-hero__side{
    display: flex;
    align-items: stretch;
  }

  .tk-sidecard{
    width: 100%;
    padding: 18px;
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,.12);
    background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.04));
    box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
    color: #f8fafc;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .tk-sidecard__label{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: rgba(226,232,240,.66);
    margin-bottom: 8px;
  }

  .tk-sidecard__title{
    font-size: 1.05rem;
    font-weight: 900;
    margin-bottom: 8px;
  }

  .tk-sidecard__text{
    font-size: .92rem;
    line-height: 1.55;
    color: rgba(226,232,240,.74);
    margin-bottom: 16px;
  }

  .tk-btn{
    border-radius: 14px;
    font-weight: 900;
    padding: 11px 16px;
    box-shadow: 0 10px 24px rgba(13,110,253,.22);
  }

  .tk-alert-success{
    border-radius: 16px;
    border: 1px solid rgba(25,135,84,.20);
    box-shadow: 0 12px 28px rgba(25,135,84,.08);
  }

  /* CARD */
  .tk-card{
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(15,23,42,.08);
    background: #ffffff;
    box-shadow: 0 18px 50px rgba(15,23,42,.07);
  }

  .tk-card__head{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 20px 22px 16px;
    border-bottom: 1px solid rgba(15,23,42,.08);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.06), transparent 30%),
      linear-gradient(180deg, rgba(248,250,252,1), rgba(255,255,255,1));
  }

  .tk-card__eyebrow{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: rgba(15,23,42,.48);
    margin-bottom: 6px;
  }

  .tk-card__title{
    margin: 0;
    color: #0f172a;
    font-size: 1.15rem;
    font-weight: 900;
  }

  .tk-card__meta{
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .tk-mini-chip{
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 0 12px;
    border-radius: 999px;
    background: rgba(15,23,42,.05);
    border: 1px solid rgba(15,23,42,.08);
    color: rgba(15,23,42,.78);
    font-size: 12px;
    font-weight: 800;
  }

  /* TABELA */
  .tk-table{
    color: #0f172a;
    margin: 0;
  }

  .tk-table thead th{
    padding: 14px 16px;
    border-bottom: 1px solid rgba(15,23,42,.08) !important;
    background: #f8fafc;
    color: rgba(15,23,42,.56);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    white-space: nowrap;
  }

  .tk-table tbody td{
    padding: 16px;
    border-top: 1px solid rgba(15,23,42,.07) !important;
    vertical-align: middle;
    background: transparent !important;
  }

  .tk-table tbody tr{
    transition: .18s ease;
  }

  .tk-table tbody tr:hover td{
    background: rgba(15,23,42,.02) !important;
  }

  .tk-row--new td{
    background:
      linear-gradient(90deg, rgba(245,158,11,.10), rgba(245,158,11,0) 55%) !important;
  }

  .tk-id{
    font-weight: 900;
    color: rgba(15,23,42,.72);
  }

  .tk-status{
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

  .tk-status--open{
    background: rgba(59,130,246,.10);
    color: #1d4ed8;
    border-color: rgba(59,130,246,.18);
  }

  .tk-status--progress{
    background: rgba(245,158,11,.12);
    color: #b45309;
    border-color: rgba(245,158,11,.20);
  }

  .tk-status--closed{
    background: rgba(16,185,129,.12);
    color: #047857;
    border-color: rgba(16,185,129,.20);
  }

  .tk-status--default{
    background: rgba(100,116,139,.10);
    color: #475569;
    border-color: rgba(100,116,139,.18);
  }

  .tk-badge-new{
    display: inline-flex;
    align-items: center;
    min-height: 28px;
    padding: 0 10px;
    border-radius: 999px;
    background: rgba(245,158,11,.16);
    color: #92400e;
    border: 1px solid rgba(245,158,11,.28);
    font-size: 10px;
    font-weight: 950;
    letter-spacing: .10em;
  }

  .tk-category{
    display: inline-flex;
    align-items: center;
    min-height: 32px;
    padding: 0 12px;
    border-radius: 12px;
    background: rgba(15,23,42,.04);
    border: 1px solid rgba(15,23,42,.07);
    color: rgba(15,23,42,.72);
    font-size: 12px;
    font-weight: 800;
  }

  .tk-titlecell{
    min-width: 240px;
  }

  .tk-link{
    color: #0f172a;
    text-decoration: none;
    font-weight: 900;
    line-height: 1.4;
  }

  .tk-link:hover{
    color: #0b5ed7;
    text-decoration: underline;
  }

  .tk-titlecell__sub{
    margin-top: 4px;
    color: rgba(15,23,42,.48);
    font-size: 12px;
    font-weight: 600;
  }

  .tk-muted{
    color: rgba(15,23,42,.58);
    font-size: .93rem;
    font-weight: 700;
  }

  .tk-time{
    color: rgba(15,23,42,.42);
    font-size: 12px;
    font-weight: 700;
    margin-top: 2px;
  }

  .tk-resp{
    color: #0f172a;
    font-weight: 800;
  }

  .tk-action{
    border-radius: 12px;
    font-weight: 900;
    padding-inline: 14px;
  }

  .tk-footer{
    padding: 14px 18px;
    border-top: 1px solid rgba(15,23,42,.08);
    background: #fafbfc;
  }

  /* EMPTY */
  .tk-empty{
    padding: 44px 20px 48px;
    text-align: center;
  }

  .tk-empty__icon{
    width: 74px;
    height: 74px;
    margin: 0 auto 16px;
    border-radius: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, rgba(59,130,246,.12), rgba(59,130,246,.06));
    border: 1px solid rgba(59,130,246,.14);
    font-size: 30px;
  }

  .tk-empty__title{
    margin: 0 0 8px;
    color: #0f172a;
    font-size: 1.25rem;
    font-weight: 900;
  }

  .tk-empty__text{
    max-width: 620px;
    margin: 0 auto 20px;
    color: rgba(15,23,42,.62);
    line-height: 1.65;
    font-weight: 500;
  }

  /* DARK MODE */
  html[data-theme="dark"] .tk-hero{
    border-color: rgba(255,255,255,.09);
    box-shadow:
      0 26px 70px rgba(0,0,0,.45),
      inset 0 1px 0 rgba(255,255,255,.04);
  }

  html[data-theme="dark"] .tk-card{
    background: rgba(10,14,20,.78);
    border-color: rgba(255,255,255,.08);
    box-shadow: 0 18px 50px rgba(0,0,0,.35);
  }

  html[data-theme="dark"] .tk-card__head{
    border-bottom-color: rgba(255,255,255,.08);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.08), transparent 30%),
      linear-gradient(180deg, rgba(15,20,28,.96), rgba(10,14,20,.92));
  }

  html[data-theme="dark"] .tk-card__eyebrow{
    color: rgba(226,232,240,.46);
  }

  html[data-theme="dark"] .tk-card__title{
    color: rgba(248,250,252,.96);
  }

  html[data-theme="dark"] .tk-mini-chip{
    background: rgba(255,255,255,.05);
    border-color: rgba(255,255,255,.08);
    color: rgba(226,232,240,.78);
  }

  html[data-theme="dark"] .tk-table{
    color: rgba(248,250,252,.94);
  }

  html[data-theme="dark"] .tk-table thead th{
    background: rgba(15,20,28,.82);
    color: rgba(226,232,240,.56);
    border-bottom-color: rgba(255,255,255,.08) !important;
  }

  html[data-theme="dark"] .tk-table tbody td{
    border-top-color: rgba(255,255,255,.07) !important;
  }

  html[data-theme="dark"] .tk-table tbody tr:hover td{
    background: rgba(255,255,255,.03) !important;
  }

  html[data-theme="dark"] .tk-row--new td{
    background:
      linear-gradient(90deg, rgba(245,158,11,.08), rgba(245,158,11,0) 55%) !important;
  }

  html[data-theme="dark"] .tk-id,
  html[data-theme="dark"] .tk-resp,
  html[data-theme="dark"] .tk-link,
  html[data-theme="dark"] .tk-empty__title{
    color: rgba(248,250,252,.94);
  }

  html[data-theme="dark"] .tk-link:hover{
    color: #93c5fd;
  }

  html[data-theme="dark"] .tk-titlecell__sub,
  html[data-theme="dark"] .tk-muted,
  html[data-theme="dark"] .tk-time,
  html[data-theme="dark"] .tk-empty__text{
    color: rgba(226,232,240,.58);
  }

  html[data-theme="dark"] .tk-category{
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.07);
    color: rgba(226,232,240,.78);
  }

  html[data-theme="dark"] .tk-status--open{
    background: rgba(59,130,246,.14);
    color: #93c5fd;
    border-color: rgba(59,130,246,.24);
  }

  html[data-theme="dark"] .tk-status--progress{
    background: rgba(245,158,11,.14);
    color: #fbbf24;
    border-color: rgba(245,158,11,.22);
  }

  html[data-theme="dark"] .tk-status--closed{
    background: rgba(16,185,129,.14);
    color: #6ee7b7;
    border-color: rgba(16,185,129,.22);
  }

  html[data-theme="dark"] .tk-status--default{
    background: rgba(148,163,184,.10);
    color: #cbd5e1;
    border-color: rgba(148,163,184,.18);
  }

  html[data-theme="dark"] .tk-badge-new{
    background: rgba(245,158,11,.16);
    color: #fde68a;
    border-color: rgba(245,158,11,.25);
  }

  html[data-theme="dark"] .tk-footer{
    background: rgba(15,20,28,.72);
    border-top-color: rgba(255,255,255,.08);
  }

  html[data-theme="dark"] .tk-empty__icon{
    background: linear-gradient(180deg, rgba(59,130,246,.12), rgba(59,130,246,.04));
    border-color: rgba(59,130,246,.18);
  }

  /* MOBILE */
  @media (max-width: 991.98px){
    .tk-hero__grid{
      grid-template-columns: 1fr;
    }

    .tk-hero__side{
      align-items: flex-start;
    }
  }

  @media (max-width: 767.98px){
    .tk-wrap{
      padding: 14px 10px 22px;
    }

    .tk-hero__grid{
      padding: 16px;
      gap: 14px;
    }

    .tk-card__head{
      padding: 16px;
      flex-direction: column;
      align-items: flex-start;
    }

    .tk-table thead{
      display: none;
    }

    .tk-table,
    .tk-table tbody,
    .tk-table tr,
    .tk-table td{
      display: block;
      width: 100%;
    }

    .tk-table tbody tr{
      padding: 14px;
      border-top: 1px solid rgba(15,23,42,.08);
    }

    .tk-table tbody tr:first-child{
      border-top: 0;
    }

    .tk-table tbody td{
      border: 0 !important;
      padding: 7px 0;
      text-align: left !important;
    }

    .tk-action{
      width: 100%;
      margin-top: 6px;
    }

    html[data-theme="dark"] .tk-table tbody tr{
      border-top-color: rgba(255,255,255,.08);
    }
  }
</style>
@endsection