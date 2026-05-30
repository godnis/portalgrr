<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRR • Recrutamento</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="portal-body">

{{-- TOPBAR --}}
<div class="portal-topbar portal-topbar--gov">
    <div class="portal-container">
        <div class="portal-topbar-inner portal-topbar-inner--gov">

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="portal-govbrand">
                    <div class="portal-govlogo">fivem<span>.bc</span></div>
                </div>

                <span class="portal-sep">|</span>

                <span class="portal-muted">
                    Ministério da Justiça e Segurança Pública
                </span>

                <nav class="portal-links d-none d-lg-flex">
                    <a href="{{ url('/') }}" class="portal-toplink">Página inicial</a>
                    <a href="{{ route('comunicados') }}" class="portal-toplink">Comunicados oficiais</a>
                    <a href="{{ route('governo') }}" class="portal-toplink">Governo da Cidade</a>
                    <a href="{{ route('legislacao') }}" class="portal-toplink">Legislação</a>

                    {{-- ATUAL --}}
                    <a
                        href="{{ route('recrutamento') }}"
                        class="portal-toplink {{ request()->routeIs('recrutamento') ? 'is-active' : '' }}"
                        @if(request()->routeIs('recrutamento'))
                            aria-current="page"
                            tabindex="-1"
                            style="pointer-events:none; opacity:.65;"
                        @endif
                    >
                        Recrutamento
                    </a>
                </nav>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button class="portal-iconbtn portal-iconbtn--gov" type="button" title="Alternar tema" id="toggleTheme">◐</button>

                <a href="{{ route('login') }}" class="btn portal-enter-btn portal-enter-btn--gov">
                    Entrar com FIVEM.BC
                </a>
            </div>

        </div>
    </div>
</div>

{{-- HEADER (IGUAL ao Comunicados) --}}
<header class="portal-header portal-header--gov">
    <div class="portal-container">

        <div class="portal-header-inner portal-header-inner--gov">
            <div class="d-flex align-items-center gap-2">
                <button class="portal-menu portal-menu--gov" type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#menuPublico"
                        aria-label="Abrir menu">☰</button>

                <div class="portal-org-title portal-org-title--gov">
                    Recrutamento • GRR
                </div>
            </div>
        </div>

        {{-- PÍLULAS --}}
        <div class="portal-services-bar portal-services-bar--gov">
            <div class="portal-services-row portal-services-row--gov">
                <a href="{{ url('/') }}" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">🏠</span>
                    Página inicial
                    <span class="portal-pill-caret">▾</span>
                </a>

                <a href="#regras" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">📜</span>
                    Regras e Leis da Cidade
                    <span class="portal-pill-caret">▾</span>
                </a>
            </div>
        </div>

    </div>
</header>

{{-- MENU MOBILE/DESKTOP (1 PAINEL SIMPLES) --}}
<div class="offcanvas offcanvas-start portal-offcanvas"
     tabindex="-1"
     id="menuPublico"
     aria-labelledby="menuPublicoLabel">

  <div class="offcanvas-header">
    <h5 class="portal-offcanvas-title" id="menuPublicoLabel">
      <span>Menu</span>
      <span class="portal-offcanvas-badge">GRR • Portal</span>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
  </div>

  <div class="offcanvas-body">

    <div class="portal-offcanvas-brand">
      <div class="kicker">FIVEM.BC</div>
      <div class="title">Grupo de Resposta Rápida</div>
      <div class="desc">Acesso rápido às principais páginas do portal.</div>
    </div>

    {{-- LISTA ÚNICA --}}
    <nav class="portal-simple-nav" aria-label="Navegação do portal">

      <a class="portal-simple-link" href="{{ route('portal') }}">
        <span class="ico">🏠</span>
        <span class="txt">
          <span class="name">Página inicial</span>
          <span class="sub">Voltar ao portal</span>
        </span>
      </a>

      <a class="portal-simple-link" href="{{ route('comunicados') }}">
        <span class="ico">📢</span>
        <span class="txt">
          <span class="name">Comunicados oficiais</span>
          <span class="sub">Avisos e notas do GRR</span>
        </span>
      </a>

      <a class="portal-simple-link" href="{{ route('governo') }}">
        <span class="ico">🏛️</span>
        <span class="txt">
          <span class="name">Governo da cidade</span>
          <span class="sub">Estrutura e informações</span>
        </span>
      </a>

      <a class="portal-simple-link" href="{{ route('legislacao') }}">
        <span class="ico">📚</span>
        <span class="txt">
          <span class="name">Legislação</span>
          <span class="sub">Leis e documentos</span>
        </span>
      </a>

      <a class="portal-simple-link" href="{{ route('recrutamento') }}">
        <span class="ico">📝</span>
        <span class="txt">
          <span class="name">Recrutamento</span>
          <span class="sub">Inscrições e etapas</span>
        </span>
      </a>

      <a class="portal-simple-link" href="{{ route('juridico') }}">
        <span class="ico">⚖️</span>
        <span class="txt">
          <span class="name">Jurídico</span>
          <span class="sub">Ordenamento e normas</span>
        </span>
      </a>

      <a class="portal-simple-link" href="{{ route('resultados.publicos') }}">
        <span class="ico">📊</span>
        <span class="txt">
          <span class="name">Resultados operacionais</span>
          <span class="sub">Dashboard público</span>
        </span>
      </a>

    </nav>

    <div class="portal-offcanvas-sep"></div>

    <div class="portal-offcanvas-cta">
      <a class="portal-offcanvas-btn" href="{{ route('login') }}">
        🔒 Acesso Operacional
      </a>
    </div>

    <div class="portal-offcanvas-foot">
      Dica: use o menu para navegar rapidamente entre as páginas do portal.
    </div>

  </div>
</div>

<main class="portal-main">

{{-- HERO --}}
<section class="portal-hero" style="margin-bottom:8px;">
    <div class="portal-container">
        <div class="portal-hero-banner" style="height:320px; --portal-hero-image:url('{{ asset('images/imgs5.png') }}');">
            <div class="portal-hero-overlay">
                <div class="portal-hero-badge">RECRUTAMENTO • BRASIL CAPITAL</div>
                <div class="portal-hero-h1" style="font-size:42px;">Grupo de Resposta Rápida - G.R.R</div>
                <div class="portal-hero-p">
                    Disciplina, respeito e padrão institucional.<br>
                    Antes de entrar, entenda exatamente o que será exigido de você no RP.
                </div>
            </div>
        </div>
        <div class="portal-dot"></div>
    </div>
</section>

{{-- DÚVIDAS --}}
<section class="portal-legal" id="duvidas">
    <div class="portal-container">
        <div class="portal-legal-card">
            <h1>❓ Tira-dúvidas do recrutamento</h1>

            <div class="row g-3">
                <div class="col-md-6">
                    <h2 style="margin-bottom:6px;">O que eu preciso saber?</h2>
                    <ul style="margin:0;">
                        <li>Hierarquia e postura em serviço</li>
                        <li>Uso correto de rádio e comunicação</li>
                        <li>Procedimentos operacionais (abordagem, condução, cerco)</li>
                        <li>Regulamento disciplinar (RDPRF)</li>
                    </ul>
                </div>

                <div class="col-md-6">
                    <h2 style="margin-bottom:6px;">Como tirar dúvidas oficiais da Cidade – BC?</h2>
                    <p style="margin:0;">
                        Abra um ticket no Discord da cidade, anexando prints e/ou vídeos, e explique a situação de forma objetiva e detalhada.<br>
                        🔗 <a href="https://discord.gg/brasilcapital" target="_blank" rel="noopener noreferrer">Acessar Discord oficial</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- PRÉ-INSCRIÇÃO --}}
<section class="portal-legal" id="pre-inscricao" style="margin-top:10px;">
  <div class="portal-container">

    <div class="portal-legal-head">
      <h1 class="portal-legal-title">📝 Edital GRR</h1>

      <div class="portal-legal-alert">
        <div class="portal-legal-alert-ico">ℹ️</div>
        <div class="portal-legal-alert-text">
          <b>Atenção:</b> todas as perguntas são obrigatórias.
          O envio não garante vaga, serve para organização e avaliação.
        </div>
      </div>

      <div class="portal-legal-alert mt-2">
        <div class="portal-legal-alert-ico">🚨</div>
        <div class="portal-legal-alert-text">
          <b>Informativo importante:</b> ao solicitarmos seu acesso para a Corregedoria, caso você não possua a <b>CNH A e B</b>, sua inscrição poderá ser <b>reprovada</b>.
          Portanto, ao finalizar o edital, vá rapidamente até o <b>P Vermelho (Poupa Tempo)</b>, próximo à <b>Praça no Sul</b>, para regularizar sua habilitação.
        </div>
      </div>
    </div>

    <div class="pre-clean">

      {{-- FORMULÁRIO COLAPSÁVEL --}}
      <details class="pre-accordion" id="preFormDetails"
        @if($errors->any() || session('ok')) open @endif
      >
        <summary class="pre-accordion__summary">
          <div class="pre-accordion__left">
            <span class="pre-accordion__icon">🧾</span>
            <div>
              <div class="pre-accordion__title">Formulário de pré-inscrição</div>
              <div class="pre-accordion__desc">Clique para abrir e preencher</div>
            </div>
          </div>

          <div class="pre-accordion__right">
            <span class="pre-clean__badge">
              <span class="dot"></span>
              Obrigatório
            </span>
            <span class="pre-accordion__chev" aria-hidden="true">▾</span>
          </div>
        </summary>

        <div class="pre-clean__body" id="preFormBody">

          {{-- TOAST (sucesso) --}}
          @if(session('ok'))
            <div class="pre-toast is-show" id="preToast" role="status" aria-live="polite">
              <div class="pre-toast__box">
                <div class="pre-toast__ico">✅</div>
                <div class="pre-toast__content">
                  <div class="pre-toast__title">Pré-inscrição enviada!</div>
                  <div class="pre-toast__text">{{ session('ok') }}</div>
                  <div class="pre-toast__actions">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
                    <button type="button" class="btn btn-primary btn-sm" id="preToastClose">Ok</button>
                  </div>
                </div>
              </div>
            </div>
          @endif

          {{-- OBSERVAÇÕES (abre sozinho quando abre o formulário) --}}
          <details class="pre-note" id="preNoteDetails">
            <summary>📌 Observações (leitura obrigatória)</summary>
            <div class="pre-note__content">
              Assista ao vídeo explicativo caso tenha dúvidas sobre como verificar seu Discord ID:
              <a href="https://www.youtube.com/watch?v=xX-aWpqnphI" target="_blank" rel="noopener">ver vídeo</a>
            </div>
          </details>

          {{-- PROGRESSO --}}
          <div class="pre-progress" aria-label="Progresso do formulário">
            <div class="pre-progress__top">
              <div class="pre-progress__label">
                Progresso: <b id="preProgressText">0/11</b>
              </div>

              <div class="pre-tools">
                <button type="button" class="pre-tool" id="preClearDraft" title="Limpar rascunho">
                  🧹 <span class="pre-tool__txt">Limpar</span>
                </button>
              </div>
            </div>

            <div class="pre-progress__bar" role="progressbar" aria-valuemin="0" aria-valuemax="11" aria-valuenow="0">
              <div class="pre-progress__fill" id="preProgressFill"></div>
            </div>

            <div class="pre-progress__hint">
              Dica: suas respostas são salvas automaticamente como rascunho.
            </div>
          </div>

          <form method="POST" action="{{ route('recrutamento.store') }}" id="preForm" novalidate>
            @csrf

            {{-- ORIGEM (tracking) --}}
            <input type="hidden" name="origem" id="origem"
                   value="{{ old('origem', request('origem', request('utm_source', url()->previous()))) }}">

            {{-- HONEYPOT (anti-spam) --}}
            <div aria-hidden="true" style="position:absolute;left:-9999px;top:-9999px;height:0;overflow:hidden;">
              <label for="website">Website</label>
              <input
                type="text"
                name="website"
                id="website"
                tabindex="-1"
                autocomplete="off"
                autocapitalize="off"
                spellcheck="false"
                value=""
              >
            </div>

            {{-- MENSAGENS (erro geral) --}}
            @if(session('error'))
              <div class="alert alert-danger pre-alert" role="alert">
                <b>Erro:</b> {{ session('error') }}
              </div>
            @endif

            @if($errors->any())
              <div class="alert alert-danger pre-alert" role="alert" id="preErrorBox">
                <b>Corrija os campos abaixo:</b>
                <ul style="margin:6px 0 0;">
                  @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            {{-- PERGUNTAS (1 COLUNA SEMPRE) --}}
            <div class="pre-grid" id="preGrid">

              {{-- 1 --}}
              <div class="q-card @error('nome_completo') is-error @enderror @error('rg') is-error @enderror" data-q="1">
                <div class="q-head">
                  <div class="q-badge">1</div>
                  <div>
                    <div class="q-title">Identificação (Brasil Capital) <span class="req">*</span></div>
                    <div class="q-sub">Preencha exatamente como consta na cidade.</div>
                  </div>
                </div>

                <div class="row g-2">
                  <div class="col-12 col-md-8">
                    <label class="form-label small fw-semibold mb-1">Nome completo <span class="req">*</span></label>
                    <input type="text" name="nome_completo" class="form-control pre-input"
                           value="{{ old('nome_completo') }}"
                           placeholder="Ex: Thomas Skywalker" required>
                    @error('nome_completo') <div class="pre-error-text">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold mb-1">RG <span class="req">*</span></label>
                    <input type="text" name="rg" class="form-control pre-input"
                           value="{{ old('rg') }}"
                           placeholder="Ex: 12178" required>
                    @error('rg') <div class="pre-error-text">{{ $message }}</div> @enderror
                  </div>
                </div>
              </div>

              {{-- 2 --}}
              <div class="q-card @error('discord_id') is-error @enderror" data-q="2" id="discordIdCard">
                <div class="q-head">
                  <div class="q-badge">2</div>
                  <div>
                    <div class="q-title">Discord ID <span class="req">*</span></div>
                    <div class="q-sub">Consulte Observações (leitura obrigatória) para saber como obter seu Discord ID</div>
                  </div>
                </div>

                <input
                  type="text"
                  name="discord_id"
                  id="discordIdInput"
                  class="form-control pre-input"
                  value="{{ old('discord_id') }}"
                  placeholder="Ex: 803441435453423636"
                  inputmode="numeric"
                  autocomplete="off"
                  minlength="6"
                  required
                >

                <div class="pre-error-text" id="discordIdError" style="display:none;">
                  Esse ID não é válido. Para saber como pegar, veja o vídeo anexado no topo do edital.
                </div>

                @error('discord_id') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

              {{-- 3 --}}
              <div class="q-card @error('possui_cnh_ab') is-error @enderror" data-q="3">
                <div class="q-head">
                  <div class="q-badge">3</div>
                  <div>
                    <div class="q-title">Você possui CNH A e B? <span class="req">*</span></div>
                    <div class="q-sub">Essa informação será verificada posteriormente na Corregedoria.</div>
                  </div>
                </div>

                <div class="chips">
                  <div class="chip">
                    <input type="radio" id="cnhab_sim" name="possui_cnh_ab" value="sim"
                           @checked(old('possui_cnh_ab') === 'sim') required>
                    <label for="cnhab_sim">Sim</label>
                  </div>
                  <div class="chip">
                    <input type="radio" id="cnhab_nao" name="possui_cnh_ab" value="nao"
                           @checked(old('possui_cnh_ab') === 'nao') required>
                    <label for="cnhab_nao">Não</label>
                  </div>
                </div>

                @error('possui_cnh_ab') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

              {{-- 4 --}}
              <div class="q-card @error('motivo_grr_agora') is-error @enderror" data-q="4">
                <div class="q-head">
                  <div class="q-badge">4</div>
                  <div>
                    <div class="q-title">Em poucas palavras, o que te fez procurar o GRR neste momento? <span class="req">*</span></div>
                    <div class="q-sub">Mínimo sugerido: 30 caracteres.</div>
                  </div>
                </div>
                <textarea name="motivo_grr_agora" class="form-control pre-input pre-ta"
                          rows="4" minlength="30" required>{{ old('motivo_grr_agora') }}</textarea>
                <div class="pre-meta">
                  <span class="pre-min">mín. 30</span>
                  <span class="pre-count"><b class="preCount">0</b> caracteres</span>
                </div>
                @error('motivo_grr_agora') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

              {{-- 5 --}}
              <div class="q-card @error('diferencial_grr') is-error @enderror" data-q="5">
                <div class="q-head">
                  <div class="q-badge">5</div>
                  <div>
                    <div class="q-title">O que você acredita que diferencia o GRR das outras forças policiais e por que isso se conecta com você? <span class="req">*</span></div>
                    <div class="q-sub">Mínimo sugerido: 30 caracteres.</div>
                  </div>
                </div>
                <textarea name="diferencial_grr" class="form-control pre-input pre-ta"
                          rows="4" minlength="30" required>{{ old('diferencial_grr') }}</textarea>
                <div class="pre-meta">
                  <span class="pre-min">mín. 30</span>
                  <span class="pre-count"><b class="preCount">0</b> caracteres</span>
                </div>
                @error('diferencial_grr') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

              {{-- 6 --}}
              <div class="q-card @error('estagio_15_dias') is-error @enderror" data-q="6">
                <div class="q-head">
                  <div class="q-badge">6</div>
                  <div>
                    <div class="q-title">O estágio probatório dura 15 dias e avalia disciplina, presença e postura. Você está disposto(a) a cumprir esse período sem privilégios? <span class="req">*</span></div>
                    <div class="q-sub">Você aceita o período inicial de avaliação?</div>
                  </div>
                </div>

                <div class="chips">
                  <div class="chip">
                    <input type="radio" id="p6s" name="estagio_15_dias" value="sim"
                           @checked(old('estagio_15_dias')==='sim') required>
                    <label for="p6s">Sim</label>
                  </div>
                  <div class="chip">
                    <input type="radio" id="p6n" name="estagio_15_dias" value="nao"
                           @checked(old('estagio_15_dias')==='nao') required>
                    <label for="p6n">Não</label>
                  </div>
                </div>
                @error('estagio_15_dias') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

              {{-- 7 --}}
              <div class="q-card @error('dias_ativo_semana') is-error @enderror" data-q="7">
                <div class="q-head">
                  <div class="q-badge">7</div>
                  <div>
                    <div class="q-title">Quantos dias por semana você consegue estar ativo(a) no jogo? <span class="req">*</span></div>
                    <div class="q-sub">Com que frequência você joga/atua?</div>
                  </div>
                </div>

                <select name="dias_ativo_semana" class="form-select pre-input" required>
                  <option value="">Selecione…</option>
                  <option value="1-2" @selected(old('dias_ativo_semana')=='1-2')>2 dias na semana</option>
                  <option value="3-4" @selected(old('dias_ativo_semana')=='3-4')>4 dias na semana</option>
                  <option value="5-6" @selected(old('dias_ativo_semana')=='5-6')>6 dias na semana</option>
                  <option value="7" @selected(old('dias_ativo_semana')=='7')>Todos os dias</option>
                </select>
                @error('dias_ativo_semana') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

              {{-- 8 --}}
              <div class="q-card @error('ordem_nao_concorda') is-error @enderror" data-q="8">
                <div class="q-head">
                  <div class="q-badge">8</div>
                  <div>
                    <div class="q-title">Se receber uma ordem que você não concorda, o que você faz? <span class="req">*</span></div>
                    <div class="q-sub">Como você reage em uma situação de discordância?</div>
                  </div>
                </div>

                <div class="chips">
                  <div class="chip">
                    <input type="radio" id="o1" name="ordem_nao_concorda" value="cumpro_e_depois_questiono"
                           @checked(old('ordem_nao_concorda')==='cumpro_e_depois_questiono') required>
                    <label for="o1">Cumpro e depois questiono</label>
                  </div>
                  <div class="chip">
                    <input type="radio" id="o2" name="ordem_nao_concorda" value="questiono_no_momento"
                           @checked(old('ordem_nao_concorda')==='questiono_no_momento') required>
                    <label for="o2">Questiono no momento</label>
                  </div>
                  <div class="chip">
                    <input type="radio" id="o3" name="ordem_nao_concorda" value="nao_cumpro"
                           @checked(old('ordem_nao_concorda')==='nao_cumpro') required>
                    <label for="o3">Não cumpro</label>
                  </div>
                </div>
                @error('ordem_nao_concorda') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

              {{-- 9 --}}
              <div class="q-card @error('horario_frequente') is-error @enderror" data-q="9">
                <div class="q-head">
                  <div class="q-badge">9</div>
                  <div>
                    <div class="q-title">Qual seu horário mais frequente de jogo? <span class="req">*</span></div>
                    <div class="q-sub">Qual período você mais participa?</div>
                  </div>
                </div>

                <select name="horario_frequente" class="form-select pre-input" required>
                  <option value="">Selecione…</option>
                  <option value="manha" @selected(old('horario_frequente')=='manha')>Manhã</option>
                  <option value="tarde" @selected(old('horario_frequente')=='tarde')>Tarde</option>
                  <option value="noite" @selected(old('horario_frequente')=='noite')>Noite</option>
                  <option value="madrugada" @selected(old('horario_frequente')=='madrugada')>Madrugada</option>
                  <option value="varia" @selected(old('horario_frequente')=='varia')>Varia</option>
                </select>
                @error('horario_frequente') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

              {{-- 10 --}}
              <div class="q-card @error('como_lida_frustracao') is-error @enderror" data-q="10">
                <div class="q-head">
                  <div class="q-badge">10</div>
                  <div>
                    <div class="q-title">Como você lida com frustração dentro do jogo? <span class="req">*</span></div>
                    <div class="q-sub">Mínimo sugerido: 30 caracteres.</div>
                  </div>
                </div>

                <textarea name="como_lida_frustracao" class="form-control pre-input pre-ta"
                          rows="4" minlength="30" required>{{ old('como_lida_frustracao') }}</textarea>
                <div class="pre-meta">
                  <span class="pre-min">mín. 30</span>
                  <span class="pre-count"><b class="preCount">0</b> caracteres</span>
                </div>
                @error('como_lida_frustracao') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

              {{-- 11 --}}
              <div class="q-card @error('experiencia_anterior') is-error @enderror" data-q="11">
                <div class="q-head">
                  <div class="q-badge">11</div>
                  <div>
                    <div class="q-title">Você já fez parte de alguma força policial ou organização semelhante? Se sim, qual e por que saiu? <span class="req">*</span></div>
                    <div class="q-sub">Mínimo sugerido: 30 caracteres.</div>
                  </div>
                </div>

                <textarea name="experiencia_anterior" class="form-control pre-input pre-ta"
                          rows="4" minlength="30" required>{{ old('experiencia_anterior') }}</textarea>
                <div class="pre-meta">
                  <span class="pre-min">mín. 30</span>
                  <span class="pre-count"><b class="preCount">0</b> caracteres</span>
                </div>
                @error('experiencia_anterior') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

            </div>

            {{-- AÇÕES --}}
            <div class="pre-actions">
              <div class="form-check">
                <input class="form-check-input pre-input" type="checkbox" name="confirm_all" id="confirmAll" required
                       @checked(old('confirm_all'))>
                <label class="form-check-label pre-confirm" for="confirmAll">
                  Confirmo que preenchi tudo e estou ciente das condições.
                </label>
                @error('confirm_all') <div class="pre-error-text">{{ $message }}</div> @enderror
              </div>

              <button class="btn btn-primary" id="preSubmitBtn" type="submit">
                <span class="btn-txt">Enviar pré-inscrição</span>
                <span class="btn-load" aria-hidden="true">Enviando…</span>
              </button>
            </div>

          </form>
        </div>
      </details>

    </div>
  </div>
</section>

{{-- JS do formulário --}}
<script>
(function () {
  const section = document.getElementById('pre-inscricao');
  if (!section) return;

  const details = document.getElementById('preFormDetails');
  const noteDetails = document.getElementById('preNoteDetails');
  const form = document.getElementById('preForm');
  const submitBtn = document.getElementById('preSubmitBtn');
  const progressFill = document.getElementById('preProgressFill');
  const progressText = document.getElementById('preProgressText');
  const progressBar = section.querySelector('.pre-progress__bar');
  const clearBtn = document.getElementById('preClearDraft');
  const toast = document.getElementById('preToast');
  const toastClose = document.getElementById('preToastClose');

  const discordInput = document.getElementById('discordIdInput');
  const discordError = document.getElementById('discordIdError');
  const discordCard = document.getElementById('discordIdCard');

  // versão nova do draft
  const DRAFT_KEY = 'preinscricao_draft_v3';

  // agora são 11 perguntas
  const TOTAL = 11;

  const openObservacoes = () => {
    if (noteDetails) noteDetails.open = true;
  };

  if (details) {
    details.addEventListener('toggle', () => {
      if (details.open) openObservacoes();
    });
    if (details.open) openObservacoes();
  }

  if (toastClose && toast) {
    toastClose.addEventListener('click', () => toast.classList.remove('is-show'));
  }

  const autoResize = (ta) => {
    ta.style.height = 'auto';
    ta.style.height = (ta.scrollHeight) + 'px';
  };

  const bindCounters = () => {
    if (!form) return;
    const textareas = form.querySelectorAll('.pre-ta');

    textareas.forEach(ta => {
      const meta = ta.closest('.q-card')?.querySelector('.preCount');
      const min = parseInt(ta.getAttribute('minlength') || '0', 10);

      const update = () => {
        const len = (ta.value || '').trim().length;
        if (meta) meta.textContent = String(len);
        ta.closest('.q-card')?.classList.toggle('is-min-ok', min > 0 && len >= min);
        autoResize(ta);
      };

      ta.addEventListener('input', update);
      update();
    });
  };

  const isFilled = (el) => {
    if (!el || el.disabled) return false;
    const name = el.name;
    if (!name) return false;

    if (el.type === 'radio') {
      return !!form.querySelector(`input[type="radio"][name="${CSS.escape(name)}"]:checked`);
    }
    if (el.type === 'checkbox') {
      return el.checked;
    }
    if (el.tagName === 'SELECT') {
      return (el.value || '').trim() !== '';
    }
    return (el.value || '').trim().length > 0;
  };

  const isQ1Done = () => {
    const nome = form.querySelector('[name="nome_completo"]');
    const rg = form.querySelector('[name="rg"]');
    return isFilled(nome) && isFilled(rg);
  };

  const isDiscordValid = () => {
    if (!discordInput) return true;
    const value = (discordInput.value || '').trim();
    return /^\d{6,}$/.test(value);
  };

  const setDiscordState = (rawValue = null) => {
    if (!discordInput || !discordError || !discordCard) return;

    const raw = rawValue ?? discordInput.value ?? '';
    const clean = raw.replace(/\D+/g, '');

    if (discordInput.value !== clean) {
      discordInput.value = clean;
    }

    const hasValue = clean.length > 0;
    const valid = /^\d{6,}$/.test(clean);

    const showError = hasValue && !valid;

    discordError.style.display = showError ? 'block' : 'none';
    discordCard.classList.toggle('is-error', showError);
    discordCard.classList.toggle('is-min-ok', valid);
  };

  const calcProgress = () => {
    if (!form) return;

    const q1 = isQ1Done();
    const q2 = isFilled(form.querySelector('[name="discord_id"]'));
    const q3 = isFilled(form.querySelector('[name="possui_cnh_ab"]'));
    const q4 = isFilled(form.querySelector('[name="motivo_grr_agora"]'));
    const q5 = isFilled(form.querySelector('[name="diferencial_grr"]'));
    const q6 = isFilled(form.querySelector('[name="estagio_15_dias"]'));
    const q7 = isFilled(form.querySelector('[name="dias_ativo_semana"]'));
    const q8 = isFilled(form.querySelector('[name="ordem_nao_concorda"]'));
    const q9 = isFilled(form.querySelector('[name="horario_frequente"]'));
    const q10 = isFilled(form.querySelector('[name="como_lida_frustracao"]'));
    const q11 = isFilled(form.querySelector('[name="experiencia_anterior"]'));

    const checks = [q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, q11];
    const done = checks.filter(Boolean).length;

    const pct = Math.round((done / TOTAL) * 100);
    if (progressText) progressText.textContent = `${done}/${TOTAL}`;
    if (progressFill) progressFill.style.width = `${pct}%`;
    progressBar?.setAttribute('aria-valuenow', String(done));
  };

  const saveDraft = () => {
    if (!form) return;
    const data = {};
    const fields = form.querySelectorAll('input[name], select[name], textarea[name]');

    fields.forEach(el => {
      const name = el.name;
      if (!name) return;

      if (el.type === 'password' || name === '_token') return;
      if (name === 'website') return;

      if (el.type === 'radio') {
        if (el.checked) data[name] = el.value;
      } else if (el.type === 'checkbox') {
        data[name] = el.checked ? '1' : '0';
      } else {
        data[name] = el.value;
      }
    });

    try { localStorage.setItem(DRAFT_KEY, JSON.stringify(data)); } catch(e){}
  };

  const loadDraft = () => {
    if (!form) return;

    let raw = null;
    try { raw = localStorage.getItem(DRAFT_KEY); } catch(e){}
    if (!raw) return;

    let data = null;
    try { data = JSON.parse(raw); } catch(e){ return; }
    if (!data || typeof data !== 'object') return;

    Object.keys(data).forEach(name => {
      const value = data[name];
      const els = form.querySelectorAll(`[name="${CSS.escape(name)}"]`);
      if (!els.length) return;

      const first = els[0];
      if (first.type === 'radio') {
        const target = form.querySelector(`input[type="radio"][name="${CSS.escape(name)}"][value="${CSS.escape(value)}"]`);
        if (target) target.checked = true;
      } else if (first.type === 'checkbox') {
        first.checked = (value === '1');
      } else {
        first.value = value;
      }
    });
  };

  const clearDraft = () => {
    try { localStorage.removeItem(DRAFT_KEY); } catch(e){}
    if (form) form.reset();
    bindCounters();
    setDiscordState();
    calcProgress();
    if (details && details.open) openObservacoes();
  };

  if (clearBtn) clearBtn.addEventListener('click', clearDraft);

  const focusFirstError = () => {
    const firstErr = section.querySelector('.q-card.is-error input, .q-card.is-error select, .q-card.is-error textarea');
    if (!firstErr) return;

    if (details) details.open = true;

    setTimeout(() => {
      openObservacoes();
      firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstErr.focus({ preventScroll: true });

      const card = firstErr.closest('.q-card');
      if (card) {
        card.classList.add('is-error-pulse');
        setTimeout(() => card.classList.remove('is-error-pulse'), 1200);
      }
    }, 150);
  };

  const lockSubmit = () => {
    if (!submitBtn) return;
    submitBtn.disabled = true;
    submitBtn.classList.add('is-loading');
  };

  if (discordInput) {
    discordInput.addEventListener('input', (e) => {
      setDiscordState(e.target.value);
      saveDraft();
      calcProgress();
    });

    discordInput.addEventListener('blur', () => {
      setDiscordState();
    });

    setDiscordState();
  }

  if (form) {
    const applyDraftSafely = () => {
      const hasOld =
        (form.querySelector('[name="nome_completo"]')?.value || '').trim().length > 0 ||
        (form.querySelector('[name="rg"]')?.value || '').trim().length > 0 ||
        (form.querySelector('[name="discord_id"]')?.value || '').trim().length > 0 ||
        !!form.querySelector('[name="possui_cnh_ab"]:checked');

      if (!hasOld) loadDraft();
    };

    applyDraftSafely();
    bindCounters();
    setDiscordState();
    calcProgress();

    form.addEventListener('input', () => {
      saveDraft();
      calcProgress();
    });

    form.querySelectorAll('.pre-ta').forEach(autoResize);

    form.addEventListener('submit', (e) => {
      setDiscordState();

      if (!isDiscordValid()) {
        e.preventDefault();
        if (details) details.open = true;
        openObservacoes();
        discordInput?.focus();
        discordInput?.scrollIntoView({ behavior: 'smooth', block: 'center' });

        if (discordCard) {
          discordCard.classList.add('is-error-pulse');
          setTimeout(() => discordCard.classList.remove('is-error-pulse'), 1200);
        }
        return;
      }

      lockSubmit();
    });
  }

  if (section.querySelector('.q-card.is-error')) {
    focusFirstError();
  }

  if (toast) {
    toast.addEventListener('click', (e) => {
      if (e.target === toast) toast.classList.remove('is-show');
    });
  }

  const sentOk = @json(session()->has('ok'));
  if (sentOk) {
    try { localStorage.removeItem(DRAFT_KEY); } catch(e){}
    if (form) form.reset();

    setTimeout(() => {
      bindCounters();
      setDiscordState();
      calcProgress();
    }, 0);
  }
})();
</script>

{{-- REGULAMENTO + BUSCA + ACCORDION --}}
<section class="portal-legal" id="regulamento" style="margin-top:10px;">
  <div class="portal-container">

    <div class="portal-legal-head">
      <h1 class="portal-legal-title">📖 RDPRF — Manual (por seções)</h1>

      <div class="portal-legal-alert">
        <div class="portal-legal-alert-ico">✅</div>
        <div class="portal-legal-alert-text">
          Use a busca para achar termos dentro das seções (ex.: “hierarquia”, “armamento”, “viaturas”, “disparo”, “ponto aberto”).
        </div>
      </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="portal-legal-card rules-toolbar">
      <div class="rules-toolbar__top">

        <div class="rules-toolbar__search">
          <label class="rules-label">Buscar no regulamento</label>
          <div class="input-group">
            <span class="input-group-text">🔎</span>
            <input id="rdSearch" class="form-control" type="text"
                   placeholder="Ex.: hierarquia, viaturas, armamento, disparo, ponto aberto...">
            <button id="rdClear" class="btn btn-outline-secondary" type="button">Limpar</button>
          </div>
          <div class="small portal-muted mt-1">Dica: a busca encontra dentro de cada seção.</div>
        </div>

        <div class="rules-toolbar__filter">
          <label class="rules-label">Filtrar seção</label>
          <select id="rdFilter" class="form-select">
            <option value="all">Todas</option>
            <option value="disposicao">Disposição geral</option>
            <option value="principios">Princípios</option>
            <option value="postura">Respeito e postura</option>
            <option value="moral">Moral</option>
            <option value="dignidade">Dignidade</option>
            <option value="hierarquia">Hierarquia</option>
            <option value="etica">Ética</option>
            <option value="verdade">Verdade</option>
            <option value="regras">Regras específicas</option>
            <option value="visual">Visual e fardamento</option>
            <option value="viaturas">Viaturas</option>
            <option value="patrulhamento">Patrulhamento</option>
            <option value="punicoes">Punições</option>
            <option value="armamento">Armamento</option>
            <option value="disparo">Disparo em acompanhamentos</option>
            <option value="superintendencia">Superintendência</option>
            <option value="treinamentos">Treinamentos/Cursos</option>
          </select>
        </div>

      </div>

      <div class="rules-toolbar__bottom">
        <small class="portal-muted">
          Exibindo <b id="rdCount">0</b> de <b id="rdTotal">0</b> seções
        </small>
      </div>
    </div>

    @php
      $rdprf = [
        [
          'sec' => 'disposicao',
          'title' => 'Disposição Geral',
          'body' => "O REGULAMENTO DISCIPLINAR DA POLÍCIA RODOVIÁRIA FEDERAL, TEM COMO FUNÇÃO ESTRUTURAR EM REGRAS A INSTITUIÇÃO, QUANTO AO SERVIÇO OPERACIONAL, ADMINISTRATIVOS E DEMAIS ATIVIDADES DESENVOLVIDAS."
        ],
        [
          'sec' => 'principios',
          'title' => 'Princípios da Instituição',
          'body' => "O POLICIAL NÃO DEVERÁ EM EXERCÍCIO DE FUNÇÃO OU FORA DESTA OS SEGUINTES:
I: FALTAR COM O RESPEITO, BEM COMO UTILIZAR DE PALAVRAS OFENSIVAS COM QUALQUER CIVIL/MILITAR/FEDERAL;
II: FALTAR COM A MORAL, UTILIZAR DE ILICITUDES E ILÍCITOS PARA SI, OU PARA OUTROS;
III: FALTAR COM A DIGNIDADE, COMETER CRIMES ATRAVÉS USO DA FUNÇÃO OU DO PODER;
IV: FALTAR COM A HIERARQUIA, DESCUMPRIR DETERMINAÇÕES LEGAIS RECEBIDAS DE SEUS SUPERIORES;
V: FALTAR COM A ÉTICA, USAR DE INFORMAÇÕES CONFIDENCIAIS PARA GERAR CONFLITOS INTERNOS E EXTERNOS;
VI: FALTAR COM A VERDADE, USAR DE MENTIRAS PARA EXCLUIR-SE DE QUALQUER SITUAÇÃO OU ACONTECIDO;
VII: FALTAR COM REGRAS ESPECÍFICAS, IMPOSTAS PELA PREFEITURA DA CIDADE OU PELA INSTITUIÇÃO."
        ],
        [
          'sec' => 'postura',
          'title' => 'Seção I — Respeito e Postura',
          'body' => "I: FALTAR COM RESPEITO PARA COM QUALQUER POLICIAL E SUPERIOR ESTANDO DE SERVIÇO, OU NÃO;
II: FALTAR COM RESPEITO PARA COM QUALQUER CIVIL DURANTE O SERVIÇO SENDO EM ABORDAGEM OU CONDUÇÃO;
III: PROFERIR PALAVRAS OFENSIVAS, PRECONCEITUOSAS E VEXATÓRIAS EM SERVIÇO, OU FARDADO;
IV: TRATAR COM DESPREZO E EXCLUIR PESSOA OU POLICIAL POR SITUAÇÕES ADVERSAS;
V: USAR DE APELIDOS INCONVENIENTES E DESCONFORTÁVEIS EM CONVERSAS, OU REFERÊNCIAS."
        ],
        [
          'sec' => 'moral',
          'title' => 'Seção II — Moral',
          'body' => "I: LIGAR-SE OU INTEGRAR-SE COM ORGANIZAÇÃO CRIMINOSA OU GRUPO ESPECÍFICO;
II: APROPRIAR-SE DE OBJETOS APREENDIDOS E ILÍCITOS ENCONTRADOS;
III: ENTREGAR, PERMITIR E FACILITAR A POSSE DE ILÍCITOS PARA TERCEIROS;
IV: FORJAR E INTRODUZIR ILÍCITO OU PROVA CABÍVEL DE PENA OU RECLUSÃO EM QUALQUER CIVIL OU MILITAR;
V: ROMPER, DANIFICAR E DESTRUIR BEM PÚBLICO, OU PRIVADO NO EXERCÍCIO DA FUNÇÃO."
        ],
        [
          'sec' => 'dignidade',
          'title' => 'Seção III — Dignidade',
          'body' => "I: UTILIZAR DA FUNÇÃO PÚBLICA PARA OBTER VANTAGEM OU ACESSO;
II: COMETER CRIMES PREVISTOS NO CÓDIGO PENAL;
III: UTILIZAR DA FUNÇÃO PARA EXCLUIR-SE DE CULPA OU DOLO;
IV: UTILIZAR DOS MATERIAIS BÉLICOS PARA MATAR, FERIR OU LESIONAR QUALQUER PESSOA SEM JUSTA CAUSA;
V: UTILIZAR PRINCÍPIOS PARA CONSTRANGER OU ALICIAR."
        ],
        [
          'sec' => 'hierarquia',
          'title' => 'Seção IV — Hierarquia',
          'body' => "I: NÃO ACATAR AS LEGAIS ORDENS EMANADAS DO SEU SUPERIOR HIERÁRQUICO;
II: CAUSAR DESCONFORTO OU CONSTRANGIMENTO A SUBORDINADO PELO USO DA PATENTE OU FUNÇÃO;
III: CAUSAR ATRITOS OU PROBLEMAS PARA CONSEGUIR POSTO, PATENTE E FUNÇÃO;
IV: USAR DE MÁ FÉ DO CARGO E PATENTE PARA PREJUDICAR, OU EXCLUIR SUBORDINADO;
V: USAR DO CARGO E FUNÇÃO PARA IMPOR REGRAS ABSURDAS, OU INCONVENIENTES."
        ],
        [
          'sec' => 'etica',
          'title' => 'Seção V — Ética',
          'body' => "I: USAR DE INFORMAÇÕES PRIVILEGIADAS PARA CAUSAR ATRITOS E PROBLEMAS INTERNOS E EXTERNOS;
II: UTILIZAR DE COMUNICAÇÃO FALSA E INDEVIDA PARA OBTER VANTAGEM, OU BENEFÍCIO;
III: MANCHAR E FERIR A IMAGEM INSTITUCIONAL COM PALAVRAS, AÇÕES E IMAGENS;
IV: FALAR EM NOME DA CORPORAÇÃO OU MANIFESTAR-SE EM ASSUNTO DE NÃO COMPETÊNCIA;
V: TORNAR PÚBLICO CONTEÚDO, DOCUMENTO OU PROCESSO QUE DEVERIA CORRER EM ATO CONFIDENCIAL."
        ],
        [
          'sec' => 'verdade',
          'title' => 'Seção VI — Verdade',
          'body' => "I: UTILIZAR DE MENTIRAS PARA EXCLUIR-SE DE PROCESSO OU ABSTENÇÃO DE CULPA;
II: INDUZIR PESSOA AO ERRO OU CRIAR PROVA CABAL CONTRA SI PRÓPRIA;
III: CRIAR IMAGENS, TEXTOS E VÍDEOS FICTÍCIOS PARA PREJUDICAR A INSTITUIÇÃO, OU PESSOA;
IV: GERAR INFORMAÇÕES DESCONCERTANTES DA VIDA PESSOAL DE CADA POLICIAL DENTRO OU FORA DA CIDADE;
V: UTILIZAR A FALSIDADE IDEOLÓGICA DENTRO E FORA DA CIDADE PARA BENEFÍCIO PRÓPRIO OU DE OUTRO."
        ],
        [
          'sec' => 'regras',
          'title' => 'Seção VII — Regras Específicas',
          'body' => "I: DESCUMPRIR QUAISQUER REGRAS IMPOSTAS PELA PREFEITURA;
II: UTILIZAR EQUIPAMENTOS, FARDAMENTOS, VEÍCULOS E MATERIAIS BÉLICOS NÃO PERMITIDOS;
III: UTILIZAR LOCAIS PÚBLICOS OU SOBRE JURISDIÇÃO PARA SERVIÇO INCOMPATÍVEL;
IV: QUALQUER SITUAÇÃO ANALISADA PELA PREFEITURA OU SUPERIOR COMO NÃO CONVENIENTE OU PERMITIDA;
V: QUALQUER ATO INCORRETO MESMO NÃO EXPRESSO OU ESCRITO NESTE DOCUMENTO.
VI: É VEDADO AO AGENTE PERMANECER DE PONTO ABERTO QUANDO NÃO ESTIVER EM SERVIÇO, BEM COMO QUANDO NÃO SE ENCONTRAR, POR ESCALA, NA CANALETA DE PLANTÃO DA DELEGACIA.
VII: É VEDADO O ESTACIONAMENTO DAS VIATURAS NO PAVILHÃO DE ENTRADA DA DELEGACIA DE POLÍCIA."
        ],
        [
          'sec' => 'visual',
          'title' => 'Seção VIII — Das Roupas, Visual e Fardamentos',
          'body' => "I: É PROIBIDO O USO DE CORES DE CABELO E OLHOS QUE NÃO SEJAM DE COLORAÇÃO NATURAL.
II: DURANTE O SERVIÇO INTERNO... (texto completo aqui)
III: É VEDADO OSTENTAR TATUAGENS NA FACE.
IV: O POLICIAL DEVERÁ OBSERVAR O REGULAMENTO DE UNIFORMES...
V: É VEDADO O USO DE MÁSCARA.
... (mantenha o restante do seu texto nesta seção)."
        ],
        [
          'sec' => 'viaturas',
          'title' => 'Seção IX — Das Viaturas',
          'body' => "É PROIBIDA QUALQUER MODIFICAÇÃO ESTÉTICA NAS VIATURAS...
EXCEÇÕES:
I - PINTAR A RODA ORIGINAL DE PRETO.
II - PINTURA METÁLICA OU FOSCA...
III - USO DE “EXTRAS”...
I: PROIBIDO USAR NOTEBOOK NAS VIATURAS POLICIAIS."
        ],
        [
          'sec' => 'patrulhamento',
          'title' => 'Seção X — Do Patrulhamento',
          'body' => "I: DURANTE AS DILIGÊNCIAS, INCUMBE AO CHEFE DE BARCA...
II: SE FICAR COMPROVADA OMISSÃO...
III: Para saídas de diligência deverá ter no mínimo 2 policiais dentro da viatura.
IV: Está proibido sair das dependências da superintendência sozinho."
        ],
        [
          'sec' => 'punicoes',
          'title' => 'Seção XI — Das Punições',
          'body' => "I - AOS DIRETORES E AOS VICE DIRETORES...
II - SANÇÕES DE ADVERTÊNCIA...
III - TODA SANÇÃO DEVERÁ SER PUBLICADA...
IV - MEDIDAS DE RECICLAGEM...
V - EXONERAÇÕES DEPENDEM DE AVAL DA CORREGEDORIA-GERAL..."
        ],
        [
          'sec' => 'armamento',
          'title' => 'Seção XII — Do Armamento',
          'body' => "1. POLICIAIS EM SERVIÇO (UNIFORMIZADOS)
A) MOTORISTA: PISTOLA GLOCK — 100 MUNIÇÕES — VEDADO MUNIÇÃO RESERVA.
B) CHEFE DE BARCA E AUXILIARES: PISTOLA GLOCK (100) + 01 FUZIL (250).
LOCKPICK APENAS PELO CHEFE DE BARCA.
2. BENEFÍCIO VIP: permitido armamento adicional do VIP (mantém limites).
3. À PAISANA: GLOCK — 80 MUNIÇÕES — VEDADO MUNIÇÃO RESERVA.
4. 12 DE ELASTÔMERO: restrito a Inspetor ou superior."
        ],
        [
          'sec' => 'disparo',
          'title' => 'Do Disparo em Acompanhamentos',
          'body' => "O DISPARO NO PNEU SÓ PODERÁ OCORRER:
- veículo capota e continua fugindo;
- veículo joga para cima das motos intencionalmente;
- veículo bate contra as unidades após ser fechado.
Necessita autorização de superior (Superintendente ou Coordenador) com menção do QRA no rádio."
        ],
        [
          'sec' => 'superintendencia',
          'title' => 'Superintendência',
          'body' => "I: Os Policiais deverão permanecer na canaleta de plantão base, desempenhando a função de blitz. Caso não estejam exercendo tal atividade, deverão manter o ponto fechado."
        ],
        [
          'sec' => 'treinamentos',
          'title' => 'Treinamentos/Cursos',
          'body' => "I: Toda e qualquer instrução deverá ser ministrada exclusivamente na canaleta de treinamento. Descumprimento sujeita o instrutor às sanções administrativas."
        ],
      ];
    @endphp

    {{-- ACCORDION --}}
    <div class="portal-legal-card">
      <div class="accordion" id="rdAccordion">
        @foreach($rdprf as $i => $s)
          @php
            $hid = 'rd-h-'.$i;
            $cid = 'rd-c-'.$i;
            $idx = mb_strtolower($s['title']."\n".$s['body']);
          @endphp

          <div class="accordion-item rd-item"
               data-sec="{{ $s['sec'] }}"
               data-text="{{ e($idx) }}">
            <h2 class="accordion-header" id="{{ $hid }}">
              <button class="accordion-button collapsed" type="button"
                      data-bs-toggle="collapse" data-bs-target="#{{ $cid }}"
                      aria-expanded="false" aria-controls="{{ $cid }}">
                {{ $s['title'] }}
              </button>
            </h2>

            <div id="{{ $cid }}" class="accordion-collapse collapse"
                 aria-labelledby="{{ $hid }}" data-bs-parent="#rdAccordion">
              <div class="accordion-body" style="white-space: pre-line;">
                {{ $s['body'] }}
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</section>

{{-- CTA CURSOS --}}
<section class="portal-legal" id="cursos" style="margin-top:10px;">
    <div class="portal-container">
        <div class="portal-legal-card">
            <h1>🎓 Cursos PRF | GRR</h1>
            <p class="portal-muted">Veja a trilha de formação, requisitos e o que será cobrado em cada etapa.</p>
            <a class="btn btn-primary" href="{{ route('cursos.prf') }}">Abrir página de Cursos PRF | GRR</a>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer class="portal-footer">
    <div class="portal-container">
        <div class="d-flex justify-content-between flex-wrap gap-2">
            <div>© {{ date('Y') }} GRR — Grupo de Resposta Rápida</div>
            <div class="portal-muted">FIVEM.BC • Portal Institucional</div>
        </div>
    </div>
</footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- THEME (GLOBAL) --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const STORAGE_KEY = "grr_theme";
  const root = document.documentElement;
  const btn = document.getElementById("toggleTheme");

  function applyIcon(theme) {
    if (!btn) return;
    btn.textContent = theme === "dark" ? "☀️" : "🌙";
  }

  function setTheme(theme) {
    root.setAttribute("data-theme", theme);
    try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
    applyIcon(theme);
  }

  function getPreferredTheme() {
    let saved = null;
    try { saved = localStorage.getItem(STORAGE_KEY); } catch (e) {}
    if (saved === "light" || saved === "dark") return saved;
    return (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) ? "dark" : "light";
  }

  setTheme(getPreferredTheme());

  btn?.addEventListener("click", () => {
    const current = root.getAttribute("data-theme") || "light";
    setTheme(current === "dark" ? "light" : "dark");
  });
});
</script>

{{-- BUSCA + FILTRO DO ACCORDION (ATUALIZADO) --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const q = document.getElementById('rdSearch');
  const filter = document.getElementById('rdFilter');
  const clearBtn = document.getElementById('rdClear');

  const countEl = document.getElementById('rdCount');
  const totalEl = document.getElementById('rdTotal');

  const items = Array.from(document.querySelectorAll('#rdAccordion .rd-item'));

  function normalize(str) {
    return (str || "")
      .toString()
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "");
  }

  function apply() {
    const term = normalize(q?.value || "");
    const sec = (filter?.value || "all").trim();

    let visible = 0;

    items.forEach(el => {
      const text = normalize(el.dataset.text || el.innerText || "");
      const itemSec = (el.dataset.sec || "").trim();

      const okText = !term || text.includes(term);
      const okSec = (sec === "all") || (itemSec === sec);

      const show = okText && okSec;
      el.style.display = show ? "" : "none";
      if (show) visible++;
    });

    if (totalEl) totalEl.textContent = String(items.length);
    if (countEl) countEl.textContent = String(visible);

    if (!items.length) {
      console.warn('[RDPRF] Nenhum item encontrado. Verifique se existe #rdAccordion e itens com classe .rd-item.');
    }
  }

  function clearFilters() {
    if (q) q.value = "";
    if (filter) filter.value = "all";
    apply();
    q?.focus();
  }

  q?.addEventListener("input", apply);
  filter?.addEventListener("change", apply);
  clearBtn?.addEventListener("click", clearFilters);

  apply();
});
</script>

</body>
</html>