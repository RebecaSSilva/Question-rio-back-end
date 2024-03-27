<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Form;
use App\Models\Question;
use App\Models\Answer;

class FormListTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_forms_with_questions_and_response_count()
    {
        $user = User::factory()->create();
        $form = Form::factory()->create(['user_id' => $user->id]);
        Question::factory()->count(5)->create(['form_id' => $form->id]);
        Answer::factory()->count(10004)->create(['form_id' => $form->id]);

        $response = $this->actingAs($user)->getJson('/forms');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            [
                'form' => [
                    'id',
                    'user_id',
                    'title',
                    'url',
                    'questions' => [
                        [
                            'id',
                            'field_slug',
                            'field_title',
                        ],
                    ]
                ],
                'total_pessoas'
            ]
        ]);

        $response->assertJson([
            [
                'form' => [
                    'id' => $form->id,
                ],
                'total_pessoas' => 10004
            ]
        ]);
    }
}
