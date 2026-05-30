@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h5 class="mb-1 fw-semibold">Aprovação de Relatórios</h5>
        <div class="text-muted small">Fila de pendências • Acesso restrito (Inspetor+)</div>
    </div>
</div>

<div class="card p-3 mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="small text-muted">Unidade</label>
            <select name="unidade" class="form-select form-select-sm">
                <option value="">Todas</option>
                @foreach($unidades as $u)
                    <option value="{{ $u }}" @selected(request('unidade')===$u)>{{ $u }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="small text-muted">RG/CPF (busca)</label>
            <input name="rg" class="form-control form-control-sm" value="{{ request('rg') }}" placeholder="Ex: 123">
        </div>

        <div class="col-md-2">
            <label class="small text-muted">Data início</label>
            <input type="date" name="data_inicio" class="form-control form-control-sm" value="{{ request('data_inicio') }}">
        </div>

        <div class="col-md-2">
            <label class="small text-muted">Data fim</label>
            <input type="date" name="data_fim" class="form-control form-control-sm" value="{{ request('data_fim') }}">
        </div>

        <div class="col-md-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="prontos" value="1" id="prontos"
                       @checked(request('prontos')==='1')>
                <label class="form-check-label small text-muted" for="prontos">
                    Apenas finalizados
                </label>
            </div>
        </div>

        <div class="col-md-1 d-flex gap-2">
            <button class="btn btn-sm btn-primary w-100">Filtrar</button>
        </div>

        <div class="col-md-12">
            <a href="{{ route('aprovacao.relatorios.index') }}" class="btn btn-sm btn-outline-secondary mt-2">Limpar</a>
        </div>
    </form>
</div>

<div class="card p-3">
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead>
                <tr class="text-muted">
                    <th style="width: 160px;">Data/Hora</th>
                    <th style="width: 160px;">Unidade</th>
                    <th style="width: 150px;">Chefe (RG)</th>
                    <th style="width: 160px;">Motorista (RG)</th>
                    <th style="width: 140px;">Status</th>
                    <th style="width: 160px;">Hora Final</th>
                    <th class="text-end" style="width: 190px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($relatorios as $r)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($r->data_patrulhamento)->format('d/m/Y') }} • {{ $r->inicio_patrulhamento }}</td>
                        <td>{{ $r->unidade }}</td>
                        <td>{{ $r->qra_chefe }}</td>
                        <td>{{ $r->motorista }}</td>
                        <td>
                            <span class="badge text-bg-secondary">Pendente</span>
                        </td>
                        <td>
                            @if($r->final_patrulhamento)
                                <span class="badge text-bg-success"> {{ $r->final_patrulhamento }} </span>
                            @else
                                <span class="badge text-bg-warning text-dark">Não finalizado</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('aprovacao.relatorios.show', $r->id) }}" class="btn btn-sm btn-outline-primary">
                                Detalhes
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted">Sem relatórios pendentes com os filtros atuais.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-2">
        {{ $relatorios->links() }}
    </div>
</div>
@endsection
