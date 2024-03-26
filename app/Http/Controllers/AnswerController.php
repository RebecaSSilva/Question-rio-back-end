<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Form;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

            // Verificar se todas as respostas são marcadas como a última resposta
            $allLast = collect($answersData)->pluck('is_last')->every(function ($value) {
                return $value === true;
            });
            
            // Se todas as respostas forem marcadas como a última resposta, enviar e-mail e webhook
            if ($allLast) {
                $ownerEmail = User::findOrFail($userId)->email;
                $this->sendEmailAndWebhookNotification($ownerEmail, $answersData);
            }

        } catch (\Exception $e) {
            return response()->json(['error' =>  $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Respostas inseridas com sucesso.'], 201);
    }

    public function sendWebhookNotification(Request $request)
    {
        $publicUserId = $request->input('public_user_id');
    
        // Verificar se o public_user_id foi fornecido
        if (!$publicUserId) {
            return response()->json(['error' => 'public_user_id não fornecido.'], 400);
        }
    
        // Obter todas as respostas associadas ao public_user_id
        $answers = Answer::where('public_user_id', $publicUserId)->get();
    
        // Verificar se há respostas encontradas para o public_user_id
        if ($answers->isEmpty()) {
            return response()->json(['error' => 'Nenhuma resposta encontrada para o public_user_id fornecido.'], 404);
        }
    
        // Montar os dados do webhook
        $webhookData = $answers->toArray();

        // Obter o ID das respostas para construir a URL
        $answerIds = $answers->pluck('id')->implode(',');

        // Substitua o valor abaixo pela URL real para onde deseja enviar o webhook
        $webhookUrl = 'http://127.0.0.1:8000/answers/' . $answerIds;
    
        // Enviar o webhook para a URL de notificação
        try {
            $client = new Client();
            $response = $client->post($webhookUrl, ['json' => $webhookData]);
    
            // Verificar o código de resposta
            if ($response->getStatusCode() === 200) {
                return response()->json(['message' => 'Webhook enviado com sucesso.'], 200);
            } else {
                return response()->json(['error' => 'Falha ao enviar o webhook.'], $response->getStatusCode());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao enviar o webhook: ' . $e->getMessage()], 500);
        }
    }
    
    // Envia a notificação de nova resposta e o webhook
    private function sendEmailAndWebhookNotification($ownerEmail, $answersData)
    {
        $subject = 'Nova resposta submetida ao formulário';
        $message = 'Uma nova resposta foi submetida ao seu formulário. Verifique o painel para mais detalhes.';
        
        try {
            // Envia o e-mail usando o SMTP do Mailtrap
            Mail::raw($message, function ($message) use ($ownerEmail, $subject) {
                $message->to($ownerEmail)->subject($subject);
            });
    
            // Envia o webhook
            $this->sendWebhookNotification($answersData);
    
            return response()->json(['message' => 'E-mail e webhook enviados com sucesso.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao enviar o e-mail e webhook: ' . $e->getMessage()], 500);
        }
    }

    // Lista as perguntas e respostas
    public function listByForm($formId, Request $request)
    {
        // Verificar se o formulário existe
        $form = Form::findOrFail($formId);
        
        // Obter o tipo de filtro do parâmetro da solicitação
        $filterType = $request->input('filter_type', 'all'); // Padrão para listar todas as pessoas
        
        // Obter todas as perguntas obrigatórias do formulário
        $mandatoryQuestionSlugs = $form->questions()->where('mandatory', true)->pluck('field_slug');
    
        // Obter todas as respostas relacionadas ao formulário
        $query = Answer::where('form_id', $formId);
    
        // Aplicar filtro com base no tipo especificado
        if ($filterType === 'all') {
            // Não aplicar nenhum filtro
        } elseif ($filterType === 'completed') {
            // Filtrar para listar apenas os usuários que responderam todas as perguntas e finalizaram 
            $query->whereHas('questions', function ($query) use ($mandatoryQuestionSlugs) {
                $query->whereIn('field_slug', $mandatoryQuestionSlugs)
                    ->where('is_last', true);
            });
        } else {
            return response()->json(['error' => 'Tipo de filtro inválido.'], 400);
        }
        
        // Obter respostas 
        $answers = $query->get();
        
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
