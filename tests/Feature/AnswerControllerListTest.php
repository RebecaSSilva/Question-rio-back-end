<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Form;
use App\Models\Answer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnswerControllerListTest extends TestCase
{

  use RefreshDatabase;

  public function testListAllAnswersByForm()
  {
    // Criar um usuário e um formulário para o teste
    $user = User::factory()->create();
    $form = Form::factory()->create(['user_id' => $user->id]);

    // Criar algumas respostas para o formulário
    Answer::factory()->count(3)->create(['form_id' => $form->id]);

    // Fazer a requisição para o endpoint
    $response = $this->actingAs($user)
      ->postJson('/answers/' . $form->id);

    // Verificar se a resposta foi bem-sucedida
    $response->assertStatus(200);

    // Verificar se a resposta contém o formulário e todas as respostas
    $responseData = json_decode($response->getContent(), true);
    $this->assertEquals($form->id, $responseData['form']['id']);
    $this->assertCount(3, $responseData['respostas']);
  }
}
