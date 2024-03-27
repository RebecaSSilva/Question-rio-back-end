<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Form;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendNotificationJob;
class AnswerController extends Controller
{
    
    // Endpoint para criar uma nova resposta
    public function create(Request $request)
    {
        $data = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'answers.*.field_slug' => 'required|string',
            'answers.*.field_title' => 'required|string',
            'answers.*.field_type' => 'required|string',
            'answers.*.value' => 'nullable|string',
            'answers.*.is_last' => 'nullable|boolean',
        ]);

        // Verificar se o formulário existe
        $form = Form::findOrFail($data['form_id']);

        // Obter o ID do usuário associado a este formulário
        $userId = $form->user_id;

        // Gerar um único public_user_id para todas as respostas enviadas nesta solicitação
        $publicUserId = $this->generatePublicUserId();

        // Verificar se o formulário pode aceitar mais respostas
        if (!$this->canAcceptResponse($userId)) {
            return response()->json(['error' => 'Limite de respostas mensais excedido.'], 403);
        }

        // Verificar se todas as perguntas obrigatórias foram respondidas
        if (!$this->allMandatoryQuestionsAnswered($form, $data['answers'])) {
            return response()->json(['error' => 'Por favor, responda todas as perguntas obrigatórias.'], 400);
        }

        $answersData = collect($data['answers'])
            ->map(function ($answer, $index) use ($data, $publicUserId) {
                // Verifica se a pergunta correspondente é a última pergunta
                $question = Question::where('field_slug', $answer['field_slug'])->first();
                $isLast = $question ? $question->is_last : false;

                return [
                    'form_id' => $data['form_id'],
                    'field_slug' => $answer['field_slug'],
                    'field_title' => $answer['field_title'],
                    'field_type' => $answer['field_type'],
                    'value' => $answer['value'],
                    'is_last' => $isLast,
                    'public_user_id' => $publicUserId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->toArray();

        try {
            // Insere a resposta
            Answer::insert($answersData);

            // Incrementar o contador de respostas do usuário
            User::where('id', $userId)->increment('responses_this_month', count($answersData));

            // Se formulário concluido, enviar e-mail e webhook
            if ($this->hasLastAnswer($publicUserId)) {
                // Despachar o job com todas as perguntas e respostas
                SendNotificationJob::dispatch($userId, $publicUserId, $answersData);
            }
        } catch (\Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Respostas inseridas com sucesso.'], 201);
    } 
    
    // Verifica se alguma resposta é a última
    private function hasLastAnswer($publicUserId)
    {
        return Answer::where('public_user_id', $publicUserId)
            ->where('is_last', true)
            ->exists();
    }

    // Lista as perguntas e respostas
    public function listByForm($formId, Request $request)
    {
        // Obter o usuário autenticado
        $user = Auth::user();

        // Verificar se o usuário está autenticado
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }

        // Obter o formulário com o ID fornecido
        $form = Form::findOrFail($formId);

        // Verificar se o formulário pertence ao usuário autenticado
        if ($form->user_id !== $user->id) {
            return response()->json(['error' => 'Você não tem permissão para visualizar estas respostas.'], 403);
        }

        // Obter o tipo de filtro do corpo da requisição
        $filterType = $request->input('filter_type', 'all'); // Padrão para listar todas as pessoas

        // Obter todas as perguntas obrigatórias do formulário
        $mandatoryQuestionSlugs = $form->questions()->where('mandatory', true)->pluck('field_slug');

        // Iniciar a consulta para obter respostas relacionadas ao formulário
        $query = Answer::where('form_id', $formId);

        // Aplicar filtro com base no tipo especificado
        if ($filterType === 'all') {
            // Não aplicar nenhum filtro
        } elseif ($filterType === 'completed') {
            // Filtrar para listar apenas as respostas que têm todas as perguntas respondidas
            $query->where('form_id', $formId)->where('is_last', true);
        } else {
            return response()->json(['error' => 'Tipo de filtro inválido.'], 400);
        }

        // Executar a consulta e obter as respostas
        $answers = $query->get();

        // Retornar as perguntas e respostas
        return response()->json(['form' => $form, 'respostas' => $answers], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // Verifica se o formulário pode aceitar mais respostas
    private function canAcceptResponse($userId)
    {
        // Obter o limite de respostas mensais por usuário
        $responseLimit = 100;
    
        // Obter o usuário correspondente ao ID
        $user = User::findOrFail($userId);
    
        // Verificar se é o primeiro dia do mês
        if (Carbon::now()->day == 1) {
            // Zerar o contador de respostas para o novo mês
            $user->responses_this_month = 0;
            $user->save();
        }
    
        // Verificar se o número atual de respostas neste mês é menor que o limite
        return $user->responses_this_month < $responseLimit;
    }

    // Função para gerar um UUID único para cada solicitação de resposta
    private function generatePublicUserId()
    {
        return Str::uuid();
    }
    
    // Verifica se todas as perguntas obrigatórias do formulário foram respondidas
    private function allMandatoryQuestionsAnswered($form, $answers)
    {
        $mandatoryQuestions = $form->questions()->where('mandatory', true)->pluck('field_slug');
        $answeredQuestions = collect($answers)->pluck('field_slug');
    
        return $mandatoryQuestions->diff($answeredQuestions)->isEmpty();
    }

}
