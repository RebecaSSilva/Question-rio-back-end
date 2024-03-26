<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class FirstCase extends Seeder
{
    public function run()
    {
        // Criar outro usuário com 1 formulário
        $user = User::factory()->create();

        for ($formCount = 0; $formCount < 200; $formCount++) {
            $form = $user->forms()->create([
                'title' => 'Formulário ' . ($formCount + 1),
                'url' => 'https://http://127.0.0.1:8000/forms/' . ($formCount + 1),
            ]);

            // Criar 5 perguntas 
            for ($questionCount = 0; $questionCount < 5; $questionCount++) {
                $field_slug = uniqid('question_' . ($questionCount + 1) . '_');
                $question = $form->questions()->create([
                    'field_slug' => $field_slug,
                    'field_title' => 'Pergunta ' . ($questionCount + 1),
                    'field_description' => 'Descrição da Pergunta ' . ($questionCount + 1),
                    'field_type' => 'text',
                    'mandatory' => false, 
                    'value_key' => '',
                ]);

                if ($questionCount == 4) {
                    $question->update(['is_last' => true]);
                }
            }
            // Criar respostas
            $answersCount = 0;
            while ($answersCount < 10000) {
                foreach ($form->questions as $question) {
                    $form->answers()->create([
                        'field_slug' => $question->field_slug,
                        'field_title' => $question->field_title,
                        'field_type' => $question->field_type,
                        'value' => 'Resposta para ' . $question->field_title,
                        'value_key' => '',
                        'public_user_id' => (string) \Illuminate\Support\Str::uuid(),
                    ]);
//  ajustar seeders na parte do public_user_id para pegar o emsmo usuario apra todas as respostas enviadas s
                    $answersCount++;

                    if ($answersCount >= 10000) {
                        break 2; // Sair do loop externo também
                    }
                }
            }
        }
    }
}
