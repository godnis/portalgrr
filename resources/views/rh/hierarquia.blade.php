@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Str;

    $auth = auth()->user();
    $authNivel = (int)($auth->nivel ?? 0);

    $canEdit = $canEdit ?? false;
    $canDelete = $auth && $authNivel >= 9;

    $all = collect()
        ->merge($diretoria ?? collect())
        ->merge($oficiais ?? collect())
        ->merge($outros ?? collect())
        ->values();

    $pageCount   = $all->count();
    $totalSystem = $pageCount;

    $statusCounts = $all
        ->groupBy(fn($r) => $r->status ?: 'indefinido')
        ->map(fn($g) => $g->count());

    $cEx = (int) ($statusCounts['em_exercicio'] ?? 0);
    $cLi = (int) ($statusCounts['em_licenca'] ?? 0);
    $cDe = (int) ($statusCounts['desligado'] ?? 0);
    $cEs = (int) ($statusCounts['estagio'] ?? 0);

    $normalize = function ($text) {
        return Str::lower(Str::ascii(trim((string) $text)));
    };

    $cargoNorm = function ($item) use ($normalize) {
        return $normalize($item->cargo ?? '');
    };

    $statusLabel = function ($status) {
        return match($status){
            'em_exercicio' => 'Em Exercício',
            'em_licenca'   => 'Em Licença',
            'desligado'    => 'Desligado',
            'estagio'      => 'Estágio',
            default        => $status ?: '—'
        };
    };

    $statusClass = function ($status) {
        return match($status){
            'em_exercicio' => 'st st--ok',
            'em_licenca'   => 'st st--warn',
            'desligado'    => 'st st--bad',
            'estagio'      => 'st st--soft',
            default        => 'st st--soft'
        };
    };

    $boolPill = function ($b) {
        return $b
            ? '<span class="pill pill--yes">✓</span>'
            : '<span class="pill pill--no">—</span>';
    };

    $containsAny = function ($cargo, array $needles) {
        foreach ($needles as $needle) {
            if (Str::contains($cargo, $needle)) return true;
        }
        return false;
    };

    $filtered = $all->filter(function ($r) use ($q, $status, $equipe, $normalize) {
        $ok = true;

        if (filled($q ?? null)) {
            $haystack = $normalize(
                ($r->nome ?? '') . ' ' .
                ($r->cpf ?? '') . ' ' .
                ($r->cargo ?? '') . ' ' .
                ($r->serial ?? '') . ' ' .
                ($r->equipe ?? '') . ' ' .
                ($r->discord_id ?? '') . ' ' .
                ($r->funcao_obs ?? '')
            );
            $ok = $ok && Str::contains($haystack, $normalize($q));
        }

        if (filled($status ?? null)) {
            $ok = $ok && (($r->status ?? null) === $status);
        }

        if (filled($equipe ?? null)) {
            $ok = $ok && Str::contains(
                $normalize($r->equipe ?? ''),
                $normalize($equipe)
            );
        }

        return $ok;
    })->values();

    $classified = [
        'diretoria' => collect(),
        'estrategicos' => collect(),
        'taticos' => collect(),
        'operacionais' => collect(),
    ];

    foreach ($filtered as $r) {
        $cargo = $cargoNorm($r);

        if (
            $cargo === 'diretor' ||
            $cargo === 'vice-diretor' ||
            $cargo === 'vice diretor'
        ) {
            $classified['diretoria']->push($r);
            continue;
        }

        if ($containsAny($cargo, ['coordenador', 'superintendente'])) {
            $classified['estrategicos']->push($r);
            continue;
        }

        if ($containsAny($cargo, ['inspetor', 'classe especial', 'agente especial'])) {
            $classified['taticos']->push($r);
            continue;
        }

        $classified['operacionais']->push($r);
    }

    $sortByCargoOrder = function ($collection, array $priorityMap) use ($cargoNorm) {
        return $collection->sortBy(function ($r) use ($priorityMap, $cargoNorm) {
            $cargo = $cargoNorm($r);
            foreach ($priorityMap as $needle => $order) {
                if (Str::contains($cargo, $needle) || $cargo === $needle) {
                    return $order;
                }
            }
            return 999;
        })->values();
    };

    $classified['diretoria'] = $sortByCargoOrder($classified['diretoria'], [
        'diretor'       => 1,
        'vice-diretor'  => 2,
        'vice diretor'  => 2,
    ]);

    $classified['estrategicos'] = $sortByCargoOrder($classified['estrategicos'], [
        'coordenador'     => 1,
        'superintendente' => 2,
    ]);

    $classified['taticos'] = $sortByCargoOrder($classified['taticos'], [
        'inspetor'        => 1,
        'classe especial' => 2,
        'agente especial' => 2,
    ]);

    $classified['operacionais'] = $sortByCargoOrder($classified['operacionais'], [
        '1 classe'    => 1,
        '1a classe'   => 1,
        '1ª classe'   => 1,
        '2 classe'    => 2,
        '2a classe'   => 2,
        '2ª classe'   => 2,
        '3 classe'    => 3,
        '3a classe'   => 3,
        '3ª classe'   => 3,
        'treinamento' => 4,
        'aluno'       => 5,
        'alunos'      => 5,
    ]);

    $blocks = [
        [
            'key' => 'diretoria',
            'title' => 'Diretoria',
            'sub' => 'Diretor e Vice-Diretor',
            'icon' => '♔',
            'items' => $classified['diretoria'],
        ],
        [
            'key' => 'estrategicos',
            'title' => 'Estratégicos',
            'sub' => 'Coordenador e Superintendente',
            'icon' => '🧭',
            'items' => $classified['estrategicos'],
        ],
        [
            'key' => 'taticos',
            'title' => 'Táticos',
            'sub' => 'Inspetor e Agente Especial',
            'icon' => '🛡️',
            'items' => $classified['taticos'],
        ],
        [
            'key' => 'operacionais',
            'title' => 'Operacionais',
            'sub' => '1ª/2ª/3ª Classe, Treinamento e Alunos',
            'icon' => '⚡',
            'items' => $classified['operacionais'],
        ],
    ];
@endphp

<div class="rhx-wrap">
    <div class="rhx-hero">
        <div class="rhx-hero__bg"></div>

        <div class="rhx-hero__inner">
            <div class="rhx-hero__left">
                <div class="rhx-kicker">RH • HIERARQUIA • GRR 3.0</div>
                <h1 class="rhx-title">Quadro de Efetivo</h1>
                <div class="rhx-sub">
                    Estrutura em planilha, organizada por níveis hierárquicos, com leitura mais rápida e detalhes expansíveis.
                </div>

                <div class="rhx-chips">
                    <span class="rhx-chip"><span class="dot dot--blue"></span> Registros exibidos: <b>{{ $pageCount }}</b></span>
                    <span class="rhx-chip"><span class="dot dot--green"></span> Em exercício: <b>{{ $cEx }}</b></span>
                    <span class="rhx-chip"><span class="dot dot--amber"></span> Licença: <b>{{ $cLi }}</b></span>
                    <span class="rhx-chip"><span class="dot dot--red"></span> Desligado: <b>{{ $cDe }}</b></span>
                    <span class="rhx-chip"><span class="dot dot--gray"></span> Estágio: <b>{{ $cEs }}</b></span>
                </div>
            </div>

            <div class="rhx-hero__right">
                <a href="{{ route('rh.index') }}" class="btn btn-outline-secondary rhx-btn">Voltar</a>

                @if($canEdit)
                    <a href="{{ route('rh.hierarquia.create') }}" class="btn btn-primary rhx-btn rhx-btn--primary">
                        + Novo Registro
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="rhx-container">

        @if(session('success'))
            <div class="alert alert-success rhx-alert">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger rhx-alert">
                <div class="fw-semibold mb-1">Corrija os campos:</div>
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rhx-panel mb-3">
            <div class="rhx-panel__head">
                <div>
                    <div class="rhx-panel__title">Filtros</div>
                    <div class="rhx-panel__sub">Pesquise por nome, CPF, cargo, serial, equipe ou observação</div>
                </div>
                <div class="rhx-muted">Total no sistema: <b>{{ $totalSystem }}</b></div>
            </div>

            <div class="rhx-panel__body">
                <form class="row g-3 align-items-end" method="GET" action="{{ route('rh.hierarquia') }}">
                    <div class="col-12 col-lg-5">
                        <label class="form-label rhx-label">Busca</label>
                        <input class="form-control rhx-input" name="q" value="{{ $q }}" placeholder="Nome, CPF, cargo, serial...">
                    </div>

                    <div class="col-12 col-md-4 col-lg-3">
                        <label class="form-label rhx-label">Status</label>
                        <select class="form-select rhx-input" name="status">
                            <option value="">Todos</option>
                            <option value="em_exercicio" @selected($status==='em_exercicio')>Em Exercício</option>
                            <option value="em_licenca" @selected($status==='em_licenca')>Em Licença</option>
                            <option value="desligado" @selected($status==='desligado')>Desligado</option>
                            <option value="estagio" @selected($status==='estagio')>Estágio</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4 col-lg-2">
                        <label class="form-label rhx-label">Equipe</label>
                        <input class="form-control rhx-input" name="equipe" value="{{ $equipe }}" placeholder="A, B, ALPHA...">
                    </div>

                    <div class="col-12 col-md-4 col-lg-2 d-flex gap-2">
                        <button class="btn btn-primary w-100 rhx-btn rhx-btn--primary" type="submit">Filtrar</button>
                        <a class="btn btn-outline-secondary rhx-btn" href="{{ route('rh.hierarquia') }}">Limpar</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="rhx-stageGrid">
            @foreach($blocks as $block)
                <div class="rhx-stage">
                    <div class="rhx-stage__icon">{{ $block['icon'] }}</div>
                    <div class="rhx-stage__meta">
                        <div class="rhx-stage__title">{{ $block['title'] }}</div>
                        <div class="rhx-stage__sub">{{ $block['sub'] }}</div>
                    </div>
                    <div class="rhx-stage__count">{{ $block['items']->count() }}</div>
                </div>
            @endforeach
        </div>

        @foreach($blocks as $block)
            <div class="rhx-panel rhx-panel--section">
                <div class="rhx-panel__head rhx-panel__head--section">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rhx-sectionIcon">{{ $block['icon'] }}</div>
                        <div>
                            <div class="rhx-panel__title">{{ $block['title'] }}</div>
                            <div class="rhx-panel__sub">{{ $block['sub'] }}</div>
                        </div>
                    </div>

                    <div class="rhx-muted">
                        Exibindo <b>{{ $block['items']->count() }}</b> registro(s)
                    </div>
                </div>

                <div class="rhx-panel__body">
                    @if($block['items']->count())
                        <div class="rhx-tableWrap">
                            <table class="table rhx-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:44px;"></th>
                                        <th>Nome</th>
                                        <th>Equipe</th>
                                        <th>Cargo</th>
                                        <th>Status</th>
                                        <th>Serial</th>
                                        <th>Admissão</th>
                                        <th>Últ. promoção</th>
                                        <th class="text-center">Instr.</th>
                                        <th class="text-end" style="width:170px;">Ações</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($block['items'] as $i => $r)
                                        @php
                                            $collapseId = $block['key'].'-'.$i.'-'.$r->id;
                                        @endphp

                                        <tr class="rhx-row-main">
                                            <td>
                                                <button class="rhx-expandBtn" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="false" aria-controls="{{ $collapseId }}">
                                                    +
                                                </button>
                                            </td>
                                            <td>
                                                <div class="rhx-nameCell">
                                                    <div class="rhx-name">{{ $r->nome ?? '—' }}</div>
                                                    @if($r->cpf)
                                                        <div class="rhx-nameSub">CPF: {{ $r->cpf }}</div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $r->equipe ?? '—' }}</td>
                                            <td>{{ $r->cargo ?? '—' }}</td>
                                            <td>
                                                <span class="{{ $statusClass($r->status ?? null) }}">
                                                    {{ $statusLabel($r->status ?? null) }}
                                                </span>
                                            </td>
                                            <td>{{ $r->serial ?? '—' }}</td>
                                            <td>{{ $r->admissao?->format('d/m/Y') ?? '—' }}</td>
                                            <td>{{ $r->ultima_promocao?->format('d/m/Y') ?? '—' }}</td>
                                            <td class="text-center">
                                                {!! $r->instrutor ? '<span class="pill pill--yes">✓</span>' : '<span class="pill pill--no">—</span>' !!}
                                            </td>
                                            <td class="text-end">
                                                <div class="rhx-actions">
                                                    @if($canEdit)
                                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('rh.hierarquia.edit', $r) }}">
                                                            Editar
                                                        </a>
                                                    @endif

                                                    @if($canDelete)
                                                        <form method="POST" action="{{ route('rh.hierarquia.destroy', $r) }}"
                                                              onsubmit="return confirm('Remover este registro?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger">Excluir</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        <tr class="rhx-row-detail">
                                            <td colspan="10" class="rhx-detailCell p-0">
                                                <div id="{{ $collapseId }}" class="collapse">
                                                    <div class="rhx-detailBox">
                                                        <div class="rhx-detailGrid">
                                                            <div class="rhx-detailItem">
                                                                <span class="rhx-detailLabel">Discord</span>
                                                                <span class="rhx-detailValue">
                                                                    @if($r->discord_id)
                                                                        <button type="button" class="copy rhx-copy" data-copy="{{ $r->discord_id }}">
                                                                            {{ $r->discord_id }}
                                                                        </button>
                                                                    @else
                                                                        —
                                                                    @endif
                                                                </span>
                                                            </div>

                                                            <div class="rhx-detailItem">
                                                                <span class="rhx-detailLabel">Medalhas</span>
                                                                <span class="rhx-detailValue">{{ $r->medalhas ?? '—' }}</span>
                                                            </div>

                                                            <div class="rhx-detailItem">
                                                                <span class="rhx-detailLabel">Alinhamento</span>
                                                                <span class="rhx-detailValue">{{ $r->alinhamento ?? '—' }}</span>
                                                            </div>

                                                            <div class="rhx-detailItem">
                                                                <span class="rhx-detailLabel">Efetivação</span>
                                                                <span class="rhx-detailValue">{{ $r->efetivacao ?? '—' }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="rhx-obsBox">
                                                            <span class="rhx-detailLabel">Função / Observação</span>
                                                            <div class="rhx-obsText">{{ $r->funcao_obs ?? '—' }}</div>
                                                        </div>

                                                        <div class="rhx-badges">
                                                            <div class="rhx-badgeItem"><span>POP</span>{!! $boolPill($r->pop) !!}</div>
                                                            <div class="rhx-badgeItem"><span>CLT</span>{!! $boolPill($r->clt) !!}</div>
                                                            <div class="rhx-badgeItem"><span>CAP</span>{!! $boolPill($r->cap) !!}</div>
                                                            <div class="rhx-badgeItem"><span>CTB</span>{!! $boolPill($r->ctb) !!}</div>
                                                            <div class="rhx-badgeItem"><span>CTA</span>{!! $boolPill($r->cta) !!}</div>
                                                            <div class="rhx-badgeItem"><span>SAT-B</span>{!! $boolPill($r->satb) !!}</div>
                                                            <div class="rhx-badgeItem"><span>BOPM</span>{!! $boolPill($r->bopm) !!}</div>
                                                            <div class="rhx-badgeItem"><span>GMP</span>{!! $boolPill($r->gmp) !!}</div>
                                                            <div class="rhx-badgeItem"><span>DOA</span>{!! $boolPill($r->doa) !!}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="rhx-empty">
                            Nenhum registro encontrado nesta etapa.
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .rhx-wrap{
        padding: 18px 18px 42px;
        color: rgba(226,232,240,.92);
    }

    .rhx-container{
        max-width: 1400px;
        margin: 0 auto;
    }

    .rhx-alert{
        border-radius: 16px;
    }

    .rhx-muted{
        color: rgba(148,163,184,.88);
        font-size: 13px;
    }

    .rhx-hero{
        position: relative;
        overflow: hidden;
        margin: 8px auto 18px;
        max-width: 1400px;
        border-radius: 26px;
        border: 1px solid rgba(148,163,184,.18);
        background: linear-gradient(180deg, rgba(2,6,23,.82), rgba(2,6,23,.70));
        box-shadow: 0 24px 60px rgba(0,0,0,.35);
    }

    .rhx-hero__bg{
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(1200px 280px at 12% 0%, rgba(59,130,246,.24), transparent 60%),
            radial-gradient(1000px 280px at 88% 0%, rgba(16,185,129,.16), transparent 60%),
            linear-gradient(180deg, rgba(255,255,255,.03), transparent 42%);
    }

    .rhx-hero__inner{
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        padding: 24px;
        flex-wrap: wrap;
    }

    .rhx-kicker{
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: rgba(226,232,240,.62);
    }

    .rhx-title{
        margin: 6px 0 4px;
        font-size: 32px;
        line-height: 1.05;
        font-weight: 900;
        letter-spacing: -.03em;
        color: #f8fafc;
    }

    .rhx-sub{
        max-width: 760px;
        color: rgba(226,232,240,.74);
        font-size: 14px;
    }

    .rhx-chips{
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .rhx-chip{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,.18);
        background: rgba(15,23,42,.62);
        color: rgba(226,232,240,.9);
        font-size: 13px;
    }

    .dot{
        width: 8px;
        height: 8px;
        border-radius: 999px;
        display: inline-block;
    }

    .dot--blue{ background: #3b82f6; }
    .dot--green{ background: #10b981; }
    .dot--amber{ background: #f59e0b; }
    .dot--red{ background: #ef4444; }
    .dot--gray{ background: #94a3b8; }

    .rhx-btn{
        border-radius: 14px;
        padding: 10px 14px;
        font-weight: 800;
    }

    .rhx-btn--primary{
        box-shadow: 0 12px 28px rgba(37,99,235,.28);
    }

    .rhx-panel{
        border-radius: 22px;
        border: 1px solid rgba(148,163,184,.16);
        background: rgba(2,6,23,.72);
        box-shadow: 0 16px 40px rgba(0,0,0,.28);
        overflow: hidden;
    }

    .rhx-panel + .rhx-panel{
        margin-top: 16px;
    }

    .rhx-panel__head{
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        padding: 18px 18px 0;
        flex-wrap: wrap;
    }

    .rhx-panel__head--section{
        padding-bottom: 8px;
    }

    .rhx-panel__body{
        padding: 18px;
    }

    .rhx-panel__title{
        font-size: 19px;
        font-weight: 900;
        letter-spacing: -.02em;
        color: #f8fafc;
    }

    .rhx-panel__sub{
        color: rgba(226,232,240,.70);
        font-size: 13px;
        margin-top: 2px;
    }

    .rhx-label{
        font-size: 12px;
        font-weight: 800;
        color: rgba(226,232,240,.72);
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .rhx-input{
        border-radius: 14px;
        background: rgba(15,23,42,.82) !important;
        border: 1px solid rgba(148,163,184,.22) !important;
        color: #f8fafc !important;
    }

    .rhx-input::placeholder{
        color: rgba(226,232,240,.42) !important;
    }

    .rhx-stageGrid{
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 16px;
    }

    .rhx-stage{
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        border-radius: 18px;
        border: 1px solid rgba(148,163,184,.16);
        background: rgba(2,6,23,.72);
        box-shadow: 0 12px 30px rgba(0,0,0,.22);
    }

    .rhx-stage__icon,
    .rhx-sectionIcon{
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(circle at 30% 30%, rgba(59,130,246,.24), transparent 55%),
            radial-gradient(circle at 70% 70%, rgba(16,185,129,.18), transparent 55%),
            rgba(15,23,42,.88);
        border: 1px solid rgba(148,163,184,.18);
        font-size: 22px;
        flex: 0 0 auto;
    }

    .rhx-stage__meta{
        min-width: 0;
        flex: 1 1 auto;
    }

    .rhx-stage__title{
        font-weight: 900;
        color: #f8fafc;
        line-height: 1.1;
    }

    .rhx-stage__sub{
        font-size: 12px;
        color: rgba(226,232,240,.65);
        margin-top: 3px;
    }

    .rhx-stage__count{
        min-width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 15px;
        color: #f8fafc;
        background: rgba(59,130,246,.16);
        border: 1px solid rgba(59,130,246,.22);
    }

    .rhx-tableWrap{
        overflow-x: auto;
        border-radius: 18px;
        border: 1px solid rgba(148,163,184,.12);
    }

    .rhx-table{
        margin: 0;
        color: rgba(226,232,240,.94) !important;
        --bs-table-bg: transparent;
        --bs-table-striped-bg: transparent;
        --bs-table-hover-bg: rgba(255,255,255,.03);
        --bs-table-border-color: rgba(148,163,184,.10);
    }

    .rhx-table thead th{
        background: rgba(15,23,42,.92) !important;
        color: rgba(226,232,240,.78) !important;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
        border-bottom: 1px solid rgba(148,163,184,.16) !important;
        padding: 14px 12px;
        white-space: nowrap;
    }

    .rhx-table tbody tr,
    .rhx-table tbody td,
    .rhx-table tbody th{
        color: rgba(241,245,249,.96) !important;
    }

    .rhx-table tbody td{
        background: rgba(2,6,23,.56) !important;
        border-top: 1px solid rgba(148,163,184,.08) !important;
        padding: 12px;
        vertical-align: middle;
    }

    .rhx-row-main:hover td{
        background: rgba(15,23,42,.74) !important;
    }

    .rhx-row-detail td{
        background: rgba(2,6,23,.44) !important;
    }

    .rhx-table tbody td:not(.text-center):not(.text-end){
        color: rgba(241,245,249,.95) !important;
    }

    .rhx-table tbody td small,
    .rhx-table tbody td span,
    .rhx-table tbody td div{
        color: inherit;
    }

    .rhx-name{
        font-weight: 800;
        color: #f8fafc !important;
        line-height: 1.1;
    }

    .rhx-nameSub{
        margin-top: 4px;
        font-size: 12px;
        color: rgba(148,163,184,.88) !important;
    }

    .rhx-table tbody td:nth-child(3),
    .rhx-table tbody td:nth-child(4),
    .rhx-table tbody td:nth-child(6),
    .rhx-table tbody td:nth-child(7),
    .rhx-table tbody td:nth-child(8){
        color: rgba(241,245,249,.95) !important;
        font-weight: 500;
    }

    .rhx-expandBtn{
        width: 30px;
        height: 30px;
        border-radius: 10px;
        border: 1px solid rgba(148,163,184,.16);
        background: rgba(15,23,42,.84) !important;
        color: #f8fafc !important;
        font-weight: 900;
        cursor: pointer;
        transition: .18s ease;
    }

    .rhx-expandBtn:hover{
        background: rgba(59,130,246,.18) !important;
        border-color: rgba(59,130,246,.28) !important;
    }

    .rhx-actions{
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .rhx-actions .btn{
        position: relative;
        z-index: 2;
    }

    .rhx-detailCell{
        border-top: 0 !important;
    }

    .rhx-detailBox{
        padding: 16px;
        background: linear-gradient(180deg, rgba(15,23,42,.66), rgba(2,6,23,.70)) !important;
        border-top: 1px dashed rgba(148,163,184,.14);
        color: rgba(241,245,249,.95) !important;
    }

    .rhx-detailGrid{
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .rhx-detailItem{
        padding: 12px;
        border-radius: 14px;
        background: rgba(2,6,23,.46) !important;
        border: 1px solid rgba(148,163,184,.10);
        color: rgba(241,245,249,.95) !important;
    }

    .rhx-detailLabel{
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: rgba(148,163,184,.76) !important;
    }

    .rhx-detailValue{
        color: #f8fafc !important;
        font-weight: 700;
        word-break: break-word;
    }

    .rhx-obsBox{
        padding: 14px;
        border-radius: 14px;
        background: rgba(2,6,23,.46) !important;
        border: 1px solid rgba(148,163,184,.10);
        margin-bottom: 14px;
    }

    .rhx-obsText{
        color: rgba(241,245,249,.90) !important;
        line-height: 1.6;
    }

    .rhx-badges{
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 10px;
    }

    .rhx-badgeItem{
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(2,6,23,.46) !important;
        border: 1px solid rgba(148,163,184,.10);
        color: #f8fafc !important;
        font-weight: 700;
    }

    .copy{
        cursor: pointer;
        border: 1px solid rgba(148,163,184,.16);
        background: rgba(15,23,42,.78) !important;
        color: #f8fafc !important;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 800;
    }

    .copy:hover{
        background: rgba(59,130,246,.14) !important;
        border-color: rgba(59,130,246,.28) !important;
    }

    .st{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-weight: 800;
        font-size: 12px;
        border: 1px solid rgba(148,163,184,.14);
        background: rgba(15,23,42,.74);
        color: rgba(226,232,240,.84);
        white-space: nowrap;
    }

    .st--ok{
        background: rgba(16,185,129,.12) !important;
        border-color: rgba(16,185,129,.22) !important;
        color: #34d399 !important;
    }

    .st--warn{
        background: rgba(245,158,11,.12) !important;
        border-color: rgba(245,158,11,.24) !important;
        color: #fbbf24 !important;
    }

    .st--bad{
        background: rgba(239,68,68,.12) !important;
        border-color: rgba(239,68,68,.24) !important;
        color: #f87171 !important;
    }

    .st--soft{
        background: rgba(148,163,184,.12) !important;
        border-color: rgba(148,163,184,.20) !important;
        color: rgba(226,232,240,.80) !important;
    }

    .pill{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        padding: 4px 8px;
        border-radius: 10px;
        font-weight: 900;
        font-size: 12px;
        border: 1px solid rgba(148,163,184,.14);
        background: rgba(15,23,42,.76);
        color: rgba(226,232,240,.74);
    }

    .pill--yes{
        background: rgba(16,185,129,.12);
        border-color: rgba(16,185,129,.22);
        color: #34d399;
    }

    .pill--no{
        background: rgba(148,163,184,.10);
        border-color: rgba(148,163,184,.18);
        color: rgba(226,232,240,.62);
    }

    .rhx-empty{
        padding: 24px;
        border-radius: 18px;
        border: 1px dashed rgba(148,163,184,.18);
        background: rgba(15,23,42,.46);
        color: rgba(226,232,240,.68);
        text-align: center;
        font-weight: 700;
    }

    @media (max-width: 1200px){
        .rhx-stageGrid{
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rhx-detailGrid{
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rhx-badges{
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px){
        .rhx-wrap{
            padding: 14px 14px 34px;
        }

        .rhx-title{
            font-size: 26px;
        }

        .rhx-stageGrid{
            grid-template-columns: 1fr;
        }

        .rhx-detailGrid,
        .rhx-badges{
            grid-template-columns: 1fr;
        }

        .rhx-table thead th:nth-child(7),
        .rhx-table thead th:nth-child(8),
        .rhx-table tbody td:nth-child(7),
        .rhx-table tbody td:nth-child(8){
            display: none;
        }
    }
</style>

<script>
    (function(){
        document.addEventListener('click', async function(e){
            const copyEl = e.target.closest('.copy');
            if(copyEl){
                const text = copyEl.getAttribute('data-copy') || copyEl.textContent.trim();

                try{
                    await navigator.clipboard.writeText(text);
                    const old = copyEl.textContent;
                    copyEl.textContent = 'Copiado!';
                    setTimeout(() => copyEl.textContent = old, 900);
                }catch(err){
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                }
                return;
            }

            const btn = e.target.closest('.rhx-expandBtn');
            if(btn){
                setTimeout(() => {
                    btn.textContent = btn.getAttribute('aria-expanded') === 'true' ? '−' : '+';
                }, 120);
            }
        });
    })();
</script>
@endsection