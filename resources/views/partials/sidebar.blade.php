@php
    $user = auth()->user();
    $nivel = (int) ($user->nivel ?? 0);

    $parts = preg_split('/\s+/', trim((string) ($user->name ?? 'GRR')));
    $initials = '';
    foreach(array_slice($parts, 0, 2) as $p){
        $initials .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    $initials = $initials ?: 'GR';

    // Flags de módulos
    $hasDashboard   = Route::has('dashboard');
    $hasRanking     = Route::has('ranking.index');

    // Relatórios
    $canRelatorios  = $nivel >= 2;
    $hasRelIndex    = Route::has('relatorios.index');
    $hasRelCreate   = Route::has('relatorios.create');

    // Regulamento
    $hasRegIndex    = Route::has('regulamento.index');
    $hasRegInstr    = Route::has('regulamento.instrucoes');
    $hasRegFard     = Route::has('regulamento.fardamento');
    $hasRegInterno  = Route::has('regulamento.interno');

    // Dossiê
    $hasDossie      = $nivel >= 5 && Route::has('dossie.index');

    // Suporte
    $hasTicketsIndex  = Route::has('tickets.index');
    $hasTicketsCreate = Route::has('tickets.create');
    $hasSuporteFaq    = Route::has('suporte.faq');
    $showSuporteGroup = $hasTicketsIndex || $hasTicketsCreate || $hasSuporteFaq;

    // Administrativo
    $hasAuditoria      = $nivel >= 7 && Route::has('auditoria.index');
    $hasEfetivo        = $nivel >= 7 && Route::has('efetivo.index');
    $hasAtend          = $nivel >= 7 && Route::has('atendimentos.index');
    $hasPreInscricoes  = $nivel >= 9 && Route::has('admin.preinscricoes.index');
    $hasAdminTickets   = $nivel >= 7 && Route::has('admin.tickets.index');
    $hasSolicAcesso    = $nivel >= 10 && Route::has('admin.solicitacoes.index');

    $showAdminGroup = $hasAuditoria || $hasEfetivo || $hasAtend || $hasPreInscricoes || $hasAdminTickets || $hasSolicAcesso;

    // RH
    $hasRhIndex = Route::has('rh.index');

    // Conta
    $hasProfile = Route::has('profile.edit');
    $hasLogout  = Route::has('logout');

    $isActive = function (...$patterns) {
        foreach ($patterns as $pattern) {
            if (request()->routeIs($pattern)) return true;
        }
        return false;
    };
@endphp

<style>
    .grr-sidebar{
        padding: 18px 14px 18px;
        min-height: 100%;
        color: var(--sidebarText);
    }

    .grr-sidebar__brand{
        display:flex;
        align-items:center;
        gap:12px;
        padding: 8px 8px 18px;
        margin-bottom: 8px;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }

    .grr-sidebar__avatar{
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display:grid;
        place-items:center;
        flex: 0 0 auto;
        font-weight: 900;
        font-size: .96rem;
        color: #fff;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,.18), transparent 40%),
            linear-gradient(135deg, rgba(90,162,255,.34), rgba(7,18,34,.92));
        border: 1px solid rgba(255,255,255,.10);
        box-shadow: 0 14px 30px rgba(0,0,0,.28);
    }

    .grr-sidebar__brandtext{
        min-width: 0;
    }

    .grr-sidebar__title{
        font-size: 1rem;
        font-weight: 900;
        color: #fff;
        line-height: 1.1;
        margin: 0;
    }

    .grr-sidebar__subtitle{
        font-size: .82rem;
        color: rgba(231,237,246,.68);
        margin-top: 3px;
        line-height: 1.15;
    }

    .grr-sidebar__level{
        margin-top: 6px;
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgba(255,255,255,.88);
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.08);
    }

    .grr-sidebar__nav{
        display:flex;
        flex-direction:column;
        gap: 14px;
        padding-top: 4px;
    }

    .grr-sidebar__group{
        display:flex;
        flex-direction:column;
        gap: 6px;
    }

    .grr-sidebar__heading{
        padding: 0 10px;
        margin-bottom: 2px;
        font-size: .70rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: rgba(231,237,246,.46);
    }

    .grr-sidebar__item,
    .grr-sidebar__subitem,
    .grr-sidebar__logout{
        width: 100%;
        display:flex;
        align-items:center;
        gap: 12px;
        text-decoration:none;
        border-radius: 14px;
        transition: .18s ease;
        border: 1px solid transparent;
    }

    .grr-sidebar__item{
        min-height: 46px;
        padding: 11px 12px;
        color: rgba(231,237,246,.88);
        background: transparent;
    }

    .grr-sidebar__item:hover{
        background: rgba(255,255,255,.05);
        border-color: rgba(255,255,255,.08);
        color: #fff;
        transform: translateX(2px);
        opacity: 1;
    }

    .grr-sidebar__item.is-active{
        background:
            linear-gradient(90deg, rgba(90,162,255,.18), rgba(90,162,255,.06));
        border-color: rgba(90,162,255,.24);
        color: #fff;
        box-shadow:
            inset 3px 0 0 rgba(90,162,255,.92),
            0 10px 24px rgba(0,0,0,.18);
    }

    .grr-sidebar__subitem{
        min-height: 38px;
        padding: 8px 12px 8px 44px;
        color: rgba(231,237,246,.66);
        font-size: .90rem;
    }

    .grr-sidebar__subitem:hover{
        background: rgba(255,255,255,.04);
        color: rgba(255,255,255,.94);
        transform: translateX(2px);
        opacity: 1;
    }

    .grr-sidebar__subitem.is-active{
        background: rgba(255,255,255,.05);
        border-color: rgba(255,255,255,.07);
        color: #fff;
    }

    .grr-sidebar__icon{
        width: 20px;
        text-align:center;
        font-size: 1rem;
        color: rgba(255,255,255,.72);
        flex: 0 0 20px;
    }

    .grr-sidebar__item.is-active .grr-sidebar__icon{
        color: #9ac5ff;
    }

    .grr-sidebar__label{
        flex: 1 1 auto;
        min-width: 0;
        font-weight: 700;
        line-height: 1.15;
    }

    .grr-sidebar__hint{
        font-size: .74rem;
        color: rgba(231,237,246,.42);
        margin-left: auto;
    }

    .grr-sidebar__divider{
        height: 1px;
        margin: 2px 8px 0;
        background: rgba(255,255,255,.07);
    }

    .grr-sidebar__logoutwrap{
        margin-top: 4px;
        padding-top: 8px;
        border-top: 1px solid rgba(255,255,255,.08);
    }

    .grr-sidebar__logout{
        min-height: 46px;
        padding: 11px 12px;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.08);
        color: rgba(255,255,255,.90);
    }

    .grr-sidebar__logout:hover{
        background: rgba(220,53,69,.10);
        border-color: rgba(220,53,69,.20);
        color: #fff;
    }

    .grr-sidebar__logoutbtn{
        all: unset;
        cursor: pointer;
        width: 100%;
        display:flex;
        align-items:center;
        gap: 12px;
    }

    .grr-sidebar__foot{
        padding: 10px 10px 0;
        color: rgba(231,237,246,.42);
        font-size: .72rem;
        line-height: 1.35;
    }
</style>

<div class="grr-sidebar">
    <div class="grr-sidebar__brand">
        <div class="grr-sidebar__avatar">{{ $initials }}</div>

        <div class="grr-sidebar__brandtext">
            <div class="grr-sidebar__title">GRR • PRF</div>
            <div class="grr-sidebar__subtitle">Sistema Operacional Interno</div>
            <div class="grr-sidebar__level">
                <i class="bi bi-shield-check"></i>
                Nível {{ $nivel }}
            </div>
        </div>
    </div>

    <nav class="grr-sidebar__nav">

        <div class="grr-sidebar__group">
            <div class="grr-sidebar__heading">Operacional</div>

            @if($hasDashboard)
                <a href="{{ route('dashboard') }}"
                   class="grr-sidebar__item {{ $isActive('dashboard') ? 'is-active' : '' }}">
                    <i class="bi bi-grid-1x2-fill grr-sidebar__icon"></i>
                    <span class="grr-sidebar__label">Dashboard</span>
                </a>
            @endif

            @if($hasRanking)
                <a href="{{ route('ranking.index') }}"
                   class="grr-sidebar__item {{ $isActive('ranking.*') ? 'is-active' : '' }}">
                    <i class="bi bi-trophy-fill grr-sidebar__icon"></i>
                    <span class="grr-sidebar__label">Ranking Oficial</span>
                </a>
            @endif

            @if($hasDossie)
                <a href="{{ route('dossie.index') }}"
                   class="grr-sidebar__item {{ $isActive('dossie.*') ? 'is-active' : '' }}">
                    <i class="bi bi-folder2-open grr-sidebar__icon"></i>
                    <span class="grr-sidebar__label">Dossiê</span>
                </a>
            @endif
        </div>

        @if($canRelatorios && $hasRelIndex)
            <div class="grr-sidebar__group">
                <div class="grr-sidebar__heading">Relatórios</div>

                <a href="{{ route('relatorios.index') }}"
                   class="grr-sidebar__item {{ $isActive('relatorios.*') ? 'is-active' : '' }}">
                    <i class="bi bi-file-earmark-text-fill grr-sidebar__icon"></i>
                    <span class="grr-sidebar__label">Relatórios</span>
                </a>
            </div>
        @endif

        <div class="grr-sidebar__group">
            <div class="grr-sidebar__heading">Regulamento</div>

            @if($hasRegIndex)
                <a href="{{ route('regulamento.index') }}"
                   class="grr-sidebar__item {{ $isActive('regulamento.index') ? 'is-active' : '' }}">
                    <i class="bi bi-book-half grr-sidebar__icon"></i>
                    <span class="grr-sidebar__label">Visão Geral</span>
                </a>
            @endif

            @if($hasRegInstr)
                <a href="{{ route('regulamento.instrucoes') }}"
                   class="grr-sidebar__subitem {{ $isActive('regulamento.instrucoes') ? 'is-active' : '' }}">
                    Instruções Iniciais
                </a>
            @endif

            @if($hasRegFard)
                <a href="{{ route('regulamento.fardamento') }}"
                   class="grr-sidebar__subitem {{ $isActive('regulamento.fardamento') ? 'is-active' : '' }}">
                    Fardamento
                </a>
            @endif

            @if($hasRegInterno)
                <a href="{{ route('regulamento.interno') }}"
                   class="grr-sidebar__subitem {{ $isActive('regulamento.interno') ? 'is-active' : '' }}">
                    Regulamento Interno
                </a>
            @endif
        </div>

        @if($showSuporteGroup)
            <div class="grr-sidebar__group">
                <div class="grr-sidebar__heading">Suporte</div>

                @if($hasTicketsIndex)
                    <a href="{{ route('tickets.index') }}"
                       class="grr-sidebar__item {{ $isActive('tickets.*') ? 'is-active' : '' }}">
                        <i class="bi bi-life-preserver grr-sidebar__icon"></i>
                        <span class="grr-sidebar__label">Meus Tickets</span>
                    </a>
                @endif

                @if($hasTicketsCreate)
                    <a href="{{ route('tickets.create') }}"
                       class="grr-sidebar__subitem {{ $isActive('tickets.create') ? 'is-active' : '' }}">
                        Abrir Ticket
                    </a>
                @endif

                @if($hasSuporteFaq)
                    <a href="{{ route('suporte.faq') }}"
                       class="grr-sidebar__subitem {{ $isActive('suporte.faq') ? 'is-active' : '' }}">
                        Base de conhecimento
                    </a>
                @endif
            </div>
        @endif

        @if($showAdminGroup)
            <div class="grr-sidebar__group">
                <div class="grr-sidebar__heading">Administrativo</div>

                @if($hasAuditoria)
                    <a href="{{ route('auditoria.index') }}"
                       class="grr-sidebar__item {{ $isActive('auditoria.*') ? 'is-active' : '' }}">
                        <i class="bi bi-shield-check grr-sidebar__icon"></i>
                        <span class="grr-sidebar__label">Auditoria</span>
                    </a>
                @endif

                @if($hasEfetivo)
                    <a href="{{ route('efetivo.index') }}"
                       class="grr-sidebar__item {{ $isActive('efetivo.*') ? 'is-active' : '' }}">
                        <i class="bi bi-people-fill grr-sidebar__icon"></i>
                        <span class="grr-sidebar__label">Efetivo</span>
                    </a>
                @endif

                @if($hasAtend)
                    <a href="{{ route('atendimentos.index') }}"
                       class="grr-sidebar__item {{ $isActive('atendimentos.*') ? 'is-active' : '' }}">
                        <i class="bi bi-headset grr-sidebar__icon"></i>
                        <span class="grr-sidebar__label">Canais de Atendimento</span>
                    </a>
                @endif

                @if($hasPreInscricoes)
                    <a href="{{ route('admin.preinscricoes.index') }}"
                       class="grr-sidebar__item {{ $isActive('admin.preinscricoes.*') ? 'is-active' : '' }}">
                        <i class="bi bi-person-vcard-fill grr-sidebar__icon"></i>
                        <span class="grr-sidebar__label">Pré-inscrições</span>
                    </a>
                @endif

                @if($hasAdminTickets)
                    <a href="{{ route('admin.tickets.index') }}"
                       class="grr-sidebar__item {{ $isActive('admin.tickets.*') ? 'is-active' : '' }}">
                        <i class="bi bi-ticket-detailed-fill grr-sidebar__icon"></i>
                        <span class="grr-sidebar__label">Tickets Admin</span>
                    </a>
                @endif

                @if($hasSolicAcesso)
                    <a href="{{ route('admin.solicitacoes.index') }}"
                       class="grr-sidebar__item {{ $isActive('admin.solicitacoes.*') ? 'is-active' : '' }}">
                        <i class="bi bi-person-lock grr-sidebar__icon"></i>
                        <span class="grr-sidebar__label">Solicitações de Acesso</span>
                    </a>
                @endif
            </div>
        @endif

        @if($hasRhIndex)
            <div class="grr-sidebar__group">
                <div class="grr-sidebar__heading">Recursos Humanos</div>

                <a href="{{ route('rh.index') }}"
                   class="grr-sidebar__item {{ $isActive('rh.*') ? 'is-active' : '' }}">
                    <i class="bi bi-person-badge-fill grr-sidebar__icon"></i>
                    <span class="grr-sidebar__label">RH • Recursos Humanos</span>
                </a>
            </div>
        @endif

        <div class="grr-sidebar__group">
            <div class="grr-sidebar__heading">Conta</div>

            @if($hasProfile)
                <a href="{{ route('profile.edit') }}"
                   class="grr-sidebar__item {{ $isActive('profile.*') ? 'is-active' : '' }}">
                    <i class="bi bi-person-circle grr-sidebar__icon"></i>
                    <span class="grr-sidebar__label">Meu Perfil</span>
                </a>
            @endif

            @if($hasLogout)
                <div class="grr-sidebar__logoutwrap">
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <div class="grr-sidebar__logout">
                            <button type="submit" class="grr-sidebar__logoutbtn">
                                <i class="bi bi-box-arrow-right grr-sidebar__icon"></i>
                                <span class="grr-sidebar__label">Sair do sistema</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <div class="grr-sidebar__foot">
            Painel institucional GRR 3.0<br>
            Ambiente interno com controle de acesso e navegação por nível.
        </div>
    </nav>
</div>