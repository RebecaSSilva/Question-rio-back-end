<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Answer;

class AnswerControllerCreateTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateMethod()
    {
        // Criar um usuário e um formulário para o teste
        $user = User::factory()->create();
        $form = Form::factory()->create(['user_id' => $user->id]);

        // Definir os dados da requisição
        $data = [
            'form_id' => $form->id,
            'answers' => [
                [
                    'field_slug' => '654265d5',
                    'field_title' => 'Questionario',
                    'field_type' => 'text',
                    'value' => 'John Doe',
                    'value_key' => null,
                    'is_last' => true,
                ],
            ],
        ];

        // Fazer uma requisição para o endpoint
        $response = $this->actingAs($user)
            ->postJson('/answers', $data);

        // Verificar se a resposta foi bem-sucedida
        $response->assertStatus(201);

        // Verificar se a resposta foi inserida no banco de dados
        $this->assertDatabaseHas('answers', [
            'form_id' => $form->id,
            'field_slug' => '654265d5',
            'value' => 'John Doe',
            'value_key' => null,
        ]);
    }
}
