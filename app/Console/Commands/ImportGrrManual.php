<?php

namespace App\Console\Commands;

use App\Models\GrrManual;
use App\Models\GrrManualArticle;
use App\Models\GrrManualSection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportGrrManual extends Command
{
    protected $signature = 'grr:import-manual {--reset : Apaga as seções e artigos atuais antes de importar}';
    protected $description = 'Importa automaticamente o Manual Interno GRR a partir de um texto bruto';

    private const MANUAL_SLUG = 'manual-interno-grr';

    public function handle(): int
    {
        $rawText = <<<'TEXT'
CAPÍTULO I – DISPOSIÇÕES PRELIMINARES
Art. 1º – O presente regulamento estabelece normas, procedimentos e condutas obrigatórias para todos os integrantes da Grupo de Resposta Rápida (GRR) no Brasil Capital Roleplay, com o objetivo de garantir disciplina, organização e eficiência nas atividades operacionais e administrativas.

Art. 2º – É dever de todo policial manter postura ética, disciplinada e profissional, zelando pela integridade física e moral de colegas, cidadãos e da instituição.

Art. 3º – Ao iniciar o serviço, o policial deverá identificar-se à equipe presente, entrar na canaleta do discord em alojamento, registrar presença e preparar-se adequadamente para o turno operacional.

Art. 4º – O uso do fardamento oficial é obrigatório e deve respeitar os padrões institucionais definidos pela GRR.

Art. 5º – Cada policial é responsável por seus equipamentos, armamentos e viaturas utilizadas durante o serviço.

Art. 6º – O registro correto de entrada e saída do serviço é obrigatório para controle do efetivo.

Art. 7º – A comunicação oficial deverá ocorrer preferencialmente pela frequência 191 MHz (PRF-CENTRAL) e 204 MHz (GRR-INTERNA), salvo situações excepcionais.

Art. 8º – O policial deve preservar a imagem institucional da corporação em todas as situações dentro do Brasil Capital Roleplay.

Art. 9º – Este manual tem como finalidade garantir disciplina, organização, eficiência operacional e realismo institucional na atuação dos agentes.

Art. 10 – Ao iniciar o turno de serviço, o policial deverá:

I – Entrar na canaleta do discord em ALOJAMENTO;

II – Identificar-se à equipe presente;

III – Registrar seu ponto de entrada;

IV – Equipar-se adequadamente;

V – Preparar viatura e equipamentos.

Art. 11 – O código de etiqueta da Grupo de Resposta Rápida estabelece os padrões mínimos de vestimenta, aparência pessoal e comportamento, que devem ser observados por todos os agentes, visando manter a imagem institucional e o respeito da sociedade.

Art. 12 - Em caso de Corrupção ou vazamento de informação (exoneração imediata e e bloqueio de acesso a qualquer outro cargo público).

CAPÍTULO II – DA HIERARQUIA E COMANDO OPERACIONAL
Art. 1º – A hierarquia da Grupo de Resposta Rápida é composta pelos seguintes cargos:

Diretor

Vice Diretor

Coordenador

Superintendente

Inspetor

Agente Classe Especial

Agente 1ª Classe

Agente 2ª Classe

Agente 3ª Classe

Aluno

Art. 2º – A hierarquia deve ser respeitada em todas as atividades administrativas e operacionais.

Art. 3º – Todas as operações da GRR obedecem ao princípio da unidade de comando, garantindo organização e clareza nas decisões.

Art. 4º – Precedência de Comando: Durante operações envolvendo múltiplas equipes, o comando será exercido:

I – Pelo agente de maior cargo hierárquico presente no local;

II – Em caso de igualdade hierárquica, pelo agente com maior tempo de serviço na GRR.

Art. 5º – O comandante da ocorrência será responsável por:

I – Coordenar as equipes presentes;

II – Definir estratégias operacionais;

III – Organizar o posicionamento das viaturas;

IV – Manter comunicação com supervisão ou comando.

Art. 6º – Todos os agentes presentes devem cumprir as determinações do comandante da ocorrência.

Art. 7º – O comandante poderá delegar funções, mantendo a responsabilidade geral pela operação.

CAPÍTULO III – DOS CARGOS E FUNÇÕES
Art. 1º – Diretor: Cargo máximo da instituição. Responsável por decisões estratégicas e administrativas. Supervisiona toda a corporação.

Art. 2º – Vice Diretor: Auxilia o Diretor. Assume a corporação na ausência do Diretor. Coordena decisões administrativas.

Art. 3º – Coordenador: O cargo de Coordenador possui as seguintes funções dentro da corporação:

Coordenador Geral: Responsável pela gestão geral da GRR.

Coordenador Administrativo: Responsável pela organização administrativa.

Coordenador Estratégico: Responsável pelo planejamento estratégico das operações.

Coordenador de Nivelamento Operacional: Responsável pela formação, cursos internos e capacitação dos agentes.

Art. 4º – Superintendente: O cargo de Superintendente possui as seguintes funções:

Superintendente Operacional Alpha: Responsável pela supervisão da equipe Alpha.

Superintendente Operacional Bravo: Responsável pela supervisão da equipe Bravo.

Superintendente Operacional Charlie: Responsável pela supervisão da equipe Charlie.

Superintendente de Inteligência: Responsável por planejamento e coleta de informações estratégicas.

Superintendente de Ações Especializadas: Responsável por operações especiais como Mandados de busca, Negociação e Escoltas.

Superintendente Tático: Responsável por operações de alto risco, como Incursões e Blitz.

Art. 5º – Inspetor: Fiscaliza procedimentos operacionais. Lidera equipes em campo. Auxilia na organização das atividades operacionais. Aprova boletins e relatórios de patrulhamento.

Art. 6º – Agente Classe Especial: Lidera operações em campo. Coordena agentes subordinados. Auxilia no treinamento operacional.

Art. 7º – Agente 1ª Classe: Atua em patrulhamento e operações. Auxilia na liderança de equipes. Apoia treinamentos internos.

Art. 8º – Agente 2ª Classe: Atua em patrulhamento. Executa operações sob supervisão.

Art. 9º – Agente 3ª Classe: Atua em patrulhamento básico. Auxilia agentes mais experientes.

Art. 10 – Aluno: Primeiro cargo dentro da corporação. Participa de treinamentos básicos. Atua sob supervisão de agentes superiores.

CAPÍTULO IV – DOS CHEFES DE EQUIPE, SUBCHEFES E AUXILIARES
Art. 1º – Definição: Os Chefes de Equipe são responsáveis pela liderança operacional das equipes da GRR, sendo designados para comandar diretamente as equipes Alpha, Bravo e Charlie durante atividades operacionais.

Art. 2º – Estrutura das equipes: A GRR é composta pelas seguintes equipes operacionais: I – Equipe Alpha; II – Equipe Bravo; III – Equipe Charlie. Todas possuem as mesmas funções operacionais.

Art. 3º – Chefes de Equipe:

I – Cada equipe possui um Chefe de Equipe responsável pela coordenação dos agentes.

II – O Chefe de Equipe será designado pelo comando da GRR.

III – Compete ao Chefe: Coordenar patrulhamento, distribuir agentes nas viaturas, garantir procedimentos, manter comunicação e zelar pela disciplina.

Art. 4º – Subchefes de Equipe:

I – Responsável por auxiliar diretamente o Chefe de Equipe.

II – Atua como segundo responsável, auxiliando na organização e cumprimento das diretrizes.

III – Compete ao Subchefe: Auxiliar na coordenação, organizar agentes em operações e substituir o Chefe em sua ausência.

Art. 5º – Auxiliares de Equipe:

I – Designados para prestar suporte administrativo e operacional.

II – Compete ao Auxiliar: Apoio no controle de presença, comunicação interna e suporte administrativo em operações.

Art. 6º – Natureza das funções: As funções de Chefe, Subchefe e Auxiliar fazem parte da organização interna e não são consideradas cargos na hierarquia institucional.

Art. 7º – Relação entre equipes: As equipes Alpha, Bravo e Charlie atuam de forma integrada e colaborativa.

Art. 8º – Precedência de comando: Em ocorrências com mais de uma equipe, respeita-se a regra de precedência estabelecida no Capítulo II.

Art. 9º – Solicitações administrativas do Chefe de Equipe:

I – Possui autoridade para encaminhar solicitações referentes aos seus agentes.

II – Inclui: Indicação de promoções, rebaixamentos (com justificativa), medalhas, bonificações e punições disciplinares.

III – As decisões finais cabem à Coordenação, Superintendência ou Diretoria.

CAPÍTULO V – DOS SETORES ADMINISTRATIVOS
Art. 1º – A GRR possui setores responsáveis pela organização interna da corporação.

Art. 2º – Recursos Humanos: Responsável por gestão de agentes, controle de registros, organização administrativa, efetivação de promoções, ingressos e desligamentos.

Art. 3º – Programação: Responsável pelo desenvolvimento do portal GRR e sistemas internos.

Art. 4º – Administrativo: Responsável por controle documental, organização institucional, registro de promoções, controle de patrulhamento, gestão de treinamentos e ausências.

Art. 5º – SGTE: Responsável pela organização do Discord, geração de boletins, realização de transferências e comunicação institucional.

Art. 6º – Marketing: Responsável pela divulgação da corporação, criação de materiais institucionais e Comunicação Social.

Art. 7º – Compete ao setor administrativo a realização de auditorias internas e manutenção de arquivos físicos e digitais.

Art. 8º – Ausências devem ser comunicadas com antecedência mínima de 24 horas, salvo emergências.

Art. 9º – Agentes administrativos ou de RH podem permanecer na superintendência durante atividades administrativas.

CAPÍTULO VI – APRESENTAÇÃO PESSOAL
Art. 1º – Os agentes deverão manter postura e apresentação compatíveis com a atividade policial.

Art. 2º – O cabelo deve estar limpo, organizado e compatível com o uso do boné institucional. Cores permitidas: Preto, Castanho, Loiro, Ruivo, Grisalho.

Art. 3º – Cortes que prejudiquem a apresentação institucional são proibidos.

Art. 4º – O uso de barba é permitido conforme autorização hierárquica e deve estar sempre alinhado.

Art. 5º – Agentes em período de formação ou estágio não possuem autorização para uso de barba.

Art. 6º – O uso de maquiagem é permitido apenas para policiais femininas, devendo ser discreto.

Art. 7º – É proibido durante o serviço:

I – Uso de brincos ou acessórios extravagantes;

II – Uso de máscaras ou vestimentas que ocultem o rosto sem autorização.

CAPÍTULO VII – VIATURAS OPERACIONAIS
Art. 1º – As viaturas da GRR são destinadas exclusivamente ao serviço policial.

Art. 2º – A condução da viatura é permitida apenas a agentes habilitados e autorizados.

Art. 3º – A equipe da viatura deverá possuir no mínimo 2 agentes e no máximo 5 agentes.

Art. 4º – Composição da equipe: I – Motorista; II – Chefe de Barca; III – Auxiliar; IV – Segurança; V – Segurança Auxiliar.

Art. 5º – Função do Motorista: Condução segura, posicionamento estratégico e preservação da viatura.

Art. 6º – Função do Chefe de Barca: Liderança da equipe, coordenação das ações, determinação de posicionamento e comunicação.

Art. 7º – Função do Auxiliar (3º homem): Apoio direto nas abordagens, consultas operacionais e controle de suspeitos.

Art. 8º – Função do Segurança (4º homem): Garantir a segurança da equipe, cobertura armada e proteção do perímetro.

Art. 9º – Função do Segurança Auxiliar (5º homem): Vigilância do ambiente, apoio na contenção e suporte geral ao Chefe de Barca.

Art. 10 – O motorista é responsável pela condução segura da viatura.

Art. 11 – O chefe de barca lidera a equipe e coordena as ações operacionais.

Art. 12 – O auxiliar presta apoio nas abordagens e consultas operacionais.

Art. 13 – O segurança garante a proteção da equipe e controle do perímetro.

Art. 14 – O transporte de detidos deve ocorrer exclusivamente no compartimento apropriado da viatura (exceto quando o veículo não possui).

Art. 15 – Qualquer irregularidade ou problema mecânico deve ser comunicado imediatamente.

Art. 16 – É PROIBIDO subir qualquer morro com qualquer tipo de veículo. Toda ação em morro deve ser feito a pé.

Art. 17 –A entrada em comunidades só é permitida durante o horário (do jogo) das 06h00 até às 20h00 em no mínimo duas viaturas da mesma unidade especializada.

CAPÍTULO VIII – VIATURAS ESPECIALIZADAS
Art. 1º – Supervisão: Responsável por fiscalizar o cumprimento dos protocolos operacionais.

Art. 2º – A supervisão possui autoridade sobre viaturas em operação, exceto a Viatura de Comando.

Art. 3º – Para abertura da viatura de supervisão é necessário no mínimo um Agente Especial.

Art. 4º – Critérios de abertura: I – 1 supervisão para 1 viatura ativa; II – 2 supervisões para 2 viaturas ativas.

Art. 5º – Comando e Coordenação: A Viatura de Comando coordena estrategicamente as operações.

Art. 6º – O comando centraliza decisões táticas e distribuição das equipes.

Art. 7º – Para abertura da viatura de comando é necessário no mínimo um Coordenador.

Art. 8º – O comando poderá assumir ocorrências de alta complexidade.

Art. 9º – Na ausência de Comando ou Supervisão, a viatura com o chefe de barca de maior cargo assume a coordenação estratégica.

CAPÍTULO IX – PROCEDIMENTOS OPERACIONAIS
Art. 1º – O patrulhamento visa garantir segurança viária, prevenir crimes e fiscalizar rodovias e acessos.

Art. 2º – Apenas agentes aprovados no curso de POP podem realizar patrulhamento.

Art. 3º – O patrulhamento é preferencialmente realizado em rodovias federais.

Art. 4º – Perseguições iniciadas ao norte podem se estender para áreas urbanas e adentrar a cidade.

Art. 5º – O motorista deve utilizar apenas pistola Glock regulamentar ou armamento autorizado.

Art. 6º – O uso de escudo tático é permitido para agentes capacitados.

Art. 7º – A comunicação constante via rádio entre equipes é obrigatória.

Art. 8º – Ao iniciar e finalizar o patrulhamento, a equipe deve comunicar via rádio.

Art. 9º – Operações em comunidades ou combate ao tráfico exigem no mínimo duas viaturas da GRR.

Art. 10º – Serviço Temporário na PRF somente é permitido na falta de contingente pra iniciar um patrulhamento com 3 agentes.

CAPÍTULO X – ARMAMENTOS E EQUIPAMENTOS
Art. 1º – O policial possui direito ao porte de arma durante o serviço conforme normas.

Art. 2º – O armamento utilizado é de responsabilidade direta do policial.

Art. 3º – O uso de armas exige habilitação prévia e cursos de capacitação.

Art. 4º – Limite de munição (Em serviço):

I – Alunos: 1 Pistola com 100 munições.

II – Agentes 3ª Classe e Superiores: 1 Pistola com 150 munições.

III – Fuzil: 250 munições equipadas.

Art. 5º – O uso de armas não autorizadas constitui infração disciplinar grave.

Art. 6º – Equipamentos obrigatórios: 1 colete balístico; 1 par de algemas; 1 rádio; 1 cinto/bandoleira; 1 pistola e 150 munições; 1 Distintivo; 5 cones/barreiras; 1 colete reserva.

Art. 7º – O uso de equipamentos oficiais fora do serviço é proibido.

Art. 8º – Perda ou extravio de armamento deve ser comunicado imediatamente.

Art. 9º – Fora de serviço, o armamento é velado e permitido apenas 1 Pistola com 50 munições.

CAPÍTULO XI – DO USO DA ARMA DE FOGO EM SITUAÇÕES OPERACIONAIS
Art. 1º – Condição: Disparo permitido apenas quando o suspeito estiver armado, representando ameaça direta à vida.

Art. 2º – Impossibilidade de rendição: Uso permitido se não houver segurança na rendição, em confronto direto ou risco iminente de morte.

Art. 3º – Disparos em veículos: Somente nos pneus em situações excepcionais para cessar a fuga.

Art. 4º – Autorização: Exige-se autorização de Superintendente ou Superior sempre que possível.

Art. 5º – Situações autorizadas para disparos nos pneus: I – Veículo capotado continuando a fuga; II – Furar cerco policial intencional; III – Derrubar motos da corporação propositalmente.

Art. 6º – Situações proibidas de disparo:

I – Atropelamentos (VDM), deve-se denunciar administrativamente;

II – Troca de veículo durante fuga;

III – Pulo de paraquedas;

IV – Entrada na água.

Art. 7º – Segurança: Avaliar risco para civis antes de disparar.

Art. 8º – Comunicação: Ocorrências com disparos devem ser comunicadas via rádio e registradas em relatório.

CAPÍTULO XII – DOS CURSOS
Art. 1º – Cursos Internos Especializados: Ações, Negociador, CQB, Direção Defensiva, Abordagem, Escolta e Cumprimento de Mandados.

Art. 2º – Cursos Básicos: POP, Tiro Básico, Modulação/BOPM, Abordagem, Legislação, SAT B e Tiro Avançado.

CAPÍTULO XIII – DAS PROMOÇÕES, RECRUTAMENTOS E TRANSFERÊNCIAS
Art. 1º – Promoções: Dia 01 de cada mês (Alunos e demais) e Dia 15 de cada mês (Alunos).

Art. 2º – Recrutamentos: Ocorrerão todas as quintas-feiras.

Art. 3º – Transferências: De 14 a 18 de cada mês e de 29 a 03 de cada mês, às 20h.

Art. 4º – Boletim de Patrulhamento para Promoções

I – Nenhum agente da GRR poderá receber promoção sem possuir Boletim de Patrulhamento devidamente registrado.

II – O Boletim de Patrulhamento é considerado documento obrigatório para comprovação de atividade operacional, participação em serviço e desempenho do agente.

III – Promoções realizadas sem a apresentação do Boletim de Patrulhamento serão consideradas irregulares, podendo ser revisadas pela Coordenação ou Diretoria da GRR.

CAPÍTULO XIV – DAS MEDALHAS
Art. 1º – Medalhas institucionais: 5º Grau, 4º Grau, 3º Grau, 2º Grau e 1º Grau.

Art. 2º – A medalha de 1º Grau somente poderá ser concedida pelo Comando Geral.

CAPÍTULO XV – DAS ADVERTÊNCIAS E DISCIPLINA
Art. 1º – Punições: Alinhamento (15 dias sem UP), Advertência Verbal (30 dias sem UP), PAD 1 (30 dias sem UP), PAD 2 (30 dias sem UP), Suspensão Temporária e Exoneração.

Art. 2º – Penalidade aplicada conforme a gravidade.

Art. 3º – Proibidos: Abuso de autoridade, desrespeito entre colegas/cidadãos e uso indevido de recursos.

Art. 4º – Proibido ponto aberto sem exercer atividades.

Art. 5º – Conflitos resolvidos pelos canais institucionais.

Art. 6º – Denúncias analisadas pela Corregedoria e Diretoria.

Art. 7º – Conduta com cidadãos e corporação:

I – Respeito e postura profissional são obrigatórios.

II – Sanções por desrespeito: Impedimento de promoções (15 a 30 dias), 1 alinhamento interno e punições da Corregedoria.

III – Reincidência gera sanções mais severas.

CAPÍTULO XVI – ROLEPLAY E REGRAS DO SERVIDOR
Art. 1º – Respeito obrigatório às regras do Brasil Capital Roleplay.

Art. 2º – Proibido Powergaming, Metagaming e Quebra de imersão.

Art. 3º – O comportamento deve preservar a credibilidade da GRR.

DISPOSIÇÕES FINAIS
Art. 1º – Este regulamento entra em vigor a partir de sua publicação.

Art. 2º – Casos omissos serão analisados pela Diretoria da GRR.

Art. 3º – Todos os membros da corporação devem cumprir integralmente este manual.
TEXT;

        DB::transaction(function () use ($rawText) {
            $manual = GrrManual::firstOrCreate(
                ['slug' => self::MANUAL_SLUG],
                [
                    'title'             => 'Manual Interno — Grupo de Resposta Rápida (GRR)',
                    'kicker'            => 'REGULAMENTO INTERNO • GRR',
                    'subtitle'          => 'Normas, diretrizes e procedimentos internos aplicáveis ao efetivo do Grupo de Resposta Rápida.',
                    'description'       => 'Documento institucional interno para consulta e padronização operacional.',
                    'status_label'      => 'Status',
                    'status_value'      => 'Ativo',
                    'environment_label' => 'Ambiente',
                    'environment_value' => 'Brasil Capital Roleplay',
                    'alert_title'       => 'Atenção',
                    'alert_text'        => 'Este manual possui caráter interno. Todos os integrantes devem conhecer e cumprir integralmente suas disposições.',
                    'summary_1_label'   => 'Finalidade',
                    'summary_1_value'   => 'Normatizar',
                    'summary_1_sub'     => 'Padronizar condutas, operações e procedimentos administrativos da corporação.',
                    'summary_2_label'   => 'Estrutura',
                    'summary_2_value'   => 'Capítulos',
                    'summary_2_sub'     => 'Organização contínua em seções e artigos para consulta direta.',
                    'summary_3_label'   => 'Obrigatoriedade',
                    'summary_3_value'   => 'Alta',
                    'summary_3_sub'     => 'Todo integrante deve conhecer e observar integralmente este manual.',
                    'is_published'      => true,
                    'version'           => 1,
                ]
            );

            $manual->update([
                'title'             => 'Manual Interno — Grupo de Resposta Rápida (GRR)',
                'kicker'            => 'REGULAMENTO INTERNO • GRR',
                'subtitle'          => 'Normas, diretrizes e procedimentos internos aplicáveis ao efetivo do Grupo de Resposta Rápida.',
                'description'       => 'Documento institucional interno para consulta e padronização operacional.',
                'status_label'      => 'Status',
                'status_value'      => 'Ativo',
                'environment_label' => 'Ambiente',
                'environment_value' => 'Brasil Capital Roleplay',
                'alert_title'       => 'Atenção',
                'alert_text'        => 'Este manual possui caráter interno. Todos os integrantes devem conhecer e cumprir integralmente suas disposições.',
                'summary_1_label'   => 'Finalidade',
                'summary_1_value'   => 'Normatizar',
                'summary_1_sub'     => 'Padronizar condutas, operações e procedimentos administrativos da corporação.',
                'summary_2_label'   => 'Estrutura',
                'summary_2_value'   => 'Capítulos',
                'summary_2_sub'     => 'Organização contínua em seções e artigos para consulta direta.',
                'summary_3_label'   => 'Obrigatoriedade',
                'summary_3_value'   => 'Alta',
                'summary_3_sub'     => 'Todo integrante deve conhecer e observar integralmente este manual.',
                'is_published'      => true,
                'version'           => 1,
            ]);

            if ($this->option('reset')) {
                $sectionIds = $manual->sections()->pluck('id');

                if ($sectionIds->isNotEmpty()) {
                    GrrManualArticle::whereIn('section_id', $sectionIds)->delete();
                }

                GrrManualSection::where('manual_id', $manual->id)->delete();
            }

            $sections = $this->parseManual($rawText);

            $sectionOrder = 1;

            foreach ($sections as $sectionData) {
                $section = GrrManualSection::create([
                    'manual_id'  => $manual->id,
                    'code'       => $sectionData['code'],
                    'anchor'     => $sectionData['anchor'],
                    'title'      => $sectionData['title'],
                    'subtitle'   => null,
                    'sort_order' => $sectionOrder++,
                    'is_active'  => true,
                ]);

                $articleOrder = 1;

                foreach ($sectionData['articles'] as $articleData) {
                    GrrManualArticle::create([
                        'section_id'     => $section->id,
                        'article_number' => $articleData['article_number'],
                        'title'          => null,
                        'body'           => $articleData['body'],
                        'sort_order'     => $articleOrder++,
                        'is_active'      => true,
                    ]);
                }
            }

            $this->importDisposicoesFinais($manual, $rawText, $sectionOrder);
        });

        $this->info('Manual importado com sucesso.');
        return self::SUCCESS;
    }

    private function parseManual(string $text): array
    {
        $text = str_replace(["\r\n", "\r"], "\n", trim($text));
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        preg_match('/^(DISPOSIÇÕES FINAIS)$/mi', $text, $finalMatch, PREG_OFFSET_CAPTURE);
        if (!empty($finalMatch[0][1])) {
            $text = trim(substr($text, 0, $finalMatch[0][1]));
        }

        $chapterRegex = '/^(CAPÍTULO\s+[IVXLCDM]+\s*[–-]\s*.+)$/mi';
        preg_match_all($chapterRegex, $text, $chapterMatches, PREG_OFFSET_CAPTURE);

        $sections = [];

        if (empty($chapterMatches[0])) {
            return [];
        }

        $count = count($chapterMatches[0]);

        for ($i = 0; $i < $count; $i++) {
            $heading = trim($chapterMatches[0][$i][0]);
            $start = $chapterMatches[0][$i][1];
            $end = $i + 1 < $count ? $chapterMatches[0][$i + 1][1] : strlen($text);
            $block = trim(substr($text, $start, $end - $start));

            [$code, $title] = $this->splitChapterHeading($heading);

            $body = trim(preg_replace('/^' . preg_quote($heading, '/') . '\s*/u', '', $block, 1));

            $sections[] = [
                'code'     => $code,
                'title'    => $title,
                'anchor'   => Str::slug($code . ' ' . $title),
                'articles' => $this->parseArticles($body),
            ];
        }

        return $sections;
    }

    private function importDisposicoesFinais(GrrManual $manual, string $text, int $sectionOrder): void
    {
        if (!preg_match('/^(DISPOSIÇÕES FINAIS)\s*(.*)$/mis', $text, $match)) {
            return;
        }

        $body = trim($match[2] ?? '');

        if ($body === '') {
            return;
        }

        $section = GrrManualSection::create([
            'manual_id'  => $manual->id,
            'code'       => 'DISPOSIÇÕES FINAIS',
            'anchor'     => Str::slug('disposicoes-finais'),
            'title'      => 'DISPOSIÇÕES FINAIS',
            'subtitle'   => null,
            'sort_order' => $sectionOrder,
            'is_active'  => true,
        ]);

        $articleOrder = 1;

        foreach ($this->parseArticles($body) as $articleData) {
            GrrManualArticle::create([
                'section_id'     => $section->id,
                'article_number' => $articleData['article_number'],
                'title'          => null,
                'body'           => $articleData['body'],
                'sort_order'     => $articleOrder++,
                'is_active'      => true,
            ]);
        }
    }

    private function splitChapterHeading(string $heading): array
    {
        if (preg_match('/^(CAPÍTULO\s+[IVXLCDM]+)\s*[–-]\s*(.+)$/iu', $heading, $m)) {
            return [trim($m[1]), trim($m[2])];
        }

        return [$heading, 'SEM TÍTULO'];
    }

    private function parseArticles(string $text): array
    {
        $text = trim($text);

        preg_match_all('/Art\.\s*\d+º?\s*[–-]?/iu', $text, $matches, PREG_OFFSET_CAPTURE);

        if (empty($matches[0])) {
            return [];
        }

        $articles = [];
        $count = count($matches[0]);

        for ($i = 0; $i < $count; $i++) {
            $articleTag = trim($matches[0][$i][0]);
            $start = $matches[0][$i][1];
            $end = $i + 1 < $count ? $matches[0][$i + 1][1] : strlen($text);

            $block = trim(substr($text, $start, $end - $start));
            $body = trim(preg_replace('/^' . preg_quote($articleTag, '/') . '\s*/u', '', $block, 1));
            $body = preg_replace("/\n{3,}/", "\n\n", $body);

            $articles[] = [
                'article_number' => $this->normalizeArticleNumber($articleTag),
                'body'           => $body,
            ];
        }

        return $articles;
    }

    private function normalizeArticleNumber(string $articleTag): string
    {
        if (preg_match('/Art\.\s*(\d+º?)/iu', $articleTag, $m)) {
            return 'Art. ' . trim($m[1]);
        }

        return trim($articleTag);
    }
}