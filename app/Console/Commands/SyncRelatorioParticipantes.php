<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Relatorio;

class SyncRelatorioParticipantes extends Command
{
    /**
     * Use:
     * php artisan relatorios:sync-participantes
     * php artisan relatorios:sync-participantes --only=aprovados
     * php artisan relatorios:sync-participantes --only=pendentes
     * php artisan relatorios:sync-participantes --only=reprovados
     * php artisan relatorios:sync-participantes --from=1000
     */
    protected $signature = 'relatorios:sync-participantes
                            {--only= : Filtra por status (aprovados|pendentes|reprovados)}
                            {--from= : ID inicial para processar (ex: 1000)}';

    protected $description = 'Sincroniza participantes (CHEFE/P2/P3/P4) de relatórios antigos para a tabela relatorio_participantes.';

    public function handle(): int
    {
        $only = $this->option('only');
        $from = $this->option('from');

        $query = Relatorio::query()->orderBy('id');

        if (!empty($from)) {
            $query->where('id', '>=', (int) $from);
        }

        if ($only) {
            $only = strtolower(trim($only));

            $status = match ($only) {
                'aprovados', 'aprovado' => 'aprovado',
                'pendentes', 'pendente' => 'pendente',
                'reprovados', 'reprovado' => 'reprovado',
                default => null,
            };

            if (!$status) {
                $this->error("Opção --only inválida. Use: aprovados | pendentes | reprovados");
                return self::FAILURE;
            }

            $query->where('status', $status);
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('Nenhum relatório encontrado para sincronizar.');
            return self::SUCCESS;
        }

        $this->info("Sincronizando {$total} relatório(s)...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $ok = 0;
        $skipped = 0;
        $errors = 0;

        $query->chunkById(200, function ($relatorios) use (&$ok, &$skipped, &$errors, $bar) {
            foreach ($relatorios as $relatorio) {
                try {
                    // Se não tiver motorista e nem P2/P3/P4, pula
                    $hasAny = !empty($relatorio->motorista)
                        || !empty($relatorio->terceiro)
                        || !empty($relatorio->quarto)
                        || !empty($relatorio->quinto);

                    if (!$hasAny) {
                        $skipped++;
                        $bar->advance();
                        continue;
                    }

                    $relatorio->syncParticipantesPorRG();
                    $ok++;
                } catch (\Throwable $e) {
                    $errors++;
                    // Mostra erro, mas continua
                    $this->newLine();
                    $this->warn("Erro no relatório #{$relatorio->id}: " . $e->getMessage());
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Concluído ✅  OK: {$ok} | Pulados: {$skipped} | Erros: {$errors}");

        return self::SUCCESS;
    }
}
