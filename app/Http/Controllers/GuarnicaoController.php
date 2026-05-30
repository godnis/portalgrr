<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuarnicaoController extends Controller
{
    public function index()
    {
        return view('guarnicao.index');
    }

    public function create()
    {
        return view('guarnicao.create');
    }

    public function store(Request $request)
    {
        // Por enquanto: placeholder (você vai ligar com o banco depois)
        return redirect()->route('guarnicao.index')->with('success', 'Registro criado (placeholder).');
    }

    public function show(string $id)
    {
        return view('guarnicao.show', compact('id'));
    }

    public function edit(string $id)
    {
        return view('guarnicao.edit', compact('id'));
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('guarnicao.index')->with('success', 'Registro atualizado (placeholder).');
    }

    public function destroy(string $id)
    {
        return redirect()->route('guarnicao.index')->with('success', 'Registro removido (placeholder).');
    }
}
