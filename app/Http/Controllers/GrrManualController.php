<?php

namespace App\Http\Controllers;

use App\Models\GrrManual;
use App\Models\GrrManualArticle;
use App\Models\GrrManualSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GrrManualController extends Controller
{
    private const MANUAL_SLUG = 'manual-interno-grr';

    private function ensureAdmin(): void
    {
        abort_unless(auth()->check() && (int) (auth()->user()->nivel ?? 0) >= 9, 403);
    }

    private function getDefaultManualData(): array
    {
        return [
            'title'             => 'Manual Interno — Grupo de Resposta Rápida (GRR)',
            'slug'              => self::MANUAL_SLUG,
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
        ];
    }

    private function getBaseManual(): GrrManual
    {
        $manual = GrrManual::where('slug', self::MANUAL_SLUG)->first();

        if (!$manual) {
            $manual = GrrManual::first();
        }

        if (!$manual) {
            $manual = GrrManual::create($this->getDefaultManualData());
        } elseif (blank($manual->slug)) {
            $manual->slug = self::MANUAL_SLUG;
            $manual->save();
        }

        return $manual;
    }

    private function getPublicManual(): GrrManual
    {
        $manual = GrrManual::with([
            'sections' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            },
            'sections.articles' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            },
        ])->where('slug', self::MANUAL_SLUG)->first();

        if (!$manual) {
            $baseManual = $this->getBaseManual();

            $manual = GrrManual::with([
                'sections' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
                'sections.articles' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
            ])->find($baseManual->id);
        }

        return $manual;
    }

    private function getAdminManual(): GrrManual
    {
        $manual = GrrManual::with([
            'sections' => function ($query) {
                $query->orderBy('sort_order');
            },
            'sections.articles' => function ($query) {
                $query->orderBy('sort_order');
            },
        ])->where('slug', self::MANUAL_SLUG)->first();

        if (!$manual) {
            $baseManual = $this->getBaseManual();

            $manual = GrrManual::with([
                'sections' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'sections.articles' => function ($query) {
                    $query->orderBy('sort_order');
                },
            ])->find($baseManual->id);
        }

        return $manual;
    }

    public function show(): View
    {
        $manual = $this->getPublicManual();

        return view('regulamento.interno', compact('manual'));
    }

    public function edit(): View
    {
        $this->ensureAdmin();

        $manual = $this->getAdminManual();

        return view('regulamento.interno-admin', compact('manual'));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $manual = $this->getBaseManual();

        $data = $request->validate([
            'kicker'             => 'nullable|string|max:255',
            'title'              => 'required|string|max:255',
            'subtitle'           => 'nullable|string',
            'status_label'       => 'nullable|string|max:255',
            'status_value'       => 'nullable|string|max:255',
            'environment_label'  => 'nullable|string|max:255',
            'environment_value'  => 'nullable|string|max:255',
            'alert_title'        => 'nullable|string|max:255',
            'alert_text'         => 'nullable|string',
            'summary_1_label'    => 'nullable|string|max:255',
            'summary_1_value'    => 'nullable|string|max:255',
            'summary_1_sub'      => 'nullable|string',
            'summary_2_label'    => 'nullable|string|max:255',
            'summary_2_value'    => 'nullable|string|max:255',
            'summary_2_sub'      => 'nullable|string',
            'summary_3_label'    => 'nullable|string|max:255',
            'summary_3_value'    => 'nullable|string|max:255',
            'summary_3_sub'      => 'nullable|string',
        ]);

        if (filled($data['title'] ?? null)) {
            $data['slug'] = Str::slug($data['title']);
        }

        $manual->update($data);

        return back()->with('success', 'Dados principais do manual atualizados com sucesso.');
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $manual = $this->getBaseManual();

        $data = $request->validate([
            'code'       => 'nullable|string|max:50',
            'anchor'     => 'nullable|string|max:100',
            'title'      => 'required|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:1',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['anchor'] = filled($data['anchor'] ?? null)
            ? Str::slug($data['anchor'])
            : Str::slug($data['title']);

        $data['is_active'] = $request->boolean('is_active', true);

        $manual->sections()->create($data);

        return back()->with('success', 'Capítulo criado com sucesso.');
    }

    public function updateSection(Request $request, GrrManualSection $section): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'code'       => 'nullable|string|max:50',
            'anchor'     => 'nullable|string|max:100',
            'title'      => 'required|string|max:255',
            'subtitle'   => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:1',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['anchor'] = filled($data['anchor'] ?? null)
            ? Str::slug($data['anchor'])
            : Str::slug($data['title']);

        $data['is_active'] = $request->boolean('is_active', true);

        $section->update($data);

        return back()->with('success', 'Capítulo atualizado com sucesso.');
    }

    public function destroySection(GrrManualSection $section): RedirectResponse
    {
        $this->ensureAdmin();

        $section->articles()->delete();
        $section->delete();

        return back()->with('success', 'Capítulo removido com sucesso.');
    }

    public function storeArticle(Request $request, GrrManualSection $section): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'article_number' => 'required|string|max:50',
            'title'          => 'nullable|string|max:255',
            'body'           => 'required|string',
            'sort_order'     => 'required|integer|min:1',
            'is_active'      => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $section->articles()->create($data);

        return back()->with('success', 'Artigo criado com sucesso.');
    }

    public function updateArticle(Request $request, GrrManualArticle $article): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'article_number' => 'required|string|max:50',
            'title'          => 'nullable|string|max:255',
            'body'           => 'required|string',
            'sort_order'     => 'required|integer|min:1',
            'is_active'      => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $article->update($data);

        return back()->with('success', 'Artigo atualizado com sucesso.');
    }

    public function destroyArticle(GrrManualArticle $article): RedirectResponse
    {
        $this->ensureAdmin();

        $article->delete();

        return back()->with('success', 'Artigo removido com sucesso.');
    }
}