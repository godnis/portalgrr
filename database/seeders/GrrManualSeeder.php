<?php

namespace Database\Seeders;

use App\Models\GrrManual;
use App\Models\GrrManualArticle;
use App\Models\GrrManualSection;
use Illuminate\Database\Seeder;

class GrrManualSeeder extends Seeder
{
    public function run(): void
    {
        $manual = GrrManual::updateOrCreate(
            ['slug' => 'manual-interno-grr'],
            [
                'title'             => 'Manual Interno — Grupo de Resposta Rápida (GRR)',
                'kicker'            => 'Uso interno institucional',
                'subtitle'          => 'Documento oficial com normas, procedimentos e condutas obrigatórias aplicáveis aos integrantes da corporação no Brasil Capital Roleplay.',
                'description'       => 'Manual interno normativo da GRR.',
                'status_label'      => 'Interno',
                'environment_label' => 'Brasil Capital RP',
                'alert_title'       => 'Acesso restrito / uso interno',
                'alert_text'        => 'Este manual possui caráter interno e normativo. O descumprimento das regras, diretrizes e procedimentos previstos poderá gerar apuração administrativa, medidas disciplinares e demais providências cabíveis.',
                'is_published'      => true,
                'version'           => 1,
            ]
        );

        GrrManualSection::where('manual_id', $manual->id)->delete();

        $sections = [
            [
                'code' => 'Cap. I',
                'anchor' => 'cap1',
                'title' => 'Disposições Preliminares',
                'subtitle' => 'Normas introdutórias, apresentação institucional e deveres básicos.',
                'sort_order' => 1,
                'articles' => [
                    'Art. 1º' => 'O presente regulamento estabelece normas, procedimentos e condutas obrigatórias para todos os integrantes da Grupo de Resposta Rápida no Brasil Capital Roleplay, com o objetivo de garantir a disciplina, a ordem e a eficiência nas ações operacionais e administrativas, além de assegurar o máximo de realismo e profissionalismo na representação do órgão.',
                    'Art. 2º' => 'É dever do policial manter postura ética e disciplinar, zelando pela integridade física e moral dos colegas, cidadãos e da própria instituição, agindo sempre em conformidade com os princípios da Grupo de Resposta Rápida.',
                    'Art. 3º' => 'Ao chegar à unidade, o policial deve se identificar à equipe presente, comunicar seu início de serviço e proceder à troca de fardamento e equipamentos, assegurando que esteja completamente preparado para as atividades que serão desempenhadas.',
                    'Art. 4º' => 'O uso do fardamento é obrigatório e deve respeitar o padrão institucional, incluindo a correta utilização das insígnias, distintivos e demais acessórios previstos, garantindo a identificação imediata do agente e o respeito à hierarquia.',
                    'Art. 5º' => 'É responsabilidade de cada policial zelar por seus equipamentos pessoais, fardamento, armamento e viatura, mantendo-os em perfeitas condições de uso, para assegurar a operacionalidade e segurança durante as ações policiais.',
                    'Art. 6º' => 'A entrada e saída de veículos particulares no interior da superintendência são permitidas exclusivamente para deslocamento dos policiais ao serviço ou residência, respeitando rigorosamente os limites de velocidade e normas de segurança para evitar acidentes.',
                    'Art. 7º' => 'O policial deverá registrar seu ponto de entrada e saída, observando os horários estabelecidos, de forma correta e tempestiva, garantindo o controle do efetivo e o cumprimento da carga horária prevista.',
                    'Art. 8º' => 'A frequência correta nas rádios oficiais, preferencialmente na frequência 191 MHz, deve ser mantida durante todo o turno, exceto em situações excepcionais que exijam o uso da frequência 190 MHz para ações em locais adversos às rodovias federais.',
                    'Art. 9º' => 'O código de etiqueta da Grupo de Resposta Rápida estabelece os padrões mínimos de vestimenta, aparência pessoal e comportamento, que devem ser observados por todos os agentes, visando manter a imagem institucional e o respeito da sociedade.',
                    'Art. 10º' => 'O cabelo dos policiais deve ser mantido limpo, penteado e com comprimento adequado para o uso correto do boné, respeitando as cores autorizadas (preto, castanho, loiro, ruivo e grisalho), sendo vetados cortes que prejudiquem a apresentação pessoal.',
                    'Art. 11º' => 'O uso da barba é permitido conforme a hierarquia e cargos, devendo estar sempre alinhada e com cumprimento controlado, respeitando as normas específicas para agentes estagiários, que não possuem autorização para barba.',
                    'Art. 12º' => 'O uso de maquiagem é permitido apenas para policiais do sexo feminino, devendo ser discreto, com cores neutras e sem destaque excessivo, de modo a preservar a formalidade e sobriedade exigidas pelo serviço policial.',
                    'Art. 13º' => 'O uso de acessórios como brincos é expressamente proibido durante o serviço policial, evitando qualquer elemento que possa comprometer a uniformidade e a imagem da corporação.',
                    'Art. 14º' => 'É vedado o uso de máscaras, gorros ou qualquer vestimenta que oculte o rosto e inviabilize a identificação do policial, salvo em situações especiais autorizadas, assegurando a transparência e confiança na atuação policial.',
                ],
            ],
            [
                'code' => 'Cap. II',
                'anchor' => 'cap2',
                'title' => 'Do Setor Administrativo',
                'subtitle' => 'Controle de efetivo, documentação, prazos e organização institucional.',
                'sort_order' => 2,
                'articles' => [
                    'Art. 1º' => 'O setor administrativo da Grupo de Resposta Rápida é responsável pelo controle rigoroso da documentação funcional dos policiais, abrangendo registros de ingresso, promoções, treinamentos e demais dados necessários à gestão eficiente do efetivo.',
                    'Art. 2º' => 'A gestão do efetivo deve incluir o controle de escalas, registros de ponto, ausências justificadas e não justificadas, garantindo a adequada cobertura das operações e a manutenção da ordem interna da instituição.',
                    'Art. 3º' => 'Todos os policiais devem comunicar previamente suas ausências, preferencialmente com no mínimo 24 horas de antecedência, por meio dos canais oficiais da Grupo de Resposta Rápida no Brasil Capital Roleplay, para que sejam devidamente registradas e autorizadas.',
                    'Art. 4º' => 'A comunicação interna entre setores e agentes deve ser clara, objetiva e formal, utilizando os meios oficiais disponibilizados, a fim de assegurar a transparência e a agilidade nos processos administrativos e operacionais.',
                    'Art. 5º' => 'Os prazos para entrega de documentos, relatórios e cumprimento de demandas devem ser rigorosamente observados, sob pena de responsabilização administrativa, a fim de manter o fluxo adequado das atividades da corporação.',
                    'Art. 6º' => 'O setor administrativo possui a atribuição de promover treinamentos periódicos para capacitação dos agentes, visando atualização constante e aprimoramento das habilidades necessárias ao serviço policial rodoviário.',
                    'Art. 7º' => 'É dever do setor administrativo realizar auditorias internas regulares para verificar o cumprimento das normas e procedimentos estabelecidos, apontando falhas e recomendando melhorias quando necessário.',
                    'Art. 8º' => 'O registro correto e atualizado do cadastro funcional de cada policial é fundamental para a gestão eficiente do efetivo, permitindo o controle de promoções, punições e demais movimentações administrativas.',
                    'Art. 9º' => 'A política de disciplina e conduta deve ser amplamente divulgada pelo setor administrativo, garantindo que todos os agentes estejam cientes das normas, direitos e deveres que lhes competem.',
                    'Art. 10º' => 'O setor administrativo é responsável por manter o arquivo físico e digital de todos os documentos oficiais, garantindo segurança, sigilo e fácil acesso para consultas autorizadas.',
                    'Art. 11º' => 'A coordenação da equipe administrativa deve garantir o atendimento às demandas dos agentes e superiores, atuando com eficiência e respeito para o bom funcionamento da instituição.',
                    'Art. 12º' => 'Em caso de irregularidades detectadas no controle de efetivo, documentação ou demais atividades administrativas, o setor deve comunicar imediatamente a chefia para que sejam adotadas as providências cabíveis.',
                    'Art. 13º' => 'O setor administrativo deve elaborar e atualizar periodicamente normativas internas e regulamentos, alinhados às diretrizes institucionais e à legislação vigente, promovendo a padronização dos procedimentos.',
                    'Art. 14º' => 'O cumprimento das responsabilidades administrativas é fundamental para a credibilidade da Grupo de Resposta Rápida e para o bom andamento das operações no Brasil Capital Roleplay.',
                    'Art. 15º' => 'É autorizado aos agentes administrativos permanecerem em serviço nas imediações da Superintendência, desde que estejam desempenhando atividades administrativas, seja no ambiente virtual (Discord) ou na cidade.',
                    'Art. 16º' => 'É autorizado aos agentes do setor de Recursos Humanos da Grupo de Resposta Rápida permanecerem em serviço nas imediações da Superintendência, sempre que estiverem realizando qualquer atividade administrativa, seja fiscalização ou atividade correlata, tanto no ambiente virtual (Discord) quanto na cidade.',
                ],
            ],
            [
                'code' => 'Cap. III',
                'anchor' => 'cap3',
                'title' => 'Do Uso de Viaturas',
                'subtitle' => 'Funções operacionais, composição de equipe e responsabilidades em patrulhamento.',
                'sort_order' => 3,
                'articles' => [
                    'Art. 1º' => 'A condução das viaturas policiais deve ser realizada exclusivamente por agentes com habilitação válida e capacitação formal. Deve-se observar os protocolos de segurança e normas de trânsito para garantir a integridade dos ocupantes e terceiros durante as operações.',
                    'Art. 2º' => 'Compete ao superior hierárquico presente na viatura designar as funções dos integrantes da equipe, organizando o trabalho de forma coordenada para assegurar o cumprimento eficiente das atividades policiais em campo.',
                    'Art. 3º' => 'O motorista é responsável pela condução segura da viatura, pela manutenção preventiva e corretiva do veículo, e deve garantir que o desembarque da equipe seja realizado com segurança e sem riscos para os agentes.',
                    'Art. 4º' => 'O chefe de barca, necessariamente agente superior, lidera as ações operacionais da equipe, emite ordens táticas, modula as comunicações e responde pelo comportamento e cumprimento das tarefas designadas.',
                    'Art. 5º' => 'O auxiliar, também chamado terceiro homem, presta suporte às abordagens, utiliza recursos tecnológicos para consultar sistemas policiais, auxilia no registro de informações e colabora na segurança da equipe.',
                    'Art. 6º' => 'O segurança, quarto homem, é responsável por garantir a proteção do grupo, controlar a área de atuação, identificar riscos e manter a integridade física dos agentes durante todas as operações.',
                    'Art. 7º' => 'A composição das equipes deve respeitar o número máximo permitido e garantir a presença obrigatória de um superior hierárquico para comandar e supervisionar as ações operacionais durante o patrulhamento.',
                    'Art. 8º' => 'É vedado o uso da viatura em condições que coloquem em risco a segurança dos agentes ou da sociedade, sendo obrigatória a realização de manutenções regulares e a comunicação imediata de falhas que comprometam sua operacionalidade.',
                    'Art. 9º' => 'O transporte de pessoas detidas deve ser realizado exclusivamente em compartimentos específicos da viatura, vedando-se o uso dos assentos destinados aos policiais para garantir a segurança e o cumprimento das normas legais.',
                    'Art. 10º' => 'Qualquer modificação nas viaturas, seja para fins operacionais ou estéticos, deve ser previamente autorizada pela chefia competente, obedecendo aos padrões institucionais e as exigências de segurança vigentes.',
                    'Art. 11º' => 'O uso correto dos equipamentos de proteção individual, como coletes balísticos e cintos de guarnição, é obrigatório durante todo o período de patrulhamento, garantindo a integridade física dos agentes.',
                    'Art. 12º' => 'A comunicação interna entre os membros da viatura deve ser clara, objetiva e eficiente, especialmente durante abordagens e operações, evitando falhas que possam comprometer a segurança e o êxito da missão.',
                    'Art. 13º' => 'Em situações emergenciais, a equipe deve agir conforme os protocolos estabelecidos, mantendo a calma, solicitando reforços e adotando medidas para garantir a segurança dos agentes e da população.',
                    'Art. 14º' => 'Ao término do turno de serviço, o responsável pela viatura deve assegurar que esta seja entregue em condições adequadas de uso, comunicando qualquer irregularidade ao setor responsável para providências imediatas.',
                ],
            ],
            [
                'code' => 'Cap. IV',
                'anchor' => 'cap4',
                'title' => 'Procedimentos de Condução Veicular',
                'subtitle' => 'Direção operacional, composição mínima e critérios de uso tático das viaturas.',
                'sort_order' => 4,
                'articles' => [
                    'Art. 1º' => 'O policial rodoviário federal só poderá assumir a função de motorista da viatura após comprovação de habilitação válida e aprovação em cursos específicos de direção defensiva e tática, garantindo segurança operacional e o cumprimento eficiente das missões no Brasil Capital Roleplay.',
                    'Art. 2º' => 'A escolha da viatura a ser utilizada obedecerá à hierarquia e à designação do chefe de equipe, que indicará o motorista e demais integrantes da tripulação, assegurando a correta organização do patrulhamento.',
                    'Art. 3º' => "A composição das equipes nas viaturas deve seguir as seguintes funções mínimas:\n\nI – Motorista: responsável pela condução segura do veículo, inspeção prévia e cuidados durante a operação;\nII – Chefe de barca: superior hierárquico que coordena as ações da equipe, designa funções e mantém comunicação com a central ou pessoa designada para tal função pelo superior hierárquico;\nIII – Auxiliar (3º homem): responsável por apoio nas abordagens, uso do GPS, registro de informações e segurança;\nIV – Segurança (4º homem): encarregado da proteção da equipe e controle da área.\nV – Segurança Auxiliar (5º homem, se houver): auxiliar encarregado da proteção da equipe e controle da área.",
                    'Art. 4º' => 'O número de agentes por viatura é de no minimo 3 pessoas e a quantidade máxima é até 5 pessoas, respeitando as diretrizes de patrulhamento e a complexidade da missão em curso.',
                    'Art. 5º' => 'É expressamente proibido que as viaturas transitem em condições que coloquem em risco a segurança dos agentes ou terceiros, incluindo a condução imprudente, desrespeito às normas de trânsito do Brasil Capital Roleplay ou falta de manutenção adequada.',
                    'Art. 6º' => 'O transporte de detidos deve ser realizado exclusivamente em compartimentos apropriados das viaturas, preservando a segurança de todos os envolvidos e evitando riscos de fugas ou acidentes.',
                    'Art. 7º' => 'Qualquer alteração física nas viaturas, incluindo modificações de performance, visual ou equipamento, deverá ser autorizada previamente pela chefia administrativa e estar dentro das normas do Brasil Capital Roleplay.',
                    'Art. 8º' => 'Em ocorrências que demandem ação imediata, a viatura de supervisão possui autonomia para intervir, fiscalizar e coordenar as operações, respeitando a hierarquia e os protocolos institucionais.',
                ],
            ],
            [
                'code' => 'Cap. V',
                'anchor' => 'cap5',
                'title' => 'Dos Armamentos',
                'subtitle' => 'Porte, habilitação, munição, segurança e responsabilidade no uso de armamento oficial.',
                'sort_order' => 5,
                'articles' => [
                    'Art. 1º' => 'O policial rodoviário federal tem direito ao porte legal de arma de fogo em todo o território nacional durante o exercício das suas funções, devendo cumprir rigorosamente as normas institucionais e legais para o uso e transporte dos armamentos.',
                    'Art. 2º' => 'Todo armamento fornecido é registrado em nome do policial, que assume total responsabilidade administrativa, civil e penal pela sua utilização, devendo garantir a guarda e manutenção adequadas do material.',
                    'Art. 3º' => 'É obrigatório que o policial esteja devidamente habilitado para o uso de qualquer arma, mediante aprovação em cursos oficiais e reciclagens periódicas, garantindo o manejo seguro e eficiente dos armamentos.',
                    'Art. 4º' => 'O porte ou uso de armamentos para os quais o agente não esteja habilitado (Armalite M15 para Agente 1ª Classe) constitui infração disciplinar grave, sujeitando o infrator a punições que podem variar desde advertência até exoneração.',
                    'Art. 5º' => 'A quantidade máxima de munições autorizadas para porte em serviço está estabelecida conforme o tipo de armamento é 150 munições equipadas para Pistola e 250 munições equipadas para o Fuzil e 100 munições reservas na mochila, o seu excesso ou uso indevido é vedado, visando manter o controle e segurança no emprego das armas.',
                    'Art. 6º' => 'O uso dos equipamentos regulamentares, como algemas, cassetetes, coletes balísticos, rádios comunicadores e lanternas, é obrigatório durante o serviço e restrito conforme as funções e atribuições de cada agente.',
                    'Art. 7º' => 'O uso de equipamentos oficiais fora do serviço é expressamente proibido, salvo situações excepcionais e autorizadas formalmente, para preservar o patrimônio e evitar riscos à segurança pública.',
                    'Art. 8º' => 'A realização de cursos de tiro e treinamentos específicos é requisito indispensável para o porte e uso de armas especiais, garantindo que somente agentes capacitados manuseiem armamentos como fuzis e escopetas.',
                    'Art. 9º' => 'O policial deve realizar inspeção periódica e manutenção básica de seus equipamentos e armamentos, comunicando imediatamente à chefia qualquer falha que comprometa a funcionalidade e segurança.',
                    'Art. 10º' => 'O armazenamento e transporte dos armamentos e munições devem obedecer aos protocolos de segurança instituídos, evitando o acesso por pessoas não autorizadas e prevenindo acidentes ou desvios.',
                    'Art. 11º' => 'O uso indevido, disparos imprudentes ou negligentes com armas de fogo serão objeto de investigação rigorosa, com aplicação de medidas disciplinares e penalidades previstas em lei.',
                    'Art. 12º' => 'Em caso de extravio, perda ou furto de qualquer armamento ou equipamento oficial, o policial deve informar imediatamente sua chefia para adoção das medidas cabíveis de segurança e investigação.',
                    'Art. 13º' => 'Armas e equipamentos VIP são restritos a agentes autorizados e devem ser usados somente em situações específicas previstas no regulamento interno, assegurando a observância das normas.',
                    'Art. 14º' => 'O ocupante do cargo de Aluno do GRR somente poderá participar do Curso de Tiro Avançado após a conclusão e aprovação no Curso de Tiro Básico.',
                    'Art. 15º' => 'A Grupo de Resposta Rápida incentiva e promove a cultura da responsabilidade, disciplina e capacitação continuada no uso de armamentos e equipamentos, visando garantir a segurança e eficiência nas operações.',
                ],
            ],
            [
                'code' => 'Cap. VI',
                'anchor' => 'cap6',
                'title' => 'Das Normas e Procedimentos',
                'subtitle' => 'Diretrizes operacionais, patrulhamento, comunicação e procedimentos em serviço.',
                'sort_order' => 6,
                'articles' => [
                    'Art. 1º' => 'O patrulhamento realizado pela Grupo de Resposta Rápida tem por objetivo garantir a segurança viária, prevenir acidentes e crimes, além de fiscalizar e manter a ordem pública nas rodovias federais sob sua jurisdição.',
                    'Art. 2º' => 'Durante as ações de patrulhamento, os agentes devem cumprir rigorosamente as normas de trânsito, evitando infrações, exceto em casos emergenciais devidamente justificados e autorizados pela hierarquia.',
                    'Art. 3º' => 'A composição das equipes deve respeitar o número mínimo e máximo estabelecido para garantir a segurança dos agentes e o atendimento adequado das ocorrências durante o patrulhamento.',
                    'Art. 4º' => 'A comunicação constante e eficaz via rádio entre as equipes e a central de comando é obrigatória, garantindo a coordenação das operações e a rápida resposta a eventos que exijam atuação policial.',
                    'Art. 5º' => 'Somente podem realizar patrulhamento os agentes que tenham concluído o curso básico de Procedimento Operacional Padrão (POP).',
                    'Art. 6º' => 'O patrulhamento está restrito às rodovias federais, salvo situações excepcionais como perseguições iniciadas nelas e que se estendam a áreas urbanas, conforme determinações superiores e protocolos operacionais.',
                    'Art. 7º' => 'O armamento permitido para o motorista designado no patrulhamento é restrito a pistola Glock com munição regulamentada ou armamento VIP autorizado e registrado.',
                    'Art. 8º' => 'É permitido o uso de escudo durante o patrulhamento para agentes capacitados do Grupo de Resposta Rápida.',
                    'Art. 9º' => 'Durante as operações, os agentes devem permanecer nas posições designadas dentro das viaturas, especialmente nas canaletas de comunicação, para manter o controle e a coordenação adequados.',
                    'Art. 10º' => 'É responsabilidade direta dos agentes a conservação, manutenção e integridade das viaturas, comunicando imediatamente qualquer problema técnico ou avaria que possa comprometer o desempenho das atividades.',
                    'Art. 11º' => 'Ao finalizar o patrulhamento, os policiais devem comunicar formalmente o encerramento das operações via rádio, informando a situação da viatura e o término da atividade operacional.',
                    'Art. 12º' => 'A presença de um superior hierárquico durante o patrulhamento é obrigatória para garantir a liderança e o cumprimento das diretrizes; na ausência, o agente responsável deve formalizar relatório com dados e ações realizadas.',
                    'Art. 13º' => 'Em operações de invasão em comunidades ou combate ao tráfico, o efetivo mínimo deve ser de duas viaturas, com solicitação prévia e formal de reforço especializado para garantir segurança e eficácia da ação.',
                    'Art. 14º' => 'As equipes devem atuar com cooperação, respeitando as ordens do chefe de barca, mantendo conduta ética, profissional e focada no cumprimento dos objetivos da Grupo de Resposta Rápida.',
                    'Art. 15º' => 'A comunicação com os Diretores deve ser realizada exclusivamente pelos canais oficiais, por meio do canal atendimento, sendo vedado o envio de mensagens privadas para tratar de assuntos banais ou não relacionados ao serviço, a fim de preservar a organização e a formalidade da corporação.',
                ],
            ],
            [
                'code' => 'Cap. VII',
                'anchor' => 'cap7',
                'title' => 'Da Disciplina',
                'subtitle' => 'Conduta institucional, postura profissional, respeito hierárquico e sanções.',
                'sort_order' => 7,
                'articles' => [
                    'Art. 1º' => 'O policial rodoviário federal deve pautar sua conduta pela ética, respeito à hierarquia, profissionalismo e comprometimento institucional, representando a Grupo de Resposta Rápida com integridade no ambiente do Brasil Capital Roleplay.',
                    'Art. 2º' => 'Condutas que prejudiquem a imagem da corporação, como abuso de autoridade, desrespeito a colegas ou uso indevido de recursos, são expressamente proibidas, sujeitando os infratores a penalidades disciplinares.',
                    'Art. 3º' => 'O não cumprimento das normas internas sujeita o agente a sanções disciplinares que variam desde advertências até a exoneração, conforme a gravidade e reincidência da infração cometida.',
                    'Art. 4º' => 'O canal de comunicação institucional deve ser utilizado com respeito, clareza e objetividade, evitando linguagem ofensiva ou inadequada, garantindo ambiente profissional e colaborativo.',
                    'Art. 5º' => 'É proibido ao policial permanecer ausente durante o expediente com ponto aberto ou realizar atividades particulares sem autorização, sob pena de sanções disciplinares por comprometimento do serviço.',
                    'Art. 6º' => 'Os agentes devem zelar pela boa imagem da Grupo de Resposta Rápida em todas as suas interações, buscando excelência na prestação do serviço e respeito da comunidade virtual do Brasil Capital Roleplay.',
                    'Art. 7º' => 'Em caso de dúvidas ou conflitos sobre procedimentos, o policial deve buscar orientação junto aos superiores, evitando atitudes precipitadas que prejudiquem a operação ou a imagem da instituição.',
                    'Art. 8º' => 'A participação em treinamentos, reciclagens e capacitações é obrigatória para garantir a atualização constante das habilidades técnicas e comportamentais dos agentes.',
                    'Art. 9º' => 'A abertura de processos administrativos por infrações deve respeitar o direito à ampla defesa e contraditório, assegurando a transparência e justiça nos procedimentos disciplinares.',
                    'Art. 10º' => 'É imprescindível que os agentes respeitem as regras do Brasil Capital Roleplay, promovendo uma experiência de roleplay justa, imersiva e condizente com o caráter policial da instituição.',
                    'Art. 11º' => 'Práticas como powergaming, metagaming e outras que violam os princípios do roleplay legítimo são proibidas, podendo acarretar punições severas conforme a gravidade.',
                    'Art. 12º' => 'O espírito de colaboração e trabalho em equipe é fundamental para o sucesso das operações, devendo ser incentivado e praticado por todos os membros do Grupo de Resposta Rápida.',
                    'Art. 13º' => 'O uso adequado do fardamento, equipamentos e postura formal durante eventos e operações reforça a credibilidade e o respeito à corporação perante a comunidade virtual.',
                    'Art. 14º' => 'O policial deve representar o Grupo de Resposta Rápida com dignidade e responsabilidade, preservando o nome e a história da instituição no âmbito do Brasil Capital Roleplay.',
                    'Art. 15º' => 'É proibido qualquer tipo de atrito, desentendimento ou conduta hostil entre policiais da mesma guarnição ou de guarnições distintas, devendo eventuais conflitos ser solucionados por meio dos canais institucionais e na presença de superiores hierárquicos, preservando sempre o respeito e a harmonia no ambiente de trabalho.',
                    'Art. 16º' => 'Todos os casos referentes a denúncias serão analisados e, quando necessário, encaminhados à Corregedoria, a quem caberá a responsabilidade de julgar o caso e aplicar as medidas cabíveis, observando-se os princípios da imparcialidade, transparência e devido processo legal.',
                ],
            ],
            [
                'code' => 'Cap. VIII',
                'anchor' => 'cap8',
                'title' => 'Viaturas Especializadas',
                'subtitle' => 'Supervisão, comando, coordenação e regras específicas de viaturas estratégicas.',
                'sort_order' => 8,
                'articles' => [
                    'Art. 1º' => 'A Viatura de Supervisão tem como finalidade fiscalizar e garantir o cumprimento dos protocolos operacionais, disciplinares e de padronização das equipes em campo, exercendo autoridade hierárquica sobre todas as viaturas em operação, exceto a Viatura de Comando.',
                    'Art. 2º' => 'A Viatura de Supervisão atuará de forma independente do posto ou patente dos demais policiais, supervisionando condutas, uniformes, procedimentos, comunicação e apresentação das viaturas, podendo intervir sempre que identificar situações contrárias às diretrizes institucionais.',
                    'Art. 3º' => 'Compete à Viatura de Supervisão recomendar a abertura de processos administrativos e solicitar reciclagem em cursos operacionais obrigatórios sempre que forem constatadas condutas inadequadas, falhas técnicas ou violações de normas durante o patrulhamento.',
                    'Art. 4º' => 'Para abrir uma viatura de Supervisão ela deve ser composta por no mínimo, um Agente Especial.',
                    'Art. 5º' => "A Viatura de Supervisão deverá observar os seguintes critérios mínimos para sua abertura:\n\nI – Uma Viatura de Supervisão poderá ser aberta somente quando houver pelo menos uma (1) viatura de quatro rodas em patrulhamento ativo;\nII – A segunda Viatura de Supervisão somente poderá ser aberta quando houver pelo menos duas (2) viaturas de quatro rodas em patrulhamento ativo.",
                    'Art. 6º' => 'A Supervisão não deverá priorizar a posição de viatura primária em abordagens ou operações em andamento, salvo em situações de flagrante delito que exijam intervenção imediata.',
                    'Art. 7º' => 'A atuação da Supervisão terá como foco principal a fiscalização de viaturas de quatro rodas.',
                    'Art. 8º' => 'A Viatura de Comando tem como finalidade coordenar e gerenciar as viaturas em operação, garantindo a execução das diretrizes estratégicas, a distribuição equilibrada das equipes e a eficiência no atendimento às ocorrências.',
                    'Art. 9º' => 'A Viatura de Comando e Coordenação atua como autoridade máxima operacional durante o turno, centralizando as decisões táticas, determinando prioridades e mantendo a comunicação direta com todas as viaturas ativas, postos fixos e setores de apoio.',
                    'Art. 10º' => 'Para abrir uma Viatura de Comando e Coordenação ela deverá ser composta por no mínimo, um Coordenador.',
                    'Art. 11º' => 'A Viatura de Comando poderá intervir diretamente em ocorrências de alta complexidade, assumindo a coordenação das equipes no local, mas deverá priorizar sua função estratégica e manter-se disponível para o controle geral da operação.',
                    'Art. 12º' => 'A Viatura de Comando e Coordenação poderá interagir e coordenar viaturas de todos os tipos.',
                    'Art. 13º' => 'Na Viatura de Comando, caso seja solicitado por superior hierárquico ao chefe de barca, este deverá ser remanejado para a Coordenação ou Supervisão, se houver vaga disponível. Na ausência de vaga, o remanejamento deverá ocorrer para viaturas convencionais, conforme determinação superior.',
                    'Art. 14º' => 'As Viaturas de Supervisão, Coordenação e Comando poderão, quando julgarem necessário, realizar revista pessoal nos agentes. Tal procedimento somente poderá ocorrer nas dependências da Superintendência, devendo haver fundada suspeita ou denúncia que justifique a medida, observando-se rigorosamente os direitos e garantias individuais. Para este fim, a Viatura de Supervisão, Coordenação ou Comando poderá determinar que a viatura se desloque até a Superintendência para a execução do procedimento.',
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            $articles = $sectionData['articles'];
            unset($sectionData['articles']);

            $section = GrrManualSection::create([
                'manual_id'   => $manual->id,
                'code'        => $sectionData['code'],
                'anchor'      => $sectionData['anchor'],
                'title'       => $sectionData['title'],
                'subtitle'    => $sectionData['subtitle'],
                'sort_order'  => $sectionData['sort_order'],
                'is_active'   => true,
            ]);

            $order = 1;
            foreach ($articles as $articleNumber => $body) {
                GrrManualArticle::create([
                    'section_id'      => $section->id,
                    'article_number'  => $articleNumber,
                    'body'            => $body,
                    'sort_order'      => $order++,
                    'is_active'       => true,
                ]);
            }
        }
    }
}