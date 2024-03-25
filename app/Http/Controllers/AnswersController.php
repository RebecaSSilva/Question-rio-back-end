<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Form;

class AnswerController extends Controller
{
    // Endpoint para criar uma nova resposta
    public function create(Request $request)
    {
        $data = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'field_slug' => 'required|string',
            'field_title' => 'required|string',
            'field_type' => 'required|string',
            'value' => 'nullable|string',
            'is_first' => 'nullable|boolean',
            'public_user_id' => 'nullable|string',
        ]);

        // Verifique se o formulário aceita mais respostas antes de criar a resposta
        $form = Form::findOrFail($data['form_id']);
        // Implemente a lógica para verificar o consumo do formulário e se ele ainda pode aceitar respostas e cria a resposta 
        $answer = Answer::create($data);

        return response()->json($answer, 201);
    }

    // Endpoint para listar todas as respostas de um formulário específico
    public function listByForm($formId)
    {
        $form = Form::findOrFail($formId);
        // Verifique se o usuário tem permissão para acessar as respostas deste formulário
        // Implemente a lógica de permissão conforme necessário

        $answers = $form->answers;

        return response()->json($answers);
    }
}
