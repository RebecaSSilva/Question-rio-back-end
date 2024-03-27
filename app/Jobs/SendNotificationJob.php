<?php

namespace App\Jobs;


use App\Models\User;
use App\Models\Answer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $publicUserId;
    protected $answersData;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param string $publicUserId
     * @param array $answersData
     * @return void
     */
    public function __construct($userId, $publicUserId, $answersData)
    {
        $this->userId = $userId;
        $this->publicUserId = $publicUserId;
        $this->answersData = $answersData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ownerEmail = User::findOrFail($this->userId)->email;
        $subject = 'Nova resposta submetida ao formulário';
        $message = 'Uma nova resposta foi submetida ao seu formulário.';

        try {
            // Adiciona os dados das respostas ao corpo do e-mail
            $message .= "\n\nRespostas:\n" . json_encode($this->answersData);

            // Envia o e-mail usando o SMTP do Mailtrap
            Mail::raw($message, function ($message) use ($ownerEmail, $subject) {
                $message->to($ownerEmail)->subject($subject);
            });

            // Obter o ID das respostas para construir a URL
            $answerIds = Answer::where('public_user_id', $this->publicUserId)->pluck('id')->implode(',');
            $webhookUrl = 'http://127.0.0.1:8000/answers/' . $answerIds;

            // Enviar o webhook para a URL de notificação
            $client = new Client();
            $response = $client->post($webhookUrl, ['json' => $this->answersData]);

            // Verificar o código de resposta
            if ($response->getStatusCode() !== 200) {
            }
        } catch (\Exception $e) {
        }
    }
}


