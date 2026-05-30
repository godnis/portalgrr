@extends('layouts.app')

@section('content')
<div class="tkc-wrap">

  {{-- HERO / TOPO --}}
  <section class="tkc-hero">
    <div class="tkc-hero__bg"></div>

    <div class="tkc-hero__content">
      <div class="tkc-hero__left">
        <div class="tkc-kicker">GRR • PRF — Central de Suporte</div>
        <h1 class="tkc-title">Abrir Ticket</h1>
        <p class="tkc-sub">
          Preencha as informações com clareza para que o setor responsável consiga analisar e responder com mais agilidade.
        </p>

        <div class="tkc-pills">
          <span class="tkc-pill"><span>📂</span> Direcione ao setor correto</span>
          <span class="tkc-pill"><span>📝</span> Informe o máximo de detalhes</span>
          <span class="tkc-pill"><span>⚠️</span> Prioridade urgente só em casos críticos</span>
        </div>
      </div>

      <div class="tkc-hero__right">
        <a class="btn btn-outline-light tkc-back-btn" href="{{ route('tickets.index') }}">
          ← Voltar para meus tickets
        </a>
      </div>
    </div>
  </section>

  @if($errors->any())
    <div class="tkc-alert alert alert-danger mt-3 mb-0">
      <div class="tkc-alert__title">Corrija os campos abaixo antes de continuar:</div>
      <ul class="mb-0 ps-3">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-3 mt-1">
    {{-- FORM --}}
    <div class="col-lg-8">
      <form method="POST" action="{{ route('tickets.store') }}" class="tkc-card">
        @csrf

        <div class="tkc-card__head">
          <div>
            <div class="tkc-card__eyebrow">Formulário de abertura</div>
            <h2 class="tkc-card__title">Informações do chamado</h2>
          </div>
        </div>

        <div class="tkc-card__body">
          <div class="row g-3">

            <div class="col-md-4">
              <label class="tkc-label">Categoria</label>
              <select class="form-select tkc-input" name="categoria" required>
                <option value="">Selecione…</option>
                <option value="suporte_geral" @selected(old('categoria')==='suporte_geral')>Suporte geral</option>
                <option value="tecnico" @selected(old('categoria')==='tecnico')>Técnico</option>
                <option value="administrativo" @selected(old('categoria')==='administrativo')>Administrativo</option>
                <option value="recrutamento" @selected(old('categoria')==='recrutamento')>Recrutamento</option>
                <option value="financeiro" @selected(old('categoria')==='financeiro')>Financeiro</option>
                <option value="denuncia" @selected(old('categoria')==='denuncia')>Denúncia</option>
              </select>
              <div class="tkc-help">Escolha o setor mais adequado para agilizar o atendimento.</div>
            </div>

            <div class="col-md-3">
              <label class="tkc-label">Prioridade</label>
              <select class="form-select tkc-input" name="prioridade" required>
                <option value="normal" @selected(old('prioridade','normal')==='normal')>Normal</option>
                <option value="baixa" @selected(old('prioridade')==='baixa')>Baixa</option>
                <option value="alta" @selected(old('prioridade')==='alta')>Alta</option>
                <option value="urgente" @selected(old('prioridade')==='urgente')>Urgente</option>
              </select>
              <div class="tkc-help">Utilize “Urgente” apenas quando o caso exigir resposta imediata.</div>
            </div>

            <div class="col-md-5">
              <label class="tkc-label">Título</label>
              <input
                class="form-control tkc-input"
                name="titulo"
                value="{{ old('titulo') }}"
                required
                maxlength="120"
                placeholder="Ex.: Problema ao logar / Denúncia / Dúvida"
              >
              <div class="tkc-help">Seja direto. Limite máximo de 120 caracteres.</div>
            </div>

            <div class="col-12">
              <label class="tkc-label">Descrição</label>
              <textarea
                class="form-control tkc-input tkc-textarea"
                name="descricao"
                rows="8"
                required
                maxlength="5000"
                placeholder="Explique o que aconteceu, quando ocorreu, quem está envolvido e como reproduzir o problema, se for um caso técnico."
              >{{ old('descricao') }}</textarea>
              <div class="tkc-help">
                Quanto mais completo o relato, mais fácil será para a equipe analisar e resolver.
              </div>
            </div>

          </div>
        </div>

        <div class="tkc-card__footer">
          <button class="btn btn-primary tkc-submit-btn">
            Abrir Ticket
          </button>

          <a class="btn btn-outline-secondary tkc-cancel-btn" href="{{ route('tickets.index') }}">
            Cancelar
          </a>
        </div>
      </form>
    </div>

    {{-- LATERAL --}}
    <div class="col-lg-4">
      <div class="tkc-side">
        <div class="tkc-side__block">
          <div class="tkc-side__label">Orientações</div>
          <h3 class="tkc-side__title">Antes de enviar</h3>

          <ul class="tkc-side__list">
            <li>Informe o setor correto para evitar redirecionamentos.</li>
            <li>Explique a situação com objetividade e detalhes importantes.</li>
            <li>Em denúncias, inclua horários, nomes e contexto do ocorrido.</li>
            <li>Em casos técnicos, descreva como o erro aconteceu.</li>
          </ul>
        </div>

        <div class="tkc-side__block tkc-side__block--soft">
          <div class="tkc-side__label">Boas práticas</div>
          <h3 class="tkc-side__title">Chamados mais claros são resolvidos mais rápido</h3>
          <p class="tkc-side__text">
            Evite títulos genéricos e procure detalhar o máximo possível no corpo da mensagem.
            Isso ajuda o setor responsável a entender o contexto logo no primeiro atendimento.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* =========================================================
     GRR 3.0 • CREATE TICKET
  ========================================================= */
  .tkc-wrap{
    max-width: 1280px;
    margin: 0 auto;
    padding: 18px 14px 28px;
  }

  /* HERO */
  .tkc-hero{
    position: relative;
    overflow: hidden;
    border-radius: 24px;
    border: 1px solid rgba(2,6,23,.10);
    background: linear-gradient(135deg, rgba(8,14,28,.98), rgba(15,23,42,.94));
    box-shadow:
      0 24px 70px rgba(2,6,23,.18),
      inset 0 1px 0 rgba(255,255,255,.05);
  }

  .tkc-hero__bg{
    position: absolute;
    inset: -60px;
    pointer-events: none;
    opacity: .95;
    background:
      radial-gradient(850px 340px at 12% 18%, rgba(59,130,246,.24), transparent 60%),
      radial-gradient(680px 280px at 88% 25%, rgba(16,185,129,.16), transparent 56%),
      radial-gradient(760px 320px at 50% 110%, rgba(168,85,247,.14), transparent 62%);
    filter: blur(14px);
  }

  .tkc-hero__content{
    position: relative;
    z-index: 1;
    padding: 22px;
    display: flex;
    gap: 18px;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
  }

  .tkc-kicker{
    color: rgba(226,232,240,.68);
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .18em;
    text-transform: uppercase;
    margin-bottom: 10px;
  }

  .tkc-title{
    margin: 0;
    color: #f8fafc;
    font-size: clamp(1.8rem, 2.6vw, 2.45rem);
    line-height: 1.05;
    font-weight: 950;
    letter-spacing: -.02em;
  }

  .tkc-sub{
    margin: 10px 0 0;
    max-width: 760px;
    color: rgba(226,232,240,.78);
    font-size: .98rem;
    line-height: 1.6;
    font-weight: 500;
  }

  .tkc-pills{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 18px;
  }

  .tkc-pill{
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 42px;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.10);
    color: rgba(248,250,252,.92);
    font-size: 12px;
    font-weight: 800;
    backdrop-filter: blur(10px);
  }

  .tkc-back-btn{
    border-radius: 14px;
    font-weight: 900;
    padding: 11px 16px;
    border-color: rgba(255,255,255,.18);
  }

  .tkc-back-btn:hover{
    border-color: rgba(255,255,255,.28);
    background: rgba(255,255,255,.08);
    color: #fff;
  }

  /* ALERT */
  .tkc-alert{
    border-radius: 18px;
    border: 1px solid rgba(220,53,69,.18);
    box-shadow: 0 14px 34px rgba(220,53,69,.08);
    padding: 16px 18px;
  }

  .tkc-alert__title{
    font-weight: 900;
    margin-bottom: 8px;
  }

  /* CARD */
  .tkc-card{
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(15,23,42,.08);
    background: #ffffff;
    box-shadow: 0 18px 50px rgba(15,23,42,.07);
  }

  .tkc-card__head{
    padding: 20px 22px 16px;
    border-bottom: 1px solid rgba(15,23,42,.08);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.06), transparent 30%),
      linear-gradient(180deg, rgba(248,250,252,1), rgba(255,255,255,1));
  }

  .tkc-card__eyebrow{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: rgba(15,23,42,.48);
    margin-bottom: 6px;
  }

  .tkc-card__title{
    margin: 0;
    color: #0f172a;
    font-size: 1.15rem;
    font-weight: 900;
  }

  .tkc-card__body{
    padding: 22px;
  }

  .tkc-card__footer{
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    padding: 16px 22px 22px;
    border-top: 1px solid rgba(15,23,42,.08);
    background: #fafbfc;
  }

  /* INPUTS */
  .tkc-label{
    display: block;
    margin-bottom: 8px;
    color: #0f172a;
    font-size: .82rem;
    font-weight: 900;
    letter-spacing: .02em;
  }

  .tkc-input{
    min-height: 48px;
    border-radius: 14px;
    border: 1px solid rgba(15,23,42,.10);
    box-shadow: none !important;
    font-weight: 600;
    color: #0f172a;
    background-color: #fff;
  }

  .tkc-input:focus{
    border-color: rgba(13,110,253,.45);
    box-shadow: 0 0 0 .22rem rgba(13,110,253,.10) !important;
  }

  .tkc-textarea{
    min-height: 220px;
    resize: vertical;
    padding-top: 12px;
  }

  .tkc-help{
    margin-top: 7px;
    color: rgba(15,23,42,.52);
    font-size: .78rem;
    font-weight: 600;
    line-height: 1.45;
  }

  .tkc-submit-btn,
  .tkc-cancel-btn{
    min-height: 46px;
    border-radius: 14px;
    font-weight: 900;
    padding-inline: 18px;
  }

  .tkc-submit-btn{
    box-shadow: 0 10px 24px rgba(13,110,253,.20);
  }

  /* SIDE */
  .tkc-side{
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .tkc-side__block{
    border-radius: 24px;
    border: 1px solid rgba(15,23,42,.08);
    background: #ffffff;
    box-shadow: 0 18px 50px rgba(15,23,42,.06);
    padding: 20px;
  }

  .tkc-side__block--soft{
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.05), transparent 35%),
      linear-gradient(180deg, rgba(248,250,252,1), rgba(255,255,255,1));
  }

  .tkc-side__label{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: rgba(15,23,42,.48);
    margin-bottom: 8px;
  }

  .tkc-side__title{
    margin: 0 0 12px;
    color: #0f172a;
    font-size: 1.02rem;
    font-weight: 900;
    line-height: 1.35;
  }

  .tkc-side__text{
    margin: 0;
    color: rgba(15,23,42,.62);
    line-height: 1.65;
    font-weight: 500;
  }

  .tkc-side__list{
    margin: 0;
    padding-left: 18px;
    color: rgba(15,23,42,.70);
  }

  .tkc-side__list li{
    margin-bottom: 10px;
    line-height: 1.55;
    font-weight: 600;
  }

  .tkc-side__list li:last-child{
    margin-bottom: 0;
  }

  /* DARK MODE */
  html[data-theme="dark"] .tkc-hero{
    border-color: rgba(255,255,255,.09);
    box-shadow:
      0 26px 70px rgba(0,0,0,.45),
      inset 0 1px 0 rgba(255,255,255,.04);
  }

  html[data-theme="dark"] .tkc-card,
  html[data-theme="dark"] .tkc-side__block{
    background: rgba(10,14,20,.78);
    border-color: rgba(255,255,255,.08);
    box-shadow: 0 18px 50px rgba(0,0,0,.35);
  }

  html[data-theme="dark"] .tkc-card__head{
    border-bottom-color: rgba(255,255,255,.08);
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.08), transparent 30%),
      linear-gradient(180deg, rgba(15,20,28,.96), rgba(10,14,20,.92));
  }

  html[data-theme="dark"] .tkc-card__footer{
    background: rgba(15,20,28,.72);
    border-top-color: rgba(255,255,255,.08);
  }

  html[data-theme="dark"] .tkc-card__eyebrow,
  html[data-theme="dark"] .tkc-side__label{
    color: rgba(226,232,240,.46);
  }

  html[data-theme="dark"] .tkc-card__title,
  html[data-theme="dark"] .tkc-side__title,
  html[data-theme="dark"] .tkc-label{
    color: rgba(248,250,252,.95);
  }

  html[data-theme="dark"] .tkc-help,
  html[data-theme="dark"] .tkc-side__text,
  html[data-theme="dark"] .tkc-side__list{
    color: rgba(226,232,240,.60);
  }

  html[data-theme="dark"] .tkc-input{
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.10);
    color: rgba(248,250,252,.94);
  }

  html[data-theme="dark"] .tkc-input::placeholder,
  html[data-theme="dark"] .tkc-textarea::placeholder{
    color: rgba(226,232,240,.42);
  }

  html[data-theme="dark"] .tkc-input:focus{
    border-color: rgba(147,197,253,.45);
    box-shadow: 0 0 0 .22rem rgba(59,130,246,.12) !important;
    background: rgba(255,255,255,.05);
    color: rgba(248,250,252,.96);
  }

  html[data-theme="dark"] .tkc-side__block--soft{
    background:
      radial-gradient(circle at top right, rgba(59,130,246,.06), transparent 35%),
      linear-gradient(180deg, rgba(15,20,28,.96), rgba(10,14,20,.88));
  }

  /* MOBILE */
  @media (max-width: 991.98px){
    .tkc-hero__content{
      padding: 18px;
    }
  }

  @media (max-width: 767.98px){
    .tkc-wrap{
      padding: 14px 10px 24px;
    }

    .tkc-hero__content{
      padding: 16px;
    }

    .tkc-card__body,
    .tkc-card__footer,
    .tkc-card__head,
    .tkc-side__block{
      padding-left: 16px;
      padding-right: 16px;
    }

    .tkc-submit-btn,
    .tkc-cancel-btn,
    .tkc-back-btn{
      width: 100%;
      justify-content: center;
    }

    .tkc-card__footer{
      flex-direction: column;
    }
  }
</style>
@endsection