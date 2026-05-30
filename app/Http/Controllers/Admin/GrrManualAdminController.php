<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrrManual;
use App\Models\GrrManualArticle;
use App\Models\GrrManualSection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GrrManualAdminController extends Controller
{
    public function index()
    {
        $manual = GrrManual::query()
            ->with([
                'sections' => fn ($q) => $q->orderBy('sort_order'),
                'sections.articles' => fn ($q) => $q->orderBy('sort_order'),
            ])
            ->orderByDesc('id')
            ->first();

        if (!$manual) {
            $manual = GrrManual::create([
                'title'             => 'Manual Interno — Grupo de Resposta Rápida (GRR)',
                'slug'              => 'manual-interno-grr',
                'kicker'            => 'REGULAMENTO INTERNO • GRR',
                'subtitle'          => 'Normas, diretrizes e procedimentos internos aplicáveis ao efetivo do Grupo de Resposta Rápida.',
                'description'       => 'Documento institucional interno.',
                'status_label'      => 'Ativo',
                'environment_label' => 'Brasil Capital Roleplay',
                'alert_title'       => 'Atenção',
                'alert_text'        => 'Este manual possui caráter interno.',
                'is_published'      => true,
                'version'           => 1,
            ]);

            $manual->load([
                'sections' => fn ($q) => $q->orderBy('sort_order'),
                'sections.articles' => fn ($q) => $q->orderBy('sort_order'),
            ]);
        }

        return view('admin.interno', compact('manual'));
    }

    public function update(Request $request)
    {
        $manual = GrrManual::query()->orderByDesc('id')->firstOrFail();

        $data = $request->validate([
            'kicker'            => ['nullable', 'string', 'max:255'],
            'title'             => ['required', 'string', 'max:255'],
            'subtitle'          => ['nullable', 'string'],
            'status_label'      => ['nullable', 'string', 'max:255'],
            'status_value'      => ['nullable', 'string', 'max:255'],
            'environment_label' => ['nullable', 'string', 'max:255'],
            'environment_value' => ['nullable', 'string', 'max:255'],
            'alert_title'       => ['nullable', 'string', 'max:255'],
            'alert_text'        => ['nullable', 'string'],
            'summary_1_label'   => ['nullable', 'string', 'max:255'],
            'summary_1_value'   => ['nullable', 'string', 'max:255'],
            'summary_1_sub'     => ['nullable', 'string', 'max:255'],
            'summary_2_label'   => ['nullable', 'string', 'max:255'],
            'summary_2_value'   => ['nullable', 'string', 'max:255'],
            'summary_2_sub'     => ['nullable', 'string', 'max:255'],
            'summary_3_label'   => ['nullable', 'string', 'max:255'],
            'summary_3_value'   => ['nullable', 'string', 'max:255'],
            'summary_3_sub'     => ['nullable', 'string', 'max:255'],
        ]);

        $manual->fill($data);
        $manual->slug = Str::slug($manual->title ?: 'manual-interno-grr');
        $manual->save();

        return back()->with('success', 'Dados principais do manual atualizados com sucesso.');
    }

    public function storeSection(Request $request)
    {
        $manual = GrrManual::query()->orderByDesc('id')->firstOrFail();

        $data = $request->validate([
            'code'       => ['nullable', 'string', 'max:255'],
            'anchor'     => ['nullable', 'string', 'max:255'],
            'title'      => ['required', 'string', 'max:255'],
            'subtitle'   => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        GrrManualSection::create([
            'manual_id'   => $manual->id,
            'code'        => $data['code'] ?? null,
            'anchor'      => filled($data['anchor'] ?? null) ? $data['anchor'] : Str::slug($data['title']),
            'title'       => $data['title'],
            'subtitle'    => $data['subtitle'] ?? null,
            'sort_order'  => $data['sort_order'],
            'is_active'   => true,
        ]);

        return back()->with('success', 'Capítulo criado com sucesso.');
    }

    public function updateSection(Request $request, GrrManualSection $section)
    {
        $data = $request->validate([
            'code'       => ['nullable', 'string', 'max:255'],
            'anchor'     => ['nullable', 'string', 'max:255'],
            'title'      => ['required', 'string', 'max:255'],
            'subtitle'   => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ]);

        $section->update([
            'code'       => $data['code'] ?? null,
            'anchor'     => filled($data['anchor'] ?? null) ? $data['anchor'] : Str::slug($data['title']),
            'title'      => $data['title'],
            'subtitle'   => $data['subtitle'] ?? null,
            'sort_order' => $data['sort_order'],
            'is_active'  => true,
        ]);

        return back()->with('success', 'Capítulo atualizado com sucesso.');
    }

    public function destroySection(GrrManualSection $section)
    {
        $section->articles()->delete();
        $section->delete();

        return back()->with('success', 'Capítulo removido com sucesso.');
    }

    public function storeArticle(Request $request, GrrManualSection $section)
    {
        $data = $request->validate([
            'article_number' => ['nullable', 'string', 'max:255'],
            'title'          => ['nullable', 'string', 'max:255'],
            'body'           => ['required', 'string'],
            'sort_order'     => ['required', 'integer', 'min:1'],
        ]);

        GrrManualArticle::create([
            'section_id'      => $section->id,
            'article_number'  => $data['article_number'] ?? null,
            'title'           => $data['title'] ?? null,
            'body'            => $data['body'],
            'sort_order'      => $data['sort_order'],
            'is_active'       => true,
        ]);

        return back()->with('success', 'Artigo criado com sucesso.');
    }

    public function updateArticle(Request $request, GrrManualArticle $article)
    {
        $data = $request->validate([
            'article_number' => ['nullable', 'string', 'max:255'],
            'title'          => ['nullable', 'string', 'max:255'],
            'body'           => ['required', 'string'],
            'sort_order'     => ['required', 'integer', 'min:1'],
        ]);

        $article->update([
            'article_number' => $data['article_number'] ?? null,
            'title'          => $data['title'] ?? null,
            'body'           => $data['body'],
            'sort_order'     => $data['sort_order'],
            'is_active'      => true,
        ]);

        return back()->with('success', 'Artigo atualizado com sucesso.');
    }

    public function destroyArticle(GrrManualArticle $article)
    {
        $article->delete();

        return back()->with('success', 'Artigo removido com sucesso.');
    }
}