@extends('layouts.app')

@section('content')
@php
  $is6 = auth()->check() && (int)(auth()->user()->nivel ?? 0) >= 6;

  $mesInt = (int)($mes ?? 0);
  $isGeral = ($mesInt === 0);

  $vsTxt = $isGeral ? 'vs ano anterior' : 'vs mês anterior';

  $prevBadge = $isGeral
    ? 'ano anterior: ' . ($inicioPrev->format('Y'))
    : 'mês anterior: ' . $inicioPrev->format('m/Y');

  $nf0 = fn($v) => number_format((float)($v ?? 0), 0, ',', '.');
  $nf1 = fn($v) => number_format((float)($v ?? 0), 1, ',', '.');
  $brl = fn($v) => 'R$ ' . number_format((float)($v ?? 0), 0, ',', '.');

  $kpis = [
    ['key'=>'relatorios','label'=>'Relatórios Aprovados','value'=>(int)($totais->relatorios ?? 0),'ico'=>'📄','fmt'=>'int'],
    ['key'=>'drogas','label'=>'Drogas (Qtd)','value'=>(int)($totais->drogas ?? 0),'ico'=>'🧪','fmt'=>'int'],
    ['key'=>'explosivos','label'=>'Explosivos (Qtd)','value'=>(int)($totais->explosivos ?? 0),'ico'=>'💣','fmt'=>'int'],
    ['key'=>'lockpicks','label'=>'Lockpicks (Qtd)','value'=>(int)($totais->lockpicks ?? 0),'ico'=>'🪛','fmt'=>'int'],
    ['key'=>'pistolas','label'=>'Armas (Pistolas)','value'=>(int)($totais->pistolas ?? 0),'ico'=>'🔫','fmt'=>'int'],
    ['key'=>'smg_fuzil','label'=>'Armas (SMG/Fuzil)','value'=>(int)($totais->smg_fuzil ?? 0),'ico'=>'⚔️','fmt'=>'int'],
    ['key'=>'municoes','label'=>'Munições','value'=>(int)($totais->municoes ?? 0),'ico'=>'💥','fmt'=>'int'],
    ['key'=>'dinheiro','label'=>'Dinheiro Marcado','value'=>(float)($totais->dinheiro ?? 0),'ico'=>'💰','fmt'=>'brl'],
    ['key'=>'multas','label'=>'Multas (Qtd)','value'=>(int)($totais->multas ?? 0),'ico'=>'🧾','fmt'=>'int'],
    ['key'=>'bopm','label'=>'BOPM (Qtd)','value'=>(int)($totais->bopm ?? 0),'ico'=>'📝','fmt'=>'int'],
  ];

  $fmtKpi = function($k) use ($nf0, $brl) {
    if (($k['fmt'] ?? 'int') === 'brl') return $brl($k['value']);
    return $nf0($k['value']);
  };

  $alerts = [];
  foreach ($kpis as $k) {
    $v = $variacoes->{$k['key']} ?? null;
    if (!$v) continue;

    $pct = (float)($v->pct ?? 0);
    $prevVal = 0;

    if (is_object($totaisPrev ?? null) && property_exists($totaisPrev, $k['key'])) {
      $prevVal = (float)($totaisPrev->{$k['key']} ?? 0);
    }

    $hasBase = ((float)($k['value'] ?? 0) > 0) || ($prevVal > 0);

    if ($hasBase && abs($pct) >= 25) {
      $dir = $pct > 0 ? 'up' : 'down';
      $arrow = $pct > 0 ? '↑' : '↓';
      $pctTxt = ($pct >= 0 ? '+' : '') . number_format($pct, 1, ',', '.') . '%';

      $alerts[] = [
        'label' => $k['label'],
        'tone'  => $dir,
        'arrow' => $arrow,
        'pct'   => $pctTxt,
      ];
    }
  }

  $diasComMovimento = max(1, (int)($porDia->count() ?? 0));
  $mediaRelatoriosDia = ((float)($totais->relatorios ?? 0)) / $diasComMovimento;

  $totalApreensoes = (float)($totais->drogas ?? 0)
    + (float)($totais->explosivos ?? 0)
    + (float)($totais->lockpicks ?? 0)
    + (float)($totais->pistolas ?? 0)
    + (float)($totais->smg_fuzil ?? 0)
    + (float)($totais->municoes ?? 0);

  $topDia = collect($porDia ?? [])->sortByDesc('total')->first();
  $topDiaLabel = $topDia ? \Carbon\Carbon::parse($topDia->dia)->format('d/m') : '—';
  $topDiaTotal = $topDia ? (int)$topDia->total : 0;

  $topUnidadeResumo = $is6 ? collect($porUnidade ?? [])->first() : null;
  $topUnidadeNome = $topUnidadeResumo->unidade ?? '—';
  $topUnidadeValor = (int)($topUnidadeResumo->relatorios ?? 0);

  $filtrosAtivos = collect([
    $fUnidade ?? '',
    $fTipo ?? '',
    $fAgente ?? '',
  ])->filter(fn($v) => (string)$v !== '')->count();

  $resumoCards = [
    [
      'label' => 'Produção média / dia',
      'value' => $nf1($mediaRelatoriosDia),
      'sub'   => 'Relatórios aprovados',
      'ico'   => '📈',
    ],
    [
      'label' => 'Volume de apreensões',
      'value' => $nf0($totalApreensoes),
      'sub'   => 'Itens operacionais',
      'ico'   => '🧰',
    ],
    [
      'label' => 'Pico do período',
      'value' => $topDiaLabel,
      'sub'   => $topDiaTotal > 0 ? $nf0($topDiaTotal) . ' relatórios' : 'Sem pico relevante',
      'ico'   => '📅',
    ],
    [
      'label' => 'Filtros avançados',
      'value' => $filtrosAtivos > 0 ? $filtrosAtivos : '0',
      'sub'   => $filtrosAtivos > 0 ? 'Filtros em uso' : 'Visão ampla do painel',
      'ico'   => '🎯',
    ],
  ];

  $filtroResumo = [
    [
      'label' => 'Período ativo',
      'value' => $isGeral ? 'Anual' : $inicio->translatedFormat('F/Y'),
      'sub'   => $inicio->format('d/m/Y') . ' até ' . $fim->format('d/m/Y'),
      'ico'   => '🗓️',
    ],
    [
      'label' => 'Comparativo',
      'value' => $isGeral ? 'Ano anterior' : 'Mês anterior',
      'sub'   => $prevBadge,
      'ico'   => '🔁',
    ],
    [
      'label' => 'Relatórios',
      'value' => $nf0($totais->relatorios ?? 0),
      'sub'   => 'Total aprovado no recorte',
      'ico'   => '📄',
    ],
    [
      'label' => 'Média por dia',
      'value' => $nf1($mediaRelatoriosDia),
      'sub'   => 'Com base em dias com movimento',
      'ico'   => '⚡',
    ],
    [
      'label' => 'Filtros ativos',
      'value' => (string)$filtrosAtivos,
      'sub'   => $filtrosAtivos > 0 ? 'Recorte refinado' : 'Sem restrições adicionais',
      'ico'   => '🎛️',
    ],
    [
      'label' => 'Destaque',
      'value' => $is6 ? $topUnidadeNome : 'Visão operacional',
      'sub'   => $is6
        ? ($topUnidadeValor > 0 ? $nf0($topUnidadeValor).' relatórios' : 'Sem unidade líder')
        : 'Leitura resumida do período',
      'ico'   => '🏆',
    ],
  ];
@endphp

<div class="dash-wrap">

  {{-- HERO --}}
  <section class="dash-hero dash-hero--rank">
    <div class="dash-hero__bg dash-hero__bg--rank"></div>

    <div class="dash-hero__content">
      <div class="dash-hero__left">
        <div class="dash-kicker">GRR • PRF — Painel Operacional</div>
        <h1 class="dash-title">Inteligência Operacional</h1>

        <div class="dash-sub">
          Período: <b>{{ $inicio->format('d/m/Y') }}</b> a <b>{{ $fim->format('d/m/Y') }}</b>
          <span class="dash-dot"></span>
          Apenas relatórios aprovados
        </div>

        <div class="dash-badges">
          <span class="dash-badge">
            <span class="dash-badge__dot"></span>
            status: consolidado
          </span>
          <span class="dash-badge dash-badge--soft">{{ $prevBadge }}</span>
          <span class="dash-badge dash-badge--soft">visão: {{ $is6 ? 'comando' : 'operacional' }}</span>
          <span class="dash-badge dash-badge--soft">{{ $isGeral ? 'escopo: anual' : 'escopo: mensal' }}</span>
        </div>

        <div class="dash-highlights">
          @foreach(array_slice($resumoCards, 0, 4) as $rc)
            <div class="dash-highlight">
              <div class="dash-highlight__ico">{{ $rc['ico'] }}</div>
              <div class="dash-highlight__meta">
                <div class="dash-highlight__label">{{ $rc['label'] }}</div>
                <div class="dash-highlight__value">{{ $rc['value'] }}</div>
                <div class="dash-highlight__sub">{{ $rc['sub'] }}</div>
              </div>
            </div>
          @endforeach
        </div>

        @if(!empty($alerts))
          <div class="dash-alertspro">
            <div class="dash-alertspro__head">
              <div class="dash-alertspro__title">Alertas do período</div>
              <div class="dash-alertspro__sub">Variações relevantes {{ $vsTxt }} (≥ 25%)</div>
            </div>

            <div class="dash-alertspro__grid">
              @foreach(array_slice($alerts, 0, 4) as $al)
                <div class="dash-alertpro dash-alertpro--{{ $al['tone'] }}">
                  <div class="dash-alertpro__stripe"></div>

                  <div class="dash-alertpro__icon" aria-hidden="true">
                    @if($al['tone'] === 'up') ▲ @else ▼ @endif
                  </div>

                  <div class="dash-alertpro__body">
                    <div class="dash-alertpro__label">{{ $al['label'] }}</div>
                    <div class="dash-alertpro__meta">
                      <span class="dash-alertpro__pill">
                        <span class="dash-alertpro__arr">{{ $al['arrow'] }}</span>
                        {{ $al['pct'] }}
                      </span>
                      <span class="dash-alertpro__muted">{{ $vsTxt }}</span>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            @if(count($alerts) > 4)
              <div class="dash-alertspro__more">
                +{{ count($alerts) - 4 }} outros alertas disponíveis
              </div>
            @endif
          </div>
        @endif
      </div>

      <div class="dash-hero__right">
        <form class="dash-filter dash-filter--rank" method="GET" action="{{ route('dashboard') }}">
          <div class="dash-filter__top">
            <div>
              <div class="dash-filter__title">Filtro operacional</div>
              <div class="dash-filter__subline">
                Ajuste período, unidade, ocorrência e agente para refinar KPIs, gráficos e tabelas.
              </div>
            </div>

            @if($filtrosAtivos > 0)
              <span class="dash-filter__badge">{{ $filtrosAtivos }} filtro{{ $filtrosAtivos > 1 ? 's' : '' }} ativo{{ $filtrosAtivos > 1 ? 's' : '' }}</span>
            @else
              <span class="dash-filter__badge dash-filter__badge--soft">visão geral</span>
            @endif
          </div>

          <div class="dash-filter__main">
            <div class="dash-filter__field">
              <div class="dash-filter__label">Mês</div>
              <select class="form-select form-select-sm dash-select" name="mes">
                @for($m=1;$m<=12;$m++)
                  <option value="{{ $m }}" @selected($m==(int)$mes)>{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
                @endfor
                <option value="0" @selected(((int)$mes)===0)>Geral (Ano)</option>
              </select>
            </div>

            <div class="dash-filter__field">
              <div class="dash-filter__label">Ano</div>
              <select class="form-select form-select-sm dash-select" name="ano">
                @for($a=now()->year;$a>=now()->year-3;$a--)
                  <option value="{{ $a }}" @selected($a==(int)$ano)>{{ $a }}</option>
                @endfor
              </select>
            </div>

            <button class="btn btn-sm btn-primary dash-filter__btn" type="submit">
              Aplicar filtro
            </button>
          </div>

          <div class="dash-filter__advanced">
            <div class="dash-filter__field">
              <div class="dash-filter__label">Unidade</div>
              <select class="form-select form-select-sm dash-select" name="unidade">
                <option value="">Todas</option>
                @foreach($unidades as $u)
                  <option value="{{ $u }}" @selected(($fUnidade ?? '') == $u)>{{ $u }}</option>
                @endforeach
              </select>
            </div>

            <div class="dash-filter__field">
              <div class="dash-filter__label">Tipo de ocorrência</div>
              <select class="form-select form-select-sm dash-select" name="tipo">
                <option value="">Todos</option>
                @foreach($tipos as $t)
                  <option value="{{ $t }}" @selected(($fTipo ?? '') == $t)>{{ $t }}</option>
                @endforeach
              </select>
            </div>

            <div class="dash-filter__field">
              <div class="dash-filter__label">Agente</div>
              <select class="form-select form-select-sm dash-select" name="agente">
                <option value="">Todos</option>
                @foreach($agentes as $a)
                  <option value="{{ $a->id }}" @selected((string)($fAgente ?? '') === (string)$a->id)>{{ $a->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="dash-filter__insights">
            <div class="dash-filter__insightsHead">
              <div class="dash-filter__insightsTitle">Leitura rápida do recorte</div>
              <div class="dash-filter__insightsSub">Resumo automático do filtro atual</div>
            </div>

            <div class="dash-filter__insightsGrid">
              @foreach($filtroResumo as $fr)
                <div class="dash-filterStat">
                  <div class="dash-filterStat__ico">{{ $fr['ico'] }}</div>
                  <div class="dash-filterStat__meta">
                    <div class="dash-filterStat__label">{{ $fr['label'] }}</div>
                    <div class="dash-filterStat__value">{{ $fr['value'] }}</div>
                    <div class="dash-filterStat__sub">{{ $fr['sub'] }}</div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <div class="dash-filter__footer">
            <div class="dash-filter__hint">
              Apenas relatórios aprovados entram no painel.
            </div>

            <div class="dash-filter__actions">
              <a class="btn btn-sm btn-outline-secondary dash-filter__btn2"
                 href="{{ route('dashboard', ['mes'=>$mes,'ano'=>$ano]) }}">
                Limpar filtros avançados
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>

  {{-- KPIs --}}
  <section class="dash-kpis">
    @foreach($kpis as $k)
      @php
        $v = $variacoes->{$k['key']} ?? (object)['diff'=>0,'pct'=>0,'dir'=>'flat'];
        $cls = $v->dir === 'up' ? 'is-up' : ($v->dir === 'down' ? 'is-down' : 'is-flat');
        $arrow = $v->dir === 'up' ? '↑' : ($v->dir === 'down' ? '↓' : '→');

        $diffTxt = (($v->diff ?? 0) >= 0 ? '+' : '') . number_format((float)($v->diff ?? 0), 0, ',', '.');
        $pctTxt  = (($v->pct ?? 0) >= 0 ? '+' : '') . number_format((float)($v->pct ?? 0), 1, ',', '.') . '%';

        $valueTxt = $fmtKpi($k);
        $zeroNice = ((float)($k['value'] ?? 0) === 0.0) ? '—' : $valueTxt;
      @endphp

      <article class="dash-kpi {{ $cls }}" title="Comparativo {{ $vsTxt }}">
        <div class="dash-kpi__top">
          <div class="dash-kpi__ico">{{ $k['ico'] }}</div>
          <div class="dash-kpi__meta">
            <div class="dash-kpi__label">{{ $k['label'] }}</div>
            <div class="dash-kpi__value" title="{{ $valueTxt }}">{{ $zeroNice }}</div>
          </div>
        </div>

        <div class="dash-kpi__foot">
          <span class="dash-kpi__mm">
            <span class="dash-kpi__arr">{{ $arrow }}</span>
            {{ $diffTxt }} <span class="dash-kpi__pct">({{ $pctTxt }})</span>
            <span class="dash-kpi__mmk">{{ $vsTxt }}</span>
          </span>
        </div>
      </article>
    @endforeach
  </section>

  {{-- GRID PRINCIPAL --}}
  <section class="dash-grid-3">
    <article class="dash-card dash-card--wide">
      <div class="dash-card__head">
        <div>
          <div class="dash-card__title">Relatórios por dia</div>
          <div class="dash-card__sub">Contagem diária de relatórios aprovados</div>
        </div>
        <div class="dash-card__pill">Linha</div>
      </div>

      <div class="dash-chart dash-chart--line">
        <canvas id="chartRelatoriosDia"></canvas>
      </div>

      <div class="dash-note">
        Dica: {{ $isGeral ? 'no Geral, a escala cobre o ano inteiro.' : 'a linha mostra o ritmo diário do mês filtrado.' }}
      </div>
    </article>

    <article class="dash-card">
      <div class="dash-card__head">
        <div>
          <div class="dash-card__title">{{ $isGeral ? 'Distribuição do ano' : 'Distribuição do mês' }}</div>
          <div class="dash-card__sub">Peso relativo por indicador</div>
        </div>
        <div class="dash-card__pill">Pizza</div>
      </div>

      <div class="dash-chart dash-chart--donut">
        <canvas id="chartDistribuicao"></canvas>
      </div>

      <div class="dash-note">
        Compare rapidamente o foco operacional entre apreensões, multas, BOPM e valores.
      </div>
    </article>

    @if($is6)
      <article class="dash-card">
        <div class="dash-card__head">
          <div>
            <div class="dash-card__title">Top Unidades</div>
            <div class="dash-card__sub">Relatórios aprovados (Top 6)</div>
          </div>
          <div class="dash-card__pill">Barras</div>
        </div>

        <div class="dash-chart dash-chart--bar">
          <canvas id="chartTopUnidades"></canvas>
        </div>

        <div class="dash-note">
          Ranking por volume de relatórios aprovados no período filtrado.
        </div>
      </article>

      <article class="dash-card dash-card--wide">
        <div class="dash-card__head">
          <div>
            <div class="dash-card__title">Resumo estratégico do período</div>
            <div class="dash-card__sub">Leitura rápida de produtividade, comando e tendência operacional</div>
          </div>
          <div class="dash-card__pill">Insights</div>
        </div>

        <div class="dash-summary">
          <div class="dash-summary__grid">
            <div class="dash-summary__item">
              <div class="dash-summary__label">Unidade líder</div>
              <div class="dash-summary__value">{{ $topUnidadeNome }}</div>
              <div class="dash-summary__sub">{{ $topUnidadeValor > 0 ? $nf0($topUnidadeValor).' relatórios aprovados' : 'Sem destaque no período' }}</div>
            </div>

            <div class="dash-summary__item">
              <div class="dash-summary__label">Dia mais forte</div>
              <div class="dash-summary__value">{{ $topDiaLabel }}</div>
              <div class="dash-summary__sub">{{ $topDiaTotal > 0 ? $nf0($topDiaTotal).' relatórios no pico' : 'Sem movimentação relevante' }}</div>
            </div>

            <div class="dash-summary__item">
              <div class="dash-summary__label">Dinheiro marcado</div>
              <div class="dash-summary__value">{{ $brl($totais->dinheiro ?? 0) }}</div>
              <div class="dash-summary__sub">Total consolidado no período</div>
            </div>

            <div class="dash-summary__item">
              <div class="dash-summary__label">Eficiência média</div>
              <div class="dash-summary__value">{{ $nf1($mediaRelatoriosDia) }}</div>
              <div class="dash-summary__sub">Relatórios aprovados por dia com movimento</div>
            </div>
          </div>

          <div class="dash-summary__aside">
            <div class="dash-summary__panel">
              <div class="dash-summary__panelTitle">Leitura do comando</div>
              <p class="dash-summary__panelText">
                O período apresenta <b>{{ $nf0($totais->relatorios ?? 0) }}</b> relatórios aprovados, com
                <b>{{ $nf0($totalApreensoes) }}</b> registros operacionais consolidados e
                <b>{{ $brl($totais->dinheiro ?? 0) }}</b> em dinheiro marcado.
              </p>
              <p class="dash-summary__panelText">
                @if($topUnidadeValor > 0)
                  A unidade de maior destaque foi <b>{{ $topUnidadeNome }}</b>, liderando o volume do período.
                @else
                  Não houve unidade com destaque estatístico relevante neste recorte.
                @endif
              </p>
            </div>
          </div>
        </div>
      </article>
    @else
      <article class="dash-card dash-card--locked">
        <div class="dash-card__head">
          <div>
            <div class="dash-card__title">Leitura rápida</div>
            <div class="dash-card__sub">Resumo executivo do período</div>
          </div>
          <div class="dash-card__pill dash-card__pill--ok">OK</div>
        </div>

        <div class="dash-locked">
          <div class="dash-locked__blur" aria-hidden="true">
            <div class="dash-insights">
              <div class="dash-insight">
                <div class="dash-insight__k">Operação</div>
                <div class="dash-insight__v">Produção consolidada</div>
              </div>
              <div class="dash-insight">
                <div class="dash-insight__k">Qualidade</div>
                <div class="dash-insight__v">Somente aprovados</div>
              </div>
              <div class="dash-insight">
                <div class="dash-insight__k">Foco</div>
                <div class="dash-insight__v">Rodovias + Apreensões</div>
              </div>
            </div>
          </div>

          <div class="dash-locked__overlay">
            <div class="dash-locked__badge">Acesso restrito</div>
            <div class="dash-locked__title">Apenas nível 6+ tem acesso</div>
            <div class="dash-locked__sub">Painel estratégico disponível somente para perfis com autorização de comando.</div>
          </div>
        </div>
      </article>

      <article class="dash-card dash-card--wide">
        <div class="dash-card__head">
          <div>
            <div class="dash-card__title">Resumo operacional</div>
            <div class="dash-card__sub">Leitura simplificada do período filtrado</div>
          </div>
          <div class="dash-card__pill">Resumo</div>
        </div>

        <div class="dash-summary-lite">
          @foreach($resumoCards as $rc)
            <div class="dash-summary-lite__item">
              <div class="dash-summary-lite__ico">{{ $rc['ico'] }}</div>
              <div class="dash-summary-lite__label">{{ $rc['label'] }}</div>
              <div class="dash-summary-lite__value">{{ $rc['value'] }}</div>
              <div class="dash-summary-lite__sub">{{ $rc['sub'] }}</div>
            </div>
          @endforeach
        </div>
      </article>
    @endif
  </section>

  {{-- TABELA POR UNIDADE --}}
  @if($is6)
    <section class="dash-card mt-3">
      <div class="dash-card__head">
        <div>
          <div class="dash-card__title">Resumo por Unidade</div>
          <div class="dash-card__sub">Comparativo do período por produção</div>
        </div>
        <div class="dash-card__pill">Controle</div>
      </div>

      <div class="table-responsive dash-table-wrap">
        <table class="table table-sm align-middle dash-table">
          <thead>
            <tr>
              <th class="dash-th-unit">Unidade</th>
              <th class="text-end">Relatórios</th>
              <th class="text-end">Drogas</th>
              <th class="text-end">Explosivos</th>
              <th class="text-end">Lockpicks</th>
              <th class="text-end">Pistolas</th>
              <th class="text-end">SMG/Fuzil</th>
              <th class="text-end">Multas</th>
              <th class="text-end">BOPM</th>
            </tr>
          </thead>
          <tbody>
            @forelse($porUnidade as $u)
              <tr>
                <td class="dash-table__unit">{{ $u->unidade }}</td>
                <td class="text-end fw-semibold">{{ $nf0($u->relatorios) }}</td>
                <td class="text-end">{{ $nf0($u->drogas) }}</td>
                <td class="text-end">{{ $nf0($u->explosivos ?? 0) }}</td>
                <td class="text-end">{{ $nf0($u->lockpicks ?? 0) }}</td>
                <td class="text-end">{{ $nf0($u->pistolas) }}</td>
                <td class="text-end">{{ $nf0($u->smg_fuzil) }}</td>
                <td class="text-end">{{ $nf0($u->multas) }}</td>
                <td class="text-end">{{ $nf0($u->bopm) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-muted">Sem dados no período.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
  function grrDestroyChart(canvasId){
    const el = document.getElementById(canvasId);
    if (!el) return null;

    const existing = Chart.getChart(el);
    if (existing) existing.destroy();

    return el;
  }

  function grrBaseGridColor(){
    return 'rgba(148, 163, 184, 0.12)';
  }

  function grrBaseTickColor(){
    return 'rgba(148, 163, 184, 0.78)';
  }

  function grrInitLineChart(){
    const el = grrDestroyChart('chartRelatoriosDia');
    if (!el) return;

    new Chart(el, {
      type: 'line',
      data: {
        labels: @json(
          $porDia->pluck('dia')->map(function($d) use ($isGeral) {
            $c = \Carbon\Carbon::parse($d);
            if ($isGeral) {
              $m = mb_strtolower($c->locale('pt_BR')->translatedFormat('M'));
              return $c->format('d') . ' ' . $m;
            }
            return $c->format('d/m');
          })->values()
        ),
        datasets: [{
          label: 'Relatórios',
          data: @json($porDia->pluck('total')->values()).map(v => Number(v) || 0),
          tension: 0.35,
          pointRadius: 3,
          pointHoverRadius: 5,
          borderWidth: 3,
          fill: true,
          backgroundColor: 'rgba(59, 130, 246, 0.18)',
          borderColor: 'rgba(59, 130, 246, 1)',
          pointBackgroundColor: 'rgba(59, 130, 246, 1)'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              color: grrBaseTickColor(),
              maxRotation: 0,
              autoSkip: true,
              maxTicksLimit: @json($isGeral) ? 14 : 31
            }
          },
          y: {
            beginAtZero: true,
            ticks: {
              color: grrBaseTickColor(),
              precision: 0
            },
            grid: {
              color: grrBaseGridColor()
            }
          }
        }
      }
    });
  }

  function grrInitDonutChart(){
    const el = grrDestroyChart('chartDistribuicao');
    if (!el) return;

    const distLabels = @json($distLabels);
    const distReal = @json($distData).map(v => Number(v) || 0);
    const distDraw = distReal.map(v => v > 0 ? Math.log1p(v) : 0);
    const hasAny = distReal.some(v => v > 0);
    const distDrawSafe = hasAny ? distDraw : distReal.map(() => 1);

    new Chart(el, {
      type: 'doughnut',
      data: {
        labels: distLabels,
        datasets: [{
          data: distDrawSafe,
          borderWidth: 2,
          hoverOffset: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '64%',
        animation: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: grrBaseTickColor(),
              boxWidth: 12,
              boxHeight: 12,
              padding: 14
            }
          },
          tooltip: {
            padding: 12,
            callbacks: {
              label: (ctx) => {
                const i = ctx.dataIndex;
                const val = distReal[i] ?? 0;
                const total = distReal.reduce((a,b) => a + (b ?? 0), 0) || 1;
                const pct = (val / total) * 100;

                const nome = ctx.label ?? '';
                const valFmt = new Intl.NumberFormat('pt-BR').format(val);
                const pctFmt = pct.toLocaleString('pt-BR', { maximumFractionDigits: 1 });

                return `${nome}: ${valFmt} (${pctFmt}%)`;
              }
            }
          }
        }
      }
    });
  }

  @if($is6)
  function grrInitTopUnidades(){
    const el = grrDestroyChart('chartTopUnidades');
    if (!el) return;

    new Chart(el, {
      type: 'bar',
      data: {
        labels: @json($topUnidades->pluck('unidade')->values()),
        datasets: [{
          data: @json($topUnidades->pluck('relatorios')->values()).map(v => Number(v) || 0),
          borderWidth: 1,
          borderRadius: 10,
          maxBarThickness: 52,
          backgroundColor: 'rgba(59, 130, 246, 0.30)',
          borderColor: 'rgba(56, 189, 248, 0.95)'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              color: grrBaseTickColor()
            }
          },
          y: {
            beginAtZero: true,
            ticks: {
              color: grrBaseTickColor(),
              precision: 0
            },
            grid: {
              color: grrBaseGridColor()
            }
          }
        }
      }
    });
  }
  @endif

  function grrInitDashboardCharts(){
    grrInitLineChart();
    grrInitDonutChart();
    @if($is6)
      grrInitTopUnidades();
    @endif
  }

  document.addEventListener('DOMContentLoaded', grrInitDashboardCharts);
  document.addEventListener('turbo:load', grrInitDashboardCharts);
</script>

<style>
  :root{
    --dash-radius-xl: 24px;
    --dash-radius-lg: 18px;
    --dash-radius-md: 14px;
    --dash-blur: 12px;
  }

  .dash-wrap{
    display: flex;
    flex-direction: column;
    gap: 18px;
  }

  .dash-hero--rank{
    position: relative;
    overflow: hidden;
    border-radius: var(--dash-radius-xl);
    border: 1px solid var(--border);
    background: var(--surface);
    box-shadow: var(--shadow);
  }

  .dash-hero__bg--rank{
    position:absolute;
    inset:0;
    pointer-events:none;
    background:
      radial-gradient(1000px 520px at 20% 20%, rgba(90,162,255,.22), transparent 60%),
      radial-gradient(900px 520px at 85% 25%, rgba(16,185,129,.14), transparent 55%),
      radial-gradient(800px 520px at 50% 115%, rgba(0,0,0,.55), transparent 60%),
      linear-gradient(180deg, rgba(8,13,20,.30), rgba(8,13,20,.92));
    filter: saturate(1.08);
  }

  .dash-hero__content{
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: minmax(0, 1.16fr) minmax(360px, .84fr);
    gap: 22px;
    padding: 24px;
    align-items: stretch;
  }

  .dash-hero__left{
    position: relative;
    min-width: 0;
  }

  .dash-hero__left::before{
    content:"";
    position:absolute;
    inset:-10px;
    border-radius: 18px;
    pointer-events:none;
    opacity: 0;
  }

  .dash-hero__left > *{
    position: relative;
    z-index: 1;
  }

  .dash-kicker{
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .18em;
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  .dash-title{
    margin: 0;
    font-size: clamp(28px, 4vw, 42px);
    line-height: 1.02;
    font-weight: 1000;
    letter-spacing: -.03em;
  }

  .dash-sub{
    margin-top: 10px;
    font-size: 14px;
    line-height: 1.5;
  }

  .dash-dot{
    display:inline-block;
    width: 6px;
    height: 6px;
    margin: 0 10px;
    border-radius: 999px;
    background: currentColor;
    opacity: .45;
    vertical-align: middle;
  }

  .dash-badges{
    display:flex;
    flex-wrap:wrap;
    gap: 10px;
    margin-top: 16px;
  }

  .dash-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    min-height: 36px;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .02em;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.08);
    backdrop-filter: blur(8px);
  }

  .dash-badge--soft{
    opacity: .92;
  }

  .dash-badge__dot{
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #10b981;
    box-shadow: 0 0 0 4px rgba(16,185,129,.18);
  }

  .dash-highlights{
    display:grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-top: 18px;
  }

  .dash-highlight{
    display:flex;
    gap: 12px;
    align-items:flex-start;
    padding: 14px;
    border-radius: 18px;
    border: 1px solid rgba(255,255,255,.10);
    background: rgba(255,255,255,.06);
    backdrop-filter: blur(10px);
  }

  .dash-highlight__ico{
    width: 40px;
    height: 40px;
    border-radius: 14px;
    display:grid;
    place-items:center;
    font-size: 18px;
    flex: 0 0 auto;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.08);
  }

  .dash-highlight__label{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
    opacity: .82;
  }

  .dash-highlight__value{
    margin-top: 5px;
    font-size: 22px;
    line-height: 1;
    font-weight: 1000;
    letter-spacing: -.03em;
  }

  .dash-highlight__sub{
    margin-top: 6px;
    font-size: 12px;
    opacity: .72;
    font-weight: 600;
  }

  html[data-theme="dark"] .dash-hero__bg--rank{
    background:
      radial-gradient(1000px 520px at 18% 18%, rgba(59,130,246,.26), transparent 60%),
      radial-gradient(900px 520px at 85% 25%, rgba(16,185,129,.16), transparent 55%),
      radial-gradient(800px 520px at 50% 115%, rgba(0,0,0,.55), transparent 60%),
      linear-gradient(180deg, rgba(5,10,18,.90), rgba(8,14,26,.96));
    filter: saturate(1.06);
  }

  html[data-theme="dark"] .dash-hero__left::before{
    opacity: 1;
    background: rgba(3,7,18,.55);
    border: 1px solid rgba(255,255,255,.10);
    backdrop-filter: blur(var(--dash-blur));
    box-shadow: 0 18px 55px rgba(0,0,0,.45);
  }

  html[data-theme="dark"] .dash-kicker{
    color: rgba(231,237,246,.82) !important;
  }

  html[data-theme="dark"] .dash-title{
    color: rgba(255,255,255,.96) !important;
    text-shadow: 0 1px 0 rgba(0,0,0,.55);
  }

  html[data-theme="dark"] .dash-sub{
    color: rgba(231,237,246,.78) !important;
  }

  html[data-theme="dark"] .dash-badge,
  html[data-theme="dark"] .dash-highlight{
    border-color: rgba(255,255,255,.16) !important;
    background: rgba(15,20,28,.55) !important;
    color: rgba(255,255,255,.90) !important;
    box-shadow: 0 10px 26px rgba(0,0,0,.22);
  }

  html[data-theme="light"] .dash-hero__bg--rank{
    background:
      radial-gradient(900px 480px at 15% 10%, rgba(13,110,253,.22), transparent 62%),
      radial-gradient(900px 480px at 80% 15%, rgba(16,185,129,.14), transparent 60%),
      linear-gradient(180deg, rgba(255,255,255,.78), rgba(255,255,255,.95));
    filter: none;
  }

  .dash-filter--rank{
    display: flex;
    flex-direction: column;
    gap: 14px;
    height: 100%;
    padding: 18px;
    border-radius: 22px;
    border: 1px solid rgba(255,255,255,.10);
    background: rgba(15,20,28,.68);
    backdrop-filter: blur(10px);
    box-shadow: 0 18px 55px rgba(0,0,0,.55);
  }

  .dash-filter__top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap: 12px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,.08);
  }

  .dash-filter__title{
    font-size: 16px;
    font-weight: 1000;
    letter-spacing: -.01em;
  }

  .dash-filter__subline{
    margin-top: 4px;
    font-size: 12px;
    line-height: 1.45;
    color: rgba(231,237,246,.68);
    font-weight: 600;
  }

  .dash-filter__badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height: 30px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
    border: 1px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.08);
    white-space: nowrap;
  }

  .dash-filter__badge--soft{
    opacity: .88;
  }

  .dash-filter__main,
  .dash-filter__advanced{
    display:grid;
    gap: 12px;
  }

  .dash-filter__main{
    grid-template-columns: 1fr 1fr auto;
    align-items:end;
  }

  .dash-filter__advanced{
    grid-template-columns: repeat(3, 1fr);
  }

  .dash-filter__field{
    min-width: 0;
  }

  .dash-filter__label{
    margin-bottom: 6px;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .04em;
    text-transform: uppercase;
  }

  .dash-filter--rank .dash-filter__label{
    color: rgba(231,237,246,.82);
  }

  .dash-select,
  .dash-filter--rank .form-control{
    min-height: 44px;
    border-radius: 14px;
  }

  .dash-filter--rank .form-select,
  .dash-filter--rank .form-control{
    background: rgba(12,16,24,.92) !important;
    border-color: rgba(255,255,255,.14) !important;
    color: rgba(231,237,246,.92) !important;
    box-shadow: none !important;
  }

  .dash-filter--rank .form-select:focus,
  .dash-filter--rank .form-control:focus{
    border-color: rgba(96,165,250,.70) !important;
    box-shadow: 0 0 0 4px rgba(59,130,246,.14) !important;
  }

  .dash-filter__btn,
  .dash-filter__btn2{
    min-height: 44px;
    border-radius: 14px;
    font-weight: 800;
  }

  .dash-filter__insights{
    margin-top: 2px;
    padding: 14px;
    border-radius: 18px;
    border: 1px solid rgba(255,255,255,.08);
    background: linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
  }

  .dash-filter__insightsHead{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap: 12px;
    margin-bottom: 12px;
  }

  .dash-filter__insightsTitle{
    font-size: 13px;
    font-weight: 1000;
    letter-spacing: .02em;
  }

  .dash-filter__insightsSub{
    font-size: 11px;
    font-weight: 700;
    color: rgba(231,237,246,.60);
  }

  .dash-filter__insightsGrid{
    display:grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }

  .dash-filterStat{
    display:flex;
    gap: 12px;
    align-items:flex-start;
    padding: 12px;
    border-radius: 16px;
    border: 1px solid rgba(255,255,255,.08);
    background: rgba(255,255,255,.04);
  }

  .dash-filterStat__ico{
    width: 34px;
    height: 34px;
    border-radius: 12px;
    display:grid;
    place-items:center;
    font-size: 16px;
    flex: 0 0 auto;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.08);
  }

  .dash-filterStat__label{
    font-size: 10px;
    font-weight: 900;
    letter-spacing: .10em;
    text-transform: uppercase;
    color: rgba(231,237,246,.66);
  }

  .dash-filterStat__value{
    margin-top: 4px;
    font-size: 17px;
    line-height: 1.1;
    font-weight: 1000;
    color: rgba(255,255,255,.96);
  }

  .dash-filterStat__sub{
    margin-top: 4px;
    font-size: 11px;
    line-height: 1.4;
    color: rgba(231,237,246,.60);
    font-weight: 600;
  }

  .dash-filter__footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap: 12px;
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid rgba(255,255,255,.08);
  }

  .dash-filter__hint{
    font-size: 12px;
    font-weight: 600;
    color: rgba(231,237,246,.70);
  }

  .dash-filter__actions{
    display:flex;
    justify-content:flex-end;
  }

  html[data-theme="light"] .dash-filter--rank{
    background: rgba(255,255,255,.78);
    border-color: rgba(2,6,23,.10);
    box-shadow: 0 16px 40px rgba(2,6,23,.10);
  }

  html[data-theme="light"] .dash-filter__subline,
  html[data-theme="light"] .dash-filter__hint{
    color: rgba(2,6,23,.58);
  }

  html[data-theme="light"] .dash-filter--rank .dash-filter__label{
    color: rgba(2,6,23,.70);
  }

  html[data-theme="light"] .dash-filter--rank .form-select,
  html[data-theme="light"] .dash-filter--rank .form-control{
    background: rgba(255,255,255,.92) !important;
    border-color: rgba(2,6,23,.14) !important;
    color: rgba(2,6,23,.92) !important;
  }

  html[data-theme="light"] .dash-filter__badge{
    border-color: rgba(2,6,23,.10);
    background: rgba(2,6,23,.04);
    color: rgba(2,6,23,.78);
  }

  html[data-theme="light"] .dash-filter__insights{
    border-color: rgba(2,6,23,.08);
    background: linear-gradient(180deg, rgba(2,6,23,.03), rgba(2,6,23,.01));
  }

  html[data-theme="light"] .dash-filter__insightsSub,
  html[data-theme="light"] .dash-filterStat__label,
  html[data-theme="light"] .dash-filterStat__sub{
    color: rgba(2,6,23,.56);
  }

  html[data-theme="light"] .dash-filterStat{
    border-color: rgba(2,6,23,.08);
    background: rgba(2,6,23,.03);
  }

  html[data-theme="light"] .dash-filterStat__ico{
    background: rgba(2,6,23,.04);
    border-color: rgba(2,6,23,.06);
  }

  html[data-theme="light"] .dash-filterStat__value{
    color: rgba(2,6,23,.92);
  }

  .dash-alertspro{
    margin-top: 16px;
    padding: 14px;
    border-radius: 18px;
    border: 1px solid rgba(2,6,23,.08);
    background: rgba(255,255,255,.55);
    backdrop-filter: blur(10px);
    box-shadow: 0 16px 50px rgba(2,6,23,.08);
  }

  .dash-alertspro__head{
    display:flex;
    align-items:flex-end;
    justify-content:space-between;
    gap: 10px;
    margin-bottom: 10px;
  }

  .dash-alertspro__title{
    font-size: 14px;
    font-weight: 1000;
    letter-spacing: .01em;
  }

  .dash-alertspro__sub,
  .dash-alertpro__muted,
  .dash-alertspro__more{
    font-size: 12px;
    font-weight: 700;
    color: rgba(2,6,23,.55);
  }

  .dash-alertspro__grid{
    display:grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }

  .dash-alertpro{
    position: relative;
    display:flex;
    gap: 12px;
    align-items:center;
    padding: 12px;
    border-radius: 16px;
    border: 1px solid rgba(2,6,23,.08);
    background: rgba(255,255,255,.65);
    box-shadow: 0 10px 24px rgba(2,6,23,.06);
    overflow:hidden;
  }

  .dash-alertpro__stripe{
    position:absolute;
    left:0;
    top:0;
    bottom:0;
    width: 5px;
    background: rgba(59,130,246,.6);
  }

  .dash-alertpro__icon{
    width: 36px;
    height: 36px;
    display:grid;
    place-items:center;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 1000;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(2,6,23,.03);
    flex: 0 0 auto;
  }

  .dash-alertpro__label{
    font-weight: 950;
    line-height: 1.15;
  }

  .dash-alertpro__meta{
    display:flex;
    align-items:center;
    gap: 10px;
    margin-top: 4px;
    flex-wrap: wrap;
  }

  .dash-alertpro__pill{
    display:inline-flex;
    align-items:center;
    gap: 8px;
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(2,6,23,.03);
    white-space: nowrap;
  }

  .dash-alertpro--up{
    border-color: rgba(16,185,129,.22);
    background: rgba(16,185,129,.08);
  }

  .dash-alertpro--up .dash-alertpro__stripe{ background: rgba(16,185,129,.75); }
  .dash-alertpro--up .dash-alertpro__icon{
    border-color: rgba(16,185,129,.25);
    background: rgba(16,185,129,.12);
  }
  .dash-alertpro--up .dash-alertpro__pill{
    border-color: rgba(16,185,129,.28);
    background: rgba(16,185,129,.12);
  }

  .dash-alertpro--down{
    border-color: rgba(239,68,68,.22);
    background: rgba(239,68,68,.08);
  }

  .dash-alertpro--down .dash-alertpro__stripe{ background: rgba(239,68,68,.75); }
  .dash-alertpro--down .dash-alertpro__icon{
    border-color: rgba(239,68,68,.25);
    background: rgba(239,68,68,.12);
  }
  .dash-alertpro--down .dash-alertpro__pill{
    border-color: rgba(239,68,68,.28);
    background: rgba(239,68,68,.12);
  }

  html[data-theme="dark"] .dash-alertspro{
    border-color: rgba(255,255,255,.10);
    background: rgba(15,20,28,.50);
    box-shadow: 0 18px 60px rgba(0,0,0,.60);
  }

  html[data-theme="dark"] .dash-alertpro{
    border-color: rgba(255,255,255,.10);
    background: rgba(15,20,28,.55);
    box-shadow: 0 12px 34px rgba(0,0,0,.55);
  }

  html[data-theme="dark"] .dash-alertpro__icon,
  html[data-theme="dark"] .dash-alertpro__pill{
    border-color: rgba(255,255,255,.12);
    background: rgba(255,255,255,.06);
    color: rgba(231,237,246,.92);
  }

  html[data-theme="dark"] .dash-alertspro__sub,
  html[data-theme="dark"] .dash-alertpro__muted,
  html[data-theme="dark"] .dash-alertspro__more{
    color: rgba(231,237,246,.62) !important;
  }

  .dash-kpis{
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
  }

  .dash-kpi{
    position: relative;
    min-height: 130px;
    padding: 16px;
    border-radius: 18px;
    border: 1px solid var(--border);
    background: linear-gradient(180deg, var(--surface), var(--surface2));
    box-shadow: var(--shadow);
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    overflow: hidden;
  }

  .dash-kpi:hover{
    transform: translateY(-3px);
    box-shadow: 0 18px 36px rgba(0,0,0,.12);
  }

  .dash-kpi::after{
    content:"";
    position:absolute;
    left:0;
    right:0;
    top:0;
    height: 4px;
    opacity: .95;
  }

  .dash-kpi.is-up::after{ background: linear-gradient(90deg, rgba(16,185,129,.85), rgba(52,211,153,.35)); }
  .dash-kpi.is-down::after{ background: linear-gradient(90deg, rgba(239,68,68,.85), rgba(248,113,113,.35)); }
  .dash-kpi.is-flat::after{ background: linear-gradient(90deg, rgba(59,130,246,.85), rgba(96,165,250,.35)); }

  .dash-kpi__top{
    display:flex;
    align-items:flex-start;
    gap: 12px;
  }

  .dash-kpi__ico{
    width: 44px;
    height: 44px;
    display:grid;
    place-items:center;
    border-radius: 14px;
    font-size: 20px;
    background: rgba(255,255,255,.06);
    border: 1px solid var(--border);
    flex: 0 0 auto;
  }

  .dash-kpi__meta{
    min-width: 0;
  }

  .dash-kpi__label{
    font-size: 12px;
    font-weight: 900;
    line-height: 1.3;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: var(--muted);
  }

  .dash-kpi__value{
    margin-top: 8px;
    font-size: clamp(22px, 2.5vw, 30px);
    line-height: 1;
    font-weight: 1000;
    letter-spacing: -.03em;
    color: var(--text);
  }

  .dash-kpi__foot{
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px dashed var(--border);
  }

  .dash-kpi__mm{
    font-size: 12px;
    font-weight: 800;
    color: var(--muted);
  }

  .dash-kpi__arr{
    font-weight: 1000;
    margin-right: 4px;
  }

  .dash-kpi__pct{
    opacity: .92;
  }

  .dash-kpi__mmk{
    display: inline-block;
    margin-left: 6px;
    opacity: .7;
  }

  .dash-grid-3{
    display:grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
  }

  .dash-card{
    border-radius: 20px;
    border: 1px solid var(--border);
    background: linear-gradient(180deg, var(--surface), var(--surface2));
    box-shadow: var(--shadow);
    padding: 18px;
    min-width: 0;
  }

  .dash-card--wide{
    grid-column: span 2;
  }

  .dash-card__head{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap: 14px;
    margin-bottom: 14px;
  }

  .dash-card__title{
    font-size: 16px;
    font-weight: 1000;
    letter-spacing: -.01em;
  }

  .dash-card__sub{
    margin-top: 4px;
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
  }

  .dash-card__pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height: 30px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .08em;
    border: 1px solid var(--border);
    background: var(--surface2);
    white-space: nowrap;
  }

  .dash-card__pill--ok{
    color: #10b981;
  }

  .dash-chart{
    position: relative;
    min-height: 310px;
    border-radius: 16px;
    padding: 10px;
    background:
      linear-gradient(180deg, rgba(59,130,246,.06), rgba(15,23,42,.02));
    border: 1px solid rgba(255,255,255,.06);
  }

  .dash-chart--donut{
    min-height: 320px;
  }

  .dash-note{
    margin-top: 12px;
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
  }

  .dash-summary{
    display:grid;
    grid-template-columns: minmax(0, 1.3fr) minmax(260px, .7fr);
    gap: 16px;
    align-items: stretch;
  }

  .dash-summary__grid{
    display:grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
  }

  .dash-summary__item{
    padding: 16px;
    border-radius: 16px;
    border: 1px solid var(--border);
    background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.01));
  }

  .dash-summary__label{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .10em;
    text-transform: uppercase;
    color: var(--muted);
  }

  .dash-summary__value{
    margin-top: 8px;
    font-size: 24px;
    line-height: 1;
    font-weight: 1000;
    letter-spacing: -.03em;
  }

  .dash-summary__sub{
    margin-top: 8px;
    font-size: 12px;
    line-height: 1.45;
    color: var(--muted);
    font-weight: 600;
  }

  .dash-summary__aside{
    min-width: 0;
  }

  .dash-summary__panel{
    height: 100%;
    padding: 18px;
    border-radius: 18px;
    border: 1px solid rgba(59,130,246,.18);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.14), transparent 34%),
      linear-gradient(180deg, rgba(15,23,42,.80), rgba(15,23,42,.45));
  }

  .dash-summary__panelTitle{
    font-size: 14px;
    font-weight: 1000;
    letter-spacing: .02em;
    margin-bottom: 10px;
  }

  .dash-summary__panelText{
    margin: 0 0 10px 0;
    font-size: 13px;
    line-height: 1.6;
    color: var(--muted);
  }

  .dash-summary-lite{
    display:grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;
  }

  .dash-summary-lite__item{
    padding: 16px;
    border-radius: 16px;
    border: 1px solid var(--border);
    background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.01));
  }

  .dash-summary-lite__ico{
    font-size: 20px;
    margin-bottom: 8px;
  }

  .dash-summary-lite__label{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--muted);
  }

  .dash-summary-lite__value{
    margin-top: 8px;
    font-size: 22px;
    line-height: 1;
    font-weight: 1000;
  }

  .dash-summary-lite__sub{
    margin-top: 8px;
    font-size: 12px;
    color: var(--muted);
    font-weight: 600;
    line-height: 1.45;
  }

  .dash-card--locked{
    position: relative;
  }

  .dash-locked{
    position: relative;
    min-height: 260px;
    border-radius: 14px;
  }

  .dash-locked__blur{
    filter: blur(6px);
    opacity: .55;
    transform: translateZ(0);
    pointer-events: none;
    user-select: none;
  }

  .dash-locked__overlay{
    position: absolute;
    inset: 0;
    display: grid;
    place-content: center;
    text-align: center;
    padding: 14px;
  }

  .dash-locked__badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width: fit-content;
    margin: 0 auto 10px auto;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(2,6,23,.03);
  }

  .dash-locked__title{
    font-size: 16px;
    font-weight: 1000;
    letter-spacing: .01em;
  }

  .dash-locked__sub{
    margin-top: 6px;
    font-size: 12px;
    font-weight: 700;
    color: rgba(2,6,23,.55);
  }

  html[data-theme="dark"] .dash-locked__badge{
    border-color: rgba(255,255,255,.12);
    background: rgba(255,255,255,.06);
    color: rgba(231,237,246,.92);
  }

  html[data-theme="dark"] .dash-locked__sub{
    color: rgba(231,237,246,.62) !important;
  }

  .dash-table-wrap{
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--border);
    background: var(--surface);
  }

  .dash-table{
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
  }

  .dash-table thead th{
    position: sticky;
    top: 0;
    z-index: 2;
    padding: 13px 12px;
    font-size: 11px;
    letter-spacing: .16em;
    text-transform: uppercase;
    font-weight: 950;
    color: var(--muted) !important;
    background: var(--surface2) !important;
    border-bottom: 1px solid var(--border) !important;
    white-space: nowrap;
  }

  .dash-th-unit{
    color: rgba(231,237,246,.92) !important;
  }

  html[data-theme="light"] .dash-th-unit{
    color: rgba(2,6,23,.82) !important;
  }

  html[data-theme="dark"] .dash-table thead th{
    background: rgba(15,20,28,.82) !important;
    border-bottom-color: rgba(255,255,255,.10) !important;
  }

  .dash-table tbody td{
    padding: 13px 12px;
    border-color: var(--border) !important;
    background: transparent;
  }

  .dash-table tbody tr:nth-child(odd){
    background: rgba(255,255,255,.02);
  }

  html[data-theme="light"] .dash-table tbody tr:nth-child(odd){
    background: rgba(2,6,23,.02);
  }

  .dash-table tbody tr:hover{
    background: var(--accentSoft);
  }

  .dash-table th:first-child,
  .dash-table td:first-child{
    width: 220px;
    max-width: 260px;
    white-space: nowrap;
  }

  .dash-table__unit{
    font-weight: 950;
    letter-spacing: .2px;
    color: var(--text) !important;
  }

  .dash-table__unit::before{
    content: "•";
    margin-right: 10px;
    color: rgba(90,162,255,.85);
  }

  html[data-theme="dark"] .dash-table tbody td:not(:first-child){
    color: rgba(231,237,246,.92) !important;
  }

  html[data-theme="light"] .dash-table tbody td:not(:first-child){
    color: rgba(2,6,23,.92) !important;
  }

  .dash-insights{
    display: grid;
    gap: 12px;
    padding: 10px 0;
  }

  .dash-insight{
    padding: 14px;
    border-radius: 14px;
    border: 1px solid var(--border);
    background: linear-gradient(180deg, var(--surface), var(--surface2));
  }

  .dash-insight__k{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--muted);
  }

  .dash-insight__v{
    margin-top: 6px;
    font-size: 14px;
    font-weight: 900;
    color: var(--text);
  }

  @media (max-width: 1200px){
    .dash-kpis{
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .dash-grid-3{
      grid-template-columns: 1fr 1fr;
    }

    .dash-card--wide{
      grid-column: span 2;
    }

    .dash-summary,
    .dash-hero__content{
      grid-template-columns: 1fr;
    }

    .dash-summary-lite{
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 992px){
    .dash-hero__content{
      grid-template-columns: 1fr;
    }

    .dash-filter__main{
      grid-template-columns: 1fr 1fr;
    }

    .dash-filter__main .dash-filter__btn{
      grid-column: 1 / -1;
      width: 100%;
    }

    .dash-filter__advanced{
      grid-template-columns: 1fr;
    }

    .dash-filter__insightsGrid{
      grid-template-columns: 1fr;
    }

    .dash-filter__footer{
      flex-direction: column;
      align-items: stretch;
    }

    .dash-filter__actions{
      justify-content: stretch;
    }

    .dash-filter__actions .dash-filter__btn2{
      width: 100%;
    }

    .dash-kpis{
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dash-grid-3{
      grid-template-columns: 1fr;
    }

    .dash-card--wide{
      grid-column: span 1;
    }

    .dash-alertspro__head{
      flex-direction:column;
      align-items:flex-start;
    }

    .dash-alertspro__grid,
    .dash-highlights,
    .dash-summary__grid,
    .dash-summary-lite{
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 576px){
    .dash-hero__content{
      padding: 16px;
      gap: 16px;
    }

    .dash-title{
      font-size: 26px;
    }

    .dash-kpis{
      grid-template-columns: 1fr;
    }

    .dash-filter--rank{
      padding: 14px;
    }

    .dash-filter__top,
    .dash-filter__main{
      grid-template-columns: 1fr;
      display:grid;
    }

    .dash-filter__insightsHead{
      flex-direction: column;
      align-items: flex-start;
    }

    .dash-chart{
      min-height: 260px;
    }

    .dash-table th:first-child,
    .dash-table td:first-child{
      width: 180px;
      max-width: 180px;
    }
  }
</style>
@endsection