@extends('layouts.app')

@section('content')
@php
    // Aba atual por querystring (?tab=...), ou decide automaticamente baseado em erros/status
    $tab = request('tab');

    $bagUpdatePassword = $errors->getBag('updatePassword');
    $bagDefault = $errors; // bag padrão

    // ✅ Se houver status/erros de senha, força a aba Segurança
    if (session('status') === 'password-updated' || ($bagUpdatePassword && $bagUpdatePassword->any())) {
        $tab = 'seguranca';
    }

    // ✅ Se não veio tab e não for segurança, decide por padrão
    if (!$tab) {
        if ($bagDefault->any() || session('status') === 'profile-updated') {
            $tab = 'dados';
        } elseif (session('status') === 'prefs-updated') {
            $tab = 'preferencias';
        } else {
            $tab = 'dados';
        }
    }
@endphp

<div class="gov-content">

    {{-- HEADER --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                <div>
                    <h4 class="fw-black mb-1">Meu Perfil</h4>
                    <p class="text-muted mb-0">
                        Gerencie sua foto, dados, preferências e segurança — uso interno GRR.
                    </p>
                </div>

                <div class="text-muted small">
                    <div><b>Usuário:</b> {{ auth()->user()->name }}</div>
                    <div><b>Nível:</b> {{ auth()->user()->nivel ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALERTAS (GERAIS) --}}
    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    {{-- Se tiver erro geral (bag default), mostra aqui (normalmente dados/prefs) --}}
    @if($errors->any() && !($errors->getBag('updatePassword') && $errors->getBag('updatePassword')->any()))
        <div class="alert alert-danger mb-3">
            <div class="fw-black mb-1">Verifique os campos:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">

        {{-- COLUNA ESQUERDA: CARTÃO DO PERFIL --}}
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-4 overflow-hidden border" style="width:86px;height:86px;">
                            <img id="avatarPreview"
                                 src="{{ auth()->user()->avatar_path ? asset('storage/'.auth()->user()->avatar_path) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=256' }}"
                                 alt="Avatar"
                                 style="width:100%;height:100%;object-fit:cover;">
                        </div>

                        <div class="flex-grow-1">
                            <div class="fw-black" style="font-size:1.05rem;">{{ auth()->user()->name }}</div>
                            <div class="text-muted small">
                                Cargo: <b>{{ auth()->user()->cargo ?? '—' }}</b><br>
                                Unidade: <b>{{ auth()->user()->unidade ?? 'GRR • PRF' }}</b>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    {{-- Upload Avatar --}}
                    <form method="POST" action="{{ route('profile.avatar', ['tab' => $tab]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-2">
                            <label class="form-label fw-semibold">Foto do perfil</label>
                            <input id="avatarInput" type="file" name="avatar" class="form-control" accept="image/*">
                            <div class="text-muted small mt-1">PNG/JPG/WebP — recomendado 512x512.</div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary w-100" type="submit">Salvar foto</button>
                        </div>
                    </form>

                    @if(auth()->user()->avatar_path)
                        <form method="POST" action="{{ route('profile.avatar.remove', ['tab' => $tab]) }}" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger w-100" type="submit">
                                Remover foto
                            </button>
                        </form>
                    @endif

                    <hr class="my-3">

                    {{-- Info rápida --}}
                    <div class="text-muted small">
                        <div class="d-flex justify-content-between">
                            <span>Registro:</span>
                            <b>{{ auth()->user()->rg ?? '—' }}</b>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Discord:</span>
                            <b>{{ auth()->user()->discord ?? '—' }}</b>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Telefone:</span>
                            <b>{{ auth()->user()->telefone ?? '—' }}</b>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- COLUNA DIREITA: ABAS DE EDIÇÃO --}}
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">

                    {{-- Tabs --}}
                    <ul class="nav nav-pills gap-2 mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-semibold {{ $tab === 'dados' ? 'active' : '' }}"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-dados"
                                type="button"
                                role="tab">
                                Dados
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-semibold {{ $tab === 'preferencias' ? 'active' : '' }}"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-preferencias"
                                type="button"
                                role="tab">
                                Preferências
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-semibold {{ $tab === 'seguranca' ? 'active' : '' }}"
                                data-bs-toggle="tab"
                                data-bs-target="#tab-seguranca"
                                type="button"
                                role="tab">
                                Segurança
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">

                        {{-- ================= TAB DADOS ================= --}}
                        <div class="tab-pane fade {{ $tab === 'dados' ? 'show active' : '' }}" id="tab-dados" role="tabpanel">

                            @if(session('status') === 'profile-updated')
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-1"></i> Dados atualizados com sucesso.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('profile.update', ['tab' => 'dados']) }}">
                                @csrf
                                @method('PATCH')

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Nome</label>
                                        <input type="text" name="name" class="form-control"
                                               value="{{ old('name', auth()->user()->name) }}" required>
                                        @error('name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">RG</label>
                                        <input type="text" name="rg" class="form-control"
                                               value="{{ old('rg', auth()->user()->rg) }}" placeholder="Ex.: 123">
                                        @error('rg')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Discord</label>
                                        <input type="text" name="discord" class="form-control"
                                               value="{{ old('discord', auth()->user()->discord) }}" placeholder="Ex.: usuario#0000">
                                        @error('discord')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Telefone</label>
                                        <input type="text" name="telefone" class="form-control"
                                               value="{{ old('telefone', auth()->user()->telefone) }}" placeholder="Ex.: (11) 99999-9999">
                                        @error('telefone')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Unidade</label>
                                        <input type="text" name="unidade" class="form-control"
                                               value="{{ old('unidade', auth()->user()->unidade) }}" placeholder="GRR • PRF">
                                        @error('unidade')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Cargo</label>
                                        <input type="text" name="cargo" class="form-control"
                                               value="{{ old('cargo', auth()->user()->cargo) }}" placeholder="Ex.: Diretor">
                                        @error('cargo')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Biografia (curta)</label>
                                        <input type="text" name="bio" class="form-control"
                                               value="{{ old('bio', auth()->user()->bio) }}" placeholder="Ex.: Foco em disciplina, operação e treinamento.">
                                        @error('bio')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary" type="submit">
                                            Salvar dados
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- ================= TAB PREFERÊNCIAS ================= --}}
                        <div class="tab-pane fade {{ $tab === 'preferencias' ? 'show active' : '' }}" id="tab-preferencias" role="tabpanel">

                            @if(session('status') === 'prefs-updated')
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-1"></i> Preferências atualizadas com sucesso.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('profile.prefs', ['tab' => 'preferencias']) }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Tema</label>
                                        <select name="tema" class="form-select">
                                            <option value="dark" {{ old('tema', auth()->user()->tema) === 'dark' ? 'selected' : '' }}>Dark (padrão GOV)</option>
                                            <option value="light" {{ old('tema', auth()->user()->tema) === 'light' ? 'selected' : '' }}>Light</option>
                                            <option value="system" {{ old('tema', auth()->user()->tema) === 'system' ? 'selected' : '' }}>Sistema</option>
                                        </select>
                                        @error('tema')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <div class="text-muted small mt-1">Você pode aplicar isso no seu layout via atributo no &lt;html&gt;.</div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="notifyOps" name="notify_ops"
                                                   value="1" {{ old('notify_ops', auth()->user()->notify_ops) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="notifyOps">
                                                Receber notificações operacionais
                                            </label>
                                        </div>
                                        @error('notify_ops')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        <div class="text-muted small mt-1">Ex.: alertas de auditoria, aprovações, relatórios pendentes.</div>
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary" type="submit">
                                            Salvar preferências
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- ================= TAB SEGURANÇA ================= --}}
                        <div class="tab-pane fade {{ $tab === 'seguranca' ? 'show active' : '' }}" id="tab-seguranca" role="tabpanel">

                            {{-- ALERTA DE ERROS SÓ DA SENHA (bag updatePassword) --}}
                            @if($errors->getBag('updatePassword') && $errors->getBag('updatePassword')->any())
                                <div class="alert alert-danger">
                                    <div class="fw-black mb-1">Verifique os campos:</div>
                                    <ul class="mb-0">
                                        @foreach($errors->getBag('updatePassword')->all() as $e)
                                            <li>{{ $e }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(session('status') === 'password-updated')
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle me-1"></i> Senha alterada com sucesso.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.update', ['tab' => 'seguranca']) }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Senha atual</label>
                                        <input
                                            type="password"
                                            name="current_password"
                                            class="form-control @if($errors->getBag('updatePassword')?->has('current_password')) is-invalid @endif"
                                            required
                                            autocomplete="current-password">
                                        @if($errors->getBag('updatePassword')?->has('current_password'))
                                            <div class="invalid-feedback">
                                                {{ $errors->getBag('updatePassword')->first('current_password') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-12 col-md-6"></div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Nova senha</label>
                                        <input
                                            type="password"
                                            name="password"
                                            class="form-control @if($errors->getBag('updatePassword')?->has('password')) is-invalid @endif"
                                            required
                                            autocomplete="new-password">
                                        @if($errors->getBag('updatePassword')?->has('password'))
                                            <div class="invalid-feedback">
                                                {{ $errors->getBag('updatePassword')->first('password') }}
                                            </div>
                                        @endif
                                        <div class="text-muted small mt-1">
                                            Dica: use no mínimo 8 caracteres, misture letras, números e símbolos.
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-semibold">Confirmar nova senha</label>
                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            class="form-control @if($errors->getBag('updatePassword')?->has('password_confirmation')) is-invalid @endif"
                                            required
                                            autocomplete="new-password">
                                        @if($errors->getBag('updatePassword')?->has('password_confirmation'))
                                            <div class="invalid-feedback">
                                                {{ $errors->getBag('updatePassword')->first('password_confirmation') }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-12">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="bi bi-shield-lock me-1"></i> Atualizar senha
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div> {{-- tab-content --}}

                </div>
            </div>
        </div>

    </div>
</div>

{{-- Preview do avatar --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('avatarInput');
    const preview = document.getElementById('avatarPreview');
    if (!input || !preview) return;

    input.addEventListener('change', (e) => {
        const file = e.target.files && e.target.files[0];
        if (!file) return;

        const url = URL.createObjectURL(file);
        preview.src = url;

        preview.onload = () => {
            URL.revokeObjectURL(url);
        };
    });
});
</script>
@endsection


Adicione isso aqui 


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
