@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width:980px;">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
      <div class="text-uppercase small text-muted fw-bold" style="letter-spacing:.08em;">RH • Hierarquia</div>
      <h2 class="fw-black mb-0">{{ $row->exists ? 'Editar Registro' : 'Novo Registro' }}</h2>
      <div class="text-muted">Preencha os dados conforme o quadro de efetivo</div>
    </div>
    <a href="{{ route('rh.hierarquia') }}" class="btn btn-outline-secondary">Voltar</a>
  </div>

  {{-- 🔄 AVISO: SINCRONIZAÇÃO --}}
  <div class="alert alert-info rounded-3">
    🔄 <b>Sincronização ativa:</b> <b>Nome</b>, <b>CPF/RG</b> e <b>Cargo</b> vêm do Efetivo (User) e não podem ser editados aqui.
    <div class="small mt-1 text-muted">Para alterar essas informações, edite diretamente no Efetivo.</div>
  </div>

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Corrija os campos:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST"
        action="{{ $row->exists ? route('rh.hierarquia.update', $row) : route('rh.hierarquia.store') }}">
    @csrf
    @if($row->exists) @method('PUT') @endif

    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3">

          {{-- 🔒 NOME (sync do efetivo) --}}
          <div class="col-md-8">
            <label class="form-label">Nome (Efetivo)</label>
            <input class="form-control bg-light" value="{{ old('nome', $row->nome) }}" readonly>
            <input type="hidden" name="nome" value="{{ old('nome', $row->nome) }}">
          </div>

          {{-- 🔒 CPF/RG (sync do efetivo) --}}
          <div class="col-md-4">
            <label class="form-label">CPF / RG (Efetivo)</label>
            <input class="form-control bg-light" value="{{ old('cpf', $row->cpf) }}" readonly>
            <input type="hidden" name="cpf" value="{{ old('cpf', $row->cpf) }}">
          </div>

          {{-- EQUIPE (manual) --}}
          <div class="col-md-4">
            <label class="form-label">Equipe</label>
            <input class="form-control" name="equipe" value="{{ old('equipe', $row->equipe) }}" placeholder="A, B, ALFA">
          </div>

          {{-- 🔒 CARGO (sync do efetivo) --}}
          <div class="col-md-8">
            <label class="form-label">Cargo (Efetivo)</label>
            <input class="form-control bg-light" value="{{ old('cargo', $row->cargo) }}" readonly>
            <input type="hidden" name="cargo" value="{{ old('cargo', $row->cargo) }}">
          </div>

          {{-- EFETIVAÇÃO (manual) --}}
          <div class="col-md-3">
            <label class="form-label">Efetivação</label>
            <input class="form-control" name="efetivacao" value="{{ old('efetivacao', $row->efetivacao ?? 'efetivo') }}">
          </div>

          {{-- STATUS (manual) --}}
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
              @foreach(['em_exercicio'=>'Em Exercício','em_licenca'=>'Em Licença','desligado'=>'Desligado','estagio'=>'Estágio'] as $k=>$v)
                <option value="{{ $k }}" @selected(old('status', $row->status ?? 'em_exercicio')===$k)>{{ $v }}</option>
              @endforeach
            </select>
          </div>

          {{-- DATAS (manual) --}}
          <div class="col-md-3">
            <label class="form-label">Admissão</label>
            <input type="date" class="form-control" name="admissao" value="{{ old('admissao', optional($row->admissao)->format('Y-m-d')) }}">
          </div>

          <div class="col-md-3">
            <label class="form-label">Últ. Promoção</label>
            <input type="date" class="form-control" name="ultima_promocao" value="{{ old('ultima_promocao', optional($row->ultima_promocao)->format('Y-m-d')) }}">
          </div>

          {{-- SERIAL (manual) --}}
          <div class="col-md-4">
            <label class="form-label">Serial</label>
            <input class="form-control" name="serial" value="{{ old('serial', $row->serial) }}">
          </div>

          {{-- DISCORD (manual) --}}
          <div class="col-md-4">
            <label class="form-label">Discord ID</label>
            <input class="form-control" name="discord_id" value="{{ old('discord_id', $row->discord_id) }}">
          </div>

          {{-- INSTRUTOR (manual) --}}
          <div class="col-md-4">
            <label class="form-label">Instrutor</label>
            <div class="form-check mt-2">
              <input class="form-check-input" type="checkbox" name="instrutor" value="1"
                     @checked((bool) old('instrutor', $row->instrutor))>
              <label class="form-check-label">Marcar como instrutor</label>
            </div>
          </div>

          {{-- FUNÇÃO/OBS (manual) --}}
          <div class="col-12">
            <label class="form-label">Função / Observação</label>
            <input class="form-control" name="funcao_obs" value="{{ old('funcao_obs', $row->funcao_obs) }}" placeholder="Ex.: Diretor Geral, Coordenador Tático, RH, etc.">
          </div>

          {{-- MEDALHAS / ALINHAMENTO (manual) --}}
          <div class="col-md-6">
            <label class="form-label">Medalhas</label>
            <input class="form-control" name="medalhas" value="{{ old('medalhas', $row->medalhas) }}" placeholder="Ex.: 5º Grau, 4º Grau...">
          </div>

          <div class="col-md-6">
            <label class="form-label">Alinhamento</label>
            <input class="form-control" name="alinhamento" value="{{ old('alinhamento', $row->alinhamento) }}" placeholder="Ex.: ALPHA / BRAVO / CHARLIE...">
          </div>

          {{-- QUALIFICAÇÕES (manual) --}}
          <div class="col-12">
            <label class="form-label">Qualificações</label>
            <div class="row g-2">
              @foreach(['pop'=>'POP','clt'=>'CLT','cap'=>'CAP','ctb'=>'CTB','cta'=>'CTA','satb'=>'SAT-B','bopm'=>'BOPM','gmp'=>'GMP','doa'=>'DOA'] as $k=>$label)
                <div class="col-md-2 col-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="{{ $k }}" value="1"
                           @checked((bool) old($k, $row->{$k}))>
                    <label class="form-check-label">{{ $label }}</label>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

        </div>
      </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
      <a href="{{ route('rh.hierarquia') }}" class="btn btn-outline-secondary">Cancelar</a>
      <button class="btn btn-primary">{{ $row->exists ? 'Salvar Alterações' : 'Criar Registro' }}</button>
    </div>

  </form>
</div>
@endsection
