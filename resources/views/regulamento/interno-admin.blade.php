@extends('layouts.app')

@section('content')
@php
    $manualTitle = $manual->title ?: 'Manual Interno — Grupo de Resposta Rápida (GRR)';
@endphp

<div class="container py-4">
    <div class="manual-admin-wrap">

        <div class="manual-admin-head mb-4">
            <div>
                <div class="manual-admin-kicker">Painel administrativo</div>
                <h2 class="manual-admin-title mb-1">Editor do Manual Interno</h2>
                <div class="manual-admin-sub">
                    Gerencie os dados principais, capítulos e artigos do manual:
                    <strong>{{ $manualTitle }}</strong>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('regulamento.interno') }}" class="btn btn-outline-light">
                    ← Voltar ao manual
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm manual-alert-ok">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm manual-alert-error">
                <div class="fw-bold mb-2">Corrija os campos abaixo:</div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- DADOS PRINCIPAIS --}}
        <div class="manual-admin-card mb-4">
            <div class="manual-admin-card__head">
                <h4 class="mb-0">Dados principais do manual</h4>
            </div>

            <div class="manual-admin-card__body">
                <form method="POST" action="{{ route('admin.manual.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kicker</label>
                            <input type="text" name="kicker" class="form-control"
                                   value="{{ old('kicker', $manual->kicker) }}">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Título</label>
                            <input type="text" name="title" class="form-control"
                                   value="{{ old('title', $manual->title) }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Subtítulo</label>
                            <textarea name="subtitle" class="form-control" rows="3">{{ old('subtitle', $manual->subtitle) }}</textarea>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Label status</label>
                            <input type="text" name="status_label" class="form-control"
                                   value="{{ old('status_label', $manual->status_label) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Valor status</label>
                            <input type="text" name="status_value" class="form-control"
                                   value="{{ old('status_value', $manual->status_value) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Label ambiente</label>
                            <input type="text" name="environment_label" class="form-control"
                                   value="{{ old('environment_label', $manual->environment_label) }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Valor ambiente</label>
                            <input type="text" name="environment_value" class="form-control"
                                   value="{{ old('environment_value', $manual->environment_value) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Título do alerta</label>
                            <input type="text" name="alert_title" class="form-control"
                                   value="{{ old('alert_title', $manual->alert_title) }}">
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Texto do alerta</label>
                            <textarea name="alert_text" class="form-control" rows="2">{{ old('alert_text', $manual->alert_text) }}</textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Resumo 1 - Label</label>
                            <input type="text" name="summary_1_label" class="form-control"
                                   value="{{ old('summary_1_label', $manual->summary_1_label) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Resumo 1 - Valor</label>
                            <input type="text" name="summary_1_value" class="form-control"
                                   value="{{ old('summary_1_value', $manual->summary_1_value) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Resumo 1 - Texto</label>
                            <input type="text" name="summary_1_sub" class="form-control"
                                   value="{{ old('summary_1_sub', $manual->summary_1_sub) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Resumo 2 - Label</label>
                            <input type="text" name="summary_2_label" class="form-control"
                                   value="{{ old('summary_2_label', $manual->summary_2_label) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Resumo 2 - Valor</label>
                            <input type="text" name="summary_2_value" class="form-control"
                                   value="{{ old('summary_2_value', $manual->summary_2_value) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Resumo 2 - Texto</label>
                            <input type="text" name="summary_2_sub" class="form-control"
                                   value="{{ old('summary_2_sub', $manual->summary_2_sub) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Resumo 3 - Label</label>
                            <input type="text" name="summary_3_label" class="form-control"
                                   value="{{ old('summary_3_label', $manual->summary_3_label) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Resumo 3 - Valor</label>
                            <input type="text" name="summary_3_value" class="form-control"
                                   value="{{ old('summary_3_value', $manual->summary_3_value) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Resumo 3 - Texto</label>
                            <input type="text" name="summary_3_sub" class="form-control"
                                   value="{{ old('summary_3_sub', $manual->summary_3_sub) }}">
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            Salvar dados principais
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- NOVO CAPÍTULO --}}
        <div class="manual-admin-card mb-4">
            <div class="manual-admin-card__head">
                <h4 class="mb-0">Criar novo capítulo</h4>
            </div>

            <div class="manual-admin-card__body">
                <form method="POST" action="{{ route('admin.manual.sections.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Código</label>
                            <input type="text" name="code" class="form-control" placeholder="CAPÍTULO I">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Âncora</label>
                            <input type="text" name="anchor" class="form-control" placeholder="capitulo-1">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Título</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Ordem</label>
                            <input type="number" name="sort_order" class="form-control" min="1" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Subtítulo</label>
                            <input type="text" name="subtitle" class="form-control">
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">
                            Criar capítulo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- CAPÍTULOS --}}
        @foreach($manual->sections as $section)
            <div class="manual-admin-card mb-4">
                <div class="manual-admin-card__head d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <h4 class="mb-0">
                        {{ $section->code ?: 'Capítulo' }} — {{ $section->title }}
                    </h4>

                    <form method="POST" action="{{ route('admin.manual.sections.destroy', $section) }}"
                          onsubmit="return confirm('Deseja remover este capítulo e todos os artigos dele?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            Excluir capítulo
                        </button>
                    </form>
                </div>

                <div class="manual-admin-card__body">
                    {{-- EDITAR CAPÍTULO --}}
                    <form method="POST" action="{{ route('admin.manual.sections.update', $section) }}" class="mb-4">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Código</label>
                                <input type="text" name="code" class="form-control"
                                       value="{{ old('code', $section->code) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Âncora</label>
                                <input type="text" name="anchor" class="form-control"
                                       value="{{ old('anchor', $section->anchor) }}">
                            </div>

                            <div class="col-md-5">
                                <label class="form-label">Título</label>
                                <input type="text" name="title" class="form-control"
                                       value="{{ old('title', $section->title) }}" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Ordem</label>
                                <input type="number" name="sort_order" class="form-control"
                                       value="{{ old('sort_order', $section->sort_order) }}" min="1" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Subtítulo</label>
                                <input type="text" name="subtitle" class="form-control"
                                       value="{{ old('subtitle', $section->subtitle) }}">
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                Salvar capítulo
                            </button>
                        </div>
                    </form>

                    <hr class="manual-admin-sep">

                    {{-- NOVO ARTIGO --}}
                    <h5 class="mb-3 text-white">Adicionar artigo</h5>
                    <form method="POST" action="{{ route('admin.manual.articles.store', $section) }}" class="mb-4">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Número do artigo</label>
                                <input type="text" name="article_number" class="form-control" placeholder="Art. 1º">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Título interno</label>
                                <input type="text" name="title" class="form-control" placeholder="Opcional">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Ordem</label>
                                <input type="number" name="sort_order" class="form-control" min="1" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Texto</label>
                                <textarea name="body" rows="4" class="form-control" required></textarea>
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                Criar artigo
                            </button>
                        </div>
                    </form>

                    {{-- LISTA DE ARTIGOS --}}
                    @foreach($section->articles as $article)
                        <div class="manual-article-editor mb-3">
                            <form method="POST" action="{{ route('admin.manual.articles.update', $article) }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Número</label>
                                        <input type="text" name="article_number" class="form-control"
                                               value="{{ old('article_number', $article->article_number) }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Título</label>
                                        <input type="text" name="title" class="form-control"
                                               value="{{ old('title', $article->title) }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Ordem</label>
                                        <input type="number" name="sort_order" class="form-control"
                                               value="{{ old('sort_order', $article->sort_order) }}" min="1" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Texto</label>
                                        <textarea name="body" rows="5" class="form-control" required>{{ old('body', $article->body) }}</textarea>
                                    </div>
                                </div>

                                <div class="mt-3 d-flex justify-content-between gap-2 flex-wrap">
                                    <button type="submit" class="btn btn-primary">
                                        Salvar artigo
                                    </button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('admin.manual.articles.destroy', $article) }}"
                                  onsubmit="return confirm('Deseja remover este artigo?')" class="mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    Excluir artigo
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

    </div>
</div>

<style>
    .manual-admin-wrap{
        max-width: 1200px;
        margin: 0 auto;
    }

    .manual-admin-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:16px;
        flex-wrap:wrap;
    }

    .manual-admin-kicker{
        font-size:12px;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.14em;
        color:rgba(148,163,184,.92);
        margin-bottom:6px;
    }

    .manual-admin-title{
        color:#fff;
        font-weight:900;
    }

    .manual-admin-sub{
        color:rgba(226,232,240,.76);
        font-weight:700;
    }

    .manual-admin-card{
        background: linear-gradient(180deg, rgba(8,12,19,.95), rgba(11,18,32,.98));
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 22px;
        box-shadow: 0 18px 45px rgba(0,0,0,.28);
        overflow: hidden;
    }

    .manual-admin-card__head{
        padding: 18px 20px;
        border-bottom: 1px solid rgba(255,255,255,.08);
        background: rgba(255,255,255,.02);
        color: #fff;
    }

    .manual-admin-card__body{
        padding: 20px;
    }

    .manual-admin-sep{
        border-color: rgba(255,255,255,.08);
        margin: 28px 0;
    }

    .manual-article-editor{
        padding: 16px;
        border-radius: 16px;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.06);
    }

    .form-label{
        color: rgba(226,232,240,.88);
        font-weight: 800;
        font-size: 13px;
    }

    .form-control{
        background: rgba(15,23,42,.72) !important;
        border: 1px solid rgba(255,255,255,.10) !important;
        color: #fff !important;
    }

    .form-control:focus{
        box-shadow: 0 0 0 .2rem rgba(59,130,246,.20) !important;
        border-color: rgba(96,165,250,.40) !important;
    }

    .manual-alert-ok{
        background: rgba(34,197,94,.18);
        color: #dcfce7;
    }

    .manual-alert-error{
        background: rgba(239,68,68,.14);
        color: #fee2e2;
    }

    @media (max-width: 576px){
        .manual-admin-card__body,
        .manual-admin-card__head{
            padding: 14px;
        }
    }
</style>
@endsection