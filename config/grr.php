<?php

return [

    /*
    |--------------------------------------------------------------------------
    | XP (RANKING / RELATÓRIOS)
    |--------------------------------------------------------------------------
    */
    'xp' => [
        'drogas'       => 8,
        'pistolas'     => 90,
        'smg_fuzil'    => 120,
        'municoes'     => 9,
        'dinheiro'     => 50,
        'explosivos'   => 100,
        'lockpicks'    => 30,

        'abordagens'            => 3,
        'multas'                => 5,
        'bopm'                  => 25,
        'viaturas_fiscalizadas' => 1,

        'relatorio_aprovado'    => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | MULTIPLICADORES DE XP POR PAPEL
    |--------------------------------------------------------------------------
    */
    'xp_multipliers' => [
        'P1' => 1.30,
        'P2' => 1.30,
        'P3' => 1.00,
        'P4' => 1.00,
        'P5' => 1.00,
    ],

    /*
    |--------------------------------------------------------------------------
    | CAMPOS DE HORÁRIO DA PATRULHA (RELATÓRIOS)
    |--------------------------------------------------------------------------
    */
    'patrulha' => [
        'inicio' => 'inicio_patrulhamento',
        'fim'    => 'final_patrulhamento',
    ],

    /*
    |--------------------------------------------------------------------------
    | EFETIVO (HIERARQUIA)
    |--------------------------------------------------------------------------
    */
    'cargos' => [
        1  => 'Aluno',
        2  => 'Agente de 3º Classe',
        3  => 'Agente de 2º Classe',
        4  => 'Agente de 1º Classe',
        5  => 'Agente Especial',
        6  => 'Inspetor',
        7  => 'Superintendente',
        8  => 'Coordenador',
        9  => 'Vice Diretor',
        10 => 'Diretor',
    ],

    /*
    |--------------------------------------------------------------------------
    | AUDITORIA (GRUPOS + LABELS)
    |--------------------------------------------------------------------------
    | Lido por: config('grr.auditoria.grupos')
    */
    'auditoria' => [
        'grupos' => [

            // =========================
            // AUTENTICAÇÃO
            // =========================
            'Autenticação' => [
                'login'           => 'Login realizado',
                'logout'          => 'Logout',
                'login_falhou'    => 'Tentativa de login inválida',
                'login_bloqueado' => 'Login bloqueado (usuário suspenso/desligado/inativo)',
            ],

            // =========================
            // RELATÓRIOS
            // =========================
            'Relatórios' => [
                'relatorio_criado'              => 'Relatório criado',
                'relatorio_editado'             => 'Relatório editado',
                'relatorio_finalizado'          => 'Relatório finalizado',
                'relatorio_criado_e_finalizado' => 'Relatório criado e finalizado',
                'relatorio_aprovado'            => 'Relatório aprovado',
                'relatorio_reprovado'           => 'Relatório reprovado',
                'relatorio_decisao_aberta'      => 'Relatórios: decisão aberta (editar)',
            ],

            // =========================
            // EFETIVO
            // =========================
            'Efetivo' => [
                'efetivo_index_aberto'   => 'Acesso ao painel do efetivo',
                'efetivo_show_aberto'    => 'Ficha do oficial acessada',

                'efetivo_create_aberto'  => 'Tela de cadastro de oficial aberta',
                'efetivo_criado'         => 'Oficial cadastrado',

                'efetivo_edit_aberto'    => 'Tela de edição de oficial aberta',
                'efetivo_editado'        => 'Oficial editado',

                'efetivo_promovido'      => 'Oficial promovido',
                'efetivo_promovido_massa'          => 'Promoção em massa (por oficial)',
                'efetivo_promocao_massa_executada' => 'Promoção em massa executada (geral)',

                'efetivo_suspenso'       => 'Oficial suspenso',
                'efetivo_reativado'      => 'Oficial reativado',
                'efetivo_removido'       => 'Oficial removido do sistema',

                'efetivo_create_negado'         => 'Tentativa negada de cadastrar oficial',
                'efetivo_update_negado'         => 'Tentativa negada de editar oficial',
                'efetivo_promocao_negada'       => 'Tentativa negada de promover oficial',
                'efetivo_promocao_massa_negada' => 'Tentativa negada de promoção em massa',
                'efetivo_suspender_negado'      => 'Tentativa negada de suspender oficial',
                'efetivo_reativar_negado'       => 'Tentativa negada de reativar oficial',
                'efetivo_destroy_negado'        => 'Tentativa negada de remover oficial',
            ],

            // =========================
            // AUDITORIA (PROTEÇÃO)
            // =========================
            'Auditoria (Proteção)' => [
                'auditoria_unlock_sucesso' => 'Auditoria desbloqueada',
                'auditoria_unlock_falha'   => 'Tentativa inválida de desbloqueio da auditoria',
                'auditoria_lock'           => 'Auditoria travada',
            ],

            // =========================
            // CANAIS DE ATENDIMENTO
            // =========================
            'Canais de Atendimento' => [
                'atendimento_publico_enviado' => 'Mensagem enviada (público)',
                'atendimento_index_aberto'    => 'Painel aberto',
                'atendimento_show_aberto'     => 'Atendimento visualizado',
                'atendimento_status_alterado' => 'Status alterado',
            ],

            // =========================
            // PRÉ-INSCRIÇÕES
            // =========================
            'Pré-inscrições' => [
                'preinscricao_admin_index_aberto'    => 'Lista aberta (admin)',
                'preinscricao_admin_show_aberto'     => 'Ficha aberta (admin)',
                'preinscricao_admin_status_alterado' => 'Decisão registrada (admin)',
            ],

            // =========================
            // TICKETS (USUÁRIO)
            // =========================
            'Tickets (Usuário)' => [
                'ticket_user_index_aberto'  => 'Meus tickets abertos',
                'ticket_user_create_aberto' => 'Tela abrir ticket',
                'ticket_user_criado'        => 'Ticket criado',
                'ticket_show_aberto'        => 'Ticket visualizado',
                'ticket_user_respondeu'     => 'Usuário respondeu',
            ],

            // =========================
            // TICKETS (ADMIN)
            // =========================
            'Tickets (Admin)' => [
                'ticket_admin_index_aberto'    => 'Lista aberta',
                'ticket_admin_show_aberto'     => 'Ticket visualizado',
                'ticket_admin_status_alterado' => 'Status alterado',
                'ticket_admin_respondeu'       => 'Resposta enviada',
            ],
        ],
    ],
];
