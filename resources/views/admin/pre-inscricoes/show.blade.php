@extends('layouts.app')

@section('content')
@php
  $s = (string)($preInscricao->status ?? 'pendente');

  $cls = match($s){
      'aprovado' => 'text-bg-success',
      'reprovado' => 'text-bg-danger',
      default => 'text-bg-warning'
  };

  $auth = auth()->user();
  $authNivel = (int)($auth->nivel ?? 0);
  $canManage = $auth && $authNivel >= 9;

  $qraRgRaw = (string)($preInscricao->qra_rg ?? '');
  $nomeCandidato = trim(preg_replace('/\s*(—|-|\|)\s*RG\s*:\s*\d+\s*$/i', '', $qraRgRaw));
  if ($nomeCandidato === '' && filled($qraRgRaw)) $nomeCandidato = trim($qraRgRaw);

  $rgCandidato = null;
  if (preg_match('/RG\s*:\s*([0-9]+)/i', $qraRgRaw, $m)) {
      $rgCandidato = $m[1];
  }

  $discordId = (string)($preInscricao->discord_id ?? '');

  $copyText = "Nome: " . ($nomeCandidato !== '' ? $nomeCandidato : '—') . "\n"
            . "RG: " . (filled($rgCandidato) ? $rgCandidato : '—') . "\n"
            . "Discord ID: " . (filled($discordId) ? $discordId : '—') . "\n"
            . "Unidade que irá ingressar: GRR\n"
            . "Requisitos verificados e aprovados pelo Comando: ";
@endphp

<style>
  /* =========================================================
     GRR 3.0 — PRÉ-INSCRIÇÃO (SHOW)
     Visual premium, compatível com light e dark
  ========================================================= */

  .pre-show-page{
    max-width: 1280px;
    margin: 0 auto;
  }

  .pre-kicker{
    letter-spacing: .16em;
    text-transform: uppercase;
    font-weight: 800;
    font-size: .74rem;
    opacity: .78;
  }

  .pre-show-hero{
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    padding: 24px;
    margin-bottom: 20px;
    border: 1px solid rgba(15,23,42,.08);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.10), transparent 28%),
      radial-gradient(circle at left bottom, rgba(16,185,129,.08), transparent 24%),
      linear-gradient(180deg, #ffffff, #f8fafc);
    box-shadow: 0 18px 50px rgba(15,23,42,.08);
  }

  .pre-show-hero__glow{
    position:absolute;
    border-radius:999px;
    filter: blur(50px);
    pointer-events:none;
    opacity:.55;
  }

  .pre-show-hero__glow--a{
    width:180px;
    height:180px;
    right:-30px;
    top:-30px;
    background: rgba(59,130,246,.18);
  }

  .pre-show-hero__glow--b{
    width:180px;
    height:180px;
    left:-35px;
    bottom:-55px;
    background: rgba(16,185,129,.14);
  }

  .pre-show-hero__content{
    position: relative;
    z-index: 2;
  }

  .pre-show-hero__title{
    margin: 0;
    font-size: clamp(1.35rem, 2vw, 1.95rem);
    font-weight: 900;
    letter-spacing: -.02em;
    color: #0f172a;
  }

  .pre-show-hero__desc{
    margin: 8px 0 0;
    max-width: 780px;
    color: #475569;
    font-size: .96rem;
  }

  .pre-show-hero__meta{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 14px;
  }

  .pre-chip{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border-radius: 999px;
    padding: 10px 14px;
    font-size: .8rem;
    font-weight: 800;
    background: rgba(255,255,255,.78);
    border: 1px solid rgba(148,163,184,.22);
    color: #0f172a;
    white-space: nowrap;
  }

  .pre-grid-card{
    border: 1px solid rgba(15,23,42,.08);
    border-radius: 22px;
    background: linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,.98));
    box-shadow: 0 14px 40px rgba(15,23,42,.06);
    overflow: hidden;
  }

  .pre-grid-card__header{
    padding: 18px 20px;
    border-bottom: 1px solid rgba(148,163,184,.14);
    background:
      linear-gradient(180deg, rgba(248,250,252,.95), rgba(255,255,255,.92));
  }

  .pre-grid-card__title{
    margin: 0;
    font-size: .98rem;
    font-weight: 900;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .pre-grid-card__title-dot{
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: linear-gradient(135deg, #2563eb, #60a5fa);
    box-shadow: 0 0 0 5px rgba(37,99,235,.10);
    flex: 0 0 auto;
  }

  .pre-grid-card__subtitle{
    margin-top: 4px;
    color: #64748b;
    font-size: .84rem;
  }

  .pre-grid-card__body{
    padding: 20px;
  }

  .pre-detail{
    margin-bottom: 16px;
  }

  .pre-detail:last-child{
    margin-bottom: 0;
  }

  .pre-detail__label{
    font-size: .76rem;
    letter-spacing: .04em;
    text-transform: uppercase;
    font-weight: 900;
    color: #64748b;
    margin-bottom: 6px;
  }

  .pre-detail__value{
    color: #0f172a;
    font-weight: 800;
    line-height: 1.4;
  }

  .pre-detail__sub{
    margin-top: 4px;
    color: #64748b;
    font-size: .84rem;
  }

  .pre-show-wrap .alert{
    border: none;
    border-radius: 18px;
    box-shadow: 0 10px 24px rgba(15,23,42,.06);
  }

  .pre-status-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    border-radius:999px;
    padding:9px 13px;
    font-size:.78rem;
    font-weight:900;
    letter-spacing:.02em;
  }

  .pre-status-pill::before{
    content:"";
    width:8px;
    height:8px;
    border-radius:999px;
    background: currentColor;
    flex:0 0 auto;
  }

  .pre-status-pill--pendente{
    background: rgba(245,158,11,.16);
    color: #f59e0b;
    border: 1px solid rgba(245,158,11,.22);
  }

  .pre-status-pill--aprovado{
    background: rgba(16,185,129,.16);
    color: #10b981;
    border: 1px solid rgba(16,185,129,.22);
  }

  .pre-status-pill--reprovado{
    background: rgba(239,68,68,.14);
    color: #ef4444;
    border: 1px solid rgba(239,68,68,.22);
  }

  .pre-back-btn{
    border-radius: 12px !important;
    font-weight: 900 !important;
  }

  .pre-show-wrap .btn{
    border-radius: 12px !important;
    font-weight: 900 !important;
  }

  .pre-show-wrap .btn-outline-secondary{
    border-color: rgba(148,163,184,.28);
  }

  .pre-show-wrap .form-label{
    font-size: .78rem;
    font-weight: 900;
    color: #475569;
    margin-bottom: 7px;
  }

  .pre-show-wrap .form-control,
  .pre-show-wrap .form-select{
    min-height: 46px;
    border-radius: 14px !important;
    border: 1px solid rgba(148,163,184,.25) !important;
    background: #fff !important;
    box-shadow: none !important;
  }

  .pre-show-wrap textarea.form-control{
    min-height: unset;
  }

  .pre-show-wrap .form-control:focus,
  .pre-show-wrap .form-select:focus{
    border-color: rgba(37,99,235,.45) !important;
    box-shadow: 0 0 0 .22rem rgba(37,99,235,.12) !important;
  }

  .pre-copy-box{
    position: relative;
    overflow: hidden;
  }

  .pre-copy-box::after{
    content:"";
    position:absolute;
    inset:auto -70px -70px auto;
    width:180px;
    height:180px;
    border-radius:999px;
    background: rgba(16,185,129,.08);
    filter: blur(26px);
    pointer-events:none;
  }

  .pre-copy-note{
    color: #64748b;
    font-size: .82rem;
    margin-top: 10px;
  }

  .pre-answer-item{
    padding: 16px 0;
    border-bottom: 1px solid rgba(148,163,184,.14);
  }

  .pre-answer-item:first-child{
    padding-top: 0;
  }

  .pre-answer-item:last-child{
    padding-bottom: 0;
    border-bottom: 0;
  }

  .pre-answer-item__question{
    font-size: .95rem;
    font-weight: 900;
    color: #0f172a;
    margin-bottom: 8px;
  }

  .pre-answer-item__answer{
    color: #475569;
    line-height: 1.65;
    font-size: .95rem;
  }

  .pre-answer-item__answer--mono{
    font-family: inherit;
    white-space: pre-wrap;
  }

  .pre-admin-note{
    color: #64748b;
    font-size: .83rem;
    margin-top: 10px;
  }

  .pre-admin-disabled{
    padding: 14px 16px;
    border-radius: 16px;
    background: rgba(245,158,11,.10);
    border: 1px solid rgba(245,158,11,.18);
    color: #92400e;
    font-size: .88rem;
    font-weight: 700;
  }

  /* =========================================================
     DARK MODE
  ========================================================= */

  body.theme-dark .pre-show-hero,
  html.theme-dark .pre-show-hero,
  [data-theme="dark"] .pre-show-hero,
  body.dark .pre-show-hero,
  html.dark .pre-show-hero{
    border-color: rgba(148,163,184,.16);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.16), transparent 28%),
      radial-gradient(circle at left bottom, rgba(16,185,129,.10), transparent 24%),
      linear-gradient(180deg, rgba(2,6,23,.94), rgba(15,23,42,.88));
    box-shadow: 0 18px 50px rgba(0,0,0,.28);
  }

  body.theme-dark .pre-show-hero__title,
  html.theme-dark .pre-show-hero__title,
  [data-theme="dark"] .pre-show-hero__title,
  body.dark .pre-show-hero__title,
  html.dark .pre-show-hero__title{
    color: #f8fafc;
  }

  body.theme-dark .pre-show-hero__desc,
  html.theme-dark .pre-show-hero__desc,
  [data-theme="dark"] .pre-show-hero__desc,
  body.dark .pre-show-hero__desc,
  html.dark .pre-show-hero__desc{
    color: rgba(226,232,240,.72);
  }

  body.theme-dark .pre-chip,
  html.theme-dark .pre-chip,
  [data-theme="dark"] .pre-chip,
  body.dark .pre-chip,
  html.dark .pre-chip{
    background: rgba(15,23,42,.62);
    border-color: rgba(148,163,184,.22);
    color: #e2e8f0;
  }

  body.theme-dark .pre-grid-card,
  html.theme-dark .pre-grid-card,
  [data-theme="dark"] .pre-grid-card,
  body.dark .pre-grid-card,
  html.dark .pre-grid-card{
    background: linear-gradient(180deg, rgba(2,6,23,.82), rgba(15,23,42,.78));
    border-color: rgba(148,163,184,.16);
    box-shadow: 0 18px 45px rgba(0,0,0,.28);
  }

  body.theme-dark .pre-grid-card__header,
  html.theme-dark .pre-grid-card__header,
  [data-theme="dark"] .pre-grid-card__header,
  body.dark .pre-grid-card__header,
  html.dark .pre-grid-card__header{
    background: rgba(15,23,42,.52);
    border-bottom-color: rgba(148,163,184,.14);
  }

  body.theme-dark .pre-grid-card__title,
  html.theme-dark .pre-grid-card__title,
  [data-theme="dark"] .pre-grid-card__title,
  body.dark .pre-grid-card__title,
  html.dark .pre-grid-card__title{
    color: #f8fafc;
  }

  body.theme-dark .pre-grid-card__subtitle,
  body.theme-dark .pre-detail__label,
  body.theme-dark .pre-detail__sub,
  body.theme-dark .pre-answer-item__answer,
  body.theme-dark .pre-copy-note,
  body.theme-dark .pre-admin-note,
  html.theme-dark .pre-grid-card__subtitle,
  html.theme-dark .pre-detail__label,
  html.theme-dark .pre-detail__sub,
  html.theme-dark .pre-answer-item__answer,
  html.theme-dark .pre-copy-note,
  html.theme-dark .pre-admin-note,
  [data-theme="dark"] .pre-grid-card__subtitle,
  [data-theme="dark"] .pre-detail__label,
  [data-theme="dark"] .pre-detail__sub,
  [data-theme="dark"] .pre-answer-item__answer,
  [data-theme="dark"] .pre-copy-note,
  [data-theme="dark"] .pre-admin-note,
  body.dark .pre-grid-card__subtitle,
  body.dark .pre-detail__label,
  body.dark .pre-detail__sub,
  body.dark .pre-answer-item__answer,
  body.dark .pre-copy-note,
  body.dark .pre-admin-note,
  html.dark .pre-grid-card__subtitle,
  html.dark .pre-detail__label,
  html.dark .pre-detail__sub,
  html.dark .pre-answer-item__answer,
  html.dark .pre-copy-note,
  html.dark .pre-admin-note{
    color: rgba(226,232,240,.70);
  }

  body.theme-dark .pre-detail__value,
  body.theme-dark .pre-answer-item__question,
  html.theme-dark .pre-detail__value,
  html.theme-dark .pre-answer-item__question,
  [data-theme="dark"] .pre-detail__value,
  [data-theme="dark"] .pre-answer-item__question,
  body.dark .pre-detail__value,
  body.dark .pre-answer-item__question,
  html.dark .pre-detail__value,
  html.dark .pre-answer-item__question{
    color: #f8fafc;
  }

  body.theme-dark .pre-show-wrap .form-label,
  html.theme-dark .pre-show-wrap .form-label,
  [data-theme="dark"] .pre-show-wrap .form-label,
  body.dark .pre-show-wrap .form-label,
  html.dark .pre-show-wrap .form-label{
    color: rgba(226,232,240,.78);
  }

  body.theme-dark .pre-show-wrap .form-control,
  body.theme-dark .pre-show-wrap .form-select,
  html.theme-dark .pre-show-wrap .form-control,
  html.theme-dark .pre-show-wrap .form-select,
  [data-theme="dark"] .pre-show-wrap .form-control,
  [data-theme="dark"] .pre-show-wrap .form-select,
  body.dark .pre-show-wrap .form-control,
  body.dark .pre-show-wrap .form-select,
  html.dark .pre-show-wrap .form-control,
  html.dark .pre-show-wrap .form-select{
    background: rgba(15,23,42,.72) !important;
    border-color: rgba(148,163,184,.22) !important;
    color: #e2e8f0 !important;
  }

  body.theme-dark .pre-show-wrap .form-control::placeholder,
  html.theme-dark .pre-show-wrap .form-control::placeholder,
  [data-theme="dark"] .pre-show-wrap .form-control::placeholder,
  body.dark .pre-show-wrap .form-control::placeholder,
  html.dark .pre-show-wrap .form-control::placeholder{
    color: rgba(226,232,240,.42) !important;
  }

  body.theme-dark .pre-show-wrap .form-select option,
  html.theme-dark .pre-show-wrap .form-select option,
  [data-theme="dark"] .pre-show-wrap .form-select option,
  body.dark .pre-show-wrap .form-select option,
  html.dark .pre-show-wrap .form-select option{
    background: #0b1220;
    color: #e2e8f0;
  }

  body.theme-dark .pre-show-wrap .btn-outline-secondary,
  html.theme-dark .pre-show-wrap .btn-outline-secondary,
  [data-theme="dark"] .pre-show-wrap .btn-outline-secondary,
  body.dark .pre-show-wrap .btn-outline-secondary,
  html.dark .pre-show-wrap .btn-outline-secondary{
    border-color: rgba(148,163,184,.25) !important;
    color: rgba(226,232,240,.88) !important;
  }

  body.theme-dark .pre-show-wrap .badge.text-bg-warning,
  html.theme-dark .pre-show-wrap .badge.text-bg-warning,
  [data-theme="dark"] .pre-show-wrap .badge.text-bg-warning,
  body.dark .pre-show-wrap .badge.text-bg-warning,
  html.dark .pre-show-wrap .badge.text-bg-warning{
    color: #111827 !important;
    font-weight: 900 !important;
  }

  body.theme-dark .pre-answer-item,
  html.theme-dark .pre-answer-item,
  [data-theme="dark"] .pre-answer-item,
  body.dark .pre-answer-item,
  html.dark .pre-answer-item{
    border-bottom-color: rgba(148,163,184,.12);
  }

  body.theme-dark .pre-admin-disabled,
  html.theme-dark .pre-admin-disabled,
  [data-theme="dark"] .pre-admin-disabled,
  body.dark .pre-admin-disabled,
  html.dark .pre-admin-disabled{
    background: rgba(245,158,11,.10);
    border-color: rgba(245,158,11,.18);
    color: #fbbf24;
  }

  @media (max-width: 991.98px){
    .pre-show-hero{
      padding: 20px;
      border-radius: 20px;
    }

    .pre-grid-card{
      border-radius: 18px;
    }

    .pre-grid-card__header,
    .pre-grid-card__body{
      padding-left: 16px;
      padding-right: 16px;
    }
  }
</style>

<div class="container-fluid py-3 pre-show-wrap">
  <div class="pre-show-page">

    @php
      $statusClass = match($s){
        'aprovado' => 'pre-status-pill--aprovado',
        'reprovado' => 'pre-status-pill--reprovado',
        default => 'pre-status-pill--pendente',
      };

      $statusLabel = match($s){
        'aprovado' => 'APROVADO',
        'reprovado' => 'REPROVADO',
        default => 'PENDENTE',
      };
    @endphp

    {{-- HERO --}}
    <div class="pre-show-hero">
      <div class="pre-show-hero__glow pre-show-hero__glow--a"></div>
      <div class="pre-show-hero__glow pre-show-hero__glow--b"></div>

      <div class="pre-show-hero__content">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
          <div>
            <div class="pre-kicker text-secondary mb-2">GRR • PRF • Administrativo</div>
            <h1 class="pre-show-hero__title">Análise de Pré-inscrição</h1>
            <p class="pre-show-hero__desc">
              Visualize as respostas do candidato, acompanhe o rastreio do envio e registre a decisão administrativa de forma organizada e profissional.
            </p>

            <div class="pre-show-hero__meta">
              <span class="pre-chip">
                <span>📄</span>
                <span>Registro #{{ $preInscricao->id }}</span>
              </span>

              <span class="pre-chip">
                <span>👤</span>
                <span>{{ $nomeCandidato !== '' ? $nomeCandidato : 'Candidato não identificado' }}</span>
              </span>
            </div>
          </div>

          <div class="d-flex gap-2 flex-wrap align-items-center">
            <a href="{{ route('admin.preinscricoes.index') }}" class="btn btn-outline-secondary btn-sm pre-back-btn">
              ← Voltar
            </a>

            <span class="pre-status-pill {{ $statusClass }}">
              {{ $statusLabel }}
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
      <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <div class="row g-3">

      {{-- COLUNA ESQUERDA --}}
      <div class="col-12 col-lg-4">

        @if((string)($preInscricao->status ?? '') === 'aprovado')
          <div class="pre-grid-card pre-copy-box mb-3">
            <div class="pre-grid-card__header">
              <h2 class="pre-grid-card__title">
                <span class="pre-grid-card__title-dot"></span>
                <span>Copiar dados do aprovado</span>
              </h2>
              <div class="pre-grid-card__subtitle">
                Utilize essas informações em comunicados, registros internos ou encaminhamentos.
              </div>
            </div>

            <div class="pre-grid-card__body">
              <textarea
                id="copyAprovadoText"
                class="form-control"
                rows="7"
                style="font-weight:700;"
              >{{ $copyText }}</textarea>

              <div class="d-flex gap-2 mt-3 flex-wrap">
                <button
                  type="button"
                  class="btn btn-primary flex-fill"
                  onclick="(function(){
                    const el = document.getElementById('copyAprovadoText');
                    if(!el) return;
                    el.focus();
                    el.select();
                    try{ document.execCommand('copy'); }catch(e){}
                    const b = document.getElementById('copyAprovadoBtnText');
                    if(b){ b.textContent = 'Copiado!'; setTimeout(()=>b.textContent='Copiar informações', 1400); }
                  })();"
                >
                  <span id="copyAprovadoBtnText">Copiar informações</span>
                </button>

                <button
                  type="button"
                  class="btn btn-outline-secondary"
                  onclick="(function(){
                    const el = document.getElementById('copyAprovadoText');
                    if(!el) return;
                    el.focus();
                    el.select();
                  })();"
                >
                  Selecionar
                </button>
              </div>

              <div class="pre-copy-note">
                * O campo “Requisitos verificados e aprovados pelo Comando” permanece aberto para preenchimento manual.
              </div>
            </div>
          </div>
        @endif

        <div class="pre-grid-card mb-3">
          <div class="pre-grid-card__header">
            <h2 class="pre-grid-card__title">
              <span class="pre-grid-card__title-dot"></span>
              <span>Resumo do envio</span>
            </h2>
            <div class="pre-grid-card__subtitle">
              Dados principais do candidato e informações de rastreio.
            </div>
          </div>

          <div class="pre-grid-card__body">
            <div class="pre-detail">
              <div class="pre-detail__label">QRA + RG</div>
              <div class="pre-detail__value">{{ $preInscricao->qra_rg ?? '—' }}</div>
            </div>

            <div class="pre-detail">
              <div class="pre-detail__label">Nome extraído</div>
              <div class="pre-detail__value">{{ $nomeCandidato !== '' ? $nomeCandidato : '—' }}</div>
            </div>

            <div class="pre-detail">
              <div class="pre-detail__label">RG identificado</div>
              <div class="pre-detail__value">{{ filled($rgCandidato) ? $rgCandidato : '—' }}</div>
            </div>

            <div class="pre-detail">
              <div class="pre-detail__label">Discord ID</div>
              <div class="pre-detail__value">{{ $preInscricao->discord_id ?? '—' }}</div>
            </div>

            <div class="pre-detail">
              <div class="pre-detail__label">Enviado em</div>
              <div class="pre-detail__value">
                {{ optional($preInscricao->created_at)->format('d/m/Y H:i') ?? '—' }}
              </div>
              <div class="pre-detail__sub">
                {{ optional($preInscricao->created_at)?->diffForHumans() }}
              </div>
            </div>

            <div class="pre-detail">
              <div class="pre-detail__label">Revisado em</div>
              <div class="pre-detail__value">
                {{ $preInscricao->revisado_em ? \Carbon\Carbon::parse($preInscricao->revisado_em)->format('d/m/Y H:i') : '—' }}
              </div>
              <div class="pre-detail__sub">
                Revisado por: {{ $preInscricao->revisado_por ?? '—' }}
              </div>
            </div>
          </div>
        </div>

        <div class="pre-grid-card">
          <div class="pre-grid-card__header">
            <h2 class="pre-grid-card__title">
              <span class="pre-grid-card__title-dot"></span>
              <span>Decisão administrativa</span>
            </h2>
            <div class="pre-grid-card__subtitle">
              Aprovação ou reprovação com justificativa obrigatória.
            </div>
          </div>

          <div class="pre-grid-card__body">
            @if($canManage)
              <form method="POST" action="{{ route('admin.preinscricoes.status', $preInscricao->id) }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                  <label class="form-label">Status</label>
                  <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="">Selecione…</option>
                    <option value="aprovado" @selected(old('status', $preInscricao->status) === 'aprovado')>Aprovar</option>
                    <option value="reprovado" @selected(old('status', $preInscricao->status) === 'reprovado')>Reprovar</option>
                  </select>
                  @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-2">
                  <label class="form-label">Observação administrativa</label>
                  <textarea
                    name="observacao_admin"
                    rows="5"
                    class="form-control @error('observacao_admin') is-invalid @enderror"
                    placeholder="Explique com clareza por que está aprovando ou reprovando esta pré-inscrição..."
                    required
                  >{{ old('observacao_admin', $preInscricao->observacao_admin) }}</textarea>
                  @error('observacao_admin')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <button class="btn btn-primary w-100 mt-2" type="submit">
                  Salvar decisão
                </button>

                <div class="pre-admin-note">
                  Esta ação ficará registrada e disponível para consulta administrativa no painel.
                </div>
              </form>
            @else
              <div class="pre-admin-disabled">
                Você pode visualizar esta pré-inscrição, mas a alteração de status administrativo está disponível apenas para usuários de nível 9 ou superior.
              </div>
            @endif
          </div>
        </div>

      </div>

      {{-- COLUNA DIREITA --}}
      <div class="col-12 col-lg-8">
        <div class="pre-grid-card">
          <div class="pre-grid-card__header">
            <h2 class="pre-grid-card__title">
              <span class="pre-grid-card__title-dot"></span>
              <span>Respostas do formulário</span>
            </h2>
            <div class="pre-grid-card__subtitle">
              Leitura completa das perguntas respondidas pelo candidato.
            </div>
          </div>

          <div class="pre-grid-card__body">
            @php
              $answer = fn($v) => filled($v) ? $v : '—';

              $map = [
                  'estagio_15_dias' => ['sim' => 'Sim', 'nao' => 'Não'],
                  'dias_ativo_semana' => ['1-2'=>'2 dias', '3-4'=>'4 dias', '5-6'=>'6 dias', '7'=>'Todos os dias'],
                  'ordem_nao_concorda' => [
                      'cumpro_e_depois_questiono' => 'Cumpro e depois questiono',
                      'questiono_no_momento' => 'Questiono no momento',
                      'nao_cumpro' => 'Não cumpro',
                  ],
                  'horario_frequente' => [
                      'manha'=>'Manhã', 'tarde'=>'Tarde', 'noite'=>'Noite', 'madrugada'=>'Madrugada', 'varia'=>'Varia',
                  ],
              ];

              $pick = function($field) use ($preInscricao, $map) {
                  $raw = $preInscricao->{$field} ?? null;
                  if (!filled($raw)) return '—';
                  return $map[$field][$raw] ?? $raw;
              };
            @endphp

            <div class="pre-answer-item">
              <div class="pre-answer-item__question">1) QRA + RG</div>
              <div class="pre-answer-item__answer pre-answer-item__answer--mono">{{ $answer($preInscricao->qra_rg ?? null) }}</div>
            </div>

            <div class="pre-answer-item">
              <div class="pre-answer-item__question">2) Discord ID</div>
              <div class="pre-answer-item__answer pre-answer-item__answer--mono">{{ $answer($preInscricao->discord_id ?? null) }}</div>
            </div>

            <div class="pre-answer-item">
              <div class="pre-answer-item__question">3) Motivo de procurar o GRR agora</div>
              <div class="pre-answer-item__answer pre-answer-item__answer--mono">{{ $answer($preInscricao->motivo_grr_agora ?? null) }}</div>
            </div>

            <div class="pre-answer-item">
              <div class="pre-answer-item__question">4) Diferencial do GRR</div>
              <div class="pre-answer-item__answer pre-answer-item__answer--mono">{{ $answer($preInscricao->diferencial_grr ?? null) }}</div>
            </div>

            <div class="pre-answer-item">
              <div class="pre-answer-item__question">5) Aceita estágio probatório de 15 dias?</div>
              <div class="pre-answer-item__answer">{{ $pick('estagio_15_dias') }}</div>
            </div>

            <div class="pre-answer-item">
              <div class="pre-answer-item__question">6) Dias ativos por semana</div>
              <div class="pre-answer-item__answer">{{ $pick('dias_ativo_semana') }}</div>
            </div>

            <div class="pre-answer-item">
              <div class="pre-answer-item__question">7) Se não concordar com a ordem</div>
              <div class="pre-answer-item__answer">{{ $pick('ordem_nao_concorda') }}</div>
            </div>

            <div class="pre-answer-item">
              <div class="pre-answer-item__question">8) Horário mais frequente</div>
              <div class="pre-answer-item__answer">{{ $pick('horario_frequente') }}</div>
            </div>

            <div class="pre-answer-item">
              <div class="pre-answer-item__question">9) Como lida com frustração</div>
              <div class="pre-answer-item__answer pre-answer-item__answer--mono">{{ $answer($preInscricao->como_lida_frustracao ?? null) }}</div>
            </div>

            <div class="pre-answer-item">
              <div class="pre-answer-item__question">10) Experiência anterior</div>
              <div class="pre-answer-item__answer pre-answer-item__answer--mono">{{ $answer($preInscricao->experiencia_anterior ?? null) }}</div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection