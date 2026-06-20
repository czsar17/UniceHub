<?php

namespace Tests\Feature;

use App\Models\Atividade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthProfileActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_does_not_create_activity_when_profile_data_is_unchanged(): void
    {
        $user = User::factory()->create([
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'telefone' => '11999999999',
            'curso' => 'ADS',
            'sobre_mim' => 'Sou estudante.',
            'interesses_markdown' => 'Laravel',
            'tecnologias' => ['PHP', 'Laravel'],
        ]);

        $this->actingAs($user);

        $response = $this->from('/perfil')->post('/perfil/atualizar', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'telefone' => '11999999999',
            'curso' => 'ADS',
            'sobre_mim' => 'Sou estudante.',
            'interesses_markdown' => 'Laravel',
            'tecnologias' => ['PHP', 'Laravel'],
        ]);

        $response->assertRedirect('/perfil');
        $this->assertDatabaseMissing('atividades', [
            'user_id' => $user->id,
            'descricao' => 'Atualizou as informações do perfil',
        ]);
    }
}
