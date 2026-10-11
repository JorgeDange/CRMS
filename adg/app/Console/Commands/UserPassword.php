<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * fase.md PENDÊNCIA 6 — redefine a password do utilizador único do sistema.
 *
 * O utilizador é criado em produção pelo ProductionSeeder com a password de
 * ADMIN_PASSWORD. Este comando permite mudá-la depois, sem aceder à BD, com
 * validação de força mínima.
 */
class UserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:password
                            {email? : Email do utilizador (por omissão, o único do sistema)}
                            {--generate : Gera uma password forte e mostra-a uma única vez}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Redefine a password do utilizador único (valida força mínima: 12+ chars, maiúscula, minúscula, número, símbolo)';

    /**
     * Tamanho mínimo da password.
     */
    private const MIN_LENGTH = 12;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $query = User::query();
        if ($email !== null) {
            $query->where('email', $email);
        }

        $user = $query->first();

        if ($user === null) {
            $this->error($email !== null
                ? "Nenhum utilizador com o email \"{$email}\"."
                : 'Nenhum utilizador no sistema — corra primeiro o ProductionSeeder.');

            return self::FAILURE;
        }

        if ($this->option('generate')) {
            return $this->setPassword($user, $this->generate());
        }

        $nova = $this->secret('Nova password');
        if ($nova === null || $nova === '') {
            $this->error('Password vazia — cancelado.');

            return self::FAILURE;
        }

        if ($nova !== $this->secret('Confirmar nova password')) {
            $this->error('As passwords não coincidem — cancelado.');

            return self::FAILURE;
        }

        return $this->setPassword($user, $nova);
    }

    /**
     * Valida a força, grava e reporta.
     */
    private function setPassword(User $user, string $password): int
    {
        if ($problema = self::validateStrength($password)) {
            $this->error("Password rejeitada: {$problema}.");

            return self::FAILURE;
        }

        $user->forceFill(['password' => Hash::make($password)])->save();

        $this->info("Password de {$user->email} ({$user->username}) redefinida.");

        return self::SUCCESS;
    }

    /**
     * Valida a força mínima. Devolve a descrição do problema ou null se válida.
     */
    public static function validateStrength(string $password): ?string
    {
        if (mb_strlen($password) < self::MIN_LENGTH) {
            return 'mínimo de '.self::MIN_LENGTH.' caracteres';
        }

        if (! preg_match('/[a-z]/', $password)) {
            return 'falta letra minúscula';
        }

        if (! preg_match('/[A-Z]/', $password)) {
            return 'falta letra maiúscula';
        }

        if (! preg_match('/[0-9]/', $password)) {
            return 'falta número';
        }

        if (! preg_match('/[^A-Za-z0-9]/', $password)) {
            return 'falta símbolo';
        }

        return null;
    }

    /**
     * Gera uma password forte e legível (sem caracteres ambiguos).
     */
    private function generate(): string
    {
        return Str::password(20, symbols: true);
    }
}
