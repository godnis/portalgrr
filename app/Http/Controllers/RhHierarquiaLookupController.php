<?php

namespace App\Http\Controllers;

use App\Models\RhHierarquiaRecord;
use Illuminate\Http\Request;

class RhHierarquiaLookupController extends Controller
{
    public function lookup(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Informe um termo de busca.',
            ], 422);
        }

        // procura por CPF, nome, serial ou discord_id
        $row = RhHierarquiaRecord::query()
            ->where('cpf', $q)
            ->orWhere('serial', $q)
            ->orWhere('discord_id', $q)
            ->orWhere('nome', 'like', "%{$q}%")
            ->first();

        if (!$row) {
            return response()->json([
                'ok' => false,
                'message' => 'Militar não encontrado na Hierarquia.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'data' => [
                'nome'           => $row->nome,
                'cpf'            => $row->cpf,
                'cargo'          => $row->cargo,
                'efetivacao'     => $row->efetivacao,
                'status'         => $row->status,
                'admissao'       => optional($row->admissao)->format('Y-m-d'),
                'ultima_promocao'=> optional($row->ultima_promocao)->format('Y-m-d'),
                'serial'         => $row->serial,
                'discord_id'     => $row->discord_id,
            ],
        ]);
    }
}
