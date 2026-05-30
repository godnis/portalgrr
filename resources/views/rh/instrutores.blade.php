@extends('layouts.app')

@section('content')
@php
  $yes = fn($b) => $b
    ? '<span class="iPill iPill--yes">✓</span>'
    : '<span class="iPill iPill--no">—</span>';

  $statusClass = function($st){
    return match($st){
      'em_exercicio' => 'iSt iSt--ok',
      'sob_reserva'  => 'iSt iSt--reserve',
      'em_licenca'   => 'iSt iSt--warn',
      'ausente'      => 'iSt iSt--soft',
      'estagio'      => 'iSt iSt--soft',
      'desligado'    => 'iSt iSt--bad',
      default        => 'iSt iSt--soft',
    };
  };

  $rows = $rows ?? collect();
  $total = (int) ($total ?? (method_exists($rows,'count') ? $rows->count() : count($rows)));
  $ativos = (int) ($ativos ?? 0);
  $statusCounts = $statusCounts ?? [];
  $form = $form ?? ['pop'=>0,'clt'=>0,'bopm'=>0,'ctb'=>0,'cta'=>0,'cap'=>0,'satb'=>0,'media'=>0];

  $q = $q ?? '';
  $status = $status ?? '';
  $equipe = $equipe ?? '';

  $statusOptions = $statusOptions ?? [
    ''            => 'Todos',
    'em_exercicio'=> 'Em Exercício',
    'sob_reserva' => 'Sob Reserva',
    'em_licenca'  => 'Em Licença',
    'ausente'     => 'Ausente',
    'estagio'     => 'Estágio',
    'desligado'   => 'Desligado',
  ];

  $count = fn($key) => (int) ($statusCounts[$key] ?? 0);

  $authNivel = (int) (auth()->user()->nivel ?? 0);
  $canFilterEquipe = $authNivel >= 7;

  $equipes = $equipes ?? collect($rows)
    ->pluck('equipe')
    ->filter(fn($v) => filled($v))
    ->map(fn($v) => trim((string) $v))
    ->unique()
    ->sort()
    ->values()
    ->all();

  $bars = [
    ['POP',  (int)($form['pop'] ?? 0),  'blue'],
    ['CLT',  (int)($form['clt'] ?? 0),  'green'],
    ['BOPM', (int)($form['bopm'] ?? 0), 'gold'],
    ['CTB',  (int)($form['ctb'] ?? 0),  'cyan'],
    ['CTA',  (int)($form['cta'] ?? 0),  'violet'],
    ['CAP',  (int)($form['cap'] ?? 0),  'red'],
    ['SAT-B',(int)($form['satb'] ?? 0), 'slate'],
  ];

  $formAvg = max(0, min(100, (int)($form['media'] ?? 0)));
@endphp

<div class="rhI-wrap">
  <div class="rhI-hero">
    <div class="rhI-hero__glow rhI-hero__glow--blue"></div>
    <div class="rhI-hero__glow rhI-hero__glow--green"></div>
    <div class="rhI-hero__grid"></div>

    <div class="rhI-hero__inner">
      <div class="rhI-hero__left">
        <div class="rhI-kicker">RH • QUADRO DE INSTRUTORES • GRR 3.0</div>
        <h1 class="rhI-title">Instrutores</h1>
        <p class="rhI-sub">
          Painel operacional dos instrutores vinculados à Hierarquia, com leitura rápida de status, formações, equipe e dados institucionais em padrão visual avançado.
        </p>

        <div class="rhI-chips">
          <span class="rhI-chip">
            <span class="dot dot--blue"></span>
            Total geral
            <b>{{ $total }}</b>
          </span>

          <span class="rhI-chip">
            <span class="dot dot--green"></span>
            Base ativa
            <b>{{ $ativos }}</b>
          </span>

          <span class="rhI-chip">
            <span class="dot dot--gray"></span>
            Média de formações
            <b>{{ $formAvg }}%</b>
          </span>
        </div>
      </div>

      <div class="rhI-hero__right">
        <a href="{{ route('rh.index') }}" class="btn rhI-btn rhI-btn--ghost">Voltar</a>
        <a href="{{ route('rh.hierarquia') }}" class="btn rhI-btn rhI-btn--primary">Ver Hierarquia</a>
      </div>
    </div>
  </div>

  <div class="rhI-container">
    <div class="rhI-filterCard">
      <div class="rhI-filterCard__head">
        <div>
          <div class="rhI-card__eyebrow">Pesquisa operacional</div>
          <div class="rhI-card__title">Filtros</div>
          <div class="rhI-card__sub">Pesquise e refine o quadro de instrutores</div>
        </div>

        <div class="rhI-filterCard__meta">
          Exibindo <b>{{ method_exists($rows,'count') ? $rows->count() : count($rows) }}</b> registro(s)
        </div>
      </div>

      <div class="rhI-filterCard__body">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('rh.instrutores') }}">
          <div class="{{ $canFilterEquipe ? 'col-xl-5 col-lg-5' : 'col-xl-7 col-lg-7' }}">
            <label class="form-label rhI-label">Busca</label>
            <div class="rhI-inputWrap">
              <span class="rhI-inputIcon">⌕</span>
              <input
                class="form-control rhI-input rhI-input--withIcon"
                name="q"
                value="{{ $q }}"
                placeholder="Nome, CPF, serial, Discord..."
              >
            </div>
          </div>

          <div class="col-xl-3 col-lg-3 col-md-4">
            <label class="form-label rhI-label">Status</label>
            <select class="form-select rhI-input" name="status">
              @foreach($statusOptions as $k => $label)
                <option value="{{ $k }}" @selected((string)$status === (string)$k)>{{ $label }}</option>
              @endforeach
            </select>
          </div>

          @if($canFilterEquipe)
            <div class="col-xl-2 col-lg-2 col-md-4">
              <label class="form-label rhI-label">Equipe</label>
              <select class="form-select rhI-input" name="equipe">
                <option value="">Todas</option>
                @foreach($equipes as $eq)
                  <option value="{{ $eq }}" @selected((string)$equipe === (string)$eq)>{{ $eq }}</option>
                @endforeach
              </select>
            </div>
          @endif

          <div class="col-xl-2 col-lg-2 col-md-4">
            <div class="rhI-actions">
              <button class="btn rhI-btn rhI-btn--primary w-100" type="submit">Filtrar</button>
              <a class="btn rhI-btn rhI-btn--ghost w-100" href="{{ route('rh.instrutores') }}">Limpar</a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-xl-4">
        <div class="rhI-card h-100">
          <div class="rhI-card__head">
            <div>
              <div class="rhI-card__eyebrow">Leitura rápida</div>
              <div class="rhI-card__title">Situação do Quadro</div>
              <div class="rhI-card__sub">Distribuição atual dos instrutores</div>
            </div>
            <span class="rhI-badge">{{ $total }} total</span>
          </div>

          <div class="rhI-card__body">
            <div class="rhI-statGrid">
              <div class="rhI-stat rhI-stat--ok">
                <div class="rhI-stat__k">Em exercício</div>
                <div class="rhI-stat__v">{{ $count('em_exercicio') }}</div>
              </div>

              <div class="rhI-stat rhI-stat--reserve">
                <div class="rhI-stat__k">Sob reserva</div>
                <div class="rhI-stat__v">{{ $count('sob_reserva') }}</div>
              </div>

              <div class="rhI-stat rhI-stat--warn">
                <div class="rhI-stat__k">Em licença</div>
                <div class="rhI-stat__v">{{ $count('em_licenca') }}</div>
              </div>

              <div class="rhI-stat rhI-stat--soft">
                <div class="rhI-stat__k">Ausente</div>
                <div class="rhI-stat__v">{{ $count('ausente') }}</div>
              </div>

              <div class="rhI-stat rhI-stat--soft">
                <div class="rhI-stat__k">Estágio</div>
                <div class="rhI-stat__v">{{ $count('estagio') }}</div>
              </div>

              <div class="rhI-stat rhI-stat--bad">
                <div class="rhI-stat__k">Desligado</div>
                <div class="rhI-stat__v">{{ $count('desligado') }}</div>
              </div>
            </div>

            <div class="rhI-sep"></div>

            <div class="rhI-row">
              <span class="rhI-muted">Base considerada nas formações</span>
              <b>{{ $ativos }}</b>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-8">
        <div class="rhI-card h-100">
          <div class="rhI-card__head">
            <div>
              <div class="rhI-card__eyebrow">Indicadores</div>
              <div class="rhI-card__title">Formações Operacionais</div>
              <div class="rhI-card__sub">Percentual sobre a base ativa (Em exercício + Em licença + Sob reserva)</div>
            </div>
            <span class="rhI-badge rhI-badge--soft">Média {{ $formAvg }}%</span>
          </div>

          <div class="rhI-card__body">
            <div class="rhI-barsGrid">
              @foreach($bars as [$k, $v, $tone])
                @php $vv = max(0, min(100, (int)$v)); @endphp
                <div class="rhI-barCard rhI-barCard--{{ $tone }}">
                  <div class="rhI-barCard__top">
                    <span class="rhI-barKey">{{ $k }}</span>
                    <span class="rhI-barVal">{{ $vv }}%</span>
                  </div>

                  <div class="rhI-barTrack">
                    <span style="width: {{ $vv }}%"></span>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="rhI-sep"></div>

            <div class="rhI-row">
              <span class="rhI-muted">Média geral</span>
              <b>{{ $formAvg }}%</b>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="rhI-card">
          <div class="rhI-card__head">
            <div>
              <div class="rhI-card__eyebrow">Base operacional</div>
              <div class="rhI-card__title">Quadro de Instrutores</div>
              <div class="rhI-card__sub">Lista completa com status, formações, equipe e dados institucionais</div>
            </div>

            <div class="rhI-card__headRight">
              <span class="rhI-muted">Role horizontalmente para visualizar todas as colunas.</span>
            </div>
          </div>

          <div class="rhI-scrollTop jsScrollTop">
            <div class="rhI-scrollTop__inner jsScrollTopInner"></div>
          </div>

          <div class="rhI-tableWrap jsTableWrap">
            <table class="table rhI-table align-middle mb-0 jsTable">
              <thead>
                <tr>
                  <th class="sticky-col sticky-col--1" style="min-width:140px;">CPF</th>
                  <th class="sticky-col sticky-col--2" style="min-width:360px;">Nome</th>
                  <th style="min-width:170px;">Status</th>
                  <th class="text-center" style="min-width:82px;">POP</th>
                  <th class="text-center" style="min-width:82px;">CLT</th>
                  <th class="text-center" style="min-width:82px;">BOPM</th>
                  <th class="text-center" style="min-width:82px;">CTB</th>
                  <th class="text-center" style="min-width:82px;">CTA</th>
                  <th class="text-center" style="min-width:82px;">CAP</th>
                  <th class="text-center" style="min-width:82px;">SAT-B</th>
                  <th style="min-width:130px;">Serial</th>
                  <th style="min-width:170px;">Discord</th>
                  <th style="min-width:140px;">Equipe</th>
                  <th style="min-width:300px;">Observação</th>
                </tr>
              </thead>

              <tbody>
                @forelse($rows as $r)
                  @php
                    $cpf = $r->cpf ?? '—';
                    $nome = $r->nome ?? ($r->nome_sync ?? '—');
                    $stLabel = $r->status_label ?? '—';
                    $st = (string)($r->status ?? '');
                    $serial = $r->serial ?? null;
                    $discord = $r->discord_id ?? null;
                    $obs = $r->funcao_obs ?? null;
                    $eq = $r->equipe ?? null;

                    $ini = 'PRF';
                    $parts = preg_split('/\s+/', trim((string)$nome));
                    $i1 = $parts[0] ?? '';
                    $i2 = $parts[1] ?? '';
                    $tmp = '';
                    if ($i1 !== '') $tmp .= mb_strtoupper(mb_substr($i1, 0, 1));
                    if ($i2 !== '') $tmp .= mb_strtoupper(mb_substr($i2, 0, 1));
                    if ($tmp !== '') $ini = $tmp;
                  @endphp

                  <tr>
                    <td class="sticky-col sticky-col--1">
                      <span class="rhI-cpf">{{ $cpf }}</span>
                    </td>

                    <td class="sticky-col sticky-col--2">
                      <div class="rhI-person">
                        <div class="rhI-avatar">{{ $ini }}</div>

                        <div class="rhI-person__meta">
                          <div class="rhI-person__name">{{ $nome }}</div>

                          <div class="rhI-person__sub">
                            @if($r->cargo ?? null)
                              <span><b>Cargo:</b> {{ $r->cargo }}</span>
                            @endif

                            @if($eq !== null && (string)$eq !== '')
                              <span class="rhI-dot"></span>
                              <span><b>Equipe:</b> {{ $eq }}</span>
                            @endif
                          </div>
                        </div>
                      </div>
                    </td>

                    <td>
                      <span class="{{ $statusClass($st) }}">{{ $stLabel }}</span>
                    </td>

                    <td class="text-center">{!! $yes((bool)($r->pop ?? false)) !!}</td>
                    <td class="text-center">{!! $yes((bool)($r->clt ?? false)) !!}</td>
                    <td class="text-center">{!! $yes((bool)($r->bopm ?? false)) !!}</td>
                    <td class="text-center">{!! $yes((bool)($r->ctb ?? false)) !!}</td>
                    <td class="text-center">{!! $yes((bool)($r->cta ?? false)) !!}</td>
                    <td class="text-center">{!! $yes((bool)($r->cap ?? false)) !!}</td>
                    <td class="text-center">{!! $yes((bool)($r->satb ?? false)) !!}</td>

                    <td>
                      @if($serial)
                        <button type="button" class="copy" data-copy="{{ $serial }}" title="Copiar serial">
                          {{ $serial }}
                        </button>
                      @else
                        <span class="rhI-empty">—</span>
                      @endif
                    </td>

                    <td>
                      @if($discord)
                        <button type="button" class="copy" data-copy="{{ $discord }}" title="Copiar Discord">
                          {{ $discord }}
                        </button>
                      @else
                        <span class="rhI-empty">—</span>
                      @endif
                    </td>

                    <td>
                      @if(filled($eq))
                        <span class="rhI-team">{{ $eq }}</span>
                      @else
                        <span class="rhI-empty">—</span>
                      @endif
                    </td>

                    <td style="white-space: normal;">
                      @if($obs)
                        <div class="rhI-obs">{{ $obs }}</div>
                      @else
                        <span class="rhI-empty">—</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="14" class="text-center py-5 rhI-empty">Nenhum instrutor encontrado.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="rhI-footerHint">
            Painel de instrutores em padrão visual GRR 3.0, com leitura mais limpa, tabela refinada e navegação horizontal sincronizada.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .rhI-wrap{
    --rh-bg: #020617;
    --rh-bg-2: #0b1220;
    --rh-panel: rgba(2, 6, 23, .78);
    --rh-panel-2: rgba(15, 23, 42, .68);
    --rh-border: rgba(148, 163, 184, .16);
    --rh-border-strong: rgba(148, 163, 184, .24);
    --rh-text: #f8fafc;
    --rh-text-soft: rgba(226, 232, 240, .78);
    --rh-muted: rgba(148, 163, 184, .82);
    --rh-blue: #3b82f6;
    --rh-green: #10b981;
    --rh-gold: #f59e0b;
    --rh-cyan: #06b6d4;
    --rh-violet: #8b5cf6;
    --rh-red: #ef4444;
    --rh-slate: #94a3b8;

    padding: 18px 18px 44px;
    color: rgba(226,232,240,.92);
  }

  .rhI-container{
    max-width: 1400px;
    margin: 0 auto;
  }

  .rhI-muted{
    color: var(--rh-muted);
    font-size: 13px;
  }

  .rhI-empty{
    color: rgba(148,163,184,.78);
  }

  .rhI-hero{
    position: relative;
    overflow: hidden;
    margin: 8px auto 18px;
    max-width: 1400px;
    border-radius: 28px;
    border: 1px solid rgba(148,163,184,.18);
    background:
      linear-gradient(180deg, rgba(2,6,23,.92), rgba(2,6,23,.78));
    box-shadow:
      0 24px 60px rgba(0,0,0,.38),
      inset 0 1px 0 rgba(255,255,255,.04);
    isolation: isolate;
  }

  .rhI-hero__glow{
    position: absolute;
    border-radius: 999px;
    filter: blur(14px);
    opacity: .85;
    pointer-events: none;
  }

  .rhI-hero__glow--blue{
    width: 320px;
    height: 320px;
    left: -70px;
    top: -120px;
    background: radial-gradient(circle, rgba(59,130,246,.26) 0%, transparent 65%);
  }

  .rhI-hero__glow--green{
    width: 340px;
    height: 340px;
    right: -80px;
    top: -110px;
    background: radial-gradient(circle, rgba(16,185,129,.20) 0%, transparent 65%);
  }

  .rhI-hero__grid{
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
      linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
    background-size: 26px 26px;
    mask-image: linear-gradient(180deg, rgba(0,0,0,.85), transparent 85%);
  }

  .rhI-hero__inner{
    position: relative;
    z-index: 2;
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 20px;
    padding: 26px;
    flex-wrap: wrap;
  }

  .rhI-kicker{
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .16em;
    color: rgba(226,232,240,.66);
    text-transform: uppercase;
  }

  .rhI-title{
    font-size: 34px;
    line-height: 1.02;
    font-weight: 900;
    margin: 8px 0 8px;
    letter-spacing: -.04em;
    color: var(--rh-text);
  }

  .rhI-sub{
    color: rgba(226,232,240,.74);
    font-size: 14px;
    max-width: 780px;
    margin: 0;
    line-height: 1.6;
  }

  .rhI-chips{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top: 16px;
  }

  .rhI-chip{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 9px 13px;
    border-radius: 999px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(15,23,42,.58);
    font-size: 13px;
    color: rgba(226,232,240,.90);
    backdrop-filter: blur(8px);
  }

  .rhI-chip b{
    color: #fff;
  }

  .dot{
    width:8px;
    height:8px;
    border-radius:99px;
    display:inline-block;
    box-shadow: 0 0 14px currentColor;
  }

  .dot--blue{ background:#3b82f6; color:#3b82f6; }
  .dot--green{ background:#10b981; color:#10b981; }
  .dot--gray{ background:#94a3b8; color:#94a3b8; }

  .rhI-hero__right{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:center;
  }

  .rhI-btn{
    border-radius: 14px;
    padding: 10px 15px;
    font-weight: 800;
    border: 1px solid rgba(148,163,184,.20);
    transition: .22s ease;
  }

  .rhI-btn:hover{
    transform: translateY(-1px);
  }

  .rhI-btn--ghost{
    color: #e2e8f0;
    background: rgba(15,23,42,.54);
  }

  .rhI-btn--ghost:hover{
    color: #fff;
    border-color: rgba(148,163,184,.30);
    background: rgba(30,41,59,.72);
  }

  .rhI-btn--primary{
    color: #fff;
    border-color: rgba(59,130,246,.35);
    background: linear-gradient(180deg, rgba(37,99,235,.98), rgba(29,78,216,.96));
    box-shadow: 0 12px 28px rgba(37,99,235,.28);
  }

  .rhI-btn--primary:hover{
    color: #fff;
    box-shadow: 0 16px 32px rgba(37,99,235,.34);
  }

  .rhI-card,
  .rhI-filterCard{
    border-radius: 24px;
    border: 1px solid var(--rh-border);
    background: var(--rh-panel);
    box-shadow:
      0 16px 40px rgba(0,0,0,.28),
      inset 0 1px 0 rgba(255,255,255,.03);
    overflow: hidden;
    backdrop-filter: blur(10px);
  }

  .rhI-filterCard{
    margin-bottom: 14px;
  }

  .rhI-card__head,
  .rhI-filterCard__head{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 14px;
    padding: 20px 20px 0;
    flex-wrap: wrap;
  }

  .rhI-card__headRight,
  .rhI-filterCard__meta{
    padding-top: 6px;
    color: rgba(148,163,184,.82);
    font-size: 13px;
  }

  .rhI-card__eyebrow{
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: rgba(148,163,184,.78);
    margin-bottom: 4px;
  }

  .rhI-card__title{
    font-weight: 900;
    letter-spacing: -.02em;
    color:#f8fafc;
    font-size: 18px;
  }

  .rhI-card__sub{
    color: rgba(226,232,240,.70);
    font-size: 13px;
    margin-top: 2px;
  }

  .rhI-card__body,
  .rhI-filterCard__body{
    padding: 20px;
  }

  .rhI-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding: 7px 10px;
    border-radius: 999px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(15,23,42,.55);
    font-weight: 900;
    font-size: 12px;
    color: rgba(226,232,240,.82);
  }

  .rhI-badge--soft{
    background: rgba(148,163,184,.10);
    border-color: rgba(148,163,184,.22);
  }

  .rhI-label{
    font-size: 11px;
    font-weight: 900;
    color: rgba(226,232,240,.72);
    letter-spacing:.10em;
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  .rhI-inputWrap{
    position: relative;
  }

  .rhI-inputIcon{
    position: absolute;
    top: 50%;
    left: 14px;
    transform: translateY(-50%);
    color: rgba(148,163,184,.66);
    font-size: 15px;
    pointer-events: none;
  }

  .rhI-input{
    min-height: 48px;
    border-radius: 14px;
    background: rgba(15,23,42,.84) !important;
    border: 1px solid rgba(148,163,184,.22) !important;
    color: #f8fafc !important;
    box-shadow: none !important;
  }

  .rhI-input--withIcon{
    padding-left: 40px;
  }

  .rhI-input:focus{
    border-color: rgba(59,130,246,.44) !important;
    box-shadow: 0 0 0 4px rgba(59,130,246,.10) !important;
    background: rgba(15,23,42,.96) !important;
  }

  .rhI-input::placeholder{
    color: rgba(226,232,240,.45) !important;
  }

  .rhI-actions{
    display:flex;
    gap: 8px;
    flex-direction: column;
  }

  .rhI-sep{
    height:1px;
    background: rgba(148,163,184,.14);
    margin: 18px 0;
  }

  .rhI-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
  }

  .rhI-statGrid{
    display:grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .rhI-stat{
    position: relative;
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.16);
    background: rgba(15,23,42,.56);
    padding: 14px;
    min-height: 98px;
    overflow: hidden;
  }

  .rhI-stat::after{
    content:"";
    position:absolute;
    inset:auto -30px -30px auto;
    width:72px;
    height:72px;
    border-radius:999px;
    opacity:.16;
  }

  .rhI-stat__k{
    font-size: 11px;
    font-weight: 900;
    color: rgba(226,232,240,.68);
    text-transform: uppercase;
    letter-spacing:.08em;
  }

  .rhI-stat__v{
    font-size: 24px;
    font-weight: 900;
    color:#f8fafc;
    margin-top: 8px;
    line-height: 1;
  }

  .rhI-stat--ok{
    background: rgba(16,185,129,.10);
    border-color: rgba(16,185,129,.20);
  }
  .rhI-stat--ok::after{ background: rgba(16,185,129,.55); }

  .rhI-stat--warn{
    background: rgba(245,158,11,.12);
    border-color: rgba(245,158,11,.22);
  }
  .rhI-stat--warn::after{ background: rgba(245,158,11,.55); }

  .rhI-stat--bad{
    background: rgba(239,68,68,.10);
    border-color: rgba(239,68,68,.20);
  }
  .rhI-stat--bad::after{ background: rgba(239,68,68,.55); }

  .rhI-stat--reserve{
    background: rgba(59,130,246,.10);
    border-color: rgba(59,130,246,.20);
  }
  .rhI-stat--reserve::after{ background: rgba(59,130,246,.55); }

  .rhI-stat--soft{
    background: rgba(148,163,184,.12);
    border-color: rgba(148,163,184,.22);
  }
  .rhI-stat--soft::after{ background: rgba(148,163,184,.40); }

  .rhI-barsGrid{
    display:grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .rhI-barCard{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.14);
    background: rgba(15,23,42,.50);
    padding: 13px;
  }

  .rhI-barCard__top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 10px;
    margin-bottom: 9px;
  }

  .rhI-barKey{
    font-weight: 900;
    color:#f8fafc;
    font-size: 13px;
    letter-spacing: .02em;
  }

  .rhI-barVal{
    font-weight: 900;
    color: rgba(226,232,240,.84);
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(15,23,42,.55);
    padding: 5px 9px;
    border-radius: 999px;
    min-width: 64px;
    text-align:center;
    font-size: 12px;
  }

  .rhI-barTrack{
    height: 10px;
    border-radius: 999px;
    background: rgba(148,163,184,.12);
    overflow:hidden;
    position: relative;
  }

  .rhI-barTrack span{
    display:block;
    height:100%;
    width:0%;
    border-radius: 999px;
    transition: width .45s ease;
  }

  .rhI-barCard--blue .rhI-barTrack span{ background: linear-gradient(90deg, rgba(59,130,246,.88), rgba(96,165,250,.88)); }
  .rhI-barCard--green .rhI-barTrack span{ background: linear-gradient(90deg, rgba(16,185,129,.88), rgba(52,211,153,.88)); }
  .rhI-barCard--gold .rhI-barTrack span{ background: linear-gradient(90deg, rgba(245,158,11,.88), rgba(251,191,36,.88)); }
  .rhI-barCard--cyan .rhI-barTrack span{ background: linear-gradient(90deg, rgba(6,182,212,.88), rgba(34,211,238,.88)); }
  .rhI-barCard--violet .rhI-barTrack span{ background: linear-gradient(90deg, rgba(139,92,246,.88), rgba(167,139,250,.88)); }
  .rhI-barCard--red .rhI-barTrack span{ background: linear-gradient(90deg, rgba(239,68,68,.88), rgba(248,113,113,.88)); }
  .rhI-barCard--slate .rhI-barTrack span{ background: linear-gradient(90deg, rgba(100,116,139,.88), rgba(148,163,184,.88)); }

  .rhI-scrollTop{
    overflow-x: auto;
    overflow-y: hidden;
    height: 16px;
    margin-top: 12px;
    border-top: 1px solid rgba(148,163,184,.12);
    border-bottom: 1px solid rgba(148,163,184,.12);
    background: rgba(2,6,23,.38);
  }

  .rhI-scrollTop__inner{
    height: 1px;
  }

  .rhI-tableWrap{
    overflow-x:auto;
    overflow-y: visible;
  }

  .rhI-table{
    margin: 0;
    color: rgba(241,245,249,.96) !important;
    --bs-table-bg: transparent;
    --bs-table-border-color: rgba(148,163,184,.10);
  }

  .rhI-table thead th{
    position: sticky;
    top: 0;
    background: rgba(15,23,42,.88);
    backdrop-filter: blur(12px);
    z-index: 3;
    font-size: 11px;
    letter-spacing: .10em;
    text-transform: uppercase;
    color: rgba(226,232,240,.74);
    border-bottom: 1px solid rgba(148,163,184,.18);
    padding: 14px 12px;
    white-space: nowrap;
  }

  .rhI-table tbody td{
    padding: 12px;
    border-top: 1px solid rgba(148,163,184,.10);
    white-space: nowrap;
    background: rgba(2,6,23,.32);
    color: rgba(226,232,240,.86);
    vertical-align: middle;
    transition: background .18s ease;
  }

  .rhI-table tbody tr:nth-child(even) td{
    background: rgba(15,23,42,.24);
  }

  .rhI-table tbody tr:hover td{
    background: rgba(15,23,42,.58);
  }

  .sticky-col{
    position: sticky;
    z-index: 2;
    background: rgba(6,12,24,.94) !important;
    box-shadow: inset -1px 0 0 rgba(148,163,184,.08);
  }

  .sticky-col--1{ left: 0; z-index: 4; }
  .sticky-col--2{ left: 140px; z-index: 4; }

  .rhI-person{
    display:flex;
    align-items:flex-start;
    gap: 12px;
    min-width: 340px;
  }

  .rhI-avatar{
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight: 900;
    color: #f8fafc;
    background:
      radial-gradient(24px 24px at 30% 25%, rgba(59,130,246,.25), transparent 60%),
      radial-gradient(24px 24px at 70% 75%, rgba(16,185,129,.18), transparent 60%),
      rgba(30,41,59,.96);
    border: 1px solid rgba(148,163,184,.14);
    flex: 0 0 auto;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
  }

  .rhI-person__meta{
    min-width: 240px;
  }

  .rhI-person__name{
    font-weight: 900;
    color:#f8fafc;
    line-height: 1.12;
    font-size: 15px;
  }

  .rhI-person__sub{
    font-size: 12px;
    color: rgba(226,232,240,.68);
    margin-top: 5px;
    display:flex;
    gap:8px;
    flex-wrap:wrap;
    align-items:center;
    line-height: 1.45;
  }

  .rhI-dot{
    width:4px;
    height:4px;
    border-radius:99px;
    background: rgba(226,232,240,.32);
    display:inline-block;
  }

  .rhI-obs{
    font-size: 12px;
    color: rgba(226,232,240,.74);
    white-space: normal;
    max-width: 540px;
    line-height: 1.58;
  }

  .iSt{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 900;
    font-size: 12px;
    border: 1px solid rgba(148,163,184,.14);
    background: rgba(15,23,42,.72);
    color: rgba(226,232,240,.84);
    white-space: nowrap;
  }

  .iSt--ok{
    background: rgba(16,185,129,.10);
    border-color: rgba(16,185,129,.22);
    color: #34d399;
  }

  .iSt--warn{
    background: rgba(245,158,11,.12);
    border-color: rgba(245,158,11,.25);
    color: #fbbf24;
  }

  .iSt--bad{
    background: rgba(239,68,68,.10);
    border-color: rgba(239,68,68,.22);
    color: #f87171;
  }

  .iSt--soft{
    background: rgba(148,163,184,.14);
    border-color: rgba(148,163,184,.26);
    color: rgba(226,232,240,.76);
  }

  .iSt--reserve{
    background: rgba(59,130,246,.10);
    border-color: rgba(59,130,246,.20);
    color: #93c5fd;
  }

  .iPill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width: 30px;
    padding: 4px 8px;
    border-radius: 10px;
    font-weight: 900;
    font-size: 12px;
    border: 1px solid rgba(148,163,184,.14);
    background: rgba(15,23,42,.76);
    color: rgba(226,232,240,.74);
  }

  .iPill--yes{
    background: rgba(16,185,129,.12);
    border-color: rgba(16,185,129,.22);
    color:#34d399;
  }

  .iPill--no{
    background: rgba(148,163,184,.12);
    border-color: rgba(148,163,184,.22);
    color: rgba(226,232,240,.60);
  }

  .rhI-cpf{
    font-weight: 900;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(15,23,42,.55);
    padding: 6px 10px;
    border-radius: 12px;
    display:inline-block;
    color: rgba(226,232,240,.86);
  }

  .rhI-team{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid rgba(59,130,246,.20);
    background: rgba(59,130,246,.10);
    color: #bfdbfe;
    font-weight: 800;
    font-size: 12px;
    white-space: nowrap;
  }

  .copy{
    cursor: pointer;
    font-weight: 800;
    padding: 6px 10px;
    border-radius: 10px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(15,23,42,.55);
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color: rgba(226,232,240,.86);
    transition: .18s ease;
  }

  .copy:hover{
    background: rgba(59,130,246,.14);
    border-color: rgba(59,130,246,.22);
    color: #fff;
    transform: translateY(-1px);
  }

  .copy.is-copied{
    background: rgba(16,185,129,.14);
    border-color: rgba(16,185,129,.24);
    color: #a7f3d0;
  }

  .rhI-footerHint{
    padding: 12px 16px 16px;
    border-top: 1px solid rgba(148,163,184,.12);
    color: rgba(226,232,240,.65);
    font-size: 12px;
  }

  @media (max-width: 1199px){
    .rhI-barsGrid{
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 992px){
    .rhI-statGrid{
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .rhI-hero__inner{
      padding: 22px;
    }
  }

  @media (max-width: 768px){
    .rhI-wrap{
      padding: 14px 14px 34px;
    }

    .rhI-title{
      font-size: 28px;
    }

    .rhI-statGrid,
    .rhI-barsGrid{
      grid-template-columns: 1fr;
    }

    .rhI-person{
      min-width: 300px;
    }

    .rhI-card__head,
    .rhI-filterCard__head,
    .rhI-card__body,
    .rhI-filterCard__body{
      padding-left: 16px;
      padding-right: 16px;
    }
  }

  @media (max-width: 520px){
    .rhI-title{
      font-size: 24px;
    }

    .rhI-statGrid{
      grid-template-columns: 1fr;
    }

    .rhI-chip{
      width: 100%;
      justify-content: flex-start;
    }

    .rhI-hero__right{
      width: 100%;
    }

    .rhI-hero__right .rhI-btn{
      flex: 1 1 auto;
    }
  }
</style>

<script>
  (function(){
    const wrap = document.querySelector('.jsTableWrap');
    const table = document.querySelector('.jsTable');
    const top = document.querySelector('.jsScrollTop');
    const topInner = document.querySelector('.jsScrollTopInner');

    if (wrap && table && top && topInner) {
      const syncWidth = () => {
        topInner.style.width = table.scrollWidth + 'px';
      };

      syncWidth();
      window.addEventListener('resize', syncWidth);

      let lock = false;

      top.addEventListener('scroll', () => {
        if (lock) return;
        lock = true;
        wrap.scrollLeft = top.scrollLeft;
        requestAnimationFrame(() => lock = false);
      });

      wrap.addEventListener('scroll', () => {
        if (lock) return;
        lock = true;
        top.scrollLeft = wrap.scrollLeft;
        requestAnimationFrame(() => lock = false);
      });
    }

    document.addEventListener('click', async (e) => {
      const el = e.target.closest('.copy');
      if (!el) return;

      const text = el.getAttribute('data-copy') || el.textContent.trim();
      const old = el.textContent;

      try{
        await navigator.clipboard.writeText(text);
      }catch(err){
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
      }

      el.classList.add('is-copied');
      el.textContent = 'Copiado!';
      setTimeout(() => {
        el.classList.remove('is-copied');
        el.textContent = old;
      }, 900);
    });
  })();
</script>
@endsection