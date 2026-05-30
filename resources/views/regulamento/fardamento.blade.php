@extends('layouts.app')

@section('content')
@php
    $sexos = [
        'masculino' => 'Masculino',
        'feminino'  => 'Feminino',
    ];

    $cargosMasculino = [
        'aluno'             => 'Aluno',
        'agente_3'          => 'Agente de 3º Classe',
        'agente_2'          => 'Agente de 2º Classe',
        'agente_1'          => 'Agente de 1º Classe',
        'classe_especial'   => 'Agente Especial',
        'inspetor'          => 'Inspetor',
        'superintendente'   => 'Superintendente',
        'coordenador'       => 'Coordenador',
        'coordenador_geral' => 'Coordenador Geral',
        'vice_diretor'      => 'Vice - Diretor',
        'diretor'           => 'Diretor',
    ];

    // No feminino, por solicitação, aparecem somente duas opções:
    // Aluna e Outras Patentes. Os demais cargos femininos ficam agrupados.
    $cargosFeminino = [
        'aluna'           => 'Aluna',
        'outras_patentes' => 'Outras Patentes',
    ];

    $cargosPorSexo = [
        'masculino' => $cargosMasculino,
        'feminino'  => $cargosFeminino,
    ];

    $cargoMeta = [
        'aluno'             => ['sigla' => 'AL', 'nivel' => 'Formação'],
        'aluna'             => ['sigla' => 'AL', 'nivel' => 'Formação'],
        'outras_patentes'   => ['sigla' => 'OP', 'nivel' => 'Feminino'],
        'agente_3'          => ['sigla' => 'A3', 'nivel' => 'Operacional'],
        'agente_2'          => ['sigla' => 'A2', 'nivel' => 'Operacional'],
        'agente_1'          => ['sigla' => 'A1', 'nivel' => 'Operacional'],
        'classe_especial'   => ['sigla' => 'CE', 'nivel' => 'Classe Especial'],
        'inspetor'          => ['sigla' => 'IN', 'nivel' => 'Comando'],
        'superintendente'   => ['sigla' => 'SU', 'nivel' => 'Comando'],
        'coordenador'       => ['sigla' => 'CO', 'nivel' => 'Gestão'],
        'coordenador_geral' => ['sigla' => 'CG', 'nivel' => 'Gestão'],
        'vice_diretor'      => ['sigla' => 'VD', 'nivel' => 'Diretoria'],
        'diretor'           => ['sigla' => 'DR', 'nivel' => 'Diretoria'],
    ];

    $uniformesBase = [
        'aluno' => [
            'titulo' => 'Aluno',
            'descricao' => 'Fardamento oficial configurado para formação e instrução.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 559 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 223'],
                ['label' => 'Mãos', 'cmd' => 'maos 285'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 187 1'],
                ['label' => 'Chapéu', 'cmd' => 'chapeu 228 1'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 91 1'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'agente_3' => [
            'titulo' => 'Agente de 3º Classe',
            'descricao' => 'Fardamento oficial configurado para atuação operacional.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 560 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 222 1'],
                ['label' => 'Mãos', 'cmd' => 'maos 174'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 188 1'],
                ['label' => 'Chapéu', 'cmd' => 'chapeu 228 1'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 89 1'],
                ['label' => 'Adesivo', 'cmd' => 'adesivo 209'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'agente_2' => [
            'titulo' => 'Agente de 2º Classe',
            'descricao' => 'Fardamento oficial configurado para patrulhamento e operações.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 560 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 222 1'],
                ['label' => 'Mãos', 'cmd' => 'maos 174'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 188 1'],
                ['label' => 'Chapéu', 'cmd' => 'chapeu 229 1'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 89 1'],
                ['label' => 'Mochila', 'cmd' => 'mochila 148 1'],
                ['label' => 'Adesivo', 'cmd' => 'adesivo 209 2'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'agente_1' => [
            'titulo' => 'Agente de 1º Classe',
            'descricao' => 'Fardamento oficial configurado para atuação avançada.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 560 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 222 1'],
                ['label' => 'Mãos', 'cmd' => 'maos 20'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 188 1'],
                ['label' => 'Máscara', 'cmd' => 'mascara 249 1'],
                ['label' => 'Colete', 'cmd' => 'colete 89 1'],
                ['label' => 'Mochila', 'cmd' => 'mochila 148 1'],
                ['label' => 'Adesivo', 'cmd' => 'adesivo 209 1'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'classe_especial' => [
            'titulo' => 'Agente Especial',
            'descricao' => 'Fardamento oficial configurado para classe especial.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 560 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 222 1'],
                ['label' => 'Mãos', 'cmd' => 'maos 20'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 187 1'],
                ['label' => 'Máscara', 'cmd' => 'mascara 249 1'],
                ['label' => 'Colete', 'cmd' => 'colete 90 1'],
                ['label' => 'Mochila', 'cmd' => 'mochila 148 1'],
                ['label' => 'Adesivo', 'cmd' => 'adesivo 209 3'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'inspetor' => [
            'titulo' => 'Inspetor',
            'descricao' => 'Fardamento oficial configurado para função de inspeção.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 560 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 223'],
                ['label' => 'Mãos', 'cmd' => 'maos 20'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 187 1'],
                ['label' => 'Chapéu', 'cmd' => 'Livre', 'obs' => 'Com ou sem'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 90 1'],
                ['label' => 'Mochila', 'cmd' => 'mochila 148 1'],
                ['label' => 'Adesivo', 'cmd' => 'adesivo 209 4'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'superintendente' => [
            'titulo' => 'Superintendente',
            'descricao' => 'Fardamento oficial configurado para comando superior.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 560 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 223'],
                ['label' => 'Mãos', 'cmd' => 'maos 20'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 187 1'],
                ['label' => 'Chapéu', 'cmd' => 'Livre', 'obs' => 'Com ou sem'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 90 1'],
                ['label' => 'Mochila', 'cmd' => 'mochila 148 1'],
                ['label' => 'Adesivo', 'cmd' => 'adesivo 209 5'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'coordenador' => [
            'titulo' => 'Coordenador',
            'descricao' => 'Fardamento oficial configurado para coordenação.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 560 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 223'],
                ['label' => 'Mãos', 'cmd' => 'maos 20'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 187 1'],
                ['label' => 'Chapéu', 'cmd' => 'Livre', 'obs' => 'Com ou sem'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 90 1'],
                ['label' => 'Mochila', 'cmd' => 'mochila 148 1'],
                ['label' => 'Adesivo', 'cmd' => 'adesivo 209 9'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'coordenador_geral' => [
            'titulo' => 'Coordenador Geral',
            'descricao' => 'Fardamento oficial configurado para coordenação geral.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 560 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 223'],
                ['label' => 'Mãos', 'cmd' => 'maos 20'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 187 1'],
                ['label' => 'Chapéu', 'cmd' => 'Livre', 'obs' => 'Com ou sem'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 90 1'],
                ['label' => 'Mochila', 'cmd' => 'mochila 148 1'],
                ['label' => 'Adesivo', 'cmd' => 'adesivo 209 10'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'vice_diretor' => [
            'titulo' => 'Vice - Diretor',
            'descricao' => 'Fardamento oficial configurado para vice-diretoria.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 560 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 223'],
                ['label' => 'Mãos', 'cmd' => 'maos 20'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 187 1'],
                ['label' => 'Chapéu', 'cmd' => 'Livre', 'obs' => 'Com ou sem'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 90 1'],
                ['label' => 'Mochila', 'cmd' => 'mochila 148 1'],
                ['label' => 'Adesivo', 'cmd' => 'adesivo 209 11'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'diretor' => [
            'titulo' => 'Diretor',
            'descricao' => 'Fardamento oficial configurado para diretoria.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 560 1'],
                ['label' => 'Blusa', 'cmd' => 'blusa 223'],
                ['label' => 'Mãos', 'cmd' => 'maos 20'],
                ['label' => 'Calça', 'cmd' => 'calca 207 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 150 2'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 187 1'],
                ['label' => 'Chapéu', 'cmd' => 'Livre', 'obs' => 'Com ou sem'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 90 1'],
                ['label' => 'Mochila', 'cmd' => 'mochila 148 1'],
                ['label' => 'Adesivo', 'cmd' => 'adesivo 209 12'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

    ];
    $uniformesFemininoBase = [
        'aluna' => [
            'titulo' => 'Aluna',
            'descricao' => 'Fardamento feminino oficial configurado para formação e instrução.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 587 1'],
                ['label' => 'Camisa', 'cmd' => 'blusa 262 1'],
                ['label' => 'Mãos', 'cmd' => 'maos 446'],
                ['label' => 'Calça', 'cmd' => 'calca 220 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 137'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 156 1'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 80 1'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],

        'outras_patentes' => [
            'titulo' => 'Outras Patentes',
            'descricao' => 'Fardamento feminino oficial para Agente de 3º Classe e demais patentes.',
            'pecas' => [
                ['label' => 'Jaqueta', 'cmd' => 'jaqueta 588 1'],
                ['label' => 'Camisa', 'cmd' => 'blusa 262 1'],
                ['label' => 'Mãos', 'cmd' => 'maos 31'],
                ['label' => 'Calça', 'cmd' => 'calca 220 1'],
                ['label' => 'Sapato', 'cmd' => 'sapatos 137'],
                ['label' => 'Acessório', 'cmd' => 'acessorios 156 1'],
                ['label' => 'Máscara', 'cmd' => 'mascara 121'],
                ['label' => 'Colete', 'cmd' => 'colete 81 1'],
                ['label' => 'Operação • Chapéu', 'cmd' => 'chapeu 227', 'obs' => 'Para operação'],
                ['label' => 'Operação • Máscara', 'cmd' => 'mascara 250', 'obs' => 'Para operação'],
                ['label' => 'Operação • Óculos', 'cmd' => 'oculos 59', 'obs' => 'Para operação'],
                ['label' => 'Operação • Orelhas', 'cmd' => 'orelhas 43', 'obs' => 'Para operação'],
            ],
        ],
    ];

    $uniformes = [
        'masculino' => [],
        'feminino' => [],
    ];

    foreach ($uniformesBase as $cargoKey => $cargoData) {
        $meta = $cargoMeta[$cargoKey] ?? ['sigla' => 'FD', 'nivel' => 'Fardamento'];

        $uniformes['masculino'][$cargoKey] = [
            'status' => 'ativo',
            'titulo' => $cargoData['titulo'] . ' — Masculino',
            'descricao' => $cargoData['descricao'],
            'pecas' => $cargoData['pecas'],
            'sigla' => $meta['sigla'],
            'nivel' => $meta['nivel'],
        ];
    }

    foreach ($uniformesFemininoBase as $cargoKey => $cargoData) {
        $meta = $cargoMeta[$cargoKey] ?? ['sigla' => 'FD', 'nivel' => 'Fardamento'];

        $uniformes['feminino'][$cargoKey] = [
            'status' => 'ativo',
            'titulo' => $cargoData['titulo'] . ' — Feminino',
            'descricao' => $cargoData['descricao'],
            'pecas' => $cargoData['pecas'],
            'sigla' => $meta['sigla'],
            'nivel' => $meta['nivel'],
        ];
    }

    $totalOpcoes = collect($cargosPorSexo)->sum(fn($grupo) => count($grupo));

@endphp

<div class="container py-4">
    <div class="grr-fit-shell">

        <section class="grr-fit-hero mb-4">
            <div class="grr-fit-hero__mesh"></div>
            <div class="grr-fit-hero__glow grr-fit-hero__glow--blue"></div>
            <div class="grr-fit-hero__glow grr-fit-hero__glow--gold"></div>

            <div class="grr-fit-hero__inner">
                <div class="grr-fit-hero__main">
                    <div class="grr-fit-hero__badge">
                        <span class="dot"></span>
                        Central oficial de fardamento
                    </div>

                    <div class="grr-fit-hero__content">
                        <div class="grr-fit-hero__iconWrap">
                            <div class="grr-fit-hero__icon">
                                <span class="grr-fit-hero__iconMark"></span>
                            </div>
                        </div>

                        <div class="grr-fit-hero__text">
                            <h1 class="grr-fit-hero__title">Fardamento Oficial — G.R.R.</h1>
                            <p class="grr-fit-hero__sub">
                                Escolha o <strong>sexo</strong>, depois o <strong>cargo</strong>, e por fim visualize a
                                <strong>numeração completa do fardamento</strong> pronta para copiar no F8.
                            </p>

                            <div class="grr-fit-hero__pills">
                                <span class="grr-fit-pill grr-fit-pill--blue">Tema escuro</span>
                                <span class="grr-fit-pill grr-fit-pill--soft">Etapas recolhíveis</span>
                                <span class="grr-fit-pill grr-fit-pill--soft">Copiar rápido</span>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="grr-fit-hero__stats">
                    <div class="stat-card">
                        <span class="stat-label">Modo</span>
                        <strong class="stat-value">Etapas inteligentes</strong>
                    </div>
                    <div class="stat-card">
                        <span class="stat-label">Cargos</span>
                        <strong class="stat-value">{{ $totalOpcoes }}</strong>
                    </div>
                    <div class="stat-card">
                        <span class="stat-label">Fluxo</span>
                        <strong class="stat-value">1 → 2 → 3</strong>
                    </div>
                </aside>
            </div>
        </section>

        <section class="wizard-board mb-4">
            <div class="wizard-mini" id="miniStepSexo" hidden>
                <div class="wizard-mini__left">
                    <span class="wizard-mini__step">Etapa 1</span>
                    <div>
                        <strong>Sexo selecionado</strong>
                        <span id="miniSexoValue">Masculino</span>
                    </div>
                </div>
                <button type="button" class="wizard-mini__edit" onclick="reopenStep('sexo')">Alterar</button>
            </div>

            <div class="wizard-mini" id="miniStepCargo" hidden>
                <div class="wizard-mini__left">
                    <span class="wizard-mini__step">Etapa 2</span>
                    <div>
                        <strong>Cargo selecionado</strong>
                        <span id="miniCargoValue">Aluno</span>
                    </div>
                </div>
                <button type="button" class="wizard-mini__edit" onclick="reopenStep('cargo')">Alterar</button>
            </div>
        </section>

        <section class="panel-card wizard-step is-open mb-4" id="stepSexo">
            <div class="panel-card__header">
                <div>
                    <div class="panel-card__eyebrow">Etapa 1</div>
                    <h3 class="panel-card__title">Selecionar sexo</h3>
                    <p class="panel-card__desc mb-0">Escolha a base visual do uniforme antes de selecionar o cargo.</p>
                </div>
                <div class="panel-card__tag">Filtro principal</div>
            </div>

            <div class="selector-grid selector-grid--sexos">
                @foreach($sexos as $sexoKey => $sexoLabel)
                    <button
                        type="button"
                        class="selector-btn selector-btn--sexo sexo-btn {{ $loop->first ? 'is-active' : '' }}"
                        data-sexo="{{ $sexoKey }}">
                        <span class="selector-btn__icon selector-btn__icon--gender"></span>
                        <span class="selector-btn__content">
                            <strong>{{ $sexoLabel }}</strong>
                            <small>Selecionar modelo</small>
                        </span>
                    </button>
                @endforeach
            </div>
        </section>

        <section class="panel-card wizard-step is-hidden mb-4" id="stepCargo">
            <div class="panel-card__header">
                <div>
                    <div class="panel-card__eyebrow">Etapa 2</div>
                    <h3 class="panel-card__title">Selecionar cargo</h3>
                    <p class="panel-card__desc mb-0">Selecione a patente ou função para visualizar o fardamento correspondente. No feminino, use Aluna ou Outras Patentes.</p>
                </div>
                <div class="panel-card__tag">Lista dinâmica</div>
            </div>

            @foreach($uniformes as $sexoKey => $cargosDoSexo)
                <div class="cargo-panel {{ $sexoKey === 'masculino' ? 'is-visible' : '' }}" data-cargo-panel="{{ $sexoKey }}">
                    <div class="selector-grid selector-grid--cargos">
                        @foreach(($cargosPorSexo[$sexoKey] ?? []) as $cargoKey => $cargoLabel)
                            @php $meta = $cargoMeta[$cargoKey] ?? ['sigla' => 'FD', 'nivel' => 'Fardamento']; @endphp
                            <button
                                type="button"
                                class="selector-btn selector-btn--cargo cargo-btn {{ $loop->first ? 'is-active' : '' }}"
                                data-sexo="{{ $sexoKey }}"
                                data-cargo="{{ $cargoKey }}">
                                <span class="selector-btn__icon selector-btn__icon--sigla">{{ $meta['sigla'] }}</span>
                                <span class="selector-btn__content">
                                    <strong>{{ $cargoLabel }}</strong>
                                    <small>{{ $meta['nivel'] ?? 'Fardamento' }}</small>
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </section>

        <section class="panel-card wizard-step is-hidden mb-4" id="stepResultadoHead">
            <div class="panel-card__header">
                <div>
                    <div class="panel-card__eyebrow">Etapa 3</div>
                    <h3 class="panel-card__title">Numeração do fardamento</h3>
                    <p class="panel-card__desc mb-0">Resultado final com todas as peças e comandos prontos para uso.</p>
                </div>
                <div class="panel-card__tag">Resultado final</div>
            </div>
        </section>

        <section class="fit-results wizard-step is-hidden" id="stepResultadoBody">
            @foreach($uniformes as $sexoKey => $cargosDoSexo)
                @foreach($cargosDoSexo as $cargoKey => $item)
                    @php
                        $outfitId = 'outfit-' . $sexoKey . '-' . $cargoKey;
                        $pecasValidas = collect($item['pecas'])
                            ->filter(fn($p) => !empty($p['cmd']))
                            ->pluck('cmd')
                            ->prepend('/outfit')
                            ->implode("\n");
                    @endphp

                    <article
                        class="result-card {{ $sexoKey === 'masculino' && $cargoKey === 'aluno' ? 'is-visible' : '' }}"
                        data-sexo-content="{{ $sexoKey }}"
                        data-cargo-content="{{ $cargoKey }}">

                        <div class="result-card__hero">
                            <div class="result-card__heroLeft">
                                <div class="result-card__icon result-card__icon--small">{{ $item['sigla'] ?? 'FD' }}</div>
                                <div>
                                    <div class="result-card__eyebrow">{{ $item['nivel'] ?? 'Fardamento' }}</div>
                                    <h2 class="result-card__title">{{ $item['titulo'] }}</h2>
                                    <p class="result-card__desc mb-0">{{ $item['descricao'] }}</p>
                                </div>
                            </div>

                            <div class="result-card__heroRight">
                                <span class="result-pill">{{ ucfirst($sexoKey) }}</span>
                                <span class="result-pill result-pill--success">Configurado</span>
                            </div>
                        </div>

                        <div class="result-card__body">
                            <div class="result-summary result-summary--compact">
                                <div class="summary-box">
                                    <span class="summary-box__label">Peças listadas</span>
                                    <strong class="summary-box__value">{{ count($item['pecas']) }}</strong>
                                </div>
                                <div class="summary-box">
                                    <span class="summary-box__label">Comando rápido</span>
                                    <strong class="summary-box__value">Disponível</strong>
                                </div>
                                <div class="summary-box">
                                    <span class="summary-box__label">Formato</span>
                                    <strong class="summary-box__value">F8 / Linha única</strong>
                                </div>
                            </div>

                            <div class="cmd-panel cmd-panel--compact">
                                <div class="cmd-panel__header">
                                    <div>
                                        <h5 class="cmd-panel__title">Comandos do fardamento</h5>
                                        <p class="cmd-panel__desc mb-0">
                                            Copie individualmente cada item ou use os atalhos para copiar o conjunto completo.
                                        </p>
                                    </div>
                                </div>

                                <div class="cmd-grid">
                                    @foreach($item['pecas'] as $peca)
                                        <div class="cmd-row cmd-row--compact">
                                            <div class="cmd-row__accent"></div>

                                            <div class="cmd-left">
                                                <div class="cmd-key">
                                                    {{ $peca['label'] }}
                                                    @if(!empty($peca['obs']))
                                                        <span class="cmd-note">{{ $peca['obs'] }}</span>
                                                    @endif
                                                </div>
                                                <div class="cmd-val" data-cmdline="{{ $peca['cmd'] }}">
                                                    {{ $peca['cmd'] }}
                                                </div>
                                            </div>

                                            <button type="button" class="copy-btn copy-btn--compact" onclick="copyLine(this)">
                                                <span>Copiar</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="cmd-actions">
                                    <button type="button" class="action-btn action-btn--primary" onclick="copyOutfit('{{ $outfitId }}')">
                                        Copiar fardamento completo
                                    </button>
                                    <button type="button" class="action-btn action-btn--ghost" onclick="copyOutfitAsChat('{{ $outfitId }}')">
                                        Copiar em linha única
                                    </button>
                                </div>

                                <textarea class="d-none" id="{{ $outfitId }}">{{ $pecasValidas }}</textarea>
                            </div>
                        </div>
                    </article>
                @endforeach
            @endforeach
        </section>

    </div>
</div>

<div class="copy-toast" id="copyToast">
    <div class="copy-toast__icon">✓</div>
    <div class="copy-toast__content">
        <strong id="toastTitle">Copiado!</strong>
        <span id="toastDesc">Cole no F8.</span>
    </div>
</div>

<style>
    .grr-fit-shell{
        position:relative;
        padding:4px;
    }

    .grr-fit-shell,
    .grr-fit-shell *{
        box-sizing:border-box;
    }

    .grr-fit-hero{
        position:relative;
        overflow:hidden;
        border-radius:30px;
        border:1px solid rgba(255,255,255,.07);
        background:
            radial-gradient(circle at 10% 10%, rgba(59,130,246,.18), transparent 24%),
            radial-gradient(circle at 90% 18%, rgba(245,158,11,.10), transparent 22%),
            linear-gradient(135deg, #050b16 0%, #081120 45%, #0b1730 100%);
        box-shadow:
            0 28px 80px rgba(0,0,0,.38),
            inset 0 1px 0 rgba(255,255,255,.04);
        isolation:isolate;
    }

    .grr-fit-hero__mesh{
        position:absolute;
        inset:0;
        background-image:
            linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
        background-size:24px 24px;
        mask-image:linear-gradient(180deg, rgba(0,0,0,.85), transparent);
        opacity:.28;
        z-index:1;
    }

    .grr-fit-hero__glow{
        position:absolute;
        border-radius:999px;
        filter:blur(65px);
        opacity:.45;
        z-index:1;
    }

    .grr-fit-hero__glow--blue{
        width:280px;
        height:280px;
        background:rgba(59,130,246,.35);
        top:-60px;
        left:-50px;
    }

    .grr-fit-hero__glow--gold{
        width:220px;
        height:220px;
        background:rgba(245,158,11,.12);
        right:-40px;
        bottom:-70px;
    }

    .grr-fit-hero__inner{
        position:relative;
        z-index:2;
        padding:30px;
        display:flex;
        gap:24px;
        justify-content:space-between;
        align-items:stretch;
        flex-wrap:wrap;
    }

    .grr-fit-hero__main{ flex:1 1 640px; }

    .grr-fit-hero__badge{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:.55rem .9rem;
        margin-bottom:18px;
        border-radius:999px;
        background:rgba(255,255,255,.06);
        border:1px solid rgba(255,255,255,.08);
        color:#e2e8f0;
        font-size:.82rem;
        font-weight:800;
        backdrop-filter:blur(10px);
    }

    .grr-fit-hero__badge .dot{
        width:8px;
        height:8px;
        border-radius:999px;
        background:#22c55e;
        box-shadow:0 0 0 6px rgba(34,197,94,.12);
    }

    .grr-fit-hero__content{
        display:flex;
        align-items:flex-start;
        gap:18px;
    }

    .grr-fit-hero__icon{
        width:74px;
        height:74px;
        border-radius:22px;
        display:grid;
        place-items:center;
        background:linear-gradient(135deg, rgba(255,255,255,.14), rgba(255,255,255,.05));
        border:1px solid rgba(255,255,255,.10);
        box-shadow:0 20px 40px rgba(0,0,0,.22);
    }

    .grr-fit-hero__iconMark{
        width:18px;
        height:18px;
        border-radius:6px;
        display:block;
        background:linear-gradient(180deg, #60a5fa 0%, #2563eb 100%);
        box-shadow:0 0 0 8px rgba(96,165,250,.10);
    }

    .grr-fit-hero__title{
        margin:0 0 10px;
        color:#fff;
        font-size:clamp(1.8rem, 2.8vw, 2.5rem);
        line-height:1.06;
        font-weight:900;
        letter-spacing:-.02em;
    }

    .grr-fit-hero__sub{
        max-width:760px;
        color:rgba(226,232,240,.88);
        line-height:1.7;
        font-size:1rem;
        margin:0;
    }

    .grr-fit-hero__pills{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        margin-top:18px;
    }

    .grr-fit-pill{
        display:inline-flex;
        align-items:center;
        padding:.58rem .95rem;
        border-radius:999px;
        font-size:.8rem;
        font-weight:800;
        border:1px solid rgba(255,255,255,.08);
    }

    .grr-fit-pill--blue{
        color:#dbeafe;
        background:rgba(59,130,246,.14);
    }

    .grr-fit-pill--soft{
        color:#e2e8f0;
        background:rgba(255,255,255,.05);
    }

    .grr-fit-hero__stats{
        display:grid;
        grid-template-columns:1fr;
        gap:12px;
        min-width:220px;
        align-self:stretch;
    }

    .stat-card{
        border-radius:20px;
        padding:16px 18px;
        background:rgba(255,255,255,.06);
        border:1px solid rgba(255,255,255,.08);
        backdrop-filter:blur(10px);
    }

    .stat-label{
        display:block;
        color:rgba(226,232,240,.68);
        text-transform:uppercase;
        letter-spacing:.12em;
        font-size:.72rem;
        font-weight:800;
        margin-bottom:6px;
    }

    .stat-value{
        color:#fff;
        font-size:1rem;
        font-weight:900;
    }

    .wizard-board{
        display:flex;
        flex-wrap:wrap;
        gap:12px;
    }

    .wizard-mini{
        flex:1 1 280px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:14px;
        padding:12px 14px;
        border-radius:18px;
        background:linear-gradient(180deg, rgba(7,14,29,.92), rgba(9,18,36,.96));
        border:1px solid rgba(255,255,255,.07);
        box-shadow:0 16px 28px rgba(0,0,0,.22);
    }

    .wizard-mini__left{
        display:flex;
        align-items:center;
        gap:12px;
        min-width:0;
    }

    .wizard-mini__step{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-width:74px;
        padding:.45rem .7rem;
        border-radius:999px;
        background:rgba(37,99,235,.16);
        color:#93c5fd;
        border:1px solid rgba(59,130,246,.22);
        font-size:.74rem;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.08em;
    }

    .wizard-mini__left strong{
        display:block;
        color:#f8fbff;
        font-size:.9rem;
        line-height:1.2;
    }

    .wizard-mini__left span:last-child{
        display:block;
        color:rgba(203,213,225,.76);
        font-size:.86rem;
        margin-top:2px;
    }

    .wizard-mini__edit{
        border:1px solid rgba(255,255,255,.08);
        background:rgba(255,255,255,.05);
        color:#f8fbff;
        border-radius:12px;
        padding:.7rem .95rem;
        font-weight:800;
        white-space:nowrap;
        transition:.18s ease;
    }

    .wizard-mini__edit:hover{
        background:rgba(255,255,255,.08);
        transform:translateY(-1px);
    }

    .panel-card{
        border-radius:28px;
        padding:22px;
        background:linear-gradient(180deg, rgba(6,12,24,.98), rgba(8,16,32,.98));
        border:1px solid rgba(255,255,255,.07);
        box-shadow:
            0 22px 50px rgba(0,0,0,.24),
            inset 0 1px 0 rgba(255,255,255,.03);
        transition:all .25s ease;
    }

    .panel-card__header{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:16px;
        flex-wrap:wrap;
        margin-bottom:18px;
    }

    .panel-card__eyebrow{
        color:#3b82f6;
        font-size:.75rem;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.14em;
        margin-bottom:6px;
    }

    .panel-card__title{
        margin:0 0 6px;
        color:#f8fbff;
        font-size:1.3rem;
        font-weight:900;
    }

    .panel-card__desc{
        color:rgba(203,213,225,.74);
        font-size:.96rem;
        line-height:1.6;
    }

    .panel-card__tag{
        padding:.58rem .9rem;
        border-radius:999px;
        background:rgba(37,99,235,.10);
        color:#3b82f6;
        border:1px solid rgba(37,99,235,.18);
        font-size:.8rem;
        font-weight:800;
    }

    .wizard-step.is-hidden{
        display:none;
    }

    .selector-grid{
        display:grid;
        gap:14px;
    }

    .selector-grid--sexos{
        grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));
    }

    .selector-grid--cargos{
        grid-template-columns:repeat(auto-fit, minmax(210px, 1fr));
    }

    .selector-btn{
        position:relative;
        display:flex;
        align-items:center;
        gap:14px;
        width:100%;
        text-align:left;
        border:1px solid rgba(255,255,255,.07);
        background:linear-gradient(180deg, rgba(8,16,32,.98), rgba(6,12,24,.98));
        color:#e5edf7;
        border-radius:22px;
        padding:16px 18px;
        transition:.22s ease;
        box-shadow:0 12px 24px rgba(0,0,0,.14);
        overflow:hidden;
    }

    .selector-btn::before{
        content:"";
        position:absolute;
        inset:0;
        background:linear-gradient(135deg, rgba(37,99,235,.08), transparent 60%);
        opacity:0;
        transition:.22s ease;
    }

    .selector-btn:hover{
        transform:translateY(-2px);
        border-color:rgba(59,130,246,.22);
        box-shadow:0 18px 32px rgba(0,0,0,.18);
    }

    .selector-btn:hover::before{
        opacity:1;
    }

    .selector-btn.is-active{
        color:#fff;
        border-color:rgba(37,99,235,.70);
        background:linear-gradient(135deg, #102452 0%, #17367b 45%, #194a9c 100%);
        box-shadow:0 22px 36px rgba(15,23,42,.34);
    }

    .selector-btn__icon{
        width:48px;
        height:48px;
        min-width:48px;
        border-radius:16px;
        display:grid;
        place-items:center;
        background:rgba(255,255,255,.04);
        border:1px solid rgba(255,255,255,.06);
        position:relative;
        z-index:1;
        font-size:.86rem;
        font-weight:900;
        letter-spacing:.04em;
    }

    .selector-btn__icon--gender::before{
        content:"";
        width:14px;
        height:14px;
        border-radius:4px;
        display:block;
        background:linear-gradient(180deg, #60a5fa 0%, #2563eb 100%);
        box-shadow:0 0 0 6px rgba(96,165,250,.08);
    }

    .selector-btn__icon--sigla{
        color:#dbeafe;
        background:rgba(3,10,24,.55);
        border-color:rgba(255,255,255,.05);
    }

    .selector-btn.is-active .selector-btn__icon{
        background:rgba(255,255,255,.10);
        border-color:rgba(255,255,255,.12);
        color:#fff;
    }

    .selector-btn__content{
        position:relative;
        z-index:1;
        display:flex;
        flex-direction:column;
        gap:2px;
    }

    .selector-btn__content strong{
        font-size:.98rem;
        font-weight:900;
        color:inherit;
    }

    .selector-btn__content small{
        font-size:.82rem;
        opacity:.78;
        font-weight:700;
        color:inherit;
    }

    .cargo-panel,
    .result-card{
        display:none;
    }

    .cargo-panel.is-visible,
    .result-card.is-visible{
        display:block;
    }

    .result-card{
        border-radius:26px;
        overflow:hidden;
        border:1px solid rgba(255,255,255,.07);
        background:linear-gradient(180deg, rgba(6,12,24,.98), rgba(8,16,32,.98));
        box-shadow:0 20px 44px rgba(0,0,0,.26);
    }

    .result-card__hero{
        display:flex;
        justify-content:space-between;
        gap:16px;
        align-items:flex-start;
        flex-wrap:wrap;
        padding:18px 20px 16px;
        background:
            radial-gradient(circle at top left, rgba(37,99,235,.08), transparent 36%),
            linear-gradient(180deg, rgba(11,23,48,.96), rgba(7,14,29,.96));
        border-bottom:1px solid rgba(255,255,255,.05);
    }

    .result-card__heroLeft{
        display:flex;
        gap:14px;
        align-items:flex-start;
    }

    .result-card__icon{
        border-radius:16px;
        display:grid;
        place-items:center;
        background:linear-gradient(135deg, rgba(37,99,235,.16), rgba(14,165,233,.10));
        border:1px solid rgba(37,99,235,.14);
        box-shadow:0 10px 20px rgba(0,0,0,.16);
        color:#dbeafe;
        font-weight:900;
        letter-spacing:.04em;
    }

    .result-card__icon--small{
        width:46px;
        height:46px;
        min-width:46px;
        font-size:.82rem;
    }

    .result-card__eyebrow{
        color:#60a5fa;
        font-size:.72rem;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.14em;
        margin-bottom:4px;
    }

    .result-card__title{
        margin:0 0 6px;
        color:#f8fbff;
        font-size:clamp(1.08rem, 1.8vw, 1.35rem);
        font-weight:900;
    }

    .result-card__desc{
        color:rgba(203,213,225,.72);
        line-height:1.5;
        font-size:.92rem;
        max-width:760px;
    }

    .result-card__heroRight{
        display:flex;
        gap:10px;
        flex-wrap:wrap;
    }

    .result-pill{
        display:inline-flex;
        align-items:center;
        padding:.52rem .85rem;
        border-radius:999px;
        background:rgba(255,255,255,.05);
        color:#f8fbff;
        border:1px solid rgba(255,255,255,.07);
        font-size:.78rem;
        font-weight:800;
    }

    .result-pill--success{
        background:rgba(34,197,94,.12);
        color:#86efac;
        border-color:rgba(34,197,94,.16);
    }

    .result-card__body{
        padding:18px 20px 20px;
    }

    .result-summary{
        display:grid;
        gap:12px;
        margin-bottom:16px;
    }

    .result-summary--compact{
        grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));
    }

    .summary-box{
        border-radius:16px;
        padding:13px 14px;
        background:rgba(2,6,23,.34);
        border:1px solid rgba(255,255,255,.05);
    }

    .summary-box__label{
        display:block;
        color:rgba(203,213,225,.60);
        font-size:.7rem;
        text-transform:uppercase;
        letter-spacing:.12em;
        font-weight:800;
        margin-bottom:5px;
    }

    .summary-box__value{
        color:#f8fbff;
        font-size:.95rem;
        font-weight:900;
    }

    .cmd-panel{
        border-radius:22px;
        background:rgba(2,6,23,.28);
        border:1px solid rgba(255,255,255,.05);
    }

    .cmd-panel--compact{
        padding:16px;
    }

    .cmd-panel__header{
        margin-bottom:14px;
    }

    .cmd-panel__title{
        margin:0 0 5px;
        color:#f8fbff;
        font-size:1rem;
        font-weight:900;
    }

    .cmd-panel__desc{
        color:rgba(203,213,225,.68);
        font-size:.9rem;
        line-height:1.5;
    }

    .cmd-grid{
        display:grid;
        gap:10px;
    }

    .cmd-row{
        position:relative;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:14px;
        border-radius:16px;
        background:rgba(7,14,29,.92);
        border:1px solid rgba(255,255,255,.05);
        box-shadow:0 8px 18px rgba(0,0,0,.12);
    }

    .cmd-row--compact{
        padding:12px 13px 12px 16px;
    }

    .cmd-row__accent{
        position:absolute;
        top:10px;
        bottom:10px;
        left:0;
        width:4px;
        border-radius:999px;
        background:linear-gradient(180deg, #2563eb, #0ea5e9);
    }

    .cmd-left{
        min-width:0;
        flex:1;
        padding-left:10px;
    }

    .cmd-key{
        display:flex;
        align-items:center;
        gap:8px;
        flex-wrap:wrap;
        color:#f8fbff;
        font-weight:900;
        margin-bottom:4px;
    }

    .cmd-note{
        display:inline-flex;
        align-items:center;
        padding:.24rem .52rem;
        border-radius:999px;
        background:rgba(245,158,11,.12);
        color:#fcd34d;
        font-size:.7rem;
        font-weight:800;
    }

    .cmd-val{
        font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        color:#93c5fd;
        font-size:.92rem;
        word-break:break-word;
    }

    .copy-btn{
        border:0;
        border-radius:12px;
        font-weight:900;
        color:#fff;
        background:linear-gradient(135deg, #0f172a, #1e293b);
        box-shadow:0 10px 18px rgba(0,0,0,.18);
        transition:.2s ease;
        white-space:nowrap;
    }

    .copy-btn--compact{
        padding:.76rem .95rem;
        min-width:104px;
    }

    .copy-btn:hover{
        transform:translateY(-1px);
        filter:brightness(1.06);
    }

    .cmd-actions{
        display:flex;
        flex-wrap:wrap;
        gap:10px;
        margin-top:14px;
    }

    .action-btn{
        border:0;
        border-radius:999px;
        padding:.86rem 1.1rem;
        font-weight:900;
        transition:.2s ease;
    }

    .action-btn--primary{
        color:#fff;
        background:linear-gradient(135deg, #1d4ed8 0%, #2563eb 55%, #0ea5e9 100%);
        box-shadow:0 14px 24px rgba(37,99,235,.20);
    }

    .action-btn--ghost{
        color:#f8fbff;
        background:rgba(255,255,255,.06);
        border:1px solid rgba(255,255,255,.08);
        box-shadow:0 8px 14px rgba(0,0,0,.10);
    }

    .action-btn:hover{
        transform:translateY(-1px);
    }

    .copy-toast{
        position:fixed;
        right:20px;
        bottom:20px;
        z-index:9999;
        display:flex;
        align-items:center;
        gap:12px;
        min-width:260px;
        max-width:340px;
        padding:14px 16px;
        border-radius:18px;
        background:rgba(15,23,42,.96);
        color:#fff;
        border:1px solid rgba(255,255,255,.08);
        box-shadow:0 24px 40px rgba(0,0,0,.28);
        opacity:0;
        pointer-events:none;
        transform:translateY(12px) scale(.98);
        transition:.22s ease;
    }

    .copy-toast.show{
        opacity:1;
        pointer-events:auto;
        transform:translateY(0) scale(1);
    }

    .copy-toast__icon{
        width:38px;
        height:38px;
        min-width:38px;
        border-radius:12px;
        display:grid;
        place-items:center;
        background:linear-gradient(135deg, #16a34a, #22c55e);
        font-weight:900;
        color:#fff;
        box-shadow:0 10px 20px rgba(34,197,94,.18);
    }

    .copy-toast__content{
        display:flex;
        flex-direction:column;
        gap:2px;
    }

    .copy-toast__content strong{
        font-size:.96rem;
    }

    .copy-toast__content span{
        font-size:.86rem;
        color:rgba(226,232,240,.82);
    }

    @media (max-width: 991px){
        .grr-fit-hero__inner{
            padding:22px;
        }

        .result-card__hero,
        .result-card__body{
            padding-left:18px;
            padding-right:18px;
        }
    }

    @media (max-width: 768px){
        .panel-card,
        .result-card,
        .grr-fit-hero{
            border-radius:24px;
        }

        .grr-fit-hero__content{
            flex-direction:column;
        }

        .grr-fit-hero__icon{
            width:64px;
            height:64px;
        }

        .cmd-row{
            flex-direction:column;
            align-items:stretch;
        }

        .copy-btn,
        .action-btn{
            width:100%;
        }

        .cmd-actions{
            flex-direction:column;
        }

        .copy-toast{
            right:14px;
            left:14px;
            bottom:14px;
            max-width:unset;
            min-width:unset;
        }

        .wizard-mini{
            flex-direction:column;
            align-items:stretch;
        }

        .wizard-mini__edit{
            width:100%;
        }
    }
</style>

<script>
    const state = {
        sexo: 'masculino',
        cargo: 'aluno',
    };

    function showToast(title, desc){
        const toast = document.getElementById('copyToast');
        const toastTitle = document.getElementById('toastTitle');
        const toastDesc = document.getElementById('toastDesc');

        toastTitle.textContent = title || 'Copiado!';
        toastDesc.textContent = desc || 'Cole no F8.';
        toast.classList.add('show');

        clearTimeout(window.__copyToastTimer);
        window.__copyToastTimer = setTimeout(() => {
            toast.classList.remove('show');
        }, 2200);
    }

    async function copyRaw(text){
        try{
            await navigator.clipboard.writeText(text);
            showToast('Copiado!', 'Cole no F8.');
        }catch(e){
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            textarea.remove();
            showToast('Copiado!', 'Cole no F8.');
        }
    }

    function copyLine(btn){
        const row = btn.closest('.cmd-row');
        const val = row.querySelector('.cmd-val');
        const text = val?.dataset?.cmdline || val?.textContent?.trim() || '';
        if(!text) return;
        copyRaw(text);
    }

    function copyOutfit(id){
        const el = document.getElementById(id);
        const text = el ? el.value.trim() : '';
        if(!text) return;
        copyRaw(text);
    }

    function copyOutfitAsChat(id){
        const el = document.getElementById(id);
        if(!el) return;

        const text = el.value
            .split('\n')
            .map(v => v.trim())
            .filter(Boolean)
            .join(' ; ');

        if(!text) return;
        copyRaw(text);
    }

    function activateButton(groupSelector, matchSelector){
        document.querySelectorAll(groupSelector).forEach(btn => btn.classList.remove('is-active'));
        const target = document.querySelector(matchSelector);
        if(target) target.classList.add('is-active');
    }

    function showCargoPanel(sexo){
        document.querySelectorAll('[data-cargo-panel]').forEach(panel => {
            panel.classList.remove('is-visible');
        });

        const panel = document.querySelector(`[data-cargo-panel="${sexo}"]`);
        if(panel) panel.classList.add('is-visible');
    }

    function showContent(sexo, cargo){
        document.querySelectorAll('[data-sexo-content][data-cargo-content]').forEach(card => {
            card.classList.remove('is-visible');
        });

        const target = document.querySelector(`[data-sexo-content="${sexo}"][data-cargo-content="${cargo}"]`);
        if(target){
            target.classList.add('is-visible');
        }
    }

    function updateMiniCards(){
        const sexoLabel = document.querySelector(`.sexo-btn[data-sexo="${state.sexo}"] strong`)?.textContent?.trim() || '—';
        const cargoLabel = document.querySelector(`.cargo-btn[data-sexo="${state.sexo}"][data-cargo="${state.cargo}"] strong`)?.textContent?.trim() || '—';

        document.getElementById('miniSexoValue').textContent = sexoLabel;
        document.getElementById('miniCargoValue').textContent = cargoLabel;
    }

    function openStep(elId){
        const el = document.getElementById(elId);
        if(el) el.classList.remove('is-hidden');
    }

    function closeStep(elId){
        const el = document.getElementById(elId);
        if(el) el.classList.add('is-hidden');
    }

    function showMini(elId){
        const el = document.getElementById(elId);
        if(el) el.hidden = false;
    }

    function hideMini(elId){
        const el = document.getElementById(elId);
        if(el) el.hidden = true;
    }

    function setSexo(sexo, collapse = true){
        state.sexo = sexo;
        showCargoPanel(sexo);

        activateButton('.sexo-btn', `.sexo-btn[data-sexo="${sexo}"]`);

        const firstCargoBtn = document.querySelector(`.cargo-panel.is-visible .cargo-btn`);
        if(firstCargoBtn){
            state.cargo = firstCargoBtn.dataset.cargo;
            activateButton(`.cargo-btn[data-sexo="${sexo}"]`, `.cargo-btn[data-sexo="${sexo}"][data-cargo="${state.cargo}"]`);
            showContent(sexo, state.cargo);
        }

        updateMiniCards();

        if(collapse){
            closeStep('stepSexo');
            showMini('miniStepSexo');
            openStep('stepCargo');
            hideMini('miniStepCargo');
            closeStep('stepResultadoHead');
            closeStep('stepResultadoBody');
        }
    }

    function setCargo(sexo, cargo, collapse = true){
        state.sexo = sexo;
        state.cargo = cargo;

        activateButton(`.cargo-btn[data-sexo="${sexo}"]`, `.cargo-btn[data-sexo="${sexo}"][data-cargo="${cargo}"]`);
        showContent(sexo, cargo);
        updateMiniCards();

        if(collapse){
            closeStep('stepCargo');
            showMini('miniStepCargo');
            openStep('stepResultadoHead');
            openStep('stepResultadoBody');
        }
    }

    function reopenStep(step){
        if(step === 'sexo'){
            openStep('stepSexo');
            hideMini('miniStepSexo');
            hideMini('miniStepCargo');
            closeStep('stepCargo');
            closeStep('stepResultadoHead');
            closeStep('stepResultadoBody');
            return;
        }

        if(step === 'cargo'){
            showMini('miniStepSexo');
            openStep('stepCargo');
            hideMini('miniStepCargo');
            closeStep('stepResultadoHead');
            closeStep('stepResultadoBody');
        }
    }

    document.querySelectorAll('.sexo-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            setSexo(btn.dataset.sexo, true);
        });
    });

    document.querySelectorAll('.cargo-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            setCargo(btn.dataset.sexo, btn.dataset.cargo, true);
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        setSexo('masculino', false);
        showMini('miniStepSexo');
        closeStep('stepSexo');
        openStep('stepCargo');
        hideMini('miniStepCargo');
        closeStep('stepResultadoHead');
        closeStep('stepResultadoBody');
        updateMiniCards();
    });
</script>
@endsection