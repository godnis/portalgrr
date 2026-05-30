@extends('layouts.app')

@section('content')
@php
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

  $canManage = $auth && $authNivel >= 9;
  $editingHigh = ((int)$user->nivel >= 9);
  $isSelf = ($auth && (int)$auth->id === (int)$user->id);

  $allowed = [];
  foreach ($cargos as $nivel => $cargo) {
    $nivel = (int)$nivel;

    if ($nivel < 1 || $nivel > 10) continue;
    if ($nivel > $authNivel) continue;
    if ($authNivel < 10 && $nivel >= 9) continue;

    $allowed[$nivel] = $cargo;
  }

  if (!$editingHigh || $authNivel >= 10) {
    if (!isset($allowed[(int)$user->nivel]) && (int)$user->nivel <= $authNivel && !($authNivel < 10 && (int)$user->nivel >= 9)) {
      $allowed[(int)$user->nivel] = $cargos[(int)$user->nivel] ?? '—';
      ksort($allowed);
    }
  }

  $nivelNow = (int) old('nivel', (int)$user->nivel);
  $cargoNow = $cargos[$nivelNow] ?? ($user->cargo ?? '—');

  $badge = match((string)($user->status ?? '')){
    'ativo' => 'text-bg-success',
    'suspenso' => 'text-bg-warning',
    'desligado' => 'text-bg-secondary',
    default => 'text-bg-light'
  };
@endphp

<style>
  .efe-wrap{
    --efe-primary: #0d6efd;
    --efe-primary-soft: rgba(13,110,253,.10);
    --efe-success: #198754;
    --efe-warning: #f59f00;
    --efe-danger: #dc3545;

    --efe-text: #0f172a;
    --efe-muted: #64748b;
    --efe-border: rgba(15,23,42,.08);
    --efe-border-strong: rgba(15,23,42,.12);
    --efe-bg: #ffffff;
    --efe-bg-soft: #f8fafc;
    --efe-bg-soft-2: #f1f5f9;

    --efe-shadow-sm: 0 8px 24px rgba(15,23,42,.06);
    --efe-shadow-md: 0 18px 48px rgba(15,23,42,.10);
  }

  .efe-page{
    max-width: 1180px;
    margin: 0 auto;
  }

  .efe-hero{
    position: relative;
    overflow: hidden;
    padding: 28px 28px 24px;
    border: 1px solid rgba(13,110,253,.10);
    background:
      radial-gradient(circle at top right, rgba(13,110,253,.12), transparent 30%),
      radial-gradient(circle at left bottom, rgba(245,159,0,.10), transparent 24%),
      linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    border-radius: 28px;
    box-shadow: var(--efe-shadow-md);
  }

  .efe-kicker{
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

  .efe-title{
    font-size: clamp(1.7rem, 2.2vw, 2.3rem);
    line-height: 1.05;
    font-weight: 900;
    color: var(--efe-text);
    margin: 14px 0 8px;
  }

  .efe-sub{
    color: var(--efe-muted);
    font-weight: 600;
    max-width: 820px;
  }

  .efe-meta{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
  }

  .efe-chip{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 38px;
    padding: 8px 14px;
    border-radius: 999px;
    border: 1px solid var(--efe-border);
    background: rgba(255,255,255,.88);
    color: var(--efe-text);
    font-size: 13px;
    font-weight: 800;
  }

  .efe-panel{
    border: 1px solid var(--efe-border);
    border-radius: 24px;
    background: var(--efe-bg);
    box-shadow: var(--efe-shadow-sm);
  }

  .efe-panel-body{
    padding: 22px;
  }

  .efe-section-title{
    font-size: 13px;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--efe-muted);
    margin-bottom: 14px;
  }

  .efe-box{
    border: 1px solid var(--efe-border);
    border-radius: 20px;
    background: linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,.96));
    padding: 18px;
  }

  .efe-label{
    display: block;
    margin-bottom: 7px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .10em;
    font-weight: 900;
    color: var(--efe-muted);
  }

  .efe-input,
  .efe-select{
    border-radius: 14px !important;
    min-height: 48px;
    border: 1px solid var(--efe-border-strong) !important;
    background: #fff !important;
    box-shadow: none !important;
    font-weight: 700;
    color: var(--efe-text) !important;
  }

  .efe-input:focus,
  .efe-select:focus{
    border-color: rgba(13,110,253,.35) !important;
    box-shadow: 0 0 0 .2rem rgba(13,110,253,.10) !important;
  }

  .efe-readonly{
    background: #f8fafc !important;
  }

  .efe-note{
    color: var(--efe-muted);
    font-size: 12px;
    font-weight: 700;
    margin-top: 8px;
  }

  .efe-btn{
    min-height: 44px;
    border-radius: 14px !important;
    font-weight: 900 !important;
    padding-inline: 16px;
  }

  .efe-btn-primary{
    background: linear-gradient(135deg, #0d6efd, #0b5ed7);
    border: none !important;
  }

  .efe-btn-soft{
    border: 1px solid var(--efe-border-strong) !important;
    background: #fff !important;
    color: var(--efe-text) !important;
  }

  .efe-divider{
    border-top: 1px solid var(--efe-border);
    margin: 22px 0;
  }

  .efe-alert-soft{
    border: 1px solid var(--efe-border);
    background: linear-gradient(180deg, rgba(248,250,252,.96), rgba(255,255,255,.96));
    border-radius: 18px;
    color: var(--efe-muted);
  }

  .efe-pass-group .btn{
    min-height: 48px;
    font-weight: 900 !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efe-wrap{
    --efe-text: rgba(231,237,246,.95);
    --efe-muted: rgba(231,237,246,.66);
    --efe-border: rgba(255,255,255,.09);
    --efe-border-strong: rgba(255,255,255,.12);
    --efe-bg: rgba(10,14,20,.92);
    --efe-bg-soft: rgba(15,20,28,.88);
    --efe-bg-soft-2: rgba(18,24,33,.92);
    --efe-shadow-sm: 0 14px 40px rgba(0,0,0,.32);
    --efe-shadow-md: 0 24px 60px rgba(0,0,0,.42);
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efe-hero{
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
  ) .efe-chip,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efe-panel,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efe-box,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efe-alert-soft{
    background: rgba(15,20,28,.86) !important;
    border-color: var(--efe-border) !important;
    color: var(--efe-text) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efe-input,
  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efe-select{
    background: rgba(15,20,28,.82) !important;
    color: var(--efe-text) !important;
    border-color: var(--efe-border) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efe-readonly{
    background: rgba(20,27,39,.95) !important;
  }

  :is(
    html[data-theme="dark"] body,
    body[data-theme="dark"],
    body.theme-dark,
    body.dark,
    body[data-bs-theme="dark"]
  ) .efe-btn-soft{
    background: rgba(15,20,28,.82) !important;
    color: var(--efe-text) !important;
    border-color: var(--efe-border) !important;
  }
</style>

<div class="container-fluid py-3 efe-wrap">
  <div class="efe-page">

    <div class="efe-hero mb-4">
      <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
        <div>
          <div class="efe-kicker">GRR • PRF • Edição de efetivo</div>
          <h1 class="efe-title">Editar oficial</h1>
          <div class="efe-sub">
            Atualize os dados cadastrais do oficial, ajuste nível, status e, se necessário, defina uma nova senha de acesso.
          </div>

          <div class="efe-meta">
            <span class="efe-chip">ID: <strong>{{ $user->id }}</strong></span>
            <span class="efe-chip">Seu nível: <strong>{{ $authNivel }}</strong></span>
            <span class="efe-chip">Status atual: <strong>{{ strtoupper($user->status ?? '—') }}</strong></span>
          </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('efetivo.index') }}" class="btn efe-btn efe-btn-soft">Voltar</a>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">{{ session('error') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger rounded-4">
        <div class="fw-black mb-1">Corrija os campos abaixo:</div>
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="efe-panel">
      <div class="efe-panel-body">

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <div class="fw-black">Dados do oficial</div>
            <div class="text-muted small fw-semibold">Atualize as informações e salve ao final.</div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill {{ $badge }} px-3 py-2" style="font-weight:900;">
              {{ strtoupper($user->status ?? '—') }}
            </span>
            <span class="badge rounded-pill text-bg-dark px-3 py-2" style="font-weight:900;">
              ID {{ $user->id }}
            </span>
          </div>
        </div>

        @if(!$canManage)
          <div class="alert efe-alert-soft mb-3 small">
            Você pode visualizar, mas não tem permissão para salvar alterações. (Necessário nível 9+)
          </div>
        @endif

        <form method="POST" action="{{ route('efetivo.update', $user->id) }}" class="row g-3" id="formEfetivoEdit">
          @csrf
          @method('PUT')

          <div class="efe-box">
            <div class="efe-section-title">Informações principais</div>

            <div class="row g-3">
              <div class="col-md-5">
                <label class="efe-label">Nome</label>
                <input
                  name="name"
                  class="form-control efe-input"
                  value="{{ old('name', $user->name) }}"
                  required
                  @disabled(!$canManage)
                >
              </div>

              <div class="col-md-3">
                <label class="efe-label">RG</label>
                <input
                  name="rg"
                  class="form-control efe-input"
                  value="{{ old('rg', $user->rg) }}"
                  required
                  @disabled(!$canManage)
                >
              </div>

              <div class="col-md-4">
                <label class="efe-label">Nível</label>

                @if($editingHigh && $authNivel < 10)
                  <input class="form-control efe-input efe-readonly"
                         value="{{ (int)$user->nivel }} — {{ $cargos[(int)$user->nivel] ?? '—' }}" readonly>
                  <div class="efe-note">Apenas Diretor (nível 10) pode editar nível 9/10.</div>
                  <input type="hidden" name="nivel" value="{{ (int)$user->nivel }}">
                @else
                  <select
                    name="nivel"
                    id="nivelSelect"
                    class="form-select efe-select"
                    required
                    @disabled(!$canManage || $isSelf)
                  >
                    @foreach($allowed as $nivel => $cargo)
                      <option value="{{ (int)$nivel }}" @selected((int)old('nivel', (int)$user->nivel) === (int)$nivel)>
                        {{ (int)$nivel }} — {{ $cargo }}
                      </option>
                    @endforeach
                  </select>

                  @if($isSelf)
                    <div class="efe-note">Você não pode alterar o seu próprio nível.</div>
                    <input type="hidden" name="nivel" value="{{ (int)$user->nivel }}">
                  @else
                    <div class="efe-note">Mostrando apenas níveis permitidos para você.</div>
                  @endif

                  @if(!$canManage)
                    <input type="hidden" name="nivel" value="{{ (int)$user->nivel }}">
                  @endif
                @endif
              </div>

              <div class="col-md-4">
                <label class="efe-label">Cargo automático</label>
                <input id="cargoAuto" class="form-control efe-input efe-readonly" value="{{ $cargoNow }}" readonly>
                <div class="efe-note">O cargo é definido pelo nível.</div>
              </div>

              <div class="col-md-3">
                <label class="efe-label">Status</label>
                <select
                  name="status"
                  id="statusSelect"
                  class="form-select efe-select"
                  required
                  @disabled(!$canManage)
                >
                  @foreach(['ativo' => 'Ativo', 'suspenso' => 'Suspenso', 'desligado' => 'Desligado'] as $k => $v)
                    <option value="{{ $k }}" @selected(old('status', $user->status) === $k)>{{ $v }}</option>
                  @endforeach
                </select>
                <div class="efe-note">Se mudar para suspenso/desligado, informe o motivo.</div>

                @if(!$canManage)
                  <input type="hidden" name="status" value="{{ (string)($user->status ?? 'ativo') }}">
                @endif
              </div>

              <div class="col-md-5">
                <label class="efe-label">Motivo</label>
                <input
                  name="motivo"
                  id="motivoInput"
                  class="form-control efe-input"
                  value="{{ old('motivo') }}"
                  placeholder="Obrigatório ao suspender/desligar"
                  @disabled(!$canManage)
                >
                <div class="efe-note" id="motivoHint">Preencha apenas se necessário.</div>
              </div>
            </div>
          </div>

          <div class="efe-box">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
              <div>
                <div class="efe-section-title mb-1">Redefinição de senha</div>
                <div class="text-muted small fw-semibold">
                  Preencha somente se quiser criar uma nova senha para este oficial.
                </div>
              </div>

              <button type="button" id="btnGerarSenha" class="btn btn-sm efe-btn efe-btn-soft py-1 px-3" style="min-height:auto;" @disabled(!$canManage)>
                Gerar forte
              </button>
            </div>

            <div class="row g-3">
              <div class="col-md-7">
                <label class="efe-label">Nova senha</label>
                <div class="input-group efe-pass-group">
                  <input
                    type="password"
                    name="password"
                    id="passwordInput"
                    class="form-control efe-input"
                    minlength="6"
                    maxlength="120"
                    placeholder="Deixe em branco para não alterar"
                    autocomplete="new-password"
                    @disabled(!$canManage)
                  >
                  <button type="button" id="btnToggleSenha" class="btn efe-btn efe-btn-soft px-3" @disabled(!$canManage)>
                    👁
                  </button>
                </div>
                <div class="efe-note">
                  Se o campo ficar vazio, a senha atual será mantida.
                </div>
              </div>

              <div class="col-md-5">
                <label class="efe-label">Resumo</label>
                <div class="alert efe-alert-soft mb-0 small h-100 d-flex align-items-center">
                  Use este campo quando o oficial perder o acesso ou precisar receber uma nova credencial provisória.
                </div>
              </div>
            </div>
          </div>

          <div class="col-12">
            <div class="efe-divider"></div>
            <div class="d-flex gap-2 flex-wrap">
              <button id="btnSalvar" class="btn btn-primary efe-btn efe-btn-primary" @disabled(!$canManage)>
                Salvar alterações
              </button>

              <a href="{{ route('efetivo.index') }}" class="btn efe-btn efe-btn-soft">
                Cancelar
              </a>
            </div>
          </div>
        </form>

      </div>
    </div>

  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const allCargos = @json($cargos);

    const selNivel = document.getElementById('nivelSelect');
    const cargoEl = document.getElementById('cargoAuto');

    const selStatus = document.getElementById('statusSelect');
    const motivo = document.getElementById('motivoInput');
    const hint = document.getElementById('motivoHint');

    const pass = document.getElementById('passwordInput');
    const btnToggle = document.getElementById('btnToggleSenha');
    const btnGerar = document.getElementById('btnGerarSenha');

    const syncCargo = () => {
      if (!selNivel || !cargoEl) return;
      const nivel = parseInt(selNivel.value || '0', 10);
      cargoEl.value = allCargos[nivel] || '—';
    };

    if (selNivel && cargoEl) {
      selNivel.addEventListener('change', syncCargo);
      syncCargo();
    }

    const syncMotivo = () => {
      if (!selStatus || !motivo || !hint) return;

      const st = (selStatus.value || '').toLowerCase();
      const required = (st === 'suspenso' || st === 'desligado');

      if (required) {
        hint.textContent = 'Obrigatório: informe o motivo para ' + (st === 'suspenso' ? 'suspender' : 'desligar') + '.';
        motivo.setAttribute('required', 'required');
      } else {
        hint.textContent = 'Preencha apenas se necessário.';
        motivo.removeAttribute('required');
      }
    };

    if (selStatus && motivo && hint) {
      selStatus.addEventListener('change', syncMotivo);
      syncMotivo();
    }

    if (pass && btnToggle) {
      btnToggle.addEventListener('click', () => {
        pass.type = (pass.type === 'password') ? 'text' : 'password';
        btnToggle.innerText = pass.type === 'password' ? '👁' : '🙈';
      });
    }

    const genStrongPassword = (len = 14) => {
      const a = "ABCDEFGHJKLMNPQRSTUVWXYZ";
      const b = "abcdefghijkmnopqrstuvwxyz";
      const c = "23456789";
      const d = "!@#$%&*_-+=";

      const pick = (s) => s[Math.floor(Math.random() * s.length)];
      let out = pick(a) + pick(b) + pick(c) + pick(d);

      const all = a + b + c + d;
      while (out.length < len) out += pick(all);

      return out.split('').sort(() => Math.random() - 0.5).join('');
    };

    if (btnGerar && pass) {
      btnGerar.addEventListener('click', () => {
        const nova = genStrongPassword(14);
        pass.value = nova;
        pass.type = 'text';
        if (btnToggle) btnToggle.innerText = '🙈';

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(nova).catch(() => {});
        }
      });
    }

    const form = document.getElementById('formEfetivoEdit');
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