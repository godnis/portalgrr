@extends('layouts.app')

@section('content')
@php
  // Tela de regulamento com suporte real a tema claro/escuro no padrão GRR
@endphp

<div class="container py-4">

  <div class="grr-page grr-reg">

    {{-- =========================
         HERO / CABEÇALHO
    ========================== --}}
    <div class="grr-hero mb-4">
      <div class="grr-hero__glow grr-hero__glow--blue"></div>
      <div class="grr-hero__glow grr-hero__glow--green"></div>

      <div class="grr-hero__inner">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
          <div class="d-flex align-items-start gap-3">
            <div class="grr-hero__ico">
              <span class="grr-hero__emoji">📚</span>
            </div>

            <div>
              <div class="grr-kicker mb-2">Central normativa</div>
              <h3 class="fw-black mb-1 grr-hero__title">Visão Geral — Regulamento</h3>
              <div class="grr-hero__sub">
                Ponto de partida para compreender normas, condutas, procedimentos obrigatórios
                e o padrão institucional exigido pela GRR.
              </div>

              <div class="d-flex gap-2 flex-wrap mt-3">
                <span class="grr-pill grr-pill--blue">Leitura obrigatória</span>
                <span class="grr-pill grr-pill--soft">Uso interno</span>
                <span class="grr-pill grr-pill--soft">Atualização contínua</span>
              </div>
            </div>
          </div>

          <div class="grr-hero__side">
            <div class="grr-hero__sidebox">
              <div class="grr-hero__rightk">Ambiente</div>
              <div class="grr-hero__rightv">Restrito</div>
            </div>

            <div class="grr-hero__sidebox">
              <div class="grr-hero__rightk">Unidade</div>
              <div class="grr-hero__rightv">GRR • PRF</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- =========================
         RESUMO RÁPIDO
    ========================== --}}
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-4">
        <div class="grr-mini">
          <div class="grr-mini__label">Seções principais</div>
          <div class="grr-mini__value">03</div>
          <div class="grr-mini__sub">Base normativa para início, padrão visual e conduta interna.</div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="grr-mini">
          <div class="grr-mini__label">Prioridade</div>
          <div class="grr-mini__value">Alta</div>
          <div class="grr-mini__sub">Leitura recomendada antes de qualquer atividade operacional.</div>
        </div>
      </div>

      <div class="col-12 col-md-4">
        <div class="grr-mini">
          <div class="grr-mini__label">Objetivo</div>
          <div class="grr-mini__value">Padrão</div>
          <div class="grr-mini__sub">Garantir uniformidade, disciplina e redução de falhas por desconhecimento.</div>
        </div>
      </div>
    </div>

    {{-- =========================
         ALERTA IMPORTANTE
    ========================== --}}
    <div class="grr-warn mb-4">
      <div class="grr-warn__ico">⚠️</div>

      <div class="grr-warn__body">
        <div class="grr-warn__title">Leitura obrigatória antes de atuar</div>
        <div class="grr-warn__text">
          O desconhecimento das normas <b>não exime responsabilidades</b>.
          Antes de iniciar atividades operacionais ou administrativas, é obrigatório
          conhecer e compreender todas as orientações aplicáveis.
        </div>
        <div class="grr-warn__tip">
          Recomendação: inicie por <b>Instruções Iniciais</b>, avance para <b>Fardamento</b>
          e finalize em <b>Regulamento Interno</b>.
        </div>
      </div>
    </div>

    {{-- =========================
         COMO COMEÇAR
    ========================== --}}
    <div class="grr-panel mb-4">
      <div class="grr-panel__inner">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <div class="fw-black grr-panel__title">✅ Como começar</div>
            <div class="grr-panel__sub">
              Siga a sequência abaixo para absorver o conteúdo da forma correta e manter o padrão institucional.
            </div>
          </div>

          <span class="grr-pill grr-pill--soft">
            Ordem recomendada: 1 → 2 → 3
          </span>
        </div>

        <div class="row g-3">
          <div class="col-md-4">
            <div class="grr-step h-100">
              <div class="grr-step__num">1</div>
              <div class="grr-step__title">Instruções Iniciais</div>
              <div class="grr-step__sub">
                Base de comunicação, postura, integração e procedimentos essenciais para início.
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="grr-step h-100">
              <div class="grr-step__num">2</div>
              <div class="grr-step__title">Fardamento</div>
              <div class="grr-step__sub">
                Padronização visual, identificação, distintivos e comandos utilizados em serviço.
              </div>
            </div>
          </div>

          <div class="col-md-4">
            <div class="grr-step h-100">
              <div class="grr-step__num">3</div>
              <div class="grr-step__title">Regulamento Interno</div>
              <div class="grr-step__sub">
                Regras, capítulos, deveres, vedações e condutas obrigatórias para permanência.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- =========================
         CARDS DE ACESSO
    ========================== --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
      <div>
        <h4 class="grr-section-title mb-1">Acessos principais</h4>
        <div class="grr-section-sub">
          Escolha abaixo a área que deseja consultar.
        </div>
      </div>
    </div>

    <div class="row g-3">

      {{-- Instruções --}}
      <div class="col-md-6 col-lg-4">
        <div class="grr-card h-100 grr-card--hover">
          <div class="grr-card__body d-flex flex-column h-100">
            <div class="d-flex align-items-start justify-content-between">
              <div class="grr-card__ico grr-card__ico--green">📌</div>
              <span class="grr-tag grr-tag--green">Recomendado</span>
            </div>

            <div class="grr-card__title mt-3">Instruções Iniciais</div>
            <div class="grr-card__sub mb-3">
              Diretrizes básicas para ingresso, comunicação, postura institucional
              e primeiros procedimentos dentro da GRR.
            </div>

            <div class="grr-card__meta mt-auto mb-3">
              Ideal para novos membros e consultas rápidas.
            </div>

            <a href="{{ route('regulamento.instrucoes') }}" class="btn grr-btn grr-btn--green w-100">
              Acessar conteúdo
            </a>
          </div>
        </div>
      </div>

      {{-- Fardamento --}}
      <div class="col-md-6 col-lg-4">
        <div class="grr-card h-100 grr-card--hover">
          <div class="grr-card__body d-flex flex-column h-100">
            <div class="d-flex align-items-start justify-content-between">
              <div class="grr-card__ico grr-card__ico--blue">👕</div>
              <span class="grr-tag grr-tag--blue">Padrão visual</span>
            </div>

            <div class="grr-card__title mt-3">Fardamento</div>
            <div class="grr-card__sub mb-3">
              Padrões oficiais de apresentação, organização visual, distintivos
              e comandos utilizados no ambiente RP.
            </div>

            <div class="grr-card__meta mt-auto mb-3">
              Consulta obrigatória antes de assumir patrulhamento.
            </div>

            <a href="{{ route('regulamento.fardamento') }}" class="btn grr-btn grr-btn--blue w-100">
              Acessar conteúdo
            </a>
          </div>
        </div>
      </div>

      {{-- Interno --}}
      <div class="col-md-6 col-lg-4">
        <div class="grr-card h-100 grr-card--hover">
          <div class="grr-card__body d-flex flex-column h-100">
            <div class="d-flex align-items-start justify-content-between">
              <div class="grr-card__ico grr-card__ico--red">🛡️</div>
              <span class="grr-tag grr-tag--red">Uso interno</span>
            </div>

            <div class="grr-card__title mt-3">Regulamento Interno</div>
            <div class="grr-card__sub mb-3">
              Documento com normas, capítulos, deveres, proibições e diretrizes
              formais de comportamento institucional.
            </div>

            <div class="grr-card__meta mt-auto mb-3">
              Leitura essencial para permanência e atuação correta.
            </div>

            <a href="{{ route('regulamento.interno') }}" class="btn grr-btn grr-btn--red-outline w-100">
              Acessar conteúdo
            </a>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>

<style>
  .grr-page{
    background: linear-gradient(180deg, #f8fafc 0%, #f4f7fb 100%);
    border: 1px solid rgba(15,23,42,.06);
    border-radius: 26px;
    padding: 20px;
    box-shadow:
      0 20px 60px rgba(2,6,23,.08),
      inset 0 1px 0 rgba(255,255,255,.75);
  }

  html[data-theme="dark"] .grr-page{
    background:
      radial-gradient(circle at top left, rgba(59,130,246,.07), transparent 30%),
      radial-gradient(circle at top right, rgba(16,185,129,.06), transparent 28%),
      linear-gradient(180deg, #0b1220 0%, #0f172a 100%) !important;
    border-color: rgba(255,255,255,.08) !important;
    box-shadow:
      0 24px 70px rgba(0,0,0,.45),
      inset 0 1px 0 rgba(255,255,255,.03);
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-page{
      background:
        radial-gradient(circle at top left, rgba(59,130,246,.07), transparent 30%),
        radial-gradient(circle at top right, rgba(16,185,129,.06), transparent 28%),
        linear-gradient(180deg, #0b1220 0%, #0f172a 100%) !important;
      border-color: rgba(255,255,255,.08) !important;
      box-shadow:
        0 24px 70px rgba(0,0,0,.45),
        inset 0 1px 0 rgba(255,255,255,.03);
    }
  }

  .grr-reg{
    --grr-card: rgba(13, 18, 28, .82);
    --grr-brd: rgba(255,255,255,.10);
    --grr-soft: rgba(255,255,255,.08);
    --grr-tx: rgba(231,237,246,.96);
    --grr-sub: rgba(231,237,246,.70);
    --grr-sub2: rgba(231,237,246,.50);
    --grr-shadow: 0 18px 60px rgba(0,0,0,.35);
    --grr-radius: 20px;
  }

  .fw-black{ font-weight: 900; }

  .grr-kicker{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: rgba(191,219,254,.88);
  }

  .grr-section-title{
    font-size: 1.05rem;
    font-weight: 900;
    color: #0f172a;
    margin: 0;
  }

  .grr-section-sub{
    font-size: 13px;
    font-weight: 700;
    color: #64748b;
  }

  html[data-theme="dark"] .grr-section-title{
    color: #f8fbff;
  }

  html[data-theme="dark"] .grr-section-sub{
    color: rgba(203,213,225,.72);
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-section-title{
      color: #f8fbff;
    }

    html[data-theme="system"] .grr-section-sub{
      color: rgba(203,213,225,.72);
    }
  }

  /* HERO */
  .grr-hero{
    position: relative;
    border-radius: var(--grr-radius);
    border: 1px solid rgba(15,23,42,.08);
    background: linear-gradient(180deg, rgba(9,13,20,.96), rgba(14,19,30,.92));
    box-shadow: 0 22px 60px rgba(2,6,23,.18);
    overflow: hidden;
    isolation: isolate;
  }

  .grr-hero::before{
    content:"";
    position:absolute;
    inset:0;
    background:
      linear-gradient(135deg, rgba(255,255,255,.05), transparent 35%),
      radial-gradient(700px 300px at 0% 0%, rgba(59,130,246,.14), transparent 60%),
      radial-gradient(700px 300px at 100% 0%, rgba(16,185,129,.10), transparent 60%);
    pointer-events:none;
  }

  .grr-hero__glow{
    position:absolute;
    border-radius:999px;
    filter: blur(50px);
    opacity: .35;
    pointer-events:none;
    z-index:0;
  }

  .grr-hero__glow--blue{
    width: 220px;
    height: 220px;
    background: rgba(59,130,246,.35);
    top: -40px;
    left: -50px;
  }

  .grr-hero__glow--green{
    width: 220px;
    height: 220px;
    background: rgba(16,185,129,.28);
    right: -40px;
    top: -50px;
  }

  .grr-hero__inner{
    position: relative;
    z-index: 1;
    padding: 24px;
  }

  .grr-hero__ico{
    width: 58px;
    height: 58px;
    border-radius: 18px;
    display:grid;
    place-items:center;
    background: linear-gradient(180deg, rgba(59,130,246,.22), rgba(59,130,246,.10));
    border: 1px solid rgba(59,130,246,.28);
    box-shadow: 0 16px 30px rgba(0,0,0,.22);
    flex-shrink: 0;
  }

  .grr-hero__emoji{
    font-size: 24px;
  }

  .grr-hero__title{
    color: var(--grr-tx);
    letter-spacing: -.02em;
  }

  .grr-hero__sub{
    color: var(--grr-sub);
    font-weight: 650;
    max-width: 720px;
    line-height: 1.55;
  }

  .grr-hero__side{
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .grr-hero__sidebox{
    min-width: 130px;
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.10);
    backdrop-filter: blur(8px);
    text-align: left;
  }

  .grr-hero__rightk{
    color: var(--grr-sub2);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .08em;
  }

  .grr-hero__rightv{
    color: var(--grr-tx);
    font-weight: 900;
    font-size: 15px;
  }

  /* Pills */
  .grr-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 7px 11px;
    border-radius: 999px;
    font-size: 12px;
    line-height: 1;
    font-weight: 900;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.07);
    color: var(--grr-tx);
    white-space: nowrap;
  }

  .grr-pill--blue{
    border-color: rgba(59,130,246,.35);
    background: rgba(59,130,246,.18);
    color: rgba(231,237,246,.96);
  }

  .grr-pill--soft{
    background: rgba(255,255,255,.05);
    color: rgba(231,237,246,.80);
  }

  /* Mini cards */
  .grr-mini{
    height: 100%;
    padding: 16px 16px 15px;
    border-radius: 18px;
    background: rgba(255,255,255,.86);
    border: 1px solid rgba(15,23,42,.06);
    box-shadow: 0 12px 28px rgba(2,6,23,.06);
  }

  .grr-mini__label{
    font-size: 12px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #64748b;
    margin-bottom: 6px;
  }

  .grr-mini__value{
    font-size: 1.45rem;
    font-weight: 900;
    color: #0f172a;
    line-height: 1.1;
    margin-bottom: 4px;
  }

  .grr-mini__sub{
    font-size: 12px;
    font-weight: 700;
    line-height: 1.5;
    color: #64748b;
  }

  html[data-theme="dark"] .grr-mini{
    background: linear-gradient(180deg, rgba(15,23,42,.72), rgba(15,23,42,.54));
    border-color: rgba(255,255,255,.08);
    box-shadow: 0 10px 24px rgba(0,0,0,.25);
  }

  html[data-theme="dark"] .grr-mini__label{
    color: rgba(148,163,184,.90);
  }

  html[data-theme="dark"] .grr-mini__value{
    color: #f8fbff;
  }

  html[data-theme="dark"] .grr-mini__sub{
    color: rgba(203,213,225,.82);
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-mini{
      background: linear-gradient(180deg, rgba(15,23,42,.72), rgba(15,23,42,.54));
      border-color: rgba(255,255,255,.08);
      box-shadow: 0 10px 24px rgba(0,0,0,.25);
    }

    html[data-theme="system"] .grr-mini__label{
      color: rgba(148,163,184,.90);
    }

    html[data-theme="system"] .grr-mini__value{
      color: #f8fbff;
    }

    html[data-theme="system"] .grr-mini__sub{
      color: rgba(203,213,225,.82);
    }
  }

  /* Warning */
  .grr-warn{
    display:flex;
    gap: 14px;
    align-items:flex-start;
    padding: 18px;
    border-radius: var(--grr-radius);
    background: linear-gradient(180deg, rgba(255,231,160,.96), rgba(255,217,102,.94));
    border: 1px solid rgba(180,120,0,.18);
    box-shadow: 0 16px 35px rgba(2,6,23,.10);
    color: rgba(17,24,39,.94);
  }

  .grr-warn__ico{
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    background: rgba(255,255,255,.32);
    border: 1px solid rgba(255,255,255,.45);
    font-size: 20px;
    flex-shrink: 0;
  }

  .grr-warn__title{
    font-weight: 950;
    margin-bottom: 3px;
  }

  .grr-warn__text{
    font-weight: 650;
    line-height: 1.55;
  }

  .grr-warn__tip{
    margin-top: 8px;
    font-size: 12px;
    font-weight: 850;
    color: rgba(17,24,39,.70);
  }

  html[data-theme="dark"] .grr-warn{
    background: linear-gradient(180deg, rgba(58,42,14,.95), rgba(40,29,8,.98));
    border-color: rgba(245,158,11,.18);
    box-shadow: 0 14px 34px rgba(0,0,0,.22);
    color: rgba(254,240,138,.94);
  }

  html[data-theme="dark"] .grr-warn__ico{
    background: rgba(245,158,11,.12);
    border-color: rgba(245,158,11,.20);
    color: #fbbf24;
  }

  html[data-theme="dark"] .grr-warn__title{
    color: #fde68a;
  }

  html[data-theme="dark"] .grr-warn__text{
    color: rgba(254,240,138,.82);
  }

  html[data-theme="dark"] .grr-warn__tip{
    color: rgba(254,240,138,.72);
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-warn{
      background: linear-gradient(180deg, rgba(58,42,14,.95), rgba(40,29,8,.98));
      border-color: rgba(245,158,11,.18);
      box-shadow: 0 14px 34px rgba(0,0,0,.22);
      color: rgba(254,240,138,.94);
    }

    html[data-theme="system"] .grr-warn__ico{
      background: rgba(245,158,11,.12);
      border-color: rgba(245,158,11,.20);
      color: #fbbf24;
    }

    html[data-theme="system"] .grr-warn__title{
      color: #fde68a;
    }

    html[data-theme="system"] .grr-warn__text{
      color: rgba(254,240,138,.82);
    }

    html[data-theme="system"] .grr-warn__tip{
      color: rgba(254,240,138,.72);
    }
  }

  /* Panel */
  .grr-panel{
    border-radius: var(--grr-radius);
    border: 1px solid rgba(15,23,42,.08);
    background: linear-gradient(180deg, rgba(13,18,28,.88), rgba(8,10,16,.84));
    box-shadow: 0 18px 50px rgba(2,6,23,.16);
    overflow:hidden;
  }

  .grr-panel__inner{
    padding: 18px;
  }

  .grr-panel__title{
    color: var(--grr-tx);
  }

  .grr-panel__sub{
    color: var(--grr-sub);
    font-weight: 700;
    font-size: 12px;
  }

  /* Steps */
  .grr-step{
    position: relative;
    padding: 16px;
    border-radius: 16px;
    background: rgba(255,255,255,.96);
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 10px 24px rgba(2,6,23,.10);
    overflow: hidden;
  }

  .grr-step::before{
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #10b981);
    opacity: .95;
  }

  .grr-step__num{
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display:grid;
    place-items:center;
    background: #0f172a;
    color: #fff;
    font-size: 13px;
    font-weight: 900;
    margin-bottom: 10px;
  }

  .grr-step__title{
    font-weight: 950;
    color: rgba(17,24,39,.92);
  }

  .grr-step__sub{
    font-size: 12px;
    font-weight: 750;
    color: rgba(17,24,39,.62);
    margin-top: 5px;
    line-height: 1.5;
  }

  html[data-theme="dark"] .grr-step{
    background: rgba(255,255,255,.96);
    border-color: rgba(15,23,42,.08);
    box-shadow: 0 10px 24px rgba(2,6,23,.10);
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-step{
      background: rgba(255,255,255,.96);
      border-color: rgba(15,23,42,.08);
      box-shadow: 0 10px 24px rgba(2,6,23,.10);
    }
  }

  /* Cards */
  .grr-card{
    border-radius: var(--grr-radius);
    border: 1px solid rgba(15,23,42,.08);
    background: linear-gradient(180deg, rgba(13,18,28,.90), rgba(8,10,16,.86));
    box-shadow: 0 16px 40px rgba(2,6,23,.14);
    overflow:hidden;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
  }

  .grr-card--hover:hover{
    transform: translateY(-4px);
    box-shadow: 0 22px 55px rgba(2,6,23,.18);
    border-color: rgba(59,130,246,.20);
  }

  .grr-card__body{
    padding: 18px;
  }

  .grr-card__title{
    font-weight: 950;
    color: var(--grr-tx);
    letter-spacing: .2px;
    font-size: 1.02rem;
  }

  .grr-card__sub{
    color: var(--grr-sub);
    font-weight: 700;
    font-size: 12px;
    line-height: 1.55;
  }

  .grr-card__meta{
    font-size: 11px;
    font-weight: 850;
    color: rgba(148,163,184,.88);
    padding-top: 10px;
    border-top: 1px dashed rgba(255,255,255,.10);
  }

  .grr-card__ico{
    width: 46px;
    height: 46px;
    border-radius: 14px;
    display:grid;
    place-items:center;
    font-size: 18px;
    border: 1px solid rgba(255,255,255,.12);
    background: rgba(255,255,255,.06);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.06);
  }

  .grr-card__ico--green{
    background: rgba(16,185,129,.12);
    border-color: rgba(16,185,129,.24);
  }

  .grr-card__ico--blue{
    background: rgba(59,130,246,.14);
    border-color: rgba(59,130,246,.24);
  }

  .grr-card__ico--red{
    background: rgba(239,68,68,.14);
    border-color: rgba(239,68,68,.24);
  }

  /* Tags */
  .grr-tag{
    display:inline-flex;
    align-items:center;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 950;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.06);
    color: var(--grr-tx);
    white-space: nowrap;
    letter-spacing: .02em;
  }

  .grr-tag--green{
    background: rgba(16,185,129,.12);
    border-color: rgba(16,185,129,.26);
  }

  .grr-tag--blue{
    background: rgba(59,130,246,.14);
    border-color: rgba(59,130,246,.26);
  }

  .grr-tag--red{
    background: rgba(239,68,68,.14);
    border-color: rgba(239,68,68,.26);
  }

  /* Buttons */
  .grr-btn{
    border-radius: 14px;
    font-weight: 950;
    padding: 11px 12px;
    transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
  }

  .grr-btn:hover{
    transform: translateY(-1px);
  }

  .grr-btn--green{
    background: linear-gradient(180deg, rgba(16,185,129,.92), rgba(5,150,105,.88));
    border: 1px solid rgba(16,185,129,.45);
    color: #04130d;
    box-shadow: 0 10px 24px rgba(16,185,129,.18);
  }

  .grr-btn--green:hover{
    color: #04130d;
    opacity: .96;
  }

  .grr-btn--blue{
    background: linear-gradient(180deg, rgba(59,130,246,.95), rgba(37,99,235,.90));
    border: 1px solid rgba(59,130,246,.45);
    color: #06101c;
    box-shadow: 0 10px 24px rgba(59,130,246,.18);
  }

  .grr-btn--blue:hover{
    color: #06101c;
    opacity: .96;
  }

  .grr-btn--red-outline{
    background: rgba(239,68,68,.04);
    border: 1px solid rgba(239,68,68,.45);
    color: rgba(255,105,105,.98);
    box-shadow: inset 0 1px 0 rgba(255,255,255,.02);
  }

  .grr-btn--red-outline:hover{
    background: rgba(239,68,68,.10);
    border-color: rgba(239,68,68,.72);
    color: rgba(255,255,255,.94);
  }

  /* Mobile */
  @media (max-width: 991.98px){
    .grr-hero__side{
      justify-content: flex-start;
    }
  }

  @media (max-width: 576px){
    .grr-page{
      padding: 14px;
      border-radius: 20px;
    }

    .grr-hero__inner{
      padding: 18px 16px;
    }

    .grr-panel__inner{
      padding: 16px 14px;
    }

    .grr-card__body{
      padding: 16px 14px;
    }

    .grr-mini{
      padding: 14px;
    }

    .grr-warn{
      padding: 14px;
    }

    .grr-hero__ico{
      width: 50px;
      height: 50px;
      border-radius: 16px;
    }

    .grr-hero__title{
      font-size: 1.12rem;
    }
  }
</style>
@endsection