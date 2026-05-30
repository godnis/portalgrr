@extends('layouts.app')

@section('content')
@php
  /**
   * ✅ Fonte única de cargos: vem do Controller (config/grr.php).
   * Se não vier por algum motivo, mantém fallback para não quebrar.
   */
  $cargos = $cargos ?? [
    1  => 'Aluno',
    2  => 'Agente de 3º Classe',
    3  => 'Agente de 2º Classe',
    4  => 'Agente de 1º Classe',
    5  => 'Agente Especial',
    6  => 'Inspetor',
    7  => 'Superintendente',
    8  => 'Coordenador',
    9  => 'Vice Diretor',
    10 => 'Diretor',
  ];

  $auth = auth()->user();
  $authNivel = (int)($auth->nivel ?? 0);

  /**
   * ✅ níveis permitidos para criar:
   * - nunca acima do seu
   * - se não for 10, não pode 9/10
   */
  $allowed = [];
  foreach ($cargos as $nivel => $cargo) {
    $nivel = (int) $nivel;
    if ($nivel <= 0) continue;

    if ($nivel > $authNivel) continue;
    if ($authNivel < 10 && $nivel >= 9) continue;

    $allowed[$nivel] = $cargo;
  }

  if (empty($allowed)) {
    for ($i=1; $i<=min($authNivel, 8); $i++) $allowed[$i] = $cargos[$i] ?? "Nível {$i}";
  }

  $nivelOld = (int) old('nivel', array_key_first($allowed) ?: 1);
  if (!isset($allowed[$nivelOld])) $nivelOld = (int) array_key_first($allowed);

  $cargoOld = $allowed[$nivelOld] ?? '—';

  $oldName   = old('name', '');
  $oldEmail  = old('email', '');
  $oldRg     = old('rg', '');
  $oldStatus = old('status', 'ativo');

  $allowedList = implode(', ', array_map(fn($n) => (string) $n, array_keys($allowed)));
  $allowedCount = count($allowed);
@endphp

<style>
  .efc-wrap{
    --efc-primary: #0d6efd;
    --efc-primary-soft: rgba(13,110,253,.10);
    --efc-success: #198754;
    --efc-success-soft: rgba(25,135,84,.12);
    --efc-warning: #f59f00;
    --efc-warning-soft: rgba(245,159,0,.14);
    --efc-danger: #dc3545;
    --efc-danger-soft: rgba(220,53,69,.12);

    --efc-text: #0f172a;
    --efc-muted: #64748b;
    --efc-border: rgba(15,23,42,.08);
    --efc-border-strong: rgba(15,23,42,.12);
    --efc-bg: #ffffff;
    --efc-bg-soft: #f8fafc;
    --efc-bg-soft-2: #f1f5f9;

    --efc-shadow-sm: 0 8px 24px rgba(15,23,42,.06);
    --efc-shadow-md: 0 18px 48px rgba(15,23,42,.10);

    --efc-radius-xl: 24px;
    --efc-radius-lg: 18px;
    --efc-radius-md: 14px;
  }

  .efc-page{
    max-width: 1180px;
    margin: 0 auto;
  }

  .efc-hero{
    position: relative;
    overflow: hidden;
    padding: 28px 28px 24px;
    border: 1px solid rgba(13,110,253,.10);
    background:
      radial-gradient(circle at top right, rgba(13,110,253,.12), transparent 30%),
      radial-gradient(circle at left bottom, rgba(245,159,0,.10), transparent 24%),
      linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    border-radius: 28px;
    box-shadow: var(--efc-shadow-md);
  }

  .efc-hero__kicker{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: #0b5ed7;
    background: rgba(13,110,253,.10);
    border: 1px solid rgba(13,110,253,.12);
  }

  .efc-hero__title{
    font-size: clamp(1.7rem, 2.2vw, 2.3rem);
    line-height: 1.05;
    font-weight: 900;
    color: var(--efc-text);
    margin: 14px 0 8px;
  }

  .efc-hero__sub{
    color: var(--efc-muted);
    font-weight: 600;
    max-width: 780px;
  }

  .efc-hero__meta{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
  }

  .efc-chip{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 38px;
    padding: 8px 14px;
    border-radius: 999px;
    border: 1px solid var(--efc-border);
    background: rgba(255,255,255,.88);
    color: var(--efc-text);
    font-size: 13px;
    font-weight: 800;
  }

  .efc-btn{
    min-height: 44px;
    border-radius: 14px !important;
    font-weight: 900 !important;
    padding-inline: 16px;
  }

  .efc-btn-primary{
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    border: none !important;
  }

  .efc-btn-soft{
    border: 1px solid var(--efc-border-strong) !important;
    background: #fff !important;
    color: var(--efc-text) !important;
  }

  .efc-panel,
  .efc-aside-card{
    border: 1px solid var(--efc-border);
    border-radius: 24px;
    background: var(--efc-bg);
    box-shadow: var(--efc-shadow-sm);
  }

  .efc-panel__body,
  .efc-aside-card__body{
    padding: 22px;
  }

  .efc-section-title{
    font-size: 13px;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--efc-muted);
    margin-bottom: 14px;
  }

  .efc-title{
    font-weight: 900;
    color: var(--efc-text);
    margin-bottom: 4px;
  }

  .efc-sub{
    color: var(--efc-muted);
    font-size: 13px;
    font-weight: 600;
  }

  .efc-form-card{
    border: 1px solid var(--efc-border);
    border-radius: 20px;
    background: linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,.96));
    padding: 18px;
  }

  .efc-label{
    display: block;
    margin-bottom: 7px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .10em;
    font-weight: 900;
    color: var(--efc-muted);
  }

  .efc-input,
  .efc-select{
    border-radius: 14px !important;
    min-height: 48px;
    border: 1px solid var(--efc-border-strong) !important;
    background: #fff !important;
    box-shadow: none !important;
    font-weight: 700;
    color: var(--efc-text) !important;
  }

  .efc-input::placeholder{
    color: #94a3b8;
  }

  .efc-input:focus,
  .efc-select:focus{
    border-color: rgba(13,110,253,.35) !important;
    box-shadow: 0 0 0 .2rem rgba(13,110,253,.10) !important;
  }

  .efc-readonly{
    background: #f8fafc !important;
  }

  .efc-note{
    color: var(--efc-muted);
    font-size: 12px;
    font-weight: 700;
    margin-top: 8px;
  }

  .efc-info-grid{
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
  }

  .efc-mini{
    border: 1px solid var(--efc-border);
    border-radius: 18px;
    padding: 16px;
    background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.98));
  }

  .efc-mini__label{
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .12em;
    font-weight: 900;
    color: var(--efc-muted);
    margin-bottom: 6px;
  }

  .efc-mini__value{
    font-size: 1.05rem;
    font-weight: 900;
    color: var(--efc-text);
    line-height: 1.2;
  }

  .efc-mini__sub{
    margin-top: 6px;
    color: var(--efc-muted);
    font-size: 12px;
    font-weight: 700;
  }

  .efc-badge{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 34px;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .06em;
  }

  .efc-badge--ok{
    background: var(--efc-success-soft);
    color: var(--efc-success);
    border: 1px solid rgba(25,135,84,.18);
  }

  .efc-badge--info{
    background: var(--efc-primary-soft);
    color: var(--efc-primary);
    border: 1px solid rgba(13,110,253,.18);
  }

  .efc-badge--warn{
    background: var(--efc-warning-soft);
    color: #9a6700;
    border: 1px solid rgba(245,159,0,.20);
  }

  .efc-list{
    margin: 0;
    padding-left: 18px;
  }

  .efc-list li{
    color: var(--efc-muted);
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 8px;
  }

  .efc-alert{
    border: 1px solid rgba(245,159,0,.18);
    background: linear-gradient(180deg, rgba(255,248,230,.95), rgba(255,252,242,.98));
    color: #7a5600;
    border-radius: 18px;
  }

  .efc-errors{
    border: 1px solid rgba(220,53,69,.16);
    background: linear-gradient(180deg, rgba(255,245,246,.98), rgba(255,250,250,.98));
    border-radius: 20px;
    color: #842029;
  }

  .efc-pass-wrap .input-group-text,
  .efc-pass-wrap .btn{
    min-height: 48px;
  }

  .efc-pass-wrap .btn{
    font-weight: 900 !important;
  }

  .efc-footer-actions{
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .efc-divider{
    border-top: 1px solid var(--efc-border);
    margin: 22px 0;
  }

  .efc-sticky{
    position: sticky;
    top: 16px;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-wrap{
    --efc-text: rgba(231,237,246,.95);
    --efc-muted: rgba(231,237,246,.66);
    --efc-border: rgba(255,255,255,.09);
    --efc-border-strong: rgba(255,255,255,.12);
    --efc-bg: rgba(10,14,20,.92);
    --efc-bg-soft: rgba(15,20,28,.88);
    --efc-bg-soft-2: rgba(18,24,33,.92);
    --efc-shadow-sm: 0 14px 40px rgba(0,0,0,.32);
    --efc-shadow-md: 0 24px 60px rgba(0,0,0,.42);
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-hero{
    border-color: rgba(255,255,255,.08);
    background:
      radial-gradient(circle at top right, rgba(13,110,253,.16), transparent 32%),
      radial-gradient(circle at left bottom, rgba(245,159,0,.10), transparent 28%),
      linear-gradient(135deg, rgba(10,14,20,.96), rgba(15,20,28,.94));
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-chip,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-panel,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-aside-card,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-form-card,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-mini{
    background: rgba(15,20,28,.86) !important;
    border-color: var(--efc-border) !important;
    color: var(--efc-text) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-input,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-select{
    background: rgba(15,20,28,.82) !important;
    color: var(--efc-text) !important;
    border-color: var(--efc-border) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-input::placeholder{
    color: rgba(231,237,246,.34);
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-readonly{
    background: rgba(20,27,39,.95) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efc-btn-soft{
    background: rgba(15,20,28,.82) !important;
    color: var(--efc-text) !important;
    border-color: var(--efc-border) !important;
  }

  @media (max-width: 991.98px){
    .efc-sticky{
      position: static;
    }
  }

  @media (max-width: 767.98px){
    .efc-hero{
      padding: 20px;
    }

    .efc-panel__body,
    .efc-aside-card__body{
      padding: 18px;
    }

    .efc-form-card{
      padding: 16px;
    }
  }
</style>

<div class="container-fluid py-3 efc-wrap">
  <div class="efc-page">

    {{-- HERO --}}
    <div class="efc-hero mb-4">
      <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
        <div>
          <div class="efc-hero__kicker">GRR • PRF • Cadastro de efetivo</div>
          <h1 class="efc-hero__title">Adicionar novo oficial</h1>
          <div class="efc-hero__sub">
            Cadastro rápido, seguro e alinhado à hierarquia da corporação. Defina os dados iniciais do oficial, o nível permitido e a senha provisória de acesso.
          </div>

          <div class="efc-hero__meta">
            <span class="efc-chip">Seu nível: <strong>{{ $authNivel }}</strong></span>
            <span class="efc-chip">Níveis liberados: <strong>{{ $allowedCount }}</strong></span>
            <span class="efc-chip">Faixa disponível: <strong>{{ $allowedList }}</strong></span>
          </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('efetivo.index') }}" class="btn efc-btn efc-btn-soft">
            Voltar
          </a>
        </div>
      </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
      <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    @if($errors->any())
      <div class="alert efc-errors mb-4">
        <div class="fw-bold mb-2">Corrija os campos abaixo:</div>
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="row g-4">
      {{-- FORM PRINCIPAL --}}
      <div class="col-lg-8">
        <div class="efc-panel">
          <div class="efc-panel__body">

            <div class="efc-section-title">Dados do novo oficial</div>
            <div class="mb-3">
              <div class="efc-title">Cadastro inicial</div>
              <div class="efc-sub">
                Preencha as informações essenciais do usuário. O cargo será definido automaticamente a partir do nível escolhido.
              </div>
            </div>

            <form method="POST" action="{{ route('efetivo.store') }}" id="formEfetivoCreate" novalidate>
              @csrf

              <div class="efc-form-card mb-3">
                <div class="row g-3">
                  {{-- Nome --}}
                  <div class="col-md-6">
                    <label class="efc-label">Nome</label>
                    <input
                      name="name"
                      class="form-control efc-input @error('name') is-invalid @enderror"
                      value="{{ $oldName }}"
                      minlength="3"
                      maxlength="120"
                      autocomplete="name"
                      required
                      autofocus
                      placeholder="Ex.: João Silva"
                    >
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>

                  {{-- Email --}}
                  <div class="col-md-6">
                    <label class="efc-label">Email</label>
                    <input
                      name="email"
                      type="email"
                      class="form-control efc-input @error('email') is-invalid @enderror"
                      value="{{ $oldEmail }}"
                      autocomplete="email"
                      required
                      placeholder="Ex.: joao@email.com"
                    >
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="efc-note">Usado para login no sistema.</div>
                  </div>

                  {{-- RG --}}
                  <div class="col-md-4">
                    <label class="efc-label">RG</label>
                    <input
                      name="rg"
                      class="form-control efc-input @error('rg') is-invalid @enderror"
                      value="{{ $oldRg }}"
                      maxlength="30"
                      required
                      placeholder="Ex.: 12345"
                    >
                    @error('rg') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="efc-note">Identificador oficial dentro da corporação.</div>
                  </div>

                  {{-- Nível --}}
                  <div class="col-md-4">
                    <label class="efc-label">Nível</label>
                    <select
                      name="nivel"
                      id="nivelSelect"
                      class="form-select efc-select @error('nivel') is-invalid @enderror"
                      required
                    >
                      @foreach($allowed as $nivel => $cargo)
                        <option value="{{ $nivel }}" @selected((int)old('nivel', $nivelOld) === (int)$nivel)>
                          {{ $nivel }} — {{ $cargo }}
                        </option>
                      @endforeach
                    </select>
                    @error('nivel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="efc-note">Mostrando apenas níveis permitidos para você.</div>
                  </div>

                  {{-- Cargo auto --}}
                  <div class="col-md-4">
                    <label class="efc-label">Cargo automático</label>
                    <input
                      id="cargoAuto"
                      class="form-control efc-input efc-readonly"
                      value="{{ $cargoOld }}"
                      readonly
                    >
                    <div class="efc-note">Definido automaticamente pelo nível selecionado.</div>
                  </div>

                  {{-- Status --}}
                  <div class="col-md-6">
                    <label class="efc-label">Status inicial</label>
                    <select
                      name="status"
                      class="form-select efc-select @error('status') is-invalid @enderror"
                      required
                    >
                      @foreach(['ativo' => 'Ativo', 'suspenso' => 'Suspenso', 'desligado' => 'Desligado'] as $k => $v)
                        <option value="{{ $k }}" @selected($oldStatus === $k)>{{ $v }}</option>
                      @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="efc-note">Recomendado: criar como <b>Ativo</b> para oficiais já aprovados.</div>
                  </div>

                  {{-- Senha --}}
                  <div class="col-md-6">
                    <label class="efc-label d-flex align-items-center justify-content-between gap-2">
                      <span>Senha inicial</span>
                      <button type="button" id="btnGerarSenha" class="btn btn-sm efc-btn efc-btn-soft py-1 px-3" style="min-height:auto;">
                        Gerar forte
                      </button>
                    </label>

                    <div class="input-group efc-pass-wrap">
                      <input
                        name="password"
                        id="passwordInput"
                        type="password"
                        class="form-control efc-input @error('password') is-invalid @enderror"
                        minlength="6"
                        maxlength="120"
                        required
                        placeholder="Mín. 6 caracteres"
                        autocomplete="new-password"
                      >
                      <button type="button" id="btnToggleSenha" class="btn efc-btn efc-btn-soft px-3">
                        👁
                      </button>
                    </div>
                    @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    <div class="efc-note">
                      Recomendado que o oficial troque a senha no primeiro acesso.
                    </div>
                  </div>
                </div>
              </div>

              <div class="alert efc-alert mb-0">
                <b>Atenção:</b> o sistema respeita automaticamente sua hierarquia de criação. Você não poderá cadastrar oficiais acima do seu nível ou em faixas bloqueadas pela regra administrativa.
              </div>

              <div class="efc-divider"></div>

              <div class="efc-footer-actions">
                <button id="btnSalvar" class="btn btn-primary efc-btn efc-btn-primary">
                  Salvar oficial
                </button>

                <a href="{{ route('efetivo.index') }}" class="btn efc-btn efc-btn-soft">
                  Cancelar
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- LATERAL --}}
      <div class="col-lg-4">
        <div class="efc-sticky d-grid gap-4">

          <div class="efc-aside-card">
            <div class="efc-aside-card__body">
              <div class="efc-section-title">Resumo de permissão</div>

              <div class="efc-info-grid">
                <div class="efc-mini">
                  <div class="efc-mini__label">Seu nível</div>
                  <div class="efc-mini__value">Nível {{ $authNivel }}</div>
                  <div class="efc-mini__sub">Base atual da sua autoridade de cadastro.</div>
                </div>

                <div class="efc-mini">
                  <div class="efc-mini__label">Níveis disponíveis</div>
                  <div class="efc-mini__value">{{ $allowedList }}</div>
                  <div class="efc-mini__sub">Somente esses níveis podem ser atribuídos por você.</div>
                </div>

                <div class="efc-mini">
                  <div class="efc-mini__label">Criação recomendada</div>
                  <div class="d-flex flex-wrap gap-2 mt-1">
                    <span class="efc-badge efc-badge--ok">Ativo</span>
                    <span class="efc-badge efc-badge--info">Cargo automático</span>
                  </div>
                  <div class="efc-mini__sub">O sistema sincroniza cargo e nível automaticamente.</div>
                </div>
              </div>
            </div>
          </div>

          <div class="efc-aside-card">
            <div class="efc-aside-card__body">
              <div class="efc-section-title">Boas práticas</div>
              <ul class="efc-list">
                <li>Cadastre o nome de forma padronizada para evitar duplicidades e inconsistências.</li>
                <li>Confira o RG antes de salvar, pois ele funciona como identificador do oficial.</li>
                <li>Prefira gerar uma senha forte para o primeiro acesso do usuário.</li>
                <li>Use status <b>Suspenso</b> ou <b>Desligado</b> apenas quando necessário administrativamente.</li>
              </ul>
            </div>
          </div>

          <div class="efc-aside-card">
            <div class="efc-aside-card__body">
              <div class="efc-section-title">Segurança do acesso</div>
              <div class="efc-sub mb-3">
                Gere uma senha forte, repasse ao oficial por canal seguro e oriente a troca imediata após o primeiro login.
              </div>

              <div class="efc-mini">
                <div class="efc-mini__label">Recomendação</div>
                <div class="efc-mini__value">Senha com letras, números e símbolos</div>
                <div class="efc-mini__sub">O botão “Gerar forte” já cria um padrão seguro automaticamente.</div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const cargos = @json($allowed);
    const sel = document.getElementById('nivelSelect');
    const cargoEl = document.getElementById('cargoAuto');

    const syncCargo = () => {
      if (!sel || !cargoEl) return;
      const nivel = parseInt(sel.value || '0', 10);
      cargoEl.value = cargos[nivel] || '—';
    };

    if (sel && cargoEl) {
      sel.addEventListener('change', syncCargo);
      syncCargo();
    }

    const pass = document.getElementById('passwordInput');
    const btnToggle = document.getElementById('btnToggleSenha');

    if (pass && btnToggle) {
      btnToggle.addEventListener('click', () => {
        pass.type = (pass.type === 'password') ? 'text' : 'password';
        btnToggle.innerText = pass.type === 'password' ? '👁' : '🙈';
      });
    }

    const btnGerar = document.getElementById('btnGerarSenha');

    const genStrongPassword = (len = 14) => {
      const a = "ABCDEFGHJKLMNPQRSTUVWXYZ";
      const b = "abcdefghijkmnopqrstuvwxyz";
      const c = "23456789";
      const d = "!@#$%&*_-+=";

      const pick = (s) => s[Math.floor(Math.random() * s.length)];
      let out = pick(a) + pick(b) + pick(c) + pick(d);

      const all = a + b + c + d;
      while (out.length < len) out += pick(all);

      out = out.split('').sort(() => Math.random() - 0.5).join('');
      return out;
    };

    if (btnGerar && pass) {
      btnGerar.addEventListener('click', () => {
        const p = genStrongPassword(14);
        pass.value = p;
        pass.type = 'text';
        if (btnToggle) btnToggle.innerText = '🙈';

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(p).catch(() => {});
        }

        pass.focus();
        pass.select?.();
      });
    }

    const form = document.getElementById('formEfetivoCreate');
    const btnSalvar = document.getElementById('btnSalvar');

    if (form && btnSalvar) {
      form.addEventListener('submit', () => {
        btnSalvar.disabled = true;
        btnSalvar.innerText = 'Salvando...';
      });
    }
  });
</script>
@endsection