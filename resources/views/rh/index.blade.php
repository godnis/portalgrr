@extends('layouts.app')

@section('content')
@php
    use App\Http\Controllers\RhController;

    $nivel = (int)(auth()->user()->nivel ?? 0);

    // ✅ VISUALIZAÇÃO (consulta) — TODO MUNDO LOGADO PODE VER
    $canViewHierarquia    = auth()->check();
    $canViewControleSaida = auth()->check();

    // ✅ EDIÇÃO — só quem tiver permissão real (ou nível 9+)
    $canEditHierarquia    = RhController::canEditSection('hierarquia');
    $canEditControleSaida = RhController::canEditSection('controle_saida');
@endphp

<style>
/* =========================================================
   RH — PATCH DARK ONLY (igual Efetivo/Auditoria)
   No LIGHT, fica Bootstrap/estilo atual.
   ========================================================= */
.rh-wrap{ max-width: 1280px; }

/* DARK: hero e cards viram escuros (sem perder legibilidade) */
body.theme-dark .rh-wrap .rh-hero,
html.theme-dark .rh-wrap .rh-hero,
[data-theme="dark"] .rh-wrap .rh-hero,
body.dark .rh-wrap .rh-hero,
html.dark .rh-wrap .rh-hero{
    background: rgba(2,6,23,.55) !important;
    border: 1px solid rgba(148,163,184,.18) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,.35) !important;
}

body.theme-dark .rh-wrap .rh-hero__bg,
html.theme-dark .rh-wrap .rh-hero__bg,
[data-theme="dark"] .rh-wrap .rh-hero__bg,
body.dark .rh-wrap .rh-hero__bg,
html.dark .rh-wrap .rh-hero__bg{
    background:
        radial-gradient(900px 240px at 10% 10%, rgba(59,130,246,.22), transparent 60%),
        radial-gradient(700px 220px at 95% 30%, rgba(245,158,11,.18), transparent 55%),
        linear-gradient(180deg, rgba(15,23,42,.35), rgba(2,6,23,0));
}

body.theme-dark .rh-wrap .rh-hero__content,
html.theme-dark .rh-wrap .rh-hero__content,
[data-theme="dark"] .rh-wrap .rh-hero__content,
body.dark .rh-wrap .rh-hero__content,
html.dark .rh-wrap .rh-hero__content{
    border-top: 3px solid rgba(245,158,11,.55) !important;
}

body.theme-dark .rh-wrap .rh-kicker,
html.theme-dark .rh-wrap .rh-kicker,
[data-theme="dark"] .rh-wrap .rh-kicker,
body.dark .rh-wrap .rh-kicker,
html.dark .rh-wrap .rh-kicker{
    color: rgba(226,232,240,.72) !important;
}

body.theme-dark .rh-wrap .rh-title,
html.theme-dark .rh-wrap .rh-title,
[data-theme="dark"] .rh-wrap .rh-title,
body.dark .rh-wrap .rh-title,
html.dark .rh-wrap .rh-title{
    color: rgba(226,232,240,.95) !important;
}

body.theme-dark .rh-wrap .rh-sub,
html.theme-dark .rh-wrap .rh-sub,
[data-theme="dark"] .rh-wrap .rh-sub,
body.dark .rh-wrap .rh-sub,
html.dark .rh-wrap .rh-sub{
    color: rgba(226,232,240,.70) !important;
}

body.theme-dark .rh-wrap .rh-dot,
html.theme-dark .rh-wrap .rh-dot,
[data-theme="dark"] .rh-wrap .rh-dot,
body.dark .rh-wrap .rh-dot,
html.dark .rh-wrap .rh-dot{
    background: rgba(226,232,240,.35) !important;
}

/* Badges do hero no dark */
body.theme-dark .rh-wrap .rh-badge,
html.theme-dark .rh-wrap .rh-badge,
[data-theme="dark"] .rh-wrap .rh-badge,
body.dark .rh-wrap .rh-badge,
html.dark .rh-wrap .rh-badge{
    background: rgba(15,23,42,.55) !important;
    border: 1px solid rgba(148,163,184,.18) !important;
    color: rgba(226,232,240,.78) !important;
}

body.theme-dark .rh-wrap .rh-badge--soft,
html.theme-dark .rh-wrap .rh-badge--soft,
[data-theme="dark"] .rh-wrap .rh-badge--soft,
body.dark .rh-wrap .rh-badge--soft,
html.dark .rh-wrap .rh-badge--soft{
    background: rgba(59,130,246,.12) !important;
    border-color: rgba(59,130,246,.22) !important;
    color: rgba(191,219,254,.95) !important;
}

/* Botão do hero */
body.theme-dark .rh-wrap .rh-btn,
html.theme-dark .rh-wrap .rh-btn,
[data-theme="dark"] .rh-wrap .rh-btn,
body.dark .rh-wrap .rh-btn,
html.dark .rh-wrap .rh-btn{
    border-radius: 12px !important;
    font-weight: 900 !important;
    box-shadow: 0 10px 22px rgba(59,130,246,.18) !important;
}

/* Cards no dark */
body.theme-dark .rh-wrap .rh-card,
html.theme-dark .rh-wrap .rh-card,
[data-theme="dark"] .rh-wrap .rh-card,
body.dark .rh-wrap .rh-card,
html.dark .rh-wrap .rh-card{
    background: rgba(2,6,23,.55) !important;
    border: 1px solid rgba(148,163,184,.18) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,.32) !important;
}

body.theme-dark .rh-wrap .rh-card__title,
html.theme-dark .rh-wrap .rh-card__title,
[data-theme="dark"] .rh-wrap .rh-card__title,
body.dark .rh-wrap .rh-card__title,
html.dark .rh-wrap .rh-card__title{
    color: rgba(226,232,240,.95) !important;
}

body.theme-dark .rh-wrap .rh-card__muted,
html.theme-dark .rh-wrap .rh-card__muted,
[data-theme="dark"] .rh-wrap .rh-card__muted,
body.dark .rh-wrap .rh-card__muted,
html.dark .rh-wrap .rh-card__muted{
    color: rgba(226,232,240,.70) !important;
}

body.theme-dark .rh-wrap .rh-card__body,
html.theme-dark .rh-wrap .rh-card__body,
[data-theme="dark"] .rh-wrap .rh-card__body,
body.dark .rh-wrap .rh-card__body,
html.dark .rh-wrap .rh-card__body{
    color: rgba(226,232,240,.78) !important;
}

body.theme-dark .rh-wrap .rh-card__foot,
html.theme-dark .rh-wrap .rh-card__foot,
[data-theme="dark"] .rh-wrap .rh-card__foot,
body.dark .rh-wrap .rh-card__foot,
html.dark .rh-wrap .rh-card__foot{
    background: rgba(15,23,42,.35) !important;
    border-top: 1px solid rgba(148,163,184,.14) !important;
    color: rgba(226,232,240,.80) !important;
}

/* Ícone do card */
body.theme-dark .rh-wrap .rh-ico,
html.theme-dark .rh-wrap .rh-ico,
[data-theme="dark"] .rh-wrap .rh-ico,
body.dark .rh-wrap .rh-ico,
html.dark .rh-wrap .rh-ico{
    background: rgba(59,130,246,.14) !important;
    box-shadow: inset 0 0 0 1px rgba(59,130,246,.18) !important;
}

/* Hover no dark */
body.theme-dark .rh-wrap .rh-card:hover,
html.theme-dark .rh-wrap .rh-card:hover,
[data-theme="dark"] .rh-wrap .rh-card:hover,
body.dark .rh-wrap .rh-card:hover,
html.dark .rh-wrap .rh-card:hover{
    transform: translateY(-4px);
    box-shadow: 0 18px 44px rgba(0,0,0,.45) !important;
    border-color: rgba(59,130,246,.32) !important;
}

/* Lock (somente leitura) no dark */
body.theme-dark .rh-wrap .rh-card--locked .rh-ico,
html.theme-dark .rh-wrap .rh-card--locked .rh-ico,
[data-theme="dark"] .rh-wrap .rh-card--locked .rh-ico,
body.dark .rh-wrap .rh-card--locked .rh-ico,
html.dark .rh-wrap .rh-card--locked .rh-ico{
    background: rgba(148,163,184,.10) !important;
    box-shadow: inset 0 0 0 1px rgba(148,163,184,.18) !important;
}

/* setinhas e arrows no dark */
body.theme-dark .rh-wrap .rh-arrow,
html.theme-dark .rh-wrap .rh-arrow,
[data-theme="dark"] .rh-wrap .rh-arrow,
body.dark .rh-wrap .rh-arrow,
html.dark .rh-wrap .rh-arrow{
    color: rgba(226,232,240,.35) !important;
}
body.theme-dark .rh-wrap .rh-card:hover .rh-arrow,
html.theme-dark .rh-wrap .rh-card:hover .rh-arrow,
[data-theme="dark"] .rh-wrap .rh-card:hover .rh-arrow,
body.dark .rh-wrap .rh-card:hover .rh-arrow,
html.dark .rh-wrap .rh-card:hover .rh-arrow{
    color: rgba(191,219,254,.85) !important;
}

body.theme-dark .rh-wrap .rh-foot-arrow,
html.theme-dark .rh-wrap .rh-foot-arrow,
[data-theme="dark"] .rh-wrap .rh-foot-arrow,
body.dark .rh-wrap .rh-foot-arrow,
html.dark .rh-wrap .rh-foot-arrow{
    color: rgba(191,219,254,.85) !important;
}

/* Responsivo */
@media (max-width: 576px){
    .rh-title{ font-size: 1.35rem; }
    .rh-hero__content{ padding: 18px; }
}
</style>

<div class="container-fluid py-4 rh-wrap">

    {{-- ================= HERO ================= --}}
    <div class="rh-hero mb-4">
        <div class="rh-hero__bg"></div>

        <div class="rh-hero__content">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                <div>
                    <div class="rh-kicker">GRR • PRF — Recursos Humanos</div>
                    <h1 class="rh-title">RH — Recursos Humanos</h1>

                    <div class="rh-sub">
                        Módulo institucional de gestão do efetivo
                        <span class="rh-dot"></span>
                        consulta liberada
                        <span class="rh-dot"></span>
                        edição por permissão
                    </div>

                    <div class="rh-badges mt-2">
                        <span class="rh-badge">
                            <span class="rh-badge__dot"></span>
                            auditoria ativa
                        </span>
                        <span class="rh-badge rh-badge--soft">
                            acesso controlado
                        </span>
                    </div>
                </div>

                @if($nivel >= 9)
                    <div>
                        <a href="{{ route('rh.permissions') }}" class="btn btn-primary rh-btn">
                            Gerenciar Permissões
                            <span class="small opacity-75">(nível 9+)</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= GRID ================= --}}
    <div class="row g-4">

        {{-- ================= HIERARQUIA ================= --}}
        <div class="col-xl-4 col-md-6">
            @if($canViewHierarquia)
                <a href="{{ route('rh.hierarquia') }}" class="rh-link">
            @else
                <div class="rh-link rh-disabled" title="Acesso restrito">
            @endif

                <div class="rh-card {{ !$canEditHierarquia ? 'rh-card--locked' : '' }}">
                    <div class="rh-card__top">
                        <div class="rh-ico">🏛️</div>
                        <div class="flex-grow-1">
                            <div class="rh-card__title">Hierarquia</div>
                            <div class="rh-card__muted">Estrutura organizacional e cargos</div>
                        </div>
                        <div class="rh-arrow">→</div>
                    </div>
                    <div class="rh-card__body">
                        Visualização completa da cadeia hierárquica, funções e estrutura operacional.
                    </div>
                    <div class="rh-card__foot">
                        <span>
                            {{ $canEditHierarquia ? 'Acessar módulo' : 'Acessar (somente leitura)' }}
                        </span>
                        <span class="rh-foot-arrow">→</span>
                    </div>
                </div>

            @if($canViewHierarquia)
                </a>
            @else
                </div>
            @endif
        </div>

        {{-- ================= CONTROLE DE SAÍDA ================= --}}
        <div class="col-xl-4 col-md-6">
            @if($canViewControleSaida)
                <a href="{{ route('rh.controle_saida') }}" class="rh-link">
            @else
                <div class="rh-link rh-disabled" title="Acesso restrito">
            @endif

                <div class="rh-card {{ !$canEditControleSaida ? 'rh-card--locked' : '' }}">
                    <div class="rh-card__top">
                        <div class="rh-ico">🕒</div>
                        <div class="flex-grow-1">
                            <div class="rh-card__title">Controle de Saída</div>
                            <div class="rh-card__muted">Registros e controle operacional</div>
                        </div>
                        <div class="rh-arrow">→</div>
                    </div>
                    <div class="rh-card__body">
                        Monitoramento e histórico de saídas/retornos com trilha operacional.
                    </div>
                    <div class="rh-card__foot">
                        <span>
                            {{ $canEditControleSaida ? 'Acessar módulo' : 'Acessar (somente leitura)' }}
                        </span>
                        <span class="rh-foot-arrow">→</span>
                    </div>
                </div>

            @if($canViewControleSaida)
                </a>
            @else
                </div>
            @endif
        </div>

        {{-- ================= ESTATÍSTICA ================= --}}
        <div class="col-xl-4 col-md-6">
            <a href="{{ route('rh.estatistica_efetivo') }}" class="rh-link">
                <div class="rh-card">
                    <div class="rh-card__top">
                        <div class="rh-ico">📊</div>
                        <div class="flex-grow-1">
                            <div class="rh-card__title">Estatística do Efetivo</div>
                            <div class="rh-card__muted">Indicadores e totais</div>
                        </div>
                        <div class="rh-arrow">→</div>
                    </div>
                    <div class="rh-card__body">
                        Painéis consolidados do efetivo ativo, histórico e distribuição.
                    </div>
                    <div class="rh-card__foot">
                        <span>Acessar módulo</span>
                        <span class="rh-foot-arrow">→</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- ================= INSTRUTORES ================= --}}
        <div class="col-xl-6 col-md-6">
            <a href="{{ route('rh.instrutores') }}" class="rh-link">
                <div class="rh-card">
                    <div class="rh-card__top">
                        <div class="rh-ico">🎓</div>
                        <div class="flex-grow-1">
                            <div class="rh-card__title">Instrutores</div>
                            <div class="rh-card__muted">Gestão de instrutores</div>
                        </div>
                        <div class="rh-arrow">→</div>
                    </div>
                    <div class="rh-card__body">
                        Cadastro, controle, atribuições e alocação de instrutores por função.
                    </div>
                    <div class="rh-card__foot">
                        <span>Acessar módulo</span>
                        <span class="rh-foot-arrow">→</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- ================= EQUIPE ================= --}}
        <div class="col-xl-6 col-md-6">
            <a href="{{ route('rh.equipe') }}" class="rh-link">
                <div class="rh-card">
                    <div class="rh-card__top">
                        <div class="rh-ico">👥</div>
                        <div class="flex-grow-1">
                            <div class="rh-card__title">Equipe</div>
                            <div class="rh-card__muted">Equipe e funções</div>
                        </div>
                        <div class="rh-arrow">→</div>
                    </div>
                    <div class="rh-card__body">
                        Organização das equipes, designações funcionais e composição operacional.
                    </div>
                    <div class="rh-card__foot">
                        <span>Acessar módulo</span>
                        <span class="rh-foot-arrow">→</span>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>

{{-- ✅ Estilos base (LIGHT / padrão) - mantidos --}}
<style>
    /* ====== HERO ====== */
    .rh-hero{
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,.08);
        box-shadow: 0 12px 30px rgba(2,6,23,.06);
        background: #fff;
    }
    .rh-hero__bg{
        position:absolute; inset:0;
        background:
            radial-gradient(900px 240px at 10% 10%, rgba(13,110,253,.12), transparent 60%),
            radial-gradient(700px 220px at 95% 30%, rgba(255,193,7,.12), transparent 55%),
            linear-gradient(180deg, rgba(2,6,23,.02), rgba(2,6,23,0));
    }
    .rh-hero__content{
        position:relative;
        padding: 22px 22px;
        border-top: 3px solid rgba(255,193,7,.45);
    }
    .rh-kicker{
        letter-spacing:.08em;
        text-transform:uppercase;
        font-size: .75rem;
        color: rgba(2,6,23,.55);
        font-weight: 700;
    }
    .rh-title{
        margin: 2px 0 4px;
        font-size: 1.65rem;
        font-weight: 900;
        color: rgba(2,6,23,.95);
    }
    .rh-sub{
        color: rgba(2,6,23,.62);
        font-size: .95rem;
    }
    .rh-dot{
        display:inline-block;
        width:4px; height:4px;
        border-radius:999px;
        background: rgba(2,6,23,.35);
        margin: 0 8px;
        transform: translateY(-2px);
    }
    .rh-badges{ display:flex; gap:10px; flex-wrap:wrap; }
    .rh-badge{
        display:inline-flex; align-items:center; gap:8px;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid rgba(0,0,0,.08);
        background: rgba(255,255,255,.75);
        font-size: .82rem;
        color: rgba(2,6,23,.72);
        font-weight: 600;
    }
    .rh-badge__dot{
        width:8px; height:8px; border-radius:999px;
        background: rgba(25,135,84,.9);
        box-shadow: 0 0 0 3px rgba(25,135,84,.15);
    }
    .rh-badge--soft{
        background: rgba(13,110,253,.06);
        border-color: rgba(13,110,253,.15);
        color: rgba(13,110,253,.9);
    }
    .rh-btn{
        border-radius: 12px;
        padding: 10px 14px;
        font-weight: 700;
        box-shadow: 0 10px 22px rgba(13,110,253,.18);
    }

    /* ====== CARDS ====== */
    .rh-link{ text-decoration:none; display:block; }
    .rh-card{
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,.08);
        background: #fff;
        overflow:hidden;
        box-shadow: 0 10px 26px rgba(2,6,23,.05);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }
    .rh-card:hover{
        transform: translateY(-4px);
        box-shadow: 0 18px 44px rgba(2,6,23,.10);
        border-color: rgba(13,110,253,.28);
    }
    .rh-card__top{
        display:flex;
        align-items:center;
        gap:12px;
        padding: 16px 16px 10px;
    }
    .rh-ico{
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(13,110,253,.08);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size: 20px;
        box-shadow: inset 0 0 0 1px rgba(13,110,253,.10);
    }
    .rh-card__title{
        font-weight: 800;
        color: rgba(2,6,23,.92);
        font-size: 1rem;
    }
    .rh-card__muted{
        color: rgba(2,6,23,.55);
        font-size: .82rem;
        margin-top: 1px;
    }
    .rh-arrow{
        font-weight: 900;
        color: rgba(2,6,23,.25);
        transition: transform .18s ease, color .18s ease;
        margin-left: 6px;
    }
    .rh-card:hover .rh-arrow{
        transform: translateX(4px);
        color: rgba(13,110,253,.65);
    }
    .rh-card__body{
        padding: 0 16px 14px;
        color: rgba(2,6,23,.70);
        font-size: .92rem;
        line-height: 1.35rem;
        min-height: 44px;
    }
    .rh-card__foot{
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding: 12px 16px;
        border-top: 1px solid rgba(0,0,0,.06);
        background: rgba(2,6,23,.015);
        color: rgba(2,6,23,.70);
        font-weight: 700;
        font-size: .88rem;
    }
    .rh-foot-arrow{
        color: rgba(13,110,253,.75);
        transition: transform .18s ease;
    }
    .rh-card:hover .rh-foot-arrow{
        transform: translateX(4px);
    }

    /* ✅ visual “somente leitura” */
    .rh-card--locked{ border-color: rgba(2,6,23,.10); }
    .rh-card--locked .rh-ico{
        background: rgba(148,163,184,.10);
        box-shadow: inset 0 0 0 1px rgba(148,163,184,.18);
    }
</style>
@endsection