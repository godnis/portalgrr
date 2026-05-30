@extends('layouts.app')

@section('content')
@php
  // ✅ Lê os valores diretamente do config/grr.php
  $xpCfg  = (array) config('grr.xp', []);
  $xpMult = (array) config('grr.xp_multipliers', []);

  // defaults
  $xpCfg = array_merge([
    'drogas' => 0,
    'pistolas' => 0,
    'smg_fuzil' => 0,
    'municoes' => 0,
    'dinheiro' => 0,
    'explosivos' => 0,
    'lockpicks' => 0,
    'abordagens' => 0,
    'multas' => 0,
    'bopm' => 0,
    'viaturas_fiscalizadas' => 0,
    'relatorio_aprovado' => 0,
  ], $xpCfg);

  $xpMult = array_merge([
    'P1' => 1.00,
    'P2' => 1.00,
    'P3' => 1.00,
    'P4' => 1.00,
    'P5' => 1.00,
  ], $xpMult);

  // ✅ helpers
  $fmtInt = function($n){
    $n = (int) ($n ?? 0);
    return number_format($n, 0, ',', '.');
  };

  $fmtMoney = function($n){
    $n = (int) ($n ?? 0);
    return 'R$ ' . number_format($n, 0, ',', '.');
  };

  $fmtMult = function($n){
    $n = (float) ($n ?? 1);
    return number_format($n, 2, ',', '.');
  };

  $pct = function($mult){
    $m = (float) $mult;
    $perc = ($m - 1) * 100;
    if (abs($perc) < 0.0001) return '0%';
    return ($perc > 0 ? '+' : '') . number_format($perc, 0, ',', '.') . '%';
  };

  $avatarUrl = function($name, $avatar_path){
    if (!empty($avatar_path)) {
      return str_starts_with($avatar_path, 'http')
        ? $avatar_path
        : asset('storage/'.ltrim($avatar_path,'/'));
    }
    return 'https://ui-avatars.com/api/?name='.urlencode((string)$name).'&size=128&background=0D2A4A&color=fff&bold=true';
  };

  $periodoLabel = match(($periodo ?? 'mes')) {
    'hoje' => 'Hoje',
    'semana' => 'Semana',
    'mes' => 'Mês',
    'geral' => 'Geral',
    default => ucfirst((string)($periodo ?? 'mes')),
  };

  $top1Name   = $top1->name ?? '—';
  $top1Avatar = $avatarUrl($top1Name, $top1->avatar_path ?? null);

  $meName   = auth()->user()->name ?? '—';
  $meAvatar = $avatarUrl($meName, auth()->user()->avatar_path ?? null);

  // ✅ Filtros
  $mesSel = (int) request('mes', now()->month);
  $anoSel = (int) request('ano', now()->year);

  $fUnidade = (string) request('unidade', '');
  $fTipo    = (string) request('tipo', '');
  $fAgente  = (string) request('agente', '');

  $agenteNomeSel = null;
  if (!empty($fAgente) && !empty($agentes)) {
    foreach(($agentes ?? []) as $a){
      if ((string)($a->id ?? '') === (string)$fAgente) { $agenteNomeSel = $a->name ?? null; break; }
    }
  }

  $hasFilters = (bool) ($fUnidade || $fTipo || $fAgente);

  $q = $q ?? request('q');

  // ✅ Resumo executivo
  $top10Count = 0;
  $top10TotalXp = 0;
  $top10TotalRel = 0;

  foreach(($top10 ?? []) as $r){
    $top10Count++;
    $top10TotalXp += (int)($r->xp ?? 0);
    $top10TotalRel += (int)($r->relatorios ?? 0);
  }

  $mediaTop10Xp = $top10Count > 0 ? (int) round($top10TotalXp / $top10Count) : 0;
  $liderXp = (int)($top1->xp ?? 0);
  $meXp = (int)($meuResumo->xp ?? 0);
  $deltaLider = max(0, $liderXp - $meXp);

  $filtrosCount = 0;
  if($fUnidade) $filtrosCount++;
  if($fTipo) $filtrosCount++;
  if($fAgente) $filtrosCount++;

  // ✅ Top 3 / gráfico
  $topItems = collect($top10 ?? [])->values();
  $podio1 = $topItems->get(0);
  $podio2 = $topItems->get(1);
  $podio3 = $topItems->get(2);

  $podio1Name = $podio1->name ?? $podio1->nome ?? '—';
  $podio2Name = $podio2->name ?? $podio2->nome ?? '—';
  $podio3Name = $podio3->name ?? $podio3->nome ?? '—';

  $podio1Avatar = $avatarUrl($podio1Name, $podio1->avatar_path ?? null);
  $podio2Avatar = $avatarUrl($podio2Name, $podio2->avatar_path ?? null);
  $podio3Avatar = $avatarUrl($podio3Name, $podio3->avatar_path ?? null);

  $chartItems = $topItems->take(5);
  $chartMaxXp = max(1, (int)($chartItems->max(fn($r) => (int)($r->xp ?? 0)) ?? 1));

  // ✅ Progresso até o líder
  $progressToLeader = 0;
  if ($liderXp > 0) {
    $progressToLeader = min(100, max(0, round(($meXp / $liderXp) * 100)));
  }

  // ✅ Gap do top 2 e top 3
  $top2Xp = (int)($podio2->xp ?? 0);
  $top3Xp = (int)($podio3->xp ?? 0);
  $gap12 = max(0, $liderXp - $top2Xp);
  $gap23 = max(0, $top2Xp - $top3Xp);
@endphp

<div class="rank-wrap">

  {{-- HERO --}}
  <div class="rank-hero">
    <div class="rank-hero__bg"></div>

    <div class="rank-hero__content">
      <div class="rank-hero__left">
        <div class="rank-kicker">GRR • PRF — Ranking Oficial</div>
        <h1 class="rank-title">Ranking de Produtividade (XP)</h1>

        <div class="rank-sub">
          @if(($periodo ?? 'mes') !== 'geral' && $inicio && $fim)
            Período: <b>{{ $inicio->format('d/m/Y') }}</b> a <b>{{ $fim->format('d/m/Y') }}</b>
            <span class="rank-dot"></span>
            Apenas relatórios aprovados
          @else
            Período: <b>Geral</b>
            <span class="rank-dot"></span>
            Apenas relatórios aprovados
          @endif
        </div>

        <div class="rank-badges">
          <span class="rank-badge">
            <span class="rank-badge__dot"></span>
            status: oficial
          </span>

          <span class="rank-badge rank-badge--soft">
            critério: XP por apreensão/atividade
          </span>

          @if($hasFilters)
            <span class="rank-badge rank-badge--filter">
              <span class="rank-badge__dot2"></span>
              {{ $filtrosCount }} filtro{{ $filtrosCount > 1 ? 's' : '' }} ativo{{ $filtrosCount > 1 ? 's' : '' }}
            </span>
          @endif
        </div>

        <div class="rank-hero-stats">
          <div class="rank-hero-stat">
            <div class="rank-hero-stat__label">Líder do período</div>
            <div class="rank-hero-stat__value">{{ $top1Name }}</div>
            <div class="rank-hero-stat__sub">{{ $fmtInt($liderXp) }} XP</div>
          </div>

          <div class="rank-hero-stat">
            <div class="rank-hero-stat__label">Média do Top 10</div>
            <div class="rank-hero-stat__value">{{ $fmtInt($mediaTop10Xp) }}</div>
            <div class="rank-hero-stat__sub">XP por servidor</div>
          </div>

          <div class="rank-hero-stat">
            <div class="rank-hero-stat__label">Relatórios Top 10</div>
            <div class="rank-hero-stat__value">{{ $fmtInt($top10TotalRel) }}</div>
            <div class="rank-hero-stat__sub">aprovados no recorte</div>
          </div>

          <div class="rank-hero-stat">
            <div class="rank-hero-stat__label">Diferença até o líder</div>
            <div class="rank-hero-stat__value">{{ $fmtInt($deltaLider) }}</div>
            <div class="rank-hero-stat__sub">XP para alcançar o topo</div>
          </div>
        </div>

        <div class="rank-hero-note">
          Painel consolidado para leitura rápida do desempenho individual e institucional.
        </div>
      </div>

      <div class="rank-hero__right">
        <form class="rank-filter" method="GET" action="{{ route('ranking.index') }}">
          <input type="hidden" name="periodo" value="{{ $periodo ?? 'mes' }}">

          <div class="rank-filter__topbar">
            <div>
              <div class="rank-filter__title">Central de filtros</div>
              <div class="rank-filter__topsub">Refine o ranking por período, unidade, tipo de ocorrência e agente.</div>
            </div>

            @if($hasFilters)
              <span class="rank-filter__status">
                filtros aplicados
              </span>
            @endif
          </div>

          <div class="rank-filter__row">
            <div class="rank-filter__field">
              <div class="rank-filter__label">Mês</div>
              <select class="form-select form-select-sm rank-select" name="mes" @disabled(($periodo ?? 'mes') === 'geral')>
                @for($m=1;$m<=12;$m++)
                  <option value="{{ $m }}" @selected($m==$mesSel)>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
                @endfor
              </select>
            </div>

            <div class="rank-filter__field">
              <div class="rank-filter__label">Ano</div>
              <select class="form-select form-select-sm rank-select" name="ano" @disabled(($periodo ?? 'mes') === 'geral')>
                @for($a=now()->year;$a>=now()->year-3;$a--)
                  <option value="{{ $a }}" @selected($a==$anoSel)>{{ $a }}</option>
                @endfor
              </select>
            </div>

            <button class="btn btn-sm btn-primary rank-filter__btn" type="submit">
              Aplicar filtro
            </button>

            <a href="#meu-posicionamento" class="btn btn-sm btn-outline-secondary rank-filter__btn">
              Minha posição
            </a>
          </div>

          <div class="rank-filter__row2">
            <div class="rank-filter__field">
              <div class="rank-filter__label">Unidade</div>
              <select class="form-select form-select-sm rank-select" name="unidade">
                <option value="">Todas</option>
                @foreach(($unidades ?? []) as $u)
                  <option value="{{ $u }}" @selected($fUnidade == $u)>{{ $u }}</option>
                @endforeach
              </select>
            </div>

            <div class="rank-filter__field">
              <div class="rank-filter__label">Tipo de ocorrência</div>
              <select class="form-select form-select-sm rank-select" name="tipo">
                <option value="">Todos</option>
                @foreach(($tipos ?? []) as $t)
                  <option value="{{ $t }}" @selected($fTipo == $t)>{{ $t }}</option>
                @endforeach
              </select>
            </div>

            <div class="rank-filter__field">
              <div class="rank-filter__label">Agente</div>
              <select class="form-select form-select-sm rank-select" name="agente">
                <option value="">Todos</option>
                @foreach(($agentes ?? []) as $a)
                  <option value="{{ $a->id }}" @selected((string)$fAgente === (string)$a->id)>{{ $a->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="rank-filter__hint">
            Filtros avançados aplicam em KPIs, leitura visual, ranking e tabelas institucionais.
          </div>

          @if($hasFilters)
            <div class="rank-filter__chips">
              @if($fUnidade)
                <span class="rank-chip rank-chip--mini"><span class="rank-chip__ico">🏷️</span> Unidade: <b>{{ $fUnidade }}</b></span>
              @endif
              @if($fTipo)
                <span class="rank-chip rank-chip--mini"><span class="rank-chip__ico">📌</span> Tipo: <b>{{ $fTipo }}</b></span>
              @endif
              @if($fAgente)
                <span class="rank-chip rank-chip--mini"><span class="rank-chip__ico">👤</span> Agente: <b>{{ $agenteNomeSel ?? ('ID '.$fAgente) }}</b></span>
              @endif
            </div>
          @endif

          <div class="rank-filter__actions">
            <a class="btn btn-sm btn-outline-secondary rank-filter__btn2"
               href="{{ route('ranking.index', ['periodo'=>$periodo ?? 'mes','mes'=>$mesSel,'ano'=>$anoSel]) }}">
              Limpar filtros avançados
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- PÓDIO + GRÁFICO --}}
  <div class="rank-grid-2 rank-grid-2--top mt-3">
    <div class="rank-card">
      <div class="rank-card__head">
        <div>
          <div class="rank-card__title">Pódio do período</div>
          <div class="rank-card__sub">Os 3 maiores desempenhos oficiais no recorte selecionado</div>
        </div>
        <div class="rank-card__pill">PÓDIO</div>
      </div>

      @if($topItems->count() > 0)
        <div class="rank-podium">
          <div class="rank-podium__inner">
            <div class="rank-podium-card rank-podium-card--silver">
              <div class="rank-podium-card__place">2º</div>
              <img src="{{ $podio2Avatar }}" class="rank-podium-card__avatar" alt="{{ $podio2Name }}">
              <div class="rank-podium-card__name">{{ $podio2Name }}</div>
              <div class="rank-podium-card__xp">{{ $fmtInt($podio2->xp ?? 0) }} XP</div>
              <div class="rank-podium-card__meta">{{ $fmtInt($podio2->relatorios ?? 0) }} relatórios</div>
              <div class="rank-podium-card__base rank-podium-card__base--2"></div>
            </div>

            <div class="rank-podium-card rank-podium-card--gold is-winner">
              <div class="rank-podium-card__crown">👑</div>
              <div class="rank-podium-card__place">1º</div>
              <img src="{{ $podio1Avatar }}" class="rank-podium-card__avatar" alt="{{ $podio1Name }}">
              <div class="rank-podium-card__name">{{ $podio1Name }}</div>
              <div class="rank-podium-card__xp">{{ $fmtInt($podio1->xp ?? 0) }} XP</div>
              <div class="rank-podium-card__meta">{{ $fmtInt($podio1->relatorios ?? 0) }} relatórios</div>
              <div class="rank-podium-card__base rank-podium-card__base--1"></div>
            </div>

            <div class="rank-podium-card rank-podium-card--bronze">
              <div class="rank-podium-card__place">3º</div>
              <img src="{{ $podio3Avatar }}" class="rank-podium-card__avatar" alt="{{ $podio3Name }}">
              <div class="rank-podium-card__name">{{ $podio3Name }}</div>
              <div class="rank-podium-card__xp">{{ $fmtInt($podio3->xp ?? 0) }} XP</div>
              <div class="rank-podium-card__meta">{{ $fmtInt($podio3->relatorios ?? 0) }} relatórios</div>
              <div class="rank-podium-card__base rank-podium-card__base--3"></div>
            </div>
          </div>

          <div class="rank-podium__footer">
            <span class="rank-chip rank-chip--mini"><span class="rank-chip__ico">🥇</span> Gap 1º→2º: <b>{{ $fmtInt($gap12) }} XP</b></span>
            <span class="rank-chip rank-chip--mini"><span class="rank-chip__ico">🥈</span> Gap 2º→3º: <b>{{ $fmtInt($gap23) }} XP</b></span>
          </div>
        </div>
      @else
        <div class="rank-empty">
          <div class="rank-empty__ico">ℹ️</div>
          <div class="rank-empty__txt">
            <div class="rank-empty__title">Sem pódio disponível</div>
            <div class="rank-empty__sub">Não há dados suficientes para exibir o pódio no período.</div>
          </div>
        </div>
      @endif
    </div>

    <div class="rank-card">
      <div class="rank-card__head">
        <div>
          <div class="rank-card__title">Gráfico comparativo de XP</div>
          <div class="rank-card__sub">Leitura rápida dos maiores desempenhos do período</div>
        </div>
        <div class="rank-card__pill rank-card__pill--ok">GRÁFICO</div>
      </div>

      @if($chartItems->count() > 0)
        <div class="rank-chart">
          @foreach($chartItems as $idx => $item)
            @php
              $chartName = $item->name ?? $item->nome ?? '—';
              $chartXp = (int)($item->xp ?? 0);
              $chartPercent = $chartMaxXp > 0 ? max(6, min(100, round(($chartXp / $chartMaxXp) * 100))) : 0;
              $chartAvatar = $avatarUrl($chartName, $item->avatar_path ?? null);
              $isChartMe = ((int)($item->user_id ?? 0) === (int)auth()->id());
            @endphp

            <div class="rank-chart__row {{ $idx === 0 ? 'is-top' : '' }} {{ $isChartMe ? 'is-me' : '' }}">
              <div class="rank-chart__user">
                <img src="{{ $chartAvatar }}" class="rank-avatar rank-avatar--sm" alt="{{ $chartName }}">
                <div class="rank-chart__meta">
                  <div class="rank-chart__name">
                    {{ $chartName }}
                    @if($isChartMe)
                      <span class="rank-badge-mini">você</span>
                    @endif
                  </div>
                  <div class="rank-chart__sub">{{ $fmtInt($item->relatorios ?? 0) }} relatórios</div>
                </div>
              </div>

              <div class="rank-chart__barwrap">
                <div class="rank-chart__bar">
                  <div class="rank-chart__fill" style="width: {{ $chartPercent }}%"></div>
                </div>
              </div>

              <div class="rank-chart__value">{{ $fmtInt($chartXp) }} XP</div>
            </div>
          @endforeach
        </div>
      @else
        <div class="rank-empty">
          <div class="rank-empty__ico">ℹ️</div>
          <div class="rank-empty__txt">
            <div class="rank-empty__title">Sem gráfico disponível</div>
            <div class="rank-empty__sub">Não há registros suficientes para gerar o comparativo visual.</div>
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- BÔNUS POR FUNÇÃO --}}
  <div class="rank-card mt-3">
    <div class="rank-card__head">
      <div>
        <div class="rank-card__title">Bônus por função (multiplicador)</div>
        <div class="rank-card__sub">Multiplicador aplicado sobre o XP total do relatório aprovado</div>
      </div>

      <button
        class="btn btn-sm btn-outline-secondary rank-xp-btn"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#xpTabelaCollapse"
        aria-expanded="false"
        aria-controls="xpTabelaCollapse"
      >
        Ver bônus
      </button>
    </div>

    <div class="collapse" id="xpTabelaCollapse">
      <div class="p-3">
        <div class="rank-xp-box">
          <div class="rank-xp-mults">
            <div class="rank-xp-mults__grid">
              <div class="rank-xp-mult">
                <div class="rank-xp-mult__k">P1 (Motorista)</div>
                <div class="rank-xp-mult__v">x {{ $fmtMult($xpMult['P1']) }}</div>
                <div class="rank-xp-mult__s">{{ $pct($xpMult['P1']) }} de bônus</div>
              </div>

              <div class="rank-xp-mult">
                <div class="rank-xp-mult__k">P2 (Chefe de Barca)</div>
                <div class="rank-xp-mult__v">x {{ $fmtMult($xpMult['P2']) }}</div>
                <div class="rank-xp-mult__s">{{ $pct($xpMult['P2']) }} de bônus</div>
              </div>

              <div class="rank-xp-mult">
                <div class="rank-xp-mult__k">P3 / P4 / P5</div>
                <div class="rank-xp-mult__v">x {{ $fmtMult($xpMult['P3']) }}</div>
                <div class="rank-xp-mult__s">sem bônus</div>
              </div>
            </div>

            <div class="rank-xp-hint">
              * Bônus aplicado no fechamento do relatório aprovado, evitando duplicação por participação.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- APENAS CENTRO DE LEITURA --}}
  <div class="rank-card mt-3">
    <div class="rank-card__head">
      <div>
        <div class="rank-card__title">Centro de leitura</div>
        <div class="rank-card__sub">Indicadores institucionais do ranking</div>
      </div>
      <div class="rank-card__pill rank-card__pill--ok">ONLINE</div>
    </div>

    <div class="rank-insights">
      <div class="rank-insight">
        <div class="rank-insight__k">Base oficial</div>
        <div class="rank-insight__v">Somente aprovados</div>
      </div>
      <div class="rank-insight">
        <div class="rank-insight__k">Período ativo</div>
        <div class="rank-insight__v">{{ $periodoLabel }}</div>
      </div>
      <div class="rank-insight">
        <div class="rank-insight__k">Top 10 visível</div>
        <div class="rank-insight__v">{{ $fmtInt($top10Count) }} servidor(es)</div>
      </div>
      <div class="rank-insight">
        <div class="rank-insight__k">Critério central</div>
        <div class="rank-insight__v">Apreensões + atividades</div>
      </div>
    </div>

    <div class="rank-panel">
      <div class="rank-panel__item">
        <div class="rank-panel__label">Função do ranking</div>
        <div class="rank-panel__value">Meritocracia, controle e comando</div>
      </div>
      <div class="rank-panel__item">
        <div class="rank-panel__label">Situação da leitura</div>
        <div class="rank-panel__value">Painel consolidado e auditável</div>
      </div>
      <div class="rank-panel__item">
        <div class="rank-panel__label">Observação</div>
        <div class="rank-panel__value">XP calculado automaticamente com base nos relatórios aprovados.</div>
      </div>
    </div>
  </div>

  {{-- TOP 10 MELHORADO --}}
  <div class="rank-card rank-card--table mt-3">
    <div class="rank-card__head rank-card__head--table">
      <div>
        <div class="rank-card__title">Top 10</div>
        <div class="rank-card__sub">Classificação oficial do período</div>
      </div>
      <div class="rank-card__pill">TOP</div>
    </div>

    <div class="table-responsive rank-table-wrap">
      <table class="table align-middle rank-table rank-table--pro">
        <thead>
          <tr>
            <th style="width: 110px;">Posição</th>
            <th style="min-width: 260px;">Servidor</th>
            <th style="width: 120px;">RG</th>
            <th style="min-width: 240px;">Cargo</th>
            <th class="text-end" style="width: 130px;">XP</th>
            <th class="text-end" style="width: 110px;">Relat.</th>
            <th class="text-end" style="width: 110px;">Drogas</th>
            <th class="text-end" style="width: 110px;">Pist.</th>
            <th class="text-end" style="width: 120px;">SMG/Fuzil</th>
            <th class="text-end" style="width: 110px;">Muni.</th>
            <th class="text-end" style="width: 150px;">Dinheiro</th>
            <th class="text-end" style="width: 110px;">Expl.</th>
            <th class="text-end" style="width: 110px;">Lock</th>
          </tr>
        </thead>

        <tbody>
          @forelse($top10 as $i => $r)
            @php
              $pos = $i + 1;
              $isMe = ((int)($r->user_id ?? 0) === (int)auth()->id());
              $medal = $pos === 1 ? '🥇' : ($pos === 2 ? '🥈' : ($pos === 3 ? '🥉' : ''));
              $rowClass = $isMe ? 'is-me' : '';

              $name = $r->name ?? $r->nome ?? '—';
              $avatar = $avatarUrl($name, $r->avatar_path ?? null);
            @endphp

            <tr class="{{ $rowClass }}">
              <td>
                <div class="rank-pospro">
                  <div class="rank-pospro__badge rank-pospro__badge--{{ $pos <= 3 ? $pos : 'default' }}">
                    <span class="rank-pospro__num">{{ $pos }}º</span>
                    @if($medal)
                      <span class="rank-pospro__medal">{{ $medal }}</span>
                    @endif
                  </div>
                </div>
              </td>

              <td>
                <div class="rank-serverpro">
                  <img src="{{ $avatar }}" class="rank-serverpro__avatar" alt="Avatar {{ $name }}">
                  <div class="rank-serverpro__meta">
                    <div class="rank-serverpro__name">
                      {{ $name }}
                      @if($isMe)
                        <span class="rank-badge-mini">você</span>
                      @endif
                    </div>
                    <div class="rank-serverpro__sub">
                      {{ $r->unidade ?? 'GRR • PRF' }}
                    </div>
                  </div>
                </div>
              </td>

              <td>
                <span class="rank-table__text">{{ $r->rg ?? '—' }}</span>
              </td>

              <td>
                <span class="rank-table__text rank-table__text--strong">{{ $r->cargo ?? '—' }}</span>
              </td>

              <td class="text-end">
                <span class="rank-table__metric rank-table__metric--xp">{{ $fmtInt($r->xp ?? 0) }}</span>
              </td>

              <td class="text-end">
                <span class="rank-table__metric">{{ $fmtInt($r->relatorios ?? 0) }}</span>
              </td>

              <td class="text-end"><span class="rank-table__metric">{{ $fmtInt($r->drogas ?? 0) }}</span></td>
              <td class="text-end"><span class="rank-table__metric">{{ $fmtInt($r->pistolas ?? 0) }}</span></td>
              <td class="text-end"><span class="rank-table__metric">{{ $fmtInt($r->smg_fuzil ?? 0) }}</span></td>
              <td class="text-end"><span class="rank-table__metric">{{ $fmtInt($r->municoes ?? 0) }}</span></td>
              <td class="text-end"><span class="rank-table__metric">{{ $fmtMoney($r->dinheiro ?? 0) }}</span></td>
              <td class="text-end"><span class="rank-table__metric">{{ $fmtInt($r->explosivos ?? 0) }}</span></td>
              <td class="text-end"><span class="rank-table__metric">{{ $fmtInt($r->lockpicks ?? 0) }}</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="13" class="text-muted">Sem dados no período.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="rank-foot">
      <div class="rank-foot__left">
        <span class="rank-foot__dot"></span>
        Ranking oficial • Auditoria ativa • Dados consolidados
      </div>
      <div class="rank-foot__right">
        GRR • PRF — fivem.bc
      </div>
    </div>
  </div>

  {{-- MEU POSICIONAMENTO --}}
  <div class="rank-card mt-3" id="meu-posicionamento">
    <div class="rank-card__head">
      <div>
        <div class="rank-card__title">Meu posicionamento</div>
        <div class="rank-card__sub">Seu desempenho no período selecionado</div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <div class="rank-card__pill rank-card__pill--me">Você</div>
        <img src="{{ $meAvatar }}" class="rank-avatar rank-avatar--sm" alt="Meu avatar">
      </div>
    </div>

    @if($posicao)
      <div class="rank-me">
        <div class="rank-me__kpis">
          <div class="rank-me__kpi">
            <div class="rank-me__lab">Posição</div>
            <div class="rank-me__val">{{ $fmtInt($posicao) }}º</div>
            <div class="rank-me__hint">ranking oficial</div>
          </div>

          <div class="rank-me__kpi">
            <div class="rank-me__lab">XP</div>
            <div class="rank-me__val">{{ $fmtInt($meuResumo->xp ?? 0) }}</div>
            <div class="rank-me__hint">avaliativo</div>
          </div>

          <div class="rank-me__kpi">
            <div class="rank-me__lab">Relatórios</div>
            <div class="rank-me__val">{{ $fmtInt($meuResumo->relatorios ?? 0) }}</div>
            <div class="rank-me__hint">aprovados</div>
          </div>
        </div>

        <div class="rank-progress-box">
          <div class="rank-progress-box__top">
            <div>
              <div class="rank-progress-box__title">Progresso até o TOP 1</div>
              <div class="rank-progress-box__sub">Comparativo do seu XP atual com o líder do período</div>
            </div>
            <div class="rank-progress-box__pct">{{ $progressToLeader }}%</div>
          </div>

          <div class="rank-progress">
            <div class="rank-progress__bar">
              <div class="rank-progress__fill" style="width: {{ $progressToLeader }}%"></div>
            </div>
          </div>

          <div class="rank-progress__meta">
            <span>Seu XP: <b>{{ $fmtInt($meXp) }}</b></span>
            <span>Líder: <b>{{ $fmtInt($liderXp) }}</b></span>
            <span>Faltam: <b>{{ $fmtInt($deltaLider) }} XP</b></span>
          </div>
        </div>

        <div class="rank-me__meta">
          <span class="rank-chip">
            <span class="rank-chip__ico">🪪</span>
            RG: <b>{{ auth()->user()->rg ?? '—' }}</b>
          </span>

          <span class="rank-chip">
            <span class="rank-chip__ico">🎖️</span>
            Cargo: <b>{{ auth()->user()->cargo ?? '—' }}</b>
          </span>

          <span class="rank-chip rank-chip--soft">
            <span class="rank-chip__ico">🔒</span>
            Auditoria ativa
          </span>
        </div>

        <div class="rank-mini-grid">
          <div class="rank-mini"><div class="rank-mini__k">Drogas</div><div class="rank-mini__v">{{ $fmtInt($meuResumo->drogas ?? 0) }}</div></div>
          <div class="rank-mini"><div class="rank-mini__k">Pistolas</div><div class="rank-mini__v">{{ $fmtInt($meuResumo->pistolas ?? 0) }}</div></div>
          <div class="rank-mini"><div class="rank-mini__k">SMG/Fuzil</div><div class="rank-mini__v">{{ $fmtInt($meuResumo->smg_fuzil ?? 0) }}</div></div>
          <div class="rank-mini"><div class="rank-mini__k">Munições</div><div class="rank-mini__v">{{ $fmtInt($meuResumo->municoes ?? 0) }}</div></div>
          <div class="rank-mini"><div class="rank-mini__k">Dinheiro sujo</div><div class="rank-mini__v">{{ $fmtMoney($meuResumo->dinheiro ?? 0) }}</div></div>
          <div class="rank-mini"><div class="rank-mini__k">Explosivos</div><div class="rank-mini__v">{{ $fmtInt($meuResumo->explosivos ?? 0) }}</div></div>
          <div class="rank-mini"><div class="rank-mini__k">Lockpick</div><div class="rank-mini__v">{{ $fmtInt($meuResumo->lockpicks ?? 0) }}</div></div>
        </div>
      </div>
    @else
      <div class="rank-empty">
        <div class="rank-empty__ico">ℹ️</div>
        <div class="rank-empty__txt">
          <div class="rank-empty__title">Sem registros aprovados</div>
          <div class="rank-empty__sub">Não há relatórios aprovados para você no período selecionado.</div>
        </div>
      </div>
    @endif
  </div>

  {{-- HORAS (NÍVEL 6+) --}}
  @if((int)(auth()->user()->nivel ?? 0) >= 6)
    <div class="rank-card rank-card--table mt-3" id="horas-patrulhamento">
      <div class="rank-card__head rank-card__head--table">
        <div>
          <div class="rank-card__title">Horas de patrulhamento (total)</div>
          <div class="rank-card__sub">Efetivo consolidado por participação • período selecionado</div>
        </div>
        <div class="rank-card__pill rank-card__pill--me">NÍVEL 6+</div>
      </div>

      <div class="p-3">
        <form method="GET" action="{{ route('ranking.index') }}" class="rank-hours-filter">
          <input type="hidden" name="periodo" value="{{ $periodo ?? 'mes' }}">
          @if(($periodo ?? 'mes') !== 'geral')
            <input type="hidden" name="mes" value="{{ $mesSel }}">
            <input type="hidden" name="ano" value="{{ $anoSel }}">
          @endif
          <input type="hidden" name="unidade" value="{{ $fUnidade }}">
          <input type="hidden" name="tipo" value="{{ $fTipo }}">
          <input type="hidden" name="agente" value="{{ $fAgente }}">

          <div class="rank-hours-filter__row">
            <div class="rank-hours-filter__field">
              <div class="rank-filter__label">Buscar (RG ou Nome)</div>
              <div class="rank-hours-inputwrap">
                <span class="rank-hours-ico" aria-hidden="true">🔎</span>
                <input
                  type="text"
                  name="q"
                  value="{{ $q }}"
                  class="form-control form-control-sm rank-hours-input"
                  placeholder="Ex.: 12.178 ou Thomas..."
                  autocomplete="off"
                >
              </div>
            </div>

            <button class="btn btn-sm btn-primary rank-filter__btn" type="submit">
              Buscar
            </button>

            @if(!empty($q))
              <a class="btn btn-sm btn-outline-secondary rank-filter__btn"
                 href="{{ route('ranking.index', [
                    'periodo' => $periodo ?? 'mes',
                    'mes' => $mesSel,
                    'ano' => $anoSel,
                    'unidade' => $fUnidade,
                    'tipo' => $fTipo,
                    'agente' => $fAgente,
                 ]) }}#horas-patrulhamento">
                Limpar
              </a>
            @endif
          </div>

          <div class="rank-filter__hint mt-2">
            Mostra apenas relatórios <b>aprovados</b>. Horas são somadas por participação.
          </div>
        </form>
      </div>

      <div class="table-responsive rank-table-wrap">
        <table class="table align-middle rank-table rank-table--pro rank-table--hourspro">
          <thead>
            <tr>
              <th style="width: 110px;">Posição</th>
              <th style="min-width: 260px;">Servidor</th>
              <th style="width: 120px;">RG</th>
              <th style="min-width: 220px;">Cargo</th>
              <th class="text-end" style="width: 140px;">Relatórios</th>
              <th class="text-end" style="width: 160px;">Horas (HH:MM)</th>
              <th class="text-end" style="width: 140px;">Horas (dec.)</th>
            </tr>
          </thead>

          <tbody>
            @forelse($horasOficiais as $i => $h)
              @php
                $pos = $i + 1;
                $isMe = ((int)($h->user_id ?? 0) === (int)auth()->id());
                $name = $h->name ?? '—';
                $avatar = $avatarUrl($name, $h->avatar_path ?? null);
              @endphp

              <tr class="{{ $isMe ? 'is-me' : '' }}">
                <td>
                  <div class="rank-pospro">
                    <div class="rank-pospro__badge rank-pospro__badge--{{ $pos <= 3 ? $pos : 'default' }}">
                      <span class="rank-pospro__num">{{ $fmtInt($pos) }}º</span>
                    </div>
                  </div>
                </td>

                <td>
                  <div class="rank-serverpro">
                    <img src="{{ $avatar }}" class="rank-serverpro__avatar" alt="Avatar {{ $name }}">
                    <div class="rank-serverpro__meta">
                      <div class="rank-serverpro__name">
                        {{ $name }}
                        @if($isMe)
                          <span class="rank-badge-mini">você</span>
                        @endif
                      </div>
                      <div class="rank-serverpro__sub">Efetivo • GRR</div>
                    </div>
                  </div>
                </td>

                <td><span class="rank-table__text">{{ $h->rg ?? '—' }}</span></td>
                <td><span class="rank-table__text rank-table__text--strong">{{ $h->cargo ?? '—' }}</span></td>
                <td class="text-end"><span class="rank-table__metric">{{ $fmtInt($h->relatorios ?? 0) }}</span></td>
                <td class="text-end"><span class="rank-table__metric rank-table__metric--xp">{{ $h->hhmm ?? '00:00' }}</span></td>
                <td class="text-end"><span class="rank-table__metric">{{ number_format((float)($h->total_horas ?? 0), 2, ',', '.') }}</span></td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-muted">Sem dados para este período/filtro.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="rank-foot">
        <div class="rank-foot__left">
          <span class="rank-foot__dot"></span>
          Horas totais • Controle institucional (nível 6+)
        </div>
        <div class="rank-foot__right">
          GRR • PRF — fivem.bc
        </div>
      </div>
    </div>
  @endif

</div>

<style>
/* =========================================================
   RANKING — GRR 3.0 PREMIUM
========================================================= */

.rank-wrap{ padding: 6px 0 14px; }

/* HERO */
.rank-hero{
  position: relative;
  border-radius: 22px;
  overflow: hidden;
  border: 1px solid rgba(15,23,42,.10);
  background: #fff;
  box-shadow: 0 18px 44px rgba(15,23,42,.10);
  margin-bottom: 14px;
}
.rank-hero__bg{
  position:absolute; inset:0;
  background:
    radial-gradient(900px 420px at 20% 30%, rgba(19,81,180,.14), transparent 60%),
    radial-gradient(900px 420px at 80% 20%, rgba(201,162,39,.14), transparent 55%),
    linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,1));
}
.rank-hero__content{
  position:relative;
  padding: 18px;
  display:grid;
  grid-template-columns: 1.08fr .92fr;
  gap: 16px;
  align-items: start;
}
.rank-hero__left{ position: relative; }
.rank-hero__left > *{ position: relative; z-index: 1; }
.rank-kicker{
  font-size: 11px;
  font-weight: 950;
  letter-spacing: .14em;
  text-transform: uppercase;
  color: rgba(100,116,139,.95);
}
.rank-title{
  margin: 6px 0 6px;
  font-size: 30px;
  line-height: 1.05;
  font-weight: 950;
  letter-spacing: -.6px;
  color: #0b2a4a;
}
.rank-sub{
  color: rgba(51,65,85,.95);
  font-size: 13px;
  font-weight: 700;
}
.rank-dot{
  display:inline-block;
  width:6px;
  height:6px;
  border-radius:999px;
  background: #c9a227;
  margin: 0 8px;
  transform: translateY(-1px);
}
.rank-badges{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
  margin-top: 12px;
}
.rank-badge{
  display:inline-flex;
  align-items:center;
  gap:8px;
  padding: 8px 12px;
  border-radius: 999px;
  border: 1px solid rgba(19,81,180,.18);
  background: rgba(19,81,180,.08);
  font-weight: 900;
  font-size: 12px;
  color: #0b2a4a;
}
.rank-badge__dot,
.rank-badge__dot2{
  width:8px;
  height:8px;
  border-radius:50%;
}
.rank-badge__dot{
  background: #1351b4;
  box-shadow: 0 0 0 4px rgba(19,81,180,.12);
}
.rank-badge__dot2{
  background: rgba(201,162,39,.95);
  box-shadow: 0 0 0 4px rgba(201,162,39,.14);
}
.rank-badge--soft{
  border-color: rgba(15,23,42,.10);
  background: rgba(15,23,42,.03);
  color: rgba(51,65,85,.95);
}
.rank-badge--filter{
  border-color: rgba(201,162,39,.35);
  background: rgba(201,162,39,.12);
  color: #6b4e00;
}

/* HERO STATS */
.rank-hero-stats{
  margin-top: 14px;
  display:grid;
  grid-template-columns: repeat(2, minmax(0,1fr));
  gap: 10px;
}
.rank-hero-stat{
  border: 1px solid rgba(15,23,42,.10);
  background: rgba(255,255,255,.78);
  backdrop-filter: blur(8px);
  border-radius: 16px;
  padding: 12px;
  box-shadow: 0 10px 24px rgba(15,23,42,.06);
}
.rank-hero-stat__label{
  font-size: 12px;
  font-weight: 900;
  color: rgba(100,116,139,.95);
}
.rank-hero-stat__value{
  margin-top: 4px;
  font-size: 18px;
  font-weight: 950;
  line-height: 1.1;
  color: #0f172a;
}
.rank-hero-stat__sub{
  margin-top: 4px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.92);
}
.rank-hero-note{
  margin-top: 12px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(71,85,105,.95);
}

/* FILTER */
.rank-filter{
  border: 1px solid rgba(15,23,42,.10);
  background: rgba(255,255,255,.88);
  border-radius: 18px;
  padding: 14px;
  box-shadow: 0 10px 22px rgba(15,23,42,.06);
}
.rank-filter__topbar{
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap: 10px;
  margin-bottom: 12px;
}
.rank-filter__title{
  font-size: 14px;
  font-weight: 950;
  color: #0b2a4a;
}
.rank-filter__topsub{
  margin-top: 2px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.92);
}
.rank-filter__status{
  display:inline-flex;
  align-items:center;
  padding: 7px 10px;
  border-radius: 999px;
  border: 1px solid rgba(201,162,39,.35);
  background: rgba(201,162,39,.12);
  color: #6b4e00;
  font-size: 11px;
  font-weight: 950;
  text-transform: uppercase;
  letter-spacing: .08em;
  white-space: nowrap;
}
.rank-filter__row{
  display:grid;
  grid-template-columns: 1fr 1fr auto auto;
  gap:10px;
  align-items:end;
}
.rank-filter__row2{
  margin-top: 10px;
  display:grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap:10px;
  align-items:end;
}
.rank-filter__field{ min-width: 0; }
.rank-filter__label{
  font-size: 12px;
  font-weight: 950;
  color: rgba(51,65,85,.95);
  margin-bottom: 6px;
}
.rank-select{
  border-radius: 12px !important;
  height: 44px;
}
.rank-select:disabled{
  opacity: .65;
  cursor: not-allowed;
}
.rank-filter__btn{
  height: 44px;
  border-radius: 12px;
  font-weight: 950;
  padding: 0 14px;
  white-space: nowrap;
}
.rank-filter__hint{
  margin-top: 10px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.95);
  line-height: 1.35;
}
.rank-filter__chips{
  margin-top: 10px;
  display:flex;
  gap: 10px;
  flex-wrap: wrap;
}
.rank-filter__actions{
  margin-top: 10px;
  display:flex;
  justify-content:flex-end;
}
.rank-filter__btn2{
  height: 40px;
  border-radius: 12px;
  font-weight: 950;
  padding: 0 14px;
  white-space: nowrap;
}

/* GRID */
.rank-grid-2{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
.rank-grid-2--top{ align-items: stretch; }

/* CARD */
.rank-card{
  border: 1px solid rgba(15,23,42,.10);
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 18px 44px rgba(15,23,42,.08);
  overflow:hidden;
}
.rank-card--table{
  border-radius: 22px;
}
.rank-card__head{
  padding: 14px 14px 10px;
  display:flex;
  align-items:flex-start;
  justify-content: space-between;
  gap: 10px;
  border-bottom: 1px solid rgba(15,23,42,.08);
  background: rgba(15,23,42,.02);
}
.rank-card__head--table{
  padding: 16px 18px 12px;
  background:
    linear-gradient(180deg, rgba(19,81,180,.08), rgba(15,23,42,.02));
}
.rank-card__title{
  font-weight: 950;
  color: #0b2a4a;
  letter-spacing: -.2px;
}
.rank-card__sub{
  margin-top: 2px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.95);
}
.rank-card__pill{
  font-size: 11px;
  font-weight: 950;
  letter-spacing: .14em;
  text-transform: uppercase;
  padding: 6px 10px;
  border-radius: 999px;
  border: 1px solid rgba(15,23,42,.10);
  background: rgba(255,255,255,.8);
  color: rgba(51,65,85,.95);
}
.rank-card__pill--me{
  border-color: rgba(201,162,39,.35);
  background: rgba(201,162,39,.12);
  color: #7a5c00;
}
.rank-card__pill--ok{
  border-color: rgba(25,135,84,.30);
  background: rgba(25,135,84,.10);
  color: #0f5132;
}

/* PÓDIO */
.rank-podium{ padding: 16px 14px 14px; }
.rank-podium__inner{
  display:grid;
  grid-template-columns: 1fr 1.15fr 1fr;
  gap: 12px;
  align-items:end;
}
.rank-podium-card{
  position: relative;
  text-align:center;
  border: 1px solid rgba(15,23,42,.10);
  border-radius: 18px;
  background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.98));
  box-shadow: 0 14px 30px rgba(15,23,42,.08);
  padding: 14px 12px 0;
  overflow:hidden;
}
.rank-podium-card.is-winner{
  transform: translateY(-8px);
  box-shadow: 0 22px 42px rgba(15,23,42,.14);
}
.rank-podium-card--gold{ border-color: rgba(201,162,39,.34); }
.rank-podium-card--silver{ border-color: rgba(148,163,184,.26); }
.rank-podium-card--bronze{ border-color: rgba(180,120,60,.28); }
.rank-podium-card__crown{
  position:absolute;
  top: 8px;
  right: 10px;
  font-size: 18px;
}
.rank-podium-card__place{
  font-size: 12px;
  font-weight: 950;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: rgba(100,116,139,.95);
}
.rank-podium-card__avatar{
  width: 68px;
  height: 68px;
  border-radius: 999px;
  object-fit: cover;
  margin-top: 10px;
  border: 3px solid rgba(255,255,255,.92);
  box-shadow: 0 8px 20px rgba(15,23,42,.12);
}
.rank-podium-card__name{
  margin-top: 10px;
  font-size: 15px;
  font-weight: 950;
  color: #0f172a;
  line-height: 1.15;
}
.rank-podium-card__xp{
  margin-top: 5px;
  font-size: 18px;
  font-weight: 950;
  color: #0b2a4a;
}
.rank-podium-card__meta{
  margin-top: 3px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.95);
}
.rank-podium-card__base{
  margin-top: 14px;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
}
.rank-podium-card__base--1{
  height: 88px;
  background: linear-gradient(180deg, rgba(201,162,39,.26), rgba(201,162,39,.14));
}
.rank-podium-card__base--2{
  height: 64px;
  background: linear-gradient(180deg, rgba(148,163,184,.24), rgba(148,163,184,.10));
}
.rank-podium-card__base--3{
  height: 46px;
  background: linear-gradient(180deg, rgba(180,120,60,.24), rgba(180,120,60,.10));
}
.rank-podium__footer{
  margin-top: 12px;
  display:flex;
  gap: 10px;
  flex-wrap: wrap;
}

/* GRÁFICO */
.rank-chart{
  padding: 14px;
  display:grid;
  gap: 12px;
}
.rank-chart__row{
  display:grid;
  grid-template-columns: minmax(180px, 220px) 1fr 110px;
  gap: 12px;
  align-items:center;
}
.rank-chart__row.is-top .rank-chart__fill{
  background: linear-gradient(90deg, #c9a227, #efd57b);
}
.rank-chart__row.is-me{
  padding: 8px 10px;
  border-radius: 16px;
  background: rgba(19,81,180,.05);
}
.rank-chart__user{
  display:flex;
  align-items:center;
  gap: 10px;
  min-width: 0;
}
.rank-chart__meta{ min-width: 0; }
.rank-chart__name{
  display:flex;
  align-items:center;
  gap: 8px;
  flex-wrap: wrap;
  font-size: 13px;
  font-weight: 950;
  color: #0f172a;
}
.rank-chart__sub{
  margin-top: 2px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.92);
}
.rank-chart__barwrap{ width: 100%; }
.rank-chart__bar{
  height: 14px;
  border-radius: 999px;
  background: rgba(15,23,42,.08);
  overflow:hidden;
}
.rank-chart__fill{
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, #1351b4, #4f8ef7);
  box-shadow: 0 6px 16px rgba(19,81,180,.26);
}
.rank-chart__value{
  text-align:right;
  font-size: 13px;
  font-weight: 950;
  color: #0f172a;
  white-space: nowrap;
}

/* XP BOX */
.rank-xp-btn{
  border-radius: 12px;
  font-weight: 950;
  padding: 8px 12px;
}
.rank-xp-box{
  border: 1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.02);
  border-radius: 16px;
  padding: 14px;
}
.rank-xp-mults__grid{
  display:grid;
  grid-template-columns: repeat(3, minmax(0,1fr));
  gap: 10px;
}
.rank-xp-mult{
  border: 1px solid rgba(15,23,42,.10);
  background: #fff;
  border-radius: 14px;
  padding: 12px;
  box-shadow: 0 10px 20px rgba(15,23,42,.06);
}
.rank-xp-mult__k{
  font-size: 12px;
  font-weight: 950;
  color: rgba(100,116,139,.95);
}
.rank-xp-mult__v{
  margin-top: 4px;
  font-size: 15px;
  font-weight: 950;
  color: #0f172a;
}
.rank-xp-mult__s{
  margin-top: 2px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.95);
}
.rank-xp-hint{
  margin-top: 10px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.95);
}

/* ME */
.rank-me{ padding: 14px; }
.rank-me__kpis{
  display:grid;
  grid-template-columns: repeat(3, minmax(0,1fr));
  gap: 10px;
}
.rank-me__kpi{
  border: 1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.02);
  border-radius: 16px;
  padding: 12px;
}
.rank-me__lab{
  font-size: 12px;
  font-weight: 900;
  color: rgba(100,116,139,.95);
}
.rank-me__val{
  margin-top: 4px;
  font-size: 22px;
  font-weight: 950;
  letter-spacing: -.4px;
  color: #0f172a;
  font-variant-numeric: tabular-nums;
}
.rank-me__hint{
  margin-top: 2px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.95);
}
.rank-me__meta{
  margin-top: 12px;
  display:flex;
  gap: 10px;
  flex-wrap: wrap;
}

/* CHIP */
.rank-chip{
  display:inline-flex;
  align-items:center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 999px;
  border: 1px solid rgba(15,23,42,.10);
  background: #fff;
  font-weight: 900;
  color: rgba(51,65,85,.95);
  box-shadow: 0 10px 20px rgba(15,23,42,.06);
}
.rank-chip--mini{
  padding: 6px 10px;
  font-size: 12px;
  box-shadow: none;
  background: rgba(255,255,255,.85);
}
.rank-chip__ico{
  width: 18px;
  text-align:center;
}
.rank-chip--soft{
  background: rgba(15,23,42,.03);
  box-shadow: none;
}

/* PROGRESS */
.rank-progress-box{
  margin-top: 12px;
  border: 1px solid rgba(15,23,42,.10);
  background: linear-gradient(180deg, rgba(15,23,42,.02), rgba(15,23,42,.03));
  border-radius: 18px;
  padding: 14px;
}
.rank-progress-box__top{
  display:flex;
  justify-content:space-between;
  gap: 10px;
  align-items:flex-start;
}
.rank-progress-box__title{
  font-size: 13px;
  font-weight: 950;
  color: #0f172a;
}
.rank-progress-box__sub{
  margin-top: 2px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.92);
}
.rank-progress-box__pct{
  font-size: 18px;
  font-weight: 950;
  color: #0b2a4a;
  white-space: nowrap;
}
.rank-progress{ margin-top: 12px; }
.rank-progress__bar{
  height: 16px;
  border-radius: 999px;
  background: rgba(15,23,42,.08);
  overflow: hidden;
}
.rank-progress__fill{
  height: 100%;
  border-radius: inherit;
  background: linear-gradient(90deg, #1351b4, #6ea8fe);
  box-shadow: 0 6px 16px rgba(19,81,180,.22);
}
.rank-progress__meta{
  margin-top: 10px;
  display:flex;
  gap: 10px 16px;
  flex-wrap: wrap;
  font-size: 12px;
  font-weight: 800;
  color: rgba(71,85,105,.95);
}

/* MINI GRID */
.rank-mini-grid{
  margin-top: 12px;
  display:grid;
  grid-template-columns: repeat(3, minmax(0,1fr));
  gap: 10px;
}
.rank-mini{
  border: 1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.02);
  border-radius: 16px;
  padding: 10px 12px;
}
.rank-mini__k{
  font-size: 12px;
  font-weight: 900;
  color: rgba(100,116,139,.95);
}
.rank-mini__v{
  margin-top: 4px;
  font-size: 14px;
  font-weight: 950;
  color: #0f172a;
  font-variant-numeric: tabular-nums;
}

/* EMPTY */
.rank-empty{
  padding: 14px;
  display:flex;
  gap: 12px;
  align-items:flex-start;
}
.rank-empty__ico{
  width: 42px;
  height: 42px;
  border-radius: 16px;
  display:flex;
  align-items:center;
  justify-content:center;
  background: rgba(249,176,0,.14);
  border: 1px solid rgba(249,176,0,.28);
  font-size: 18px;
}
.rank-empty__title{
  font-weight: 950;
  color: #0f172a;
}
.rank-empty__sub{
  margin-top: 2px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.95);
}

/* INSIGHTS */
.rank-insights{
  padding: 14px;
  display:grid;
  grid-template-columns: repeat(2, minmax(0,1fr));
  gap: 10px;
}
.rank-insight{
  border: 1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.02);
  border-radius: 16px;
  padding: 12px;
}
.rank-insight__k{
  font-size: 12px;
  font-weight: 900;
  color: rgba(100,116,139,.95);
}
.rank-insight__v{
  margin-top: 4px;
  font-weight: 950;
  color: #0f172a;
}
.rank-panel{
  padding: 0 14px 14px;
  display:grid;
  gap: 10px;
}
.rank-panel__item{
  border: 1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.02);
  border-radius: 16px;
  padding: 12px;
}
.rank-panel__label{
  font-size: 12px;
  font-weight: 900;
  color: rgba(100,116,139,.95);
}
.rank-panel__value{
  margin-top: 4px;
  font-size: 13px;
  font-weight: 900;
  color: #0f172a;
}

/* TABLE GERAL */
.rank-table-wrap{
  padding: 0;
}
.rank-table{
  margin: 0;
}
.rank-table thead th{
  font-size: 12px;
  font-weight: 950;
  letter-spacing: .05em;
  text-transform: uppercase;
  color: rgba(100,116,139,.95);
  background: rgba(15,23,42,.02);
  border-bottom: 1px solid rgba(15,23,42,.08) !important;
  white-space: nowrap;
}
.rank-table td{
  border-top: 1px solid rgba(15,23,42,.06) !important;
  white-space: nowrap;
  vertical-align: middle;
  font-variant-numeric: tabular-nums;
}

/* TABELA PROFISSIONAL */
.rank-table--pro thead th{
  padding: 14px 14px;
  background:
    linear-gradient(180deg, rgba(18,30,56,.92), rgba(31,43,70,.96));
  color: rgba(233,239,248,.92);
  border-bottom: none !important;
  position: sticky;
  top: 0;
  z-index: 1;
}
.rank-table--pro tbody tr{
  transition: background .16s ease, transform .16s ease;
}
.rank-table--pro tbody td{
  padding: 14px 14px;
  background: transparent;
}
.rank-table--pro tbody tr:hover{
  background: rgba(19,81,180,.06);
}
.rank-table--pro tbody tr.is-me{
  background: linear-gradient(90deg, rgba(19,81,180,.10), rgba(19,81,180,.04));
}
.rank-table--pro tbody tr.is-me td{
  border-top-color: rgba(19,81,180,.14) !important;
}

.rank-pospro{
  display:flex;
  align-items:center;
}
.rank-pospro__badge{
  min-width: 78px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  gap: 8px;
  padding: 9px 12px;
  border-radius: 14px;
  font-weight: 950;
  border: 1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.03);
  color: #0f172a;
}
.rank-pospro__badge--1{
  background: rgba(201,162,39,.14);
  border-color: rgba(201,162,39,.28);
  color: #7a5c00;
}
.rank-pospro__badge--2{
  background: rgba(148,163,184,.14);
  border-color: rgba(148,163,184,.24);
  color: #475569;
}
.rank-pospro__badge--3{
  background: rgba(180,120,60,.12);
  border-color: rgba(180,120,60,.24);
  color: #8a4b20;
}
.rank-pospro__num{
  font-size: 14px;
  font-weight: 950;
}
.rank-pospro__medal{
  font-size: 14px;
}

.rank-serverpro{
  display:flex;
  align-items:center;
  gap: 12px;
  min-width: 240px;
}
.rank-serverpro__avatar{
  width: 44px;
  height: 44px;
  border-radius: 999px;
  object-fit: cover;
  border: 1px solid rgba(15,23,42,.12);
  background: rgba(15,23,42,.04);
  flex: 0 0 auto;
}
.rank-serverpro__meta{
  display:flex;
  flex-direction:column;
  min-width: 0;
}
.rank-serverpro__name{
  display:flex;
  align-items:center;
  gap: 8px;
  flex-wrap: wrap;
  font-size: 15px;
  font-weight: 950;
  color: #0f172a;
  line-height: 1.1;
}
.rank-serverpro__sub{
  margin-top: 3px;
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.92);
}

.rank-table__text{
  font-size: 14px;
  font-weight: 850;
  color: rgba(15,23,42,.92);
}
.rank-table__text--strong{
  font-weight: 900;
}
.rank-table__metric{
  font-size: 15px;
  font-weight: 950;
  color: #0f172a;
}
.rank-table__metric--xp{
  color: #1351b4;
}

.rank-badge-mini{
  font-size: 11px;
  font-weight: 950;
  padding: 4px 8px;
  border-radius: 999px;
  border: 1px solid rgba(201,162,39,.35);
  background: rgba(201,162,39,.12);
  color: #7a5c00;
}

/* FOOT */
.rank-foot{
  padding: 12px 14px;
  display:flex;
  justify-content: space-between;
  gap: 10px;
  flex-wrap: wrap;
  border-top: 1px solid rgba(15,23,42,.08);
  background: rgba(15,23,42,.02);
  font-size: 12px;
  font-weight: 800;
  color: rgba(100,116,139,.95);
}
.rank-foot__dot{
  display:inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(25,135,84,.85);
  box-shadow: 0 0 0 4px rgba(25,135,84,.12);
  margin-right: 8px;
  transform: translateY(1px);
}

/* AVATAR GERAL */
.rank-avatar{
  width: 42px;
  height: 42px;
  border-radius: 999px;
  object-fit: cover;
  border: 1px solid rgba(15,23,42,.12);
  background: rgba(15,23,42,.04);
  flex: 0 0 auto;
}
.rank-avatar--sm{
  width: 26px;
  height: 26px;
}

/* HORAS */
.rank-hours-filter{
  border: 1px solid rgba(15,23,42,.10);
  background: rgba(255,255,255,.72);
  border-radius: 16px;
  padding: 12px;
  box-shadow: 0 10px 22px rgba(15,23,42,.06);
}
.rank-hours-filter__row{
  display:flex;
  gap: 10px;
  align-items:end;
  flex-wrap: wrap;
}
.rank-hours-filter__field{
  flex: 1 1 360px;
  min-width: 280px;
}
.rank-hours-inputwrap{ position: relative; }
.rank-hours-ico{
  position:absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  opacity: .65;
  font-size: 14px;
  pointer-events: none;
}
.rank-hours-input{
  height: 44px;
  border-radius: 14px;
  border: 1px solid rgba(15,23,42,.14);
  background: rgba(255,255,255,.98);
  padding-left: 38px;
  box-shadow: inset 0 1px 0 rgba(255,255,255,.9);
}
.rank-hours-input:focus{
  border-color: rgba(19,81,180,.40);
  box-shadow: 0 0 0 4px rgba(19,81,180,.14);
}

/* RESPONSIVO */
@media (max-width: 992px){
  .rank-hero__content{ grid-template-columns: 1fr; }
  .rank-grid-2{ grid-template-columns: 1fr; }
  .rank-filter__row{ grid-template-columns: 1fr 1fr; }
  .rank-filter__row2{ grid-template-columns: 1fr; }
  .rank-filter__actions{ justify-content: stretch; }
  .rank-filter__btn2{ width: 100%; justify-content: center; }
  .rank-xp-mults__grid{ grid-template-columns: 1fr; }
  .rank-podium__inner{
    grid-template-columns: 1fr;
    align-items: stretch;
  }
  .rank-podium-card.is-winner{ transform: none; }
  .rank-chart__row{
    grid-template-columns: 1fr;
    gap: 8px;
  }
  .rank-chart__value{ text-align:left; }
}
@media (max-width: 576px){
  .rank-filter__row{ grid-template-columns: 1fr; }
  .rank-me__kpis,
  .rank-mini-grid,
  .rank-insights,
  .rank-hero-stats{
    grid-template-columns: 1fr;
  }
  .rank-title{ font-size: 25px; }
  .rank-progress-box__top{ flex-direction: column; }
}

/* DARK MODE */
html[data-theme="dark"] .rank-hero,
html[data-theme="dark"] .rank-card{
  background: rgba(255,255,255,.04);
  border-color: rgba(255,255,255,.12);
  box-shadow: 0 18px 44px rgba(0,0,0,.45);
}
html[data-theme="dark"] .rank-hero__bg{
  background:
    radial-gradient(900px 420px at 15% 20%, rgba(59,130,246,.28), transparent 60%),
    radial-gradient(900px 420px at 85% 15%, rgba(201,162,39,.18), transparent 55%),
    linear-gradient(180deg, rgba(5,10,18,.92), rgba(8,14,26,.96));
}
html[data-theme="dark"] .rank-hero__left::before{
  content:"";
  position:absolute;
  inset: -10px;
  border-radius: 18px;
  background: rgba(3,7,18,.55);
  border: 1px solid rgba(255,255,255,.10);
  backdrop-filter: blur(10px);
  box-shadow: 0 18px 55px rgba(0,0,0,.45);
  opacity: 1;
}
html[data-theme="dark"] .rank-kicker{
  color: rgba(231,237,246,.82) !important;
}
html[data-theme="dark"] .rank-title{
  color: rgba(255,255,255,.96) !important;
  text-shadow: 0 1px 0 rgba(0,0,0,.55);
}
html[data-theme="dark"] .rank-sub,
html[data-theme="dark"] .rank-hero-note{
  color: rgba(231,237,246,.78) !important;
}
html[data-theme="dark"] .rank-badge{
  border-color: rgba(255,255,255,.16) !important;
  background: rgba(15,20,28,.55) !important;
  color: rgba(255,255,255,.90) !important;
  box-shadow: 0 10px 26px rgba(0,0,0,.30);
}
html[data-theme="dark"] .rank-badge--soft{
  border-color: rgba(255,255,255,.14) !important;
  background: rgba(15,20,28,.40) !important;
  color: rgba(231,237,246,.84) !important;
}
html[data-theme="dark"] .rank-badge--filter{
  border-color: rgba(201,162,39,.35) !important;
  background: rgba(201,162,39,.14) !important;
  color: rgba(255,255,255,.92) !important;
}
html[data-theme="dark"] .rank-badge__dot{
  box-shadow: 0 0 0 4px rgba(59,130,246,.20) !important;
}
html[data-theme="dark"] .rank-badge__dot2{
  box-shadow: 0 0 0 4px rgba(201,162,39,.18) !important;
}

html[data-theme="dark"] .rank-filter{
  background: rgba(15,20,28,.62) !important;
  border-color: rgba(255,255,255,.12) !important;
  backdrop-filter: blur(10px);
  box-shadow: 0 18px 55px rgba(0,0,0,.50) !important;
}
html[data-theme="dark"] .rank-filter__title,
html[data-theme="dark"] .rank-card__title{
  color: rgba(255,255,255,.94);
}
html[data-theme="dark"] .rank-filter__topsub,
html[data-theme="dark"] .rank-filter__hint,
html[data-theme="dark"] .rank-card__sub,
html[data-theme="dark"] .rank-me__lab,
html[data-theme="dark"] .rank-me__hint,
html[data-theme="dark"] .rank-insight__k,
html[data-theme="dark"] .rank-panel__label,
html[data-theme="dark"] .rank-mini__k,
html[data-theme="dark"] .rank-foot,
html[data-theme="dark"] .rank-progress-box__sub,
html[data-theme="dark"] .rank-chart__sub,
html[data-theme="dark"] .rank-serverpro__sub{
  color: rgba(226,232,240,.72) !important;
}
html[data-theme="dark"] .rank-filter__status{
  color: rgba(255,255,255,.92);
}
html[data-theme="dark"] .rank-filter__label{
  color: rgba(226,232,240,.88);
}
html[data-theme="dark"] .rank-hero-stat,
html[data-theme="dark"] .rank-me__kpi,
html[data-theme="dark"] .rank-insight,
html[data-theme="dark"] .rank-mini,
html[data-theme="dark"] .rank-panel__item,
html[data-theme="dark"] .rank-xp-box,
html[data-theme="dark"] .rank-xp-mult,
html[data-theme="dark"] .rank-podium-card,
html[data-theme="dark"] .rank-progress-box{
  background: rgba(148,163,184,.06);
  border-color: rgba(148,163,184,.18);
}
html[data-theme="dark"] .rank-chart__bar{
  background: rgba(148,163,184,.16);
}
html[data-theme="dark"] .rank-chart__row.is-me{
  background: rgba(99,102,241,.10);
}
html[data-theme="dark"] .rank-podium-card__name,
html[data-theme="dark"] .rank-podium-card__xp,
html[data-theme="dark"] .rank-hero-stat__value,
html[data-theme="dark"] .rank-me__val,
html[data-theme="dark"] .rank-insight__v,
html[data-theme="dark"] .rank-panel__value,
html[data-theme="dark"] .rank-empty__title,
html[data-theme="dark"] .rank-xp-mult__v,
html[data-theme="dark"] .rank-progress-box__title,
html[data-theme="dark"] .rank-progress-box__pct,
html[data-theme="dark"] .rank-chart__name,
html[data-theme="dark"] .rank-chart__value,
html[data-theme="dark"] .rank-table__text,
html[data-theme="dark"] .rank-table__metric,
html[data-theme="dark"] .rank-serverpro__name{
  color: rgba(248,250,252,.96) !important;
}
html[data-theme="dark"] .rank-table__metric--xp{
  color: #8ab4ff !important;
}
html[data-theme="dark"] .rank-podium-card__place,
html[data-theme="dark"] .rank-podium-card__meta,
html[data-theme="dark"] .rank-hero-stat__label,
html[data-theme="dark"] .rank-hero-stat__sub,
html[data-theme="dark"] .rank-xp-mult__k,
html[data-theme="dark"] .rank-xp-mult__s,
html[data-theme="dark"] .rank-xp-hint,
html[data-theme="dark"] .rank-progress__meta{
  color: rgba(226,232,240,.72);
}
html[data-theme="dark"] .rank-chip{
  background: rgba(15,23,42,.70);
  border-color: rgba(148,163,184,.18);
  color: rgba(226,232,240,.88);
  box-shadow: 0 10px 26px rgba(0,0,0,.35);
}

html[data-theme="dark"] .rank-table--pro thead th{
  background:
    linear-gradient(180deg, rgba(14,22,40,.96), rgba(24,34,55,.98));
  color: rgba(232,238,248,.92);
  border-bottom: none !important;
}
html[data-theme="dark"] .rank-table--pro tbody td{
  border-top-color: rgba(148,163,184,.10) !important;
}
html[data-theme="dark"] .rank-table--pro tbody tr:hover{
  background: rgba(59,130,246,.08);
}
html[data-theme="dark"] .rank-table--pro tbody tr.is-me{
  background: linear-gradient(90deg, rgba(59,130,246,.14), rgba(59,130,246,.06));
}
html[data-theme="dark"] .rank-pospro__badge{
  background: rgba(148,163,184,.08);
  border-color: rgba(148,163,184,.16);
  color: rgba(248,250,252,.94);
}
html[data-theme="dark"] .rank-pospro__badge--1{
  background: rgba(201,162,39,.16);
  border-color: rgba(201,162,39,.28);
  color: #f5dd8a;
}
html[data-theme="dark"] .rank-pospro__badge--2{
  background: rgba(148,163,184,.14);
  border-color: rgba(148,163,184,.22);
  color: #dbe4ef;
}
html[data-theme="dark"] .rank-pospro__badge--3{
  background: rgba(180,120,60,.14);
  border-color: rgba(180,120,60,.22);
  color: #e9ba93;
}

html[data-theme="dark"] .rank-hours-filter{
  border-color: rgba(148,163,184,.18);
  background: rgba(15,20,28,.58);
  box-shadow: 0 18px 55px rgba(0,0,0,.55);
  backdrop-filter: blur(10px);
}
html[data-theme="dark"] .rank-hours-input{
  background: rgba(14,19,28,.92) !important;
  border-color: rgba(255,255,255,.14) !important;
  color: rgba(231,237,246,.92) !important;
}
html[data-theme="dark"] .rank-hours-input::placeholder{
  color: rgba(231,237,246,.42);
}
html[data-theme="dark"] .rank-hours-ico{
  opacity: .75;
}
</style>
@endsection