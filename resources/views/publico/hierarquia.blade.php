@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Str;

    $lista = collect($hierarquiaPublica ?? [])->values();

    $normalize = function ($value) {
        return Str::lower(Str::ascii(trim((string) $value)));
    };

    $cargoPublico = function ($membro) {
        $nome = $membro->nome ?? '';

        return $nome === 'Thomas Skywalker'
            ? 'Diretor'
            : ($membro->cargo ?? '—');
    };

    $cargoPeso = function ($cargo) use ($normalize) {
        $cargo = $normalize($cargo);

        if (Str::contains($cargo, 'diretor') && !Str::contains($cargo, 'vice')) return 1;
        if (Str::contains($cargo, 'vice diretor') || Str::contains($cargo, 'vice-diretor')) return 2;
        if (Str::contains($cargo, 'coordenador')) return 3;
        if (Str::contains($cargo, 'superintendente')) return 4;
        if (Str::contains($cargo, 'inspetor')) return 5;
        if (Str::contains($cargo, 'agente especial')) return 6;
        if (Str::contains($cargo, ['agente de 1', '1 classe', '1º classe', '1ª classe'])) return 7;
        if (Str::contains($cargo, ['agente de 2', '2 classe', '2º classe', '2ª classe'])) return 8;
        if (Str::contains($cargo, ['agente de 3', '3 classe', '3º classe', '3ª classe'])) return 9;
        if (Str::contains($cargo, 'aluno')) return 10;

        return 99;
    };

    $lista = $lista->sort(function ($a, $b) use ($cargoPeso, $cargoPublico) {
        $pesoA = $cargoPeso($cargoPublico($a));
        $pesoB = $cargoPeso($cargoPublico($b));

        if ($pesoA === $pesoB) {
            return strcmp($a->nome ?? '', $b->nome ?? '');
        }

        return $pesoA <=> $pesoB;
    })->values();

    $total = $lista->count();
    $instrutores = $lista->filter(fn($m) => !empty($m->instrutor))->count();
    $equipes = $lista->pluck('equipe')->filter()->unique()->sort()->values();
    $cargos = $lista->map(fn($m) => $cargoPublico($m))->filter()->unique()->sort()->values();

    $cursosDisponiveis = collect(['POP', 'CLT', 'CAP', 'CTB', 'CTA', 'SAT-B', 'BOPM', 'GMP', 'DOA']);

    $totalCursos = $lista->sum(function ($membro) {
        return collect([
            $membro->pop ?? false,
            $membro->clt ?? false,
            $membro->cap ?? false,
            $membro->ctb ?? false,
            $membro->cta ?? false,
            $membro->satb ?? false,
            $membro->bopm ?? false,
            $membro->gmp ?? false,
            $membro->doa ?? false,
        ])->filter(fn($v) => (bool) $v)->count();
    });

    $blocks = [
        [
            'title' => 'Diretoria',
            'subtitle' => 'Comando superior da unidade',
            'icon' => '♔',
            'items' => $lista->filter(fn($m) => $cargoPeso($cargoPublico($m)) <= 2)->values(),
        ],
        [
            'title' => 'Coordenação e Superintendência',
            'subtitle' => 'Gestão estratégica e operacional',
            'icon' => '🧭',
            'items' => $lista->filter(fn($m) => in_array($cargoPeso($cargoPublico($m)), [3, 4]))->values(),
        ],
        [
            'title' => 'Tático',
            'subtitle' => 'Inspetores e agentes especiais',
            'icon' => '🛡️',
            'items' => $lista->filter(fn($m) => in_array($cargoPeso($cargoPublico($m)), [5, 6]))->values(),
        ],
        [
            'title' => 'Operacional',
            'subtitle' => 'Demais cargos operacionais',
            'icon' => '⚡',
            'items' => $lista->filter(fn($m) => $cargoPeso($cargoPublico($m)) >= 7)->values(),
        ],
    ];
@endphp

<style>
    :root {
        --hp-bg: #07111f;
        --hp-bg-2: #0b1220;
        --hp-border: rgba(148, 163, 184, .18);
        --hp-text: #e5e7eb;
        --hp-muted: #94a3b8;
        --hp-blue: #60a5fa;
        --hp-gold: #f59e0b;
        --hp-shadow: 0 24px 80px rgba(0, 0, 0, .34);
    }

    .hp-page {
        min-height: 100vh;
        padding: 28px 18px 72px;
        color: var(--hp-text);
        background:
            radial-gradient(circle at 8% 0%, rgba(37, 99, 235, .18), transparent 34rem),
            radial-gradient(circle at 92% 6%, rgba(245, 158, 11, .12), transparent 30rem),
            linear-gradient(180deg, var(--hp-bg) 0%, var(--hp-bg-2) 48%, #070d19 100%);
    }

    .hp-shell {
        max-width: 1480px;
        margin: 0 auto;
    }

    .hp-progress {
        position: fixed;
        left: 0;
        top: 0;
        height: 3px;
        width: 0;
        background: linear-gradient(90deg, var(--hp-blue), var(--hp-gold));
        z-index: 9999;
        box-shadow: 0 0 18px rgba(96, 165, 250, .70);
    }

    .hp-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--hp-border);
        border-radius: 28px;
        padding: 26px;
        margin-bottom: 16px;
        background:
            linear-gradient(135deg, rgba(15, 23, 42, .96), rgba(30, 41, 59, .82)),
            radial-gradient(circle at 84% 18%, rgba(37, 99, 235, .32), transparent 25rem);
        box-shadow: var(--hp-shadow);
        animation: hpFadeUp .55s ease both;
    }

    .hp-hero::before {
        content: "";
        position: absolute;
        top: -45%;
        left: -30%;
        width: 46%;
        height: 190%;
        background: linear-gradient(
            115deg,
            transparent 0%,
            rgba(255, 255, 255, .015) 28%,
            rgba(96, 165, 250, .10) 48%,
            rgba(245, 158, 11, .055) 58%,
            transparent 100%
        );
        filter: blur(3px);
        opacity: .75;
        transform: translateX(-40%) skewX(-18deg);
        animation: hpHeroGlow 7.5s ease-in-out infinite alternate;
        pointer-events: none;
    }

    .hp-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at var(--mx, 50%) var(--my, 50%), rgba(96, 165, 250, .08), transparent 18rem);
        opacity: .8;
        pointer-events: none;
    }

    .hp-hero-content {
        position: relative;
        z-index: 1;
    }

    .hp-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #bae6fd;
        font-size: .72rem;
        font-weight: 1000;
        letter-spacing: .16em;
        text-transform: uppercase;
    }

    .hp-kicker-dot {
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: var(--hp-gold);
        box-shadow: 0 0 0 6px rgba(245, 158, 11, .13);
    }

    .hp-title {
        margin: 10px 0 6px;
        color: #fff;
        font-size: clamp(1.85rem, 3.3vw, 3.25rem);
        line-height: .98;
        font-weight: 1000;
        letter-spacing: -.06em;
    }

    .hp-subtitle {
        max-width: 820px;
        color: var(--hp-muted);
        font-size: .94rem;
        line-height: 1.65;
    }

    .hp-stat-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
        align-items: flex-start;
        min-width: auto;
        max-width: none;
    }

    .hp-stat {
        position: relative;
        overflow: hidden;
        width: auto;
        min-width: 92px;
        height: auto;
        min-height: 0;
        border: 1px solid rgba(96, 165, 250, .18);
        border-radius: 13px;
        padding: 8px 12px;
        background:
            linear-gradient(145deg, rgba(37, 99, 235, .16), rgba(30, 41, 59, .50)),
            rgba(15, 23, 42, .70);
        backdrop-filter: blur(10px);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
        transition: transform .2s ease, border-color .2s ease, background .2s ease;
    }

    .hp-stat:hover {
        transform: translateY(-2px);
        border-color: rgba(96, 165, 250, .40);
        background:
            linear-gradient(145deg, rgba(37, 99, 235, .24), rgba(30, 41, 59, .62)),
            rgba(15, 23, 42, .82);
    }

    .hp-stat::before {
        content: "";
        position: absolute;
        inset: -40%;
        background: linear-gradient(120deg, transparent, rgba(255,255,255,.08), transparent);
        transform: translateX(-110%) rotate(12deg);
        animation: hpStatGlow 5.8s ease-in-out infinite;
        pointer-events: none;
    }

    .hp-stat strong {
        display: block;
        color: #fff;
        font-size: 1rem;
        line-height: 1;
        font-weight: 1000;
    }

    .hp-stat span {
        display: block;
        margin-top: 4px;
        color: #bfdbfe;
        font-size: .55rem;
        line-height: 1;
        font-weight: 1000;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .hp-toolbar {
        position: sticky;
        top: 8px;
        z-index: 10;
        border: 1px solid var(--hp-border);
        border-radius: 22px;
        padding: 13px;
        margin-bottom: 16px;
        background: rgba(15, 23, 42, .90);
        backdrop-filter: blur(18px);
        box-shadow: 0 18px 60px rgba(0, 0, 0, .24);
        animation: hpFadeUp .65s ease both;
    }

    .hp-input,
    .hp-select {
        width: 100%;
        min-height: 44px;
        border: 1px solid rgba(148, 163, 184, .18);
        border-radius: 15px;
        background: #020617;
        color: #fff;
        padding: 10px 13px;
        font-size: .86rem;
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .hp-input:focus,
    .hp-select:focus {
        border-color: rgba(96, 165, 250, .56);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .16);
    }

    .hp-input::placeholder {
        color: #64748b;
    }

    .hp-filter-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        width: 100%;
        border: 1px solid rgba(96, 165, 250, .20);
        border-radius: 15px;
        padding: 0 13px;
        background: rgba(37, 99, 235, .12);
        color: #bfdbfe;
        font-size: .76rem;
        font-weight: 1000;
        white-space: nowrap;
    }

    .hp-btn {
        min-height: 38px;
        border: 1px solid rgba(96, 165, 250, .24);
        border-radius: 13px;
        background: rgba(37, 99, 235, .13);
        color: #bfdbfe;
        padding: 0 13px;
        font-size: .74rem;
        font-weight: 1000;
        transition: transform .18s ease, background .18s ease, border-color .18s ease;
    }

    .hp-btn:hover {
        transform: translateY(-1px);
        background: rgba(37, 99, 235, .24);
        border-color: rgba(96, 165, 250, .46);
    }

    .hp-block-card {
        overflow: hidden;
        border: 1px solid var(--hp-border);
        border-radius: 24px;
        background: rgba(15, 23, 42, .80);
        box-shadow: var(--hp-shadow);
        animation: hpFadeUp .78s ease both;
        margin-bottom: 18px;
    }

    .hp-block-card.is-hidden {
        display: none;
    }

    .hp-block-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 16px 18px;
        border-bottom: 1px solid rgba(148, 163, 184, .12);
        background:
            linear-gradient(90deg, rgba(30, 41, 59, .92), rgba(15, 23, 42, .90));
    }

    .hp-block-title {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #fff;
        font-size: 1.03rem;
        font-weight: 1000;
        letter-spacing: -.02em;
    }

    .hp-block-icon {
        width: 36px;
        height: 36px;
        border-radius: 13px;
        display: grid;
        place-items: center;
        background: rgba(37, 99, 235, .14);
        border: 1px solid rgba(96, 165, 250, .22);
    }

    .hp-block-sub {
        color: var(--hp-muted);
        font-size: .76rem;
        margin-top: 3px;
    }

    .hp-block-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        border-radius: 999px;
        padding: 0 12px;
        background: rgba(37, 99, 235, .12);
        border: 1px solid rgba(96, 165, 250, .22);
        color: #bfdbfe;
        font-size: .74rem;
        font-weight: 1000;
        white-space: nowrap;
    }

    .hp-table-wrap {
        overflow-x: auto;
        max-height: 520px;
    }

    .hp-table {
        width: 100%;
        min-width: 1240px;
        border-collapse: collapse;
    }

    .hp-table th {
        position: sticky;
        top: 0;
        z-index: 3;
        padding: 12px 14px;
        background: #07111f;
        border-bottom: 1px solid rgba(148, 163, 184, .14);
        color: #93a4bd;
        font-size: .67rem;
        font-weight: 1000;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .12em;
        white-space: nowrap;
    }

    .hp-table td {
        padding: 13px 14px;
        border-bottom: 1px solid rgba(148, 163, 184, .08);
        color: #dbeafe;
        font-size: .82rem;
        vertical-align: middle;
    }

    .hp-row {
        opacity: 0;
        transform: translateY(10px);
        animation: hpRowIn .45s ease forwards;
    }

    .hp-row:hover td {
        background: rgba(37, 99, 235, .085);
    }

    .hp-row:hover .hp-avatar {
        transform: scale(1.05) rotate(-2deg);
    }

    .hp-name-wrap {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 245px;
    }

    .hp-avatar {
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        background:
            linear-gradient(135deg, rgba(37, 99, 235, .96), rgba(245, 158, 11, .84));
        color: #fff;
        font-size: .76rem;
        font-weight: 1000;
        box-shadow: 0 10px 24px rgba(37, 99, 235, .22);
        transition: transform .18s ease;
    }

    .hp-name {
        color: #fff;
        font-weight: 1000;
        line-height: 1.15;
        white-space: nowrap;
    }

    .hp-name-meta {
        margin-top: 3px;
        color: #64748b;
        font-size: .68rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .hp-cargo {
        color: #93c5fd;
        font-weight: 1000;
        white-space: nowrap;
    }

    .hp-muted {
        color: var(--hp-muted);
        font-weight: 800;
        white-space: nowrap;
    }

    .hp-serial {
        color: #e2e8f0;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        font-size: .78rem;
        font-weight: 900;
        letter-spacing: .03em;
        white-space: nowrap;
    }

    .hp-team {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 68px;
        border-radius: 999px;
        padding: 5px 9px;
        color: #dbeafe;
        background: rgba(30, 41, 59, .72);
        border: 1px solid rgba(148, 163, 184, .16);
        font-size: .69rem;
        font-weight: 1000;
        white-space: nowrap;
    }

    .hp-instructor {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 999px;
        padding: 5px 9px;
        background: rgba(245, 158, 11, .16);
        border: 1px solid rgba(245, 158, 11, .30);
        color: #fde68a;
        font-size: .68rem;
        font-weight: 1000;
        white-space: nowrap;
    }

    .hp-course-list {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        max-width: 390px;
    }

    .hp-course {
        border-radius: 999px;
        padding: 4px 7px;
        background: rgba(37, 99, 235, .14);
        border: 1px solid rgba(96, 165, 250, .18);
        color: #bfdbfe;
        font-size: .64rem;
        font-weight: 1000;
        white-space: nowrap;
        transition: transform .16s ease, background .16s ease, border-color .16s ease;
    }

    .hp-course:hover {
        transform: translateY(-1px);
        background: rgba(37, 99, 235, .26);
        border-color: rgba(96, 165, 250, .38);
    }

    .hp-empty {
        padding: 34px 18px;
        text-align: center;
        color: var(--hp-muted);
        border: 1px dashed rgba(148, 163, 184, .22);
        border-radius: 20px;
        background: rgba(15, 23, 42, .62);
    }

    .hp-empty strong {
        display: block;
        color: #fff;
        margin-bottom: 4px;
        font-size: 1rem;
    }

    .hp-footer-note {
        margin-top: 12px;
        color: #64748b;
        font-size: .72rem;
        font-weight: 800;
        text-align: right;
    }

    @keyframes hpHeroGlow {
        0% { transform: translateX(-35%) skewX(-18deg); opacity: .25; }
        50% { opacity: .85; }
        100% { transform: translateX(235%) skewX(-18deg); opacity: .35; }
    }

    @keyframes hpStatGlow {
        0%, 65% { transform: translateX(-110%) rotate(12deg); opacity: 0; }
        78% { opacity: .75; }
        100% { transform: translateX(110%) rotate(12deg); opacity: 0; }
    }

    @keyframes hpFadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes hpRowIn {
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 992px) {
        .hp-stat-grid {
            justify-content: flex-start;
            max-width: 100%;
            width: 100%;
        }

        .hp-toolbar {
            position: relative;
            top: auto;
        }

        .hp-table-wrap {
            max-height: none;
        }
    }

    @media (max-width: 768px) {
        .hp-page {
            padding: 18px 10px 42px;
        }

        .hp-hero,
        .hp-block-head {
            padding: 18px;
        }

        .hp-block-head {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="hp-progress" id="scrollProgress"></div>

<div class="hp-page">
    <div class="hp-shell">

        <section class="hp-hero" id="heroPanel">
            <div class="hp-hero-content">
                <div class="d-flex flex-column flex-xl-row justify-content-between gap-4">
                    <div>
                        <div class="hp-kicker">
                            <span class="hp-kicker-dot"></span>
                            RH • Hierarquia Pública • GRR 3.0
                        </div>

                        <h1 class="hp-title">Quadro Operacional da GRR</h1>

                        <div class="hp-subtitle">
                            Consulta institucional pública com membros ativos, equipes, cargos, instrutores,
                            datas operacionais e cursos registrados no sistema interno.
                        </div>
                    </div>

                    <div class="hp-stat-grid">
                        <div class="hp-stat">
                            <strong>{{ $total }}</strong>
                            <span>Membros</span>
                        </div>

                        <div class="hp-stat">
                            <strong>{{ $instrutores }}</strong>
                            <span>Instrutores</span>
                        </div>

                        <div class="hp-stat">
                            <strong>{{ $equipes->count() }}</strong>
                            <span>Equipes</span>
                        </div>

                        <div class="hp-stat">
                            <strong>{{ $totalCursos }}</strong>
                            <span>Cursos</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="hp-toolbar">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-xl">
                    <input
                        type="text"
                        id="searchInput"
                        class="hp-input"
                        placeholder="Buscar por nome, cargo, equipe, serial ou curso..."
                    >
                </div>

                <div class="col-12 col-md-6 col-xl-2">
                    <select id="teamFilter" class="hp-select">
                        <option value="">Todas as equipes</option>
                        @foreach($equipes as $equipe)
                            <option value="{{ $normalize($equipe) }}">{{ $equipe }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 col-xl-2">
                    <select id="cargoFilter" class="hp-select">
                        <option value="">Todos os cargos</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $normalize($cargo) }}">{{ $cargo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 col-xl-2">
                    <select id="courseFilter" class="hp-select">
                        <option value="">Todos os cursos</option>
                        @foreach($cursosDisponiveis as $curso)
                            <option value="{{ $normalize($curso) }}">{{ $curso }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-6 col-xl-1">
                    <select id="instructorFilter" class="hp-select">
                        <option value="">Todos</option>
                        <option value="sim">Instrutores</option>
                        <option value="nao">Não instrutores</option>
                    </select>
                </div>

                <div class="col-12 col-xl-auto">
                    <div class="hp-filter-chip">
                        <span id="visibleCount">{{ $total }}</span>&nbsp;visíveis
                    </div>
                </div>
            </div>
        </section>

        @if($total > 0)
            @foreach($blocks as $block)
                @if($block['items']->count() > 0)
                    <section class="hp-block-card" data-block>
                        <div class="hp-block-head">
                            <div>
                                <div class="hp-block-title">
                                    <span class="hp-block-icon">{{ $block['icon'] }}</span>
                                    {{ $block['title'] }}
                                </div>
                                <div class="hp-block-sub">
                                    {{ $block['subtitle'] }} •
                                    <span data-block-count>{{ $block['items']->count() }}</span> registros visíveis
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <span class="hp-block-count">
                                    {{ $block['items']->count() }} cadastrados
                                </span>

                                <button type="button" class="hp-btn" data-clear-filters>
                                    Limpar filtros
                                </button>
                            </div>
                        </div>

                        <div class="hp-table-wrap">
                            <table class="hp-table">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Cargo</th>
                                        <th>Equipe</th>
                                        <th>Serial</th>
                                        <th>Admissão</th>
                                        <th>Última promoção</th>
                                        <th>Instrutor</th>
                                        <th>Cursos</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($block['items'] as $index => $membro)
                                        @php
                                            $nome = $membro->nome ?? 'Sem nome';
                                            $cargoExibido = $cargoPublico($membro);

                                            $iniciais = collect(explode(' ', trim($nome)))
                                                ->filter()
                                                ->take(2)
                                                ->map(fn($p) => mb_substr($p, 0, 1))
                                                ->implode('');

                                            $cursos = collect([
                                                'POP' => $membro->pop ?? false,
                                                'CLT' => $membro->clt ?? false,
                                                'CAP' => $membro->cap ?? false,
                                                'CTB' => $membro->ctb ?? false,
                                                'CTA' => $membro->cta ?? false,
                                                'SAT-B' => $membro->satb ?? false,
                                                'BOPM' => $membro->bopm ?? false,
                                                'GMP' => $membro->gmp ?? false,
                                                'DOA' => $membro->doa ?? false,
                                            ])->filter(fn($v) => (bool) $v)->keys();

                                            $admissao = !empty($membro->admissao)
                                                ? \Carbon\Carbon::parse($membro->admissao)->format('d/m/Y')
                                                : '—';

                                            $promocao = !empty($membro->ultima_promocao)
                                                ? \Carbon\Carbon::parse($membro->ultima_promocao)->format('d/m/Y')
                                                : '—';

                                            $search = $normalize(
                                                ($membro->nome ?? '') . ' ' .
                                                $cargoExibido . ' ' .
                                                ($membro->equipe ?? '') . ' ' .
                                                ($membro->serial ?? '') . ' ' .
                                                $cursos->implode(' ')
                                            );

                                            $equipeSearch = $normalize($membro->equipe ?? '');
                                            $cargoSearch = $normalize($cargoExibido);
                                            $cursoSearch = $normalize($cursos->implode(' '));
                                            $instrutorSearch = !empty($membro->instrutor) ? 'sim' : 'nao';
                                        @endphp

                                        <tr
                                            class="hp-row"
                                            style="animation-delay: {{ min($index * 0.018, 1.1) }}s"
                                            data-search="{{ $search }}"
                                            data-equipe="{{ $equipeSearch }}"
                                            data-cargo="{{ $cargoSearch }}"
                                            data-cursos="{{ $cursoSearch }}"
                                            data-instrutor="{{ $instrutorSearch }}"
                                        >
                                            <td>
                                                <div class="hp-name-wrap">
                                                    <div class="hp-avatar">{{ $iniciais ?: 'GR' }}</div>

                                                    <div>
                                                        <div class="hp-name">{{ $nome }}</div>
                                                        <div class="hp-name-meta">Efetivo operacional</div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td><span class="hp-cargo">{{ $cargoExibido }}</span></td>
                                            <td><span class="hp-team">{{ $membro->equipe ?? '—' }}</span></td>
                                            <td><span class="hp-serial">{{ $membro->serial ?? '—' }}</span></td>
                                            <td><span class="hp-muted">{{ $admissao }}</span></td>
                                            <td><span class="hp-muted">{{ $promocao }}</span></td>

                                            <td>
                                                @if(!empty($membro->instrutor))
                                                    <span class="hp-instructor">⭐ Instrutor</span>
                                                @else
                                                    <span class="hp-muted">—</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="hp-course-list">
                                                    @forelse($cursos as $curso)
                                                        <span class="hp-course">{{ $curso }}</span>
                                                    @empty
                                                        <span class="hp-muted">—</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                @endif
            @endforeach

            <div class="hp-empty d-none mt-3" id="emptySearch">
                <strong>Nenhum membro encontrado</strong>
                Tente limpar os filtros ou buscar por outro termo.
            </div>

            <div class="hp-footer-note">
                Atualização automática conforme registros da hierarquia interna.
            </div>
        @else
            <div class="hp-empty">
                <strong>Nenhum membro ativo encontrado</strong>
                A hierarquia pública ainda não possui registros disponíveis.
            </div>
        @endif

    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const teamFilter = document.getElementById('teamFilter');
    const cargoFilter = document.getElementById('cargoFilter');
    const courseFilter = document.getElementById('courseFilter');
    const instructorFilter = document.getElementById('instructorFilter');
    const clearButtons = document.querySelectorAll('[data-clear-filters]');
    const visibleCount = document.getElementById('visibleCount');
    const emptySearch = document.getElementById('emptySearch');
    const scrollProgress = document.getElementById('scrollProgress');
    const heroPanel = document.getElementById('heroPanel');
    const rows = document.querySelectorAll('.hp-row');
    const blocks = document.querySelectorAll('[data-block]');

    function applyFilters() {
        const term = (searchInput?.value || '').toLowerCase().trim();
        const team = (teamFilter?.value || '').toLowerCase().trim();
        const cargo = (cargoFilter?.value || '').toLowerCase().trim();
        const course = (courseFilter?.value || '').toLowerCase().trim();
        const instructor = (instructorFilter?.value || '').toLowerCase().trim();

        let visible = 0;

        rows.forEach(row => {
            const search = row.dataset.search || '';
            const rowTeam = row.dataset.equipe || '';
            const rowCargo = row.dataset.cargo || '';
            const rowCourses = row.dataset.cursos || '';
            const rowInstructor = row.dataset.instrutor || '';

            const passSearch = !term || search.includes(term);
            const passTeam = !team || rowTeam === team;
            const passCargo = !cargo || rowCargo === cargo;
            const passCourse = !course || rowCourses.includes(course);
            const passInstructor = !instructor || rowInstructor === instructor;

            const show = passSearch && passTeam && passCargo && passCourse && passInstructor;

            row.style.display = show ? '' : 'none';

            if (show) visible++;
        });

        blocks.forEach(block => {
            const blockRows = block.querySelectorAll('.hp-row');
            let blockVisible = 0;

            blockRows.forEach(row => {
                if (row.style.display !== 'none') blockVisible++;
            });

            const countElement = block.querySelector('[data-block-count]');
            if (countElement) countElement.textContent = blockVisible;

            block.classList.toggle('is-hidden', blockVisible === 0);
        });

        if (visibleCount) visibleCount.textContent = visible;
        if (emptySearch) emptySearch.classList.toggle('d-none', visible !== 0);
    }

    function clearAllFilters() {
        if (searchInput) searchInput.value = '';
        if (teamFilter) teamFilter.value = '';
        if (cargoFilter) cargoFilter.value = '';
        if (courseFilter) courseFilter.value = '';
        if (instructorFilter) instructorFilter.value = '';

        applyFilters();
    }

    function updateScrollProgress() {
        if (!scrollProgress) return;

        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

        scrollProgress.style.width = progress + '%';
    }

    heroPanel?.addEventListener('mousemove', (event) => {
        const rect = heroPanel.getBoundingClientRect();
        const x = ((event.clientX - rect.left) / rect.width) * 100;
        const y = ((event.clientY - rect.top) / rect.height) * 100;

        heroPanel.style.setProperty('--mx', x + '%');
        heroPanel.style.setProperty('--my', y + '%');
    });

    searchInput?.addEventListener('input', applyFilters);
    teamFilter?.addEventListener('change', applyFilters);
    cargoFilter?.addEventListener('change', applyFilters);
    courseFilter?.addEventListener('change', applyFilters);
    instructorFilter?.addEventListener('change', applyFilters);

    clearButtons.forEach(button => {
        button.addEventListener('click', clearAllFilters);
    });

    window.addEventListener('scroll', updateScrollProgress);

    applyFilters();
    updateScrollProgress();
</script>
@endsection