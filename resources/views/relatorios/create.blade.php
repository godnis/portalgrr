@extends('layouts.app')

@section('content')

{{-- BANNER DE CONEXÃO --}}
<div id="connBannerTop" class="d-none" aria-live="polite">
  <div class="rep-conn-banner">
    <span class="rep-conn-banner__dot" aria-hidden="true"></span>
    <span>Sem conexão — seus dados estão salvos. Aguarde para enviar.</span>
  </div>
</div>

<style>
  .rep-create{
    --rep-radius-2xl: 26px;
    --rep-radius-xl: 22px;
    --rep-radius-lg: 18px;
    --rep-radius-md: 14px;
    --rep-radius-sm: 12px;

    --rep-card: #ffffff;
    --rep-card-2: #f8fafc;
    --rep-card-3: #f1f5f9;
    --rep-border: rgba(15, 23, 42, .08);
    --rep-border-2: rgba(15, 23, 42, .06);
    --rep-text: #0f172a;
    --rep-muted: #64748b;
    --rep-muted-2: #94a3b8;
    --rep-primary: #2563eb;
    --rep-primary-2: #1d4ed8;
    --rep-success: #059669;
    --rep-warning: #d97706;
    --rep-danger: #dc2626;
    --rep-shadow: 0 20px 60px rgba(15, 23, 42, .10);
    --rep-shadow-soft: 0 10px 30px rgba(15, 23, 42, .08);
    --rep-shadow-inset: inset 0 1px 0 rgba(255,255,255,.55);
  }

  html[data-theme="dark"] .rep-create{
    --rep-card: rgba(255,255,255,.06);
    --rep-card-2: rgba(255,255,255,.04);
    --rep-card-3: rgba(255,255,255,.05);
    --rep-border: rgba(255,255,255,.12);
    --rep-border-2: rgba(255,255,255,.10);
    --rep-text: rgba(231,237,246,.92);
    --rep-muted: rgba(231,237,246,.62);
    --rep-muted-2: rgba(231,237,246,.46);
    --rep-primary: #5aa2ff;
    --rep-primary-2: #3b82f6;
    --rep-success: #10b981;
    --rep-warning: #f59e0b;
    --rep-danger: #ef4444;
    --rep-shadow: 0 18px 55px rgba(0,0,0,.55);
    --rep-shadow-soft: 0 14px 40px rgba(0,0,0,.45);
    --rep-shadow-inset: none;
  }

  #connBannerTop{
    position: fixed;
    top: 12px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    width: min(980px, calc(100% - 24px));
    pointer-events: none;
  }

  .rep-conn-banner{
    display:flex;
    align-items:center;
    gap:10px;
    background: rgba(255,193,7,.16);
    border: 1px solid rgba(255,193,7,.35);
    color: #856404;
    border-radius: 999px;
    padding: 10px 14px;
    font-weight: 900;
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    backdrop-filter: blur(8px);
  }

  .rep-conn-banner__dot{
    width:10px;
    height:10px;
    border-radius:50%;
    background:#ffc107;
    box-shadow: 0 0 0 4px rgba(255,193,7,.20);
    flex: 0 0 auto;
  }

  .rep-shell{
    max-width: 1080px;
    margin: 0 auto;
    padding: 10px 12px 24px;
  }

  .rep-hero{
    position: relative;
    overflow: hidden;
    border-radius: var(--rep-radius-2xl);
    border: 1px solid var(--rep-border);
    background:
      radial-gradient(900px 420px at 0% 0%, rgba(37,99,235,.12), transparent 55%),
      radial-gradient(720px 340px at 100% 10%, rgba(16,185,129,.08), transparent 55%),
      linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.98));
    box-shadow: var(--rep-shadow);
    margin-bottom: 16px;
  }

  html[data-theme="dark"] .rep-hero{
    background: var(--rep-card);
  }

  .rep-hero__bg{
    position:absolute;
    inset:0;
    pointer-events:none;
    background:
      radial-gradient(1000px 520px at 18% 18%, rgba(37,99,235,.14), transparent 60%),
      radial-gradient(900px 520px at 85% 18%, rgba(16,185,129,.10), transparent 55%),
      radial-gradient(800px 520px at 50% 120%, rgba(2,6,23,.10), transparent 60%);
  }

  html[data-theme="dark"] .rep-hero__bg{
    background:
      radial-gradient(1000px 520px at 20% 20%, rgba(90,162,255,.22), transparent 60%),
      radial-gradient(900px 520px at 85% 25%, rgba(16,185,129,.14), transparent 55%),
      radial-gradient(800px 520px at 50% 115%, rgba(0,0,0,.65), transparent 60%),
      linear-gradient(180deg, rgba(8,13,20,.25), rgba(8,13,20,.88));
  }

  .rep-hero__content{
    position: relative;
    z-index: 1;
    padding: 22px;
    display:grid;
    grid-template-columns: minmax(0, 1.15fr) minmax(280px, .85fr);
    gap: 18px;
    align-items: stretch;
  }

  @media (max-width: 992px){
    .rep-hero__content{ grid-template-columns: 1fr; }
  }

  .rep-kicker{
    font-size: 11px;
    font-weight: 950;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: var(--rep-muted);
  }

  .rep-title{
    margin: 8px 0 8px;
    font-size: clamp(28px, 3vw, 36px);
    line-height: 1.04;
    font-weight: 950;
    letter-spacing: -.05em;
    color: var(--rep-text);
  }

  .rep-sub{
    color: var(--rep-muted);
    font-size: 14px;
    font-weight: 700;
    line-height: 1.55;
    max-width: 760px;
  }

  .rep-badges{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:14px;
  }

  .rep-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid rgba(37,99,235,.16);
    background: rgba(37,99,235,.08);
    color: var(--rep-text);
    font-size: 12px;
    font-weight: 900;
    box-shadow: var(--rep-shadow-inset);
  }

  html[data-theme="dark"] .rep-badge{
    border-color: rgba(90,162,255,.26);
    background: rgba(90,162,255,.12);
  }

  .rep-badge--soft{
    border-color: var(--rep-border);
    background: rgba(255,255,255,.7);
    color: var(--rep-muted);
  }

  html[data-theme="dark"] .rep-badge--soft{
    background: rgba(255,255,255,.06);
    color: rgba(231,237,246,.74);
  }

  .rep-badge__dot{
    width:8px;
    height:8px;
    border-radius:50%;
    background: var(--rep-primary);
    box-shadow: 0 0 0 4px rgba(37,99,235,.14);
  }

  .rep-mini{
    height: 100%;
    border-radius: 18px;
    border: 1px solid var(--rep-border);
    background: rgba(255,255,255,.75);
    box-shadow: var(--rep-shadow-soft);
    overflow: hidden;
    backdrop-filter: blur(10px);
  }

  html[data-theme="dark"] .rep-mini{
    background: rgba(15,20,28,.55);
  }

  .rep-mini__item{
    padding: 14px 15px;
    border-top: 1px solid var(--rep-border-2);
  }

  .rep-mini__item:first-child{ border-top: 0; }

  .rep-mini__k{
    font-size: 11px;
    font-weight: 950;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--rep-muted);
  }

  .rep-mini__v{
    margin-top: 4px;
    font-size: 13px;
    line-height: 1.5;
    font-weight: 800;
    color: var(--rep-text);
  }

  .rep-alert{
    border-radius: 18px;
    border: 1px solid var(--rep-border);
    box-shadow: var(--rep-shadow-soft);
  }

  .rep-form-card{
    border: 1px solid var(--rep-border);
    background: var(--rep-card);
    border-radius: var(--rep-radius-2xl);
    box-shadow: var(--rep-shadow);
    overflow: hidden;
  }

  .rep-form-card__head{
    padding: 16px 18px;
    border-bottom: 1px solid var(--rep-border);
    background:
      linear-gradient(180deg, rgba(148,163,184,.06), rgba(148,163,184,.03));
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 12px;
    flex-wrap: wrap;
  }

  .rep-form-card__title{
    font-size: 18px;
    font-weight: 950;
    letter-spacing: -.03em;
    color: var(--rep-text);
  }

  .rep-form-card__sub{
    margin-top: 4px;
    font-size: 13px;
    font-weight: 700;
    color: var(--rep-muted);
  }

  .rep-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 900;
    border: 1px solid rgba(37,99,235,.18);
    background: rgba(37,99,235,.08);
    color: var(--rep-text);
  }

  html[data-theme="dark"] .rep-pill{
    border-color: rgba(90,162,255,.28);
    background: rgba(90,162,255,.12);
  }

  .rep-pill__dot{
    width:8px;
    height:8px;
    border-radius:50%;
    background: var(--rep-primary);
    box-shadow: 0 0 0 4px rgba(37,99,235,.14);
  }

  .rep-form-card__body{
    padding: 18px;
  }

  .rep-info-bar{
    border: 1px solid rgba(37,99,235,.16);
    background: rgba(37,99,235,.06);
    color: var(--rep-text);
    border-radius: 18px;
    padding: 12px 14px;
    font-weight: 800;
    margin-bottom: 18px;
  }

  html[data-theme="dark"] .rep-info-bar{
    border-color: rgba(90,162,255,.24);
    background: rgba(90,162,255,.10);
  }

  .rep-info-bar__badge{
    display:inline-flex;
    align-items:center;
    padding: 6px 10px;
    border-radius: 999px;
    background: rgba(255,255,255,.8);
    border: 1px solid var(--rep-border);
    font-weight: 900;
    margin-left: 6px;
  }

  html[data-theme="dark"] .rep-info-bar__badge{
    background: rgba(255,255,255,.06);
  }

  .rep-section{
    margin-bottom: 18px;
    border: 1px solid var(--rep-border);
    border-radius: 20px;
    background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.94));
    box-shadow: var(--rep-shadow-soft);
    overflow: hidden;
  }

  html[data-theme="dark"] .rep-section{
    background: linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.035));
  }

  .rep-section__head{
    padding: 14px 16px;
    border-bottom: 1px solid var(--rep-border);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 12px;
    flex-wrap: wrap;
    background: rgba(148,163,184,.05);
  }

  .rep-section__title{
    font-size: 15px;
    font-weight: 950;
    color: var(--rep-text);
    letter-spacing: -.02em;
  }

  .rep-section__sub{
    font-size: 12px;
    font-weight: 800;
    color: var(--rep-muted);
  }

  .rep-section__body{
    padding: 16px;
  }

  .rep-field .form-label{
    color: var(--rep-text);
    font-weight: 900;
    margin-bottom: 6px;
  }

  .rep-field .form-control,
  .rep-field .form-select{
    border-radius: 14px !important;
    min-height: 48px;
    border: 1px solid var(--rep-border) !important;
    background: rgba(255,255,255,.92) !important;
    color: var(--rep-text) !important;
    font-weight: 700;
    box-shadow: none !important;
  }

  html[data-theme="dark"] .rep-field .form-control,
  html[data-theme="dark"] .rep-field .form-select{
    background: rgba(15,23,42,.82) !important;
    border-color: rgba(255,255,255,.12) !important;
  }

  .rep-field .form-control::placeholder{
    color: var(--rep-muted-2);
  }

  .rep-field .form-control:focus,
  .rep-field .form-select:focus{
    border-color: rgba(37,99,235,.45) !important;
    box-shadow: 0 0 0 4px rgba(37,99,235,.10) !important;
  }

  html[data-theme="dark"] .rep-field .form-control:focus,
  html[data-theme="dark"] .rep-field .form-select:focus{
    border-color: rgba(90,162,255,.55) !important;
    box-shadow: 0 0 0 4px rgba(90,162,255,.16) !important;
  }

  .rep-field .input-group-text{
    border-color: var(--rep-border) !important;
    background: rgba(248,250,252,.96) !important;
    color: var(--rep-text);
    font-weight: 900;
  }

  html[data-theme="dark"] .rep-field .input-group-text{
    background: rgba(255,255,255,.06) !important;
    border-color: rgba(255,255,255,.12) !important;
  }

  .rep-static-input{
    display:flex;
    align-items:center;
    min-height:48px;
    border-radius:14px;
    border:1px solid var(--rep-border);
    background: rgba(248,250,252,.95);
    padding: 0 14px;
    font-weight: 800;
    color: var(--rep-text);
  }

  html[data-theme="dark"] .rep-static-input{
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.12);
  }

  .rep-feedback{
    min-height: 18px;
    font-size: 12px;
    line-height: 1.4;
  }

  .rep-form-note{
    font-size: 12px;
    color: var(--rep-muted);
    font-weight: 700;
    margin-top: 6px;
  }

  .rep-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:center;
    margin-top: 4px;
  }

  .rep-btn{
    border-radius: 14px !important;
    min-height: 46px;
    padding-inline: 16px;
    font-weight: 900 !important;
    box-shadow: var(--rep-shadow-soft);
  }

  .rep-btn-primary{
    background: linear-gradient(135deg, var(--rep-primary), var(--rep-primary-2)) !important;
    border-color: transparent !important;
  }

  .rep-footer-note{
    margin-top: 14px;
    color: var(--rep-muted);
    font-size: 12px;
    font-weight: 800;
  }

  .is-dup-rg{
    border-color: #ffc107 !important;
    box-shadow: 0 0 0 .15rem rgba(255,193,7,.25) !important;
  }

  @media (max-width: 768px){
    .rep-shell{ padding-inline: 8px; }
    .rep-title{ font-size: 25px; }
    .rep-actions .rep-btn{ width: 100%; justify-content: center; }
  }
</style>

<div class="rep-create">
  <div class="rep-shell">

    {{-- HERO --}}
    <div class="rep-hero">
      <div class="rep-hero__bg"></div>

      <div class="rep-hero__content">
        <div>
          <div class="rep-kicker">GRR • PRF — Registro Operacional</div>
          <h1 class="rep-title">Novo Relatório de Patrulhamento</h1>
          <div class="rep-sub">
            Preencha o relatório com atenção. Os dados serão registrados no fluxo interno de auditoria, revisão e aprovação administrativa.
          </div>

          <div class="rep-badges">
            <span class="rep-badge">
              <span class="rep-badge__dot"></span>
              envio interno
            </span>

            <span class="rep-badge rep-badge--soft">
              rascunho automático ativo
            </span>
          </div>
        </div>

        <div>
          <div class="rep-mini">
            <div class="rep-mini__item">
              <div class="rep-mini__k">Fluxo do relatório</div>
              <div class="rep-mini__v">Preenchimento → envio → revisão → aprovação ou reprovação.</div>
            </div>

            <div class="rep-mini__item">
              <div class="rep-mini__k">Segurança operacional</div>
              <div class="rep-mini__v">Os horários são vinculados ao relógio do sistema, com suporte a rascunho local e prevenção contra perda de dados.</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- FLASH / ERROS --}}
    @if ($errors->any())
      <div class="alert alert-danger rep-alert">
        <b>Corrija os campos abaixo:</b>
        <ul class="mb-0 mt-2">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(session('success'))
      <div class="alert alert-success rep-alert">
        {{ session('success') }}
      </div>
    @endif

    @if(session('ok'))
      <div class="alert alert-success rep-alert">
        {{ session('ok') }}
      </div>
    @endif

    <form id="relatorioForm" method="POST" action="{{ route('relatorios.store') }}" class="rep-form-card">
      @csrf

      <input type="hidden" name="redirect_to" id="redirect_to" value="{{ route('dashboard') }}">

      <input
        type="hidden"
        name="inicio_patrulhamento"
        value="{{ old('inicio_patrulhamento', $inicio_registrado ?? now()->format('H:i')) }}"
      >

      <input type="hidden" name="client_token" id="client_token" value="{{ old('client_token') }}">

      <div class="rep-form-card__head">
        <div>
          <div class="rep-form-card__title">Formulário operacional</div>
          <div class="rep-form-card__sub">Campos obrigatórios e indicadores operacionais da patrulha.</div>
        </div>

        <div class="rep-pill">
          <span class="rep-pill__dot"></span>
          preenchimento interno
        </div>
      </div>

      <div class="rep-form-card__body">

        <div class="rep-info-bar">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
              <span class="me-2">⏱️</span>
              <span>Início registrado automaticamente:</span>
              <span class="rep-info-bar__badge">
                {{ old('inicio_patrulhamento', $inicio_registrado ?? now()->format('H:i')) }}
              </span>
            </div>
            <small class="text-muted fw-semibold">A hora final será registrada automaticamente no envio.</small>
          </div>
        </div>

        {{-- 1) GUARNIÇÃO --}}
        <section class="rep-section">
          <div class="rep-section__head">
            <div>
              <div class="rep-section__title">Guarnição</div>
              <div class="rep-section__sub">Composição da equipe e identificação da unidade.</div>
            </div>
            <div class="rep-section__sub">* obrigatório</div>
          </div>

          <div class="rep-section__body">
            <div class="row g-3">
              <div class="col-md-3 rep-field">
                <label class="form-label">Chefe (RG) *</label>
                <div class="rep-static-input">{{ auth()->user()->rg }}</div>
                <input type="hidden" name="qra_chefe" value="{{ auth()->user()->rg }}">
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Unidade *</label>
                <select name="unidade" class="form-select @error('unidade') is-invalid @enderror" required>
                  <option value="">Selecione</option>
                  <option value="GRR-01 CMD" @selected(old('unidade')=='GRR-01 CMD')>GRR-01 CMD</option>
                  <option value="GRR-02 CRD" @selected(old('unidade')=='GRR-02 CRD')>GRR-02 CRD</option>
                  <option value="GRR-03 SUP-A" @selected(old('unidade')=='GRR-03 SUP-A')>GRR-03 SUP-A</option>
                  <option value="GRR-04 SUP-B" @selected(old('unidade')=='GRR-04 SUP-B')>GRR-04 SUP-B</option>
                  <option value="GRR-05" @selected(old('unidade')=='GRR-05')>GRR-05</option>
                  <option value="GRR-06" @selected(old('unidade')=='GRR-06')>GRR-06</option>
                  <option value="GRR-10" @selected(old('unidade')=='GRR-10')>GRR-10</option>
                  <option value="GRR-11" @selected(old('unidade')=='GRR-11')>GRR-11</option>
                  <option value="GRR-15" @selected(old('unidade')=='GRR-15')>GRR-15</option>
                  <option value="GRR-16" @selected(old('unidade')=='GRR-16')>GRR-16</option>
                  <option value="GRR-17" @selected(old('unidade')=='GRR-17')>GRR-17</option>
                  <option value="GRR-18" @selected(old('unidade')=='GRR-18')>GRR-18</option>
                  <option value="GRR-17" @selected(old('unidade')=='GRR-17')>GRR-22063</option>
                  <option value="GRR-18" @selected(old('unidade')=='GRR-18')>GRR-511</option>
                  <option value="Batedor 01" @selected(old('unidade')=='Batedor 01')>Batedor 01</option>
                  <option value="Batedor 02" @selected(old('unidade')=='Batedor 02')>Batedor 02</option>
                  <option value="Batedor 03" @selected(old('unidade')=='Batedor 03')>Batedor 03</option>
                  <option value="Administrativo" @selected(old('unidade')=='Administrativo')>Administrativo</option>
                  <option value="Blitz" @selected(old('unidade')=='Blitz')>Plantão Base</option>
                  <option value="Guincho" @selected(old('unidade')=='Guincho')>Guincho</option>
                  <option value="DEJEM" @selected(old('unidade')=='DEJEM')>DEJEM</option>
                </select>
                @error('unidade') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Motorista (RG) *</label>
                <input
                  name="motorista"
                  class="form-control @error('motorista') is-invalid @enderror"
                  value="{{ old('motorista') }}"
                  required
                  placeholder="Ex.: 12178"
                >
                @error('motorista') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="rep-feedback mt-1" data-rg-feedback="motorista"></div>
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Auxiliar P3 (RG)</label>
                <input
                  name="terceiro"
                  class="form-control @error('terceiro') is-invalid @enderror"
                  value="{{ old('terceiro') }}"
                  placeholder="Opcional"
                >
                @error('terceiro') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="rep-feedback mt-1" data-rg-feedback="terceiro"></div>
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Segurança P4 (RG)</label>
                <input
                  name="quarto"
                  class="form-control @error('quarto') is-invalid @enderror"
                  value="{{ old('quarto') }}"
                  placeholder="Opcional"
                >
                @error('quarto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="rep-feedback mt-1" data-rg-feedback="quarto"></div>
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Segurança P5 (RG)</label>
                <input
                  name="quinto"
                  class="form-control @error('quinto') is-invalid @enderror"
                  value="{{ old('quinto') }}"
                  placeholder="Opcional"
                >
                @error('quinto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="rep-feedback mt-1" data-rg-feedback="quinto"></div>
              </div>
            </div>
          </div>
        </section>

        {{-- 2) APREENSÕES --}}
        <section class="rep-section">
          <div class="rep-section__head">
            <div>
              <div class="rep-section__title">Apreensões</div>
              <div class="rep-section__sub">Preencha apenas com números. Se não houver, deixe em branco.</div>
            </div>
          </div>

          <div class="rep-section__body">
            <div class="row g-3">
              <div class="col-md-3 rep-field">
                <label class="form-label">Pistolas</label>
                <input name="pistolas" class="form-control @error('pistolas') is-invalid @enderror"
                       value="{{ old('pistolas') }}" placeholder="0" inputmode="numeric">
                @error('pistolas') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">SMG / Fuzil</label>
                <input name="smg_fuzil" class="form-control @error('smg_fuzil') is-invalid @enderror"
                       value="{{ old('smg_fuzil') }}" placeholder="0" inputmode="numeric">
                @error('smg_fuzil') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Munições</label>
                <input name="municoes" class="form-control @error('municoes') is-invalid @enderror"
                       value="{{ old('municoes') }}" placeholder="0" inputmode="numeric">
                @error('municoes') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Drogas</label>
                <input name="drogas" class="form-control @error('drogas') is-invalid @enderror"
                       value="{{ old('drogas') }}" placeholder="0" inputmode="numeric">
                @error('drogas') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Explosivos</label>
                <input name="explosivos" class="form-control @error('explosivos') is-invalid @enderror"
                       value="{{ old('explosivos') }}" placeholder="0" inputmode="numeric">
                @error('explosivos') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Lockpicks</label>
                <input name="lockpicks" class="form-control @error('lockpicks') is-invalid @enderror"
                       value="{{ old('lockpicks') }}" placeholder="0" inputmode="numeric">
                @error('lockpicks') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Dinheiro</label>
                <div class="input-group">
                  <span class="input-group-text" style="border-radius:14px 0 0 14px;">R$</span>
                  <input
                    name="dinheiro"
                    class="form-control @error('dinheiro') is-invalid @enderror"
                    value="{{ old('dinheiro') }}"
                    placeholder="0"
                    inputmode="numeric"
                    style="border-radius:0 14px 14px 0;"
                  >
                </div>
                @error('dinheiro') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                <div class="rep-form-note">Digite apenas números. Ex.: 1500.</div>
              </div>
            </div>
          </div>
        </section>

        {{-- 3) MULTAS / AÇÕES --}}
        <section class="rep-section">
          <div class="rep-section__head">
            <div>
              <div class="rep-section__title">Multas / Ações</div>
              <div class="rep-section__sub">Indicadores operacionais da patrulha.</div>
            </div>
          </div>

          <div class="rep-section__body">
            <div class="row g-3">
              <div class="col-md-3 rep-field">
                <label class="form-label">Abordagens</label>
                <input name="abordagens" class="form-control @error('abordagens') is-invalid @enderror"
                       value="{{ old('abordagens') }}" placeholder="0" inputmode="numeric">
                @error('abordagens') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Apoio</label>
                <input name="apoio" class="form-control @error('apoio') is-invalid @enderror"
                       value="{{ old('apoio') }}" placeholder="0" inputmode="numeric">
                @error('apoio') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Incursão</label>
                <input name="incursao" class="form-control @error('incursao') is-invalid @enderror"
                       value="{{ old('incursao') }}" placeholder="0" inputmode="numeric">
                @error('incursao') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Negociação</label>
                <input name="negociacao" class="form-control @error('negociacao') is-invalid @enderror"
                       value="{{ old('negociacao') }}" placeholder="0" inputmode="numeric">
                @error('negociacao') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Blitz</label>
                <input name="blitz" class="form-control @error('blitz') is-invalid @enderror"
                       value="{{ old('blitz') }}" placeholder="0" inputmode="numeric">
                @error('blitz') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Escolta</label>
                <input name="escolta" class="form-control @error('escolta') is-invalid @enderror"
                       value="{{ old('escolta') }}" placeholder="0" inputmode="numeric">
                @error('escolta') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Multas</label>
                <input name="multas" class="form-control @error('multas') is-invalid @enderror"
                       value="{{ old('multas') }}" placeholder="0" inputmode="numeric">
                @error('multas') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">BOPM</label>
                <input name="bopm" class="form-control @error('bopm') is-invalid @enderror"
                       value="{{ old('bopm') }}" placeholder="0" inputmode="numeric">
                @error('bopm') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Viaturas fiscalizadas</label>
                <input name="viaturas_fiscalizadas" class="form-control @error('viaturas_fiscalizadas') is-invalid @enderror"
                       value="{{ old('viaturas_fiscalizadas') }}" placeholder="0" inputmode="numeric">
                @error('viaturas_fiscalizadas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="rep-form-note">Só vale para CMD / CRD / SUP, conforme regra institucional.</div>
              </div>
            </div>
          </div>
        </section>

        {{-- 4) OBSERVAÇÕES --}}
        <section class="rep-section">
          <div class="rep-section__head">
            <div>
              <div class="rep-section__title">Observações gerais</div>
              <div class="rep-section__sub">Registre informações complementares relevantes da ocorrência.</div>
            </div>
          </div>

          <div class="rep-section__body">
            <div class="rep-field">
              <label class="form-label">Observações</label>
              <textarea
                name="observacoes"
                rows="4"
                class="form-control @error('observacoes') is-invalid @enderror"
                placeholder="Ex.: ocorrência relevante, local, apoio de outras unidades..."
                style="font-weight:700; min-height:120px;"
              >{{ old('observacoes') }}</textarea>
              @error('observacoes') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          </div>
        </section>

        <div class="rep-actions">
          <button id="btnSubmit" class="btn btn-primary rep-btn rep-btn-primary">
            Enviar relatório
          </button>

          <button type="button" id="relDraftClear" class="btn btn-outline-secondary rep-btn">
            Limpar rascunho
          </button>

          <button type="button" id="btnCancelForm" class="btn btn-outline-danger rep-btn">
            Cancelar formulário
          </button>
        </div>

        <div class="rep-footer-note">
          Ao enviar, o relatório ficará registrado no sistema e seguirá o fluxo interno de revisão e aprovação.
        </div>

      </div>
    </form>

  </div>
</div>

<script>
(function () {
  const sel = document.querySelector('select[name="unidade"]');
  const redirect = document.getElementById('redirect_to');
  if (!sel || !redirect) return;

  const dashBase = @json(route('dashboard'));

  function updateRedirect() {
    const unidade = (sel.value || '').trim();

    if (!unidade) {
      redirect.value = dashBase;
      return;
    }

    const url = new URL(dashBase, window.location.origin);
    url.searchParams.set('unidade', unidade);
    redirect.value = url.toString();
  }

  sel.addEventListener('change', updateRedirect);
  updateRedirect();
})();
</script>

<script>
(function () {
  const endpoint = @json(route('usuarios.por_rg'));

  const fields = [
    { name: 'motorista', label: 'Motorista' },
    { name: 'terceiro',  label: 'P3' },
    { name: 'quarto',    label: 'P4' },
    { name: 'quinto',    label: 'P5' },
  ];

  const getInput = (n) => document.querySelector(`[name="${n}"]`);
  const getBox   = (n) => document.querySelector(`[data-rg-feedback="${n}"]`);

  const onlyDigits = (v) => (v || '').toString().replace(/\D+/g, '');

  const debounce = (fn, ms = 350) => {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  };

  function setMsg(box, type, html) {
    if (!box) return;
    box.className = 'rep-feedback mt-1';

    if (type === 'ok') box.classList.add('text-success', 'fw-semibold');
    if (type === 'bad') box.classList.add('text-danger', 'fw-semibold');
    if (type === 'warn') box.classList.add('text-warning', 'fw-semibold');
    if (type === 'muted') box.classList.add('text-muted');

    box.innerHTML = html || '';
  }

  async function lookup(rg) {
    const url = new URL(endpoint, window.location.origin);
    url.searchParams.set('rg', rg);

    const res = await fetch(url.toString(), {
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin',
    });

    return await res.json();
  }

  function clearDupWarnings() {
    fields.forEach(f => {
      const box = getBox(f.name);
      if (!box) return;
      box.querySelectorAll('[data-dup="1"]').forEach(el => el.remove());
    });

    fields.forEach(f => {
      const input = getInput(f.name);
      if (input) input.classList.remove('is-dup-rg');
    });
  }

  function applyDupWarnings() {
    clearDupWarnings();

    const values = fields
      .map(f => ({ ...f, rg: onlyDigits(getInput(f.name)?.value) }))
      .filter(x => x.rg);

    const count = values.reduce((acc, x) => {
      acc[x.rg] = (acc[x.rg] || 0) + 1;
      return acc;
    }, {});

    values.forEach(x => {
      if (count[x.rg] <= 1) return;

      const box = getBox(x.name);
      const input = getInput(x.name);
      if (!box) return;

      if (input) input.classList.add('is-dup-rg');

      const warn = document.createElement('div');
      warn.setAttribute('data-dup', '1');
      warn.className = 'mt-1 text-warning fw-semibold';
      warn.textContent = '⚠️ RG repetido em mais de um campo.';
      box.appendChild(warn);
    });
  }

  const run = debounce(async (fieldName) => {
    const input = getInput(fieldName);
    const box = getBox(fieldName);
    if (!input || !box) return;

    const rg = onlyDigits(input.value);

    if (!rg) {
      setMsg(box, 'muted', '');
      applyDupWarnings();
      return;
    }

    if (rg.length < 3) {
      setMsg(box, 'muted', 'Digite mais dígitos para buscar…');
      applyDupWarnings();
      return;
    }

    setMsg(box, 'muted', 'Buscando…');

    try {
      const data = await lookup(rg);

      if (!data?.ok) {
        setMsg(box, 'bad', 'Erro na busca.');
        applyDupWarnings();
        return;
      }

      if (!data.found) {
        setMsg(box, 'bad', `❌ RG <span class="fw-bold">${rg}</span> não encontrado no efetivo.`);
        applyDupWarnings();
        return;
      }

      const statusExtra = data.status ? ` • ${data.status}` : '';
      setMsg(
        box,
        'ok',
        `✅ <span class="fw-bold">${data.name}</span>${data.cargo ? ` <span class="text-muted">(${data.cargo}${statusExtra})</span>` : ''}`
      );

      applyDupWarnings();
    } catch (e) {
      setMsg(box, 'bad', 'Erro de conexão na busca.');
      applyDupWarnings();
    }
  }, 350);

  fields.forEach(f => {
    const input = getInput(f.name);
    if (!input) return;

    input.addEventListener('input', () => run(f.name));
    input.addEventListener('change', () => run(f.name));
    input.addEventListener('blur', () => run(f.name));
  });

  setTimeout(applyDupWarnings, 0);
})();
</script>

<script>
(function () {
  const form = document.getElementById('relatorioForm');
  if (!form) return;

  const DRAFT_KEY = 'relatorio_create_draft_v1';
  const TOKEN_KEY = 'relatorio_client_token_v1';
  const PENDING_KEY = 'relatorio_pending_submit_v1';
  const clearBtn = document.getElementById('relDraftClear');

  const IGNORE_START_TIME = false;

  const SHOULD_CLEAR = {{ session()->has('clear_relatorio_draft') ? 'true' : 'false' }};
  if (SHOULD_CLEAR) {
    try { localStorage.removeItem(DRAFT_KEY); } catch(e) {}
    try { localStorage.removeItem(TOKEN_KEY); } catch(e) {}
    try { localStorage.removeItem(PENDING_KEY); } catch(e) {}
  }

  function ensureClientToken(){
    const input = document.getElementById('client_token');
    if (!input) return null;

    let token = (input.value || '').trim();

    if (!token) {
      try { token = (localStorage.getItem(TOKEN_KEY) || '').trim(); } catch(e) {}
    }

    if (!token) {
      try {
        token = (crypto?.randomUUID?.() || (Date.now() + '-' + Math.random().toString(16).slice(2)));
      } catch(e) {
        token = (Date.now() + '-' + Math.random().toString(16).slice(2));
      }
    }

    input.value = token;
    try { localStorage.setItem(TOKEN_KEY, token); } catch(e) {}

    return token;
  }

  ensureClientToken();

  const fields = () => form.querySelectorAll('input[name], select[name], textarea[name]');

  const saveDraft = () => {
    const data = {};
    fields().forEach(el => {
      const name = el.name;
      if (!name) return;

      if (name === '_token') return;
      if (el.type === 'file') return;
      if (IGNORE_START_TIME && name === 'inicio_patrulhamento') return;

      if (el.type === 'radio') {
        if (el.checked) data[name] = el.value;
      } else if (el.type === 'checkbox') {
        data[name] = el.checked ? '1' : '0';
      } else {
        data[name] = el.value;
      }
    });

    try { localStorage.setItem(DRAFT_KEY, JSON.stringify(data)); } catch (e) {}
  };

  const loadDraft = () => {
    let raw = null;
    try { raw = localStorage.getItem(DRAFT_KEY); } catch (e) {}
    if (!raw) return;

    let data = null;
    try { data = JSON.parse(raw); } catch (e) { return; }
    if (!data || typeof data !== 'object') return;

    Object.keys(data).forEach(name => {
      const value = data[name];
      const els = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
      if (!els.length) return;

      const first = els[0];

      if (first.type === 'radio') {
        const target = form.querySelector(`input[type="radio"][name="${CSS.escape(name)}"][value="${CSS.escape(value)}"]`);
        if (target) target.checked = true;
      } else if (first.type === 'checkbox') {
        first.checked = (value === '1');
      } else {
        first.value = value;
      }
    });
  };

  const clearDraft = () => {
    try { localStorage.removeItem(DRAFT_KEY); } catch (e) {}
    try { localStorage.removeItem(TOKEN_KEY); } catch (e) {}
    try { localStorage.removeItem(PENDING_KEY); } catch (e) {}
    form.reset();

    document.querySelectorAll('[data-rg-feedback]').forEach(el => el.innerHTML = '');

    ensureClientToken();
  };

  loadDraft();
  ensureClientToken();

  ['motorista','terceiro','quarto','quinto'].forEach((n) => {
    const el = form.querySelector(`[name="${n}"]`);
    if (el && (el.value || '').trim().length) {
      el.dispatchEvent(new Event('input', { bubbles: true }));
    }
  });

  form.addEventListener('input', saveDraft);
  form.addEventListener('change', saveDraft);

  clearBtn?.addEventListener('click', clearDraft);

  const sentOk = {{ (session()->has('success') || session()->has('ok')) ? 'true' : 'false' }};
  if (sentOk) clearDraft();
})();
</script>

<script>
(function () {
  const banner = document.getElementById('connBannerTop');
  const form = document.getElementById('relatorioForm');
  const btn = document.getElementById('btnSubmit');

  const PENDING_KEY = 'relatorio_pending_submit_v1';

  function showBanner(show) {
    if (!banner) return;
    banner.classList.toggle('d-none', !show);
  }

  function setBtnState(online){
    if (!btn) return;

    if (!online) {
      btn.disabled = true;
      btn.innerText = 'Sem conexão';
      btn.classList.remove('btn-primary');
      btn.classList.add('btn-outline-secondary');
    } else {
      btn.disabled = false;
      btn.innerText = 'Enviar relatório';
      btn.classList.add('btn-primary');
      btn.classList.remove('btn-outline-secondary');
    }
  }

  function updateStatus() {
    const online = navigator.onLine;

    showBanner(!online);
    setBtnState(online);

    if (online && localStorage.getItem(PENDING_KEY) === '1') {
      localStorage.removeItem(PENDING_KEY);
      form?.submit();
    }
  }

  window.addEventListener('online', updateStatus);
  window.addEventListener('offline', updateStatus);

  form?.addEventListener('submit', function (e) {
    if (!navigator.onLine) {
      e.preventDefault();
      localStorage.setItem(PENDING_KEY, '1');
      updateStatus();
      return false;
    }
  });

  updateStatus();
})();
</script>

<script>
(function () {
  const btn = document.getElementById('btnCancelForm');
  if (!btn) return;

  const DRAFT_KEY   = 'relatorio_create_draft_v1';
  const TOKEN_KEY   = 'relatorio_client_token_v1';
  const PENDING_KEY = 'relatorio_pending_submit_v1';

  btn.addEventListener('click', function () {
    const ok = confirm(
      'Cancelar este formulário?\n\n' +
      '• Seus dados preenchidos serão apagados\n' +
      '• Você voltará para a lista de relatórios'
    );

    if (!ok) return;

    try { localStorage.removeItem(DRAFT_KEY); } catch(e) {}
    try { localStorage.removeItem(TOKEN_KEY); } catch(e) {}
    try { localStorage.removeItem(PENDING_KEY); } catch(e) {}

    window.location.href = @json(route('relatorios.index'));
  });
})();
</script>

<script>
document.addEventListener('submit', function(e){
  const form = e.target;
  if (!form || form.tagName !== 'FORM') return;

  if (!navigator.onLine) return;

  const btn = form.querySelector('#btnSubmit');
  if (btn) {
    btn.disabled = true;
    btn.innerText = 'Enviando...';
  }
}, true);
</script>

@endsection