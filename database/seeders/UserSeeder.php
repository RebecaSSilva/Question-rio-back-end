<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Form;
use App\Models\Answer;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Criação do primeiro usuário com 200 formulários
        $user1 = User::factory()->create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
        ]);

        for ($i = 0; $i < 200; $i++) {
            $form = $user1->forms()->create([
                'title' => "Formulário $i",
                // Adicione outras informações do formulário, se necessário
            ]);

            // Criação de 10 mil respostas para cada formulário
            for ($j = 0; $j < 10000; $j++) {
                $form->answers()->create([
                    // Adicione os campos das respostas conforme necessário
                ]);
            }
        }

        // Criação do segundo usuário com 1 formulário e 100 mil respostas
        $user2 = User::factory()->create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
        ]);

        $form = $user2->forms()->create([
            'title' => 'Formulário Único',
            // Adicione outras informações do formulário, se necessário
        ]);

        // Criação de 100 mil respostas para o formulário único
        for ($k = 0; $k < 100000; $k++) {
            $form->answers()->create([
                // Adicione os campos das respostas conforme necessário
            ]);
        }
    }
}
