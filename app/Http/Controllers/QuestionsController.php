<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

class QuestionController extends Controller
{
    // Endpoint para criar uma nova questão associada a um formulário
    public function create(Request $request, $formId)
    {
        $data = $request->validate([
            'field_slug' => 'required|string|unique:questions',
            'field_title' => 'required|string',
            'field_description' => 'required|string',
            'field_type' => 'required|string',
            'is_last' => 'nullable|boolean',
            'mandatory' => 'nullable|boolean',
            'value_key' => 'nullable|string',
            // Adicione validações adicionais conforme necessário
        ]);

        $data['form_id'] = $formId;

        $question = Question::create($data);

        return response()->json($question, 201);
    }

    // Endpoint para mostrar os detalhes de uma questão específica
    public function show($id)
    {
        $question = Question::findOrFail($id);
        // Verifique se o usuário tem permissão para acessar esta questão
        // Implemente a lógica de permissão conforme necessário

        return response()->json($question);
    }
}
