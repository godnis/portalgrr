@extends('layouts.app')

@section('content')
@php
  $tab = $tab ?? request('tab', 'dados');

  // ✅ bag da senha
  $bagUpdatePassword = $errors->getBag('updatePassword');

  // ✅ força aba segurança em caso de erro/sucesso de senha
  if (session('status') === 'password-updated' || ($bagUpdatePassword && $bagUpdatePassword->any())) {
      $tab = 'seguranca';
  }

  // ✅ Somente nível 10 pode editar nome/e-mail (identidade)
  $podeEditarIdentidade = ((int)($user->nivel ?? 0) >= 10);

  // ✅ avatar
  $avatarUrl = !empty($user->avatar_path)
      ? asset('storage/' . $user->avatar_path)
      : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0B2A4A&color=fff&size=256';

  // ✅ tema (agora só dark|light) — padrão light
  $tema = old('tema', $user->tema ?? 'light');
  if (!in_array($tema, ['dark','light'], true)) $tema = 'light';
@endphp

<div class="gov-content">

  {{-- HEADER --}}
  <div class="card mb-3">
    <div class="card-body">
      <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
        <div>
          <h4 class="gov-title mb-1">Meu Perfil</h4>
          <p class="text-muted mb-0">Gerencie foto, identidade, preferências e segurança.</p>
        </div>
        <div class="text-muted small">
          <div><b>Usuário:</b> {{ $user->name }}</div>
          <div><b>Nível:</b> {{ $user->nivel ?? '-' }}</div>
          <div><b>Status:</b> {{ method_exists($user,'statusLabel') ? $user->statusLabel() : ($user->status ?? '—') }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- ALERTAS --}}
  @if (session('status') === 'profile-updated')
    <div class="alert alert-success">Perfil atualizado com sucesso.</div>
  @endif

  @if (session('status') === 'profile-locked')
    <div class="alert alert-warning">
      Edição de <b>nome/e-mail</b> bloqueada para o seu nível. Somente <b>nível 10</b>.
    </div>
  @endif

  @if (session('status') === 'password-updated')
    <div class="alert alert-success">Senha atualizada com sucesso.</div>
  @endif

  @if (session('status') === 'avatar-updated')
    <div class="alert alert-success">Foto do perfil atualizada com sucesso.</div>
  @endif

  @if (session('status') === 'avatar-removed')
    <div class="alert alert-success">Foto do perfil removida com sucesso.</div>
  @endif

  @if (session('status') === 'prefs-updated')
    <div class="alert alert-success">Preferências atualizadas com sucesso.</div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="row g-3">

    {{-- ESQUERDA: IDENTIDADE + AVATAR --}}
    <div class="col-12 col-lg-4">
      <div class="card h-100">
        <div class="card-body">

          <div class="d-flex align-items-center gap-3">
            <img src="{{ $avatarUrl }}" alt="Foto do perfil"
                 style="width:76px;height:76px;border-radius:18px;object-fit:cover;border:2px solid rgba(255,255,255,.12);">

            <div class="flex-grow-1">
              <div class="fw-black" style="font-size:1.05rem;">{{ $user->name }}</div>
              <div class="text-muted small">
                {{ $user->cargo ?? 'Cargo não definido' }} • Nível {{ $user->nivel ?? '-' }}
              </div>
              <div class="text-muted small">RG: {{ $user->rg ?? '-' }}</div>
            </div>
          </div>

          <hr>

          {{-- FOTO DO PERFIL (UI custom no dark) --}}
          <div class="mb-3">
            <div class="fw-black mb-1">Foto do perfil</div>
            <div class="text-muted small mb-2">JPG/PNG/WEBP até 3MB.</div>

            <form method="POST" action="{{ route('profile.avatar', ['tab' => $tab]) }}" enctype="multipart/form-data" class="d-grid gap-2">
              @csrf
              @method('PUT')

              {{-- input nativo escondido --}}
              <input id="avatarInput" type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="d-none">

              {{-- UI custom --}}
              <div class="filepick">
                <label for="avatarInput" class="filepick__btn">
                  <span class="filepick__ico">📷</span>
                  <span>Selecionar foto</span>
                </label>

                <div class="filepick__name" id="avatarFileName">Nenhum arquivo selecionado</div>
              </div>

              <div id="avatarPreviewWrap" class="d-none">
                <div class="small text-muted mt-1">Pré-visualização:</div>
                <img id="avatarPreview"
                     alt="Prévia do avatar"
                     style="width:100%;max-width:260px;height:160px;object-fit:cover;border-radius:14px;border:1px solid var(--border);">
              </div>

              <button class="btn btn-primary" id="avatarSaveBtn" disabled>Salvar foto</button>
            </form>

            @if (!empty($user->avatar_path))
              <form method="POST" action="{{ route('profile.avatar.remove', ['tab' => $tab]) }}" class="mt-2">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger w-100">Remover foto</button>
              </form>
            @endif
          </div>

          <hr>

          <div class="d-grid gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Voltar ao Dashboard</a>

            @if (\Illuminate\Support\Facades\Route::has('relatorios.index'))
              <a href="{{ route('relatorios.index') }}" class="btn btn-outline-primary">Ir para Relatórios</a>
            @endif
          </div>

          <hr>

          <div class="small text-muted">
            <b>Padrão institucional:</b> foto nítida, sem fundo poluído.
          </div>

        </div>
      </div>
    </div>

    {{-- DIREITA: ABAS --}}
    <div class="col-12 col-lg-8">
      <div class="card h-100">
        <div class="card-body">

          <ul class="nav nav-pills gap-2 mb-3" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link fw-semibold {{ $tab === 'dados' ? 'active' : '' }}"
                      data-bs-toggle="tab" data-bs-target="#tab-dados" type="button" role="tab">
                Dados & Identidade
              </button>
            </li>

            <li class="nav-item" role="presentation">
              <button class="nav-link fw-semibold {{ $tab === 'preferencias' ? 'active' : '' }}"
                      data-bs-toggle="tab" data-bs-target="#tab-preferencias" type="button" role="tab">
                Preferências
              </button>
            </li>

            <li class="nav-item" role="presentation">
              <button class="nav-link fw-semibold {{ $tab === 'seguranca' ? 'active' : '' }}"
                      data-bs-toggle="tab" data-bs-target="#tab-seguranca" type="button" role="tab">
                Segurança
              </button>
            </li>
          </ul>

          <div class="tab-content">

            {{-- TAB DADOS --}}
            <div class="tab-pane fade {{ $tab === 'dados' ? 'show active' : '' }}" id="tab-dados" role="tabpanel">
              <div class="card border-0 profile-softpanel">
                <div class="card-body">
                  <h6 class="fw-black mb-1">Identidade (nome/e-mail)</h6>
                  <div class="text-muted small mb-3">
                    @if($podeEditarIdentidade)
                      Alterações liberadas para <b>nível 10</b>.
                    @else
                      Alterações bloqueadas (somente <b>nível 10</b>).
                    @endif
                  </div>

                  @include('profile.partials.update-profile-information-form', ['user' => $user])
                </div>
              </div>
            </div>

            {{-- TAB PREFERÊNCIAS --}}
            <div class="tab-pane fade {{ $tab === 'preferencias' ? 'show active' : '' }}" id="tab-preferencias" role="tabpanel">
              <div class="card border-0 profile-softpanel">
                <div class="card-body">
                  <h6 class="fw-black mb-1">Preferências</h6>
                  <div class="text-muted small mb-3">Campos opcionais para completar o perfil.</div>

                  <form id="prefsForm" method="POST" action="{{ route('profile.prefs', ['tab' => 'preferencias']) }}" class="row g-2">
                    @csrf
                    @method('PUT')

                    <div class="col-12 col-md-6">
                      <label class="form-label small text-muted">Telefone no BC</label>
                      <input type="text" name="telefone" class="form-control"
                             value="{{ old('telefone', $user->telefone ?? '') }}"
                             placeholder="(00) 00000-0000">
                    </div>

                    <div class="col-12 col-md-6">
                      <label class="form-label small text-muted">Tema</label>

                      <select id="temaSelect" name="tema" class="form-select">
                        <option value="light" @selected($tema==='light')>Light (Padrão)</option>
                        <option value="dark"  @selected($tema==='dark')>Dark</option>
                      </select>

                      <div class="small text-muted mt-1">
                        Ao salvar, o sistema aplica o tema em todo o painel.
                      </div>
                    </div>

                    <div class="col-12">
                      <label class="form-label small text-muted">Bio (curta)</label>
                      <textarea name="bio" rows="3" class="form-control"
                                placeholder="Ex.: Especialidade, setor, estilo de atuação...">{{ old('bio', $user->bio ?? '') }}</textarea>
                      <div class="small text-muted mt-1">Até 500 caracteres.</div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notifyOps" name="notify_ops" value="1"
                               @checked(old('notify_ops', $user->notify_ops ?? false))>
                        <label class="form-check-label fw-semibold" for="notifyOps">
                          Receber notificações operacionais
                        </label>
                      </div>
                      <div class="small text-muted mt-1"></div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                      <button class="btn btn-outline-primary">Salvar preferências</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>

            {{-- TAB SEGURANÇA --}}
            <div class="tab-pane fade {{ $tab === 'seguranca' ? 'show active' : '' }}" id="tab-seguranca" role="tabpanel">

              @if($bagUpdatePassword && $bagUpdatePassword->any())
                <div class="alert alert-danger">
                  <div class="fw-black mb-1">Verifique os campos:</div>
                  <ul class="mb-0">
                    @foreach($bagUpdatePassword->all() as $e)
                      <li>{{ $e }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <div class="card border-0 profile-softpanel">
                <div class="card-body">
                  <h6 class="fw-black mb-1">Alterar senha</h6>
                  <div class="text-muted small mb-3">Use uma senha forte.</div>

                  @include('profile.partials.update-password-form')
                </div>
              </div>

              @if ((int)($user->nivel ?? 0) >= 10)
                <div class="mt-3">
                  <div class="card border-danger">
                    <div class="card-body">
                      <h6 class="fw-black text-danger mb-1">Zona restrita</h6>
                      <div class="text-muted small mb-3">
                        Exclusão de conta é irreversível.
                      </div>

                      @include('profile.partials.delete-user-form')
                    </div>
                  </div>
                </div>
              @else
                <div class="alert alert-info mt-3 mb-0">
                  <b>Exclusão de conta:</b> bloqueada para o seu nível.
                </div>
              @endif

            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- AVATAR: preview + nome do arquivo + habilita botão --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('avatarInput');
  const wrap  = document.getElementById('avatarPreviewWrap');
  const img   = document.getElementById('avatarPreview');
  const name  = document.getElementById('avatarFileName');
  const btn   = document.getElementById('avatarSaveBtn');

  if (!input) return;

  input.addEventListener('change', function () {
    const file = this.files && this.files[0];

    if (!file) {
      if (wrap) wrap.classList.add('d-none');
      if (name) name.textContent = 'Nenhum arquivo selecionado';
      if (btn) btn.disabled = true;
      return;
    }

    if (name) name.textContent = file.name;
    if (btn) btn.disabled = false;

    if (wrap && img) {
      const url = URL.createObjectURL(file);
      img.src = url;
      wrap.classList.remove('d-none');
      img.onload = () => URL.revokeObjectURL(url);
    }
  });
});
</script>

{{-- ✅ Ativa a aba baseada em ?tab= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tab = @json($tab);

  const map = {
    'dados': '#tab-dados',
    'preferencias': '#tab-preferencias',
    'seguranca': '#tab-seguranca',
  };

  const target = map[tab] || '#tab-dados';

  const btn = document.querySelector(`[data-bs-target="${target}"]`);
  if (btn && window.bootstrap) {
    const instance = new bootstrap.Tab(btn);
    instance.show();
  } else if (btn) {
    btn.click();
  }
});
</script>

{{-- ✅ Tema: aplica e PERSISTE (não volta pro branco) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const STORAGE_KEY = 'grr_theme';
  const sel = document.getElementById('temaSelect');
  const html = document.documentElement;

  function isTheme(v){ return v === 'dark' || v === 'light'; }

  // ⚠️ IMPORTANTE:
  // - Se existir tema salvo no navegador, ele manda na UI (evita “voltar pro branco”)
  // - Se não existir, o tema do servidor (banco) continua sendo o fallback natural.

  try{
    const saved = localStorage.getItem(STORAGE_KEY);
    if (isTheme(saved)) {
      html.setAttribute('data-theme', saved);
      if (sel) sel.value = saved;
    }
  } catch(e){}

  if (sel) {
    sel.addEventListener('change', function(){
      const v = this.value;
      if (!isTheme(v)) return;
      html.setAttribute('data-theme', v);
      try{ localStorage.setItem(STORAGE_KEY, v); } catch(e){}
    });
  }

  const form = document.getElementById('prefsForm');
  if (form && sel) {
    form.addEventListener('submit', function(){
      const v = sel.value;
      if (isTheme(v)) {
        try{ localStorage.setItem(STORAGE_KEY, v); } catch(e){}
      }
    });
  }
});
</script>

<style>
  .profile-softpanel{
    background: var(--surface2) !important;
    border: 1px solid var(--border) !important;
    box-shadow: none !important;
    border-radius: 16px;
  }

  /* ====== Upload bonito (dark e light) ====== */
  .filepick{
    display:flex;
    align-items:center;
    gap:12px;
    padding:10px 12px;
    border:1px solid var(--border);
    border-radius:14px;
    background: var(--surface2);
  }
  .filepick__btn{
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:10px 12px;
    border-radius:12px;
    cursor:pointer;
    user-select:none;
    font-weight:900;
    letter-spacing:.2px;
    border:1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.06);
    color: var(--text);
    transition: transform .08s ease, opacity .08s ease, background .15s ease;
    white-space:nowrap;
  }
  html[data-theme="light"] .filepick__btn{
    border-color: rgba(2,6,23,.10);
    background: rgba(2,6,23,.03);
  }
  .filepick__btn:hover{ opacity:.95; }
  .filepick__btn:active{ transform: translateY(1px); }

  .filepick__ico{ font-size: 16px; line-height: 1; }

  .filepick__name{
    flex:1;
    min-width:0;
    font-weight:800;
    color: var(--muted);
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    padding:8px 10px;
    border-radius:12px;
    border:1px dashed rgba(255,255,255,.14);
    background: rgba(0,0,0,.12);
  }
  html[data-theme="light"] .filepick__name{
    border-color: rgba(2,6,23,.16);
    background: rgba(255,255,255,.65);
  }
</style>

@endsection