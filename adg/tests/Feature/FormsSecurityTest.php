<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Regressão de SEGURANÇA — forms sem method="POST".
 *
 * Bug real (2026-10-12): o form de login foi copiado do protótipo estático
 * sem `method="POST"`. Em HTML, um <form> sem method assume GET por omissão;
 * se o JS/Livewire ainda não tiver carregado (ou falhar), o browser submete
 * NATIVAMENTE e os campos (login, password) ficam expostos na query string
 * da URL — visíveis no browser, no histórico e nos logs do servidor.
 *
 * Todos os <form> do sistema têm de declarar method="POST".
 */
class FormsSecurityTest extends TestCase
{
    public function test_every_form_declares_post_method(): void
    {
        $violacoes = [];

        $arquivos = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($arquivos as $file) {
            if ($file->isDir() || $file->getExtension() !== 'blade.php') {
                continue;
            }

            $html = file_get_contents($file->getPathname());

            // Cada <form ...> tem de ter method="POST"
            preg_match_all('/<form\b[^>]*>/i', $html, $encontrados);

            foreach ($encontrados[0] as $tag) {
                if (! preg_match('/method\s*=\s*["\']POST["\']/i', $tag)) {
                    $violacoes[] = $file->getFilename().' → '.$tag;
                }
            }
        }

        $this->assertSame(
            [],
            $violacoes,
            "Forms sem method=\"POST\" (podem expor dados na URL como GET):\n".implode("\n", $violacoes)
        );
    }

    public function test_login_form_posts_to_login_route_with_csrf(): void
    {
        $html = file_get_contents(
            resource_path('views/livewire/pages/auth/login.blade.php')
        );

        // method POST + action + CSRF — defesa em profundidade no form mais crítico
        $this->assertMatchesRegularExpression(
            '/<form[^>]*method\s*=\s*["\']POST["\'][^>]*>/i',
            $html
        );
        $this->assertStringContainsString("route('login')", $html);
        $this->assertStringContainsString('@csrf', $html);
    }
}
