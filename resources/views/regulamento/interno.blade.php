@extends('layouts.app')

@section('content')
@php
    $userNivel = (int) (auth()->user()->nivel ?? 0);
    $canEditManual = auth()->check() && $userNivel >= 9;

    $manualTitle = $manual->title ?: 'Manual Interno — Grupo de Resposta Rápida (GRR)';
    $manualSubtitle = $manual->subtitle ?: 'Documento oficial com normas, procedimentos e condutas obrigatórias aplicáveis aos integrantes da corporação no Brasil Capital Roleplay.';
    $manualKicker = $manual->kicker ?: 'Uso interno institucional';

    $statusLabel = $manual->status_label ?: 'Status';
    $statusValue = $manual->status_value ?: 'Interno';

    $environmentLabel = $manual->environment_label ?: 'Ambiente';
    $environmentValue = $manual->environment_value ?: 'Brasil Capital RP';

    $alertTitle = $manual->alert_title ?: 'Acesso restrito / uso interno';
    $alertText = $manual->alert_text ?: 'Este manual possui caráter interno e normativo. O descumprimento das regras, diretrizes e procedimentos previstos poderá gerar apuração administrativa, medidas disciplinares e demais providências cabíveis.';

    $summaryCards = [
        [
            'label' => $manual->summary_1_label ?: 'Finalidade',
            'value' => $manual->summary_1_value ?: 'Normatizar',
            'sub'   => $manual->summary_1_sub ?: 'Padronizar condutas, operações e procedimentos administrativos da corporação.',
        ],
        [
            'label' => $manual->summary_2_label ?: 'Estrutura',
            'value' => $manual->summary_2_value ?: (($manual->sections?->count() ?? 0) . ' capítulos'),
            'sub'   => $manual->summary_2_sub ?: 'Organização contínua em capítulos com artigos visíveis para consulta direta.',
        ],
        [
            'label' => $manual->summary_3_label ?: 'Obrigatoriedade',
            'value' => $manual->summary_3_value ?: 'Alta',
            'sub'   => $manual->summary_3_sub ?: 'Todo integrante deve conhecer e observar integralmente este manual.',
        ],
    ];
@endphp

<div class="container py-4">
    <div class="grr-doc-layout">

        <div class="grr-doc-main">
            <div class="grr-doc-page">

                <section class="grr-doc-hero mb-4">
                    <div class="grr-doc-hero__glow grr-doc-hero__glow--blue"></div>
                    <div class="grr-doc-hero__glow grr-doc-hero__glow--gold"></div>

                    <div class="grr-doc-hero__inner">
                        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                            <div class="d-flex align-items-start gap-3">
                                <div class="grr-doc-hero__icon">📘</div>

                                <div>
                                    <div class="grr-doc-kicker mb-2">{{ $manualKicker }}</div>

                                    <h3 class="fw-black mb-2 grr-doc-hero__title">
                                        {{ $manualTitle }}
                                    </h3>

                                    <p class="grr-doc-hero__sub mb-0">
                                        {{ $manualSubtitle }}
                                    </p>

                                    <div class="d-flex flex-wrap gap-2 mt-3">
                                        <span class="grr-doc-pill grr-doc-pill--blue">Documento oficial</span>
                                        <span class="grr-doc-pill grr-doc-pill--soft">Uso restrito</span>
                                        <span class="grr-doc-pill grr-doc-pill--soft">Leitura obrigatória</span>

                                        @if($canEditManual)
                                            <a href="{{ route('admin.manual.edit') }}"
                                               class="grr-doc-pill grr-doc-pill--edit text-decoration-none">
                                                ✏️ Editar manual
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="grr-doc-hero__aside">
                                <div class="grr-doc-hero__asideBox">
                                    <span class="grr-doc-hero__asideLabel">{{ $statusLabel }}</span>
                                    <strong class="grr-doc-hero__asideValue">{{ $statusValue }}</strong>
                                </div>

                                <div class="grr-doc-hero__asideBox">
                                    <span class="grr-doc-hero__asideLabel">{{ $environmentLabel }}</span>
                                    <strong class="grr-doc-hero__asideValue">{{ $environmentValue }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grr-doc-alert mb-4">
                    <div class="grr-doc-alert__icon">⚠️</div>
                    <div class="grr-doc-alert__content">
                        <div class="grr-doc-alert__title">{{ $alertTitle }}</div>
                        <div class="grr-doc-alert__text">
                            {{ $alertText }}
                        </div>
                    </div>
                </section>

                <section class="row g-3 mb-4">
                    @foreach($summaryCards as $card)
                        <div class="col-md-4">
                            <div class="grr-doc-mini">
                                <div class="grr-doc-mini__label">{{ $card['label'] }}</div>
                                <div class="grr-doc-mini__value">{{ $card['value'] }}</div>
                                <div class="grr-doc-mini__sub">{{ $card['sub'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </section>

                @if($manual->sections->count())
                    <section class="grr-doc-summary mb-4">
                        <div class="grr-doc-summary__head">
                            <div class="grr-doc-summary__title">📑 Estrutura do manual</div>
                            <div class="grr-doc-summary__sub">
                                Navegação rápida pelos capítulos do documento.
                            </div>
                        </div>

                        <div class="row g-2 mt-1">
                            @foreach($manual->sections as $section)
                                @php
                                    $anchor = $section->anchor ?: 'cap' . $section->id;
                                    $code = $section->code ?: ('Cap. ' . ($loop->iteration));
                                @endphp

                                <div class="col-md-6 col-xl-4">
                                    <a href="#{{ $anchor }}" class="grr-doc-summary__item">
                                        {{ $code }} — {{ $section->title }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <article class="grr-doc-body">
                    @forelse($manual->sections as $section)
                        @php
                            $anchor = $section->anchor ?: 'cap' . $section->id;
                            $code = $section->code ?: ('Cap. ' . ($loop->iteration));
                        @endphp

                        <section class="grr-doc-section" id="{{ $anchor }}" data-nav="{{ $anchor }}">
                            <div class="grr-doc-section__header">
                                <span class="grr-doc-section__badge">{{ $code }}</span>

                                <div>
                                    <h4 class="grr-doc-section__title">{{ $section->title }}</h4>

                                    @if(!empty($section->subtitle))
                                        <div class="grr-doc-section__sub">{{ $section->subtitle }}</div>
                                    @endif
                                </div>
                            </div>

                            @forelse($section->articles as $article)
                                <div class="grr-doc-article {{ $loop->last ? 'grr-doc-article--last' : '' }}">
                                    <div class="grr-doc-article__title">
                                        {{ $article->article_number ?: $article->title ?: 'Artigo' }}
                                    </div>

                                    <div class="grr-doc-article__text">
                                        {!! nl2br(e($article->body)) !!}
                                    </div>
                                </div>
                            @empty
                                <div class="grr-doc-empty">
                                    Nenhum artigo cadastrado neste capítulo.
                                </div>
                            @endforelse
                        </section>
                    @empty
                        <div class="grr-doc-empty grr-doc-empty--big">
                            Este manual ainda não possui capítulos cadastrados.
                        </div>
                    @endforelse
                </article>
            </div>
        </div>

        @if($manual->sections->count())
            <aside class="grr-doc-side">
                <div class="grr-doc-side__card">
                    <div class="grr-doc-side__title">Navegação rápida</div>
                    <div class="grr-doc-side__sub">Clique para ir até o capítulo</div>

                    <nav class="grr-doc-side__nav">
                        @foreach($manual->sections as $section)
                            @php
                                $anchor = $section->anchor ?: 'cap' . $section->id;
                                $code = $section->code ?: ('Cap. ' . ($loop->iteration));
                            @endphp

                            <a href="#{{ $anchor }}"
                               class="grr-doc-side__link {{ $loop->first ? 'is-active' : '' }}"
                               data-nav-link="{{ $anchor }}">
                                {{ $code }} — {{ $section->title }}
                            </a>
                        @endforeach
                    </nav>

                    @if($canEditManual)
                        <div class="grr-doc-side__footer">
                            <a href="{{ route('admin.manual.edit') }}" class="grr-doc-side__editBtn">
                                ✏️ Abrir editor
                            </a>
                        </div>
                    @endif
                </div>
            </aside>
        @endif

    </div>
</div>

<style>
    html{
        scroll-behavior: smooth;
    }

    .container,
    .grr-doc-layout,
    .grr-doc-main,
    .grr-doc-page{
        overflow: visible !important;
    }

    .grr-doc-layout{
        display: grid;
        grid-template-columns: minmax(0, 1fr) 290px;
        gap: 20px;
        align-items: start;
    }

    .grr-doc-main{
        min-width: 0;
    }

    .grr-doc-side{
        position: relative;
        align-self: start;
        height: max-content;
        overflow: visible !important;
    }

    .grr-doc-side__card{
        position: sticky;
        top: 24px;
        max-height: calc(100vh - 48px);
        overflow-y: auto;
        overflow-x: hidden;
        background: linear-gradient(180deg, rgba(8,12,19,.96), rgba(11,18,32,.98));
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 20px;
        padding: 16px;
        box-shadow: 0 18px 45px rgba(0,0,0,.28);
    }

    .grr-doc-side__card::-webkit-scrollbar{
        width: 8px;
    }

    .grr-doc-side__card::-webkit-scrollbar-track{
        background: transparent;
    }

    .grr-doc-side__card::-webkit-scrollbar-thumb{
        background: rgba(148,163,184,.22);
        border-radius: 999px;
    }

    .grr-doc-side__card::-webkit-scrollbar-thumb:hover{
        background: rgba(148,163,184,.34);
    }

    .grr-doc-side__title{
        color: #f8fbff;
        font-weight: 900;
        margin-bottom: 4px;
    }

    .grr-doc-side__sub{
        color: rgba(203,213,225,.70);
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .grr-doc-side__nav{
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .grr-doc-side__link{
        display: block;
        text-decoration: none;
        color: rgba(226,232,240,.86);
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.06);
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.35;
        transition: .18s ease;
        position: relative;
    }

    .grr-doc-side__link:hover{
        color: #fff;
        background: rgba(59,130,246,.10);
        border-color: rgba(59,130,246,.22);
        transform: translateX(-2px);
    }

    .grr-doc-side__link.is-active{
        color: #fff !important;
        background: linear-gradient(180deg, rgba(59,130,246,.18), rgba(37,99,235,.10));
        border-color: rgba(96,165,250,.34);
        box-shadow: 0 10px 24px rgba(37,99,235,.16), inset 0 1px 0 rgba(255,255,255,.05);
        transform: translateX(-4px);
    }

    .grr-doc-side__link.is-active::before{
        content: "";
        position: absolute;
        left: -1px;
        top: 8px;
        bottom: 8px;
        width: 3px;
        border-radius: 999px;
        background: linear-gradient(180deg, #60a5fa, #3b82f6);
        box-shadow: 0 0 12px rgba(59,130,246,.45);
    }

    .grr-doc-side__footer{
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid rgba(255,255,255,.08);
    }

    .grr-doc-side__editBtn{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        text-decoration: none;
        color: #fff;
        font-size: 12px;
        font-weight: 900;
        padding: 10px 12px;
        border-radius: 12px;
        background: linear-gradient(180deg, rgba(245,158,11,.24), rgba(217,119,6,.18));
        border: 1px solid rgba(245,158,11,.30);
        transition: .18s ease;
    }

    .grr-doc-side__editBtn:hover{
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(245,158,11,.16);
    }

    .grr-doc-page{
        background:
            radial-gradient(circle at top left, rgba(59,130,246,.08), transparent 26%),
            radial-gradient(circle at top right, rgba(245,158,11,.06), transparent 24%),
            linear-gradient(180deg, #0b1220 0%, #0f172a 100%) !important;
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 28px;
        padding: 20px;
        box-shadow:
            0 24px 70px rgba(0,0,0,.35),
            inset 0 1px 0 rgba(255,255,255,.04);
        color: #e5edf7;
    }

    html[data-theme="dark"] .grr-doc-page{
        background:
            radial-gradient(circle at top left, rgba(59,130,246,.08), transparent 26%),
            radial-gradient(circle at top right, rgba(245,158,11,.06), transparent 24%),
            linear-gradient(180deg, #0b1220 0%, #0f172a 100%) !important;
        border-color: rgba(255,255,255,.08) !important;
        color: #e5edf7 !important;
    }

    .grr-doc-page,
    .grr-doc-page *{
        box-sizing: border-box;
    }

    .fw-black{ font-weight: 900; }

    .grr-doc-kicker{
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .16em;
        text-transform: uppercase;
        color: rgba(191,219,254,.92) !important;
    }

    .grr-doc-hero{
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(8,12,19,.98), rgba(12,17,28,.96));
        border: 1px solid rgba(255,255,255,.08);
        box-shadow: 0 24px 60px rgba(0,0,0,.28);
        isolation: isolate;
    }

    .grr-doc-hero__inner{
        position: relative;
        z-index: 2;
        padding: 24px;
    }

    .grr-doc-hero__glow{
        position: absolute;
        border-radius: 999px;
        filter: blur(55px);
        opacity: .34;
        z-index: 0;
    }

    .grr-doc-hero__glow--blue{
        width: 240px;
        height: 240px;
        background: rgba(59,130,246,.30);
        top: -60px;
        left: -30px;
    }

    .grr-doc-hero__glow--gold{
        width: 220px;
        height: 220px;
        background: rgba(245,158,11,.20);
        top: -40px;
        right: -30px;
    }

    .grr-doc-hero__icon{
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: grid;
        place-items: center;
        font-size: 24px;
        flex-shrink: 0;
        background: linear-gradient(180deg, rgba(59,130,246,.22), rgba(59,130,246,.12));
        border: 1px solid rgba(59,130,246,.26);
        box-shadow: 0 14px 28px rgba(0,0,0,.20);
        color: #fff;
    }

    .grr-doc-hero__title{
        color: #f8fbff !important;
        letter-spacing: -.02em;
    }

    .grr-doc-hero__sub{
        max-width: 760px;
        color: rgba(226,232,240,.78) !important;
        font-weight: 650;
        line-height: 1.6;
    }

    .grr-doc-hero__aside{
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .grr-doc-hero__asideBox{
        min-width: 135px;
        padding: 12px 14px;
        border-radius: 16px;
        background: rgba(255,255,255,.05);
        border: 1px solid rgba(255,255,255,.08);
        backdrop-filter: blur(8px);
    }

    .grr-doc-hero__asideLabel{
        display: block;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: rgba(226,232,240,.52) !important;
        margin-bottom: 2px;
    }

    .grr-doc-hero__asideValue{
        color: #f8fbff !important;
        font-size: 14px;
        font-weight: 900;
    }

    .grr-doc-pill{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: 12px;
        line-height: 1;
        font-weight: 900;
        border: 1px solid rgba(255,255,255,.12);
        background: rgba(255,255,255,.06);
        color: #eaf0f8 !important;
        white-space: nowrap;
    }

    .grr-doc-pill--blue{
        border-color: rgba(59,130,246,.35);
        background: rgba(59,130,246,.18);
        color: rgba(231,237,246,.97) !important;
    }

    .grr-doc-pill--soft{
        background: rgba(255,255,255,.05);
        color: rgba(231,237,246,.84) !important;
    }

    .grr-doc-pill--edit{
        border-color: rgba(245,158,11,.30);
        background: rgba(245,158,11,.16);
        color: #fff !important;
    }

    .grr-doc-pill--edit:hover{
        color: #fff !important;
        background: rgba(245,158,11,.22);
    }

    .grr-doc-alert{
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 18px;
        border-radius: 18px;
        border: 1px solid rgba(245,158,11,.18);
        background: linear-gradient(180deg, rgba(58,42,14,.95), rgba(40,29,8,.98));
        box-shadow: 0 14px 34px rgba(0,0,0,.22);
    }

    .grr-doc-alert__icon{
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        font-size: 20px;
        background: rgba(245,158,11,.12);
        border: 1px solid rgba(245,158,11,.20);
        flex-shrink: 0;
        color: #fbbf24 !important;
    }

    .grr-doc-alert__title{
        font-size: 15px;
        font-weight: 900;
        color: #fde68a !important;
        margin-bottom: 2px;
    }

    .grr-doc-alert__text{
        color: rgba(254,240,138,.78) !important;
        font-weight: 700;
        line-height: 1.6;
    }

    .grr-doc-mini{
        height: 100%;
        padding: 16px;
        border-radius: 18px;
        background: linear-gradient(180deg, rgba(15,23,42,.72), rgba(15,23,42,.54));
        border: 1px solid rgba(255,255,255,.08);
        box-shadow: 0 10px 24px rgba(0,0,0,.18);
    }

    .grr-doc-mini__label{
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 900;
        color: rgba(148,163,184,.90) !important;
        margin-bottom: 6px;
    }

    .grr-doc-mini__value{
        font-size: 1.35rem;
        font-weight: 900;
        color: #f8fbff !important;
        line-height: 1.1;
        margin-bottom: 6px;
    }

    .grr-doc-mini__sub{
        font-size: 12px;
        line-height: 1.5;
        color: rgba(203,213,225,.82) !important;
        font-weight: 700;
    }

    .grr-doc-summary{
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.08);
        background: linear-gradient(180deg, rgba(8,12,19,.95), rgba(11,18,32,.98));
        box-shadow: 0 20px 55px rgba(0,0,0,.28);
        padding: 18px;
    }

    .grr-doc-summary__title{
        color: #f8fbff !important;
        font-weight: 900;
    }

    .grr-doc-summary__sub{
        color: rgba(203,213,225,.72) !important;
        font-size: 12px;
        font-weight: 700;
    }

    .grr-doc-summary__item{
        display: block;
        padding: 12px 14px;
        border-radius: 14px;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.06);
        color: rgba(226,232,240,.88) !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 800;
        transition: .18s ease;
    }

    .grr-doc-summary__item:hover{
        background: rgba(255,255,255,.05);
        border-color: rgba(59,130,246,.18);
        color: #fff !important;
        transform: translateY(-1px);
    }

    .grr-doc-body{
        border-radius: 22px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.08);
        background: linear-gradient(180deg, rgba(8,12,19,.95), rgba(11,18,32,.98));
        box-shadow: 0 20px 55px rgba(0,0,0,.28);
        padding: 18px;
    }

    .grr-doc-section{
        scroll-margin-top: 110px;
        padding: 0 0 24px 0;
        margin-bottom: 24px;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }

    .grr-doc-section:last-child{
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: 0;
    }

    .grr-doc-section__header{
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 16px;
    }

    .grr-doc-section__badge{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 78px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.06);
        color: #cbd5e1 !important;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .04em;
        border: 1px solid rgba(255,255,255,.06);
        flex-shrink: 0;
    }

    .grr-doc-section__title{
        color: #ffffff !important;
        font-weight: 900;
        margin: 0 0 4px;
    }

    .grr-doc-section__sub{
        color: rgba(203,213,225,.72) !important;
        font-size: 12px;
        font-weight: 700;
    }

    .grr-doc-article{
        padding: 14px 15px;
        border-radius: 16px;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.06);
        margin-bottom: 12px;
    }

    .grr-doc-article--last{
        margin-bottom: 0;
    }

    .grr-doc-article__title{
        font-size: 14px;
        font-weight: 900;
        color: #ffffff !important;
        margin-bottom: 6px;
    }

    .grr-doc-article__text{
        color: rgba(226,232,240,.82) !important;
        font-weight: 700;
        line-height: 1.7;
        font-size: 13px;
    }

    .grr-doc-empty{
        padding: 16px;
        border-radius: 16px;
        border: 1px dashed rgba(255,255,255,.12);
        background: rgba(255,255,255,.02);
        color: rgba(203,213,225,.75);
        font-size: 13px;
        font-weight: 700;
    }

    .grr-doc-empty--big{
        text-align: center;
        padding: 28px 18px;
    }

    @media (max-width: 1199.98px){
        .grr-doc-layout{
            grid-template-columns: 1fr;
        }

        .grr-doc-side{
            display: none;
        }
    }

    @media (max-width: 991.98px){
        .grr-doc-hero__aside{
            justify-content: flex-start;
        }
    }

    @media (max-width: 576px){
        .grr-doc-page{
            padding: 14px;
            border-radius: 20px;
        }

        .grr-doc-hero__inner,
        .grr-doc-summary,
        .grr-doc-body{
            padding: 16px 14px;
        }

        .grr-doc-hero__icon{
            width: 50px;
            height: 50px;
            border-radius: 16px;
            font-size: 21px;
        }

        .grr-doc-hero__title{
            font-size: 1.08rem;
        }

        .grr-doc-section__header{
            flex-direction: column;
            gap: 10px;
        }

        .grr-doc-section__badge{
            min-width: auto;
            font-size: 10px;
            padding: 6px 9px;
        }

        .grr-doc-article{
            padding: 13px;
            border-radius: 14px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sections = Array.from(document.querySelectorAll('.grr-doc-section[data-nav]'));
    const navLinks = Array.from(document.querySelectorAll('.grr-doc-side__link[data-nav-link]'));

    if (!sections.length || !navLinks.length) return;

    function setActiveNav(id) {
        navLinks.forEach(link => {
            const isActive = link.dataset.navLink === id;
            link.classList.toggle('is-active', isActive);

            if (isActive) {
                link.setAttribute('aria-current', 'true');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }

    const observer = new IntersectionObserver((entries) => {
        const visibleEntries = entries
            .filter(entry => entry.isIntersecting)
            .sort((a, b) => b.intersectionRatio - a.intersectionRatio);

        if (visibleEntries.length) {
            const activeId = visibleEntries[0].target.dataset.nav;
            setActiveNav(activeId);
        }
    }, {
        root: null,
        rootMargin: '-20% 0px -55% 0px',
        threshold: [0.15, 0.3, 0.5, 0.75]
    });

    sections.forEach(section => observer.observe(section));

    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            const targetId = this.dataset.navLink;
            setActiveNav(targetId);
        });
    });

    const initialSection = sections.find(section => {
        const rect = section.getBoundingClientRect();
        return rect.top <= window.innerHeight * 0.35 && rect.bottom > window.innerHeight * 0.35;
    });

    if (initialSection) {
        setActiveNav(initialSection.dataset.nav);
    } else if (sections[0]) {
        setActiveNav(sections[0].dataset.nav);
    }
});
</script>
@endsection