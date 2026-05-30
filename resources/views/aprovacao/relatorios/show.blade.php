@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h5 class="mb-1 fw-semibold">Detalhes do Relatório</h5>
        <div class="text-muted small">
            Unidade: {{ $relatorio->unidade }} • Status:
            <span class="badge text-bg-secondary">{{ strtoupper($relatorio->status) }}</span>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('aprovacao.relatorios.index') }}" class="btn btn-sm btn-outline-secondary">Voltar</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-3">
    <div class="col-md-6">
        <div class="card p-3">
            <div class="fw-semibold mb-2">Identificação</div>

            <div class="row g-2 small">
                <div class="col-6 text-muted">Autor (usuário)</div>
                <div class="col-6">{{ $autor?->name ?? '—' }}</div>

                <div class="col-6 text-muted">Chefe da Barca (RG)</div>
                <div class="col-6">{{ $relatorio->qra_chefe }}</div>

                <div class="col-6 text-muted">Motorista (RG)</div>
                <div class="col-6">{{ $relatorio->motorista }}</div>

                <div class="col-6 text-muted">3º homem</div>
                <div class="col-6">{{ $relatorio->terceiro ?? '—' }}</div>

                <div class="col-6 text-muted">4º homem</div>
                <div class="col-6">{{ $relatorio->quarto ?? '—' }}</div>

                <div class="col-6 text-muted">5º homem</div>
                <div class="col-6">{{ $relatorio->quinto ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card p-3">
            <div class="fw-semibold mb-2">Período</div>

            <div class="row g-2 small">
                <div class="col-6 text-muted">Data</div>
                <div class="col-6">{{ \Carbon\Carbon::parse($relatorio->data_patrulhamento)->format('d/m/Y') }}</div>

                <div class="col-6 text-muted">Início</div>
                <div class="col-6">{{ $relatorio->inicio_patrulhamento }}</div>

                <div class="col-6 text-muted">Final</div>
                <div class="col-6">
                    @if($relatorio->final_patrulhamento)
                        {{ $relatorio->final_patrulhamento }}
                    @else
                        <span class="badge text-bg-warning text-dark">Não finalizado</span>
                    @endif
                </div>
            </div>

            <hr>

            <div class="d-flex gap-2">
                {{-- Aprovar / Reprovar --}}
                <form method="POST" action="{{ route('relatorios.aprovar', $relatorio->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-success"
                            @disabled(empty($relatorio->final_patrulhamento) || $relatorio->status !== 'pendente')>
                        Aprovar
                    </button>
                </form>

                <form method="POST" action="{{ route('relatorios.reprovar', $relatorio->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-danger"
                            @disabled($relatorio->status !== 'pendente')>
                        Reprovar
                    </button>
                </form>

                @if(empty($relatorio->final_patrulhamento))
                    <div class="text-muted small align-self-center">
                        Para aprovar, o relatório deve estar finalizado.
                    </div>
                @endif
            </div>

        </div>
    </div>

    <div class="col-md-12">
        <div class="card p-3">
            <div class="fw-semibold mb-2">Dados de Apreensão (quantidade)</div>

            <div class="row g-2 small">
                <div class="col-md-2 text-muted">Pistolas</div><div class="col-md-2">{{ $relatorio->pistolas ?? '—' }}</div>
                <div class="col-md-2 text-muted">SMG/Fuzil</div><div class="col-md-2">{{ $relatorio->smg_fuzil ?? '—' }}</div>
                <div class="col-md-2 text-muted">Munições</div><div class="col-md-2">{{ $relatorio->municoes ?? '—' }}</div>

                <div class="col-md-2 text-muted">Drogas</div><div class="col-md-2">{{ $relatorio->drogas ?? '—' }}</div>
                <div class="col-md-2 text-muted">Dinheiro Marcado</div><div class="col-md-2">{{ $relatorio->dinheiro ?? '—' }}</div>
                <div class="col-md-2 text-muted">Explosivos</div><div class="col-md-2">{{ $relatorio->explosivos ?? '—' }}</div>

                <div class="col-md-2 text-muted">Lockpicks</div><div class="col-md-2">{{ $relatorio->lockpicks ?? '—' }}</div>
            </div>

            <hr>

            <div class="fw-semibold mb-2">Multas / Apreensões (quantidade)</div>
            <div class="row g-2 small">
                <div class="col-md-2 text-muted">Abordagens</div><div class="col-md-2">{{ $relatorio->abordagens ?? '—' }}</div>
                <div class="col-md-2 text-muted">Multas</div><div class="col-md-2">{{ $relatorio->multas ?? '—' }}</div>
                <div class="col-md-2 text-muted">BOPM</div><div class="col-md-2">{{ $relatorio->bopm ?? '—' }}</div>
                <div class="col-md-2 text-muted">Viaturas Fiscalizadas</div><div class="col-md-2">{{ $relatorio->viaturas_fiscalizadas ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
