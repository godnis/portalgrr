@extends('layouts.app')

@section('content')
@php
  $status = (string)($ticket->status ?? 'aberto');

  $map = [
    'aberto'             => ['warning', '#f59f00', 'Aberto', 'tadm-show-status--open'],
    'em_andamento'       => ['primary', '#0d6efd', 'Em andamento', 'tadm-show-status--progress'],
    'aguardando_usuario' => ['secondary', '#6c757d', 'Aguardando usuário', 'tadm-show-status--waiting'],
    'resolvido'          => ['success', '#198754', 'Resolvido', 'tadm-show-status--resolved'],
    'fechado'            => ['dark', '#212529', 'Fechado', 'tadm-show-status--closed'],
  ];
  $m = $map[$status] ?? ['secondary', '#6c757d', strtoupper($status), 'tadm-show-status--default'];

  $blocked = in_array($status, ['fechado'], true);

  $meId = (int) auth()->id();
  $assignedTo = (int) ($ticket->assigned_to ?? 0);
  $isMine = $assignedTo === $meId;

  $createdAt = optional($ticket->created_at)->format('d/m/Y H:i');
  $lastMsgAt = optional($ticket->lastActivityAt())->format('d/m/Y H:i');
@endphp

<style>
  /* =========================================================
     GRR 3.0 • TICKETS ADMIN SHOW
  ========================================================= */
  .tadm-show-wrap{
    max-width: 1280px;
    margin: 0 auto;
    padding: 18px 14px 28px;
  }

  /* HERO */
  .tadm-show-hero{
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    border: 1px solid rgba(2,6,23,.10);
    background: linear-gradient(135deg, rgba(8,14,28,.98), rgba(15,23,42,.94));
    box-shadow:
      0 24px 70px rgba(2,6,23,.18),
      inset 0 1px 0 rgba(255,255,255,.05);
  }

  .tadm-show-hero__bg{
    position: absolute;
    inset: -60px;
    pointer-events: none;
    opacity: .95;
    background:
      radial-gradient(850px 340px at 12% 18%, rgba(59,130,246,.24), transparent 60%),
      radial-gradient(680px 280px at 88% 25%, rgba(16,185,129,.16), transparent 56%),
      radial-gradient(760px 320px at 50% 110%, rgba(168,85,247,.14), transparent 62%);
    filter: blur(14px);
  }

  .tadm-show-hero__content{
    position: relative;
    z-index: 1;
    padding: 22px;
    display: flex;
    gap: 18px;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
  }

  .tadm-show-kicker{
    color: rgba(226,232,240,.68);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .18em;
    text-transform: uppercase;
    margin-bottom: 10px;
  }

  .tadm-show-title{
    margin: 0;
    color: #f8fafc;
    font-size: clamp(1.45rem, 2.3vw, 2.2rem);
    line-height: 1.08;
    font-weight: 950;
    letter-spacing: -.02em;
  }

  .tadm-show-sub{
    margin-top: 10px;
    color: rgba(226,232,240,.78);
    font-size: .96rem;
    line-height: 1.6;
    font-weight: 500;
  }

  .tadm-show-sub b{
    color: rgba(248,250,252,.96);
  }

  .tadm-show-hero__right{
    display: flex;
    align-items: flex-start;
    gap: 10px;
    flex-wrap: wrap;
  }

  .tadm-show-back{
    border-radius: 14px;
    font-weight: 900;
    padding: 11px 16px;
    border-color: rgba(255,255,255,.18);
  }

  .tadm-show-back:hover{
    border-color: rgba(255,255,255,.28);
    background: rgba(255,255,255,.08);
    color: #fff;
  }

  .tadm-show-assume,
  .tadm-show-chip{
    min-height: 42px;
    border-radius: 999px;
    font-weight: 900;
    padding-inline: 16px;
  }

  .tadm-show-chip{
    display: inline-flex;
    align-items: center;
    border: 1px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.08);
    color: #f8fafc;
    font-size: 12px;
    letter-spacing: .08em;
    text-transform: uppercase;
    backdrop-filter: blur(10px);
  }

  /* STATUS */
  .tadm-show-status{
    display: inline-flex;
    align-items: center;
    gap: 9px;
    min-height: 42px;
    padding: 0 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
    border: 1px solid rgba(255,255,255,.10);
    background: rgba(255,255,255,.08);
    color: #f8fafc;
    backdrop-filter: blur(10px);
  }

  .tadm-show-dot{
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 4px rgba(255,255,255,.06);
  }

  .tadm-show-status--open{
    background: rgba(245,159,0,.14);
    border-color: rgba(245,159,0,.28);
  }

  .tadm-show-status--progress{
    background: rgba(13,110,253,.14);
    border-color: rgba(13,110,253,.28);
  }

  .tadm-show-status--waiting{
    background: rgba(108,117,125,.18);
    border-color: rgba(148,163,184,.24);
  }

  .tadm-show-status--resolved{
    background: rgba(25,135,84,.14);
    border-color: rgba(25,135,84,.26);
  }

  .tadm-show-status--closed{
    background: rgba(33,37,41,.26);
    border-color: rgba(255,255,255,.12);
  }

  .tadm-show-status--default{
    background: rgba(255,255,255,.08);
  }

  /* ALERTS */
  .tadm-show-alert{
    border-radius: 18px;
    padding: 15px 16px;
    box-shadow: 0 12px 32px rgba(15,23,42,.06);
  }

  /* GRID */
  .tadm-show-grid{
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    margin-top: 16px;
  }

  @media (min-width: 992px){
    .tadm-show-grid{
      grid-template-columns: minmax(0, 1fr) 390px;
      align-items: start;
    }
  }

  /* CARD */
  .tadm-show-card{
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(15,23,42,.08);
    background: #ffffff;
    box-shadow: 0 18px 50px rgba(15,23,42,.07);
  }

  .tadm-show-card__head{
    padding: 18px 20px;
    border-bottom: 1px solid rgba(15,23,42,.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.06), transparent 30%),
      linear-gradient(180deg, rgba(248,250,252,1), rgba(255,255,255,1));
  }

  .tadm-show-card__title{
    margin: 0;
    color: #0f172a;
    font-size: 1.02rem;
    font-weight: 900;
  }

  .tadm-show-card__sub{
    color: rgba(15,23,42,.52);
    font-size: .82rem;
    font-weight: 700;
    margin-top: 2px;
  }

  .tadm-show-card__body{
    padding: 18px;
  }

  .tadm-show-pill{
    display: inline-flex;
    align-items: center;
    min-height: 34px;
    padding: 0 12px;
    border-radius: 999px;
    border: 1px solid rgba(15,23,42,.08);
    background: rgba(15,23,42,.04);
    color: rgba(15,23,42,.76);
    font-size: 12px;
    font-weight: 900;
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  /* CHAT */
  .tadm-show-chat{
    height: 560px;
    overflow: auto;
    padding: 18px;
    background:
      linear-gradient(180deg, rgba(15,23,42,.02), rgba(15,23,42,0)),
      radial-gradient(circle at top right, rgba(59,130,246,.03), transparent 30%);
    position: relative;
    scroll-behavior: smooth;
  }

  .tadm-show-day{
    display: flex;
    justify-content: center;
    margin: 12px 0 16px;
  }

  .tadm-show-day span{
    display: inline-flex;
    align-items: center;
    min-height: 30px;
    padding: 0 12px;
    border-radius: 999px;
    border: 1px solid rgba(15,23,42,.08);
    background: #fff;
    color: rgba(15,23,42,.58);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
    box-shadow: 0 8px 24px rgba(15,23,42,.05);
  }

  .tadm-show-empty{
    min-height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: rgba(15,23,42,.56);
    font-weight: 600;
  }

  .tadm-show-msg{
    display: flex;
    margin-bottom: 14px;
  }

  .tadm-show-msg.is-me{
    justify-content: flex-end;
  }

  .tadm-show-msg.is-user,
  .tadm-show-msg.is-other{
    justify-content: flex-start;
  }

  .tadm-show-bubble{
    width: min(100%, 820px);
    max-width: 76ch;
    padding: 12px 14px;
    border-radius: 18px;
    border: 1px solid rgba(15,23,42,.08);
    background: #ffffff;
    box-shadow: 0 10px 28px rgba(15,23,42,.06);
  }

  .tadm-show-msg.is-me .tadm-show-bubble{
    border-top-right-radius: 6px;
    background: linear-gradient(180deg, rgba(13,110,253,.06), rgba(13,110,253,.03));
    border-color: rgba(13,110,253,.14);
  }

  .tadm-show-msg.is-user .tadm-show-bubble{
    border-top-left-radius: 6px;
    background: rgba(108,117,125,.05);
    border-color: rgba(108,117,125,.16);
  }

  .tadm-show-msg.is-other .tadm-show-bubble{
    border-top-left-radius: 6px;
    background: rgba(13,110,253,.04);
    border-color: rgba(13,110,253,.16);
  }

  .tadm-show-meta{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
  }

  .tadm-show-name{
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-weight: 900;
    font-size: .92rem;
    color: #0f172a;
  }

  .tadm-show-time{
    color: rgba(15,23,42,.48);
    font-size: .78rem;
    font-weight: 700;
    white-space: nowrap;
  }

  .tadm-show-text{
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    word-break: break-word;
    color: rgba(15,23,42,.88);
    line-height: 1.6;
    font-size: .95rem;
    font-weight: 500;
  }

  .tadm-show-badge-staff,
  .tadm-show-badge-user{
    display: inline-flex;
    align-items: center;
    min-height: 24px;
    padding: 0 8px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: .08em;
  }

  .tadm-show-badge-staff{
    border: 1px solid rgba(13,110,253,.25);
    color: #0d6efd;
    background: rgba(13,110,253,.06);
  }

  .tadm-show-badge-user{
    border: 1px solid rgba(108,117,125,.24);
    color: #6c757d;
    background: rgba(108,117,125,.08);
  }

  .tadm-show-fab{
    position: sticky;
    bottom: 10px;
    display: flex;
    justify-content: flex-end;
    pointer-events: none;
    margin-top: 6px;
  }

  .tadm-show-fab button{
    pointer-events: auto;
    border-radius: 999px;
    border: 1px solid rgba(15,23,42,.08);
    background: rgba(255,255,255,.92);
    padding: .45rem .80rem;
    font-size: .82rem;
    font-weight: 800;
    box-shadow: 0 12px 28px rgba(15,23,42,.10);
  }

  /* PANEL DETAILS */
  .tadm-show-kv{
    display: grid;
    gap: 12px;
  }

  .tadm-show-kv .rowx{
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
  }

  .tadm-show-kv .k{
    color: rgba(15,23,42,.54);
    font-size: .84rem;
    font-weight: 700;
  }

  .tadm-show-kv .v{
    text-align: right;
    color: #0f172a;
    font-weight: 800;
    line-height: 1.45;
  }

  .tadm-show-kv .v small{
    display: block;
    margin-top: 3px;
    font-weight: 600;
    color: rgba(15,23,42,.48);
  }

  .tadm-show-divider{
    margin: 18px 0;
    border: 0;
    border-top: 1px solid rgba(15,23,42,.08);
    opacity: 1;
  }

  .tadm-show-section-title{
    margin: 0 0 10px;
    color: #0f172a;
    font-size: 1rem;
    font-weight: 900;
  }

  .tadm-show-section-help{
    color: rgba(15,23,42,.58);
    font-size: .83rem;
    line-height: 1.5;
    margin-top: 10px;
    font-weight: 600;
  }

  .tadm-show-label{
    display: block;
    margin-bottom: 8px;
    color: #0f172a;
    font-size: .82rem;
    font-weight: 900;
  }

  .tadm-show-input{
    min-height: 46px;
    border-radius: 14px;
    border: 1px solid rgba(15,23,42,.10);
    box-shadow: none !important;
    font-weight: 600;
  }

  .tadm-show-input:focus{
    border-color: rgba(13,110,253,.42);
    box-shadow: 0 0 0 .22rem rgba(13,110,253,.10) !important;
  }

  .tadm-show-textarea{
    min-height: 128px;
    resize: vertical;
    border-radius: 16px;
    border: 1px solid rgba(15,23,42,.10);
    box-shadow: none !important;
    font-weight: 600;
  }

  .tadm-show-textarea:focus{
    border-color: rgba(13,110,253,.42);
    box-shadow: 0 0 0 .22rem rgba(13,110,253,.10) !important;
  }

  .tadm-show-btn{
    min-height: 46px;
    border-radius: 14px;
    font-weight: 900;
    padding-inline: 16px;
  }

  .tadm-show-btn-primary{
    box-shadow: 0 10px 24px rgba(13,110,253,.18);
  }

  .btn[disabled]{
    opacity: .7;
    cursor: not-allowed;
  }

  /* DARK MODE */
  html[data-theme="dark"] .tadm-show-hero{
    border-color: rgba(255,255,255,.09);
    box-shadow:
      0 26px 70px rgba(0,0,0,.45),
      inset 0 1px 0 rgba(255,255,255,.04);
  }

  html[data-theme="dark"] .tadm-show-card{
    background: rgba(10,14,20,.78);
    border-color: rgba(255,255,255,.08);
    box-shadow: 0 18px 50px rgba(0,0,0,.35);
  }

  html[data-theme="dark"] .tadm-show-card__head{
    border-bottom-color: rgba(255,255,255,.08);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.08), transparent 30%),
      linear-gradient(180deg, rgba(15,20,28,.96), rgba(10,14,20,.92));
  }

  html[data-theme="dark"] .tadm-show-card__title,
  html[data-theme="dark"] .tadm-show-name,
  html[data-theme="dark"] .tadm-show-kv .v,
  html[data-theme="dark"] .tadm-show-section-title,
  html[data-theme="dark"] .tadm-show-label{
    color: rgba(248,250,252,.95);
  }

  html[data-theme="dark"] .tadm-show-card__sub,
  html[data-theme="dark"] .tadm-show-kv .k,
  html[data-theme="dark"] .tadm-show-kv .v small,
  html[data-theme="dark"] .tadm-show-time,
  html[data-theme="dark"] .tadm-show-section-help{
    color: rgba(226,232,240,.56);
  }

  html[data-theme="dark"] .tadm-show-pill{
    background: rgba(255,255,255,.05);
    border-color: rgba(255,255,255,.08);
    color: rgba(226,232,240,.82);
  }

  html[data-theme="dark"] .tadm-show-chat{
    background:
      linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,0)),
      radial-gradient(circle at top right, rgba(59,130,246,.05), transparent 30%);
  }

  html[data-theme="dark"] .tadm-show-day span{
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.08);
    color: rgba(226,232,240,.74);
    box-shadow: none;
  }

  html[data-theme="dark"] .tadm-show-bubble{
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.09);
    box-shadow: none;
  }

  html[data-theme="dark"] .tadm-show-msg.is-me .tadm-show-bubble{
    background: rgba(13,110,253,.12);
    border-color: rgba(13,110,253,.22);
  }

  html[data-theme="dark"] .tadm-show-msg.is-user .tadm-show-bubble{
    background: rgba(108,117,125,.14);
    border-color: rgba(148,163,184,.18);
  }

  html[data-theme="dark"] .tadm-show-msg.is-other .tadm-show-bubble{
    background: rgba(13,110,253,.10);
    border-color: rgba(13,110,253,.20);
  }

  html[data-theme="dark"] .tadm-show-text{
    color: rgba(248,250,252,.88);
  }

  html[data-theme="dark"] .tadm-show-badge-staff{
    color: #93c5fd;
    background: rgba(59,130,246,.12);
    border-color: rgba(59,130,246,.22);
  }

  html[data-theme="dark"] .tadm-show-badge-user{
    color: #cbd5e1;
    background: rgba(148,163,184,.10);
    border-color: rgba(148,163,184,.18);
  }

  html[data-theme="dark"] .tadm-show-fab button{
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.10);
    color: rgba(255,255,255,.85);
    box-shadow: none;
  }

  html[data-theme="dark"] .tadm-show-divider{
    border-top-color: rgba(255,255,255,.08);
  }

  html[data-theme="dark"] .tadm-show-input,
  html[data-theme="dark"] .tadm-show-textarea{
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.10);
    color: rgba(248,250,252,.94);
  }

  html[data-theme="dark"] .tadm-show-input option{
    background: #0b1220;
    color: rgba(248,250,252,.96);
  }

  html[data-theme="dark"] .tadm-show-input::placeholder,
  html[data-theme="dark"] .tadm-show-textarea::placeholder{
    color: rgba(226,232,240,.42);
  }

  html[data-theme="dark"] .tadm-show-input:focus,
  html[data-theme="dark"] .tadm-show-textarea:focus{
    border-color: rgba(147,197,253,.45);
    box-shadow: 0 0 0 .22rem rgba(59,130,246,.12) !important;
    background: rgba(255,255,255,.05);
    color: rgba(248,250,252,.96);
  }

  /* MOBILE */
  @media (max-width: 991.98px){
    .tadm-show-hero__content{
      padding: 18px;
    }
  }

  @media (max-width: 767.98px){
    .tadm-show-wrap{
      padding: 14px 10px 24px;
    }

    .tadm-show-hero__content,
    .tadm-show-card__head,
    .tadm-show-card__body{
      padding-left: 16px;
      padding-right: 16px;
    }

    .tadm-show-chat{
      height: 460px;
      padding: 14px;
    }

    .tadm-show-kv .rowx{
      flex-direction: column;
      gap: 4px;
    }

    .tadm-show-kv .v{
      text-align: left;
    }

    .tadm-show-back{
      width: 100%;
      justify-content: center;
    }

    .tadm-show-hero__right{
      width: 100%;
    }

    .tadm-show-status,
    .tadm-show-chip{
      width: 100%;
      justify-content: center;
    }

    .tadm-show-bubble{
      max-width: 100%;
    }
  }
</style>

<div class="tadm-show-wrap">

  {{-- HERO --}}
  <section class="tadm-show-hero">
    <div class="tadm-show-hero__bg"></div>

    <div class="tadm-show-hero__content">
      <div>
        <div class="tadm-show-kicker">GRR • PRF — Painel Administrativo</div>
        <h1 class="tadm-show-title">Admin — Ticket #{{ $ticket->id }} — {{ $ticket->titulo }}</h1>
        <div class="tadm-show-sub">
          Categoria: <b>{{ $ticket->categoriaLabel() }}</b>
          • Prioridade: <b>{{ strtoupper($ticket->prioridadeLabel()) }}</b>
        </div>
      </div>

      <div class="tadm-show-hero__right">
        <span class="tadm-show-status {{ $m[3] }}">
          <span class="tadm-show-dot" style="background: {{ $m[1] }}"></span>
          {{ $m[2] }}
        </span>

        @if(!$isMine)
          <form method="POST" action="{{ route('admin.tickets.assume', $ticket) }}" class="m-0">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success tadm-show-assume">
              Assumir ticket
            </button>
          </form>
        @else
          <span class="tadm-show-chip">Você é o responsável</span>
        @endif

        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-light tadm-show-back">
          ← Voltar para tickets
        </a>
      </div>
    </div>
  </section>

  @if(session('success'))
    <div class="alert alert-success tadm-show-alert mt-3 mb-0">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger tadm-show-alert mt-3 mb-0">
      {{ session('error') }}
    </div>
  @endif

  <div class="tadm-show-grid">

    {{-- CHAT --}}
    <div class="tadm-show-card">
      <div class="tadm-show-card__head">
        <div>
          <h2 class="tadm-show-card__title">Mensagens do ticket</h2>
          <div class="tadm-show-card__sub">
            Autor: <b>{{ $ticket->user->name ?? '—' }}</b>
            • Resp.: <b>{{ $ticket->responsavel->name ?? '—' }}</b>
          </div>
        </div>
      </div>

      <div class="tadm-show-chat" id="ticketChatAdmin">
        @php $lastDay = null; @endphp

        @forelse($ticket->messages as $msg)
          @php
            $mine = (int) $msg->user_id === (int) auth()->id();
            $isStaff = (bool) ($msg->is_staff ?? false);
            $cls = $mine ? 'is-me' : ($isStaff ? 'is-other' : 'is-user');

            $day = optional($msg->created_at)->format('Y-m-d');
            $dayLabel = optional($msg->created_at)->format('d/m/Y');
          @endphp

          @if($day && $day !== $lastDay)
            <div class="tadm-show-day"><span>{{ $dayLabel }}</span></div>
            @php $lastDay = $day; @endphp
          @endif

          <div class="tadm-show-msg {{ $cls }}">
            <div class="tadm-show-bubble">
              <div class="tadm-show-meta">
                <div class="tadm-show-name">
                  {{ $msg->user->name ?? 'Usuário' }}

                  @if($isStaff)
                    <span class="tadm-show-badge-staff">EQUIPE</span>
                  @else
                    <span class="tadm-show-badge-user">USUÁRIO</span>
                  @endif
                </div>

                <div class="tadm-show-time">
                  {{ optional($msg->created_at)->format('d/m/Y H:i') }}
                </div>
              </div>

              <div class="tadm-show-text">{{ $msg->mensagem }}</div>
            </div>
          </div>
        @empty
          <div class="tadm-show-empty">
            <div>Nenhuma mensagem ainda.</div>
          </div>
        @endforelse

        <div class="tadm-show-fab">
          <button type="button" class="btn btn-sm" id="btnScrollBottomAdmin">Ir para o fim ↓</button>
        </div>
      </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="tadm-show-card">
      <div class="tadm-show-card__head">
        <div>
          <h2 class="tadm-show-card__title">Painel administrativo</h2>
          <div class="tadm-show-card__sub">Controle do atendimento e resposta</div>
        </div>

        <span class="tadm-show-pill">Nível 9+</span>
      </div>

      <div class="tadm-show-card__body">
        <div class="tadm-show-kv mb-3">
          <div class="rowx">
            <div class="k">Ticket</div>
            <div class="v">#{{ $ticket->id }}</div>
          </div>

          <div class="rowx">
            <div class="k">Criado</div>
            <div class="v">
              {{ $createdAt }}
              <small>{{ $ticket->ip ?? '—' }}</small>
            </div>
          </div>

          <div class="rowx">
            <div class="k">Última atividade</div>
            <div class="v">{{ $lastMsgAt ?? '—' }}</div>
          </div>

          <div class="rowx">
            <div class="k">Responsável</div>
            <div class="v">{{ $ticket->responsavel->name ?? '—' }}</div>
          </div>
        </div>

        <hr class="tadm-show-divider">

        <div class="mb-3">
          <h3 class="tadm-show-section-title">Atualizar status</h3>

          <form method="POST" action="{{ route('admin.tickets.status', $ticket) }}" class="d-flex gap-2 flex-wrap">
            @csrf
            @method('PATCH')

            <div class="flex-grow-1">
              <label class="tadm-show-label">Novo status</label>
              <select class="form-select tadm-show-input" name="status" required>
                <option value="aberto" @selected($ticket->status==='aberto')>Aberto</option>
                <option value="em_andamento" @selected($ticket->status==='em_andamento')>Em andamento</option>
                <option value="aguardando_usuario" @selected($ticket->status==='aguardando_usuario')>Aguardando usuário</option>
                <option value="resolvido" @selected($ticket->status==='resolvido')>Resolvido</option>
                <option value="fechado" @selected($ticket->status==='fechado')>Fechado</option>
              </select>
            </div>

            <div class="align-self-end">
              <button class="btn btn-outline-primary tadm-show-btn" type="submit">Salvar</button>
            </div>
          </form>

          <div class="tadm-show-section-help">
            Ao alterar o status, o ticket é assumido automaticamente por você.
          </div>
        </div>

        <hr class="tadm-show-divider">

        <div>
          <h3 class="tadm-show-section-title">Responder</h3>

          @if($blocked)
            <div class="alert alert-warning mb-0 tadm-show-alert">
              Ticket <b>FECHADO</b>. Para responder, altere o status primeiro.
            </div>
          @else
            <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}" id="formReplyAdmin">
              @csrf

              <label class="tadm-show-label">Mensagem administrativa</label>
              <textarea
                class="form-control tadm-show-textarea mb-2"
                name="mensagem"
                id="replyTextareaAdmin"
                placeholder="Resposta do administrador... (Enter envia • Shift+Enter quebra linha)"
                required
              ></textarea>

              <button class="btn btn-primary w-100 tadm-show-btn tadm-show-btn-primary" type="submit" id="btnSendAdmin">
                Enviar resposta
              </button>
            </form>
          @endif

          <div class="tadm-show-section-help">
            Ao responder, o ticket vai para <b>Em andamento</b> automaticamente, caso não esteja fechado.
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  (function(){
    const chat = document.getElementById('ticketChatAdmin');
    const btn  = document.getElementById('btnScrollBottomAdmin');

    function scrollBottom(){
      if (chat) chat.scrollTop = chat.scrollHeight;
    }

    scrollBottom();

    if (btn) {
      btn.addEventListener('click', scrollBottom);
    }

    const ta = document.getElementById('replyTextareaAdmin');
    const form = document.getElementById('formReplyAdmin');
    const btnSend = document.getElementById('btnSendAdmin');

    if (ta && form) {
      ta.addEventListener('keydown', function(e){
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          if (btnSend) btnSend.disabled = true;
          form.submit();
        }
      });

      form.addEventListener('submit', function(){
        if (btnSend) btnSend.disabled = true;
      });
    }
  })();
</script>
@endsection