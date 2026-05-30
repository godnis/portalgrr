@extends('layouts.app')

@section('content')
@php
    $st = $status ?? 'pendente';

    $statusLabel = match(mb_strtolower((string) $st)) {
        'aprovado' => 'Aprovadas',
        'reprovado' => 'Reprovadas',
        'todos', 'todas', 'all' => 'Todas',
        default => 'Pendentes',
    };

    $copyMessageTop = session('copy_message') ?? session('credenciais');
    $approvedCreds = session('approved_creds', []);
    $totalItems = method_exists($solicitacoes, 'total') ? $solicitacoes->total() : $solicitacoes->count();

    $delegaveis = $delegaveis ?? collect();
    $usuariosComAcessoSolicitacoes = $usuariosComAcessoSolicitacoes ?? collect();

    $auth = auth()->user();
    $canManageSolicAccess = $auth && (int) ($auth->nivel ?? 0) >= 9;
@endphp

<div class="container py-4 solic3-page">

    <div class="solic3-hero mb-4">
        <div class="solic3-hero__glow solic3-hero__glow--blue"></div>
        <div class="solic3-hero__glow solic3-hero__glow--gold"></div>

        <div class="solic3-hero__inner">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div class="solic3-hero__left">
                    <div class="solic3-kicker">PAINEL ADMINISTRATIVO</div>
                    <h2 class="solic3-title mb-2">Solicitações de Acesso</h2>
                    <p class="solic3-sub mb-0">
                        Central de análise para aprovação, reprovação e ajuste de cadastros enviados ao sistema.
                    </p>
                </div>

                <div class="solic3-hero__stats">
                    <div class="solic3-stat">
                        <span class="solic3-stat__label">Filtro atual</span>
                        <span class="solic3-stat__value">{{ $statusLabel }}</span>
                    </div>

                    <div class="solic3-stat">
                        <span class="solic3-stat__label">Registros</span>
                        <span class="solic3-stat__value">{{ number_format((int) $totalItems, 0, ',', '.') }}</span>
                    </div>

                    <div class="solic3-stat">
                        <span class="solic3-stat__label">Permissão base</span>
                        <span class="solic3-stat__value">Nível 10+</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="solic3-toolbar card border-0 mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div>
                    <h5 class="mb-1 fw-bold">Filtros e pesquisa</h5>
                    <div class="text-muted small">
                        Refine a listagem por status ou pesquise por nome, RG e e-mail.
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="solic3-toolbar__hint">
                        Você pode liberar este módulo para pessoas específicas.
                    </div>

                    @if($canManageSolicAccess)
                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#gerenciarAcessosModal"
                        >
                            Gerenciar acessos
                        </button>
                    @endif
                </div>
            </div>

            @if($usuariosComAcessoSolicitacoes->isNotEmpty())
                <div class="solic3-global-access mb-3">
                    <div class="solic3-global-access__label">Usuários com acesso ao módulo:</div>
                    <div class="solic3-global-access__list">
                        @foreach($usuariosComAcessoSolicitacoes->take(8) as $u)
                            <span class="solic3-mini-pill">
                                {{ $u->name }}
                            </span>
                        @endforeach

                        @if($usuariosComAcessoSolicitacoes->count() > 8)
                            <span class="solic3-mini-pill is-more">
                                +{{ $usuariosComAcessoSolicitacoes->count() - 8 }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <form class="row g-2 align-items-end solic3-filter" method="GET" action="{{ route('admin.solicitacoes.index') }}">
                <div class="col-12 col-lg-3">
                    <label class="form-label solic3-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="pendente" @selected($st==='pendente')>Pendente</option>
                        <option value="aprovado" @selected($st==='aprovado')>Aprovado</option>
                        <option value="reprovado" @selected($st==='reprovado')>Reprovado</option>
                        <option value="todos" @selected(in_array(mb_strtolower($st), ['todos','todas','all'], true))>Todos</option>
                    </select>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label solic3-label">Pesquisar</label>
                    <input
                        name="q"
                        class="form-control"
                        placeholder="Buscar por nome, sobrenome, RG ou e-mail..."
                        value="{{ $q ?? '' }}"
                    >
                </div>

                <div class="col-12 col-lg-3">
                    <div class="d-grid d-sm-flex gap-2">
                        <button class="btn btn-primary flex-fill">
                            Filtrar resultados
                        </button>

                        <a href="{{ route('admin.solicitacoes.index') }}" class="btn btn-outline-secondary">
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger solic3-alert mb-3">
            <div class="solic3-alert__icon">!</div>
            <div>
                <div class="fw-bold mb-1">Não foi possível concluir a ação</div>
                <div>{{ $errors->first() }}</div>
            </div>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success solic3-alert mb-3">
            <div class="solic3-alert__icon">✓</div>
            <div>
                <div class="fw-bold mb-1">Operação realizada com sucesso</div>
                <div>{{ session('status') }}</div>
            </div>
        </div>
    @endif

    @if (!empty($copyMessageTop))
        <div class="solic3-copybox mb-4">
            <div class="solic3-copybox__content">
                <div class="solic3-copybox__header">
                    <div>
                        <div class="solic3-copybox__kicker">MENSAGEM PRONTA</div>
                        <h5 class="mb-1 fw-bold">Texto para envio no Discord</h5>
                        <p class="mb-0 text-muted">
                            Copie a mensagem abaixo e envie diretamente ao usuário aprovado.
                        </p>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button
                            type="button"
                            class="btn btn-sm btn-primary"
                            data-copy-text="{{ $copyMessageTop }}"
                        >
                            Copiar mensagem
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary"
                            onclick="document.getElementById('copyMessagePre')?.scrollIntoView({behavior:'smooth', block:'center'});"
                        >
                            Ver conteúdo
                        </button>
                    </div>
                </div>

                <pre class="solic3-copybox__pre" id="copyMessagePre">{{ $copyMessageTop }}</pre>

                <div class="solic3-copybox__footer">
                    Dica: após copiar, revise rapidamente nome, e-mail e senha antes de enviar ao usuário.
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 solic3-tablecard">
        <div class="card-header solic3-tablecard__head">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="mb-1 fw-bold">Listagem de solicitações</h5>
                    <div class="text-muted small">
                        Gerencie aprovações, reprovações e correções cadastrais em um único lugar.
                    </div>
                </div>

                <div class="solic3-chip">
                    {{ $statusLabel }}
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 solic3-table">
                    <thead>
                        <tr>
                            <th style="width: 90px;">#ID</th>
                            <th>Solicitante</th>
                            <th style="width: 150px;">RG</th>
                            <th style="width: 280px;">E-mail</th>
                            <th style="width: 180px;">Status</th>
                            <th class="text-end" style="width: 420px;">Ações</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($solicitacoes as $s)
                            @php
                                $credRow = $approvedCreds[$s->id] ?? null;
                                $rowMsg  = is_array($credRow) ? ($credRow['msg'] ?? '') : '';

                                $badgeClass = match($s->status) {
                                    'pendente'  => 'is-pending',
                                    'aprovado'  => 'is-approved',
                                    'reprovado' => 'is-rejected',
                                    default     => 'is-neutral',
                                };

                                $statusText = match($s->status) {
                                    'pendente'  => 'Pendente',
                                    'aprovado'  => 'Aprovado',
                                    'reprovado' => 'Reprovado',
                                    default     => ucfirst((string) $s->status),
                                };
                            @endphp

                            <tr>
                                <td>
                                    <div class="solic3-id">#{{ $s->id }}</div>
                                </td>

                                <td>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="solic3-avatar">
                                            {{ mb_strtoupper(mb_substr((string) $s->nome, 0, 1) . mb_substr((string) $s->sobrenome, 0, 1)) }}
                                        </div>

                                        <div>
                                            <div class="fw-bold solic3-name">
                                                {{ $s->nome }} {{ $s->sobrenome }}
                                            </div>

                                            <div class="text-muted small">
                                                Enviado em {{ $s->created_at?->format('d/m/Y H:i') ?? '—' }}
                                                @if($s->ip)
                                                    • IP <span class="font-monospace">{{ $s->ip }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="solic3-mono">{{ $s->rg }}</span>
                                </td>

                                <td>
                                    <span class="solic3-email" title="{{ $s->email }}">
                                        {{ $s->email }}
                                    </span>
                                </td>

                                <td>
                                    <span class="solic3-badge {{ $badgeClass }}">
                                        {{ $statusText }}
                                    </span>

                                    @if($s->status === 'reprovado' && $s->motivo)
                                        <div class="text-muted small mt-2">
                                            {{ $s->motivo }}
                                        </div>
                                    @endif
                                </td>

                                <td class="text-end">
                                    @if($s->status === 'pendente')
                                        <div class="d-flex flex-wrap justify-content-end gap-2">
                                            @if(Route::has('admin.solicitacoes.show'))
                                                <a href="{{ route('admin.solicitacoes.show', $s->id) }}" class="btn btn-sm btn-outline-dark">
                                                    Ver
                                                </a>
                                            @endif

                                            @if(Route::has('admin.solicitacoes.edit'))
                                                <a href="{{ route('admin.solicitacoes.edit', $s->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    Editar
                                                </a>
                                            @endif

                                            <form method="POST" action="{{ route('admin.solicitacoes.aprovar', $s->id) }}" class="d-inline">
                                                @csrf
                                                <button
                                                    class="btn btn-sm btn-success"
                                                    onclick="return confirm('Aprovar e criar usuário?')"
                                                >
                                                    Aprovar
                                                </button>
                                            </form>

                                            <button
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reprovarModal{{ $s->id }}"
                                            >
                                                Reprovar
                                            </button>
                                        </div>

                                        <div class="modal fade solic3-modal" id="reprovarModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <div>
                                                            <h5 class="modal-title fw-bold mb-1">Reprovar solicitação</h5>
                                                            <div class="text-muted small">
                                                                Registre o motivo para manter histórico mais claro.
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <form method="POST" action="{{ route('admin.solicitacoes.reprovar', $s->id) }}">
                                                        @csrf

                                                        <div class="modal-body">
                                                            <div class="solic3-userresume mb-3">
                                                                <div class="fw-bold">{{ $s->nome }} {{ $s->sobrenome }}</div>
                                                                <div class="text-muted small">
                                                                    RG <span class="font-monospace">{{ $s->rg }}</span>
                                                                    •
                                                                    <span class="font-monospace">{{ $s->email }}</span>
                                                                </div>
                                                            </div>

                                                            <label class="form-label fw-bold">Motivo da reprovação <span class="text-muted fw-normal"></span></label>
                                                            <textarea
                                                                name="motivo"
                                                                class="form-control"
                                                                rows="4"
                                                                placeholder="Ex.: Dados inconsistentes, RG inválido, cadastro incompleto, divergência de informações, etc."
                                                                required
                                                            ></textarea>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-danger">Confirmar reprovação</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @elseif($s->status === 'aprovado')
                                        <div class="d-flex flex-wrap justify-content-end gap-2">
                                            @if(Route::has('admin.solicitacoes.show'))
                                                <a href="{{ route('admin.solicitacoes.show', $s->id) }}" class="btn btn-sm btn-outline-dark">
                                                    Ver
                                                </a>
                                            @endif

                                            @if(!empty($rowMsg))
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-copy-text="{{ $rowMsg }}"
                                                    title="Copiar mensagem de acesso"
                                                >
                                                    Copiar mensagem
                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#msgModal{{ $s->id }}"
                                                >
                                                    Visualizar
                                                </button>
                                            @endif
                                        </div>

                                        @if(!empty($rowMsg))
                                            <div class="modal fade solic3-modal" id="msgModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div>
                                                                <h5 class="modal-title fw-bold mb-1">Mensagem de acesso</h5>
                                                                <div class="text-muted small">{{ $s->email }}</div>
                                                            </div>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <pre class="solic3-copybox__pre" style="max-height: 420px; overflow:auto;">{{ $rowMsg }}</pre>
                                                            <div class="text-muted small mt-2">
                                                                Utilize esta mensagem para envio ao usuário aprovado.
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                                                            <button type="button" class="btn btn-primary" data-copy-text="{{ $rowMsg }}">Copiar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="d-flex flex-wrap justify-content-end gap-2">
                                            @if(Route::has('admin.solicitacoes.show'))
                                                <a href="{{ route('admin.solicitacoes.show', $s->id) }}" class="btn btn-sm btn-outline-dark">
                                                    Ver
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="solic3-empty">
                                        <div class="solic3-empty__icon">📭</div>
                                        <h5 class="fw-bold mb-2">Nenhuma solicitação encontrada</h5>
                                        <p class="text-muted mb-0">
                                            Não existem registros compatíveis com os filtros aplicados no momento.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4 solic3-pagination">
        {{ $solicitacoes->links() }}
    </div>

</div>

@if($canManageSolicAccess)
<div class="modal fade solic3-modal" id="gerenciarAcessosModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold mb-1">Gerenciar acessos do módulo</h5>
                    <div class="text-muted small">
                        Escolha quais pessoas poderão visualizar, aprovar e reprovar solicitações de acesso.
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('admin.solicitacoes.acessos.update') }}">
                @csrf

                <div class="modal-body">
                    <div class="solic3-userresume mb-3">
                        <div class="fw-bold">Permissão global do módulo</div>
                        <div class="text-muted small">
                            Os usuários selecionados terão acesso completo à tela de solicitações de acesso.
                        </div>
                    </div>

                    <div class="solic3-delegate-head">
                        <div>
                            <div class="fw-bold">Usuários liberados</div>
                            <div class="text-muted small">
                                Marque quem poderá ver, aprovar e reprovar todas as solicitações.
                            </div>
                        </div>

                        <div class="solic3-delegate-actions">
                            <button type="button" class="btn btn-sm btn-outline-secondary js-check-all" data-target="#globalAccessList">
                                Marcar todos
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary js-uncheck-all" data-target="#globalAccessList">
                                Limpar
                            </button>
                        </div>
                    </div>

                    <div class="solic3-delegate-list" id="globalAccessList">
                        @forelse($delegaveis as $user)
                            @php
                                $checked = $usuariosComAcessoSolicitacoes->contains(fn($u) => (int)$u->id === (int)$user->id);
                            @endphp

                            <label class="solic3-delegate-item">
                                <input
                                    type="checkbox"
                                    name="users[]"
                                    value="{{ $user->id }}"
                                    @checked($checked)
                                >
                                <div class="solic3-delegate-item__body">
                                    <div class="solic3-delegate-item__name">
                                        {{ $user->name }}
                                    </div>
                                    <div class="solic3-delegate-item__meta">
                                        RG {{ $user->rg ?? '—' }}
                                        @if(!empty($user->cargo))
                                            • {{ $user->cargo }}
                                        @endif
                                        @if(!empty($user->nivel))
                                            • Nível {{ $user->nivel }}
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="solic3-empty-inline">
                                Nenhum usuário elegível foi enviado para a view.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar acessos</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
.solic3-page{
    --solic-bg:#ffffff;
    --solic-card:#ffffff;
    --solic-text:#0f172a;
    --solic-muted:#64748b;
    --solic-line:rgba(15,23,42,.10);
    --solic-line-soft:rgba(15,23,42,.06);
    --solic-soft:rgba(15,23,42,.03);
    --solic-primary:#1d4ed8;
    --solic-primary-2:#2563eb;
    --solic-gold:#d4af37;
}
.solic3-hero{
    position:relative;
    overflow:hidden;
    border-radius:24px;
    border:1px solid rgba(15,23,42,.08);
    background:
        radial-gradient(circle at top left, rgba(37,99,235,.12), transparent 34%),
        radial-gradient(circle at top right, rgba(212,175,55,.12), transparent 28%),
        linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    box-shadow:0 20px 60px rgba(15,23,42,.08);
}
.solic3-hero__inner{ position:relative; z-index:2; padding:26px 28px; }
.solic3-hero__glow{
    position:absolute; border-radius:999px; filter:blur(40px); opacity:.55; pointer-events:none;
}
.solic3-hero__glow--blue{
    width:240px; height:240px; top:-90px; left:-60px; background:rgba(37,99,235,.18);
}
.solic3-hero__glow--gold{
    width:200px; height:200px; top:-50px; right:-40px; background:rgba(212,175,55,.20);
}
.solic3-kicker{
    font-size:11px; font-weight:900; letter-spacing:.18em; text-transform:uppercase; color:var(--solic-primary); margin-bottom:10px;
}
.solic3-title{
    font-size:clamp(1.4rem, 2vw, 2rem); font-weight:900; letter-spacing:-.02em; color:var(--solic-text);
}
.solic3-sub{
    max-width:760px; color:var(--solic-muted); font-size:.97rem; line-height:1.6;
}
.solic3-hero__stats{ display:flex; flex-wrap:wrap; gap:12px; }
.solic3-stat{
    min-width:140px; padding:14px 16px; border-radius:16px; border:1px solid rgba(15,23,42,.08); background:rgba(255,255,255,.75); backdrop-filter:blur(8px);
}
.solic3-stat__label{
    display:block; font-size:11px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; color:var(--solic-muted); margin-bottom:6px;
}
.solic3-stat__value{
    display:block; font-size:1rem; font-weight:900; color:var(--solic-text);
}
.solic3-toolbar{
    border-radius:22px; background:linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,.96));
    box-shadow:0 12px 32px rgba(15,23,42,.06); border:1px solid rgba(15,23,42,.08)!important;
}
.solic3-toolbar .card-body{ padding:22px; }
.solic3-toolbar__hint{
    padding:10px 14px; border-radius:999px; background:rgba(15,23,42,.04); border:1px solid rgba(15,23,42,.08); color:var(--solic-muted); font-size:12px; font-weight:700;
}
.solic3-global-access{
    padding:14px 16px;
    border-radius:16px;
    border:1px solid rgba(15,23,42,.08);
    background:rgba(15,23,42,.03);
}
.solic3-global-access__label{
    font-size:11px;
    font-weight:900;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:#64748b;
    margin-bottom:8px;
}
.solic3-global-access__list{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}
.solic3-label{
    font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#475569;
}
.solic3-filter .form-select,
.solic3-filter .form-control{
    min-height:46px; border-radius:14px; border:1px solid rgba(15,23,42,.10); background:#fff; box-shadow:none!important;
}
.solic3-filter .form-select:focus,
.solic3-filter .form-control:focus{
    border-color:rgba(37,99,235,.45); box-shadow:0 0 0 .25rem rgba(37,99,235,.10)!important;
}
.solic3-alert{
    display:flex; align-items:flex-start; gap:12px; border:1px solid transparent; border-radius:18px; padding:14px 16px;
}
.solic3-alert__icon{
    width:34px; height:34px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-weight:900; flex-shrink:0; background:rgba(255,255,255,.22);
}
.solic3-copybox{
    border-radius:22px; border:1px solid rgba(15,23,42,.08);
    background:radial-gradient(circle at top right, rgba(37,99,235,.08), transparent 30%), linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    box-shadow:0 14px 36px rgba(15,23,42,.07);
}
.solic3-copybox__content{ padding:22px; }
.solic3-copybox__header{
    display:flex; align-items:flex-start; justify-content:space-between; gap:18px; flex-wrap:wrap; margin-bottom:16px;
}
.solic3-copybox__kicker{
    font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; color:var(--solic-primary); margin-bottom:6px;
}
.solic3-copybox__pre{
    margin:0; padding:16px; border-radius:18px; border:1px solid rgba(15,23,42,.10); background:rgba(15,23,42,.03);
    white-space:pre-wrap; word-break:break-word;
    font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size:12.8px; line-height:1.45; color:#1e293b;
}
.solic3-copybox__footer{
    margin-top:10px; font-size:12px; font-weight:700; color:var(--solic-muted);
}
.solic3-tablecard{
    border-radius:22px; overflow:hidden; border:1px solid rgba(15,23,42,.08)!important; box-shadow:0 16px 40px rgba(15,23,42,.08); background:#fff;
}
.solic3-tablecard__head{
    background:linear-gradient(180deg, rgba(248,250,252,.96), rgba(255,255,255,1)); border-bottom:1px solid rgba(15,23,42,.08); padding:18px 22px;
}
.solic3-chip{
    display:inline-flex; align-items:center; min-height:36px; padding:0 14px; border-radius:999px; background:rgba(37,99,235,.10); color:#1d4ed8;
    font-size:12px; font-weight:900; letter-spacing:.04em; text-transform:uppercase;
}
.solic3-table thead th{
    background:rgba(15,23,42,.03); color:#475569; font-size:11.5px; font-weight:900; letter-spacing:.08em; text-transform:uppercase;
    border-bottom:1px solid rgba(15,23,42,.08); padding:14px 16px;
}
.solic3-table tbody td{
    padding:16px; border-top:1px solid rgba(15,23,42,.06); vertical-align:middle;
}
.solic3-table tbody tr{ transition:.18s ease; }
.solic3-table tbody tr:hover{ background:rgba(37,99,235,.035); }
.solic3-id{
    display:inline-flex; align-items:center; justify-content:center; min-width:52px; min-height:34px; padding:0 10px; border-radius:999px;
    background:rgba(15,23,42,.05); font-weight:900; color:#334155;
}
.solic3-avatar{
    width:44px; height:44px; flex-shrink:0; border-radius:14px; display:inline-flex; align-items:center; justify-content:center;
    font-size:.9rem; font-weight:900; color:#fff; background:linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
}
.solic3-name{ color:#0f172a; }
.solic3-mono{
    font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-weight:800; color:#0f172a;
}
.solic3-email{
    display:inline-block; max-width:240px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
    font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.solic3-badge{
    display:inline-flex; align-items:center; justify-content:center; min-height:34px; padding:0 12px; border-radius:999px; font-size:11.5px; font-weight:900;
    letter-spacing:.06em; text-transform:uppercase; border:1px solid transparent;
}
.solic3-badge.is-pending{ background:rgba(245,159,0,.14); color:#9a6700; border-color:rgba(245,159,0,.22); }
.solic3-badge.is-approved{ background:rgba(25,135,84,.13); color:#0f7a49; border-color:rgba(25,135,84,.20); }
.solic3-badge.is-rejected{ background:rgba(220,53,69,.12); color:#b42318; border-color:rgba(220,53,69,.18); }
.solic3-badge.is-neutral{ background:rgba(100,116,139,.12); color:#475569; border-color:rgba(100,116,139,.18); }
.solic3-mini-pill{
    display:inline-flex;
    align-items:center;
    min-height:26px;
    padding:0 10px;
    border-radius:999px;
    background:rgba(13,110,253,.08);
    border:1px solid rgba(13,110,253,.14);
    color:#1d4ed8;
    font-size:11px;
    font-weight:800;
}
.solic3-mini-pill.is-more{
    background:rgba(15,23,42,.06);
    border-color:rgba(15,23,42,.08);
    color:#475569;
}
.solic3-delegate-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
    margin-bottom:14px;
    flex-wrap:wrap;
}
.solic3-delegate-actions{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}
.solic3-delegate-list{
    display:grid;
    gap:10px;
    max-height:360px;
    overflow:auto;
    padding-right:4px;
}
.solic3-delegate-item{
    display:flex;
    gap:12px;
    align-items:flex-start;
    padding:12px 14px;
    border-radius:16px;
    border:1px solid rgba(15,23,42,.08);
    background:rgba(15,23,42,.03);
    cursor:pointer;
}
.solic3-delegate-item input{
    margin-top:4px;
}
.solic3-delegate-item__name{
    font-weight:800;
    color:#0f172a;
}
.solic3-delegate-item__meta{
    margin-top:2px;
    font-size:12px;
    color:#64748b;
}
.solic3-empty{ padding:46px 20px; text-align:center; }
.solic3-empty__icon{
    width:74px; height:74px; margin:0 auto 16px; border-radius:22px; display:grid; place-items:center; font-size:2rem;
    background:rgba(15,23,42,.04); border:1px solid rgba(15,23,42,.08);
}
.solic3-empty-inline{
    padding:18px;
    border-radius:16px;
    background:rgba(15,23,42,.03);
    border:1px dashed rgba(15,23,42,.10);
    color:#64748b;
    font-size:14px;
}
.solic3-modal .modal-content{
    border-radius:22px; border:1px solid rgba(15,23,42,.10); overflow:hidden; box-shadow:0 28px 80px rgba(15,23,42,.24);
}
.solic3-modal .modal-header{
    border-bottom:1px solid rgba(15,23,42,.08); padding:18px 20px;
    background:radial-gradient(circle at top right, rgba(37,99,235,.06), transparent 24%), linear-gradient(180deg, rgba(248,250,252,.95), rgba(255,255,255,1));
}
.solic3-modal .modal-body{ padding:20px; }
.solic3-modal .modal-footer{
    border-top:1px solid rgba(15,23,42,.08); padding:16px 20px; background:rgba(248,250,252,.62);
}
.solic3-modal .form-control,
.solic3-modal textarea{
    min-height:46px; border-radius:14px; border-color:rgba(15,23,42,.12); box-shadow:none!important;
}
.solic3-modal textarea{ min-height:120px; }
.solic3-userresume{
    padding:12px 14px; border-radius:14px; background:rgba(15,23,42,.03); border:1px solid rgba(15,23,42,.08);
}
.solic3-pagination nav{ display:flex; justify-content:center; }

html[data-theme="dark"] .solic3-hero{
    border-color:rgba(255,255,255,.10);
    background:radial-gradient(circle at top left, rgba(37,99,235,.18), transparent 34%), radial-gradient(circle at top right, rgba(212,175,55,.15), transparent 28%), linear-gradient(135deg, rgba(15,20,28,.98) 0%, rgba(17,24,39,.96) 100%);
    box-shadow:0 22px 60px rgba(0,0,0,.40);
}
html[data-theme="dark"] .solic3-title{ color:rgba(241,245,249,.96); }
html[data-theme="dark"] .solic3-sub{ color:rgba(226,232,240,.66); }
html[data-theme="dark"] .solic3-stat{
    background:rgba(255,255,255,.04); border-color:rgba(255,255,255,.10);
}
html[data-theme="dark"] .solic3-stat__label{ color:rgba(226,232,240,.58); }
html[data-theme="dark"] .solic3-stat__value{ color:rgba(241,245,249,.96); }
html[data-theme="dark"] .solic3-toolbar{
    background:linear-gradient(180deg, rgba(15,20,28,.96), rgba(12,18,28,.94)); border-color:rgba(255,255,255,.10)!important; box-shadow:0 16px 42px rgba(0,0,0,.32);
}
html[data-theme="dark"] .solic3-toolbar .text-muted,
html[data-theme="dark"] .solic3-toolbar__hint{ color:rgba(226,232,240,.66)!important; }
html[data-theme="dark"] .solic3-toolbar__hint{
    background:rgba(148,163,184,.08); border-color:rgba(148,163,184,.16);
}
html[data-theme="dark"] .solic3-global-access{
    background:rgba(148,163,184,.06);
    border-color:rgba(148,163,184,.14);
}
html[data-theme="dark"] .solic3-global-access__label{
    color:rgba(226,232,240,.62);
}
html[data-theme="dark"] .solic3-label{ color:rgba(226,232,240,.72); }
html[data-theme="dark"] .solic3-filter .form-select,
html[data-theme="dark"] .solic3-filter .form-control{
    background:rgba(11,17,26,.96)!important; border-color:rgba(255,255,255,.12)!important; color:rgba(241,245,249,.94)!important;
}
html[data-theme="dark"] .solic3-filter .form-control::placeholder{ color:rgba(226,232,240,.38)!important; }
html[data-theme="dark"] .solic3-copybox{
    border-color:rgba(255,255,255,.10);
    background:radial-gradient(circle at top right, rgba(37,99,235,.14), transparent 30%), linear-gradient(180deg, rgba(15,20,28,.98) 0%, rgba(12,18,28,.96) 100%);
    box-shadow:0 18px 44px rgba(0,0,0,.34);
}
html[data-theme="dark"] .solic3-copybox .text-muted{ color:rgba(226,232,240,.66)!important; }
html[data-theme="dark"] .solic3-copybox__pre{
    background:rgba(10,16,24,.94); border-color:rgba(255,255,255,.10); color:rgba(241,245,249,.92);
}
html[data-theme="dark"] .solic3-copybox__footer{ color:rgba(226,232,240,.64); }
html[data-theme="dark"] .solic3-tablecard{
    background:rgba(15,20,28,.95); border-color:rgba(255,255,255,.10)!important; box-shadow:0 18px 46px rgba(0,0,0,.34);
}
html[data-theme="dark"] .solic3-tablecard__head{
    background:linear-gradient(180deg, rgba(20,27,38,.96), rgba(15,20,28,.98)); border-bottom-color:rgba(255,255,255,.08);
}
html[data-theme="dark"] .solic3-chip{
    background:rgba(59,130,246,.14); color:rgba(191,219,254,.96);
}
html[data-theme="dark"] .solic3-table thead th{
    background:rgba(148,163,184,.08); color:rgba(226,232,240,.72); border-bottom-color:rgba(148,163,184,.16);
}
html[data-theme="dark"] .solic3-table tbody td{
    border-top-color:rgba(148,163,184,.10); color:rgba(241,245,249,.92);
}
html[data-theme="dark"] .solic3-table tbody tr:hover{ background:rgba(59,130,246,.08); }
html[data-theme="dark"] .solic3-id{
    background:rgba(148,163,184,.10); color:rgba(241,245,249,.90);
}
html[data-theme="dark"] .solic3-name,
html[data-theme="dark"] .solic3-mono,
html[data-theme="dark"] .solic3-email{ color:rgba(241,245,249,.94); }
html[data-theme="dark"] .solic3-table .text-muted{ color:rgba(226,232,240,.58)!important; }
html[data-theme="dark"] .solic3-mini-pill{
    background:rgba(59,130,246,.14);
    border-color:rgba(59,130,246,.20);
    color:rgba(191,219,254,.96);
}
html[data-theme="dark"] .solic3-mini-pill.is-more{
    background:rgba(148,163,184,.08);
    border-color:rgba(148,163,184,.14);
    color:rgba(226,232,240,.80);
}
html[data-theme="dark"] .solic3-delegate-item,
html[data-theme="dark"] .solic3-userresume{
    background:rgba(148,163,184,.06);
    border-color:rgba(148,163,184,.14);
}
html[data-theme="dark"] .solic3-delegate-item__name{
    color:rgba(241,245,249,.94);
}
html[data-theme="dark"] .solic3-delegate-item__meta{
    color:rgba(226,232,240,.62);
}
html[data-theme="dark"] .solic3-empty__icon{
    background:rgba(148,163,184,.08); border-color:rgba(148,163,184,.14);
}
html[data-theme="dark"] .solic3-empty-inline{
    background:rgba(148,163,184,.06);
    border-color:rgba(148,163,184,.14);
    color:rgba(226,232,240,.70);
}
html[data-theme="dark"] .solic3-modal .modal-content{
    background:rgba(15,20,28,.98)!important; color:rgba(241,245,249,.94)!important; border-color:rgba(255,255,255,.10);
}
html[data-theme="dark"] .solic3-modal .modal-header{
    background:radial-gradient(circle at top right, rgba(37,99,235,.10), transparent 24%), linear-gradient(180deg, rgba(20,27,38,.96), rgba(15,20,28,.98));
    border-bottom-color:rgba(148,163,184,.16);
}
html[data-theme="dark"] .solic3-modal .modal-footer{
    background:rgba(11,17,26,.78); border-top-color:rgba(148,163,184,.14);
}
html[data-theme="dark"] .solic3-modal .text-muted{ color:rgba(226,232,240,.62)!important; }
html[data-theme="dark"] .solic3-modal .form-control,
html[data-theme="dark"] .solic3-modal textarea{
    background:rgba(11,17,26,.96)!important; border-color:rgba(255,255,255,.12)!important; color:rgba(241,245,249,.94)!important;
}
html[data-theme="dark"] .solic3-userresume{
    background:rgba(148,163,184,.06); border-color:rgba(148,163,184,.14); color:rgba(226,232,240,.72);
}
html[data-theme="dark"] .alert-success{
    background:rgba(25,135,84,.14)!important; color:rgba(241,245,249,.94)!important; border-color:rgba(25,135,84,.22)!important;
}
html[data-theme="dark"] .alert-danger{
    background:rgba(220,53,69,.14)!important; color:rgba(241,245,249,.94)!important; border-color:rgba(220,53,69,.22)!important;
}
@media (max-width:991.98px){
    .solic3-hero__inner, .solic3-toolbar .card-body, .solic3-copybox__content, .solic3-tablecard__head{ padding:18px; }
    .solic3-hero__stats{ width:100%; }
    .solic3-stat{ flex:1 1 150px; }
}
@media (max-width:767.98px){
    .solic3-title{ font-size:1.4rem; }
    .solic3-copybox__header{ flex-direction:column; align-items:stretch; }
    .solic3-table thead th, .solic3-table tbody td{ padding:12px; }
    .solic3-avatar{ width:40px; height:40px; border-radius:12px; }
}
</style>

<script>
(function(){
    async function copyText(text){
        if(!text) return false;

        try{
            if(navigator.clipboard && window.isSecureContext){
                await navigator.clipboard.writeText(text);
                return true;
            }
        }catch(e){}

        try{
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.top = '-1000px';
            ta.style.left = '-1000px';
            document.body.appendChild(ta);
            ta.select();
            ta.setSelectionRange(0, ta.value.length);
            var ok = document.execCommand('copy');
            document.body.removeChild(ta);
            return ok;
        }catch(e){
            return false;
        }
    }

    function setBtnState(btn, ok){
        var original = btn.getAttribute('data-original') || btn.innerText;
        btn.setAttribute('data-original', original);

        if(ok){
            btn.innerText = 'Copiado!';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
        }else{
            btn.innerText = 'Falhou :(';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-danger');
        }

        setTimeout(function(){
            btn.innerText = original;
            btn.classList.remove('btn-success','btn-danger');
            btn.classList.add('btn-outline-primary');
        }, 1800);
    }

    document.addEventListener('click', async function(e){
        var btn = e.target.closest('[data-copy-text]');
        if(btn){
            var text = btn.getAttribute('data-copy-text') || '';
            var ok = await copyText(text);
            setBtnState(btn, ok);
            return;
        }

        var checkAll = e.target.closest('.js-check-all');
        if(checkAll){
            var target = document.querySelector(checkAll.getAttribute('data-target'));
            if(target){
                target.querySelectorAll('input[type="checkbox"]').forEach(function(cb){
                    cb.checked = true;
                });
            }
            return;
        }

        var uncheckAll = e.target.closest('.js-uncheck-all');
        if(uncheckAll){
            var target2 = document.querySelector(uncheckAll.getAttribute('data-target'));
            if(target2){
                target2.querySelectorAll('input[type="checkbox"]').forEach(function(cb){
                    cb.checked = false;
                });
            }
        }
    });
})();
</script>
@endsection