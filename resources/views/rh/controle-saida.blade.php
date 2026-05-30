@extends('layouts.app')

@section('content')
@php
  $countPage = method_exists($rows, 'count') ? $rows->count() : (is_countable($rows) ? count($rows) : 0);
  $totalAll  = method_exists($rows, 'total') ? $rows->total() : $countPage;

  $itemsForStats = collect($rows instanceof \Illuminate\Contracts\Pagination\Paginator ? $rows->items() : $rows);

  $motivoCounts = $itemsForStats
    ->groupBy(fn($r) => (string)($r->motivo_saida ?? '—'))
    ->map(fn($g) => $g->count())
    ->sortDesc();

  $topMotivo = $motivoCounts->keys()->first() ?? '—';
  $topMotivoCount = (int) ($motivoCounts->values()->first() ?? 0);

  $canForm = (bool) ($canEdit ?? false);
  $canDelete = auth()->check() && ((int) auth()->user()->nivel >= 9);
@endphp

<div class="rhS-wrap">

  {{-- HERO --}}
  <div class="rhS-hero">
    <div class="rhS-hero__bg"></div>

    <div class="rhS-hero__inner">
      <div class="rhS-hero__left">
        <div class="rhS-kicker">RH • CONTROLE DE SAÍDAS • GRR 3.0</div>
        <h1 class="rhS-title">Controle de Saídas</h1>
        <div class="rhS-sub">Registro, auditoria e baixa automática do efetivo com padrão visual novo</div>

        <div class="rhS-chips">
          <span class="rhS-chip"><span class="dot dot--blue"></span> Registros exibidos: <b>{{ $countPage }}</b></span>
          <span class="rhS-chip"><span class="dot dot--green"></span> Total filtrado: <b>{{ $totalAll }}</b></span>
          <span class="rhS-chip"><span class="dot dot--amber"></span> Período: <b>{{ $fromDate->format('d/m/Y') }}</b> a <b>{{ $toDate->format('d/m/Y') }}</b></span>
          <span class="rhS-chip"><span class="dot dot--gray"></span> Motivo mais comum: <b>{{ $topMotivo }}</b> ({{ $topMotivoCount }})</span>
        </div>
      </div>

      <div class="rhS-hero__right">
        <a href="{{ route('rh.index') }}" class="btn btn-outline-secondary rhS-btn">Voltar</a>

        @if(!$canForm)
          <span class="rhS-readonly">modo leitura</span>
        @else
          <a href="#registrar-saida" class="btn btn-primary rhS-btn rhS-btn--primary">+ Registrar</a>
        @endif
      </div>
    </div>
  </div>

  <div class="rhS-container">

    @if(session('success'))
      <div class="alert alert-success rhS-alert">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger rhS-alert">
        <div class="fw-semibold mb-1">Corrija os campos:</div>
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- FILTROS --}}
    <div class="rhS-card">
      <div class="rhS-card__head">
        <div>
          <div class="rhS-card__title">Filtros</div>
          <div class="rhS-card__sub">Pesquise por nome, CPF, cargo, serial, discord, motivo ou detalhe</div>
        </div>
        <div class="rhS-card__headRight">
          <span class="rhS-muted">Total no filtro: <b>{{ $totalAll }}</b></span>
        </div>
      </div>

      <div class="rhS-card__body">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('rh.controle_saida') }}">
          <div class="col-lg-5">
            <label class="form-label rhS-label">Busca</label>
            <input class="form-control rhS-input" name="q" value="{{ $q }}" placeholder="Nome, CPF, cargo, serial, discord, motivo...">
          </div>

          <div class="col-lg-2">
            <label class="form-label rhS-label">De</label>
            <input type="date" class="form-control rhS-input" name="from" value="{{ $from }}">
          </div>

          <div class="col-lg-2">
            <label class="form-label rhS-label">Até</label>
            <input type="date" class="form-control rhS-input" name="to" value="{{ $to }}">
          </div>

          <div class="col-lg-3 d-flex gap-2">
            <button class="btn btn-primary w-100 rhS-btn rhS-btn--primary" type="submit">Filtrar</button>
            <a class="btn btn-outline-secondary rhS-btn" href="{{ route('rh.controle_saida') }}">Limpar</a>
          </div>
        </form>
      </div>
    </div>

    {{-- REGISTRAR SAÍDA --}}
    <div class="rhS-card" id="registrar-saida">
      <div class="rhS-card__head">
        <div>
          <div class="rhS-card__title">Registrar saída</div>
          <div class="rhS-card__sub">Selecione o militar e confirme a baixa do efetivo</div>
        </div>
        <div class="rhS-card__headRight">
          @if(!$canForm)
            <span class="rhS-muted">Somente leitura</span>
          @else
            <span class="rhS-muted">Permissão: <b>OK</b></span>
          @endif
        </div>
      </div>

      <div class="rhS-card__body">
        @if(!$canForm)
          <div class="alert alert-warning mb-0 rhS-alertBox">
            Você está em modo leitura. Somente quem tem permissão pode registrar saídas.
          </div>
        @else
          <form class="row g-3" method="POST" action="{{ route('rh.controle_saida.store') }}">
            @csrf

            <div class="col-lg-6">
              <label class="form-label rhS-label">Militar (Hierarquia)</label>

              {{-- SELECT REAL (hidden) --}}
              <select class="d-none" name="hierarquia_id" id="militarSelect" required>
                <option value="">Selecione...</option>
                @foreach($militares as $m)
                  <option
                    value="{{ $m->id }}"
                    data-nome="{{ e($m->nome) }}"
                    data-cpf="{{ e($m->cpf) }}"
                    data-cargo="{{ e($m->cargo) }}"
                    data-admissao="{{ optional($m->admissao)->format('d/m/Y') }}"
                    data-promocao="{{ optional($m->ultima_promocao)->format('d/m/Y') }}"
                    data-serial="{{ e($m->serial) }}"
                    data-discord="{{ e($m->discord_id) }}"
                  >
                    {{ $m->nome }} — {{ $m->cargo ?? '—' }} (CPF/RG: {{ $m->cpf ?? '-' }})
                  </option>
                @endforeach
              </select>

              {{-- SELECT CUSTOM --}}
              <div class="rhS-picker" id="militarPicker">
                <div class="rhS-picker__control" id="militarPickerControl">
                  <input
                    type="text"
                    id="militarSearch"
                    class="form-control rhS-input rhS-picker__input"
                    placeholder="Digite o nome, cargo ou CPF/RG..."
                    autocomplete="off"
                  >
                  <button type="button" class="rhS-picker__toggle" id="militarToggle" aria-label="Abrir lista">
                    ▾
                  </button>
                </div>

                <div class="rhS-picker__selected" id="militarSelected" hidden>
                  <div class="rhS-picker__selectedMain" id="militarSelectedMain"></div>
                  <div class="rhS-picker__selectedSub" id="militarSelectedSub"></div>
                </div>

                <div class="rhS-picker__menu" id="militarMenu" hidden>
                  <div class="rhS-picker__hint">Selecione um militar do efetivo</div>
                  <div class="rhS-picker__list" id="militarList"></div>
                </div>
              </div>

              <div class="rhS-help mt-1">Digite para buscar mais rápido. Ao registrar a saída, o militar deve deixar o quadro ativo da Hierarquia.</div>
            </div>

            <div class="col-lg-3">
              <label class="form-label rhS-label">Data/Hora da saída</label>
              <input type="datetime-local" class="form-control rhS-input" name="saida_em" required value="{{ old('saida_em') }}">
            </div>

            <div class="col-lg-3">
              <label class="form-label rhS-label">Motivo</label>
              <select class="form-select rhS-input" name="motivo_saida" required>
                @foreach([
                  'Abandono de Posto',
                  'Desligamento Voluntário',
                  'Remanejamento Funcional',
                  'Transferência',
                  'Inatividade',
                  'Ausência',
                  'Outros',
                ] as $mot)
                  <option value="{{ $mot }}" @selected(old('motivo_saida')===$mot)>{{ $mot }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <div class="rhS-autoGrid">
                <div class="rhS-auto">
                  <div class="rhS-auto__label">Admissão</div>
                  <input class="form-control rhS-input" id="autoAdmissao" disabled>
                </div>
                <div class="rhS-auto">
                  <div class="rhS-auto__label">Últ. Promoção</div>
                  <input class="form-control rhS-input" id="autoPromocao" disabled>
                </div>
                <div class="rhS-auto">
                  <div class="rhS-auto__label">Serial</div>
                  <input class="form-control rhS-input" id="autoSerial" disabled>
                </div>
                <div class="rhS-auto">
                  <div class="rhS-auto__label">Discord ID</div>
                  <input class="form-control rhS-input" id="autoDiscord" disabled>
                </div>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label rhS-label">Detalhe (opcional)</label>
              <input class="form-control rhS-input" name="motivo_detalhe" value="{{ old('motivo_detalhe') }}" placeholder="Ex.: À pedido, por rescisão, etc.">
            </div>

            <div class="col-12">
              <div class="rhS-warningLine">
                <span class="rhS-warningLine__icon">⚠️</span>
                <span>Ao salvar, esse militar deve ser retirado do efetivo ativo na Hierarquia.</span>
              </div>
            </div>

            <div class="col-12 d-flex justify-content-end">
              <button class="btn btn-primary rhS-btn rhS-btn--primary">Registrar Saída</button>
            </div>
          </form>
        @endif
      </div>
    </div>

    {{-- LISTA --}}
    <div class="rhS-card">
      <div class="rhS-card__head">
        <div>
          <div class="rhS-card__title">Registros</div>
          <div class="rhS-card__sub">Visual em planilha com leitura mais rápida e detalhes melhores</div>
        </div>
        <div class="rhS-card__headRight">
          <span class="rhS-muted">Exibindo <b>{{ $countPage }}</b> registro(s)</span>
        </div>
      </div>

      <div class="rhS-tableWrap">
        <table class="table rhS-table align-middle mb-0">
          <thead>
            <tr>
              <th>Militar</th>
              <th>Saída</th>
              <th>Cargo</th>
              <th>Admissão</th>
              <th>Últ. Promoção</th>
              <th>Serial</th>
              <th>Discord</th>
              <th>Motivo</th>
              <th>Detalhe</th>
              <th class="text-end" style="width: 140px;">Ações</th>
            </tr>
          </thead>

          <tbody>
            @forelse($rows as $r)
              @php
                $nome = (string) ($r->nome ?? '');
                $parts = preg_split('/\s+/', trim($nome));
                $ini = '';
                if (!empty($parts[0])) $ini .= mb_strtoupper(mb_substr($parts[0], 0, 1));
                if (!empty($parts[1])) $ini .= mb_strtoupper(mb_substr($parts[1], 0, 1));
                if ($ini === '') $ini = 'PRF';

                $motivo = (string) ($r->motivo_saida ?? '—');

                $motBadge = match($motivo){
                  'Abandono de Posto'        => 'st st--bad',
                  'Desligamento Voluntário'  => 'st st--warn',
                  'Remanejamento Funcional'  => 'st st--soft',
                  'Transferência'            => 'st st--soft',
                  'Inatividade'              => 'st st--warn',
                  'Ausência'                 => 'st st--bad',
                  default                    => 'st st--soft'
                };

                $serial = $r->serial ?? null;
                $discord = $r->discord_id ?? null;
              @endphp

              <tr>
                <td>
                  <div class="rhS-person">
                    <div class="rhS-avatar">{{ $ini }}</div>
                    <div class="rhS-person__meta">
                      <div class="rhS-person__name">{{ $r->nome ?? '-' }}</div>
                      <div class="rhS-person__sub">
                        <span><b>CPF/RG:</b> {{ $r->cpf ?? '-' }}</span>
                      </div>
                    </div>
                  </div>
                </td>

                <td>
                  <span class="rhS-date">{{ optional($r->saida_em)->format('d/m/Y H:i') ?? '-' }}</span>
                </td>

                <td>
                  <div class="rhS-mainText">{{ $r->cargo ?? '-' }}</div>
                  <div class="rhS-subText">{{ $r->efetivacao ?? '—' }}</div>
                </td>

                <td><span class="rhS-mainText">{{ $r->admissao?->format('d/m/Y') ?? '-' }}</span></td>
                <td><span class="rhS-mainText">{{ $r->ultima_promocao?->format('d/m/Y') ?? '-' }}</span></td>

                <td>
                  @if($serial)
                    <span class="copy" data-copy="{{ $serial }}">{{ $serial }}</span>
                  @else
                    <span class="rhS-dash">—</span>
                  @endif
                </td>

                <td>
                  @if($discord)
                    <span class="copy" data-copy="{{ $discord }}">{{ $discord }}</span>
                  @else
                    <span class="rhS-dash">—</span>
                  @endif
                </td>

                <td>
                  <span class="{{ $motBadge }}">{{ $motivo }}</span>
                </td>

                <td>
                  @if($r->motivo_detalhe)
                    <div class="rhS-detail">{{ $r->motivo_detalhe }}</div>
                  @else
                    <span class="rhS-dash">—</span>
                  @endif
                </td>

                <td class="text-end">
                  @if($canDelete)
                    <form method="POST" action="{{ route('rh.controle_saida.destroy', $r) }}" class="d-inline"
                          onsubmit="return confirm('Remover este registro de saída?');">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger">Remover</button>
                    </form>
                  @else
                    <span class="text-muted small">somente leitura</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center text-muted py-5">Nenhum registro no período.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if(method_exists($rows, 'links'))
        <div class="p-3">
          {{ $rows->links() }}
        </div>
      @endif

      <div class="rhS-footerHint">
        Controle de saídas em padrão GRR 3.0, com leitura melhor e visual mais limpo.
      </div>
    </div>

  </div>
</div>

<style>
  .rhS-wrap{ padding: 18px 18px 40px; }
  .rhS-container{ max-width: 1320px; margin: 0 auto; }
  .rhS-muted{ color: rgba(226,232,240,.70); font-size: 13px; }
  .rhS-alert{ border-radius: 14px; }

  .rhS-hero{
    position: relative;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 18px 40px rgba(0,0,0,.25);
    border: 1px solid rgba(148,163,184,.18);
    margin: 8px auto 18px;
    max-width: 1320px;
    background: rgba(2,6,23,.72);
  }
  .rhS-hero__bg{
    position:absolute; inset:0;
    background:
      radial-gradient(1200px 240px at 15% 0%, rgba(59,130,246,.20), transparent 60%),
      radial-gradient(1000px 240px at 85% 0%, rgba(16,185,129,.14), transparent 60%),
      linear-gradient(180deg, rgba(255,255,255,.03), transparent 35%);
    pointer-events:none;
  }
  .rhS-hero__inner{
    position: relative;
    display:flex; align-items:flex-start; justify-content:space-between;
    gap: 16px;
    padding: 22px;
    flex-wrap: wrap;
  }
  .rhS-kicker{
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .12em;
    color: rgba(226,232,240,.65);
  }
  .rhS-title{
    font-size: 28px;
    font-weight: 900;
    margin: 4px 0 2px;
    letter-spacing: -.02em;
    color: #f8fafc;
  }
  .rhS-sub{ color: rgba(226,232,240,.72); font-size: 14px; }
  .rhS-chips{ display:flex; gap:10px; flex-wrap:wrap; margin-top: 12px; }
  .rhS-chip{
    display:inline-flex; align-items:center; gap:8px;
    padding: 8px 10px;
    border-radius: 999px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(15,23,42,.60);
    font-size: 13px;
    color: rgba(226,232,240,.88);
  }

  .dot{ width:8px; height:8px; border-radius:99px; display:inline-block; }
  .dot--blue{ background:#3b82f6; }
  .dot--green{ background:#10b981; }
  .dot--amber{ background:#f59e0b; }
  .dot--gray{ background:#94a3b8; }

  .rhS-btn{ border-radius: 12px; padding: 10px 14px; font-weight: 700; }
  .rhS-btn--primary{ box-shadow: 0 10px 22px rgba(37,99,235,.18); }

  .rhS-readonly{
    display:inline-flex; align-items:center; justify-content:center;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid rgba(148,163,184,.22);
    background: rgba(15,23,42,.55);
    font-weight: 900;
    font-size: 12px;
    color: rgba(226,232,240,.82);
    text-transform: uppercase;
    letter-spacing: .08em;
  }

  .rhS-card{
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.16);
    background: rgba(2,6,23,.72);
    box-shadow: 0 12px 28px rgba(0,0,0,.20);
    margin-bottom: 14px;
    overflow: hidden;
  }
  .rhS-card__head{
    display:flex; align-items:flex-start; justify-content:space-between;
    gap: 12px;
    padding: 16px 16px 0;
    flex-wrap: wrap;
  }
  .rhS-card__headRight{ padding-top: 6px; }
  .rhS-card__title{ font-weight: 900; letter-spacing: -.01em; color:#f8fafc; }
  .rhS-card__sub{ color: rgba(226,232,240,.68); font-size: 13px; }
  .rhS-card__body{ padding: 16px; }

  .rhS-label{
    font-size: 12px;
    font-weight: 800;
    color: rgba(226,232,240,.70);
    letter-spacing:.06em;
    text-transform: uppercase;
  }

  .rhS-input{
    border-radius: 14px;
    background: rgba(15,23,42,.70) !important;
    border: 1px solid rgba(148,163,184,.22) !important;
    color: rgba(226,232,240,.92) !important;
  }
  .rhS-input::placeholder{
    color: rgba(226,232,240,.45) !important;
  }
  .rhS-input:disabled{
    background: rgba(15,23,42,.48) !important;
    opacity: .9;
  }

  .rhS-help{ color: rgba(226,232,240,.55); font-size: 12px; }

  .rhS-picker{
    position: relative;
  }

  .rhS-picker__control{
    position: relative;
    display: flex;
    align-items: center;
  }

  .rhS-picker__input{
    padding-right: 52px !important;
  }

  .rhS-picker__toggle{
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 36px;
    height: 36px;
    border-radius: 12px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(15,23,42,.9);
    color: rgba(226,232,240,.82);
    font-weight: 900;
    cursor: pointer;
    transition: .18s ease;
  }

  .rhS-picker__toggle:hover{
    background: rgba(59,130,246,.14);
    border-color: rgba(59,130,246,.26);
    color: #fff;
  }

  .rhS-picker__selected{
    margin-top: 10px;
    padding: 12px 14px;
    border-radius: 14px;
    border: 1px solid rgba(16,185,129,.22);
    background: rgba(16,185,129,.10);
  }

  .rhS-picker__selectedMain{
    color: #ecfeff;
    font-weight: 800;
    line-height: 1.2;
  }

  .rhS-picker__selectedSub{
    margin-top: 4px;
    font-size: 12px;
    color: rgba(209,250,229,.86);
  }

  .rhS-picker__menu{
    position: absolute;
    top: calc(100% + 10px);
    left: 0;
    right: 0;
    z-index: 50;
    border-radius: 18px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(10,15,28,.98);
    box-shadow: 0 24px 60px rgba(0,0,0,.40);
    overflow: hidden;
    backdrop-filter: blur(10px);
  }

  .rhS-picker__hint{
    padding: 12px 14px;
    border-bottom: 1px solid rgba(148,163,184,.10);
    font-size: 12px;
    color: rgba(148,163,184,.74);
    background: rgba(15,23,42,.55);
  }

  .rhS-picker__list{
    max-height: 320px;
    overflow: auto;
  }

  .rhS-picker__item{
    width: 100%;
    border: 0;
    background: transparent;
    text-align: left;
    padding: 12px 14px;
    border-bottom: 1px solid rgba(148,163,184,.08);
    cursor: pointer;
    transition: .15s ease;
  }

  .rhS-picker__item:last-child{
    border-bottom: 0;
  }

  .rhS-picker__item:hover,
  .rhS-picker__item.is-active{
    background: rgba(59,130,246,.14);
  }

  .rhS-picker__itemName{
    color: #f8fafc;
    font-weight: 800;
    line-height: 1.25;
  }

  .rhS-picker__itemMeta{
    margin-top: 4px;
    color: rgba(226,232,240,.68);
    font-size: 12px;
    line-height: 1.35;
  }

  .rhS-picker__empty{
    padding: 16px 14px;
    color: rgba(148,163,184,.78);
    font-size: 13px;
  }

  .rhS-autoGrid{
    display:grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
  }
  .rhS-auto__label{
    font-size: 12px;
    font-weight: 800;
    color: rgba(226,232,240,.60);
    letter-spacing:.06em;
    text-transform: uppercase;
    margin-bottom: 6px;
  }

  .rhS-alertBox{
    border-radius:14px;
  }

  .rhS-warningLine{
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px 14px;
    border-radius:14px;
    border:1px solid rgba(245,158,11,.24);
    background: rgba(245,158,11,.10);
    color:#fcd34d;
    font-weight:700;
  }
  .rhS-warningLine__icon{
    font-size:16px;
    line-height:1;
  }

  .rhS-tableWrap{
    overflow-x:auto;
  }

  .rhS-table{
    margin: 0;
    color: rgba(241,245,249,.96) !important;
    --bs-table-bg: transparent;
    --bs-table-striped-bg: transparent;
    --bs-table-hover-bg: rgba(255,255,255,.03);
    --bs-table-border-color: rgba(148,163,184,.10);
  }

  .rhS-table thead th{
    background: rgba(15,23,42,.92) !important;
    color: rgba(226,232,240,.80) !important;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    border-bottom: 1px solid rgba(148,163,184,.16) !important;
    padding: 14px 12px;
    white-space: nowrap;
  }

  .rhS-table tbody td{
    background: rgba(2,6,23,.56) !important;
    border-top: 1px solid rgba(148,163,184,.08) !important;
    padding: 14px 12px;
    vertical-align: middle;
    color: rgba(241,245,249,.96) !important;
  }

  .rhS-table tbody tr:hover td{
    background: rgba(15,23,42,.74) !important;
  }

  .rhS-person{
    display:flex;
    align-items:flex-start;
    gap: 12px;
    min-width: 260px;
  }

  .rhS-avatar{
    width: 40px;
    height: 40px;
    border-radius: 14px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight: 900;
    color: #f8fafc;
    background:
      radial-gradient(22px 22px at 30% 25%, rgba(59,130,246,.25), transparent 60%),
      radial-gradient(22px 22px at 70% 75%, rgba(16,185,129,.18), transparent 60%),
      rgba(30,41,59,.96);
    border: 1px solid rgba(148,163,184,.14);
    flex: 0 0 auto;
  }

  .rhS-person__meta{ min-width: 220px; }
  .rhS-person__name{
    font-weight: 900;
    color:#f8fafc;
    line-height: 1.1;
  }
  .rhS-person__sub{
    font-size: 12px;
    color: rgba(226,232,240,.68);
    margin-top: 3px;
  }

  .rhS-mainText{
    color:#f8fafc;
    font-weight:700;
  }

  .rhS-subText{
    font-size:12px;
    color: rgba(226,232,240,.60);
    margin-top: 4px;
  }

  .rhS-date{
    color:#f8fafc;
    font-weight:600;
    white-space: nowrap;
  }

  .rhS-detail{
    font-size: 12px;
    color: rgba(226,232,240,.82);
    white-space: normal;
    max-width: 360px;
    line-height: 1.5;
  }

  .rhS-dash{
    color: rgba(148,163,184,.72);
  }

  .st{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 12px;
    border: 1px solid rgba(148,163,184,.14);
    background: rgba(15,23,42,.74);
    color: rgba(226,232,240,.84);
    white-space: nowrap;
  }
  .st--warn{ background: rgba(245,158,11,.12); border-color: rgba(245,158,11,.25); color: #fbbf24; }
  .st--bad{ background: rgba(239,68,68,.10); border-color: rgba(239,68,68,.22); color: #f87171; }
  .st--soft{ background: rgba(148,163,184,.14); border-color: rgba(148,163,184,.28); color: rgba(226,232,240,.80); }

  .copy{
    cursor: pointer;
    font-weight: 700;
    padding: 6px 10px;
    border-radius: 10px;
    border: 1px solid rgba(148,163,184,.18);
    background: rgba(15,23,42,.72);
    color: rgba(226,232,240,.92);
    display:inline-block;
    white-space: nowrap;
  }
  .copy:hover{
    background: rgba(59,130,246,.12);
    border-color: rgba(59,130,246,.25);
  }

  .rhS-footerHint{
    padding: 12px 16px 16px;
    border-top: 1px solid rgba(148,163,184,.14);
    color: rgba(226,232,240,.65);
    font-size: 12px;
  }

  @media (max-width: 992px){
    .rhS-autoGrid{ grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }

  @media (max-width: 768px){
    .rhS-wrap{ padding: 14px 14px 34px; }
    .rhS-title{ font-size: 24px; }
    .rhS-table thead th:nth-child(4),
    .rhS-table thead th:nth-child(5),
    .rhS-table tbody td:nth-child(4),
    .rhS-table tbody td:nth-child(5){
      display:none;
    }
  }

  @media (max-width: 520px){
    .rhS-autoGrid{ grid-template-columns: 1fr; }
  }
</style>

<script>
  (function(){
    const sel = document.getElementById('militarSelect');
    const adm = document.getElementById('autoAdmissao');
    const pro = document.getElementById('autoPromocao');
    const ser = document.getElementById('autoSerial');
    const dis = document.getElementById('autoDiscord');

    const picker = document.getElementById('militarPicker');
    const search = document.getElementById('militarSearch');
    const menu = document.getElementById('militarMenu');
    const list = document.getElementById('militarList');
    const toggle = document.getElementById('militarToggle');
    const selected = document.getElementById('militarSelected');
    const selectedMain = document.getElementById('militarSelectedMain');
    const selectedSub = document.getElementById('militarSelectedSub');

    function fillAuto() {
      if (!sel) return;
      const opt = sel.options[sel.selectedIndex];

      if (!opt || !opt.value) {
        if (adm) adm.value = '';
        if (pro) pro.value = '';
        if (ser) ser.value = '';
        if (dis) dis.value = '';
        if (selected) selected.hidden = true;
        return;
      }

      if (adm) adm.value = opt.getAttribute('data-admissao') || '-';
      if (pro) pro.value = opt.getAttribute('data-promocao') || '-';
      if (ser) ser.value = opt.getAttribute('data-serial') || '-';
      if (dis) dis.value = opt.getAttribute('data-discord') || '-';

      if (selected) selected.hidden = false;
      if (selectedMain) selectedMain.textContent = opt.getAttribute('data-nome') || opt.textContent.trim();
      if (selectedSub) {
        selectedSub.textContent =
          (opt.getAttribute('data-cargo') || '—') +
          ' • CPF/RG: ' +
          (opt.getAttribute('data-cpf') || '-');
      }
    }

    function getOptions() {
      if (!sel) return [];
      return Array.from(sel.options)
        .filter(opt => opt.value)
        .map(opt => ({
          value: opt.value,
          nome: opt.getAttribute('data-nome') || '',
          cpf: opt.getAttribute('data-cpf') || '',
          cargo: opt.getAttribute('data-cargo') || '',
          admissao: opt.getAttribute('data-admissao') || '',
          promocao: opt.getAttribute('data-promocao') || '',
          serial: opt.getAttribute('data-serial') || '',
          discord: opt.getAttribute('data-discord') || '',
          label: opt.textContent.trim()
        }));
    }

    function openMenu() {
      if (menu) menu.hidden = false;
      if (picker) picker.classList.add('is-open');
    }

    function closeMenu() {
      if (menu) menu.hidden = true;
      if (picker) picker.classList.remove('is-open');
    }

    function renderOptions(term = '') {
      if (!list) return;
      const q = term.trim().toLowerCase();

      const items = getOptions().filter(item => {
        const hay = `${item.nome} ${item.cpf} ${item.cargo} ${item.label}`.toLowerCase();
        return hay.includes(q);
      });

      if (!items.length) {
        list.innerHTML = `<div class="rhS-picker__empty">Nenhum militar encontrado.</div>`;
        return;
      }

      list.innerHTML = items.map(item => `
        <button type="button" class="rhS-picker__item" data-value="${item.value}">
          <div class="rhS-picker__itemName">${item.nome || '—'}</div>
          <div class="rhS-picker__itemMeta">${item.cargo || '—'} • CPF/RG: ${item.cpf || '-'} ${item.serial ? '• Serial: ' + item.serial : ''}</div>
        </button>
      `).join('');
    }

    function selectValue(value) {
      if (!sel) return;
      sel.value = value;
      fillAuto();

      const opt = sel.options[sel.selectedIndex];
      if (search && opt) {
        search.value = opt.getAttribute('data-nome') || '';
      }

      closeMenu();
    }

    if (sel && search && menu && list) {
      renderOptions();

      search.addEventListener('focus', () => {
        renderOptions(search.value);
        openMenu();
      });

      search.addEventListener('input', () => {
        renderOptions(search.value);
        openMenu();
      });

      if (toggle) {
        toggle.addEventListener('click', () => {
          if (menu.hidden) {
            renderOptions(search.value);
            openMenu();
            search.focus();
          } else {
            closeMenu();
          }
        });
      }

      list.addEventListener('click', (e) => {
        const item = e.target.closest('.rhS-picker__item');
        if (!item) return;
        selectValue(item.getAttribute('data-value'));
      });

      document.addEventListener('click', (e) => {
        if (!picker || picker.contains(e.target)) return;
        closeMenu();
      });

      fillAuto();
    }

    document.addEventListener('click', async (e) => {
      const el = e.target.closest('.copy');
      if (!el) return;
      const text = el.getAttribute('data-copy') || el.textContent.trim();
      try{
        await navigator.clipboard.writeText(text);
        const old = el.textContent;
        el.textContent = 'Copiado!';
        setTimeout(() => el.textContent = old, 800);
      }catch(err){
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
      }
    });
  })();
</script>
@endsection