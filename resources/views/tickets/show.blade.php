@extends('layouts.app')

@section('content')
@php
  $status = (string)($ticket->status ?? 'aberto');
  $blocked = in_array($status, ['fechado', 'resolvido'], true);

  $map = [
    'aberto'             => ['warning', '#f59f00', 'Aberto', 't-status--open'],
    'em_andamento'       => ['primary', '#0d6efd', 'Em andamento', 't-status--progress'],
    'aguardando_usuario' => ['secondary', '#6c757d', 'Aguardando usuário', 't-status--waiting'],
    'resolvido'          => ['success', '#198754', 'Resolvido', 't-status--resolved'],
    'fechado'            => ['dark', '#212529', 'Fechado', 't-status--closed'],
  ];

  $m = $map[$status] ?? ['secondary', '#6c757d', strtoupper($status), 't-status--default'];
@endphp

<style>
  /* =========================================================
     GRR 3.0 • TICKET SHOW
  ========================================================= */
  .t-wrap{
    max-width: 1280px;
    margin: 0 auto;
    padding: 18px 14px 28px;
  }

  /* HERO */
  .t-hero{
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    border: 1px solid rgba(2,6,23,.10);
    background: linear-gradient(135deg, rgba(8,14,28,.98), rgba(15,23,42,.94));
    box-shadow:
      0 24px 70px rgba(2,6,23,.18),
      inset 0 1px 0 rgba(255,255,255,.05);
  }

  .t-hero__bg{
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

  .t-hero__content{
    position: relative;
    z-index: 1;
    padding: 22px;
    display: flex;
    gap: 18px;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
  }

  .t-kicker{
    color: rgba(226,232,240,.68);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .18em;
    text-transform: uppercase;
    margin-bottom: 10px;
  }

  .t-title{
    margin: 0;
    color: #f8fafc;
    font-size: clamp(1.4rem, 2.4vw, 2.2rem);
    line-height: 1.08;
    font-weight: 950;
    letter-spacing: -.02em;
  }

  .t-sub{
    margin-top: 10px;
    color: rgba(226,232,240,.78);
    font-size: .96rem;
    line-height: 1.6;
    font-weight: 500;
  }

  .t-sub b{
    color: rgba(248,250,252,.96);
  }

  .t-hero__right{
    display: flex;
    align-items: flex-start;
    gap: 10px;
    flex-wrap: wrap;
  }

  .t-back-btn{
    border-radius: 14px;
    font-weight: 900;
    padding: 11px 16px;
    border-color: rgba(255,255,255,.18);
  }

  .t-back-btn:hover{
    border-color: rgba(255,255,255,.28);
    background: rgba(255,255,255,.08);
    color: #fff;
  }

  /* STATUS */
  .t-status{
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

  .t-dot{
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 4px rgba(255,255,255,.06);
  }

  .t-status--open{
    background: rgba(245,159,0,.14);
    border-color: rgba(245,159,0,.28);
  }

  .t-status--progress{
    background: rgba(13,110,253,.14);
    border-color: rgba(13,110,253,.28);
  }

  .t-status--waiting{
    background: rgba(108,117,125,.18);
    border-color: rgba(148,163,184,.24);
  }

  .t-status--resolved{
    background: rgba(25,135,84,.14);
    border-color: rgba(25,135,84,.26);
  }

  .t-status--closed{
    background: rgba(33,37,41,.26);
    border-color: rgba(255,255,255,.12);
  }

  .t-status--default{
    background: rgba(255,255,255,.08);
  }

  /* ALERTAS */
  .t-alert{
    border-radius: 18px;
    padding: 15px 16px;
    box-shadow: 0 12px 32px rgba(15,23,42,.06);
  }

  /* GRID */
  .t-grid{
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    margin-top: 16px;
  }

  @media (min-width: 992px){
    .t-grid{
      grid-template-columns: minmax(0, 1fr) 380px;
      align-items: start;
    }
  }

  /* CARD BASE */
  .t-card{
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(15,23,42,.08);
    background: #ffffff;
    box-shadow: 0 18px 50px rgba(15,23,42,.07);
  }

  .t-card-h{
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

  .t-card-title{
    margin: 0;
    font-size: 1.02rem;
    font-weight: 900;
    color: #0f172a;
  }

  .t-card-sub{
    color: rgba(15,23,42,.52);
    font-size: .82rem;
    font-weight: 700;
    margin-top: 2px;
  }

  .t-card-b{
    padding: 18px;
  }

  .t-pill{
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
  .t-chat{
    height: 560px;
    overflow: auto;
    padding: 18px;
    background:
      linear-gradient(180deg, rgba(15,23,42,.02), rgba(15,23,42,0)),
      radial-gradient(circle at top right, rgba(59,130,246,.03), transparent 30%);
    position: relative;
    scroll-behavior: smooth;
  }

  .t-day{
    display: flex;
    justify-content: center;
    margin: 12px 0 16px;
  }

  .t-day span{
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

  .t-empty{
    min-height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: rgba(15,23,42,.56);
    font-weight: 600;
  }

  .t-msg{
    display: flex;
    margin-bottom: 14px;
  }

  .t-msg.is-me{
    justify-content: flex-end;
  }

  .t-msg.is-staff,
  .t-msg.is-other{
    justify-content: flex-start;
  }

  .t-bubble{
    width: min(100%, 820px);
    max-width: 76ch;
    padding: 12px 14px;
    border-radius: 18px;
    border: 1px solid rgba(15,23,42,.08);
    background: #ffffff;
    box-shadow: 0 10px 28px rgba(15,23,42,.06);
  }

  .t-msg.is-me .t-bubble{
    border-top-right-radius: 6px;
    background: linear-gradient(180deg, rgba(13,110,253,.06), rgba(13,110,253,.03));
    border-color: rgba(13,110,253,.14);
  }

  .t-msg.is-staff .t-bubble{
    border-top-left-radius: 6px;
    background: rgba(13,110,253,.04);
    border-color: rgba(13,110,253,.16);
  }

  .t-msg.is-other .t-bubble{
    border-top-left-radius: 6px;
  }

  .t-meta{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 8px;
  }

  .t-name{
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-weight: 900;
    font-size: .92rem;
    color: #0f172a;
  }

  .t-time{
    color: rgba(15,23,42,.48);
    font-size: .78rem;
    font-weight: 700;
    white-space: nowrap;
  }

  .t-text{
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    word-break: break-word;
    color: rgba(15,23,42,.88);
    line-height: 1.6;
    font-size: .95rem;
    font-weight: 500;
  }

  .t-badge-staff{
    display: inline-flex;
    align-items: center;
    min-height: 24px;
    padding: 0 8px;
    border-radius: 999px;
    border: 1px solid rgba(13,110,253,.25);
    color: #0d6efd;
    background: rgba(13,110,253,.06);
    font-size: 10px;
    font-weight: 900;
    letter-spacing: .08em;
  }

  .t-fab{
    position: sticky;
    bottom: 10px;
    display: flex;
    justify-content: flex-end;
    pointer-events: none;
    margin-top: 6px;
  }

  .t-fab button{
    pointer-events: auto;
    border-radius: 999px;
    border: 1px solid rgba(15,23,42,.08);
    background: rgba(255,255,255,.92);
    padding: .45rem .80rem;
    font-size: .82rem;
    font-weight: 800;
    box-shadow: 0 12px 28px rgba(15,23,42,.10);
  }

  /* DETALHES */
  .t-kv{
    display: grid;
    gap: 12px;
  }

  .t-kv .rowx{
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
  }

  .t-kv .k{
    color: rgba(15,23,42,.54);
    font-size: .84rem;
    font-weight: 700;
  }

  .t-kv .v{
    text-align: right;
    color: #0f172a;
    font-weight: 800;
    line-height: 1.45;
  }

  .t-kv .v small{
    display: block;
    margin-top: 3px;
    font-weight: 600;
    color: rgba(15,23,42,.48);
  }

  .t-divider{
    margin: 18px 0;
    border: 0;
    border-top: 1px solid rgba(15,23,42,.08);
    opacity: 1;
  }

  /* REPLY */
  .t-reply-title{
    margin: 0 0 10px;
    color: #0f172a;
    font-size: 1rem;
    font-weight: 900;
  }

  .t-reply-text{
    color: rgba(15,23,42,.58);
    font-size: .83rem;
    line-height: 1.5;
    margin-top: 10px;
    font-weight: 600;
  }

  .t-textarea{
    min-height: 128px;
    resize: vertical;
    border-radius: 16px;
    border: 1px solid rgba(15,23,42,.10);
    box-shadow: none !important;
    font-weight: 600;
  }

  .t-textarea:focus{
    border-color: rgba(13,110,253,.42);
    box-shadow: 0 0 0 .22rem rgba(13,110,253,.10) !important;
  }

  .t-submit{
    min-height: 46px;
    border-radius: 14px;
    font-weight: 900;
    box-shadow: 0 10px 24px rgba(13,110,253,.18);
  }

  /* DARK MODE */
  html[data-theme="dark"] .t-hero{
    border-color: rgba(255,255,255,.09);
    box-shadow:
      0 26px 70px rgba(0,0,0,.45),
      inset 0 1px 0 rgba(255,255,255,.04);
  }

  html[data-theme="dark"] .t-card{
    background: rgba(10,14,20,.78);
    border-color: rgba(255,255,255,.08);
    box-shadow: 0 18px 50px rgba(0,0,0,.35);
  }

  html[data-theme="dark"] .t-card-h{
    border-bottom-color: rgba(255,255,255,.08);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.08), transparent 30%),
      linear-gradient(180deg, rgba(15,20,28,.96), rgba(10,14,20,.92));
  }

  html[data-theme="dark"] .t-card-title,
  html[data-theme="dark"] .t-name,
  html[data-theme="dark"] .t-kv .v,
  html[data-theme="dark"] .t-reply-title{
    color: rgba(248,250,252,.95);
  }

  html[data-theme="dark"] .t-card-sub,
  html[data-theme="dark"] .t-kv .k,
  html[data-theme="dark"] .t-kv .v small,
  html[data-theme="dark"] .t-time,
  html[data-theme="dark"] .t-reply-text{
    color: rgba(226,232,240,.56);
  }

  html[data-theme="dark"] .t-pill{
    background: rgba(255,255,255,.05);
    border-color: rgba(255,255,255,.08);
    color: rgba(226,232,240,.82);
  }

  html[data-theme="dark"] .t-chat{
    background:
      linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,0)),
      radial-gradient(circle at top right, rgba(59,130,246,.05), transparent 30%);
  }

  html[data-theme="dark"] .t-day span{
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.08);
    color: rgba(226,232,240,.74);
    box-shadow: none;
  }

  html[data-theme="dark"] .t-bubble{
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.09);
    box-shadow: none;
  }

  html[data-theme="dark"] .t-msg.is-me .t-bubble{
    background: rgba(13,110,253,.12);
    border-color: rgba(13,110,253,.22);
  }

  html[data-theme="dark"] .t-msg.is-staff .t-bubble{
    background: rgba(13,110,253,.10);
    border-color: rgba(13,110,253,.20);
  }

  html[data-theme="dark"] .t-text{
    color: rgba(248,250,252,.88);
  }

  html[data-theme="dark"] .t-badge-staff{
    color: #93c5fd;
    background: rgba(59,130,246,.12);
    border-color: rgba(59,130,246,.22);
  }

  html[data-theme="dark"] .t-fab button{
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.10);
    color: rgba(255,255,255,.85);
    box-shadow: none;
  }

  html[data-theme="dark"] .t-divider{
    border-top-color: rgba(255,255,255,.08);
  }

  html[data-theme="dark"] .t-textarea{
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.10);
    color: rgba(248,250,252,.94);
  }

  html[data-theme="dark"] .t-textarea::placeholder{
    color: rgba(226,232,240,.42);
  }

  html[data-theme="dark"] .t-textarea:focus{
    border-color: rgba(147,197,253,.45);
    box-shadow: 0 0 0 .22rem rgba(59,130,246,.12) !important;
    background: rgba(255,255,255,.05);
    color: rgba(248,250,252,.96);
  }

  /* MOBILE */
  @media (max-width: 991.98px){
    .t-hero__content{
      padding: 18px;
    }
  }

  @media (max-width: 767.98px){
    .t-wrap{
      padding: 14px 10px 24px;
    }

    .t-hero__content,
    .t-card-h,
    .t-card-b{
      padding-left: 16px;
      padding-right: 16px;
    }

    .t-chat{
      height: 460px;
      padding: 14px;
    }

    .t-kv .rowx{
      flex-direction: column;
      gap: 4px;
    }

    .t-kv .v{
      text-align: left;
    }

    .t-back-btn{
      width: 100%;
      justify-content: center;
    }

    .t-hero__right{
      width: 100%;
    }

    .t-status{
      width: 100%;
      justify-content: center;
    }

    .t-bubble{
      max-width: 100%;
    }
  }
</style>

<div class="t-wrap">

  {{-- HERO --}}
  <section class="t-hero">
    <div class="t-hero__bg"></div>

    <div class="t-hero__content">
      <div class="t-hero__left">
        <div class="t-kicker">GRR • PRF — Central de Suporte</div>
        <h1 class="t-title">Ticket #{{ $ticket->id }} — {{ $ticket->titulo }}</h1>
        <div class="t-sub">
          Categoria: <b>{{ $ticket->categoriaLabel() }}</b>
          • Prioridade: <b>{{ strtoupper($ticket->prioridadeLabel()) }}</b>
        </div>
      </div>

      <div class="t-hero__right">
        <span class="t-status {{ $m[3] }}">
          <span class="t-dot" style="background: {{ $m[1] }}"></span>
          {{ $m[2] }}
        </span>

        <a href="{{ route('tickets.index') }}" class="btn btn-outline-light t-back-btn">
          ← Voltar para meus tickets
        </a>
      </div>
    </div>
  </section>

  @if(session('success'))
    <div class="alert alert-success t-alert mt-3 mb-0">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger t-alert mt-3 mb-0">
      {{ session('error') }}
    </div>
  @endif

  <div class="t-grid">

    {{-- CHAT --}}
    <div class="t-card">
      <div class="t-card-h">
        <div>
          <h2 class="t-card-title">Mensagens do ticket</h2>
          <div class="t-card-sub">
            Aberto por: <b>{{ $ticket->user->name ?? '—' }}</b>
          </div>
        </div>
      </div>

      <div class="t-chat" id="ticketChat">
        @php $lastDay = null; @endphp

        @forelse($ticket->messages as $msg)
          @php
            $mine = (int)$msg->user_id === (int)auth()->id();
            $isStaff = (bool)($msg->is_staff ?? false);
            $cls = $mine ? 'is-me' : ($isStaff ? 'is-staff' : 'is-other');

            $day = optional($msg->created_at)->format('Y-m-d');
            $dayLabel = optional($msg->created_at)->format('d/m/Y');
          @endphp

          @if($day && $day !== $lastDay)
            <div class="t-day"><span>{{ $dayLabel }}</span></div>
            @php $lastDay = $day; @endphp
          @endif

          <div class="t-msg {{ $cls }}">
            <div class="t-bubble">
              <div class="t-meta">
                <div class="t-name">
                  {{ $msg->user->name ?? ($isStaff ? 'Equipe' : 'Usuário') }}
                  @if($isStaff)
                    <span class="t-badge-staff">EQUIPE</span>
                  @endif
                </div>

                <div class="t-time">
                  {{ optional($msg->created_at)->format('d/m/Y H:i') }}
                </div>
              </div>

              <div class="t-text">{{ $msg->mensagem }}</div>
            </div>
          </div>
        @empty
          <div class="t-empty">
            <div>
              Nenhuma mensagem ainda.
            </div>
          </div>
        @endforelse

        <div class="t-fab">
          <button type="button" class="btn btn-sm" id="btnScrollBottom">Ir para o fim ↓</button>
        </div>
      </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="t-card">
      <div class="t-card-h">
        <div>
          <h2 class="t-card-title">Detalhes do ticket</h2>
          <div class="t-card-sub">Informações gerais do atendimento</div>
        </div>

        <span class="t-pill">{{ strtoupper($ticket->prioridadeLabel()) }}</span>
      </div>

      <div class="t-card-b">
        <div class="t-kv">
          <div class="rowx">
            <div class="k">Categoria</div>
            <div class="v">{{ $ticket->categoriaLabel() }}</div>
          </div>

          <div class="rowx">
            <div class="k">Criado em</div>
            <div class="v">
              {{ optional($ticket->created_at)->format('d/m/Y H:i') }}
              <small>{{ $ticket->ip ?? '—' }}</small>
            </div>
          </div>

          <div class="rowx">
            <div class="k">Responsável</div>
            <div class="v">{{ $ticket->responsavel->name ?? '—' }}</div>
          </div>

          <div class="rowx">
            <div class="k">Última atividade</div>
            <div class="v">
              {{ optional($ticket->lastActivityAt())->format('d/m/Y H:i') ?? '—' }}
              <small>{{ $ticket->statusLabel() }}</small>
            </div>
          </div>
        </div>

        <hr class="t-divider">

        <div class="t-reply">
          <h3 class="t-reply-title">Responder</h3>

          @if($blocked)
            <div class="alert alert-warning mb-0 t-alert">
              Este ticket está <b>{{ strtoupper($ticket->statusLabel()) }}</b>. Não é possível responder no momento.
            </div>
          @else
            <form method="POST" action="{{ route('tickets.reply', $ticket) }}" id="formReplyUser">
              @csrf

              <textarea
                class="form-control t-textarea mb-2"
                name="mensagem"
                id="replyTextareaUser"
                placeholder="Digite sua mensagem... (Enter envia • Shift+Enter quebra linha)"
                required
              ></textarea>

              <button class="btn btn-primary w-100 t-submit" type="submit">
                Enviar resposta
              </button>
            </form>
          @endif

          <div class="t-reply-text">
            Se for denúncia, descreva horários, envolvidos e links de prints, se houver.
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  (function(){
    const chat = document.getElementById('ticketChat');
    const btn  = document.getElementById('btnScrollBottom');

    function scrollBottom(){
      if (chat) chat.scrollTop = chat.scrollHeight;
    }

    scrollBottom();

    if (btn) {
      btn.addEventListener('click', scrollBottom);
    }

    const ta = document.getElementById('replyTextareaUser');
    const form = document.getElementById('formReplyUser');

    if (ta && form) {
      ta.addEventListener('keydown', function(e){
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          form.submit();
        }
      });
    }
  })();
</script>
@endsection