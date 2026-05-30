@extends('layouts.app')

@section('content')
@php
  $statusLabel = function($st){
    return match($st){
      'em_ingresso'  => 'Em ingresso',
      'em_exercicio' => 'Em Exercício',
      'sob_reserva'  => 'Sob Reserva',
      'em_licenca'   => 'Em Licença',
      'ausente'      => 'Ausente',
      'estagio'      => 'Estágio',
      'desligado'    => 'Desligado',
      default        => $st ? ucfirst(str_replace('_',' ', $st)) : '—',
    };
  };

  $statusClass = function($st){
    return match($st){
      'em_exercicio' => 'pill pill--ok',
      'sob_reserva'  => 'pill pill--warn',
      'em_licenca'   => 'pill pill--warn',
      'ausente'      => 'pill pill--soft',
      'estagio'      => 'pill pill--soft',
      'desligado'    => 'pill pill--bad',
      default        => 'pill pill--soft',
    };
  };

  $fmtInt = function($n){
    return number_format((int)($n ?? 0), 0, ',', '.');
  };

  $fmtHourDec = function($n){
    return number_format((float)($n ?? 0), 2, ',', '.');
  };

  $displayRg = function($r){
    return $r->rg ?? $r->serial ?? '—';
  };

  $displayEquipe = function($r){
    $eq = trim((string)($r->equipe_norm ?? $r->equipe ?? ''));
    return $eq !== '' ? $eq : '—';
  };

  $total = (int)($total ?? 0);
  $sc = $statusCounts ?? [];
  $cEx = (int)($sc['em_exercicio'] ?? 0);
  $cLi = (int)($sc['em_licenca'] ?? 0);
  $cSr = (int)($sc['sob_reserva'] ?? 0);
  $cAu = (int)($sc['ausente'] ?? 0);
  $cEs = (int)($sc['estagio'] ?? 0);
  $cDe = (int)($sc['desligado'] ?? 0);
  $cIn = (int)($sc['em_ingresso'] ?? 0);

  $alfa = $alfa ?? collect();
  $bravo = $bravo ?? collect();
  $charlie = $charlie ?? collect();
  $semEquipe = $semEquipe ?? collect();
  $equipes = $equipes ?? collect();

  $q = $q ?? '';
  $status = !empty($status) ? $status : 'em_exercicio';
  $statusOptions = $statusOptions ?? [
    ''             => 'Todos',
    'em_ingresso'  => 'Em ingresso',
    'em_exercicio' => 'Em Exercício',
    'sob_reserva'  => 'Sob Reserva',
    'em_licenca'   => 'Em Licença',
    'ausente'      => 'Ausente',
    'estagio'      => 'Estágio',
    'desligado'    => 'Desligado',
  ];
@endphp

<div class="eq3-page">

  {{-- HERO --}}
  <section class="eq3-hero">
    <div class="eq3-hero__bg"></div>

    <div class="eq3-hero__content">
      <div class="eq3-hero__main">
        <div class="eq3-kicker">
          <span class="eq3-kicker__dot"></span>
          RH • CENTRO DE EQUIPES • GRR 3.0
        </div>

        <h1 class="eq3-title">Equipes Operacionais</h1>

        <p class="eq3-subtitle">
          Painel institucional com separação automática por equipe, leitura de produtividade,
          integração com ranking e horas patrulhadas, além de classificação geral por desempenho.
        </p>

        <div class="eq3-highlightRow">
          <div class="eq3-highlight">
            <span class="eq3-highlight__label">Total filtrado</span>
            <span class="eq3-highlight__value">{{ $fmtInt($total) }}</span>
          </div>

          <div class="eq3-highlight">
            <span class="eq3-highlight__label">Equipes lidas</span>
            <span class="eq3-highlight__value">{{ $fmtInt($equipes->count()) }}</span>
          </div>

          <div class="eq3-highlight">
            <span class="eq3-highlight__label">Sem equipe</span>
            <span class="eq3-highlight__value">{{ $fmtInt($semEquipe->count()) }}</span>
          </div>
        </div>

        <div class="eq3-statusChips">
          <span class="statusChip statusChip--blue"><i></i> Em ingresso <b>{{ $cIn }}</b></span>
          <span class="statusChip statusChip--green"><i></i> Em exercício <b>{{ $cEx }}</b></span>
          <span class="statusChip statusChip--amber"><i></i> Em licença <b>{{ $cLi }}</b></span>
          <span class="statusChip statusChip--orange"><i></i> Sob reserva <b>{{ $cSr }}</b></span>
          <span class="statusChip statusChip--slate"><i></i> Ausente <b>{{ $cAu }}</b></span>
          <span class="statusChip statusChip--gray"><i></i> Estágio <b>{{ $cEs }}</b></span>
          <span class="statusChip statusChip--red"><i></i> Desligado <b>{{ $cDe }}</b></span>
        </div>
      </div>

      <div class="eq3-hero__actions">
        <a href="{{ route('rh.index') }}" class="btn eq3-btn eq3-btn--ghost">
          ← Voltar ao RH
        </a>

        <a href="{{ route('rh.hierarquia') }}" class="btn eq3-btn eq3-btn--primary">
          Ver hierarquia
        </a>
      </div>
    </div>
  </section>

  <div class="eq3-container">

    {{-- ALERTAS --}}
    @if(session('success'))
      <div class="eq3-alert eq3-alert--success">
        <div class="eq3-alert__icon">✓</div>
        <div>
          <div class="eq3-alert__title">Operação concluída</div>
          <div class="eq3-alert__text">{{ session('success') }}</div>
        </div>
      </div>
    @endif

    @if($errors->any())
      <div class="eq3-alert eq3-alert--danger">
        <div class="eq3-alert__icon">!</div>
        <div>
          <div class="eq3-alert__title">Corrija os campos abaixo</div>
          <ul class="mb-0 ps-3">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    {{-- RESUMO --}}
    <section class="eq3-summary">
      <article class="sumCard sumCard--best">
        <div class="sumCard__top">
          <div class="sumCard__eyebrow">DESTAQUE GERAL</div>
          <div class="sumCard__icon">🏆</div>
        </div>

        <div class="sumCard__title">Melhor equipe</div>
        <div class="sumCard__value">{{ $melhorEquipe->nome ?? '—' }}</div>

        <div class="sumCard__meta">
          <span>XP total <b>{{ $fmtInt($melhorEquipe->xp_total ?? 0) }}</b></span>
          <span>Horas <b>{{ $melhorEquipe->horas_hhmm ?? '00:00' }}</b></span>
        </div>
      </article>

      <article class="sumCard">
        <div class="sumCard__top">
          <div class="sumCard__eyebrow">MENOR DESEMPENHO</div>
          <div class="sumCard__icon">📉</div>
        </div>

        <div class="sumCard__title">Pior equipe</div>
        <div class="sumCard__value">{{ $piorEquipe->nome ?? '—' }}</div>

        <div class="sumCard__meta">
          <span>XP total <b>{{ $fmtInt($piorEquipe->xp_total ?? 0) }}</b></span>
          <span>Horas <b>{{ $piorEquipe->horas_hhmm ?? '00:00' }}</b></span>
        </div>
      </article>

      <article class="sumCard">
        <div class="sumCard__top">
          <div class="sumCard__eyebrow">CRITÉRIO OFICIAL</div>
          <div class="sumCard__icon">⚙️</div>
        </div>

        <div class="sumCard__title">Leitura automática</div>
        <div class="sumCard__value">XP + Horas</div>

        <div class="sumCard__meta">
          <span>Desempate por horas patrulhadas</span>
          <span>Novo desempate por relatórios aprovados</span>
        </div>
      </article>
    </section>

    {{-- FILTROS --}}
    <section class="panelCard">
      <div class="panelCard__head">
        <div>
          <div class="panelCard__eyebrow">CONTROLE DE EXIBIÇÃO</div>
          <div class="panelCard__title">Filtros e busca</div>
          <div class="panelCard__sub">Pesquise registros por nome, CPF, cargo, RG, serial ou Discord.</div>
        </div>

        <div class="panelCard__counter">
          <span>Total listado</span>
          <strong>{{ $fmtInt($total) }}</strong>
        </div>
      </div>

      <div class="panelCard__body">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('rh.equipe') }}">
          <div class="col-xl-7 col-lg-6">
            <label class="fieldLabel">Busca geral</label>
            <div class="fieldWrap">
              <span class="fieldIcon">⌕</span>
              <input
                type="text"
                name="q"
                value="{{ $q }}"
                class="form-control fieldInput"
                placeholder="Nome, CPF, cargo, RG, serial, discord..."
              >
            </div>
          </div>

          <div class="col-xl-3 col-lg-4">
            <label class="fieldLabel">Status operacional</label>
            <select name="status" class="form-select fieldInput">
              @foreach($statusOptions as $k => $v)
                <option value="{{ $k }}" @selected((string)$status === (string)$k)>{{ $v }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-xl-2 col-lg-2 d-grid d-lg-flex gap-2">
            <button class="btn eq3-btn eq3-btn--primary w-100" type="submit">Filtrar</button>
            <a class="btn eq3-btn eq3-btn--ghost w-100" href="{{ route('rh.equipe') }}">Limpar</a>
          </div>
        </form>
      </div>
    </section>

    {{-- SEM EQUIPE --}}
    <section class="panelCard panelCard--danger">
      <div class="panelCard__head">
        <div>
          <div class="panelCard__eyebrow">PENDÊNCIA DE ORGANIZAÇÃO</div>
          <div class="panelCard__title">Integrantes sem equipe</div>
          <div class="panelCard__sub">Registros que não estão alocados em ALFA, BRAVO ou CHARLIE.</div>
        </div>

        <div class="tagBadge tagBadge--warn">
          {{ $fmtInt($semEquipe->count()) }} registro(s)
        </div>
      </div>

      <div class="tableShell">
        <table class="table eq3-table align-middle mb-0">
          <thead>
            <tr>
              <th style="min-width:110px;">CPF</th>
              <th style="min-width:280px;">Nome</th>
              <th style="min-width:150px;">RG</th>
              <th style="min-width:220px;">Cargo</th>
              <th style="min-width:150px;">Status</th>
              <th style="min-width:150px;">Equipe atual</th>
              <th style="min-width:240px;">Definir equipe</th>
              <th style="min-width:200px;">Discord</th>
              <th class="text-end" style="min-width:120px;">XP</th>
              <th class="text-end" style="min-width:130px;">Relatórios</th>
              <th class="text-end" style="min-width:130px;">Horas</th>
              <th class="text-end" style="min-width:140px;">Horas dec.</th>
            </tr>
          </thead>
          <tbody>
            @forelse($semEquipe as $r)
              <tr>
                <td class="mono">{{ $r->cpf ?? '—' }}</td>
                <td>
                  <div class="td-main">{{ $r->nome ?? '—' }}</div>
                </td>
                <td class="mono">{{ $displayRg($r) }}</td>
                <td>{{ $r->cargo ?? '—' }}</td>
                <td>
                  <span class="{{ $statusClass($r->status ?? null) }}">
                    {{ $statusLabel($r->status ?? null) }}
                  </span>
                </td>
                <td>
                  <span class="teamBadge teamBadge--none">{{ $displayEquipe($r) }}</span>
                </td>
                <td>
                  <form method="POST" action="{{ route('rh.equipe.vincular', $r->id) }}" class="assignTeamForm">
                    @csrf
                    @method('PATCH')

                    <div class="assignTeamBox">
                      <select name="equipe" class="form-select assignTeamSelect" required>
                        <option value="">Selecionar</option>
                        <option value="ALFA">ALFA</option>
                        <option value="BRAVO">BRAVO</option>
                        <option value="CHARLIE">CHARLIE</option>
                      </select>

                      <button type="submit" class="btn assignTeamBtn">
                        Salvar
                      </button>
                    </div>
                  </form>
                </td>
                <td class="mono">
                  @if($r->discord_id ?? null)
                    <span class="copyTag" data-copy="{{ $r->discord_id }}">{{ $r->discord_id }}</span>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-end fw-bold">{{ $fmtInt($r->xp ?? 0) }}</td>
                <td class="text-end">{{ $fmtInt($r->relatorios ?? 0) }}</td>
                <td class="text-end fw-semibold">{{ $r->hhmm ?? '00:00' }}</td>
                <td class="text-end">{{ $fmtHourDec($r->total_horas ?? 0) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="12">
                  <div class="emptyState">
                    <div class="emptyState__icon">✓</div>
                    <div class="emptyState__title">Nenhum integrante pendente</div>
                    <div class="emptyState__text">Todos os registros filtrados já estão vinculados a uma equipe operacional.</div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    {{-- EQUIPES --}}
    @foreach($equipes as $i => $eq)
      @php
        $posEquipe = $i + 1;
        $isBest = $posEquipe === 1;
        $isWorst = $posEquipe === $equipes->count();
      @endphp

      <section class="panelCard panelCard--team {{ $isBest ? 'panelCard--teamBest' : '' }} {{ $isWorst ? 'panelCard--teamWorst' : '' }}">
        <div class="panelCard__head panelCard__head--team">
          <div>
            <div class="teamTitleRow">
              <h2 class="teamTitle">{{ $eq->nome }}</h2>

              @if($isBest)
                <span class="rankPill rankPill--best">🥇 Melhor equipe</span>
              @elseif($isWorst)
                <span class="rankPill rankPill--worst">📉 Pior equipe</span>
              @else
                <span class="rankPill">#{{ $posEquipe }} no desempenho</span>
              @endif
            </div>

            <div class="panelCard__sub">
              Classificação institucional automática com base na produtividade consolidada da equipe.
            </div>
          </div>

          <div class="teamStats">
            <span class="tagBadge">{{ $fmtInt($eq->membros_count) }} membro(s)</span>
            <span class="tagBadge">XP {{ $fmtInt($eq->xp_total) }}</span>
            <span class="tagBadge">Relatórios {{ $fmtInt($eq->relatorios_total) }}</span>
            <span class="tagBadge">Horas {{ $eq->horas_hhmm }}</span>
            <span class="tagBadge">Média XP {{ $fmtInt($eq->xp_medio) }}</span>
          </div>
        </div>

        <div class="tableShell">
          <table class="table eq3-table align-middle mb-0">
            <thead>
              <tr>
                <th style="min-width:84px;">Pos.</th>
                <th style="min-width:110px;">CPF</th>
                <th style="min-width:280px;">Nome</th>
                <th style="min-width:130px;">RG</th>
                <th style="min-width:220px;">Cargo</th>
                <th style="min-width:150px;">Status</th>
                <th style="min-width:190px;">Discord</th>
                <th class="text-end" style="min-width:120px;">XP</th>
                <th class="text-end" style="min-width:130px;">Relatórios</th>
                <th class="text-end" style="min-width:130px;">Horas</th>
                <th class="text-end" style="min-width:140px;">Horas dec.</th>
              </tr>
            </thead>
            <tbody>
              @forelse($eq->membros as $k => $r)
                <tr class="{{ $k === 0 ? 'rowTop' : '' }}">
                  <td>
                    <span class="tablePos {{ $k === 0 ? 'tablePos--top' : '' }}">{{ $k + 1 }}º</span>
                  </td>
                  <td class="mono">{{ $r->cpf ?? '—' }}</td>
                  <td>
                    <div class="td-main">{{ $r->nome ?? '—' }}</div>
                  </td>
                  <td class="mono">{{ $displayRg($r) }}</td>
                  <td>{{ $r->cargo ?? '—' }}</td>
                  <td>
                    <span class="{{ $statusClass($r->status ?? null) }}">
                      {{ $statusLabel($r->status ?? null) }}
                    </span>
                  </td>
                  <td class="mono">
                    @if($r->discord_id ?? null)
                      <span class="copyTag" data-copy="{{ $r->discord_id }}">{{ $r->discord_id }}</span>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td class="text-end fw-bold">{{ $fmtInt($r->xp ?? 0) }}</td>
                  <td class="text-end">{{ $fmtInt($r->relatorios ?? 0) }}</td>
                  <td class="text-end fw-semibold">{{ $r->hhmm ?? '00:00' }}</td>
                  <td class="text-end">{{ $fmtHourDec($r->total_horas ?? 0) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="11">
                    <div class="emptyState emptyState--sm">
                      <div class="emptyState__title">Sem membros nesta equipe</div>
                      <div class="emptyState__text">Nenhum registro foi encontrado com os filtros atuais.</div>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="panelCard__foot">
          <div class="panelCard__footItem">
            <span class="footLabel">Leitura oficial:</span>
            XP total da equipe
          </div>

          <div class="panelCard__footItem">
            <span class="footLabel">Desempate 1:</span>
            Horas patrulhadas
          </div>

          <div class="panelCard__footItem">
            <span class="footLabel">Desempate 2:</span>
            Relatórios aprovados
          </div>

          <div class="panelCard__footItem panelCard__footItem--muted">
            Ordenação institucional automática
          </div>
        </div>
      </section>
    @endforeach

  </div>
</div>

<style>
  .eq3-page{
    --bg-card: rgba(255,255,255,.96);
    --bg-soft: rgba(248,250,252,.88);
    --bd: rgba(15,23,42,.08);
    --bd-2: rgba(15,23,42,.06);
    --tx-1: #0f172a;
    --tx-2: rgba(15,23,42,.72);
    --tx-3: rgba(15,23,42,.56);
    --shadow-lg: 0 22px 50px rgba(2,6,23,.10);
    --shadow-md: 0 14px 32px rgba(2,6,23,.07);
    --blue: #3b82f6;
    --indigo: #2563eb;
    --green: #10b981;
    --amber: #f59e0b;
    --orange: #f97316;
    --red: #ef4444;
    --slate: #94a3b8;
    --gold: #f59e0b;

    color: var(--tx-1);
    padding: 20px 18px 42px;
  }

  .eq3-container{
    max-width: 1480px;
    margin: 0 auto;
  }

  .eq3-hero{
    position: relative;
    overflow: hidden;
    border-radius: 28px;
    margin: 0 auto 18px;
    max-width: 1480px;
    background:
      linear-gradient(135deg, rgba(255,255,255,.96), rgba(248,250,252,.92));
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 28px 60px rgba(2,6,23,.12);
  }

  .eq3-hero__bg{
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
      radial-gradient(900px 280px at 8% 0%, rgba(59,130,246,.18), transparent 60%),
      radial-gradient(1000px 320px at 100% 10%, rgba(16,185,129,.12), transparent 62%),
      linear-gradient(180deg, rgba(15,23,42,.04), rgba(15,23,42,0) 48%);
  }

  .eq3-hero__content{
    position: relative;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    padding: 28px;
    flex-wrap: wrap;
  }

  .eq3-hero__main{
    flex: 1 1 780px;
    min-width: 0;
  }

  .eq3-hero__actions{
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-self: flex-start;
  }

  .eq3-kicker{
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .14em;
    color: var(--tx-3);
    text-transform: uppercase;
  }

  .eq3-kicker__dot{
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: linear-gradient(135deg, #3b82f6, #10b981);
    box-shadow: 0 0 0 4px rgba(59,130,246,.10);
  }

  .eq3-title{
    margin: 10px 0 6px;
    font-size: clamp(28px, 4vw, 42px);
    line-height: 1.02;
    letter-spacing: -.04em;
    font-weight: 950;
    color: #020617;
  }

  .eq3-subtitle{
    max-width: 900px;
    margin: 0;
    font-size: 14px;
    line-height: 1.65;
    color: var(--tx-2);
  }

  .eq3-highlightRow{
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 18px;
  }

  .eq3-highlight{
    min-width: 170px;
    padding: 14px 16px;
    border-radius: 18px;
    border: 1px solid rgba(15,23,42,.08);
    background: rgba(255,255,255,.75);
    backdrop-filter: blur(8px);
    box-shadow: 0 10px 22px rgba(2,6,23,.05);
  }

  .eq3-highlight__label{
    display: block;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--tx-3);
  }

  .eq3-highlight__value{
    display: block;
    margin-top: 6px;
    font-size: 24px;
    line-height: 1;
    font-weight: 950;
    letter-spacing: -.03em;
    color: #020617;
  }

  .eq3-statusChips{
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 16px;
  }

  .statusChip{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid rgba(15,23,42,.08);
    background: rgba(255,255,255,.70);
    color: var(--tx-2);
    font-size: 12px;
    font-weight: 900;
    backdrop-filter: blur(8px);
  }

  .statusChip i{
    width: 8px;
    height: 8px;
    border-radius: 999px;
    display: inline-block;
    background: currentColor;
    opacity: .95;
  }

  .statusChip b{
    color: #020617;
    font-weight: 950;
  }

  .statusChip--blue{ color: #2563eb; }
  .statusChip--green{ color: #059669; }
  .statusChip--amber{ color: #d97706; }
  .statusChip--orange{ color: #ea580c; }
  .statusChip--slate{ color: #64748b; }
  .statusChip--gray{ color: #475569; }
  .statusChip--red{ color: #dc2626; }

  .eq3-btn{
    border-radius: 14px;
    padding: 11px 16px;
    font-weight: 900;
    letter-spacing: -.01em;
    border: 1px solid transparent;
  }

  .eq3-btn--primary{
    color: #fff;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    box-shadow: 0 14px 28px rgba(37,99,235,.24);
  }

  .eq3-btn--primary:hover{
    color: #fff;
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
  }

  .eq3-btn--ghost{
    background: rgba(255,255,255,.78);
    border-color: rgba(15,23,42,.10);
    color: var(--tx-1);
  }

  .eq3-btn--ghost:hover{
    background: rgba(248,250,252,.96);
    color: #020617;
  }

  .eq3-alert{
    display: flex;
    gap: 14px;
    align-items: flex-start;
    border-radius: 18px;
    padding: 16px 18px;
    margin-bottom: 14px;
    border: 1px solid transparent;
    box-shadow: var(--shadow-md);
  }

  .eq3-alert__icon{
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    border-radius: 999px;
    display: grid;
    place-items: center;
    font-weight: 900;
  }

  .eq3-alert__title{
    font-weight: 900;
    margin-bottom: 3px;
  }

  .eq3-alert__text{
    color: inherit;
  }

  .eq3-alert--success{
    background: rgba(16,185,129,.10);
    color: #065f46;
    border-color: rgba(16,185,129,.18);
  }

  .eq3-alert--success .eq3-alert__icon{
    background: rgba(16,185,129,.16);
  }

  .eq3-alert--danger{
    background: rgba(239,68,68,.10);
    color: #991b1b;
    border-color: rgba(239,68,68,.18);
  }

  .eq3-alert--danger .eq3-alert__icon{
    background: rgba(239,68,68,.16);
  }

  .eq3-summary{
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 14px;
  }

  .sumCard{
    position: relative;
    overflow: hidden;
    border-radius: 22px;
    padding: 18px;
    background: var(--bg-card);
    border: 1px solid var(--bd);
    box-shadow: var(--shadow-md);
  }

  .sumCard::before{
    content: "";
    position: absolute;
    inset: 0 0 auto 0;
    height: 4px;
    background: linear-gradient(90deg, rgba(59,130,246,.85), rgba(16,185,129,.85));
  }

  .sumCard--best::before{
    background: linear-gradient(90deg, rgba(245,158,11,.95), rgba(251,191,36,.95));
  }

  .sumCard__top{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
  }

  .sumCard__eyebrow{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--tx-3);
  }

  .sumCard__icon{
    font-size: 20px;
    line-height: 1;
  }

  .sumCard__title{
    margin-top: 12px;
    font-size: 14px;
    font-weight: 800;
    color: var(--tx-2);
  }

  .sumCard__value{
    margin-top: 6px;
    font-size: 28px;
    line-height: 1.05;
    letter-spacing: -.03em;
    font-weight: 950;
    color: #020617;
  }

  .sumCard__meta{
    margin-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    color: var(--tx-2);
    font-size: 13px;
  }

  .panelCard{
    overflow: hidden;
    border-radius: 22px;
    background: var(--bg-card);
    border: 1px solid var(--bd);
    box-shadow: var(--shadow-md);
    margin-bottom: 16px;
  }

  .panelCard--danger{
    border-color: rgba(239,68,68,.16);
  }

  .panelCard--teamBest{
    border-color: rgba(245,158,11,.28);
    box-shadow: 0 20px 42px rgba(245,158,11,.10);
  }

  .panelCard--teamWorst{
    border-color: rgba(148,163,184,.24);
  }

  .panelCard__head{
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding: 18px 18px 0;
    flex-wrap: wrap;
  }

  .panelCard__head--team{
    padding-bottom: 16px;
    background:
      linear-gradient(180deg, rgba(248,250,252,.78), rgba(248,250,252,.36));
    border-bottom: 1px solid var(--bd-2);
  }

  .panelCard__eyebrow{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--tx-3);
  }

  .panelCard__title{
    margin-top: 4px;
    font-size: 22px;
    line-height: 1.1;
    font-weight: 950;
    letter-spacing: -.02em;
    color: #020617;
  }

  .panelCard__sub{
    margin-top: 4px;
    font-size: 13px;
    color: var(--tx-2);
  }

  .panelCard__counter{
    min-width: 140px;
    padding: 12px 14px;
    border-radius: 16px;
    background: var(--bg-soft);
    border: 1px solid var(--bd);
    text-align: right;
  }

  .panelCard__counter span{
    display: block;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--tx-3);
  }

  .panelCard__counter strong{
    display: block;
    margin-top: 4px;
    font-size: 24px;
    line-height: 1;
    font-weight: 950;
    color: #020617;
  }

  .panelCard__body{
    padding: 18px;
  }

  .fieldLabel{
    display: block;
    margin-bottom: 8px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--tx-3);
  }

  .fieldWrap{
    position: relative;
  }

  .fieldIcon{
    position: absolute;
    top: 50%;
    left: 14px;
    transform: translateY(-50%);
    color: var(--tx-3);
    font-size: 15px;
    pointer-events: none;
  }

  .fieldInput{
    min-height: 48px;
    border-radius: 14px;
    border-color: rgba(15,23,42,.10);
    background: rgba(255,255,255,.96);
    color: var(--tx-1);
    box-shadow: none !important;
  }

  .fieldWrap .fieldInput{
    padding-left: 42px;
  }

  .fieldInput:focus{
    border-color: rgba(37,99,235,.40);
    box-shadow: 0 0 0 4px rgba(37,99,235,.08) !important;
  }

  .tagBadge{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(248,250,252,.94);
    border: 1px solid rgba(15,23,42,.10);
    color: rgba(15,23,42,.76);
    font-size: 12px;
    font-weight: 900;
  }

  .tagBadge--warn{
    background: rgba(245,158,11,.11);
    border-color: rgba(245,158,11,.22);
    color: #92400e;
  }

  .teamTitleRow{
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .teamTitle{
    margin: 0;
    font-size: 24px;
    line-height: 1.1;
    font-weight: 950;
    letter-spacing: -.02em;
    color: #020617;
  }

  .rankPill{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px 11px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
    border: 1px solid rgba(15,23,42,.10);
    background: rgba(255,255,255,.8);
    color: rgba(15,23,42,.76);
  }

  .rankPill--best{
    background: rgba(245,158,11,.13);
    border-color: rgba(245,158,11,.24);
    color: #92400e;
  }

  .rankPill--worst{
    background: rgba(148,163,184,.16);
    border-color: rgba(148,163,184,.24);
    color: #334155;
  }

  .teamStats{
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .tableShell{
    overflow-x: auto;
  }

  .eq3-table{
    margin-bottom: 0;
  }

  .eq3-table thead th{
    position: sticky;
    top: 0;
    z-index: 1;
    background: #f8fafc;
    color: rgba(15,23,42,.62);
    border-bottom: 1px solid rgba(15,23,42,.08);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .10em;
    text-transform: uppercase;
    white-space: nowrap;
    padding: 13px 12px;
  }

  .eq3-table tbody td{
    padding: 13px 12px;
    border-top: 1px solid rgba(15,23,42,.06);
    vertical-align: middle;
    white-space: nowrap;
    background: rgba(255,255,255,.98);
  }

  .eq3-table tbody tr:hover td{
    background: rgba(248,250,252,.92);
  }

  .eq3-table tbody tr.rowTop td{
    background:
      linear-gradient(180deg, rgba(16,185,129,.08), rgba(16,185,129,.03));
  }

  .td-main{
    font-weight: 800;
    color: #0f172a;
  }

  .mono{
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 12px;
    color: rgba(15,23,42,.76);
  }

  .pill{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 32px;
    padding: 6px 11px;
    border-radius: 999px;
    border: 1px solid rgba(15,23,42,.10);
    font-size: 12px;
    font-weight: 900;
    background: #fff;
    color: rgba(15,23,42,.70);
  }

  .pill--ok{
    background: rgba(16,185,129,.11);
    border-color: rgba(16,185,129,.22);
    color: #047857;
  }

  .pill--warn{
    background: rgba(245,158,11,.13);
    border-color: rgba(245,158,11,.25);
    color: #92400e;
  }

  .pill--bad{
    background: rgba(239,68,68,.10);
    border-color: rgba(239,68,68,.22);
    color: #991b1b;
  }

  .pill--soft{
    background: rgba(148,163,184,.14);
    border-color: rgba(148,163,184,.24);
    color: #334155;
  }

  .teamBadge{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 32px;
    padding: 6px 11px;
    border-radius: 999px;
    border: 1px solid rgba(15,23,42,.10);
    background: rgba(248,250,252,.96);
    color: rgba(15,23,42,.78);
    font-size: 12px;
    font-weight: 900;
  }

  .teamBadge--none{
    background: rgba(239,68,68,.08);
    border-color: rgba(239,68,68,.16);
    color: #991b1b;
  }

  .copyTag{
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 6px 10px;
    border-radius: 10px;
    border: 1px solid rgba(15,23,42,.10);
    background: rgba(248,250,252,.92);
    color: rgba(15,23,42,.82);
    cursor: pointer;
    transition: .18s ease;
  }

  .copyTag:hover{
    background: rgba(59,130,246,.08);
    border-color: rgba(59,130,246,.20);
    color: #1d4ed8;
  }

  .assignTeamBox{
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 220px;
  }

  .assignTeamSelect{
    min-width: 132px;
    min-height: 38px;
    border-radius: 12px;
    border: 1px solid rgba(15,23,42,.12);
    background: rgba(255,255,255,.96);
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    box-shadow: none !important;
  }

  .assignTeamSelect:focus{
    border-color: rgba(37,99,235,.38);
    box-shadow: 0 0 0 4px rgba(37,99,235,.08) !important;
  }

  .assignTeamBtn{
    min-height: 38px;
    border: 0;
    border-radius: 12px;
    padding: 0 12px;
    font-size: 12px;
    font-weight: 900;
    color: #fff;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    box-shadow: 0 10px 20px rgba(37,99,235,.18);
    white-space: nowrap;
  }

  .assignTeamBtn:hover{
    color: #fff;
    background: linear-gradient(135deg, #1d4ed8, #1e40af);
  }

  .tablePos{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 56px;
    min-height: 34px;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid rgba(15,23,42,.10);
    background: rgba(248,250,252,.96);
    color: rgba(15,23,42,.78);
    font-size: 12px;
    font-weight: 900;
  }

  .tablePos--top{
    background: rgba(16,185,129,.12);
    border-color: rgba(16,185,129,.20);
    color: #047857;
  }

  .panelCard__foot{
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-top: 1px solid rgba(15,23,42,.06);
    background: linear-gradient(180deg, rgba(248,250,252,.75), rgba(248,250,252,.45));
  }

  .panelCard__footItem{
    font-size: 12px;
    font-weight: 800;
    color: rgba(15,23,42,.66);
  }

  .panelCard__footItem--muted{
    color: rgba(15,23,42,.52);
  }

  .footLabel{
    color: rgba(15,23,42,.48);
    text-transform: uppercase;
    letter-spacing: .08em;
    font-size: 10px;
    font-weight: 900;
    margin-right: 6px;
  }

  .emptyState{
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 34px 18px;
    color: rgba(15,23,42,.62);
  }

  .emptyState--sm{
    padding: 24px 14px;
  }

  .emptyState__icon{
    width: 52px;
    height: 52px;
    border-radius: 999px;
    display: grid;
    place-items: center;
    margin-bottom: 12px;
    background: rgba(16,185,129,.12);
    color: #047857;
    font-size: 22px;
    font-weight: 900;
  }

  .emptyState__title{
    font-size: 16px;
    font-weight: 900;
    color: #0f172a;
  }

  .emptyState__text{
    margin-top: 4px;
    max-width: 520px;
    font-size: 13px;
    line-height: 1.6;
  }

  /* DARK MODE */
  body.theme-dark .eq3-page,
  html.theme-dark .eq3-page,
  [data-theme="dark"] .eq3-page,
  body.dark .eq3-page,
  html.dark .eq3-page{
    --bg-card: rgba(2,6,23,.72);
    --bg-soft: rgba(15,23,42,.70);
    --bd: rgba(148,163,184,.16);
    --bd-2: rgba(148,163,184,.12);
    --tx-1: rgba(248,250,252,.96);
    --tx-2: rgba(226,232,240,.78);
    --tx-3: rgba(226,232,240,.56);
    --shadow-lg: 0 22px 50px rgba(0,0,0,.34);
    --shadow-md: 0 14px 32px rgba(0,0,0,.28);
  }

  body.theme-dark .eq3-hero,
  html.theme-dark .eq3-hero,
  [data-theme="dark"] .eq3-hero,
  body.dark .eq3-hero,
  html.dark .eq3-hero{
    background:
      linear-gradient(135deg, rgba(2,6,23,.84), rgba(15,23,42,.74));
    border-color: rgba(148,163,184,.16);
    box-shadow: 0 28px 60px rgba(0,0,0,.36);
  }

  body.theme-dark .eq3-hero__bg,
  html.theme-dark .eq3-hero__bg,
  [data-theme="dark"] .eq3-hero__bg,
  body.dark .eq3-hero__bg,
  html.dark .eq3-hero__bg{
    background:
      radial-gradient(900px 280px at 8% 0%, rgba(59,130,246,.22), transparent 60%),
      radial-gradient(1000px 320px at 100% 10%, rgba(16,185,129,.16), transparent 62%),
      linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,0) 48%);
  }

  body.theme-dark .eq3-title,
  html.theme-dark .eq3-title,
  [data-theme="dark"] .eq3-title,
  body.dark .eq3-title,
  html.dark .eq3-title,
  body.theme-dark .sumCard__value,
  html.theme-dark .sumCard__value,
  [data-theme="dark"] .sumCard__value,
  body.dark .sumCard__value,
  html.dark .sumCard__value,
  body.theme-dark .panelCard__title,
  html.theme-dark .panelCard__title,
  [data-theme="dark"] .panelCard__title,
  body.dark .panelCard__title,
  html.dark .panelCard__title,
  body.theme-dark .teamTitle,
  html.theme-dark .teamTitle,
  [data-theme="dark"] .teamTitle,
  body.dark .teamTitle,
  html.dark .teamTitle,
  body.theme-dark .eq3-highlight__value,
  html.theme-dark .eq3-highlight__value,
  [data-theme="dark"] .eq3-highlight__value,
  body.dark .eq3-highlight__value,
  html.dark .eq3-highlight__value,
  body.theme-dark .panelCard__counter strong,
  html.theme-dark .panelCard__counter strong,
  [data-theme="dark"] .panelCard__counter strong,
  body.dark .panelCard__counter strong,
  html.dark .panelCard__counter strong,
  body.theme-dark .td-main,
  html.theme-dark .td-main,
  [data-theme="dark"] .td-main,
  body.dark .td-main,
  html.dark .td-main,
  body.theme-dark .emptyState__title,
  html.theme-dark .emptyState__title,
  [data-theme="dark"] .emptyState__title,
  body.dark .emptyState__title,
  html.dark .emptyState__title{
    color: rgba(248,250,252,.96) !important;
  }

  body.theme-dark .eq3-highlight,
  html.theme-dark .eq3-highlight,
  [data-theme="dark"] .eq3-highlight,
  body.dark .eq3-highlight,
  html.dark .eq3-highlight,
  body.theme-dark .statusChip,
  html.theme-dark .statusChip,
  [data-theme="dark"] .statusChip,
  body.dark .statusChip,
  html.dark .statusChip,
  body.theme-dark .tagBadge,
  html.theme-dark .tagBadge,
  [data-theme="dark"] .tagBadge,
  body.dark .tagBadge,
  html.dark .tagBadge,
  body.theme-dark .rankPill,
  html.theme-dark .rankPill,
  [data-theme="dark"] .rankPill,
  body.dark .rankPill,
  html.dark .rankPill,
  body.theme-dark .panelCard__counter,
  html.theme-dark .panelCard__counter,
  [data-theme="dark"] .panelCard__counter,
  body.dark .panelCard__counter,
  html.dark .panelCard__counter{
    background: rgba(15,23,42,.72) !important;
    border-color: rgba(148,163,184,.16) !important;
    color: rgba(226,232,240,.80) !important;
  }

  body.theme-dark .fieldInput,
  html.theme-dark .fieldInput,
  [data-theme="dark"] .fieldInput,
  body.dark .fieldInput,
  html.dark .fieldInput,
  body.theme-dark .assignTeamSelect,
  html.theme-dark .assignTeamSelect,
  [data-theme="dark"] .assignTeamSelect,
  body.dark .assignTeamSelect,
  html.dark .assignTeamSelect{
    background: rgba(15,23,42,.74) !important;
    border-color: rgba(148,163,184,.16) !important;
    color: rgba(248,250,252,.94) !important;
  }

  body.theme-dark .fieldInput::placeholder,
  html.theme-dark .fieldInput::placeholder,
  [data-theme="dark"] .fieldInput::placeholder,
  body.dark .fieldInput::placeholder,
  html.dark .fieldInput::placeholder{
    color: rgba(226,232,240,.42) !important;
  }

  body.theme-dark .eq3-table thead th,
  html.theme-dark .eq3-table thead th,
  [data-theme="dark"] .eq3-table thead th,
  body.dark .eq3-table thead th,
  html.dark .eq3-table thead th{
    background: rgba(15,23,42,.92) !important;
    color: rgba(226,232,240,.66) !important;
    border-bottom-color: rgba(148,163,184,.16) !important;
  }

  body.theme-dark .eq3-table tbody td,
  html.theme-dark .eq3-table tbody td,
  [data-theme="dark"] .eq3-table tbody td,
  body.dark .eq3-table tbody td,
  html.dark .eq3-table tbody td{
    background: rgba(2,6,23,.52) !important;
    border-top-color: rgba(148,163,184,.10) !important;
    color: rgba(226,232,240,.84) !important;
  }

  body.theme-dark .eq3-table tbody tr:hover td,
  html.theme-dark .eq3-table tbody tr:hover td,
  [data-theme="dark"] .eq3-table tbody tr:hover td,
  body.dark .eq3-table tbody tr:hover td,
  html.dark .eq3-table tbody tr:hover td{
    background: rgba(15,23,42,.58) !important;
  }

  body.theme-dark .eq3-table tbody tr.rowTop td,
  html.theme-dark .eq3-table tbody tr.rowTop td,
  [data-theme="dark"] .eq3-table tbody tr.rowTop td,
  body.dark .eq3-table tbody tr.rowTop td,
  html.dark .eq3-table tbody tr.rowTop td{
    background:
      linear-gradient(180deg, rgba(16,185,129,.12), rgba(16,185,129,.05)) !important;
  }

  body.theme-dark .mono,
  html.theme-dark .mono,
  [data-theme="dark"] .mono,
  body.dark .mono,
  html.dark .mono{
    color: rgba(226,232,240,.76) !important;
  }

  body.theme-dark .copyTag,
  html.theme-dark .copyTag,
  [data-theme="dark"] .copyTag,
  body.dark .copyTag,
  html.dark .copyTag,
  body.theme-dark .tablePos,
  html.theme-dark .tablePos,
  [data-theme="dark"] .tablePos,
  body.dark .tablePos,
  html.dark .tablePos,
  body.theme-dark .teamBadge,
  html.theme-dark .teamBadge,
  [data-theme="dark"] .teamBadge,
  body.dark .teamBadge,
  html.dark .teamBadge,
  body.theme-dark .pill,
  html.theme-dark .pill,
  [data-theme="dark"] .pill,
  body.dark .pill,
  html.dark .pill{
    background: rgba(15,23,42,.72) !important;
    border-color: rgba(148,163,184,.16) !important;
    color: rgba(226,232,240,.82) !important;
  }

  body.theme-dark .panelCard__head--team,
  html.theme-dark .panelCard__head--team,
  [data-theme="dark"] .panelCard__head--team,
  body.dark .panelCard__head--team,
  html.dark .panelCard__head--team,
  body.theme-dark .panelCard__foot,
  html.theme-dark .panelCard__foot,
  [data-theme="dark"] .panelCard__foot,
  body.dark .panelCard__foot,
  html.dark .panelCard__foot{
    background:
      linear-gradient(180deg, rgba(15,23,42,.68), rgba(15,23,42,.52)) !important;
    border-color: rgba(148,163,184,.12) !important;
  }

  body.theme-dark .assignTeamBtn,
  html.theme-dark .assignTeamBtn,
  [data-theme="dark"] .assignTeamBtn,
  body.dark .assignTeamBtn,
  html.dark .assignTeamBtn{
    color: #fff !important;
  }

  @media (max-width: 1200px){
    .eq3-summary{
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 768px){
    .eq3-page{
      padding: 14px 12px 30px;
    }

    .eq3-hero__content{
      padding: 18px;
    }

    .eq3-title{
      font-size: 30px;
    }

    .panelCard__head,
    .panelCard__foot{
      padding-left: 14px;
      padding-right: 14px;
    }

    .panelCard__body{
      padding: 14px;
    }

    .teamStats{
      justify-content: flex-start;
    }

    .assignTeamBox{
      min-width: 200px;
    }
  }
</style>

<script>
  (function(){
    document.addEventListener('click', async function(e){
      const el = e.target.closest('.copyTag');
      if(!el) return;

      const text = el.getAttribute('data-copy') || el.textContent.trim();
      const old = el.textContent;

      try{
        await navigator.clipboard.writeText(text);
        el.textContent = 'Copiado!';
        setTimeout(() => el.textContent = old, 900);
      }catch(err){
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        el.textContent = 'Copiado!';
        setTimeout(() => el.textContent = old, 900);
      }
    });
  })();
</script>
@endsection