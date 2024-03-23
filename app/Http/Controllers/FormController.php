<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;

class FormController extends Controller
{
    // Endpoint para criar um novo formulário
    public function create(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'info_style' => 'nullable|string',
            'url' => 'nullable|string',
            // Adicione validações adicionais conforme necessário
        ]);

        $form = auth()->user()->forms()->create($data);

        return response()->json($form, 201);
    }

    // Endpoint para mostrar os detalhes de um formulário específico
    public function show($id)
    {
        $form = Form::findOrFail($id);
        // Verifique se o usuário tem permissão para acessar este formulário
        // Implemente a lógica de permissão conforme necessário

        return response()->json($form);
    }

    // Endpoint para listar todos os formulários do usuário atual
    public function list()
    {
        $forms = auth()->user()->forms;

        return response()->json($forms);
    }
}
