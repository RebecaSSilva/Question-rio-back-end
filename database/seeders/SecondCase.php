<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Answer;

class SecondCase extends Seeder
{
    public function run()
    {
        // Criar outro usuário com 1 formulário
        $user = User::factory()->create();
        $form = $user->forms()->create([
            'title' => 'Formulário 201',
            'url' => 'https://http://127.0.0.1:8000/forms/201',
        ]);

        // Criar questões para o formulário
        for ($i = 0; $i < 5; $i++) {
            $field_slug = uniqid('question_' . ($i + 1) . '_');
            $question = $form->questions()->create([
                'field_slug' => $field_slug,
                'field_title' => 'Pergunta ' . ($i + 1),
                'field_description' => 'Descrição da Pergunta ' . ($i + 1),
                'field_type' => 'text',
                'mandatory' => false, 
                'value_key' => '',
            ]);

            // Definir a última pergunta como verdadeira
            if ($i == 4) {
                $question->update(['is_last' => true]);
            }
        }

        // Limitar o número de respostas para 100 mil
        $max_responses = 100000;
        $responses_created = 0;

        // Criar as respostas para o formulário
        for ($k = 0; $k < $max_responses; $k++) {
            foreach ($form->questions as $question) {
                // Verificar se já atingiu o limite de respostas
                if ($responses_created >= $max_responses) {
                    break 2; // Sai do loop externo
                }
        
                $answer = $form->answers()->create([
                    'field_slug' => $question->field_slug,
                    'field_title' => $question->field_title,
                    'field_type' => $question->field_type,
                    'value' => 'Resposta para ' . $question->field_title,
                    'value_key' => '',
                    'public_user_id' => (string) \Illuminate\Support\Str::uuid(),
                ]);

                $responses_created++;
            }
        }

    }
}

