<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Controllers públicos e internos
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditoriaController;

/* ✅ Efetivo */
use App\Http\Controllers\EfetivoController;

/* ✅ Recrutamento */
use App\Http\Controllers\RecrutamentoController;

/* ✅ Atendimento (Denúncia / Solicitação / Sugestão / Elogio) */
use App\Http\Controllers\AtendimentoController;

/* ✅ Resultados Operacionais (PÚBLICO) */
use App\Http\Controllers\ResultadosPublicosController;

/* ✅ Busca (PÚBLICO) */
use App\Http\Controllers\BuscaController;

/* ✅ Regulamento (INTERNO) */
use App\Http\Controllers\RegulamentoController;

/* ✅ Manual Interno editável */
use App\Http\Controllers\GrrManualController;

/* ✅ Administrativo - Pré-inscrições (NÍVEL 9+) */
use App\Http\Controllers\Admin\PreInscricoesAdminController;

/* ✅ SUPORTE - Tickets (Qualquer nível logado) */
use App\Http\Controllers\TicketController;

/* ✅ SUPORTE - Tickets (Admin) */
use App\Http\Controllers\Admin\TicketAdminController;

/* ✅ RH - Recursos Humanos */
use App\Http\Controllers\RhController;
use App\Http\Controllers\RhPermissionController;
use App\Http\Controllers\RhHierarquiaController;

/* ✅ Lookup da hierarquia (JSON p/ autopreencher Controle de Saída) */
use App\Http\Controllers\RhHierarquiaLookupController;

/* ✅ Controle de Saída (CRUD) */
use App\Http\Controllers\RhControleSaidaController;

/* ✅ SOLICITAÇÃO DE ACESSO (Cadastro vira aprovação) */
use App\Http\Controllers\SolicitacaoAcessoController;

/* ✅ Model da Hierarquia Interna */
use App\Models\RhHierarquiaRecord;

/*
|--------------------------------------------------------------------------
| HOME / PORTAL
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('publico.home');
})->name('publico.home');

Route::get('/portal', function () {
    return redirect()->route('publico.home');
})->name('portal');

/*
|--------------------------------------------------------------------------
| ✅ SOLICITAÇÃO DE ACESSO (PÚBLICO)
|--------------------------------------------------------------------------
| - GET: exibe a página/form
| - POST: cria solicitação (não cria usuário)
| - Use no Login.blade.php: href="{{ route('solicitacao.form') }}"
| - Use no form: action="{{ route('solicitacao.store') }}"
*/
Route::get('/solicitar-acesso', function () {
    return view('publico.solicitar-acesso');
})->name('solicitacao.form');

Route::post('/solicitar-acesso', [SolicitacaoAcessoController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('solicitacao.store');

/*
|--------------------------------------------------------------------------
| ✅ BUSCA (PÚBLICO) — /buscar?q=...
|--------------------------------------------------------------------------
*/
Route::get('/buscar', [BuscaController::class, 'index'])->name('buscar');

/*
|--------------------------------------------------------------------------
| ✅ HIERARQUIA PÚBLICA — /hierarquia
|--------------------------------------------------------------------------
| Página pública da hierarquia, puxando os mesmos registros da hierarquia
| interna através do Model RhHierarquiaRecord.
*/
Route::get('/hierarquia', function () {
    $hierarquiaPublica = RhHierarquiaRecord::query()
        ->where(function ($query) {
            $query->whereNull('status')
                ->orWhereNotIn('status', [
                    'desligado',
                    'exonerado',
                    'inativo',
                    'reprovado',
                    'expulso',
                ]);
        })
        ->get()
        ->sortBy(function ($membro) {
            $cargo = Str::of($membro->cargo ?? '')
                ->lower()
                ->ascii()
                ->replace(['º', '°', 'ª', '-', '_', '.'], ' ')
                ->replaceMatches('/\s+/', ' ')
                ->trim()
                ->toString();

            if (str_contains($cargo, 'diretor') && ! str_contains($cargo, 'vice diretor')) return 1;
            if (str_contains($cargo, 'vice diretor')) return 2;
            if (str_contains($cargo, 'coordenador')) return 3;
            if (str_contains($cargo, 'superintendente')) return 4;
            if (str_contains($cargo, 'inspetor')) return 5;
            if (str_contains($cargo, 'agente especial')) return 6;
            if (str_contains($cargo, 'agente de 1')) return 7;
            if (str_contains($cargo, 'agente de 2')) return 8;
            if (str_contains($cargo, 'agente de 3')) return 9;
            if (str_contains($cargo, 'aluno')) return 10;

            return 99;
        })
        ->values();

    return view('publico.hierarquia', compact('hierarquiaPublica'));
})->name('publico.hierarquia');
/*
|--------------------------------------------------------------------------
| PÁGINAS PÚBLICAS (PORTAL)
|--------------------------------------------------------------------------
*/
Route::get('/legislacao', fn () => view('publico.legislacao'))->name('legislacao');
Route::get('/comunicados', fn () => view('publico.comunicados'))->name('comunicados');
Route::get('/governo-da-cidade', fn () => view('publico.governo'))->name('governo');
Route::get('/acesso-sistema', fn () => view('publico.acesso-sistema'))->name('acesso.sistema');

/*
|--------------------------------------------------------------------------
| ✅ JURÍDICO (PÚBLICO)
|--------------------------------------------------------------------------
*/
Route::get('/juridico', fn () => view('publico.juridico'))->name('juridico');

/*
|--------------------------------------------------------------------------
| ✅ RESULTADOS OPERACIONAIS — PÚBLICO (CIVIS)
|--------------------------------------------------------------------------
*/
Route::get('/resultados-operacionais', [ResultadosPublicosController::class, 'index'])
    ->name('resultados.publicos');

/*
|--------------------------------------------------------------------------
| ✅ CANAIS DE ATENDIMENTO (FORM DO MODAL) — PÚBLICO
|--------------------------------------------------------------------------
*/
Route::post('/atendimento/enviar', [AtendimentoController::class, 'enviar'])
    ->middleware('throttle:atendimento')
    ->name('atendimento.enviar');

/*
|--------------------------------------------------------------------------
| ✅ RECRUTAMENTO (COM CONTROLLER)
|--------------------------------------------------------------------------
*/
Route::get('/recrutamento', [RecrutamentoController::class, 'index'])->name('recrutamento');

Route::post('/recrutamento/pre-inscricao', [RecrutamentoController::class, 'store'])
    ->middleware('throttle:preinscricao')
    ->name('recrutamento.store');

Route::get('/cursos-prf', [RecrutamentoController::class, 'cursos'])->name('cursos.prf');

/*
|--------------------------------------------------------------------------
| ✅ ROTAS PRIVADAS (AUTENTICADO + USUÁRIO ATIVO)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'usuario.ativo'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PERFIL DO USUÁRIO (Breeze padrão + extras)
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::put('/profile/preferences', [ProfileController::class, 'updatePrefs'])->name('profile.prefs');

    /*
    |--------------------------------------------------------------------------
    | ✅ NOTIFICAÇÕES (Sininho) — marcar como lida
    |--------------------------------------------------------------------------
    */
    Route::post('/notificacoes/{id}/ler', function (string $id) {
        $n = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $n->markAsRead();
        return back();
    })->name('notifications.read');

    Route::post('/notificacoes/ler-todas', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read_all');

    /*
    |--------------------------------------------------------------------------
    | RANKING
    |--------------------------------------------------------------------------
    */
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

    Route::get('/ranking/resumo/{user}', [RankingController::class, 'resumo'])
        ->whereNumber('user')
        ->name('ranking.resumo');

    /*
    |--------------------------------------------------------------------------
    | ✅ RH — Recursos Humanos
    |--------------------------------------------------------------------------
    */
    Route::prefix('rh')->name('rh.')->group(function () {

        Route::get('/', [RhController::class, 'index'])->name('index');

        Route::get('/hierarquia', [RhHierarquiaController::class, 'index'])->name('hierarquia');

        Route::get('/hierarquia/lookup', [RhHierarquiaLookupController::class, 'lookup'])
            ->name('hierarquia.lookup');

        Route::get('/hierarquia/criar', [RhHierarquiaController::class, 'create'])
            ->middleware('rh.edit:hierarquia')
            ->name('hierarquia.create');

        Route::post('/hierarquia', [RhHierarquiaController::class, 'store'])
            ->middleware('rh.edit:hierarquia')
            ->name('hierarquia.store');

        Route::get('/hierarquia/{row}/editar', [RhHierarquiaController::class, 'edit'])
            ->middleware('rh.edit:hierarquia')
            ->whereNumber('row')
            ->name('hierarquia.edit');

        Route::put('/hierarquia/{row}', [RhHierarquiaController::class, 'update'])
            ->middleware('rh.edit:hierarquia')
            ->whereNumber('row')
            ->name('hierarquia.update');

        Route::delete('/hierarquia/{row}', [RhHierarquiaController::class, 'destroy'])
            ->middleware('rh.edit:hierarquia')
            ->whereNumber('row')
            ->name('hierarquia.destroy');

        Route::get('/controle-saida', [RhControleSaidaController::class, 'index'])
            ->name('controle_saida');

        Route::post('/controle-saida', [RhControleSaidaController::class, 'store'])
            ->middleware('rh.edit:controle_saida')
            ->name('controle_saida.store');

        Route::delete('/controle-saida/{row}', [RhControleSaidaController::class, 'destroy'])
            ->middleware('rh.edit:controle_saida')
            ->whereNumber('row')
            ->name('controle_saida.destroy');

        Route::get('/estatistica-efetivo', [RhController::class, 'estatisticaEfetivo'])->name('estatistica_efetivo');
        Route::get('/instrutores', [RhController::class, 'instrutores'])->name('instrutores');
        Route::get('/equipe', [RhController::class, 'equipe'])->name('equipe');

        Route::patch('/equipe/{user}/vincular', [RhController::class, 'vincularEquipe'])
            ->middleware('hierarquia:7')
            ->whereNumber('user')
            ->name('equipe.vincular');

        Route::get('/permissoes', [RhPermissionController::class, 'index'])
            ->middleware('hierarquia:9')
            ->name('permissions');

        Route::put('/permissoes/{user}', [RhPermissionController::class, 'update'])
            ->middleware('hierarquia:9')
            ->whereNumber('user')
            ->name('permissions.update');

        Route::post('/permissoes/{user}', [RhPermissionController::class, 'update'])
            ->middleware('hierarquia:9')
            ->whereNumber('user');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ REGULAMENTO — TODOS logados podem ver
    |--------------------------------------------------------------------------
    */
    Route::get('/regulamento', [RegulamentoController::class, 'index'])->name('regulamento.index');
    Route::get('/regulamento/instrucoes', [RegulamentoController::class, 'instrucoes'])->name('regulamento.instrucoes');
    Route::get('/regulamento/fardamento', [RegulamentoController::class, 'fardamento'])->name('regulamento.fardamento');

    /*
    |--------------------------------------------------------------------------
    | ✅ MANUAL INTERNO PELO BANCO
    |--------------------------------------------------------------------------
    */
    Route::get('/regulamento/interno', [GrrManualController::class, 'show'])
        ->name('regulamento.interno');

    /*
    |--------------------------------------------------------------------------
    | ✅ SUPORTE — TICKETS (QUALQUER NÍVEL LOGADO)
    |--------------------------------------------------------------------------
    */
    Route::prefix('suporte')->group(function () {

        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/criar', [TicketController::class, 'create'])->name('tickets.create');

        Route::post('/tickets', [TicketController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('tickets.store');

        Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');

        Route::post('/tickets/{ticket}/responder', [TicketController::class, 'reply'])
            ->middleware('throttle:20,1')
            ->name('tickets.reply');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ RELATÓRIOS — nível 2+ podem ver/iniciar turno/salvar rascunho/encerrar
    |--------------------------------------------------------------------------
    */
    Route::middleware(['hierarquia:2'])->group(function () {

        Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');

        Route::get('/relatorios/unidade-status', [RelatorioController::class, 'unidadeStatus'])
            ->middleware('throttle:60,1')
            ->name('relatorios.unidade_status');

        Route::post('/relatorios/iniciar-turno', [RelatorioController::class, 'iniciarTurno'])
            ->name('relatorios.iniciar_turno');

        Route::put('/relatorios/{relatorio}/rascunho', [RelatorioController::class, 'salvarRascunho'])
            ->name('relatorios.rascunho');

        Route::post('/relatorios/{relatorio}/encerrar-turno', [RelatorioController::class, 'encerrarTurno'])
            ->name('relatorios.encerrar_turno');

        Route::get('/relatorios/{relatorio}', [RelatorioController::class, 'show'])->name('relatorios.show');

        Route::get('/relatorios/criar', [RelatorioController::class, 'create'])->name('relatorios.create');
        Route::post('/relatorios', [RelatorioController::class, 'store'])->name('relatorios.store');
        Route::post('/relatorios/{relatorio}/finalizar', [RelatorioController::class, 'finalizar'])->name('relatorios.finalizar');

        Route::get('/usuarios/por-rg', [RelatorioController::class, 'buscarUsuarioPorRg'])
            ->middleware('throttle:30,1')
            ->name('usuarios.por_rg');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ APROVAR/REPROVAR + EDITAR/ATUALIZAR — Inspetor (nível 6+)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['hierarquia:6'])->group(function () {

        Route::post('/relatorios/{relatorio}/aprovar', [RelatorioController::class, 'aprovar'])->name('relatorios.aprovar');
        Route::post('/relatorios/{relatorio}/reprovar', [RelatorioController::class, 'reprovar'])->name('relatorios.reprovar');

        Route::get('/relatorios/{relatorio}/editar', [RelatorioController::class, 'edit'])->name('relatorios.edit');
        Route::put('/relatorios/{relatorio}', [RelatorioController::class, 'update'])->name('relatorios.update');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ AUDITORIA — NÍVEL 7+
    |--------------------------------------------------------------------------
    */
    Route::middleware(['hierarquia:7', 'auditar.acesso'])->group(function () {

        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');

        Route::post('/auditoria/revelar', [AuditoriaController::class, 'revelar'])
            ->middleware('throttle:6,1')
            ->name('auditoria.revelar');

        Route::post('/auditoria/travar', [AuditoriaController::class, 'travar'])->name('auditoria.travar');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ EFETIVO — VISUALIZAÇÃO (nível 6+)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['hierarquia:6'])->group(function () {
        Route::get('/efetivo', [EfetivoController::class, 'index'])->name('efetivo.index');

        Route::get('/efetivo/{user}', [EfetivoController::class, 'show'])
            ->whereNumber('user')
            ->name('efetivo.show');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ EFETIVO — PROMOÇÕES (nível 8+)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['hierarquia:8', 'auditar.acesso'])->group(function () {

        Route::post('/efetivo/{user}/promover', [EfetivoController::class, 'promover'])
            ->whereNumber('user')
            ->middleware('throttle:30,1')
            ->name('efetivo.promover');

        Route::post('/efetivo/promover-em-massa', [EfetivoController::class, 'promoverMassa'])
            ->middleware('throttle:10,1')
            ->name('efetivo.promover_massa');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ EFETIVO — AÇÕES ADMIN (somente nível 9+)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['hierarquia:9', 'auditar.acesso'])->group(function () {

        Route::get('/efetivo/criar', [EfetivoController::class, 'create'])->name('efetivo.create');
        Route::post('/efetivo', [EfetivoController::class, 'store'])->name('efetivo.store');

        Route::get('/efetivo/{user}/editar', [EfetivoController::class, 'edit'])
            ->whereNumber('user')
            ->name('efetivo.edit');

        Route::put('/efetivo/{user}', [EfetivoController::class, 'update'])
            ->whereNumber('user')
            ->name('efetivo.update');

        Route::post('/efetivo/{user}/suspender', [EfetivoController::class, 'suspender'])
            ->whereNumber('user')
            ->name('efetivo.suspender');

        Route::post('/efetivo/{user}/reativar', [EfetivoController::class, 'reativar'])
            ->whereNumber('user')
            ->name('efetivo.reativar');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ EFETIVO — REMOÇÃO DEFINITIVA (somente nível 10)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['hierarquia:10', 'auditar.acesso'])->group(function () {
        Route::delete('/efetivo/{user}', [EfetivoController::class, 'destroy'])
            ->whereNumber('user')
            ->name('efetivo.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ ATENDIMENTOS — (nível 7+)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['hierarquia:7', 'auditar.acesso'])->group(function () {

        Route::get('/atendimentos', [AtendimentoController::class, 'index'])->name('atendimentos.index');
        Route::get('/atendimentos/{atendimento}', [AtendimentoController::class, 'show'])->name('atendimentos.show');
        Route::patch('/atendimentos/{atendimento}/status', [AtendimentoController::class, 'updateStatus'])->name('atendimentos.status');
    });

    /*
    |--------------------------------------------------------------------------
    | ✅ ADMINISTRATIVO
    |--------------------------------------------------------------------------
    */
    Route::prefix('administrativo')->name('admin.')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | ✅ PRÉ-INSCRIÇÕES (nível 9+)
        |--------------------------------------------------------------------------
        */
        Route::middleware(['hierarquia:9'])->group(function () {

            Route::get('/pre-inscricoes', [PreInscricoesAdminController::class, 'index'])
                ->name('preinscricoes.index');

            Route::get('/pre-inscricoes/{preInscricao}', [PreInscricoesAdminController::class, 'show'])
                ->name('preinscricoes.show');

            Route::patch('/pre-inscricoes/{preInscricao}/status', [PreInscricoesAdminController::class, 'updateStatus'])
                ->name('preinscricoes.status');
        });

        /*
        |--------------------------------------------------------------------------
        | ✅ TICKETS ADMIN (nível 7+)
        |--------------------------------------------------------------------------
        */
        Route::middleware(['hierarquia:7'])->group(function () {

            Route::get('/tickets', [TicketAdminController::class, 'index'])
                ->name('tickets.index');

            Route::get('/tickets/{ticket}', [TicketAdminController::class, 'show'])
                ->name('tickets.show');

            Route::patch('/tickets/{ticket}/assumir', [TicketAdminController::class, 'assume'])
                ->middleware('throttle:20,1')
                ->name('tickets.assume');

            Route::patch('/tickets/{ticket}/status', [TicketAdminController::class, 'updateStatus'])
                ->middleware('throttle:20,1')
                ->name('tickets.status');

            Route::post('/tickets/{ticket}/responder', [TicketAdminController::class, 'reply'])
                ->middleware('throttle:30,1')
                ->name('tickets.reply');
        });

        /*
        |--------------------------------------------------------------------------
        | ✅ MANUAL INTERNO — EDIÇÃO ADMIN (nível 9+)
        |--------------------------------------------------------------------------
        */
        Route::middleware(['hierarquia:9', 'auditar.acesso'])->prefix('manual')->name('manual.')->group(function () {

            Route::get('/', [GrrManualController::class, 'edit'])->name('edit');
            Route::put('/', [GrrManualController::class, 'update'])->name('update');

            Route::post('/secoes', [GrrManualController::class, 'storeSection'])->name('sections.store');

            Route::put('/secoes/{section}', [GrrManualController::class, 'updateSection'])
                ->whereNumber('section')
                ->name('sections.update');

            Route::delete('/secoes/{section}', [GrrManualController::class, 'destroySection'])
                ->whereNumber('section')
                ->name('sections.destroy');

            Route::post('/secoes/{section}/artigos', [GrrManualController::class, 'storeArticle'])
                ->whereNumber('section')
                ->name('articles.store');

            Route::put('/artigos/{article}', [GrrManualController::class, 'updateArticle'])
                ->whereNumber('article')
                ->name('articles.update');

            Route::delete('/artigos/{article}', [GrrManualController::class, 'destroyArticle'])
                ->whereNumber('article')
                ->name('articles.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | ✅ SOLICITAÇÕES DE ACESSO
        |--------------------------------------------------------------------------
        | - index/show/aprovar/reprovar: nível 9+ OU liberados pelo controller
        | - edit/update/acessos.update: somente nível 9+
        |--------------------------------------------------------------------------
        */
        Route::prefix('solicitacoes-acesso')->name('solicitacoes.')->group(function () {

            Route::get('/', [SolicitacaoAcessoController::class, 'index'])
                ->middleware('auditar.acesso')
                ->name('index');

            Route::post('/acessos', [SolicitacaoAcessoController::class, 'updateAcessosGlobais'])
                ->middleware(['hierarquia:9', 'auditar.acesso', 'throttle:20,1'])
                ->name('acessos.update');

            Route::get('/{solicitacao}', [SolicitacaoAcessoController::class, 'show'])
                ->middleware('auditar.acesso')
                ->whereNumber('solicitacao')
                ->name('show');

            Route::get('/{solicitacao}/editar', [SolicitacaoAcessoController::class, 'edit'])
                ->middleware(['hierarquia:9', 'auditar.acesso'])
                ->whereNumber('solicitacao')
                ->name('edit');

            Route::put('/{solicitacao}', [SolicitacaoAcessoController::class, 'update'])
                ->middleware(['hierarquia:9', 'auditar.acesso', 'throttle:30,1'])
                ->whereNumber('solicitacao')
                ->name('update');

            Route::post('/{solicitacao}/aprovar', [SolicitacaoAcessoController::class, 'aprovar'])
                ->middleware(['auditar.acesso', 'throttle:30,1'])
                ->whereNumber('solicitacao')
                ->name('aprovar');

            Route::post('/{solicitacao}/reprovar', [SolicitacaoAcessoController::class, 'reprovar'])
                ->middleware(['auditar.acesso', 'throttle:30,1'])
                ->whereNumber('solicitacao')
                ->name('reprovar');
        });
    });
});

require __DIR__ . '/auth.php';