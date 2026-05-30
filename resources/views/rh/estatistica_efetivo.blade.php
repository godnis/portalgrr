@extends('layouts.app')

@section('content')
@php
  // atalhos seguros
  $tot  = $stats['efetivo_estagio'] ?? ['efetivo'=>0,'estagio'=>0,'total'=>0];
  $sit  = $stats['situacao'] ?? ['em_ingresso'=>0,'em_exercicio'=>0,'ausente'=>0,'em_licenca'=>0,'desligado'=>0,'estagio'=>0,'total'=>0];
  $cls  = $stats['classes_funcionais'] ?? ['estrategicos'=>0,'taticos'=>0,'operacionais'=>0,'total'=>0];
  $f    = $stats['formacoes'] ?? ['pop'=>0,'clt'=>0,'cap'=>0,'ctb'=>0,'bopm'=>0,'satb'=>0,'cta'=>0,'gmp'=>0,'doa'=>0,'media'=>0];
  $meta = $stats['meta'] ?? ['total_registros'=>0,'base_formacoes'=>0];

  $pct = function ($value, $total) {
    $total = max(1, (int) $total);
    $v = (int) round(((int)$value / $total) * 100);
    return max(0, min(100, $v));
  };

  $pEfetivo = $pct($tot['efetivo'], max(1, $tot['total']));
  $pEstagio = $pct($tot['estagio'], max(1, $tot['total']));

  $pEx = $pct($sit['em_exercicio'], max(1, $sit['total']));
  $pLi = $pct($sit['em_licenca'], max(1, $sit['total']));
  $pDe = $pct($sit['desligado'], max(1, $sit['total']));
  $pEs = $pct($sit['estagio'], max(1, $sit['total']));
  $pAu = $pct($sit['ausente'], max(1, $sit['total']));
  $pIn = $pct($sit['em_ingresso'], max(1, $sit['total']));

  $helpClasses = "Estratégicos: diretoria (Diretor, Vice, Coord., Superint.). "
               . "Táticos: Inspetor + Agente Especial. "
               . "Operacionais: Agentes 1ª/2ª/3ª Classe. Alunos ficam fora.";

  $rawCargos = $stats['cargos'] ?? [];

  $norm = function(string $s): string {
    $s = mb_strtolower(trim($s));
    $s = @iconv('UTF-8', 'ASCII//TRANSLIT', $s) ?: $s;
    $s = str_replace(['º','°','ª','-','_','.'], ' ', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
  };

  $cargoOrder = function(string $cargo) use ($norm): int {
    $c = $norm($cargo);

    if (str_contains($c, 'aluno')) return 10;
    if (str_contains($c, 'agente') && str_contains($c, 'de 3')) return 20;
    if (str_contains($c, 'agente') && str_contains($c, 'de 2')) return 30;
    if (str_contains($c, 'agente') && str_contains($c, 'de 1')) return 40;
    if (str_contains($c, 'agente especial')) return 50;
    if (str_contains($c, 'inspetor')) return 60;
    if (str_contains($c, 'superintendente')) return 70;
    if (str_contains($c, 'coordenador')) return 80;
    if (str_contains($c, 'vice diretor') || str_contains($c, 'vice-diretor')) return 90;
    if (str_contains($c, 'diretor')) return 100;

    return 999;
  };

  $cargosSorted = collect($rawCargos)
    ->map(fn($qt, $cargo) => ['cargo' => (string)$cargo, 'qt' => (int)$qt, 'ord' => $cargoOrder((string)$cargo)])
    ->sortBy([
      ['ord', 'asc'],
      ['cargo', 'asc'],
    ])
    ->values();

  $cargosMain = $cargosSorted->filter(fn($x) => $x['ord'] < 999)->values();
  $cargosOut  = $cargosSorted->filter(fn($x) => $x['ord'] >= 999)->values();

  $clamp = fn($v) => max(0, min(100, (int)$v));
  $formacoes = [
    ['POP',  $clamp($f['pop'])],
    ['CLT',  $clamp($f['clt'])],
    ['CAP',  $clamp($f['cap'])],
    ['CTB',  $clamp($f['ctb'])],
    ['BOPM', $clamp($f['bopm'])],
    ['SAT-B',$clamp($f['satb'])],
    ['CTA',  $clamp($f['cta'])],
    ['GMP',  $clamp($f['gmp'])],
    ['DOA',  $clamp($f['doa'])],
  ];
@endphp

<div class="rhS-wrap">
  {{-- HERO --}}
  <div class="rhS-hero">
    <div class="rhS-hero__bg"></div>

    <div class="rhS-hero__inner">
      <div class="rhS-hero__left">
        <div class="rhS-kicker">RH • ESTATÍSTICAS • GRR 3.0</div>
        <h1 class="rhS-title">Estatísticas do Efetivo</h1>
        <div class="rhS-sub">Indicadores consolidados e leitura operacional da base atual da Hierarquia</div>

        <div class="rhS-chips">
          <span class="rhS-chip"><span class="dot dot--blue"></span> Registros no sistema: <b>{{ $meta['total_registros'] }}</b></span>
          <span class="rhS-chip"><span class="dot dot--green"></span> Base das formações: <b>{{ $meta['base_formacoes'] }}</b></span>
          <span class="rhS-chip"><span class="dot dot--gray"></span> Atualização: <b>tempo real</b></span>
        </div>
      </div>

      <div class="rhS-hero__right">
        <a href="{{ route('rh.index') }}" class="btn btn-outline-secondary rhS-btn">Voltar</a>
        <a href="{{ route('rh.hierarquia') }}" class="btn btn-primary rhS-btn rhS-btn--primary">Ver Hierarquia</a>
      </div>
    </div>
  </div>

  <div class="rhS-container">
    <div class="row g-3">

      {{-- EFETIVO / ESTÁGIO --}}
      <div class="col-lg-4">
        <div class="rhS-card h-100">
          <div class="rhS-card__head">
            <div>
              <div class="rhS-card__title">Efetivo e Estágio</div>
              <div class="rhS-card__sub">Panorama geral do quadro atual</div>
            </div>
            <span class="rhS-badge">{{ $tot['total'] }} total</span>
          </div>

          <div class="rhS-card__body">
            <div class="rhS-statBox rhS-statBox--primary">
              <div class="rhS-statBox__label">Efetivo</div>
              <div class="rhS-statBox__value">{{ $tot['efetivo'] }}</div>
              <div class="rhS-statBox__meta">{{ $pEfetivo }}% do total</div>
              <div class="rhS-bar"><span style="width: {{ $pEfetivo }}%"></span></div>
            </div>

            <div class="rhS-statBox rhS-statBox--soft mt-3">
              <div class="rhS-statBox__label">Estágio</div>
              <div class="rhS-statBox__value">{{ $tot['estagio'] }}</div>
              <div class="rhS-statBox__meta">{{ $pEstagio }}% do total</div>
              <div class="rhS-bar rhS-bar--soft"><span style="width: {{ $pEstagio }}%"></span></div>
            </div>

            <div class="rhS-sep"></div>

            <div class="rhS-row">
              <span class="rhS-label">Total geral</span>
              <div class="rhS-right">
                <b class="rhS-val">{{ $tot['total'] }}</b>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- SITUAÇÃO --}}
      <div class="col-lg-4">
        <div class="rhS-card h-100">
          <div class="rhS-card__head">
            <div>
              <div class="rhS-card__title">Situação</div>
              <div class="rhS-card__sub">Distribuição por status funcional</div>
            </div>
            <span class="rhS-badge rhS-badge--soft">{{ $sit['total'] }} status</span>
          </div>

          <div class="rhS-card__body">
            <div class="rhS-grid">
              <div class="rhS-pill rhS-pill--ok">
                <div class="rhS-pill__k">Em exercício</div>
                <div class="rhS-pill__v">{{ $sit['em_exercicio'] }}</div>
                <div class="rhS-pill__p">{{ $pEx }}%</div>
              </div>

              <div class="rhS-pill rhS-pill--warn">
                <div class="rhS-pill__k">Em licença</div>
                <div class="rhS-pill__v">{{ $sit['em_licenca'] }}</div>
                <div class="rhS-pill__p">{{ $pLi }}%</div>
              </div>

              <div class="rhS-pill rhS-pill--bad">
                <div class="rhS-pill__k">Desligado</div>
                <div class="rhS-pill__v">{{ $sit['desligado'] }}</div>
                <div class="rhS-pill__p">{{ $pDe }}%</div>
              </div>

              <div class="rhS-pill rhS-pill--soft">
                <div class="rhS-pill__k">Estágio</div>
                <div class="rhS-pill__v">{{ $sit['estagio'] }}</div>
                <div class="rhS-pill__p">{{ $pEs }}%</div>
              </div>
            </div>

            <div class="rhS-sep"></div>

            <div class="rhS-row">
              <span class="rhS-label">Em ingresso</span>
              <div class="rhS-right">
                <b class="rhS-val">{{ $sit['em_ingresso'] }}</b>
                <span class="rhS-mini">{{ $pIn }}%</span>
              </div>
            </div>

            <div class="rhS-row mt-2">
              <span class="rhS-label">Ausente</span>
              <div class="rhS-right">
                <b class="rhS-val">{{ $sit['ausente'] }}</b>
                <span class="rhS-mini">{{ $pAu }}%</span>
              </div>
            </div>

            <div class="rhS-sep"></div>

            <div class="rhS-row">
              <span class="rhS-label">Total</span>
              <b class="rhS-val">{{ $sit['total'] }}</b>
            </div>
          </div>
        </div>
      </div>

      {{-- INSTRUTORES + CLASSES --}}
      <div class="col-lg-4">
        <div class="rhS-card h-100">
          <div class="rhS-card__head">
            <div>
              <div class="rhS-card__title">Instrutores & Classes</div>
              <div class="rhS-card__sub">Capacitação e distribuição funcional</div>
            </div>
            <span class="rhS-badge rhS-badge--soft">RH</span>
          </div>

          <div class="rhS-card__body">
            <div class="rhS-highlight">
              <div class="rhS-highlight__label">Instrutores cadastrados</div>
              <div class="rhS-highlight__value">{{ $stats['instrutores'] ?? 0 }}</div>
            </div>

            <div class="rhS-sep"></div>

            <div class="d-flex align-items-center justify-content-between">
              <div class="fw-black rhS-strong">Classes Funcionais</div>
              <span class="rhS-help" title="{{ $helpClasses }}">?</span>
            </div>

            <div class="rhS-classes mt-3">
              <div class="rhS-classBox">
                <span class="rhS-classBox__k">Estratégicos</span>
                <span class="rhS-classBox__v">{{ $cls['estrategicos'] }}</span>
              </div>
              <div class="rhS-classBox">
                <span class="rhS-classBox__k">Táticos</span>
                <span class="rhS-classBox__v">{{ $cls['taticos'] }}</span>
              </div>
              <div class="rhS-classBox">
                <span class="rhS-classBox__k">Operacionais</span>
                <span class="rhS-classBox__v">{{ $cls['operacionais'] }}</span>
              </div>
            </div>

            <div class="rhS-sep"></div>

            <div class="rhS-row">
              <span class="rhS-label">Total</span>
              <b class="rhS-val">{{ $cls['total'] }}</b>
            </div>
          </div>
        </div>
      </div>

      {{-- CARGOS --}}
      <div class="col-lg-8">
        <div class="rhS-card h-100">
          <div class="rhS-card__head">
            <div>
              <div class="rhS-card__title">Cargos</div>
              <div class="rhS-card__sub">Organizado de <b>Aluno</b> até <b>Diretor</b> (menor → maior)</div>
            </div>
            <span class="rhS-badge rhS-badge--soft">{{ $cargosSorted->count() }} tipos</span>
          </div>

          <div class="rhS-card__body">
            <div class="rhS-cargos">
              @forelse($cargosMain as $row)
                <div class="rhS-cargo">
                  <div class="rhS-cargo__name">{{ $row['cargo'] }}</div>
                  <div class="rhS-cargo__right">
                    <span class="rhS-count">{{ $row['qt'] }}</span>
                  </div>
                </div>
              @empty
                <div class="text-muted">Nenhum registro.</div>
              @endforelse
            </div>

            @if($cargosOut->count() > 0)
              <div class="rhS-sep"></div>
              <div class="rhS-strong mb-2">Outros (fora da hierarquia padrão)</div>

              <div class="rhS-cargos">
                @foreach($cargosOut as $row)
                  <div class="rhS-cargo rhS-cargo--out">
                    <div class="rhS-cargo__name">{{ $row['cargo'] }}</div>
                    <div class="rhS-cargo__right">
                      <span class="rhS-count">{{ $row['qt'] }}</span>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- FORMAÇÕES --}}
      <div class="col-lg-4">
        <div class="rhS-card h-100">
          <div class="rhS-card__head">
            <div>
              <div class="rhS-card__title">Formações</div>
              <div class="rhS-card__sub">Percentual sobre a base ativa</div>
            </div>
            <span class="rhS-badge rhS-badge--soft">{{ $meta['base_formacoes'] }} base</span>
          </div>

          <div class="rhS-card__body">
            @foreach($formacoes as [$k, $v])
              <div class="rhS-row">
                <span class="rhS-label">{{ $k }}</span>
                <div class="rhS-right">
                  <b class="rhS-val">{{ $v }}%</b>
                </div>
              </div>
              <div class="rhS-bar rhS-bar--thin"><span style="width: {{ $v }}%"></span></div>
              <div class="rhS-sp"></div>
            @endforeach

            <div class="rhS-sep"></div>

            <div class="rhS-row">
              <span class="rhS-label">Média</span>
              <b class="rhS-val">{{ $clamp($f['media']) }}%</b>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<style>
  /* =========================================================
     PATCH DARK ONLY — ESTATÍSTICAS
     ========================================================= */

  body.theme-dark .rhS-wrap,
  html.theme-dark .rhS-wrap,
  [data-theme="dark"] .rhS-wrap,
  body.dark .rhS-wrap,
  html.dark .rhS-wrap{
    color: rgba(226,232,240,.92);
  }

  body.theme-dark .rhS-hero,
  html.theme-dark .rhS-hero,
  [data-theme="dark"] .rhS-hero,
  body.dark .rhS-hero,
  html.dark .rhS-hero{
    background: rgba(2,6,23,.55) !important;
    border: 1px solid rgba(148,163,184,.18) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,.35) !important;
  }

  body.theme-dark .rhS-hero__bg,
  html.theme-dark .rhS-hero__bg,
  [data-theme="dark"] .rhS-hero__bg,
  body.dark .rhS-hero__bg,
  html.dark .rhS-hero__bg{
    background:
      radial-gradient(1200px 260px at 15% 0%, rgba(59,130,246,.22), transparent 60%),
      radial-gradient(1000px 260px at 85% 0%, rgba(16,185,129,.18), transparent 60%),
      linear-gradient(180deg, rgba(15,23,42,.35), transparent 45%);
  }

  body.theme-dark .rhS-kicker,
  html.theme-dark .rhS-kicker,
  [data-theme="dark"] .rhS-kicker,
  body.dark .rhS-kicker,
  html.dark .rhS-kicker{ color: rgba(226,232,240,.70) !important; }

  body.theme-dark .rhS-title,
  html.theme-dark .rhS-title,
  [data-theme="dark"] .rhS-title,
  body.dark .rhS-title,
  html.dark .rhS-title{ color: rgba(226,232,240,.95) !important; }

  body.theme-dark .rhS-sub,
  html.theme-dark .rhS-sub,
  [data-theme="dark"] .rhS-sub,
  body.dark .rhS-sub,
  html.dark .rhS-sub{ color: rgba(226,232,240,.70) !important; }

  body.theme-dark .rhS-chip,
  html.theme-dark .rhS-chip,
  [data-theme="dark"] .rhS-chip,
  body.dark .rhS-chip,
  html.dark .rhS-chip{
    background: rgba(15,23,42,.55) !important;
    border-color: rgba(148,163,184,.18) !important;
    color: rgba(226,232,240,.86) !important;
  }

  body.theme-dark .rhS-card,
  html.theme-dark .rhS-card,
  [data-theme="dark"] .rhS-card,
  body.dark .rhS-card,
  html.dark .rhS-card{
    background: rgba(2,6,23,.55) !important;
    border: 1px solid rgba(148,163,184,.18) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,.32) !important;
  }

  body.theme-dark .rhS-card__title,
  html.theme-dark .rhS-card__title,
  [data-theme="dark"] .rhS-card__title,
  body.dark .rhS-card__title,
  html.dark .rhS-card__title{
    color: rgba(226,232,240,.95) !important;
  }

  body.theme-dark .rhS-card__sub,
  html.theme-dark .rhS-card__sub,
  [data-theme="dark"] .rhS-card__sub,
  body.dark .rhS-card__sub,
  html.dark .rhS-card__sub{
    color: rgba(226,232,240,.70) !important;
  }

  body.theme-dark .rhS-badge,
  html.theme-dark .rhS-badge,
  [data-theme="dark"] .rhS-badge,
  body.dark .rhS-badge,
  html.dark .rhS-badge{
    background: rgba(15,23,42,.55) !important;
    border-color: rgba(148,163,184,.22) !important;
    color: rgba(226,232,240,.82) !important;
  }

  body.theme-dark .rhS-label,
  html.theme-dark .rhS-label,
  [data-theme="dark"] .rhS-label,
  body.dark .rhS-label,
  html.dark .rhS-label{
    color: rgba(226,232,240,.70) !important;
  }

  body.theme-dark .rhS-val,
  html.theme-dark .rhS-val,
  [data-theme="dark"] .rhS-val,
  body.dark .rhS-val,
  html.dark .rhS-val{
    color: rgba(226,232,240,.95) !important;
  }

  body.theme-dark .rhS-mini,
  html.theme-dark .rhS-mini,
  [data-theme="dark"] .rhS-mini,
  body.dark .rhS-mini,
  html.dark .rhS-mini{
    background: rgba(15,23,42,.55) !important;
    border-color: rgba(148,163,184,.22) !important;
    color: rgba(226,232,240,.75) !important;
  }

  body.theme-dark .rhS-sep,
  html.theme-dark .rhS-sep,
  [data-theme="dark"] .rhS-sep,
  body.dark .rhS-sep,
  html.dark .rhS-sep{
    background: rgba(148,163,184,.18) !important;
  }

  body.theme-dark .rhS-bar,
  html.theme-dark .rhS-bar,
  [data-theme="dark"] .rhS-bar,
  body.dark .rhS-bar,
  html.dark .rhS-bar{
    background: rgba(148,163,184,.14) !important;
  }

  body.theme-dark .rhS-pill,
  html.theme-dark .rhS-pill,
  [data-theme="dark"] .rhS-pill,
  body.dark .rhS-pill,
  html.dark .rhS-pill{
    background: rgba(15,23,42,.55) !important;
    border-color: rgba(148,163,184,.18) !important;
  }

  body.theme-dark .rhS-pill__k,
  html.theme-dark .rhS-pill__k,
  [data-theme="dark"] .rhS-pill__k,
  body.dark .rhS-pill__k,
  html.dark .rhS-pill__k{
    color: rgba(226,232,240,.72) !important;
  }

  body.theme-dark .rhS-pill__v,
  html.theme-dark .rhS-pill__v,
  [data-theme="dark"] .rhS-pill__v,
  body.dark .rhS-pill__v,
  html.dark .rhS-pill__v{
    color: rgba(226,232,240,.96) !important;
  }

  body.theme-dark .rhS-pill__p,
  html.theme-dark .rhS-pill__p,
  [data-theme="dark"] .rhS-pill__p,
  body.dark .rhS-pill__p,
  html.dark .rhS-pill__p{
    color: rgba(226,232,240,.65) !important;
  }

  body.theme-dark .rhS-cargo,
  html.theme-dark .rhS-cargo,
  [data-theme="dark"] .rhS-cargo,
  body.dark .rhS-cargo,
  html.dark .rhS-cargo{
    background: rgba(15,23,42,.55) !important;
    border-color: rgba(148,163,184,.18) !important;
  }

  body.theme-dark .rhS-cargo__name,
  html.theme-dark .rhS-cargo__name,
  [data-theme="dark"] .rhS-cargo__name,
  body.dark .rhS-cargo__name,
  html.dark .rhS-cargo__name{
    color: rgba(226,232,240,.92) !important;
  }

  body.theme-dark .rhS-count,
  html.theme-dark .rhS-count,
  [data-theme="dark"] .rhS-count,
  body.dark .rhS-count,
  html.dark .rhS-count{
    background: rgba(2,6,23,.35) !important;
    border-color: rgba(148,163,184,.18) !important;
    color: rgba(226,232,240,.86) !important;
  }

  body.theme-dark .rhS-cargo--out,
  html.theme-dark .rhS-cargo--out,
  [data-theme="dark"] .rhS-cargo--out,
  body.dark .rhS-cargo--out,
  html.dark .rhS-cargo--out{
    background: rgba(239,68,68,.08) !important;
    border-color: rgba(239,68,68,.22) !important;
  }

  body.theme-dark .rhS-help,
  html.theme-dark .rhS-help,
  [data-theme="dark"] .rhS-help,
  body.dark .rhS-help,
  html.dark .rhS-help{
    background: rgba(15,23,42,.55) !important;
    border-color: rgba(148,163,184,.22) !important;
    color: rgba(226,232,240,.78) !important;
  }

  body.theme-dark .rhS-highlight,
  html.theme-dark .rhS-highlight,
  [data-theme="dark"] .rhS-highlight,
  body.dark .rhS-highlight,
  html.dark .rhS-highlight{
    background: rgba(15,23,42,.56) !important;
    border-color: rgba(148,163,184,.18) !important;
  }

  body.theme-dark .rhS-highlight__label,
  html.theme-dark .rhS-highlight__label,
  [data-theme="dark"] .rhS-highlight__label,
  body.dark .rhS-highlight__label,
  html.dark .rhS-highlight__label{
    color: rgba(226,232,240,.68) !important;
  }

  body.theme-dark .rhS-highlight__value,
  html.theme-dark .rhS-highlight__value,
  [data-theme="dark"] .rhS-highlight__value,
  body.dark .rhS-highlight__value,
  html.dark .rhS-highlight__value{
    color: rgba(226,232,240,.96) !important;
  }

  body.theme-dark .rhS-classBox,
  html.theme-dark .rhS-classBox,
  [data-theme="dark"] .rhS-classBox,
  body.dark .rhS-classBox,
  html.dark .rhS-classBox{
    background: rgba(15,23,42,.56) !important;
    border-color: rgba(148,163,184,.18) !important;
  }

  body.theme-dark .rhS-classBox__k,
  html.theme-dark .rhS-classBox__k,
  [data-theme="dark"] .rhS-classBox__k,
  body.dark .rhS-classBox__k,
  html.dark .rhS-classBox__k{
    color: rgba(226,232,240,.70) !important;
  }

  body.theme-dark .rhS-classBox__v,
  html.theme-dark .rhS-classBox__v,
  [data-theme="dark"] .rhS-classBox__v,
  body.dark .rhS-classBox__v,
  html.dark .rhS-classBox__v{
    color: rgba(226,232,240,.95) !important;
  }

  body.theme-dark .rhS-statBox,
  html.theme-dark .rhS-statBox,
  [data-theme="dark"] .rhS-statBox,
  body.dark .rhS-statBox,
  html.dark .rhS-statBox{
    background: rgba(15,23,42,.56) !important;
    border-color: rgba(148,163,184,.18) !important;
  }

  body.theme-dark .rhS-statBox__label,
  html.theme-dark .rhS-statBox__label,
  [data-theme="dark"] .rhS-statBox__label,
  body.dark .rhS-statBox__label,
  html.dark .rhS-statBox__label{
    color: rgba(226,232,240,.68) !important;
  }

  body.theme-dark .rhS-statBox__value,
  html.theme-dark .rhS-statBox__value,
  [data-theme="dark"] .rhS-statBox__value,
  body.dark .rhS-statBox__value,
  html.dark .rhS-statBox__value{
    color: rgba(226,232,240,.96) !important;
  }

  body.theme-dark .rhS-statBox__meta,
  html.theme-dark .rhS-statBox__meta,
  [data-theme="dark"] .rhS-statBox__meta,
  body.dark .rhS-statBox__meta,
  html.dark .rhS-statBox__meta{
    color: rgba(226,232,240,.62) !important;
  }

  /* =========================================================
     BASE
     ========================================================= */

  .rhS-wrap{ padding: 18px 18px 44px; }
  .rhS-container{ max-width: 1320px; margin: 0 auto; }

  .rhS-hero{
    position: relative;
    border-radius: 22px;
    overflow: hidden;
    border: 1px solid rgba(2,6,23,.07);
    box-shadow: 0 18px 40px rgba(2,6,23,.10);
    background:#fff;
    margin: 8px auto 18px;
    max-width: 1320px;
  }

  .rhS-hero__bg{
    position:absolute; inset:0;
    background:
      radial-gradient(1200px 260px at 15% 0%, rgba(59,130,246,.18), transparent 60%),
      radial-gradient(1000px 260px at 85% 0%, rgba(16,185,129,.14), transparent 60%),
      linear-gradient(180deg, rgba(2,6,23,.03), transparent 40%);
    pointer-events:none;
  }

  .rhS-hero__inner{
    position:relative;
    display:flex; justify-content:space-between; align-items:flex-start;
    gap: 16px;
    padding: 22px;
    flex-wrap: wrap;
  }

  .rhS-kicker{
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .14em;
    color: rgba(2,6,23,.55);
    text-transform: uppercase;
  }

  .rhS-title{
    font-size: 28px;
    font-weight: 900;
    margin: 6px 0 4px;
    letter-spacing: -.02em;
    color:#0b1220;
  }

  .rhS-sub{ color: rgba(2,6,23,.62); font-size: 14px; }

  .rhS-chips{ display:flex; gap:10px; flex-wrap:wrap; margin-top: 12px; }

  .rhS-chip{
    display:inline-flex; align-items:center; gap:8px;
    padding: 8px 10px;
    border-radius: 999px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(248,250,252,.9);
    font-size: 13px;
  }

  .dot{ width:8px; height:8px; border-radius:99px; display:inline-block; }
  .dot--blue{ background:#3b82f6; }
  .dot--green{ background:#10b981; }
  .dot--gray{ background:#94a3b8; }

  .rhS-btn{ border-radius: 12px; padding: 10px 14px; font-weight: 800; }
  .rhS-btn--primary{ box-shadow: 0 10px 22px rgba(37,99,235,.18); }

  .rhS-card{
    border-radius: 18px;
    border: 1px solid rgba(2,6,23,.08);
    background: #fff;
    box-shadow: 0 12px 28px rgba(2,6,23,.06);
    overflow: hidden;
  }

  .rhS-card__head{
    display:flex; align-items:flex-start; justify-content:space-between;
    gap: 12px;
    padding: 16px 16px 0;
    flex-wrap: wrap;
  }

  .rhS-card__title{ font-weight: 900; letter-spacing: -.01em; color:#0b1220; }
  .rhS-card__sub{ color: rgba(2,6,23,.60); font-size: 13px; }
  .rhS-card__body{ padding: 16px; }

  .rhS-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 7px 10px;
    border-radius: 999px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(2,6,23,.04);
    font-weight: 900;
    font-size: 12px;
    color: rgba(2,6,23,.72);
  }

  .rhS-badge--soft{
    background: rgba(148,163,184,.12);
    border-color: rgba(148,163,184,.22);
  }

  .rhS-row{
    display:flex; align-items:center; justify-content:space-between;
    gap: 10px;
    line-height: 1.2;
  }

  .rhS-label{ color: rgba(2,6,23,.65); font-size: 13px; }
  .rhS-val{ font-weight: 900; color:#0b1220; }
  .rhS-right{ display:flex; align-items:baseline; gap: 10px; }

  .rhS-mini{
    font-size: 12px;
    font-weight: 900;
    color: rgba(2,6,23,.55);
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(248,250,252,.9);
    padding: 4px 8px;
    border-radius: 999px;
  }

  .rhS-sep{ height: 1px; background: rgba(2,6,23,.08); margin: 14px 0; }
  .rhS-sp{ height: 8px; }
  .rhS-strong{ font-weight: 900; color: #0b1220; }

  .rhS-bar{
    height: 10px;
    border-radius: 999px;
    background: rgba(2,6,23,.06);
    overflow: hidden;
    margin-top: 8px;
  }

  .rhS-bar span{
    display:block;
    height: 100%;
    width: 0%;
    border-radius: 999px;
    background: rgba(59,130,246,.50);
  }

  .rhS-bar--soft span{ background: rgba(148,163,184,.55); }
  .rhS-bar--thin{ height: 8px; margin-top: 6px; }
  .rhS-bar--thin span{ background: rgba(16,185,129,.55); }

  .rhS-grid{
    display:grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    grid-auto-rows: 1fr;
    align-items: stretch;
  }

  .rhS-pill{
    border-radius: 16px;
    border: 1px solid rgba(2,6,23,.08);
    background: rgba(248,250,252,.92);
    padding: 12px;
    height: 100%;
    display:flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 92px;
  }

  .rhS-pill__k{ font-size: 12px; font-weight: 900; color: rgba(2,6,23,.62); text-transform: uppercase; letter-spacing:.08em; }
  .rhS-pill__v{ font-size: 22px; font-weight: 900; color:#0b1220; margin-top: 6px; }
  .rhS-pill__p{ font-size: 12px; font-weight: 900; color: rgba(2,6,23,.55); margin-top: 2px; }

  .rhS-pill--ok{ background: rgba(16,185,129,.10); border-color: rgba(16,185,129,.20); }
  .rhS-pill--warn{ background: rgba(245,158,11,.12); border-color: rgba(245,158,11,.22); }
  .rhS-pill--bad{ background: rgba(239,68,68,.10); border-color: rgba(239,68,68,.20); }
  .rhS-pill--soft{ background: rgba(148,163,184,.12); border-color: rgba(148,163,184,.22); }

  .rhS-statBox{
    border-radius: 18px;
    border: 1px solid rgba(2,6,23,.08);
    background: rgba(248,250,252,.92);
    padding: 14px;
  }

  .rhS-statBox--primary{
    background: linear-gradient(180deg, rgba(59,130,246,.10), rgba(248,250,252,.96));
  }

  .rhS-statBox--soft{
    background: linear-gradient(180deg, rgba(148,163,184,.10), rgba(248,250,252,.96));
  }

  .rhS-statBox__label{
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: rgba(2,6,23,.62);
  }

  .rhS-statBox__value{
    margin-top: 6px;
    font-size: 28px;
    line-height: 1;
    font-weight: 900;
    color: #0b1220;
  }

  .rhS-statBox__meta{
    margin-top: 6px;
    font-size: 12px;
    color: rgba(2,6,23,.58);
    font-weight: 700;
  }

  .rhS-highlight{
    border-radius: 18px;
    border: 1px solid rgba(2,6,23,.08);
    background: rgba(248,250,252,.92);
    padding: 14px;
  }

  .rhS-highlight__label{
    font-size: 12px;
    font-weight: 900;
    color: rgba(2,6,23,.62);
    text-transform: uppercase;
    letter-spacing: .08em;
  }

  .rhS-highlight__value{
    margin-top: 6px;
    font-size: 30px;
    line-height: 1;
    font-weight: 900;
    color: #0b1220;
  }

  .rhS-classes{
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .rhS-classBox{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 14px;
    border: 1px solid rgba(2,6,23,.08);
    background: rgba(248,250,252,.92);
  }

  .rhS-classBox__k{
    font-size: 13px;
    font-weight: 800;
    color: rgba(2,6,23,.68);
  }

  .rhS-classBox__v{
    font-size: 18px;
    font-weight: 900;
    color: #0b1220;
  }

  .rhS-cargos{
    display:grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    align-items: stretch;
  }

  .rhS-cargo{
    border-radius: 16px;
    border: 1px solid rgba(2,6,23,.08);
    background: rgba(248,250,252,.9);
    padding: 10px 12px;
    display:flex; align-items:center; justify-content:space-between; gap: 10px;
    min-height: 48px;
  }

  .rhS-cargo--out{
    background: rgba(239,68,68,.06);
    border-color: rgba(239,68,68,.14);
  }

  .rhS-cargo__name{
    font-weight: 900;
    color:#0b1220;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 230px;
  }

  .rhS-count{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width: 34px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(2,6,23,.06);
    font-weight: 900;
    color: rgba(2,6,23,.75);
  }

  .rhS-help{
    width: 26px; height: 26px;
    display:inline-flex; align-items:center; justify-content:center;
    border-radius: 999px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(248,250,252,.9);
    font-weight: 900;
    color: rgba(2,6,23,.65);
    cursor: help;
    user-select:none;
  }

  @media (max-width: 992px){
    .rhS-cargos{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }

  @media (max-width: 520px){
    .rhS-grid{ grid-template-columns: 1fr; }
    .rhS-cargos{ grid-template-columns: 1fr; }
    .rhS-title{ font-size: 24px; }
  }
</style>
@endsection