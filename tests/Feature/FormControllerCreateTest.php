<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FormControllerCreateTest extends TestCase
{
  use RefreshDatabase;

  public function testCreateForm()
    {
        // Criar um usuário para autenticação
        $user = User::factory()->create();

        $formData = [
            "title"=> "Formulário de bebel",
            "url"=> "URL do formulário",
            "button_color"=> "#ffffff",
            "question_color"=> "#000000",
            "answer_color"=> "#333333",
            "background_color"=> "#f0f0f0",
            "background_image"=> "URL da imagem de fundo",
            "logo"=> "URL do logo",
            "font"=> "Arial",
            "questions"=> [
                    "field_title"=> "Qual é o seu fruta?",
                    "field_description"=> "Digite seu nome completo",
                    "field_type"=> "text",
                    "is_last"=> false,
                    "mandatory"=> false,
                    "value_key"=> "[\"Maça 🍎\",\"Laranja 🍊\",\"Morango 🍓\",\"Abacaxi 🍍\"]",
                    "field_slug"=> ""
                ],
                [
                    "field_title"=> "Qual é o seu e-mail?",
                    "field_description"=> "Digite seu endereço de e-mail",
                    "field_type"=> "email",
                    "is_last"=> true,
                    "mandatory"=> false,
                    "value_key"=> null,
                    "field_slug"=> ""
            ]
        ];

        // Remover o título do formulário
        $formData['title'] = '';

        $response = $this->actingAs($user)
        ->postJson('/form', $formData);

    // Verificar se a resposta é um erro de validação (status 422)
    $response->assertStatus(422);

    // Verificar se a resposta contém a mensagem de erro esperada
    $responseData = $response->json();

    $this->assertArrayHasKey('error', $responseData);
    $this->assertStringContainsString('The title field is required.', $responseData['error']);
    }

}
