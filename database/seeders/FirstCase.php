<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Form;
use App\Models\Question;
use App\Models\Answer;

class FirstCase extends Seeder
{
    public function run()
    {
        // Criar usuário com UUID único
        $user = User::factory()->create();
        $publicUserId = (string) \Illuminate\Support\Str::uuid();

        // Loop para criar 200 formulários
        for ($formCount = 0; $formCount < 200; $formCount++) {
            $form = $user->forms()->create([
                'title' => 'Formulário ' . ($formCount + 1),
                'url' => 'https://http://127.0.0.1:8000/forms/' . ($formCount + 1),
            ]);

            // Criar 5 perguntas
            for ($questionCount = 0; $questionCount < 5; $questionCount++) {
                $fieldSlug = uniqid('question_' . ($questionCount + 1) . '_');
                $question = $form->questions()->create([
                    'field_slug' => $fieldSlug,
                    'field_title' => 'Pergunta ' . ($questionCount + 1),
                    'field_description' => 'Descrição da Pergunta ' . ($questionCount + 1),
                    'field_type' => 'text',
                    'mandatory' => false,
                    'value_key' => '',
                    'is_last' => ($questionCount == 4), // Define is_last como true para a última pergunta
                ]);
            }

            // Criar respostas para cada usuário
            for ($userCount = 0; $userCount < 10000; $userCount++) {
                $userPublicUserId = (string) \Illuminate\Support\Str::uuid();
                foreach ($form->questions as $question) {
                    Answer::create([
                        'form_id' => $form->id,
                        'field_slug' => $question->field_slug,
                        'field_title' => $question->field_title,
                        'field_type' => $question->field_type,
                        'value' => 'Resposta para ' . $question->field_title,
                        'value_key' => '',
                        'public_user_id' => $userPublicUserId,
                        'is_last' => ($question->is_last && $question->field_slug === $question->field_slug), // Define is_last como true para a última resposta correspondente à última pergunta
                    ]);
                }
            }
        }
    }
}
