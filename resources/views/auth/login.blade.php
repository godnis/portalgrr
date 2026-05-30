@extends('layouts.guest-gov')

@section('title', 'GRR • PRF — Login')

@section('content')
<style>
    .gov-register-modal .modal-content{
        border-radius: 22px !important;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, .08);
        box-shadow: 0 30px 80px rgba(0, 0, 0, .28);
        background:
            linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,.98));
    }

    .gov-register-modal .modal-header{
        padding: 20px 22px 14px;
        border-bottom: 1px solid rgba(15, 23, 42, .08);
        background:
            radial-gradient(circle at top right, rgba(212,175,55,.10), transparent 28%),
            linear-gradient(180deg, rgba(19,81,180,.03), rgba(255,255,255,0));
    }

    .gov-register-modal .modal-body{
        padding: 20px 22px 18px;
    }

    .gov-register-modal .modal-footer{
        padding: 14px 22px 22px;
        border-top: 1px solid rgba(15, 23, 42, .08);
        background: rgba(248, 250, 252, .78);
    }

    .gov-register-head{
        display:flex;
        align-items:flex-start;
        gap:14px;
    }

    .gov-register-head__ico{
        width:48px;
        height:48px;
        border-radius:16px;
        display:grid;
        place-items:center;
        flex:0 0 48px;
        font-size:1.15rem;
        background:
            linear-gradient(145deg, rgba(212,175,55,.18), rgba(212,175,55,.06)),
            rgba(19,81,180,.04);
        border:1px solid rgba(212,175,55,.28);
        color:#7a5a00;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.45);
    }

    .gov-register-title{
        margin:0;
        font-size:1.18rem;
        font-weight:900;
        color:#0f172a;
        letter-spacing:-.02em;
    }

    .gov-register-subtitle{
        margin:4px 0 0;
        color:#64748b;
        font-size:.92rem;
        line-height:1.55;
        font-weight:500;
    }

    .gov-register-info{
        margin-bottom:16px;
        border:1px solid rgba(19,81,180,.10);
        background:
            linear-gradient(180deg, rgba(19,81,180,.05), rgba(19,81,180,.02));
        border-radius:16px;
        padding:14px 15px;
    }

    .gov-register-info__title{
        font-size:.78rem;
        font-weight:900;
        letter-spacing:.10em;
        text-transform:uppercase;
        color:#1351B4;
        margin-bottom:6px;
    }

    .gov-register-info__text{
        color:#334155;
        font-size:.92rem;
        line-height:1.58;
        margin:0;
    }

    .gov-register-modal .form-label{
        font-weight:800 !important;
        color:#0f172a !important;
        font-size:.92rem !important;
        margin-bottom:7px !important;
        text-shadow:none !important;
    }

    .gov-register-modal .form-control{
        min-height:48px;
        border-radius:14px;
        border:1px solid #d7deea;
        background:#fff;
        box-shadow:none !important;
        font-weight:600;
        color:#0f172a !important;
    }

    .gov-register-modal .form-control::placeholder{
        color:#64748b;
        opacity:1;
    }

    .gov-register-modal .form-control:focus{
        border-color:rgba(19,81,180,.40);
        box-shadow:0 0 0 4px rgba(19,81,180,.10) !important;
    }

    .gov-register-modal .form-control[readonly]{
        background:linear-gradient(180deg, #f8fafc, #f1f5f9);
        color:#0f172a;
        font-weight:800;
    }

    .gov-register-preview{
        display:flex;
        align-items:center;
        gap:10px;
        min-height:48px;
        border:1px dashed rgba(19,81,180,.22);
        background:rgba(19,81,180,.04);
        border-radius:14px;
        padding:10px 12px;
    }

    .gov-register-preview__ico{
        width:34px;
        height:34px;
        border-radius:12px;
        display:grid;
        place-items:center;
        background:rgba(19,81,180,.10);
        color:#1351B4;
        flex:0 0 34px;
        font-size:.95rem;
    }

    .gov-register-preview__text{
        min-width:0;
        font-size:.92rem;
        font-weight:800;
        color:#0f172a;
        overflow-wrap:anywhere;
    }

    .gov-register-hint{
        margin-top:8px;
        color:#64748b;
        font-size:.80rem;
        line-height:1.45;
    }

    .gov-register-note{
        margin-top:18px;
        border:1px solid rgba(212,175,55,.26);
        background:linear-gradient(180deg, rgba(249,176,0,.14), rgba(249,176,0,.08));
        border-radius:16px;
        padding:14px 15px;
        color:#5b4300;
        font-size:.92rem;
        line-height:1.6;
    }

    .gov-register-note strong{
        color:#3d2d00;
    }

    .gov-register-actions{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:12px;
        flex-wrap:wrap;
        width:100%;
    }

    .gov-register-actions__meta{
        font-size:.80rem;
        color:#64748b;
        font-weight:600;
    }

    .gov-register-btn{
        min-height:46px;
        border-radius:14px !important;
        font-weight:800 !important;
        padding:0 18px !important;
    }

    .gov-register-btn.btn-light{
        border:1px solid #dbe3f0 !important;
        background:#fff !important;
        color:#0f172a !important;
    }

    .gov-register-btn.btn-primary{
        background:linear-gradient(135deg, #0B2A4A, #1351B4) !important;
        border-color:#0B2A4A !important;
        box-shadow:0 12px 24px rgba(11,42,74,.18);
        color:#fff !important;
    }

    .gov-register-btn.btn-primary:hover{
        filter:brightness(1.04);
    }

    .gov-register-btn .btn-load{
        display:none;
    }

    .gov-register-btn.is-loading .btn-txt{
        display:none;
    }

    .gov-register-btn.is-loading .btn-load{
        display:inline-flex;
        align-items:center;
        gap:8px;
    }

    .gov-auth-btn.is-loading{
        pointer-events:none;
        opacity:.92;
    }

    .gov-auth-btn .gov-btn__load{
        display:none;
        align-items:center;
        justify-content:center;
        gap:8px;
    }

    .gov-auth-btn.is-loading .gov-btn__txt{
        display:none;
    }

    .gov-auth-btn.is-loading .gov-btn__load{
        display:inline-flex;
    }

    @media (max-width: 767.98px){
        .gov-register-modal .modal-header,
        .gov-register-modal .modal-body,
        .gov-register-modal .modal-footer{
            padding-left:16px;
            padding-right:16px;
        }

        .gov-register-actions{
            flex-direction:column;
            align-items:stretch;
        }

        .gov-register-actions__meta{
            order:3;
            text-align:center;
        }

        .gov-register-btn{
            width:100%;
        }
    }
</style>

<div class="gov-auth-grid">

    {{-- COLUNA INSTITUCIONAL --}}
    <aside class="gov-auth-side">
        <div class="gov-auth-side__top">
            <div class="gov-auth-brand">
                <div class="gov-auth-emblem">
                    <span class="gov-auth-emblem-dot"></span>
                </div>

                <div class="gov-auth-brand__txt">
                    <div class="gov-auth-brand__eyebrow">Grupo de Resposta Rápida</div>
                    <div class="gov-auth-brand__title">GRR</div>
                    <div class="gov-auth-brand__sub">Portal Operacional</div>
                </div>
            </div>

            <div class="gov-auth-side__meta">
                <span class="gov-auth-chip">
                    <span class="gov-auth-chip__dot"></span>
                    Acesso restrito • fivem.bc
                </span>

                <div class="gov-auth-side__hint">
                    Ambiente institucional protegido. Todas as ações realizadas neste sistema poderão ser registradas para auditoria e controle interno.
                </div>
            </div>
        </div>

        <div class="gov-auth-side__center">
            <div class="gov-auth-panel">
                <div class="gov-auth-panel__title">Diretrizes de acesso</div>

                <ul class="gov-auth-panel__list">
                    <li>Uso exclusivo para efetivo autorizado</li>
                    <li>Credenciais pessoais e intransferíveis</li>
                    <li>Solicitações passam por análise administrativa</li>
                    <li>Tentativas indevidas poderão gerar bloqueio</li>
                </ul>
            </div>
        </div>

        <div class="gov-auth-side__bottom">
            <div class="gov-auth-side__line"></div>

            <div class="gov-auth-side__foot">
                <span class="gov-auth-lock">
                    <span class="gov-auth-dot"></span>
                    Sessão monitorada
                </span>

                <span class="gov-auth-side__mini">
                    Uso restrito ao efetivo autorizado
                </span>
            </div>
        </div>
    </aside>

    {{-- COLUNA FORMULÁRIO --}}
    <section class="gov-auth-main">
        <div class="gov-auth-head">
            <a href="{{ route('portal') }}" class="gov-auth-back">
                ← Voltar para a página da população
            </a>

            <div class="gov-auth-kicker">Acesso institucional</div>
            <h1 class="gov-auth-title">Login</h1>
            <p class="gov-auth-subtitle">
                Informe suas credenciais para acessar o painel operacional da GRR.
            </p>
        </div>

        <div class="gov-auth-body">
            @if ($errors->any())
                <div class="alert alert-danger gov-alert gov-auto-dismiss mb-3" role="alert">
                    <strong>Falha na autenticação.</strong><br>
                    Verifique os dados informados e tente novamente.
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-info gov-alert gov-auto-dismiss mb-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="gov-auth-form" novalidate id="loginForm">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail institucional</label>

                    <div class="gov-input @error('email') gov-input--error @enderror">
                        <span class="gov-input__ico" aria-hidden="true">✉️</span>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            class="form-control gov-input__field"
                            value="{{ old('email') }}"
                            placeholder="nome.sobrenome@grr.com"
                            required
                            autofocus
                            autocomplete="username"
                            spellcheck="false"
                        >
                    </div>

                    @error('email')
                        <div class="gov-field-error gov-field-error--dismiss">
                            <span class="gov-field-error__icon">⚠️</span>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="passwordField" class="form-label">Senha</label>

                    <div class="gov-input gov-input--pass @error('password') gov-input--error @enderror">
                        <span class="gov-input__ico" aria-hidden="true">🔒</span>

                        <input
                            id="passwordField"
                            type="password"
                            name="password"
                            class="form-control gov-input__field"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >

                        <button
                            class="gov-input__toggle"
                            type="button"
                            id="togglePass"
                            aria-label="Mostrar senha"
                            aria-controls="passwordField"
                            aria-pressed="false"
                        >
                            👁️
                        </button>
                    </div>

                    @error('password')
                        <div class="gov-field-error gov-field-error--dismiss">
                            <span class="gov-field-error__icon">⚠️</span>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <div class="gov-auth-actions">
                    <label class="d-flex align-items-center gap-2 m-0 gov-auth-check" for="remember">
                        <input
                            id="remember"
                            type="checkbox"
                            name="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        Lembrar de mim
                    </label>

                    <a
                        href="#"
                        class="gov-auth-link"
                        data-bs-toggle="modal"
                        data-bs-target="#passwordHelpModal"
                    >
                        Esqueceu sua senha?
                    </a>
                </div>

                <button class="btn btn-primary w-100 mt-3 gov-auth-btn" type="submit" id="loginSubmitBtn">
                    <span class="gov-btn__txt">Entrar no sistema</span>
                    <span class="gov-btn__load">
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                        Entrando...
                    </span>
                </button>

                <button
                    type="button"
                    class="btn btn-outline-secondary w-100 mt-2 gov-auth-btn"
                    data-bs-toggle="modal"
                    data-bs-target="#registerModal"
                >
                    Solicitar acesso
                </button>

                <div class="gov-auth-mini mt-3">
                    Ao acessar este sistema, você declara ciência das regras internas, dos registros institucionais e do monitoramento do ambiente.
                </div>
            </form>
        </div>
    </section>
</div>

<script>
    (function () {
        const passwordField = document.getElementById('passwordField');
        const togglePass = document.getElementById('togglePass');
        const loginSubmitBtn = document.getElementById('loginSubmitBtn');
        const loginForm = document.getElementById('loginForm');

        const registerSubmitBtn = document.getElementById('registerSubmitBtn');
        const nomeEl = document.getElementById('cadNome');
        const sobrenomeEl = document.getElementById('cadSobrenome');
        const rgEl = document.getElementById('cadRg');
        const emailPreview = document.getElementById('cadEmailPreview');
        const emailPreviewText = document.getElementById('cadEmailPreviewText');
        const emailHidden = document.getElementById('cadEmailHidden');
        const registerModal = document.getElementById('registerModal');
        const registerForm = document.getElementById('registerForm');

        if (passwordField && togglePass) {
            togglePass.addEventListener('click', function () {
                const isPassword = passwordField.type === 'password';

                passwordField.type = isPassword ? 'text' : 'password';
                togglePass.textContent = isPassword ? '🙈' : '👁️';
                togglePass.setAttribute('aria-label', isPassword ? 'Ocultar senha' : 'Mostrar senha');
                togglePass.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
            });
        }

        if (loginForm && loginSubmitBtn) {
            loginForm.addEventListener('submit', function () {
                loginSubmitBtn.classList.add('is-loading');
                loginSubmitBtn.disabled = true;
            });
        }

        function normalizeText(value) {
            return (value || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^\p{L}\s-]/gu, '')
                .trim();
        }

        function capitalizeNameToken(value) {
            const clean = normalizeText(value);

            if (!clean) return '';

            return clean
                .split(/(\s|-)/g)
                .map(part => {
                    if (part === ' ' || part === '-') return part;
                    const lower = part.toLowerCase();
                    return lower.charAt(0).toUpperCase() + lower.slice(1);
                })
                .join('')
                .replace(/[\s-]+/g, '');
        }

        function buildEmail() {
            if (!nomeEl || !sobrenomeEl || !emailPreview || !emailHidden || !emailPreviewText) return;

            const nome = capitalizeNameToken(nomeEl.value);
            const sobrenome = capitalizeNameToken(sobrenomeEl.value);

            if (!nome || !sobrenome) {
                emailPreview.value = '';
                emailHidden.value = '';
                emailPreviewText.textContent = 'Nome.Sobrenome@grr.com';
                return;
            }

            const email = `${nome}.${sobrenome}@grr.com`;
            emailPreview.value = email;
            emailHidden.value = email;
            emailPreviewText.textContent = email;
        }

        if (nomeEl) {
            nomeEl.addEventListener('input', buildEmail);
            nomeEl.addEventListener('blur', buildEmail);
        }

        if (sobrenomeEl) {
            sobrenomeEl.addEventListener('input', buildEmail);
            sobrenomeEl.addEventListener('blur', buildEmail);
        }

        if (rgEl) {
            rgEl.addEventListener('input', function () {
                this.value = this.value.replace(/\D+/g, '');
            });
        }

        if (registerForm && registerSubmitBtn) {
            registerForm.addEventListener('submit', function (event) {
                buildEmail();

                if (!emailHidden.value) {
                    event.preventDefault();
                    if (nomeEl) nomeEl.focus();
                    return;
                }

                registerSubmitBtn.classList.add('is-loading');
                registerSubmitBtn.disabled = true;
            });
        }

        if (registerModal) {
            registerModal.addEventListener('shown.bs.modal', function () {
                buildEmail();
                if (nomeEl) nomeEl.focus();
            });

            registerModal.addEventListener('hidden.bs.modal', function () {
                if (registerForm) registerForm.reset();
                if (emailPreview) emailPreview.value = '';
                if (emailHidden) emailHidden.value = '';
                if (emailPreviewText) emailPreviewText.textContent = 'Nome.Sobrenome@grr.com';

                if (registerSubmitBtn) {
                    registerSubmitBtn.classList.remove('is-loading');
                    registerSubmitBtn.disabled = false;
                }
            });
        }

        function dismissElement(element, delay = 4000) {
            if (!element) return;

            setTimeout(() => {
                element.style.transition = 'opacity 0.4s ease, transform 0.4s ease, max-height 0.4s ease, margin 0.4s ease, padding 0.4s ease';
                element.style.opacity = '0';
                element.style.transform = 'translateY(-8px)';
                element.style.maxHeight = '0';
                element.style.marginTop = '0';
                element.style.marginBottom = '0';
                element.style.paddingTop = '0';
                element.style.paddingBottom = '0';
                element.style.overflow = 'hidden';

                setTimeout(() => {
                    element.remove();
                }, 450);
            }, delay);
        }

        document.querySelectorAll('.gov-auto-dismiss').forEach((alert) => {
            dismissElement(alert, 4000);
        });

        document.querySelectorAll('.gov-field-error--dismiss').forEach((fieldError) => {
            dismissElement(fieldError, 4000);
        });
    })();
</script>
@endsection

@push('modals')
<div class="modal fade gov-register-modal" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <div class="gov-register-head">
                    <div class="gov-register-head__ico">🛂</div>

                    <div>
                        <h5 class="gov-register-title" id="registerModalLabel">Solicitação de Acesso</h5>
                        <p class="gov-register-subtitle">
                            Adicione o bot em seu Discord e solicite o seu acesso conforme instruções abaixo.
                        </p>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form method="POST" action="{{ route('solicitacao.store') }}" id="registerForm" novalidate>
                @csrf

                <div class="modal-body">
                    <div class="gov-register-info">
                        <div class="gov-register-info__title">Passo 01:</div>
                        <p class="gov-register-info__text">
                            Click no botão abaixo para ser redirecionado ao Discord e autorizar o bot oficial da GRR em seu perfil.
                        </p>
                        <a href="https://discord.com/oauth2/authorize?client_id=1315488135999918141" target="_blank" class="btn btn-primary mt-2">
                            <span class="btn-txt">Adicionar Bot</span>
                        </a>
                    </div>
                    <div class="gov-register-info">
                        <div class="gov-register-info__title">Passo 02:</div>
                        <p class="gov-register-info__text">
                            Autorize o bot em seu Discord.
                        </p>
                            <img src="https://media.discordapp.net/attachments/905428170956750850/1486181142867607732/image.png?ex=69c4918c&is=69c3400c&hm=ef5d4a01a6cc6d5bce29f4e9db6685829083fba5efa5217358e648a71928191a&=&format=webp&quality=lossless" class="img-fluid rounded mt-2">
                    </div>
                    <div class="gov-register-info">
                        <div class="gov-register-info__title">Passo 03:</div>
                        <p class="gov-register-info__text">
                            O bot irá lhe enviar por DM um convite, click para solicitar o acesso.
                        </p>
                            <img src="https://media.discordapp.net/attachments/905428170956750850/1486181199964803102/image.png?ex=69c49199&is=69c34019&hm=722c6e7c914272698d5fbe18cb3e7d9482c856234f502874fddbbe9f5aa6fd95&=&format=webp&quality=lossless" class="img-fluid rounded mt-2">
                    </div>
                    <div class="gov-register-info">
                        <div class="gov-register-info__title">Passo 04:</div>
                        <p class="gov-register-info__text">
                            Preencha o formulário com os dados corretos e envie a solicitação para análise do setor administrativo.
                        </p>
                        <img src="https://media.discordapp.net/attachments/905428170956750850/1486181270861123664/image.png?ex=69c491aa&is=69c3402a&hm=106ed1c681615c2c85a28207e28e255c9ea83dc26b0fdb18e036578bc4d672b0&=&format=webp&quality=lossless" class="img-fluid rounded mt-2">
                    </div>

                    <div class="gov-register-note">
                        <strong>Atenção:</strong>
                        Após enviar a solicitação, os dados serão encaminhados para conferência, validação e aprovação do setor responsável. Você receberá uma DM do bot informando a aprovação ou reprovação do seu cadastro.
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="gov-register-actions">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-light gov-register-btn" data-bs-dismiss="modal">
                                Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
<div class="modal fade gov-register-modal" id="registerModal2" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <div class="gov-register-head">
                    <div class="gov-register-head__ico">🛂</div>

                    <div>
                        <h5 class="gov-register-title" id="registerModalLabel">Solicitação de Acesso</h5>
                        <p class="gov-register-subtitle">
                            Preencha corretamente as informações abaixo para enviar sua solicitação ao setor administrativo.
                        </p>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <form method="POST" action="{{ route('solicitacao.store') }}" id="registerForm" novalidate>
                @csrf

                <div class="modal-body">
                    <div class="gov-register-info">
                        <div class="gov-register-info__title">Análise administrativa</div>
                        <p class="gov-register-info__text">
                            O preenchimento deste formulário não cria acesso imediato. Os dados serão encaminhados para conferência, validação e aprovação do setor responsável.
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cadNome" class="form-label">Nome (Brasil Capital)</label>
                            <input
                                type="text"
                                class="form-control"
                                name="nome"
                                id="cadNome"
                                placeholder="Ex.: Vitor"
                                required
                                maxlength="80"
                                autocomplete="given-name"
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="cadSobrenome" class="form-label">Sobrenome (Brasil Capital)</label>
                            <input
                                type="text"
                                class="form-control"
                                name="sobrenome"
                                id="cadSobrenome"
                                placeholder="Ex.: Luciano"
                                required
                                maxlength="80"
                                autocomplete="family-name"
                            >
                        </div>

                        <div class="col-md-4">
                            <label for="cadRg" class="form-label">RG (Brasil Capital)</label>
                            <input
                                type="text"
                                class="form-control"
                                name="rg"
                                id="cadRg"
                                placeholder="Ex.: 12178"
                                required
                                maxlength="30"
                                inputmode="numeric"
                                autocomplete="off"
                            >
                        </div>

                        <div class="col-md-8">
                            <label for="cadEmailPreview" class="form-label">E-mail institucional gerado</label>

                            <div class="gov-register-preview">
                                <div class="gov-register-preview__ico">✉️</div>
                                <div class="gov-register-preview__text" id="cadEmailPreviewText">
                                    Nome.Sobrenome@grr.com
                                </div>
                            </div>

                            <input
                                type="text"
                                class="form-control d-none"
                                id="cadEmailPreview"
                                value=""
                                readonly
                                tabindex="-1"
                            >

                            <input type="hidden" name="email" id="cadEmailHidden" value="">

                            <div class="gov-register-hint">
                                Formato automático:
                                <span class="font-monospace">Nome.Sobrenome@grr.com</span>
                            </div>
                        </div>
                    </div>

                    <div class="gov-register-note">
                        <strong>Atenção:</strong>
                        esta ação não cria o usuário automaticamente. A solicitação será enviada para análise e somente será liberada após aprovação do Administrativo.
                    </div>
                </div>

                <div class="modal-footer">
                    <div class="gov-register-actions">
                        <div class="gov-register-actions__meta">
                            Confira os dados antes de enviar.
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-light gov-register-btn" data-bs-dismiss="modal">
                                Cancelar
                            </button>

                            <button type="submit" class="btn btn-primary gov-register-btn" id="registerSubmitBtn">
                                <span class="btn-txt">Enviar solicitação</span>
                                <span class="btn-load">
                                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                    Enviando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade gov-register-modal" id="passwordHelpModal" tabindex="-1" aria-labelledby="passwordHelpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <div class="gov-register-head">
                    <div class="gov-register-head__ico">🔐</div>

                    <div>
                        <h5 class="gov-register-title" id="passwordHelpModalLabel">Recuperação de Senha</h5>
                        <p class="gov-register-subtitle">
                            Procedimento de suporte para redefinição de acesso institucional.
                        </p>
                    </div>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="gov-register-info">
                    <div class="gov-register-info__title">Orientação</div>
                    <p class="gov-register-info__text mb-0">
                        Para realizar a recuperação da senha, entre em contato no privado do Discord com
                        <strong>Farias Hartmann</strong> ou com o <strong>Vice-Diretor Thomas Skywalker</strong>.
                    </p>
                </div>

                <div class="gov-register-note mt-3">
                    <strong>Atenção:</strong>
                    informe corretamente seu nome institucional e os dados necessários para agilizar a validação da solicitação.
                </div>
            </div>

            <div class="modal-footer">
                <div class="gov-register-actions">
                    <div class="gov-register-actions__meta">
                        Atendimento realizado pela administração.
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary gov-register-btn" data-bs-dismiss="modal">
                            Entendi
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endpush