<?php

namespace App\Observers;

use App\Models\User;
use App\Models\RhHierarquiaRecord;

class UserObserver
{
    /**
     * Quando o usuário é CRIADO
     */
    public function created(User $user): void
    {
        $this->syncHierarquia($user);
    }

    /**
     * Quando o usuário é ATUALIZADO
     */
    public function updated(User $user): void
    {
        // Só sincroniza se mudou algo relevante do efetivo
        if ($user->wasChanged(['name', 'rg', 'cargo', 'status', 'avatar_path'])) {
            $this->syncHierarquia($user);
        }
    }

    /**
     * Sincroniza dados automáticos na hierarquia
     * - Automáticos: nome, rg(campo cpf no seu banco atual), cargo, status, user_id/updated_by
     * - Não mexe nos campos manuais (equipe, medalhas, alinhamento, qualificações etc.)
     */
    private function syncHierarquia(User $user): void
    {
        // Sem RG não cria hierarquia (evita “registros zumbis”)
        $rg = trim((string) ($user->rg ?? ''));
        if ($rg === '') return;

        // Mapeia status do EFETIVO -> status da HIERARQUIA
        // (ajuste se você quiser outro comportamento)
        $statusHierarquia = match ((string) ($user->status ?? 'ativo')) {
            'ativo'     => 'em_exercicio',
            'suspenso'  => 'em_licenca',
            'desligado' => 'desligado',
            default     => 'em_exercicio',
        };

        RhHierarquiaRecord::updateOrCreate(
            ['user_id' => $user->id],
            [
                // 🔒 AUTOMÁTICOS (sempre sincronizados)
                'nome'  => (string) $user->name,
                'cpf'   => $rg,                 // aqui você usa o campo "cpf" como RG (mantendo seu banco atual)
                'cargo' => (string) ($user->cargo ?? ''),
                'status'=> $statusHierarquia,

                // auditoria/vínculo
                'updated_by' => $user->id,
            ]
        );
    }
}
