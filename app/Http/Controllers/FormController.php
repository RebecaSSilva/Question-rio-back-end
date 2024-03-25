<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Question;

class FormController extends Controller
{
    // Endpoint para criar um novo formulário com questões
    public function create(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'info_style' => 'nullable|string',
            'url' => 'nullable|string',
            'questions' => 'required|array', // Garante que pelo menos uma pergunta seja fornecida
        ]);
    
        // Crie o formulário sem associá-lo ao usuário autenticado
        $form = Form::create($data);
    
        return response()->json($form, 201);
    }

    // Método para criar uma questão a partir dos dados fornecidos
    protected function createQuestion($questionData)
    {
        return Question::create($questionData);
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
    public function list(Request $request)
    {
        // Se o usuário estiver autenticado, retorne seus formulários com suas questões
        $forms = $request->user()->forms()->with('questions')->get();
        
        return response()->json($forms);
    }
    
    
}
