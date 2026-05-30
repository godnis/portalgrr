<!DOCTYPE html>
<html lang="pt-br" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1220">
    <meta name="description" content="Portal de Legislação da GRR — Regras, diretrizes internas e Código Penal fictício para ambiente RP da cidade Brasil Capital.">
    <title>GRR • Legislação — Regras e Leis</title>

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="portal-body">

@php
    $topLinks = [
        ['label' => 'Página inicial', 'href' => url('/'), 'route' => 'portal'],
        ['label' => 'Comunicados oficiais', 'href' => route('comunicados'), 'route' => 'comunicados'],
        ['label' => 'Governo da Cidade', 'href' => route('governo'), 'route' => 'governo'],
        ['label' => 'Recrutamento', 'href' => route('recrutamento'), 'route' => 'recrutamento'],
        ['label' => 'Legislação', 'href' => route('legislacao'), 'route' => 'legislacao'],
    ];

    $menuLinks = [
        ['icon' => '🏠', 'name' => 'Página inicial', 'sub' => 'Voltar ao portal', 'href' => route('portal')],
        ['icon' => '📢', 'name' => 'Comunicados oficiais', 'sub' => 'Avisos e notas do GRR', 'href' => route('comunicados')],
        ['icon' => '🏛️', 'name' => 'Governo da cidade', 'sub' => 'Estrutura e informações', 'href' => route('governo')],
        ['icon' => '📚', 'name' => 'Legislação', 'sub' => 'Leis e documentos', 'href' => route('legislacao')],
        ['icon' => '📝', 'name' => 'Recrutamento', 'sub' => 'Inscrições e etapas', 'href' => route('recrutamento')],
        ['icon' => '⚖️', 'name' => 'Jurídico', 'sub' => 'Ordenamento e normas', 'href' => route('juridico')],
        ['icon' => '📊', 'name' => 'Resultados operacionais', 'sub' => 'Dashboard público', 'href' => route('resultados.publicos')],
    ];

    $sumario = [
        ['id' => 'conduta', 'label' => '1. Conduta Policial'],
        ['id' => 'forca', 'label' => '2. Uso da Força'],
        ['id' => 'perseguicoes', 'label' => '3. Perseguições e Abordagens'],
        ['id' => 'blipadas', 'label' => '4. Blipadas e Ações Policiais'],
        ['id' => 'ilicitos', 'label' => '5. Recolhimento de Ilícitos'],
        ['id' => 'relatorios', 'label' => '6. Relatórios e Registro'],
        ['id' => 'armamento', 'label' => '7. Armamento e Viaturas'],
        ['id' => 'hierarquia', 'label' => '8. Cargos e Hierarquia'],
        ['id' => 'penalidades', 'label' => '9. Penalidades'],
        ['id' => 'cerimonia', 'label' => '10. Cerimônia e Cursos'],
        ['id' => 'discord', 'label' => '11. Discord / WhatsApp'],
        ['id' => 'viaturas', 'label' => '12. Modificações de Viaturas'],
        ['id' => 'exoneracoes', 'label' => '13. Exonerações'],
        ['id' => 'convite-direto', 'label' => 'Convite Direto'],
        ['id' => 'patrulhamento', 'label' => 'Regras de Patrulhamento'],
        ['id' => 'promocoes', 'label' => 'Regras de Promoções'],
        ['id' => 'regras-policiais', 'label' => 'Regras Policiais'],
        ['id' => 'codigo-penal', 'label' => 'Código Penal'],
    ];
@endphp

{{-- TOPBAR --}}
<div class="portal-topbar portal-topbar--gov">
    <div class="portal-container">
        <div class="portal-topbar-inner portal-topbar-inner--gov">

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="portal-govbrand">
                    <div class="portal-govlogo">fivem<span>.bc</span></div>
                </div>

                <span class="portal-sep" aria-hidden="true">|</span>

                <span class="portal-muted">
                    Ministério da Justiça e Segurança Pública
                </span>

                <nav class="portal-links d-none d-lg-flex" aria-label="Navegação superior">
                    @foreach ($topLinks as $link)
                        @php $isActive = request()->routeIs($link['route']); @endphp
                        <a
                            href="{{ $link['href'] }}"
                            class="portal-toplink {{ $isActive ? 'is-active' : '' }}"
                            @if($isActive) aria-current="page" @endif
                        >
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button
                    class="portal-iconbtn portal-iconbtn--gov"
                    type="button"
                    title="Alternar tema"
                    id="toggleTheme"
                    aria-label="Alternar tema"
                >
                    ◐
                </button>

                <a href="{{ route('login') }}" class="btn portal-enter-btn portal-enter-btn--gov">
                    Entrar com FIVEM.BC
                </a>
            </div>

        </div>
    </div>
</div>

{{-- HEADER COMPACTO --}}
<header class="portal-header portal-header--gov portal-header--legal-compact">
    <div class="portal-container">

        <div class="portal-header-inner portal-header-inner--gov portal-header-inner--legal">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button
                    class="portal-menu portal-menu--gov"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#menuPublico"
                    aria-label="Abrir menu"
                >
                    ☰
                </button>

                <div>
                    <div class="portal-org-title portal-org-title--gov">
                        Legislação • GRR
                    </div>
                    <div class="portal-muted small">
                        Regras internas, diretrizes operacionais e Código Penal fictício
                    </div>
                </div>
            </div>

            <div class="portal-legal-shortcuts d-flex flex-wrap gap-2">
                <a href="#regras" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">📜</span>
                    Regras Gerais
                </a>

                <a href="#codigo-penal" class="portal-pill portal-pill--gov text-decoration-none">
                    <span class="portal-pill-ico">⚖️</span>
                    Código Penal
                </a>
            </div>
        </div>

    </div>
</header>

{{-- MENU OFFCANVAS --}}
<div
    class="offcanvas offcanvas-start portal-offcanvas"
    tabindex="-1"
    id="menuPublico"
    aria-labelledby="menuPublicoLabel"
>
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

        <nav class="portal-simple-nav" aria-label="Navegação do portal">
            @foreach ($menuLinks as $item)
                <a class="portal-simple-link" href="{{ $item['href'] }}">
                    <span class="ico">{{ $item['icon'] }}</span>
                    <span class="txt">
                        <span class="name">{{ $item['name'] }}</span>
                        <span class="sub">{{ $item['sub'] }}</span>
                    </span>
                </a>
            @endforeach
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

<main class="portal-main portal-main--legal">

    {{-- CABEÇALHO DA PÁGINA --}}
    <section class="portal-legal-intro" id="regras">
        <div class="portal-container">

            <div class="portal-legal-head">
                <div class="d-flex flex-column gap-3">
                    <div class="portal-legal-title-wrap">
                        <div class="portal-legal-kicker">DOCUMENTO INSTITUCIONAL • BRASIL CAPITAL</div>
                        <h1 class="portal-legal-title mb-2">Regras Gerais Internas</h1>
                        <p class="portal-muted mb-0">
                            Base normativa institucional para policiamento, conduta, procedimentos operacionais e organização interna.
                        </p>
                    </div>

                    <div class="portal-legal-alert" role="alert">
                        <div class="portal-legal-alert-ico" aria-hidden="true">⚠️</div>
                        <div class="portal-legal-alert-text">
                            Todo o conteúdo aqui descrito é <b>exclusivamente fictício</b> e destinado ao ambiente de jogo.
                            Não possui vínculo com corporações reais.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <label for="topSearchMirror" class="form-label fw-semibold mb-2">Busca rápida no Código Penal</label>
                            <input
                                id="topSearchMirror"
                                type="text"
                                class="form-control"
                                placeholder="Ex.: homicídio, art. 157, desacato..."
                                autocomplete="off"
                            >
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="portal-legal-helper-card">
                                <div class="fw-semibold mb-1">Leitura recomendada</div>
                                <div class="portal-muted small mb-0">
                                    Use o sumário lateral para navegar com rapidez, ou digite um termo e vá direto ao artigo no Código Penal.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="portal-legal-grid">
                <aside class="portal-legal-nav" aria-label="Sumário da legislação">
                    <div class="portal-legal-nav-title">Sumário</div>

                    @foreach ($sumario as $item)
                        @if($item['id'] === 'convite-direto')
                            <hr class="portal-legal-hr">
                        @endif

                        <a href="#{{ $item['id'] }}" class="portal-legal-nav-link">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </aside>

                <article class="portal-legal-doc">

                    <section class="portal-legal-card" id="conduta">
                        <h2>1. Conduta Policial</h2>
                        <p><b>1.1</b> – Todo policial deve manter postura ética e respeitosa, seja com civis, colegas ou criminosos.</p>
                        <p><b>1.2</b> – Abuso de autoridade, ameaças ou uso de força injustificada serão punidos.</p>
                        <p><b>1.3</b> – É proibido <b>atuar em RP policial fora do serviço</b> (férias, folga ou logado fora de base sem farda).</p>
                        <p><b>1.4</b> – É proibido utilizar fardas ou viaturas para fins pessoais ou fora de operação oficial.</p>
                    </section>

                    <section class="portal-legal-card" id="forca">
                        <h2>2. Uso da Força</h2>
                        <p><b>2.1</b> – O uso de arma de fogo deve ser sempre <b>a última opção</b>.</p>
                        <p><b>2.2</b> – Só é permitido abrir fogo:</p>
                        <ul>
                            <li>Em legítima defesa;</li>
                            <li>Para proteger terceiros de ameaça iminente;</li>
                            <li>Em ações autorizadas pela liderança (blipadas ou grandes operações);</li>
                        </ul>
                        <p><b>2.3</b> – Disparos de advertência são permitidos <b>somente com autorização do superior imediato</b>.</p>
                    </section>

                    <section class="portal-legal-card" id="perseguicoes">
                        <h2>3. Perseguições e Abordagens</h2>
                        <p><b>3.1</b> – Toda abordagem deve ser feita com <b>fundada suspeita</b>, baseada em atitudes suspeitas, fuga ou denúncias.</p>
                        <p><b>3.2</b> – Perseguições devem sempre <b>preservar a segurança pública</b>.</p>
                        <p><b>3.3</b> – Em abordagem de trânsito, é obrigatório informar que se trata de uma abordagem de rotina.</p>
                        <p><b>3.4</b> – Em abordagens de trânsito, é proibido revistar os indivíduos do veículo.</p>
                    </section>

                    <section class="portal-legal-card" id="blipadas">
                        <h2>4. Blipadas e Ações Policiais</h2>
                        <p><b>4.1</b> – Toda ação blipada é prioridade de todo policial, independentemente da unidade.</p>
                        <p><b>4.2</b> – É obrigatório o uso de colete, armamento padrão e rádio durante a ação.</p>
                        <p><b>4.3</b> – O responsável pela ação será a maior patente presente ou, em empate, o policial com maior tempo de casa.</p>
                    </section>

                    <section class="portal-legal-card" id="ilicitos">
                        <h2>5. Recolhimento de Ilícitos</h2>
                        <p><b>5.1</b> – É permitido recolher itens ilegais de players:</p>
                        <ul>
                            <li>Presos formalmente;</li>
                            <li>Internados no hospital;</li>
                            <li>Mortos após encaminhamento ao IML ou hospital;</li>
                        </ul>
                        <p><b>5.2</b> – É proibido recolher itens que não sejam considerados ilícitos.</p>
                        <p>
                            <b>Itens ilegais:</b> Lockpick (a partir de 2 unidades ou quando usada em crime), algema (a partir de 2 unidades ou quando usada em crime),
                            munição sem porte, bomba caseira, armas sem porte, drogas e dinheiro marcado.
                        </p>
                    </section>

                    <section class="portal-legal-card" id="relatorios">
                        <h2>6. Relatórios e Registro de Atividades</h2>
                        <p><b>6.1</b> – Toda ocorrência relevante deve ser registrada em relatório oficial.</p>
                        <p><b>6.2</b> – Ações blipadas, apreensões e ocorrências com morte devem conter:</p>
                        <ul>
                            <li>Print ou gravação;</li>
                            <li>ID dos envolvidos;</li>
                            <li>Resumo objetivo da situação;</li>
                        </ul>
                        <p><b>6.3</b> – Falsificação, omissão de informações ou manipulação de registros podem gerar punição administrativa.</p>
                    </section>

                    <section class="portal-legal-card" id="armamento">
                        <h2>7. Uso de Armamento e Viaturas</h2>
                        <p><b>7.1</b> – Só é permitido utilizar armamento e viaturas da unidade à qual o policial pertence.</p>
                        <p><b>7.2</b> – É proibido circular com viaturas de outra unidade sem autorização.</p>
                        <p><b>7.3</b> – O uso de armamento deve respeitar os cursos e qualificações já realizados.</p>
                    </section>

                    <section class="portal-legal-card" id="hierarquia">
                        <h2>8. Cargos e Hierarquia</h2>
                        <p><b>8.1</b> – Todos devem respeitar a hierarquia e as ordens dos superiores.</p>
                        <p><b>8.2</b> – Decisões operacionais são de responsabilidade dos comandantes das unidades ou da maior patente presente.</p>
                        <p><b>8.3</b> – Situações de abuso de poder devem ser reportadas via ticket na Corregedoria.</p>
                    </section>

                    <section class="portal-legal-card" id="penalidades">
                        <h2>9. Penalidades e Advertências</h2>
                        <p><b>9.1</b> – O descumprimento de condutas, abandono de serviço, desobediência e demais infrações administrativas poderá gerar advertência.</p>
                        <p><b>9.2</b> – Três advertências acumuladas resultam em expulsão, salvo deliberação superior em caso mais grave.</p>
                    </section>

                    <section class="portal-legal-card" id="cerimonia">
                        <h2>10. Cerimônia e Cursos</h2>
                        <p><b>10.1</b> – Eventos e cursos devem ocorrer, preferencialmente, até 00h (Nárnia). Caso não seja possível finalizar, o conteúdo deverá ser dividido em mais de um dia, respeitando os limites operacionais.</p>
                    </section>

                    <section class="portal-legal-card" id="discord">
                        <h2>11. Discords e Grupos de WhatsApp</h2>

                        <div class="portal-legal-callout">
                            <div class="portal-legal-callout-ico" aria-hidden="true">⚠️</div>
                            <div>
                                É proibido criar, administrar ou participar de grupos não oficiais para tratar de assuntos da corporação.
                            </div>
                        </div>

                        <p><b>11.1</b> – A comunicação oficial deve ocorrer apenas pelos canais institucionais autorizados.</p>
                        <p><b>11.2</b> – O descumprimento poderá resultar em advertência e banimento aos administradores dos grupos.</p>
                        <p><b>11.3</b> – Denúncias acompanhadas de provas poderão gerar isenção de responsabilidade e eventual reconhecimento pelo Comando Geral.</p>
                    </section>

                    <section class="portal-legal-card" id="viaturas">
                        <h2>12. Modificações de Viaturas</h2>
                        <p><b>12.1</b> – É proibido instalar turbo, suspensão e vidro fumê. A tunagem é opcional e sem reembolso. Exceção: vidros fumê apenas para a Polícia Civil.</p>
                    </section>

                    <section class="portal-legal-card" id="exoneracoes">
                        <h2>13. Exonerações e Pedido de Baixa</h2>
                        <ul>
                            <li><b>Pedido de baixa:</b> retorno na mesma temporada via TAF.</li>
                            <li><b>Ausência:</b> retorno na mesma temporada via TAF.</li>
                            <li><b>Abandono:</b> retorno na mesma temporada via TAF.</li>
                            <li><b>Exoneração disciplinar:</b> retorno apenas na próxima temporada.</li>
                        </ul>
                        <p>O histórico de PAD e advertências permanece registrado.</p>
                    </section>

                    <section class="portal-legal-card" id="convite-direto">
                        <h2>Regras sobre Convite Direto</h2>
                        <p>Trata-se de exceção ao edital. É permitido somente por convite oficial do comando e com informação formal à Corregedoria.</p>
                        <p><b>Requisitos:</b></p>
                        <ol>
                            <li>Bom comportamento;</li>
                            <li>Experiência em RP policial;</li>
                            <li>Existência de vagas estratégicas;</li>
                            <li>CNH categoria B;</li>
                            <li>Ausência de passagem criminal;</li>
                            <li>Estar desempregado, com carteira disponível;</li>
                        </ol>
                        <p><b>Não será permitido</b> caso o candidato tenha sido exonerado na temporada, possua PAD transitado ou tenha solicitado baixa na mesma temporada.</p>
                    </section>

                    <section class="portal-legal-card" id="patrulhamento">
                        <h2>Regras de Patrulhamento</h2>

                        <h3>1. Disparo em Pneus</h3>
                        <ul>
                            <li>Quando o veículo capota e continua fugindo;</li>
                            <li>Quando o veículo joga intencionalmente em motos;</li>
                            <li>Quando o veículo colide nas viaturas para removê-las da perseguição;</li>
                        </ul>
                        <p>É necessária autorização expressa da maior patente em QRA na rádio.</p>

                        <h3>2. Aplicação de Multas</h3>
                        <p><b>2.1</b> – Podem aplicar multas: PM, GCM e PRF.</p>
                        <p><b>2.2</b> – É vedado à Polícia Civil.</p>
                        <p><b>2.3</b> – Em caso de multa indevida, o valor deverá ser ressarcido em dobro pelo policial responsável.</p>

                        <h3>3. Lockpick Policial</h3>
                        <p><b>3.1</b> – Cada viatura poderá portar 01 lockpick, sob responsabilidade do chefe de barca.</p>
                        <p><b>3.2</b> – O uso é permitido apenas quando o motorista se negar a abrir o veículo, com desenvolvimento adequado de RP.</p>
                        <p><b>3.3</b> – Uso incorreto ou porte indevido poderá gerar advertência.</p>

                        <h3>4. Polícia Civil</h3>
                        <ul>
                            <li>Das 06h às 18h: mínimo de 2 policiais na delegacia;</li>
                            <li>Das 18h às 06h: mínimo de 4 policiais na delegacia;</li>
                        </ul>
                    </section>

                    <section class="portal-legal-card" id="promocoes">
                        <h2>Regras de Promoções</h2>

                        <div class="portal-legal-callout">
                            <div class="portal-legal-callout-ico" aria-hidden="true">⚠️</div>
                            <div>Objetivo: garantir crescimento equilibrado, meritocracia e evitar o inchaço indevido de cargos.</div>
                        </div>

                        <h3>1. Estrutura de Efetivo</h3>
                        <p><b>PM:</b> 1 Oficial para cada 2 Praças Graduadas; 1 Praça Graduada para cada 2 Praças.</p>
                        <p><b>PC / GCM / PRF:</b> 1 investigador+ para cada 2 agentes; 1 classe especial+ para cada 2 agentes.</p>

                        <h3>2. Critérios Gerais</h3>
                        <ul>
                            <li>Tempo mínimo no cargo;</li>
                            <li>Ausência de punições nos últimos 30 dias;</li>
                            <li>Participação ativa;</li>
                            <li>Qualificações exigidas;</li>
                        </ul>

                        <h3>3. Avaliação</h3>
                        <p>Toda promoção deve passar por avaliação e aprovação do comando responsável.</p>

                        <h3>4. Limites</h3>
                        <ul>
                            <li>É vedado pular patentes, salvo convite direto autorizado pelo Comando Geral;</li>
                            <li>É proibida promoção por amizade, favorecimento ou interesse pessoal;</li>
                            <li>Descumprimentos podem resultar em reversão de patente e punição ao responsável;</li>
                        </ul>
                    </section>

                    <section class="portal-legal-card" id="regras-policiais">
                        <h2>Regras Policiais</h2>

                        <h3>1. Hierarquia e Autoridade</h3>
                        <p><b>1.1</b> – A palavra final é da maior patente presente em serviço.</p>
                        <p><b>1.3</b> – Alunos só podem patrulhar acompanhados por superiores.</p>

                        <h3>2. Conduta e Postura</h3>
                        <p><b>2.2</b> – São proibidas ofensas, humilhações e atitudes agressivas sem contexto operacional legítimo.</p>
                        <p><b>2.5</b> – Fora do plantão, o policial poderá portar apenas pistola e 50 munições.</p>

                        <h3>3. Patrulhamento e Comunicação</h3>
                        <p><b>3.1</b> – Em serviço, é obrigatório estar na call do Discord da unidade.</p>
                        <p><b>3.5</b> – Entrada em comunidades somente das 06h às 20h, com no mínimo 2 viaturas da mesma especializada.</p>
                        <p><b>3.6</b> – Das 20h às 06h, é proibido entrar em comunidades.</p>

                        <h3>4. Viaturas e Equipamentos</h3>
                        <p><b>4.1</b> – É vedado utilizar turbo, suspensão e vidro fumê, salvo exceções já definidas para unidades específicas.</p>

                        <h3>5. Revistas, Prisões e BO</h3>
                        <p><b>5.1</b> – A revista pessoal somente poderá ocorrer com fundada suspeita ou vínculo direto com a ação.</p>
                        <p><b>5.7</b> – Em abordagens de trânsito, a revista é proibida.</p>

                        <h3>6. Fugas e Acompanhamentos</h3>
                        <p><b>6.1</b> – É permitido o máximo de 4 viaturas por veículo em fuga, sendo motos contabilizadas como 1 viatura.</p>

                        <h3>7. Corrupção e Desvios</h3>
                        <p><b>7.1</b> – Corrupção direta é terminantemente proibida e poderá resultar em banimento.</p>

                        <h3>8. Negociações e Reféns</h3>
                        <p><b>8.1</b> – A negociação é obrigatória antes de qualquer ação tática envolvendo reféns.</p>

                        <h3>9. Investigação e Invasões</h3>
                        <p><b>9.2</b> – É proibido acampar em farms; as ações devem ser iniciadas fora do local.</p>

                        <h3>10. Infrações e Punições</h3>
                        <p>As penalidades podem variar entre advertência, suspensão, exoneração e banimento, conforme a gravidade da infração.</p>
                    </section>

                    <section class="portal-legal-card" id="codigo-penal">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
                            <div>
                                <h2 class="mb-1">Código Penal</h2>
                                <p class="portal-muted mb-0">
                                    Consulte artigos, multas e penas aplicáveis no cenário RP. Use os filtros por categoria ou pesquise por nome e número do artigo.
                                </p>
                            </div>

                            <div class="text-lg-end">
                                <div class="small portal-muted">Resultado da busca</div>
                                <div class="fw-bold" id="cpResultsInfo">Exibindo todos os artigos</div>
                            </div>
                        </div>

                        <div class="cp-wrap" id="cp">
                            <div class="cp-top">
                                <div class="cp-search">
                                    <span class="cp-search-ico" aria-hidden="true">🔎</span>
                                    <input
                                        id="cpSearch"
                                        class="cp-search-input"
                                        type="text"
                                        placeholder="Buscar artigo ou nome..."
                                        autocomplete="off"
                                        aria-label="Buscar artigo do Código Penal"
                                    />
                                </div>

                                <div class="cp-hint">
                                    Clique em uma categoria para filtrar ou use a busca para encontrar artigos rapidamente.
                                </div>
                            </div>

                            <div class="cp-tabs" role="tablist" aria-label="Categorias do Código Penal">
                                <button class="cp-tab is-active" data-cat="all" type="button">Todas <span class="cp-count" data-count="all">0</span></button>
                                <button class="cp-tab" data-cat="transito" type="button">Trânsito <span class="cp-count" data-count="transito">0</span></button>
                                <button class="cp-tab" data-cat="pessoa" type="button">Crimes contra a pessoa <span class="cp-count" data-count="pessoa">0</span></button>
                                <button class="cp-tab" data-cat="patrimonio" type="button">Crimes contra o patrimônio <span class="cp-count" data-count="patrimonio">0</span></button>
                                <button class="cp-tab" data-cat="ordem" type="button">Crimes contra a ordem <span class="cp-count" data-count="ordem">0</span></button>
                                <button class="cp-tab" data-cat="armas" type="button">Lei de Armas e Drogas <span class="cp-count" data-count="armas">0</span></button>
                            </div>

                            <div class="cp-grid" id="cpGrid">

                                <div class="cp-card" data-cat="transito" data-text="art 218 alta velocidade r$ 1500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 218</span></div>
                                    <div class="cp-title">Alta Velocidade</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 1.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 175 direção perigosa r$ 3000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 175</span></div>
                                    <div class="cp-title">Direção Perigosa</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 3.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 181 estacionar em local proibido r$ 2500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 181</span></div>
                                    <div class="cp-title">Estacionar em local proibido</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 2.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 01 pousar em local proibido ou sem designação r$ 30000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 01</span></div>
                                    <div class="cp-title">Pousar em local proibido ou sem designação</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 30.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 02 manobra imprudente com aeronave r$ 25000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 02</span></div>
                                    <div class="cp-title">Manobra imprudente com aeronave</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 25.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 24 veículo abandonado r$ 1750,00">
                                    <div class="cp-head"><span class="cp-art">Art. 24</span></div>
                                    <div class="cp-title">Veículo Abandonado</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 1.750,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 186 trafegar na contra mão r$ 2000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 186</span></div>
                                    <div class="cp-title">Trafegar na contra mão</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 2.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 135 omissão de socorro 15 r$ 4000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 135</span></div>
                                    <div class="cp-title">Omissão de Socorro</div>
                                    <div class="cp-meta"><span class="cp-pena">15</span><span class="cp-price">R$ 4.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 180 falta de combustível em vias r$ 6780,00">
                                    <div class="cp-head"><span class="cp-art">Art. 180</span></div>
                                    <div class="cp-title">Falta de combustível em vias</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 6.780,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 162 dirigir sem habilitação r$ 3000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 162</span></div>
                                    <div class="cp-title">Dirigir sem habilitação</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 3.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 210 ultrapassar blitz 10 r$ 3470,00">
                                    <div class="cp-head"><span class="cp-art">Art. 210</span></div>
                                    <div class="cp-title">Ultrapassar blitz</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 3.470,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 225 praticar corrida ilegal r$ 5000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 225</span></div>
                                    <div class="cp-title">Praticar corrida ilegal</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 5.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 311 clonagem de placas r$ 5000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 311</span></div>
                                    <div class="cp-title">Clonagem de placas</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 5.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 244-a conduzir motocicleta sem usar capacete r$ 1750,00">
                                    <div class="cp-head"><span class="cp-art">Art. 244-A</span></div>
                                    <div class="cp-title">Conduzir motocicleta sem usar capacete</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 1.750,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 230 conduzir veículo com as taxas atrasadas r$ 2300,00">
                                    <div class="cp-head"><span class="cp-art">Art. 230</span></div>
                                    <div class="cp-title">Conduzir veículo com as taxas atrasadas</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 2.300,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="transito" data-text="art 244-b conduzir motocicleta fazendo malabarismo r$ 1900,00">
                                    <div class="cp-head"><span class="cp-art">Art. 244-B</span></div>
                                    <div class="cp-title">Conduzir motocicleta fazendo malabarismo</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 1.900,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="tentativa de homicídio 10 r$ 1000,00">
                                    <div class="cp-head"><span class="cp-art">Tentativa de Homicídio</span></div>
                                    <div class="cp-title"></div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 1.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 121 homicídio 30 r$ 3000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 121</span></div>
                                    <div class="cp-title">Homicídio</div>
                                    <div class="cp-meta"><span class="cp-pena">30</span><span class="cp-price">R$ 3.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 121-a homicídio de funcionário público 40 r$ 6300,00">
                                    <div class="cp-head"><span class="cp-art">Art. 121-A</span></div>
                                    <div class="cp-title">Homicídio de funcionário público</div>
                                    <div class="cp-meta"><span class="cp-pena">40</span><span class="cp-price">R$ 6.300,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 121-b homicídio culposo 10 r$ 800,00">
                                    <div class="cp-head"><span class="cp-art">Art. 121-B</span></div>
                                    <div class="cp-title">Homicídio culposo</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 800,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 129 lesão corporal 15 r$ 1500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 129</span></div>
                                    <div class="cp-title">Lesão corporal</div>
                                    <div class="cp-meta"><span class="cp-pena">15</span><span class="cp-price">R$ 1.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 138 calúnia 10 r$ 2000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 138</span></div>
                                    <div class="cp-title">Calúnia</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 2.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 139 difamação 10 r$ 2000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 139</span></div>
                                    <div class="cp-title">Difamação</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 2.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 140 injúria 10 r$ 2000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 140</span></div>
                                    <div class="cp-title">Injúria</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 2.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 147 ameaça 15 r$ 2500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 147</span></div>
                                    <div class="cp-title">Ameaça</div>
                                    <div class="cp-meta"><span class="cp-pena">15</span><span class="cp-price">R$ 2.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 148 sequestro e cárcere privado 20 r$ 4500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 148</span></div>
                                    <div class="cp-title">Sequestro e cárcere privado</div>
                                    <div class="cp-meta"><span class="cp-pena">20</span><span class="cp-price">R$ 4.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 121-c feminicídio 50 r$ 10200,00">
                                    <div class="cp-head"><span class="cp-art">Art. 121-C</span></div>
                                    <div class="cp-title">Feminicídio</div>
                                    <div class="cp-meta"><span class="cp-pena">50</span><span class="cp-price">R$ 10.200,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 215 importunação sexual 40 r$ 5000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 215</span></div>
                                    <div class="cp-title">Importunação sexual</div>
                                    <div class="cp-meta"><span class="cp-pena">40</span><span class="cp-price">R$ 5.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 286 incitação e/ou apologia ao crime 8 r$ 750,00">
                                    <div class="cp-head"><span class="cp-art">Art. 286</span></div>
                                    <div class="cp-title">Incitação e/ou apologia ao crime</div>
                                    <div class="cp-meta"><span class="cp-pena">8</span><span class="cp-price">R$ 750,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="pessoa" data-text="art 150 violação de domicílio 7 r$ 1300,00">
                                    <div class="cp-head"><span class="cp-art">Art. 150</span></div>
                                    <div class="cp-title">Violação de domicílio</div>
                                    <div class="cp-meta"><span class="cp-pena">7</span><span class="cp-price">R$ 1.300,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="patrimonio" data-text="art 155 furto 10 r$ 2500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 155</span></div>
                                    <div class="cp-title">Furto</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 2.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="patrimonio" data-text="art 157 roubo 20 r$ 4000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 157</span></div>
                                    <div class="cp-title">Roubo</div>
                                    <div class="cp-meta"><span class="cp-pena">20</span><span class="cp-price">R$ 4.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="patrimonio" data-text="art 157-a subtração de viatura 100 r$ 20000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 157-A</span></div>
                                    <div class="cp-title">Subtração de viatura</div>
                                    <div class="cp-meta"><span class="cp-pena">100</span><span class="cp-price">R$ 20.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="patrimonio" data-text="art 157-b roubo seguido de morte latrocínio 60 r$ 8000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 157-B</span></div>
                                    <div class="cp-title">Roubo seguido de morte (latrocínio)</div>
                                    <div class="cp-meta"><span class="cp-pena">60</span><span class="cp-price">R$ 8.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="patrimonio" data-text="art 168 apropriação indébita 10 r$ 3000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 168</span></div>
                                    <div class="cp-title">Apropriação indébita</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 3.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="patrimonio" data-text="art 171 estelionato 12 r$ 2500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 171</span></div>
                                    <div class="cp-title">Estelionato</div>
                                    <div class="cp-meta"><span class="cp-pena">12</span><span class="cp-price">R$ 2.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="patrimonio" data-text="art 126 desmonte de veículos 10 r$ 2500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 126</span></div>
                                    <div class="cp-title">Desmonte de veículos</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 2.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="patrimonio" data-text="art 180 receptação 10 r$ 2500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 180</span></div>
                                    <div class="cp-title">Receptação</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 2.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 200-a roupas policiais 50 r$ 5000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 200-A</span></div>
                                    <div class="cp-title">Roupas Policiais</div>
                                    <div class="cp-meta"><span class="cp-pena">50</span><span class="cp-price">R$ 5.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 200-b roupas militares 0 r$ 5000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 200-B</span></div>
                                    <div class="cp-title">Roupas Militares</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 5.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 200-c ocultação facial 0 r$ 5000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 200-C</span></div>
                                    <div class="cp-title">Ocultação facial</div>
                                    <div class="cp-meta"><span class="cp-pena">0</span><span class="cp-price">R$ 5.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 201 produtos ilícitos 13 r$ 0">
                                    <div class="cp-head"><span class="cp-art">Art. 201</span></div>
                                    <div class="cp-title">Produtos ilícitos</div>
                                    <div class="cp-meta"><span class="cp-pena">13</span><span class="cp-price">R$ 0</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 288 associação criminosa 15 r$ 2000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 288</span></div>
                                    <div class="cp-title">Associação criminosa</div>
                                    <div class="cp-meta"><span class="cp-pena">15</span><span class="cp-price">R$ 2.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 289 dinheiro ilícito 18 r$ 5000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 289</span></div>
                                    <div class="cp-title">Dinheiro ilícito</div>
                                    <div class="cp-meta"><span class="cp-pena">18</span><span class="cp-price">R$ 5.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 299 falsidade ideológica 10 r$ 1750,00">
                                    <div class="cp-head"><span class="cp-art">Art. 299</span></div>
                                    <div class="cp-title">Falsidade ideológica</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 1.750,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 330 desobediência 10 r$ 1700,00">
                                    <div class="cp-head"><span class="cp-art">Art. 330</span></div>
                                    <div class="cp-title">Desobediência</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 1.700,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 331 desacato 12 r$ 1500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 331</span></div>
                                    <div class="cp-title">Desacato</div>
                                    <div class="cp-meta"><span class="cp-pena">12</span><span class="cp-price">R$ 1.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 333 corrupção 20 r$ 3000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 333</span></div>
                                    <div class="cp-title">Corrupção</div>
                                    <div class="cp-meta"><span class="cp-pena">20</span><span class="cp-price">R$ 3.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 333-a suborno 15 r$ 2600,00">
                                    <div class="cp-head"><span class="cp-art">Art. 333-A</span></div>
                                    <div class="cp-title">Suborno</div>
                                    <div class="cp-meta"><span class="cp-pena">15</span><span class="cp-price">R$ 2.600,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 339 denunciação caluniosa 12 r$ 2000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 339</span></div>
                                    <div class="cp-title">Denunciação caluniosa</div>
                                    <div class="cp-meta"><span class="cp-pena">12</span><span class="cp-price">R$ 2.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 13.260 terrorismo 100 r$ 10000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 13.260</span></div>
                                    <div class="cp-title">Terrorismo</div>
                                    <div class="cp-meta"><span class="cp-pena">100</span><span class="cp-price">R$ 10.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 342 falso testemunho 12 r$ 2500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 342</span></div>
                                    <div class="cp-title">Falso testemunho</div>
                                    <div class="cp-meta"><span class="cp-pena">12</span><span class="cp-price">R$ 2.500,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 351 fuga de pessoa custodiada 12 r$ 2000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 351</span></div>
                                    <div class="cp-title">Fuga de pessoa custodiada</div>
                                    <div class="cp-meta"><span class="cp-pena">12</span><span class="cp-price">R$ 2.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 355 abuso de autoridade 30 r$ 5000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 355</span></div>
                                    <div class="cp-title">Abuso de autoridade</div>
                                    <div class="cp-meta"><span class="cp-pena">30</span><span class="cp-price">R$ 5.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="ordem" data-text="art 357 obstrução de justiça 12 r$ 2000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 357</span></div>
                                    <div class="cp-title">Obstrução de Justiça</div>
                                    <div class="cp-meta"><span class="cp-pena">12</span><span class="cp-price">R$ 2.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="armas" data-text="art 12 porte ou posse ilegal de arma classe 1 13 r$ 2000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 12</span></div>
                                    <div class="cp-title">Porte ou posse ilegal de arma classe 1</div>
                                    <div class="cp-meta"><span class="cp-pena">13</span><span class="cp-price">R$ 2.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="armas" data-text="art 16 porte ou posse ilegal de arma classe 2 15 r$ 3000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 16</span></div>
                                    <div class="cp-title">Porte ou posse ilegal de arma classe 2</div>
                                    <div class="cp-meta"><span class="cp-pena">15</span><span class="cp-price">R$ 3.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="armas" data-text="art 17 tráfico de munições 8 r$ 1800,00">
                                    <div class="cp-head"><span class="cp-art">Art. 17</span></div>
                                    <div class="cp-title">Tráfico de munições</div>
                                    <div class="cp-meta"><span class="cp-pena">8</span><span class="cp-price">R$ 1.800,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="armas" data-text="art 18 tráfico de armamento 20 r$ 3000,00">
                                    <div class="cp-head"><span class="cp-art">Art. 18</span></div>
                                    <div class="cp-title">Tráfico de armamento</div>
                                    <div class="cp-meta"><span class="cp-pena">20</span><span class="cp-price">R$ 3.000,00</span></div>
                                </div>

                                <div class="cp-card" data-cat="armas" data-text="art 33 tráfico de drogas 10 r$ 1500,00">
                                    <div class="cp-head"><span class="cp-art">Art. 33</span></div>
                                    <div class="cp-title">Tráfico de drogas</div>
                                    <div class="cp-meta"><span class="cp-pena">10</span><span class="cp-price">R$ 1.500,00</span></div>
                                </div>
                            </div>

                            <div id="cpEmptyState" class="text-center py-4 d-none">
                                <div class="fs-3 mb-2">📄</div>
                                <div class="fw-semibold">Nenhum artigo encontrado</div>
                                <div class="portal-muted small">
                                    Tente pesquisar por outro termo ou alterar a categoria selecionada.
                                </div>
                            </div>
                        </div>
                    </section>

                </article>
            </div>

        </div>
    </section>

    <footer class="portal-footer" id="contato">
        <div class="portal-container">
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>© {{ date('Y') }} GRR — Grupo de Resposta Rápida</div>
                <div class="portal-muted">FIVEM.BC • Portal Institucional • Legislação</div>
            </div>
        </div>
    </footer>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {
    const STORAGE_KEY = "grr_theme";
    const root = document.documentElement;
    const btn = document.getElementById("toggleTheme");

    function applyIcon(theme) {
        if (!btn) return;
        btn.textContent = theme === "dark" ? "☀️" : "🌙";
        btn.setAttribute("title", theme === "dark" ? "Ativar tema claro" : "Ativar tema escuro");
        btn.setAttribute("aria-label", theme === "dark" ? "Ativar tema claro" : "Ativar tema escuro");
    }

    function setTheme(theme) {
        root.setAttribute("data-theme", theme);
        localStorage.setItem(STORAGE_KEY, theme);
        applyIcon(theme);
    }

    function getPreferredTheme() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved === "light" || saved === "dark") return saved;

        return (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches)
            ? "dark"
            : "light";
    }

    setTheme(getPreferredTheme());

    if (btn) {
        btn.addEventListener("click", () => {
            const current = root.getAttribute("data-theme") || "light";
            setTheme(current === "dark" ? "light" : "dark");
        });
    }
})();
</script>

<script>
(function () {
    const top = document.getElementById('topSearchMirror');
    const cp = document.getElementById('cpSearch');

    if (!top || !cp) return;

    top.addEventListener('input', () => {
        cp.value = top.value;
        cp.dispatchEvent(new Event('input', { bubbles: true }));
    });
})();
</script>

<script>
(function () {
    const grid = document.getElementById('cpGrid');
    const search = document.getElementById('cpSearch');
    const tabs = document.querySelectorAll('.cp-tab');
    const emptyState = document.getElementById('cpEmptyState');
    const info = document.getElementById('cpResultsInfo');

    if (!grid || !search || !tabs.length) return;

    const cards = Array.from(grid.querySelectorAll('.cp-card'));
    let activeCat = 'all';

    function normalize(str) {
        return (str || "")
            .toString()
            .toLowerCase()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "");
    }

    function categoryLabel(cat) {
        const map = {
            all: 'todas as categorias',
            transito: 'Trânsito',
            pessoa: 'Crimes contra a pessoa',
            patrimonio: 'Crimes contra o patrimônio',
            ordem: 'Crimes contra a ordem',
            armas: 'Lei de Armas e Drogas'
        };
        return map[cat] || 'categoria selecionada';
    }

    function setCounts() {
        const counts = { all: 0 };

        cards.forEach(card => {
            const cat = card.dataset.cat || 'all';
            counts[cat] = (counts[cat] || 0) + 1;
            counts.all++;
        });

        document.querySelectorAll('.cp-count').forEach(el => {
            const key = el.getAttribute('data-count');
            el.textContent = counts[key] || 0;
        });
    }

    function updateInfo(visibleCount, query) {
        const label = categoryLabel(activeCat);

        if (!info) return;

        if (!query && activeCat === 'all') {
            info.textContent = `Exibindo todos os artigos (${visibleCount})`;
            return;
        }

        if (query) {
            info.textContent = `Encontrados ${visibleCount} resultado(s) em ${label}`;
            return;
        }

        info.textContent = `Exibindo ${visibleCount} artigo(s) em ${label}`;
    }

    function applyFilters() {
        const q = normalize(search.value || '');
        let visibleCount = 0;

        cards.forEach(card => {
            const cat = card.dataset.cat || 'all';
            const text = normalize(card.dataset.text || '');
            const okCat = (activeCat === 'all') || (cat === activeCat);
            const okText = !q || text.includes(q);
            const show = okCat && okText;

            card.style.display = show ? '' : 'none';

            if (show) visibleCount++;
        });

        if (emptyState) {
            emptyState.classList.toggle('d-none', visibleCount > 0);
        }

        updateInfo(visibleCount, q);
    }

    tabs.forEach(btn => {
        btn.addEventListener('click', () => {
            tabs.forEach(tab => {
                tab.classList.remove('is-active');
                tab.setAttribute('aria-selected', 'false');
            });

            btn.classList.add('is-active');
            btn.setAttribute('aria-selected', 'true');
            activeCat = btn.dataset.cat || 'all';

            applyFilters();
        });
    });

    search.addEventListener('input', applyFilters);

    setCounts();
    applyFilters();
})();
</script>

</body>
</html>