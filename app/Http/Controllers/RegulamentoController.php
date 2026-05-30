<?php

namespace App\Http\Controllers;

use App\Models\GrrManual;

class RegulamentoController extends Controller
{
    public function index()
    {
        return view('regulamento.index');
    }

    public function instrucoes()
    {
        return view('regulamento.instrucoes');
    }

    public function fardamento()
    {
        return view('regulamento.fardamento');
    }

    public function interno()
    {
        $manual = GrrManual::query()
            ->where('is_published', true)
            ->with([
                'sections' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
                'sections.articles' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                },
            ])
            ->orderByDesc('id')
            ->first();

        if (!$manual) {
            $manual = new GrrManual([
                'title'             => 'Manual Interno — Grupo de Resposta Rápida (GRR)',
                'slug'              => 'manual-interno-grr',
                'kicker'            => 'REGULAMENTO INTERNO • GRR',
                'subtitle'          => 'Normas, diretrizes e procedimentos internos aplicáveis ao efetivo do Grupo de Resposta Rápida.',
                'description'       => 'Documento institucional interno para consulta e padronização operacional.',
                'status_label'      => 'Ativo',
                'environment_label' => 'Brasil Capital Roleplay',
                'alert_title'       => 'Atenção',
                'alert_text'        => 'Este manual possui caráter interno. Todos os integrantes devem conhecer e cumprir integralmente suas disposições.',
                'is_published'      => true,
                'version'           => 1,
            ]);

            $manual->setRelation('sections', collect());
        }

        return view('regulamento.interno', compact('manual'));
    }
}