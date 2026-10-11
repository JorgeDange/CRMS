<?php

namespace Tests\Feature;

use App\Console\Commands\UserPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * fase.md PENDÊNCIA 6 — comando user:password.
 */
class UserPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create([
            'password' => Hash::make('password'),
        ]);
    }

    // ------------------------------------------------------------------
    // Validação de força (regras da Pendência 6: 12+ chars, A-Z, a-z, 0-9, símbolo)
    // ------------------------------------------------------------------

    public function test_strength_rules_reject_weak_passwords(): void
    {
        $this->assertNotNull(UserPassword::validateStrength('Curta1!'), 'curta');
        $this->assertNotNull(UserPassword::validateStrength('semmaiuscula1!'), 'sem maiúscula');
        $this->assertNotNull(UserPassword::validateStrength('SEMMINUSCULA1!'), 'sem minúscula');
        $this->assertNotNull(UserPassword::validateStrength('SemNumeros!!'), 'sem número');
        $this->assertNotNull(UserPassword::validateStrength('SemSimbolo123'), 'sem símbolo');
    }

    public function test_strength_rules_accept_strong_password(): void
    {
        $this->assertNull(UserPassword::validateStrength('Otimize#2026Ang'));
        $this->assertNull(UserPassword::validateStrength('Kk9$mZp2!wQr#7x'));
    }

    // ------------------------------------------------------------------
    // Comando
    // ------------------------------------------------------------------

    public function test_generate_option_sets_a_strong_password(): void
    {
        $user = $this->user();

        $this->artisan('user:password', [
            'email' => $user->email,
            '--generate' => true,
        ])->assertSuccessful();

        $user->refresh();

        // A password antiga deixa de servir e a nova grava-se como hash bcrypt
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertStringStartsWith('$2y$', $user->password);
    }

    public function test_command_rejects_weak_password_and_keeps_old_one(): void
    {
        $user = $this->user();
        $antes = $user->password;

        $this->artisan('user:password', [
            'email' => $user->email,
        ])
            ->expectsQuestion('Nova password', 'fraca')
            ->expectsQuestion('Confirmar nova password', 'fraca')
            ->assertFailed();

        $this->assertSame($antes, $user->fresh()->password, 'password não pode mudar com valor rejeitado');
    }

    public function test_command_rejects_mismatched_confirmation(): void
    {
        $user = $this->user();
        $antes = $user->password;

        $this->artisan('user:password', [
            'email' => $user->email,
        ])
            ->expectsQuestion('Nova password', 'Otimize#2026Ang')
            ->expectsQuestion('Confirmar nova password', 'Otimize#2026Axx')
            ->assertFailed();

        $this->assertSame($antes, $user->fresh()->password);
    }

    public function test_command_accepts_strong_password_via_prompts(): void
    {
        $user = $this->user();

        $this->artisan('user:password', [
            'email' => $user->email,
        ])
            ->expectsQuestion('Nova password', 'Otimize#2026Ang')
            ->expectsQuestion('Confirmar nova password', 'Otimize#2026Ang')
            ->assertSuccessful();

        $this->assertTrue(Hash::check('Otimize#2026Ang', $user->fresh()->password));
    }

    public function test_command_fails_for_unknown_email(): void
    {
        $this->user();

        $this->artisan('user:password', ['email' => 'naoexiste@x.ao'])->assertFailed();
    }

    public function test_command_targets_the_single_user_when_email_omitted(): void
    {
        $user = $this->user();

        // Sem argumento → usa o único utilizador do sistema
        $this->artisan('user:password', ['--generate' => true])->assertSuccessful();

        $this->assertFalse(Hash::check('password', $user->fresh()->password));
    }
}
