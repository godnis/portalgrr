@extends('layouts.app')

@section('content')
@php
  $auth = auth()->user();
  $authNivel = (int)($auth->nivel ?? 0);

  $canManage = $auth && $authNivel >= 9;
  $canDelete = $auth && $authNivel >= 10;

  $badge = match($user->status){
    'ativo' => 'text-bg-success',
    'suspenso' => 'text-bg-warning',
    'desligado' => 'text-bg-secondary',
    default => 'text-bg-light'
  };

  $partesNome = preg_split('/\s+/', trim((string) $user->name));
  $iniciais = '';
  foreach (array_slice($partesNome, 0, 2) as $parte) {
    $iniciais .= mb_strtoupper(mb_substr($parte, 0, 1));
  }
  $iniciais = $iniciais ?: 'OF';

  $logsCount = isset($logs) ? $logs->count() : 0;
@endphp

<style>
  .efs-wrap{
    --efs-blue: #60a5fa;
    --efs-blue-strong: #3b82f6;
    --efs-blue-soft: rgba(96,165,250,.12);

    --efs-gold: #f5c542;
    --efs-gold-soft: rgba(245,197,66,.12);

    --efs-green: #22c55e;
    --efs-yellow: #facc15;
    --efs-red: #ef4444;

    --efs-text: #e5edf7;
    --efs-text-soft: rgba(229,237,247,.72);
    --efs-text-faint: rgba(229,237,247,.54);

    --efs-border: rgba(148,163,184,.16);
    --efs-border-strong: rgba(148,163,184,.24);

    --efs-bg-main: #020817;
    --efs-bg-card: linear-gradient(180deg, rgba(6,12,24,.96), rgba(4,10,19,.98));
    --efs-bg-card-2: linear-gradient(180deg, rgba(8,15,29,.98), rgba(5,11,21,.98));
    --efs-bg-soft: rgba(15,23,42,.72);
    --efs-bg-soft-2: rgba(15,23,42,.88);
    --efs-bg-row: rgba(255,255,255,.01);
    --efs-bg-row-alt: rgba(96,165,250,.022);
    --efs-bg-row-hover: rgba(96,165,250,.06);
    --efs-table-head: rgba(255,255,255,.03);

    --efs-shadow-sm: 0 10px 30px rgba(0,0,0,.22);
    --efs-shadow-md: 0 24px 60px rgba(0,0,0,.34);
  }

  .efs-page{
    max-width: 1280px;
    margin: 0 auto;
  }

  .efs-hero{
    position: relative;
    overflow: hidden;
    padding: 30px 28px 24px;
    border-radius: 28px;
    border: 1px solid rgba(96,165,250,.14);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.16), transparent 28%),
      radial-gradient(circle at left bottom, rgba(245,197,66,.10), transparent 24%),
      linear-gradient(135deg, rgba(7,14,27,.98), rgba(3,8,16,.98));
    box-shadow: var(--efs-shadow-md);
  }

  .efs-hero::after{
    content:"";
    position:absolute;
    inset:0;
    pointer-events:none;
    background: linear-gradient(90deg, transparent, rgba(96,165,250,.05), transparent);
    mix-blend-mode: screen;
  }

  .efs-kicker{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:9px 13px;
    border-radius:999px;
    font-size:11px;
    font-weight:900;
    letter-spacing:.16em;
    text-transform:uppercase;
    color: var(--efs-blue);
    border:1px solid rgba(96,165,250,.18);
    background: rgba(59,130,246,.10);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
  }

  .efs-title{
    font-size: clamp(1.8rem, 2.4vw, 2.6rem);
    line-height: 1.02;
    font-weight: 900;
    color: var(--efs-text);
    margin: 16px 0 10px;
  }

  .efs-sub{
    color: var(--efs-text-soft);
    font-weight: 600;
    max-width: 860px;
    font-size: 15px;
  }

  .efs-meta{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-top:18px;
  }

  .efs-chip{
    display:inline-flex;
    align-items:center;
    gap:8px;
    min-height:40px;
    padding:8px 14px;
    border-radius:999px;
    border:1px solid var(--efs-border);
    background: rgba(255,255,255,.03);
    color: var(--efs-text);
    font-size:13px;
    font-weight:800;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.02);
  }

  .efs-btn{
    min-height:46px;
    border-radius:15px !important;
    font-weight:900 !important;
    padding-inline:18px;
    box-shadow:none !important;
  }

  .efs-btn-primary{
    background: linear-gradient(135deg, #60a5fa, #3b82f6);
    color:#06101f !important;
    border:none !important;
  }

  .efs-btn-primary:hover{
    filter: brightness(1.04);
  }

  .efs-btn-soft{
    border:1px solid var(--efs-border-strong) !important;
    background: rgba(255,255,255,.03) !important;
    color: var(--efs-text) !important;
  }

  .efs-card{
    border:1px solid var(--efs-border);
    border-radius:24px;
    background: var(--efs-bg-card);
    box-shadow: var(--efs-shadow-sm);
    overflow:hidden;
    backdrop-filter: blur(8px);
  }

  .efs-card-body{
    padding:22px;
  }

  .efs-section-title{
    font-size:13px;
    font-weight:900;
    letter-spacing:.16em;
    text-transform:uppercase;
    color: #cbd5e1;
    margin-bottom:16px;
  }

  .efs-profile{
    display:flex;
    align-items:center;
    gap:16px;
    margin-bottom:18px;
  }

  .efs-avatar{
    width:74px;
    height:74px;
    border-radius:22px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    flex:0 0 74px;
    font-size:24px;
    font-weight:900;
    color:#8dc2ff;
    background:
      linear-gradient(135deg, rgba(59,130,246,.18), rgba(59,130,246,.04));
    border:1px solid rgba(96,165,250,.18);
    box-shadow:
      inset 0 1px 0 rgba(255,255,255,.04),
      0 10px 24px rgba(59,130,246,.08);
  }

  .efs-profile__name{
    font-size:1.8rem;
    font-weight:900;
    color: var(--efs-text);
    line-height:1.05;
    margin-bottom:6px;
  }

  .efs-profile__sub{
    color: var(--efs-text-soft);
    font-size:14px;
    font-weight:700;
    line-height:1.25;
  }

  .efs-grid{
    display:grid;
    grid-template-columns:1fr;
    gap:14px;
  }

  .efs-mini{
    border:1px solid var(--efs-border);
    border-radius:18px;
    padding:16px;
    background: var(--efs-bg-card-2);
  }

  .efs-mini__label{
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.14em;
    font-weight:900;
    color: var(--efs-text-faint);
    margin-bottom:8px;
  }

  .efs-mini__value{
    font-size:1.12rem;
    font-weight:900;
    color: var(--efs-text);
    line-height:1.25;
  }

  .efs-mini__sub{
    margin-top:7px;
    color: var(--efs-text-soft);
    font-size:12px;
    font-weight:700;
  }

  .efs-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:18px;
  }

  .efs-info-note{
    border:1px solid var(--efs-border);
    background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.02));
    border-radius:18px;
    color: var(--efs-text-soft);
    font-size:13px;
    font-weight:700;
    padding:14px 16px;
  }

  .efs-table-head{
    padding:20px 22px;
    border-bottom:1px solid var(--efs-border);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.12), transparent 22%),
      linear-gradient(180deg, rgba(255,255,255,.025), rgba(255,255,255,.015));
  }

  .efs-table-title{
    font-weight:900;
    color: var(--efs-text);
    margin-bottom:4px;
    font-size: 1.1rem;
  }

  .efs-table-sub{
    color: var(--efs-text-soft);
    font-size:13px;
    font-weight:600;
  }

  .efs-table-meta{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
  }

  .efs-mini-chip{
    display:inline-flex;
    align-items:center;
    gap:8px;
    min-height:38px;
    padding:8px 13px;
    border-radius:999px;
    border:1px solid var(--efs-border);
    background: rgba(255,255,255,.04);
    color: var(--efs-text);
    font-size:12px;
    font-weight:800;
  }

  .efs-table-wrap{
    background: transparent;
  }

  .efs-table{
    width:100%;
    margin-bottom:0;
    color: var(--efs-text) !important;
    background: transparent !important;
    --bs-table-color: var(--efs-text);
    --bs-table-bg: transparent;
    --bs-table-border-color: rgba(148,163,184,.12);
    --bs-table-striped-color: var(--efs-text);
    --bs-table-striped-bg: transparent;
    --bs-table-hover-color: var(--efs-text);
    --bs-table-hover-bg: transparent;
  }

  .efs-table thead th{
    background: var(--efs-table-head) !important;
    color: rgba(229,237,247,.62) !important;
    border-bottom:1px solid var(--efs-border) !important;
    letter-spacing:.12em;
    text-transform:uppercase;
    font-size:11px;
    font-weight:900;
    padding:16px 14px;
    white-space:nowrap;
  }

  .efs-table tbody tr{
    background: var(--efs-bg-row) !important;
    transition: .18s ease;
  }

  .efs-table tbody tr:nth-child(even){
    background: var(--efs-bg-row-alt) !important;
  }

  .efs-table tbody td{
    padding:18px 14px;
    border-top:1px solid rgba(255,255,255,.05) !important;
    vertical-align:middle;
    background:transparent !important;
    color:var(--efs-text) !important;
  }

  .efs-table tbody tr:hover{
    background: var(--efs-bg-row-hover) !important;
  }

  .efs-log-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:36px;
    padding:8px 13px;
    border-radius:999px;
    background: rgba(96,165,250,.10);
    color: #9ec5ff;
    border: 1px solid rgba(96,165,250,.15);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .05em;
  }

  .efs-empty{
    padding:46px 16px !important;
    color: var(--efs-text-soft) !important;
    font-weight:700;
  }

  .efs-modal .modal-content{
    border:1px solid var(--efs-border);
    border-radius:22px;
    box-shadow: var(--efs-shadow-md);
    overflow:hidden;
    background: linear-gradient(180deg, rgba(7,13,24,.98), rgba(4,9,17,.98));
    color: var(--efs-text);
  }

  .efs-modal .modal-header{
    border-bottom:1px solid var(--efs-border);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.10), transparent 30%),
      linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,.015));
  }

  .efs-modal .modal-footer{
    border-top:1px solid var(--efs-border);
    background: rgba(255,255,255,.02);
  }

  .efs-input{
    border-radius:14px !important;
    min-height:48px;
    border:1px solid var(--efs-border-strong) !important;
    background: rgba(255,255,255,.03) !important;
    box-shadow:none !important;
    font-weight:700;
    color: var(--efs-text) !important;
  }

  .efs-input::placeholder{
    color: rgba(229,237,247,.34);
  }

  .efs-input:focus{
    border-color: rgba(96,165,250,.32) !important;
    box-shadow: 0 0 0 .2rem rgba(59,130,246,.12) !important;
  }

  .efs-wrap .alert-success{
    background: rgba(34,197,94,.12) !important;
    color: #bbf7d0 !important;
    border: 1px solid rgba(34,197,94,.18) !important;
  }

  .efs-wrap .alert-danger{
    background: rgba(239,68,68,.12) !important;
    color: #fecaca !important;
    border: 1px solid rgba(239,68,68,.18) !important;
  }

  .efs-wrap .alert-light{
    background: rgba(255,255,255,.03) !important;
    color: var(--efs-text-soft) !important;
    border: 1px solid var(--efs-border) !important;
  }

  .efs-wrap .text-muted{
    color: var(--efs-text-soft) !important;
  }

  .efs-wrap .badge.text-bg-light{
    background: rgba(255,255,255,.08) !important;
    color: var(--efs-text) !important;
    border-color: rgba(148,163,184,.20) !important;
  }

  .efs-wrap .btn-warning{
    background: linear-gradient(135deg, #facc15, #eab308);
    border: none !important;
    color: #111827 !important;
  }

  .efs-wrap .btn-success{
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border: none !important;
    color: #04110a !important;
  }

  .efs-wrap .btn-outline-danger{
    border-color: rgba(239,68,68,.35) !important;
    color: #fca5a5 !important;
    background: rgba(239,68,68,.04) !important;
  }

  .efs-wrap .btn-outline-danger:hover{
    background: rgba(239,68,68,.10) !important;
    color: #fff !important;
  }

  .efs-wrap .btn-close{
    filter: invert(1) grayscale(1) brightness(200%);
    opacity: .85;
  }

  @media (max-width: 767.98px){
    .efs-hero{
      padding:20px;
    }

    .efs-card-body,
    .efs-table-head{
      padding:16px;
    }

    .efs-profile{
      align-items:flex-start;
    }

    .efs-avatar{
      width:58px;
      height:58px;
      flex-basis:58px;
      border-radius:18px;
      font-size:18px;
    }

    .efs-profile__name{
      font-size: 1.45rem;
    }
  }
</style>

<div class="container-fluid py-3 efs-wrap">
  <div class="efs-page">

    {{-- HERO --}}
    <div class="efs-hero mb-4">
      <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
        <div>
          <div class="efs-kicker">GRR • PRF • Ficha do efetivo</div>
          <h1 class="efs-title">Ficha do oficial</h1>
          <div class="efs-sub">
            Visualização completa do cadastro, status operacional e histórico recente de auditoria do oficial.
            @if(!$canManage)
              <span class="d-inline-block mt-1">Ações administrativas permanecem restritas para nível 9+.</span>
            @endif
          </div>

          <div class="efs-meta">
            <span class="efs-chip">ID: <strong>{{ $user->id }}</strong></span>
            <span class="efs-chip">Discord: <strong>{{ $user->discord }}</strong></span>
            <span class="efs-chip">Nível: <strong>{{ $user->nivel ?? '—' }}</strong></span>
            <span class="efs-chip">Status: <strong>{{ strtoupper($user->status ?? '—') }}</strong></span>
          </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('efetivo.index') }}" class="btn efs-btn efs-btn-soft">
            Voltar
          </a>

          @if($canManage)
            <a href="{{ route('efetivo.edit', $user->id) }}" class="btn efs-btn efs-btn-primary">
              Editar
            </a>
          @endif
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    <div class="row g-4">
      {{-- FICHA --}}
      <div class="col-lg-5">
        <div class="efs-card">
          <div class="efs-card-body">

            <div class="efs-section-title">Dados do oficial</div>

            <div class="efs-profile">
              <div class="efs-avatar">{{ $iniciais }}</div>

              <div>
                <div class="efs-profile__name">{{ $user->name }}</div>
                <div class="efs-profile__sub">RG: {{ $user->rg }}</div>
                <div class="efs-profile__sub">ID interno: {{ $user->id }}</div>
              </div>
            </div>

            <div class="efs-grid">
              <div class="efs-mini">
                <div class="efs-mini__label">Cargo</div>
                <div class="efs-mini__value">{{ $user->cargo ?? '—' }}</div>
                <div class="efs-mini__sub">Função atual registrada no sistema.</div>
              </div>

              <div class="efs-mini">
                <div class="efs-mini__label">Nível</div>
                <div class="efs-mini__value">{{ $user->nivel ?? '—' }}</div>
                <div class="efs-mini__sub">Posição hierárquica do oficial.</div>
              </div>

              <div class="efs-mini">
                <div class="efs-mini__label">Status</div>
                <div class="efs-mini__value">
                  <span class="badge rounded-pill {{ $badge }} px-3 py-2" style="font-weight:900;">
                    {{ strtoupper($user->status ?? '—') }}
                  </span>
                </div>
                <div class="efs-mini__sub">Condição atual do oficial no efetivo.</div>
              </div>

              <div class="efs-mini">
                <div class="efs-mini__label">Motivo da Suspenção</div>
                <div class="efs-mini__value">{{ $user->motivo_suspensao ?? '—' }}</div>
                <div class="efs-mini__sub">Justificativa para a suspensão do oficial.</div>
              </div>
            </div>

            <div class="efs-actions">
              @if($canManage)
                @if((string)$user->status !== 'ativo')
                  <form method="POST" action="{{ route('efetivo.reativar', $user->id) }}" class="m-0">
                    @csrf
                    <button class="btn efs-btn btn-success" style="min-height:40px;">
                      Reativar
                    </button>
                  </form>
                @else
                  <button class="btn efs-btn btn-warning" style="min-height:40px;" data-bs-toggle="modal" data-bs-target="#suspenderModal">
                    Suspender
                  </button>
                @endif

                @if($canDelete)
                  <button class="btn efs-btn btn-outline-danger" style="min-height:40px;" data-bs-toggle="modal" data-bs-target="#removerModal">
                    Remover
                  </button>
                @endif
              @else
                <div class="efs-info-note w-100">
                  Você pode visualizar a ficha e o histórico. Para editar, suspender, reativar ou remover, é necessário nível 9+.
                </div>
              @endif
            </div>

          </div>
        </div>
      </div>

      {{-- AUDITORIA --}}
      <div class="col-lg-7">
        <div class="efs-card">
          <div class="efs-table-head d-flex align-items-start justify-content-between gap-3 flex-wrap">
            <div>
              <div class="efs-table-title">Histórico de auditoria</div>
              <div class="efs-table-sub">Mostrando os 50 registros mais recentes vinculados a este oficial.</div>
            </div>

            <div class="efs-table-meta">
              <span class="efs-mini-chip">Registros: <strong>{{ $logsCount }}</strong></span>
              <span class="efs-mini-chip">Entidade: <strong>User</strong></span>
            </div>
          </div>

          <div class="table-responsive efs-table-wrap">
            <table class="table table-sm align-middle efs-table">
              <thead>
                <tr>
                  <th style="width:180px;">Data/Hora</th>
                  <th style="width:240px;">Ação registrada</th>
                  <th>Entidade</th>
                  <th style="width:90px;">ID</th>
                </tr>
              </thead>
              <tbody>
                @forelse($logs as $l)
                  <tr>
                    <td class="fw-semibold">
                      {{ optional($l->created_at)->format('d/m/Y H:i:s') ?? '—' }}
                    </td>

                    <td>
                      <span class="efs-log-badge">
                        {{ $l->acao ?? '—' }}
                      </span>
                    </td>

                    <td class="fw-semibold">{{ $l->entidade_tipo ?? '—' }}</td>
                    <td class="fw-semibold">{{ $l->entidade_id ?? '—' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center efs-empty">
                      Sem registros de auditoria para este oficial.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

{{-- MODAL SUSPENDER --}}
@if($canManage)
  <div class="modal fade efs-modal" id="suspenderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-black">Suspender oficial</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <form method="POST" action="{{ route('efetivo.suspender', $user->id) }}">
          @csrf
          <div class="modal-body p-4">
            <div class="text-muted fw-semibold mb-2">
              Informe o motivo da suspensão do oficial <span class="fw-black">{{ $user->name }}</span>.
            </div>

            <input
              name="motivo"
              class="form-control efs-input"
              required
              maxlength="200"
              placeholder="Ex.: conduta, ausência, decisão administrativa..."
            >
          </div>

          <div class="modal-footer">
            <button type="button" class="btn efs-btn efs-btn-soft" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn efs-btn btn-warning">Suspender</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif

{{-- MODAL REMOVER --}}
@if($canDelete)
  <div class="modal fade efs-modal" id="removerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-black">Remover oficial</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>

        <form method="POST" action="{{ route('efetivo.destroy', $user->id) }}">
          @csrf
          @method('DELETE')

          <div class="modal-body p-4">
            <div class="alert alert-danger rounded-4 mb-3">
              <b>Atenção:</b> esta ação remove o oficial do sistema. O motivo será registrado na auditoria.
            </div>

            <input
              name="motivo"
              class="form-control efs-input"
              required
              maxlength="200"
              placeholder="Motivo da remoção..."
            >
          </div>

          <div class="modal-footer">
            <button type="button" class="btn efs-btn efs-btn-soft" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn efs-btn btn-outline-danger">Remover</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif
@endsection