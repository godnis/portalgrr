<?php

namespace App\Http\Controllers;

use App\Models\RhHierarquiaRecord;
use Illuminate\Http\Request;
use App\Support\Rh;
use App\Services\AuditoriaLogger;
use Illuminate\Support\Str;

class RhHierarquiaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTAGEM (SEM PAGINAÇÃO - TUDO EM 1 PÁGINA)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $q      = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));
        $equipe = trim((string) $request->get('equipe', ''));

        $query = RhHierarquiaRecord::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nome', 'like', "%{$q}%")
                        ->orWhere('cpf', 'like', "%{$q}%")
                        ->orWhere('cargo', 'like', "%{$q}%")
                        ->orWhere('serial', 'like', "%{$q}%")
                        ->orWhere('discord_id', 'like', "%{$q}%")
                        ->orWhere('funcao_obs', 'like', "%{$q}%")
                        ->orWhere('medalhas', 'like', "%{$q}%")
                        ->orWhere('alinhamento', 'like', "%{$q}%");
                });
            })
            ->when($equipe !== '', fn ($query) => $query->where('equipe', $equipe));

        // ✅ REGRA NOVA:
        // Se não filtrou status, esconde desligados da hierarquia.
        // Se filtrou status, respeita o filtro.
        if ($status !== '') {
            $query->where('status', $status);
        } else {
            $query->where(function ($sub) {
                $sub->whereNull('status')
                    ->orWhere('status', '!=', 'desligado');
            });
        }

        $rows = $query->get();

        // ✅ define rank + bloco e já ordena (maior -> menor, empate por nome)
        $rows = $rows->map(function ($r) {
            $r->cargo_rank  = $this->cargoRank((string) ($r->cargo ?? ''));
            $r->cargo_bloco = $this->cargoBloco((string) ($r->cargo ?? ''));
            return $r;
        });

        // ⚠️ IMPORTANTE: use um sort ÚNICO (sortByDesc + sortBy quebra a ordem)
        $sorter = function ($a, $b) {
            $ra = (int) ($a->cargo_rank ?? 0);
            $rb = (int) ($b->cargo_rank ?? 0);

            if ($ra === $rb) {
                return strcmp((string) $a->nome, (string) $b->nome);
            }

            return $rb <=> $ra; // maior primeiro
        };

        $diretoria = $rows->filter(fn ($r) => $r->cargo_bloco === 'diretoria')
            ->sort($sorter)
            ->values();

        $oficiais = $rows->filter(fn ($r) => $r->cargo_bloco === 'oficial')
            ->sort($sorter)
            ->values();

        $outros = $rows->filter(fn ($r) => $r->cargo_bloco === 'outros')
            ->sort($sorter)
            ->values();

        $canEdit = Rh::canEdit(auth()->user(), 'hierarquia');

        return view('rh.hierarquia', compact(
            'diretoria',
            'oficiais',
            'outros',
            'q',
            'status',
            'equipe',
            'canEdit'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | FORM CRIAR
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        abort_unless(Rh::canEdit(auth()->user(), 'hierarquia'), 403);

        return view('rh.hierarquia_form', [
            'row' => new RhHierarquiaRecord()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SALVAR NOVO
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        abort_unless(Rh::canEdit(auth()->user(), 'hierarquia'), 403);

        $data = $this->validateData($request);
        $data['updated_by'] = auth()->id();

        $row = RhHierarquiaRecord::create($data);

        if (class_exists(AuditoriaLogger::class)) {
            AuditoriaLogger::log(
                'rh_hierarquia_criada',
                auth()->id(),
                [
                    'id'         => $row->id,
                    'nome'       => $row->nome,
                    'cpf'        => $row->cpf,
                    'cargo'      => $row->cargo,
                    'equipe'     => $row->equipe,
                    'serial'     => $row->serial,
                    'discord_id' => $row->discord_id,
                ]
            );
        }

        return redirect()
            ->route('rh.hierarquia')
            ->with('success', 'Registro criado com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | FORM EDITAR
    |--------------------------------------------------------------------------
    */
    public function edit(RhHierarquiaRecord $row)
    {
        abort_unless(Rh::canEdit(auth()->user(), 'hierarquia'), 403);

        return view('rh.hierarquia_form', compact('row'));
    }

    /*
    |--------------------------------------------------------------------------
    | ATUALIZAR
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, RhHierarquiaRecord $row)
    {
        abort_unless(Rh::canEdit(auth()->user(), 'hierarquia'), 403);

        $before = $row->toArray();

        $data = $this->validateData($request);
        $data['updated_by'] = auth()->id();

        $row->update($data);

        if (class_exists(AuditoriaLogger::class)) {
            AuditoriaLogger::log(
                'rh_hierarquia_atualizada',
                auth()->id(),
                [
                    'id'         => $row->id,
                    'nome'       => $row->nome,
                    'cpf'        => $row->cpf,
                    'cargo'      => $row->cargo,
                    'equipe'     => $row->equipe,
                    'serial'     => $row->serial,
                    'discord_id' => $row->discord_id,
                    'before'     => $before,
                    'after'      => $row->toArray(),
                ]
            );
        }

        return redirect()
            ->route('rh.hierarquia')
            ->with('success', 'Registro atualizado com sucesso.');
    }

    /*
    |--------------------------------------------------------------------------
    | REMOVER
    |--------------------------------------------------------------------------
    */
    public function destroy(RhHierarquiaRecord $row)
    {
        abort_unless(Rh::canEdit(auth()->user(), 'hierarquia'), 403);

        $payload = [
            'id'          => $row->id,
            'nome'        => $row->nome,
            'cpf'         => $row->cpf,
            'cargo'       => $row->cargo,
            'equipe'      => $row->equipe,
            'status'      => $row->status,
            'serial'      => $row->serial,
            'discord_id'  => $row->discord_id,
            'funcao_obs'  => $row->funcao_obs,
            'medalhas'    => $row->medalhas,
            'alinhamento' => $row->alinhamento,
        ];

        $row->delete();

        if (class_exists(AuditoriaLogger::class)) {
            AuditoriaLogger::log(
                'rh_hierarquia_removida',
                auth()->id(),
                $payload
            );
        }

        return redirect()
            ->route('rh.hierarquia')
            ->with('success', 'Registro removido.');
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÃO
    |--------------------------------------------------------------------------
    */
    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'user_id' => ['nullable', 'integer'],
            'cpf'     => ['nullable', 'string', 'max:32'],
            'nome'    => ['required', 'string', 'max:140'],
            'equipe'  => ['nullable', 'string', 'max:30'],
            'cargo'   => ['nullable', 'string', 'max:80'],

            'efetivacao' => ['nullable', 'string', 'max:30'],
            'status'     => ['nullable', 'string', 'max:40'],

            'admissao'        => ['nullable', 'date'],
            'ultima_promocao' => ['nullable', 'date'],

            'serial'     => ['nullable', 'string', 'max:40'],
            'discord_id' => ['nullable', 'string', 'max:40'],
            'funcao_obs' => ['nullable', 'string', 'max:220'],

            'instrutor' => ['nullable', 'boolean'],

            'pop'  => ['nullable', 'boolean'],
            'clt'  => ['nullable', 'boolean'],
            'cap'  => ['nullable', 'boolean'],
            'ctb'  => ['nullable', 'boolean'],
            'cta'  => ['nullable', 'boolean'],
            'satb' => ['nullable', 'boolean'],
            'bopm' => ['nullable', 'boolean'],
            'gmp'  => ['nullable', 'boolean'],
            'doa'  => ['nullable', 'boolean'],

            'medalhas'    => ['nullable', 'string', 'max:200'],
            'alinhamento' => ['nullable', 'string', 'max:200'],
        ]);

        foreach (['instrutor','pop','clt','cap','ctb','cta','satb','bopm','gmp','doa'] as $k) {
            $data[$k] = (bool) ($request->input($k, false));
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | REGRAS DE BLOCO E ORDEM (TOP = MAIOR)
    |--------------------------------------------------------------------------
    */

    private function cargoBloco(string $cargo): string
    {
        $c = $this->normalizeCargo($cargo);

        // ✅ Diretoria (inclui Superintendente)
        if (
            $this->has($c, 'diretor') ||
            $this->has($c, 'vice diretor') ||
            $this->has($c, 'coordenador') ||
            $this->has($c, 'superintendente') ||
            $this->has($c, 'inspetor')
        ) {
            return 'diretoria';
        }

        // ✅ Oficiais (inclui Agente Especial)
        if (
            $this->has($c, 'agente especial') ||
            $this->has($c, 'agente de 1') ||
            $this->has($c, 'agente de 2') ||
            $this->has($c, 'agente de 3') ||
            $this->has($c, 'aluno')
        ) {
            return 'oficial';
        }

        return 'outros';
    }

    private function cargoRank(string $cargo): int
    {
        $c = $this->normalizeCargo($cargo);

        // ✅ Ordem exatamente como você passou (maior -> menor)
        if ($this->has($c, 'diretor') && !$this->has($c, 'vice diretor')) return 1000;
        if ($this->has($c, 'vice diretor')) return 900;
        if ($this->has($c, 'coordenador')) return 800;
        if ($this->has($c, 'superintendente')) return 700;
        if ($this->has($c, 'inspetor')) return 600;

        if ($this->has($c, 'agente especial')) return 500;
        if ($this->has($c, 'agente de 1')) return 400;
        if ($this->has($c, 'agente de 2')) return 300;
        if ($this->has($c, 'agente de 3')) return 200;
        if ($this->has($c, 'aluno')) return 100;

        return 0;
    }

    private function normalizeCargo(string $cargo): string
    {
        $c = trim($cargo);

        $c = Str::of($c)->lower()->ascii()->__toString();
        $c = str_replace(['º', '°', 'ª'], '', $c);
        $c = str_replace(['-', '_', '.'], ' ', $c);
        $c = preg_replace('/\s+/', ' ', $c);

        $c = str_replace('direto', 'diretor', $c);
        $c = str_replace('vice-diretor', 'vice diretor', $c);

        return trim($c);
    }

    private function has(string $haystack, string $needle): bool
    {
        return str_contains($haystack, $needle);
    }
}