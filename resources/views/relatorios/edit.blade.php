@extends('layouts.app')

@section('content')
<div class="rep-edit">

  @php
    $backUrl = request('back', route('relatorios.index'));

    $statusAtual = (string) ($relatorio->status ?? 'pendente');

    $statusLabel = match($statusAtual) {
      'aprovado'    => 'Aprovado',
      'reprovado'   => 'Reprovado',
      'em_patrulha' => 'Em patrulha',
      default       => 'Pendente',
    };

    $statusClass = match($statusAtual) {
      'aprovado'    => 'rep-badge-status rep-badge--ok',
      'reprovado'   => 'rep-badge-status rep-badge--bad',
      'em_patrulha' => 'rep-badge-status rep-badge--info',
      default       => 'rep-badge-status rep-badge--warn',
    };

    $dataFmt = !empty($relatorio->data_patrulhamento)
      ? \Carbon\Carbon::parse($relatorio->data_patrulhamento)->format('d/m/Y')
      : '—';

    $inicioFmt = !empty($relatorio->inicio_patrulhamento)
      ? \Carbon\Carbon::parse($relatorio->inicio_patrulhamento)->format('H:i')
      : '—';

    $finalFmt = !empty($relatorio->final_patrulhamento)
      ? \Carbon\Carbon::parse($relatorio->final_patrulhamento)->format('H:i')
      : '—';

    $bopmRegistros = data_get($relatorio, 'bopm_registros', []);
    if (is_string($bopmRegistros)) {
      $decoded = json_decode($bopmRegistros, true);
      $bopmRegistros = is_array($decoded) ? $decoded : [];
    }
    if (!is_array($bopmRegistros)) {
      $bopmRegistros = [];
    }
    $bopmRegistros = array_values(array_filter(array_map(fn($v) => trim((string)$v), $bopmRegistros), fn($v) => $v !== ''));
  @endphp

  <style>
    .rep-edit{
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
      --rep-info: #0284c7;
      --rep-shadow: 0 20px 60px rgba(15, 23, 42, .10);
      --rep-shadow-soft: 0 10px 30px rgba(15, 23, 42, .08);
      --rep-shadow-inset: inset 0 1px 0 rgba(255,255,255,.55);
    }

    html[data-theme="dark"] .rep-edit{
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
      --rep-info: #38bdf8;
      --rep-shadow: 0 18px 55px rgba(0,0,0,.55);
      --rep-shadow-soft: 0 14px 40px rgba(0,0,0,.45);
      --rep-shadow-inset: none;
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
        radial-gradient(900px 420px at 0% 0%, rgba(245,158,11,.12), transparent 55%),
        radial-gradient(720px 340px at 100% 10%, rgba(37,99,235,.08), transparent 55%),
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
        radial-gradient(1000px 520px at 18% 18%, rgba(245,158,11,.14), transparent 60%),
        radial-gradient(900px 520px at 85% 18%, rgba(37,99,235,.10), transparent 55%),
        radial-gradient(800px 520px at 50% 120%, rgba(2,6,23,.10), transparent 60%);
    }

    html[data-theme="dark"] .rep-hero__bg{
      background:
        radial-gradient(1000px 520px at 20% 20%, rgba(245,158,11,.20), transparent 60%),
        radial-gradient(900px 520px at 85% 25%, rgba(90,162,255,.14), transparent 55%),
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
      border: 1px solid rgba(245,158,11,.18);
      background: rgba(245,158,11,.10);
      color: var(--rep-text);
      font-size: 12px;
      font-weight: 900;
      box-shadow: var(--rep-shadow-inset);
    }

    html[data-theme="dark"] .rep-badge{
      border-color: rgba(245,158,11,.28);
      background: rgba(245,158,11,.12);
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
      background: var(--rep-warning);
      box-shadow: 0 0 0 4px rgba(245,158,11,.14);
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

    .rep-card{
      border: 1px solid var(--rep-border);
      background: var(--rep-card);
      border-radius: var(--rep-radius-2xl);
      box-shadow: var(--rep-shadow);
      overflow: hidden;
    }

    .rep-card__head{
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

    .rep-card__title{
      font-size: 18px;
      font-weight: 950;
      letter-spacing: -.03em;
      color: var(--rep-text);
    }

    .rep-card__sub{
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
      border: 1px solid rgba(245,158,11,.22);
      background: rgba(245,158,11,.10);
      color: var(--rep-text);
    }

    html[data-theme="dark"] .rep-pill{
      border-color: rgba(245,158,11,.30);
      background: rgba(245,158,11,.12);
    }

    .rep-pill__dot{
      width:8px;
      height:8px;
      border-radius:50%;
      background: var(--rep-warning);
      box-shadow: 0 0 0 4px rgba(245,158,11,.14);
    }

    .rep-card__body{
      padding: 18px;
    }

    .rep-decision-bar{
      border: 1px solid rgba(245,158,11,.24);
      background: rgba(245,158,11,.08);
      border-radius: 18px;
      padding: 16px;
      margin-bottom: 18px;
    }

    html[data-theme="dark"] .rep-decision-bar{
      border-color: rgba(245,158,11,.28);
      background: rgba(245,158,11,.10);
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
    .rep-field .form-select,
    .rep-field textarea{
      border-radius: 14px !important;
      min-height: 48px;
      border: 1px solid var(--rep-border) !important;
      background: rgba(255,255,255,.94) !important;
      color: var(--rep-text) !important;
      font-weight: 700;
      box-shadow: none !important;
    }

    html[data-theme="dark"] .rep-field .form-control,
    html[data-theme="dark"] .rep-field .form-select,
    html[data-theme="dark"] .rep-field textarea{
      background: rgba(15,23,42,.82) !important;
      border-color: rgba(255,255,255,.12) !important;
    }

    .rep-field .form-control:focus,
    .rep-field .form-select:focus,
    .rep-field textarea:focus{
      border-color: rgba(245,158,11,.45) !important;
      box-shadow: 0 0 0 4px rgba(245,158,11,.10) !important;
    }

    html[data-theme="dark"] .rep-field .form-control:focus,
    html[data-theme="dark"] .rep-field .form-select:focus,
    html[data-theme="dark"] .rep-field textarea:focus{
      border-color: rgba(245,158,11,.55) !important;
      box-shadow: 0 0 0 4px rgba(245,158,11,.16) !important;
    }

    .rep-field .form-control[readonly],
    .rep-field textarea[readonly]{
      background: rgba(248,250,252,.96) !important;
      color: var(--rep-text) !important;
      opacity: 1 !important;
    }

    html[data-theme="dark"] .rep-field .form-control[readonly],
    html[data-theme="dark"] .rep-field textarea[readonly]{
      background: rgba(255,255,255,.06) !important;
    }

    .rep-status-line{
      display:flex;
      align-items:center;
      gap:10px;
      flex-wrap:wrap;
      margin-top: 8px;
    }

    .rep-badge-status{
      display:inline-flex;
      align-items:center;
      gap:8px;
      width: fit-content;
      padding:7px 11px;
      border-radius:999px;
      font-size:12px;
      line-height:1;
      font-weight:950;
      letter-spacing:.02em;
      white-space:nowrap;
      border: 1px solid transparent;
    }

    .rep-badge-status .rep-dot{
      width:8px;
      height:8px;
      border-radius:99px;
      flex: 0 0 auto;
    }

    .rep-badge--info{
      background: rgba(14,165,233,.10) !important;
      color: #0369a1 !important;
      border-color: rgba(14,165,233,.22);
    }
    .rep-badge--info .rep-dot{
      background: var(--rep-info);
      box-shadow: 0 0 0 3px rgba(14,165,233,.16);
    }

    .rep-badge--warn{
      background: rgba(245,158,11,.12) !important;
      color: #b45309 !important;
      border-color: rgba(245,158,11,.22);
    }
    .rep-badge--warn .rep-dot{
      background: var(--rep-warning);
      box-shadow: 0 0 0 3px rgba(245,158,11,.16);
    }

    .rep-badge--ok{
      background: rgba(16,185,129,.12) !important;
      color: #047857 !important;
      border-color: rgba(16,185,129,.22);
    }
    .rep-badge--ok .rep-dot{
      background: var(--rep-success);
      box-shadow: 0 0 0 3px rgba(16,185,129,.16);
    }

    .rep-badge--bad{
      background: rgba(239,68,68,.10) !important;
      color: #b91c1c !important;
      border-color: rgba(239,68,68,.18);
    }
    .rep-badge--bad .rep-dot{
      background: var(--rep-danger);
      box-shadow: 0 0 0 3px rgba(239,68,68,.16);
    }

    html[data-theme="dark"] .rep-badge--info{
      background: rgba(14,165,233,.12) !important;
      color: rgba(200,240,255,.92) !important;
      border-color: rgba(14,165,233,.28);
    }
    html[data-theme="dark"] .rep-badge--warn{
      background: rgba(245,158,11,.14) !important;
      color: rgba(255,220,170,.92) !important;
      border-color: rgba(245,158,11,.30);
    }
    html[data-theme="dark"] .rep-badge--ok{
      background: rgba(16,185,129,.12) !important;
      color: rgba(190,255,230,.92) !important;
      border-color: rgba(16,185,129,.26);
    }
    html[data-theme="dark"] .rep-badge--bad{
      background: rgba(239,68,68,.12) !important;
      color: rgba(255,200,200,.92) !important;
      border-color: rgba(239,68,68,.26);
    }

    .rep-note{
      font-size: 12px;
      color: var(--rep-muted);
      font-weight: 800;
      margin-top: 6px;
      line-height: 1.45;
    }

    .rep-soft-box{
      padding: 14px;
      border-radius: 16px;
      border: 1px solid var(--rep-border);
      background: rgba(255,255,255,.70);
      box-shadow: var(--rep-shadow-soft);
    }

    html[data-theme="dark"] .rep-soft-box{
      background: rgba(15,20,28,.62);
    }

    .rep-field-k{
      font-size: 12px;
      font-weight: 900;
      color: var(--rep-muted);
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: 5px;
    }

    .rep-field-v{
      font-size: 14px;
      font-weight: 800;
      color: var(--rep-text);
      line-height: 1.55;
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

    .rep-btn-warning{
      background: linear-gradient(135deg, #f59e0b, #d97706) !important;
      border-color: transparent !important;
      color: #1f1300 !important;
    }

    .rep-footer-note{
      margin-top: 14px;
      color: var(--rep-muted);
      font-size: 12px;
      font-weight: 800;
    }

    .rep-loading-overlay{
      position: fixed;
      inset: 0;
      z-index: 20000;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(15, 23, 42, .72);
      backdrop-filter: blur(10px);
    }

    .rep-loading-overlay.d-none{
      display: none !important;
    }

    .rep-loading-card{
      width: min(100%, 420px);
      border-radius: 24px;
      padding: 28px 24px;
      text-align: center;
      border: 1px solid rgba(255,255,255,.14);
      background: #fff;
      box-shadow: 0 28px 80px rgba(0,0,0,.28);
    }

    html[data-theme="dark"] .rep-loading-card{
      background: #0b1220;
      color: #fff;
    }

    .rep-loading-spinner{
      width: 58px;
      height: 58px;
      margin: 0 auto 16px;
      border-radius: 50%;
      border: 4px solid rgba(245,158,11,.18);
      border-top-color: #f59e0b;
      animation: spin .8s linear infinite;
    }

    @keyframes spin{
      to{ transform: rotate(360deg); }
    }

    @media (max-width: 768px){
      .rep-shell{ padding-inline: 8px; }
      .rep-title{ font-size: 25px; }
      .rep-actions .rep-btn{ width: 100%; justify-content: center; }
    }
  </style>

  <div class="rep-shell">

    {{-- HERO --}}
    <div class="rep-hero">
      <div class="rep-hero__bg"></div>

      <div class="rep-hero__content">
        <div>
          <div class="rep-kicker">GRR • PRF — Revisão Administrativa</div>
          <h1 class="rep-title">Decisão do Relatório de Patrulhamento</h1>
          <div class="rep-sub">
            Área restrita para análise administrativa do relatório, com registro de decisão, justificativa obrigatória e impacto direto nos indicadores internos.
          </div>

          <div class="rep-badges">
            <span class="rep-badge">
              <span class="rep-badge__dot"></span>
              revisão restrita
            </span>

            <span class="rep-badge rep-badge--soft">
              nível 6+ obrigatório
            </span>
          </div>
        </div>

        <div>
          <div class="rep-mini">
            <div class="rep-mini__item">
              <div class="rep-mini__k">Status atual</div>
              <div class="rep-mini__v">{{ $statusLabel }}</div>
            </div>

            <div class="rep-mini__item">
              <div class="rep-mini__k">Horário da patrulha</div>
              <div class="rep-mini__v">{{ $dataFmt }} • {{ $inicioFmt }} até {{ $finalFmt }}</div>
            </div>

            <div class="rep-mini__item">
              <div class="rep-mini__k">Impacto da decisão</div>
              <div class="rep-mini__v">A decisão atualiza ranking, XP, dashboard e trilha de auditoria do sistema.</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ERROS --}}
    @if ($errors->any())
      <div class="alert alert-danger rep-alert">
        <b>Erro:</b>
        <ul class="mb-0 mt-2">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST"
          action="{{ route('relatorios.update', $relatorio) }}"
          class="rep-card"
          id="formDecisaoRelatorio">
      @csrf
      @method('PUT')

      <input type="hidden" name="back" value="{{ $backUrl }}">

      <div class="rep-card__head">
        <div>
          <div class="rep-card__title">Decisão administrativa</div>
          <div class="rep-card__sub">Selecione o resultado da análise e registre a justificativa formal da decisão.</div>
        </div>

        <div class="rep-pill">
          <span class="rep-pill__dot"></span>
          auditoria ativa
        </div>
      </div>

      <div class="rep-card__body">

        {{-- BARRA DE DECISÃO --}}
        <div class="rep-decision-bar">
          <div class="row g-3 align-items-end">
            <div class="col-lg-4 rep-field">
              <label class="form-label">Decisão do relatório</label>
              <select name="status" class="form-select" required>
                <option value="aprovado" @selected($relatorio->status === 'aprovado')>
                  ✅ Aprovar relatório
                </option>
                <option value="reprovado" @selected($relatorio->status === 'reprovado')>
                  ❌ Reprovar relatório
                </option>
              </select>
            </div>

            <div class="col-lg-8">
              <div class="rep-status-line">
                <span class="{{ $statusClass }}">
                  <span class="rep-dot"></span>
                  {{ $statusLabel }}
                </span>

                <span class="rep-note">Relatório #{{ $relatorio->id }} • {{ $dataFmt }} • Unidade {{ $relatorio->unidade ?? '—' }}</span>
              </div>

              <div class="rep-note mt-2">
                ⚠️ Esta ação altera ranking, XP e dashboard, além de registrar a decisão na auditoria interna.
              </div>
            </div>
          </div>
        </div>

        {{-- DADOS DO RELATÓRIO --}}
        <section class="rep-section">
          <div class="rep-section__head">
            <div>
              <div class="rep-section__title">Dados do relatório</div>
              <div class="rep-section__sub">Visualização em modo somente leitura para conferência antes da decisão.</div>
            </div>
            <div class="rep-section__sub">campos bloqueados</div>
          </div>

          <div class="rep-section__body">
            <div class="row g-3">
              @foreach([
                'qra_chefe' => 'Chefe (RG)',
                'unidade' => 'Unidade',
                'motorista' => 'Motorista',
                'terceiro' => 'Auxiliar P3',
                'quarto' => 'Auxiliar P4',
                'quinto' => 'Auxiliar P5',
              ] as $field => $label)
                <div class="col-md-3 rep-field">
                  <label class="form-label">{{ $label }}</label>
                  <input
                    class="form-control"
                    value="{{ $relatorio->$field }}"
                    readonly
                  >
                </div>
              @endforeach

              <div class="col-md-3 rep-field">
                <label class="form-label">Data</label>
                <input class="form-control" value="{{ $dataFmt }}" readonly>
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Início</label>
                <input class="form-control" value="{{ $inicioFmt }}" readonly>
              </div>

              <div class="col-md-3 rep-field">
                <label class="form-label">Final</label>
                <input class="form-control" value="{{ $finalFmt }}" readonly>
              </div>
            </div>
          </div>
        </section>

        {{-- RESULTADOS OPERACIONAIS --}}
        <section class="rep-section">
          <div class="rep-section__head">
            <div>
              <div class="rep-section__title">Resultados operacionais</div>
              <div class="rep-section__sub">Conferência completa dos indicadores lançados no relatório.</div>
            </div>
            <div class="rep-section__sub">somente leitura</div>
          </div>

          <div class="rep-section__body">
            <h6 class="fw-bold mb-3" style="color: var(--rep-text);">Apreensões</h6>
            <div class="row g-3">
              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Pistolas</div>
                  <div class="rep-field-v">{{ (int)($relatorio->pistolas ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">SMG / Fuzil</div>
                  <div class="rep-field-v">{{ (int)($relatorio->smg_fuzil ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Munições</div>
                  <div class="rep-field-v">{{ (int)($relatorio->municoes ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Drogas</div>
                  <div class="rep-field-v">{{ (int)($relatorio->drogas ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Explosivos</div>
                  <div class="rep-field-v">{{ (int)($relatorio->explosivos ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Lockpicks</div>
                  <div class="rep-field-v">{{ (int)($relatorio->lockpicks ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Dinheiro</div>
                  <div class="rep-field-v">R$ {{ number_format((int)($relatorio->dinheiro ?? 0), 0, ',', '.') }}</div>
                </div>
              </div>
            </div>

            <hr class="my-4">

            <h6 class="fw-bold mb-3" style="color: var(--rep-text);">Multas / Ações</h6>
            <div class="row g-3">
              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Abordagens</div>
                  <div class="rep-field-v">{{ (int)($relatorio->abordagens ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Apoio</div>
                  <div class="rep-field-v">{{ (int)($relatorio->apoio ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Incursão</div>
                  <div class="rep-field-v">{{ (int)($relatorio->incursao ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Negociação</div>
                  <div class="rep-field-v">{{ (int)($relatorio->negociacao ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Blitz</div>
                  <div class="rep-field-v">{{ (int)($relatorio->blitz ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Escolta</div>
                  <div class="rep-field-v">{{ (int)($relatorio->escolta ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Multas</div>
                  <div class="rep-field-v">{{ (int)($relatorio->multas ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">BOPM</div>
                  <div class="rep-field-v">{{ (int)($relatorio->bopm ?? 0) }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="rep-soft-box">
                  <div class="rep-field-k">Viaturas fiscalizadas</div>
                  <div class="rep-field-v">{{ (int)($relatorio->viaturas_fiscalizadas ?? 0) }}</div>
                </div>
              </div>
            </div>

            @if(count($bopmRegistros))
              <hr class="my-4">

              <h6 class="fw-bold mb-3" style="color: var(--rep-text);">Registros do BOPM</h6>
              <div class="row g-3">
                @foreach($bopmRegistros as $idx => $registro)
                  <div class="col-md-6">
                    <div class="rep-soft-box">
                      <div class="rep-field-k">Registro BOPM {{ $idx + 1 }}</div>
                      <div class="rep-field-v">{{ $registro }}</div>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </section>

        {{-- OBSERVAÇÕES DO RELATÓRIO --}}
        <section class="rep-section">
          <div class="rep-section__head">
            <div>
              <div class="rep-section__title">Observações do relatório</div>
              <div class="rep-section__sub">Conteúdo preenchido pela guarnição, disponível apenas para consulta.</div>
            </div>
          </div>

          <div class="rep-section__body">
            <div class="rep-field">
              <label class="form-label">Observações registradas</label>
              <textarea
                class="form-control"
                rows="4"
                readonly
                style="min-height: 120px;"
              >{{ $relatorio->observacoes }}</textarea>
            </div>
          </div>
        </section>

        {{-- JUSTIFICATIVA --}}
        <section class="rep-section">
          <div class="rep-section__head">
            <div>
              <div class="rep-section__title">Justificativa da decisão</div>
              <div class="rep-section__sub">Campo obrigatório para registro administrativo e rastreabilidade da decisão.</div>
            </div>
            <div class="rep-section__sub">obrigatório</div>
          </div>

          <div class="rep-section__body">
            <div class="rep-field">
              <label class="form-label">
                Justificativa <span class="text-danger">*</span>
              </label>

              <textarea
                name="observacao"
                rows="4"
                class="form-control @error('observacao') is-invalid @enderror"
                placeholder="Ex.: aprovado, relatório consistente e dentro do protocolo..."
                required
                minlength="10"
                maxlength="400"
                style="min-height: 120px;"
              >{{ old('observacao') }}</textarea>

              @error('observacao')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror

              <div class="rep-note">
                Obrigatório • mínimo 10 caracteres • máximo 400 • auditoria ativa.
              </div>
            </div>
          </div>
        </section>

        {{-- AÇÕES --}}
        <div class="rep-actions">
          <button class="btn btn-warning rep-btn rep-btn-warning" id="btnSalvarDecisao" type="submit">
            ✔ Registrar decisão
          </button>

          <a href="{{ $backUrl }}"
             class="btn btn-outline-secondary rep-btn">
            Cancelar
          </a>
        </div>

        <div class="rep-footer-note">
          Ao confirmar, a decisão ficará registrada no relatório e refletirá imediatamente nos indicadores administrativos do sistema.
        </div>

      </div>
    </form>
  </div>

  <div id="loadingDecisao" class="rep-loading-overlay d-none">
    <div class="rep-loading-card">
      <div class="rep-loading-spinner"></div>
      <div style="font-weight:900; font-size:18px;">Registrando decisão...</div>
      <div style="font-size:13px; margin-top:6px;">
        Aguarde, salvando informações no sistema.
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('formDecisaoRelatorio');
  const btn = document.getElementById('btnSalvarDecisao');
  const overlay = document.getElementById('loadingDecisao');

  if (!form || !btn) return;

  form.addEventListener('submit', function(e) {
    if (btn.dataset.loading === '1') {
      e.preventDefault();
      return;
    }

    btn.dataset.loading = '1';
    btn.disabled = true;
    btn.innerHTML = `
      <span style="width:16px;height:16px;border:2px solid #0003;border-top-color:#000;border-radius:50%;display:inline-block;animation:spin .8s linear infinite;margin-right:6px;"></span>
      Salvando...
    `;

    if (overlay) {
      overlay.classList.remove('d-none');
    }
  });
});
</script>
@endsection