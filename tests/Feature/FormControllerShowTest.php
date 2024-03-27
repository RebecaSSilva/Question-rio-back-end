<?php

namespace Tests\Unit\Http\Controllers;

use App\Models\Form;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormControllerShowTest extends TestCase
{
    use RefreshDatabase;

    // Teste de mostrar formulário
    public function testShowFormSuccess()
    {
        $user = User::factory()->create();
        $form = Form::factory()->create(['user_id' => $user->id]);
    
        $response = $this->actingAs($user)->getJson("/forms/{$form->id}");
    
        $response->assertStatus(200);
    }
}
