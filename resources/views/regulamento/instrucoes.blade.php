@extends('layouts.app')

@section('content')
<div class="container py-4">

  <div class="grr-page grr-manual">

    {{-- HERO --}}
    <div class="grr-hero mb-4">
      <div class="grr-hero__glow grr-hero__glow--blue"></div>
      <div class="grr-hero__glow grr-hero__glow--gold"></div>

      <div class="grr-hero__inner">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
          <div class="d-flex align-items-start gap-3">
            <div class="grr-hero__icon">📘</div>

            <div>
              <div class="grr-kicker mb-2">Manual introdutório</div>
              <h3 class="fw-black mb-2 grr-hero__title">
                Instruções Técnicas para Iniciantes da Polícia Rodoviária Federal
              </h3>
              <p class="grr-hero__sub mb-0">
                Manual destinado a orientar novos integrantes da PRF no ambiente RP,
                garantindo padronização, clareza operacional, boa comunicação
                e conduta adequada em serviço.
              </p>

              <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="grr-pill grr-pill--blue">Leitura obrigatória</span>
                <span class="grr-pill grr-pill--soft">Uso interno RP</span>
                <span class="grr-pill grr-pill--soft">Base para iniciantes</span>
              </div>
            </div>
          </div>

          <div class="grr-hero__aside">
            <div class="grr-hero__asideBox">
              <span class="grr-hero__asideLabel">Categoria</span>
              <strong class="grr-hero__asideValue">Manual técnico</strong>
            </div>
            <div class="grr-hero__asideBox">
              <span class="grr-hero__asideLabel">Aplicação</span>
              <strong class="grr-hero__asideValue">PRF • RP</strong>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- AVISO LEGAL --}}
    <div class="grr-alert grr-alert--warning mb-4">
      <div class="grr-alert__icon">⚠️</div>
      <div class="grr-alert__content">
        <div class="grr-alert__title">Aviso legal</div>
        <div class="grr-alert__text">
          Este documento é de caráter <b>fictício</b>, destinado exclusivamente ao uso no
          <b>Grand Theft Auto V — Role Play (RP)</b>, sem qualquer vínculo com a
          Polícia Rodoviária Federal real.
        </div>
      </div>
    </div>

    {{-- RESUMO --}}
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="grr-mini">
          <div class="grr-mini__label">Finalidade</div>
          <div class="grr-mini__value">Integração</div>
          <div class="grr-mini__sub">Orientar o policial iniciante antes do serviço operacional.</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="grr-mini">
          <div class="grr-mini__label">Conteúdo</div>
          <div class="grr-mini__value">06+</div>
          <div class="grr-mini__sub">Comunicação, rádio, viatura, revista e procedimentos básicos.</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="grr-mini">
          <div class="grr-mini__label">Prioridade</div>
          <div class="grr-mini__value">Alta</div>
          <div class="grr-mini__sub">Consulta recomendada antes de iniciar patrulhamento.</div>
        </div>
      </div>
    </div>

    {{-- SUMÁRIO VISUAL --}}
    <div class="grr-panel mb-4">
      <div class="grr-panel__inner">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <div class="grr-panel__title">📑 Conteúdo deste manual</div>
            <div class="grr-panel__sub">
              Consulte os principais tópicos abordados nesta instrução técnica.
            </div>
          </div>
          <span class="grr-pill grr-pill--soft">Leitura recomendada do início ao fim</span>
        </div>

        <div class="row g-3">
          <div class="col-md-6 col-xl-4">
            <div class="grr-topic">📡 Códigos Q e comunicação por rádio</div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="grr-topic">🔤 Alfabeto fonético</div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="grr-topic">📻 Uso do rádio</div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="grr-topic">🚓 Comandos da viatura</div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="grr-topic">🚫 Revista em sexo oposto</div>
          </div>
          <div class="col-md-6 col-xl-4">
            <div class="grr-topic">🚧 Cones e barreiras</div>
          </div>
        </div>
      </div>
    </div>

    {{-- MANUAL CÓDIGOS Q --}}
    <div class="grr-card mb-4">
      <div class="grr-card__body p-4">
        <div class="grr-sectionHead mb-3">
          <div class="grr-sectionHead__icon grr-sectionHead__icon--blue">📡</div>
          <div>
            <h5 class="fw-bold mb-1 grr-title-dark">Manual de Comunicação Rádio — Códigos Q</h5>
            <div class="grr-sectionHead__sub">Padronização de fala, objetividade e agilidade nas modulações.</div>
          </div>
        </div>

        <div class="row g-4">
          <div class="col-lg-4">
            <div class="grr-infoBox h-100">
              <div class="grr-infoBox__title">Objetivo</div>
              <p class="grr-text-soft mb-0">
                Fornecer um guia prático para o uso adequado dos <b>Códigos Q</b> nas comunicações via rádio,
                garantindo agilidade, clareza e precisão durante ocorrências e patrulhamentos.
              </p>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="grr-infoBox h-100">
              <div class="grr-infoBox__title">O que são?</div>
              <p class="grr-text-soft mb-0">
                Abreviações padronizadas usadas para facilitar a comunicação rápida e eficiente entre policiais,
                reduzindo o tempo de transmissão e evitando falhas de entendimento.
              </p>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="grr-infoBox h-100">
              <div class="grr-infoBox__title">Estrutura</div>
              <p class="grr-text-soft mb-0">
                Cada código inicia com a letra <b>Q</b>, seguida de duas letras,
                representando conceitos específicos, como confirmação, localização ou situação.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- LISTA CÓDIGOS Q --}}
    <div class="grr-card mb-4">
      <div class="grr-card__body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
          <div>
            <h6 class="fw-bold mb-1 grr-title-dark">Principais Códigos Q Utilizados</h6>
            <div class="grr-muted small">Referência rápida para modulação em serviço.</div>
          </div>
          <span class="grr-badgeSoft">Comunicação operacional</span>
        </div>

        <div class="row g-3">
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>QAP</b><span>Qual a situação?</span></div></div>
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>QRA</b><span>Qual o seu nome?</span></div></div>
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>QRU</b><span>Não tenho nada a relatar</span></div></div>
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>QRV</b><span>Estou à disposição</span></div></div>
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>QRX</b><span>Ocupado</span></div></div>
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>QSL</b><span>Entendido / Confirmado</span></div></div>
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>QSM</b><span>Repetir mensagem</span></div></div>
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>QTA</b><span>Qual a situação do atendimento?</span></div></div>
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>QTH</b><span>Qual a localização?</span></div></div>
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>QTO</b><span>Breve pausa</span></div></div>
          <div class="col-md-6 col-xl-4"><div class="grr-qcode"><b>TKS</b><span>Obrigado</span></div></div>
        </div>
      </div>
    </div>

    {{-- ALFABETO FONÉTICO --}}
    <div class="grr-card mb-4">
      <div class="grr-card__body p-4">
        <div class="grr-sectionHead mb-3">
          <div class="grr-sectionHead__icon grr-sectionHead__icon--indigo">🔤</div>
          <div>
            <h5 class="fw-bold mb-1 grr-title-dark">Alfabeto Fonético</h5>
            <div class="grr-sectionHead__sub">Padronização da transmissão de letras para evitar erros na comunicação.</div>
          </div>
        </div>

        <p class="grr-text-soft">
          O alfabeto fonético é utilizado para evitar erros na transmissão de letras,
          especialmente em ambientes com ruído, interferência ou comunicação via rádio.
        </p>

        <p class="grr-text-soft">
          Ele padroniza a forma como letras são transmitidas, garantindo clareza
          e reduzindo confusões entre sons semelhantes.
        </p>

        <h6 class="fw-bold mt-3 mb-2 grr-title-dark">Tabela do Alfabeto Fonético</h6>

        <div class="table-responsive phon-wrap">
          <table class="table table-sm align-middle phon-table mb-0">
            <thead>
              <tr>
                <th style="width: 90px;">LETRA</th>
                <th>CÓDIGO FONÉTICO</th>
                <th>PRONÚNCIA</th>
              </tr>
            </thead>
            <tbody>
              <tr><td class="fw-bold">A</td><td>Alpha</td><td>Al-fa</td></tr>
              <tr><td class="fw-bold">B</td><td>Bravo</td><td>Bra-vo</td></tr>
              <tr><td class="fw-bold">C</td><td>Charlie</td><td>Tchar-li</td></tr>
              <tr><td class="fw-bold">D</td><td>Delta</td><td>Del-ta</td></tr>
              <tr><td class="fw-bold">E</td><td>Echo</td><td>É-co</td></tr>
              <tr><td class="fw-bold">F</td><td>Foxtrot</td><td>Fox-trot</td></tr>
              <tr><td class="fw-bold">G</td><td>Golf</td><td>Golf</td></tr>
              <tr><td class="fw-bold">H</td><td>Hotel</td><td>Ho-tel</td></tr>
              <tr><td class="fw-bold">I</td><td>India</td><td>Ín-dia</td></tr>
              <tr><td class="fw-bold">J</td><td>Juliett</td><td>Dju-li-ét</td></tr>
              <tr><td class="fw-bold">K</td><td>Kilo</td><td>Qui-lo</td></tr>
              <tr><td class="fw-bold">L</td><td>Lima</td><td>Li-ma</td></tr>
              <tr><td class="fw-bold">M</td><td>Mike</td><td>Maik</td></tr>
              <tr><td class="fw-bold">N</td><td>November</td><td>No-vem-ber</td></tr>
              <tr><td class="fw-bold">O</td><td>Oscar</td><td>Ós-car</td></tr>
              <tr><td class="fw-bold">P</td><td>Papa</td><td>Pa-pa</td></tr>
              <tr><td class="fw-bold">Q</td><td>Quebec</td><td>Que-bec</td></tr>
              <tr><td class="fw-bold">R</td><td>Romeo</td><td>Ro-me-o</td></tr>
              <tr><td class="fw-bold">S</td><td>Sierra</td><td>Si-er-ra</td></tr>
              <tr><td class="fw-bold">T</td><td>Tango</td><td>Tan-go</td></tr>
              <tr><td class="fw-bold">U</td><td>Uniform</td><td>Iu-ni-form</td></tr>
              <tr><td class="fw-bold">V</td><td>Victor</td><td>Vic-tor</td></tr>
              <tr><td class="fw-bold">W</td><td>Whiskey</td><td>Uís-qui</td></tr>
              <tr><td class="fw-bold">X</td><td>X-Ray</td><td>Écs-rêi</td></tr>
              <tr><td class="fw-bold">Y</td><td>Yankee</td><td>Iân-qui</td></tr>
              <tr><td class="fw-bold">Z</td><td>Zulu</td><td>Zu-lu</td></tr>
            </tbody>
          </table>
        </div>

        <div class="grr-example mt-3">
          <div class="grr-example__title">Exemplo prático</div>
          <div class="grr-example__text">
            <b>Placa:</b> ABC1D34<br>
            <b>Modulação:</b> <i>Alpha, Bravo, Charlie, Primeiro, Delta, Terceiro, Quarto</i>
          </div>
        </div>
      </div>
    </div>

    {{-- BLOCOS INFERIORES --}}
    <div class="row g-4">

      <div class="col-lg-6">
        <div class="grr-card h-100">
          <div class="grr-card__body p-4">
            <div class="grr-sectionHead mb-3">
              <div class="grr-sectionHead__icon grr-sectionHead__icon--teal">📻</div>
              <div>
                <h5 class="fw-bold mb-1 grr-title-dark">Uso do Rádio</h5>
                <div class="grr-sectionHead__sub">Ferramenta obrigatória de comunicação em serviço.</div>
              </div>
            </div>

            <ul class="grr-list mb-0">
              <li>Uso obrigatório em serviço</li>
              <li>Frequência oficial: <b>191</b></li>
              <li>Acessar mochila → rádio → inserir frequência → ligar</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="grr-card h-100">
          <div class="grr-card__body p-4">
            <div class="grr-sectionHead mb-3">
              <div class="grr-sectionHead__icon grr-sectionHead__icon--blue">🚓</div>
              <div>
                <h5 class="fw-bold mb-1 grr-title-dark">Comandos da Viatura</h5>
                <div class="grr-sectionHead__sub">Ações rápidas e comandos principais utilizados em patrulha.</div>
              </div>
            </div>

            <ul class="grr-list mb-0">
              <li><b>Giroflex:</b> mentalizar <b>Q</b></li>
              <li><b>/giroflex1 a 4</b> — modos do giroflex</li>
              <li><b>Sirene:</b> mentalizar <b>ALT</b></li>
              <li><b>R</b> — muda som da sirene</li>
              <li><b>E</b> — sinal sonoro rápido</li>
              <li><b>/luzdebeco1 e 2</b></li>
              <li><b>BACKSPACE</b> — pisca alerta</li>
              <li><b>/seat 1 a 4</b> — troca de assento</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="grr-card grr-card--danger h-100">
          <div class="grr-card__body p-4">
            <div class="grr-sectionHead mb-3">
              <div class="grr-sectionHead__icon grr-sectionHead__icon--red">🚫</div>
              <div>
                <h5 class="fw-bold mb-1 grr-title-danger">Revista no Sexo Oposto</h5>
                <div class="grr-sectionHead__sub">Diretriz de conduta obrigatória.</div>
              </div>
            </div>

            <p class="grr-text-soft">
              É <b>proibido</b> realizar revista em pessoa do sexo oposto.
              O descumprimento gera sanções internas e consequências no RP.
            </p>

            <h6 class="fw-bold grr-title-dark">Procedimentos:</h6>
            <ul class="grr-list mb-0">
              <li>Chamar policial feminina, se disponível</li>
              <li>Procedimento da caixinha (<b>/e caixa</b> + <b>/revistar</b>)</li>
              <li>Em caso de resistência, condução ao batalhão</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="grr-card h-100">
          <div class="grr-card__body p-4">
            <div class="grr-sectionHead mb-3">
              <div class="grr-sectionHead__icon grr-sectionHead__icon--gold">🚧</div>
              <div>
                <h5 class="fw-bold mb-1 grr-title-dark">Cones e Barreiras</h5>
                <div class="grr-sectionHead__sub">Uso operacional para isolamento, organização e bloqueios.</div>
              </div>
            </div>

            <p class="grr-text-soft">Uso exclusivo em serviço, com aquisição no arsenal policial.</p>

            <ol class="grr-ol mb-0">
              <li>Comprar no arsenal</li>
              <li>Usar o item na mochila</li>
              <li>Posicionar com botão direito</li>
              <li>Confirmar com <b>E</b></li>
              <li>Para guardar: <b>ALT</b> → Guardar</li>
            </ol>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>

<style>
  .grr-page{
    background: linear-gradient(180deg, #f8fafc 0%, #f3f6fb 100%) !important;
    border: 1px solid rgba(15,23,42,.07);
    border-radius: 26px;
    padding: 20px;
    box-shadow:
      0 20px 60px rgba(2,6,23,.08),
      inset 0 1px 0 rgba(255,255,255,.78);
    color: #0f172a;
  }

  html[data-theme="dark"] .grr-page{
    background:
      radial-gradient(circle at top left, rgba(59,130,246,.07), transparent 28%),
      radial-gradient(circle at top right, rgba(245,158,11,.06), transparent 26%),
      linear-gradient(180deg, #0b1220 0%, #0f172a 100%) !important;
    border-color: rgba(255,255,255,.08) !important;
    box-shadow:
      0 24px 70px rgba(0,0,0,.42),
      inset 0 1px 0 rgba(255,255,255,.03);
    color: #e5edf7 !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-page{
      background:
        radial-gradient(circle at top left, rgba(59,130,246,.07), transparent 28%),
        radial-gradient(circle at top right, rgba(245,158,11,.06), transparent 26%),
        linear-gradient(180deg, #0b1220 0%, #0f172a 100%) !important;
      border-color: rgba(255,255,255,.08) !important;
      box-shadow:
        0 24px 70px rgba(0,0,0,.42),
        inset 0 1px 0 rgba(255,255,255,.03);
      color: #e5edf7 !important;
    }
  }

  .grr-manual{
    --grr-dark: #0f172a;
    --grr-text: #eaf0f8;
    --grr-sub: rgba(234,240,248,.76);
    --grr-muted: #64748b;
    --grr-radius: 20px;
  }

  .fw-black{ font-weight: 900; }

  .grr-page,
  .grr-page *{
    box-sizing: border-box;
  }

  .grr-page h1,
  .grr-page h2,
  .grr-page h3,
  .grr-page h4,
  .grr-page h5,
  .grr-page h6{
    color: #0f172a;
  }

  html[data-theme="dark"] .grr-page h1,
  html[data-theme="dark"] .grr-page h2,
  html[data-theme="dark"] .grr-page h3,
  html[data-theme="dark"] .grr-page h4,
  html[data-theme="dark"] .grr-page h5,
  html[data-theme="dark"] .grr-page h6{
    color: #f8fbff;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-page h1,
    html[data-theme="system"] .grr-page h2,
    html[data-theme="system"] .grr-page h3,
    html[data-theme="system"] .grr-page h4,
    html[data-theme="system"] .grr-page h5,
    html[data-theme="system"] .grr-page h6{
      color: #f8fbff;
    }
  }

  .grr-page p,
  .grr-page li,
  .grr-page ol,
  .grr-page ul,
  .grr-page small,
  .grr-page span,
  .grr-page div{
    color: inherit;
  }

  .grr-page .text-muted{
    color: #64748b !important;
  }

  .grr-muted{
    color: #64748b !important;
  }

  .grr-title-dark{
    color: #0f172a !important;
  }

  .grr-title-danger{
    color: #dc2626 !important;
  }

  .grr-text-soft{
    color: #5f6f84 !important;
    font-weight: 700;
    line-height: 1.65;
  }

  html[data-theme="dark"] .grr-page .text-muted,
  html[data-theme="dark"] .grr-muted{
    color: rgba(203,213,225,.72) !important;
  }

  html[data-theme="dark"] .grr-title-dark{
    color: #f8fbff !important;
  }

  html[data-theme="dark"] .grr-text-soft{
    color: rgba(203,213,225,.82) !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-page .text-muted,
    html[data-theme="system"] .grr-muted{
      color: rgba(203,213,225,.72) !important;
    }

    html[data-theme="system"] .grr-title-dark{
      color: #f8fbff !important;
    }

    html[data-theme="system"] .grr-text-soft{
      color: rgba(203,213,225,.82) !important;
    }
  }

  /* HERO */
  .grr-kicker{
    font-size: 11px;
    font-weight: 900;
    letter-spacing: .16em;
    text-transform: uppercase;
    color: rgba(191,219,254,.92) !important;
  }

  .grr-hero{
    position: relative;
    overflow: hidden;
    border-radius: 22px;
    background: linear-gradient(180deg, rgba(9,13,20,.97), rgba(15,23,42,.95));
    border: 1px solid rgba(15,23,42,.08);
    box-shadow: 0 24px 60px rgba(2,6,23,.18);
    isolation: isolate;
  }

  .grr-hero__inner{
    position: relative;
    z-index: 2;
    padding: 24px;
  }

  .grr-hero__glow{
    position: absolute;
    border-radius: 999px;
    filter: blur(55px);
    opacity: .34;
    z-index: 0;
  }

  .grr-hero__glow--blue{
    width: 240px;
    height: 240px;
    background: rgba(59,130,246,.34);
    top: -60px;
    left: -30px;
  }

  .grr-hero__glow--gold{
    width: 220px;
    height: 220px;
    background: rgba(245,158,11,.24);
    top: -40px;
    right: -30px;
  }

  .grr-hero__icon{
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
  }

  .grr-hero__title{
    color: var(--grr-text) !important;
    letter-spacing: -.02em;
  }

  .grr-hero__sub{
    max-width: 760px;
    color: var(--grr-sub) !important;
    font-weight: 650;
    line-height: 1.6;
  }

  .grr-hero__aside{
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .grr-hero__asideBox{
    min-width: 135px;
    padding: 12px 14px;
    border-radius: 16px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.10);
    backdrop-filter: blur(8px);
  }

  .grr-hero__asideLabel{
    display: block;
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: rgba(234,240,248,.56) !important;
    margin-bottom: 2px;
  }

  .grr-hero__asideValue{
    color: var(--grr-text) !important;
    font-size: 14px;
    font-weight: 900;
  }

  /* PILLS */
  .grr-pill{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding: 7px 11px;
    border-radius: 999px;
    font-size: 12px;
    line-height: 1;
    font-weight: 900;
    border: 1px solid rgba(255,255,255,.14);
    background: rgba(255,255,255,.07);
    color: var(--grr-text) !important;
    white-space: nowrap;
  }

  .grr-pill--blue{
    border-color: rgba(59,130,246,.35);
    background: rgba(59,130,246,.18);
    color: rgba(231,237,246,.97) !important;
  }

  .grr-pill--soft{
    background: rgba(255,255,255,.05);
    color: rgba(231,237,246,.84) !important;
  }

  /* ALERT */
  .grr-alert{
    display: flex;
    gap: 14px;
    align-items: flex-start;
    padding: 18px;
    border-radius: 18px;
    border: 1px solid rgba(180,120,0,.16);
    box-shadow: 0 14px 34px rgba(2,6,23,.08);
  }

  .grr-alert--warning{
    background: linear-gradient(180deg, rgba(255,237,181,.98), rgba(255,224,130,.95));
  }

  .grr-alert__icon{
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 20px;
    background: rgba(255,255,255,.38);
    border: 1px solid rgba(255,255,255,.48);
    flex-shrink: 0;
    color: #7c2d12 !important;
  }

  .grr-alert__title{
    font-size: 15px;
    font-weight: 900;
    color: rgba(17,24,39,.96) !important;
    margin-bottom: 2px;
  }

  .grr-alert__text{
    color: rgba(17,24,39,.84) !important;
    font-weight: 700;
    line-height: 1.6;
  }

  /* MINI */
  .grr-mini{
    height: 100%;
    padding: 16px;
    border-radius: 18px;
    background: rgba(255,255,255,.92);
    border: 1px solid rgba(15,23,42,.07);
    box-shadow: 0 12px 28px rgba(2,6,23,.06);
  }

  .grr-mini__label{
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .08em;
    font-weight: 900;
    color: #64748b !important;
    margin-bottom: 6px;
  }

  .grr-mini__value{
    font-size: 1.35rem;
    font-weight: 900;
    color: #0f172a !important;
    line-height: 1.1;
    margin-bottom: 6px;
  }

  .grr-mini__sub{
    font-size: 12px;
    line-height: 1.5;
    color: #64748b !important;
    font-weight: 700;
  }

  html[data-theme="dark"] .grr-mini{
    background: linear-gradient(180deg, rgba(15,23,42,.72), rgba(15,23,42,.54));
    border-color: rgba(255,255,255,.08);
    box-shadow: 0 10px 24px rgba(0,0,0,.18);
  }

  html[data-theme="dark"] .grr-mini__label{
    color: rgba(148,163,184,.90) !important;
  }

  html[data-theme="dark"] .grr-mini__value{
    color: #f8fbff !important;
  }

  html[data-theme="dark"] .grr-mini__sub{
    color: rgba(203,213,225,.82) !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-mini{
      background: linear-gradient(180deg, rgba(15,23,42,.72), rgba(15,23,42,.54));
      border-color: rgba(255,255,255,.08);
      box-shadow: 0 10px 24px rgba(0,0,0,.18);
    }

    html[data-theme="system"] .grr-mini__label{
      color: rgba(148,163,184,.90) !important;
    }

    html[data-theme="system"] .grr-mini__value{
      color: #f8fbff !important;
    }

    html[data-theme="system"] .grr-mini__sub{
      color: rgba(203,213,225,.82) !important;
    }
  }

  /* PANEL */
  .grr-panel{
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(15,23,42,.08);
    background: linear-gradient(180deg, rgba(13,18,28,.92), rgba(8,10,16,.88));
    box-shadow: 0 18px 50px rgba(2,6,23,.16);
  }

  .grr-panel__inner{ padding: 18px; }

  .grr-panel__title{
    color: rgba(255,255,255,.95) !important;
    font-weight: 900;
  }

  .grr-panel__sub{
    color: rgba(255,255,255,.66) !important;
    font-size: 12px;
    font-weight: 700;
  }

  .grr-topic{
    height: 100%;
    padding: 14px 15px;
    border-radius: 14px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.08);
    color: rgba(255,255,255,.90) !important;
    font-weight: 800;
    font-size: 13px;
  }

  /* CARD */
  .grr-card{
    border-radius: 20px;
    border: 1px solid rgba(15,23,42,.08);
    background: rgba(255,255,255,.94) !important;
    box-shadow: 0 14px 36px rgba(2,6,23,.07);
    overflow: hidden;
  }

  .grr-card--danger{
    border-color: rgba(220,53,69,.24);
    box-shadow: 0 14px 38px rgba(220,53,69,.08);
  }

  .grr-card__body{
    padding: 18px;
    color: #0f172a !important;
  }

  html[data-theme="dark"] .grr-card{
    border-color: rgba(255,255,255,.08) !important;
    background: linear-gradient(180deg, rgba(8,12,19,.95), rgba(11,18,32,.98)) !important;
    box-shadow: 0 20px 55px rgba(0,0,0,.28);
  }

  html[data-theme="dark"] .grr-card__body{
    color: #e5edf7 !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-card{
      border-color: rgba(255,255,255,.08) !important;
      background: linear-gradient(180deg, rgba(8,12,19,.95), rgba(11,18,32,.98)) !important;
      box-shadow: 0 20px 55px rgba(0,0,0,.28);
    }

    html[data-theme="system"] .grr-card__body{
      color: #e5edf7 !important;
    }
  }

  .grr-sectionHead{
    display: flex;
    align-items: flex-start;
    gap: 12px;
  }

  .grr-sectionHead__icon{
    width: 42px;
    height: 42px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 18px;
    flex-shrink: 0;
    border: 1px solid rgba(15,23,42,.08);
  }

  .grr-sectionHead__icon--blue{
    background: rgba(59,130,246,.10);
    border-color: rgba(59,130,246,.18);
  }

  .grr-sectionHead__icon--indigo{
    background: rgba(99,102,241,.10);
    border-color: rgba(99,102,241,.18);
  }

  .grr-sectionHead__icon--teal{
    background: rgba(20,184,166,.10);
    border-color: rgba(20,184,166,.18);
  }

  .grr-sectionHead__icon--gold{
    background: rgba(245,158,11,.12);
    border-color: rgba(245,158,11,.20);
  }

  .grr-sectionHead__icon--red{
    background: rgba(239,68,68,.12);
    border-color: rgba(239,68,68,.20);
  }

  html[data-theme="dark"] .grr-sectionHead__icon{
    border-color: rgba(255,255,255,.08);
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-sectionHead__icon{
      border-color: rgba(255,255,255,.08);
    }
  }

  .grr-sectionHead__sub{
    color: #64748b !important;
    font-size: 12px;
    font-weight: 700;
  }

  html[data-theme="dark"] .grr-sectionHead__sub{
    color: rgba(203,213,225,.72) !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-sectionHead__sub{
      color: rgba(203,213,225,.72) !important;
    }
  }

  .grr-infoBox{
    padding: 16px;
    border-radius: 16px;
    background: rgba(248,250,252,.98) !important;
    border: 1px solid rgba(15,23,42,.07);
  }

  .grr-infoBox__title{
    font-size: 13px;
    font-weight: 900;
    color: #0f172a !important;
    margin-bottom: 8px;
  }

  html[data-theme="dark"] .grr-infoBox{
    background: rgba(255,255,255,.03) !important;
    border-color: rgba(255,255,255,.08);
  }

  html[data-theme="dark"] .grr-infoBox__title{
    color: #f8fbff !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-infoBox{
      background: rgba(255,255,255,.03) !important;
      border-color: rgba(255,255,255,.08);
    }

    html[data-theme="system"] .grr-infoBox__title{
      color: #f8fbff !important;
    }
  }

  .grr-badgeSoft{
    display: inline-flex;
    align-items: center;
    padding: 7px 10px;
    border-radius: 999px;
    background: rgba(15,23,42,.06);
    color: #475569 !important;
    font-size: 11px;
    font-weight: 900;
    white-space: nowrap;
  }

  html[data-theme="dark"] .grr-badgeSoft{
    background: rgba(255,255,255,.06);
    color: rgba(226,232,240,.88) !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-badgeSoft{
      background: rgba(255,255,255,.06);
      color: rgba(226,232,240,.88) !important;
    }
  }

  .grr-qcode{
    height: 100%;
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 14px;
    border-radius: 15px;
    background: rgba(248,250,252,.98) !important;
    border: 1px solid rgba(15,23,42,.07);
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
  }

  .grr-qcode:hover{
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(2,6,23,.06);
    border-color: rgba(59,130,246,.16);
  }

  .grr-qcode b{
    font-size: 14px;
    color: #0f172a !important;
  }

  .grr-qcode span{
    font-size: 12px;
    color: #5f6f84 !important;
    font-weight: 700;
    line-height: 1.45;
  }

  html[data-theme="dark"] .grr-qcode{
    background: rgba(255,255,255,.03) !important;
    border-color: rgba(255,255,255,.08);
  }

  html[data-theme="dark"] .grr-qcode:hover{
    box-shadow: 0 10px 22px rgba(0,0,0,.16);
    border-color: rgba(59,130,246,.26);
  }

  html[data-theme="dark"] .grr-qcode b{
    color: #f8fbff !important;
  }

  html[data-theme="dark"] .grr-qcode span{
    color: rgba(203,213,225,.78) !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-qcode{
      background: rgba(255,255,255,.03) !important;
      border-color: rgba(255,255,255,.08);
    }

    html[data-theme="system"] .grr-qcode:hover{
      box-shadow: 0 10px 22px rgba(0,0,0,.16);
      border-color: rgba(59,130,246,.26);
    }

    html[data-theme="system"] .grr-qcode b{
      color: #f8fbff !important;
    }

    html[data-theme="system"] .grr-qcode span{
      color: rgba(203,213,225,.78) !important;
    }
  }

  .grr-example{
    padding: 14px 15px;
    border-radius: 15px;
    background: rgba(59,130,246,.06) !important;
    border: 1px solid rgba(59,130,246,.10);
  }

  .grr-example__title{
    font-size: 13px;
    font-weight: 900;
    color: #0f172a !important;
    margin-bottom: 5px;
  }

  .grr-example__text{
    font-size: 13px;
    color: #475569 !important;
    line-height: 1.55;
    font-weight: 700;
  }

  html[data-theme="dark"] .grr-example{
    background: rgba(59,130,246,.10) !important;
    border-color: rgba(59,130,246,.18);
  }

  html[data-theme="dark"] .grr-example__title{
    color: #f8fbff !important;
  }

  html[data-theme="dark"] .grr-example__text{
    color: rgba(203,213,225,.84) !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-example{
      background: rgba(59,130,246,.10) !important;
      border-color: rgba(59,130,246,.18);
    }

    html[data-theme="system"] .grr-example__title{
      color: #f8fbff !important;
    }

    html[data-theme="system"] .grr-example__text{
      color: rgba(203,213,225,.84) !important;
    }
  }

  .grr-list{
    padding-left: 18px;
    margin: 0;
  }

  .grr-list li{
    color: #475569 !important;
    font-weight: 700;
    line-height: 1.7;
    margin-bottom: 4px;
  }

  .grr-ol{
    padding-left: 18px;
    margin: 0;
  }

  .grr-ol li{
    color: #475569 !important;
    font-weight: 700;
    line-height: 1.7;
    margin-bottom: 4px;
  }

  html[data-theme="dark"] .grr-list li,
  html[data-theme="dark"] .grr-ol li{
    color: rgba(203,213,225,.82) !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .grr-list li,
    html[data-theme="system"] .grr-ol li{
      color: rgba(203,213,225,.82) !important;
    }
  }

  /* TABELA */
  .phon-wrap{
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(15,23,42,.10);
    background: #ffffff !important;
    box-shadow: 0 10px 24px rgba(2,6,23,.06);
  }

  .phon-table{
    width: 100%;
    margin: 0 !important;
    background: transparent !important;
    color: rgba(15,23,42,.92) !important;
    border-collapse: collapse;
  }

  .phon-table thead th{
    background: #ffffff !important;
    color: rgba(15,23,42,.60) !important;
    font-size: 11px;
    letter-spacing: .14em;
    text-transform: uppercase;
    font-weight: 950;
    padding: 13px 14px;
    border-bottom: 1px solid rgba(15,23,42,.10) !important;
    white-space: nowrap;
  }

  .phon-table tbody td{
    background: transparent !important;
    color: rgba(15,23,42,.90) !important;
    padding: 12px 14px;
    border-top: 1px solid rgba(15,23,42,.08) !important;
    font-weight: 600;
  }

  .phon-table tbody tr:hover td{
    background: rgba(2,6,23,.03) !important;
  }

  html[data-theme="dark"] .phon-wrap{
    background: #0b0f16 !important;
    border-color: rgba(255,255,255,.10) !important;
    box-shadow: 0 10px 26px rgba(0,0,0,.30) !important;
  }

  html[data-theme="dark"] .phon-table{
    color: rgba(255,255,255,.92) !important;
  }

  html[data-theme="dark"] .phon-table thead th{
    background: #0b0f16 !important;
    color: rgba(255,255,255,.72) !important;
    border-bottom-color: rgba(255,255,255,.10) !important;
  }

  html[data-theme="dark"] .phon-table tbody td{
    color: rgba(255,255,255,.92) !important;
    border-top-color: rgba(255,255,255,.08) !important;
  }

  html[data-theme="dark"] .phon-table tbody tr:hover td{
    background: rgba(255,255,255,.04) !important;
  }

  @media (prefers-color-scheme: dark){
    html[data-theme="system"] .phon-wrap{
      background: #0b0f16 !important;
      border-color: rgba(255,255,255,.10) !important;
      box-shadow: 0 10px 26px rgba(0,0,0,.30) !important;
    }

    html[data-theme="system"] .phon-table{
      color: rgba(255,255,255,.92) !important;
    }

    html[data-theme="system"] .phon-table thead th{
      background: #0b0f16 !important;
      color: rgba(255,255,255,.72) !important;
      border-bottom-color: rgba(255,255,255,.10) !important;
    }

    html[data-theme="system"] .phon-table tbody td{
      color: rgba(255,255,255,.92) !important;
      border-top-color: rgba(255,255,255,.08) !important;
    }

    html[data-theme="system"] .phon-table tbody tr:hover td{
      background: rgba(255,255,255,.04) !important;
    }
  }

  .phon-wrap .table,
  .phon-wrap .table thead,
  .phon-wrap .table tbody,
  .phon-wrap .table tr,
  .phon-wrap .table th,
  .phon-wrap .table td{
    background: transparent !important;
    color: inherit !important;
  }

  /* MOBILE */
  @media (max-width: 991.98px){
    .grr-hero__aside{
      justify-content: flex-start;
    }
  }

  @media (max-width: 576px){
    .grr-page{
      padding: 14px;
      border-radius: 20px;
    }

    .grr-hero__inner,
    .grr-card__body,
    .grr-panel__inner{
      padding: 16px 14px;
    }

    .grr-hero__icon{
      width: 50px;
      height: 50px;
      border-radius: 16px;
      font-size: 21px;
    }

    .grr-hero__title{
      font-size: 1.08rem;
    }
  }
</style>
@endsection