<?php

namespace App\Policies;

use App\Models\Relatorio;
use App\Models\User;

class RelatorioPolicy
{
    private function normRg($v): ?string
    {
        $rg = preg_replace('/\D+/', '', (string)($v ?? ''));
        return $rg !== '' ? $rg : null;
    }

    private function userIsInGuarnicao(User $user, Relatorio $relatorio): bool
    {
        $uRg = $this->normRg($user->rg ?? null);
        if (!$uRg) return false;

        $rgs = [
            $this->normRg($relatorio->qra_chefe ?? null),
            $this->normRg($relatorio->motorista ?? null),
            $this->normRg($relatorio->terceiro ?? null),
            $this->normRg($relatorio->quarto ?? null),
            $this->normRg($relatorio->quinto ?? null),
        ];

        $rgs = array_filter($rgs);
        return in_array($uRg, $rgs, true);
    }

    /**
     * Decide = aprovar/reprovar
     */
    public function decide(User $user, Relatorio $relatorio): bool
    {
        $nivel = (int)($user->nivel ?? 0);

        // ✅ Somente o Diretor nível 10 pode decidir mesmo estando na guarnição
        if ($nivel >= 10) {
            return true;
        }

        // ✅ Regra normal: precisa ser 6+ pra decidir
        if ($nivel < 6) {
            return false;
        }

        // ✅ Bloqueio: quem está na guarnição NÃO pode decidir
        if ($this->userIsInGuarnicao($user, $relatorio)) {
            return false;
        }

        return true;
    }
}