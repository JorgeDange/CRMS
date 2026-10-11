<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * fase.md PENDÊNCIA 4 — backup com destino externo opcional.
 *
 * Garante que: (1) por omissão o backup é SÓ local (não quebra o actual),
 * (2) com BACKUP_CLOUD_DISK=s3 passa a enviar também para o disco cloud,
 * (3) o disco backup-cloud está configurado para fornecedores S3-compatíveis.
 */
class BackupConfigTest extends TestCase
{
    public function test_backup_destination_is_local_only_by_default(): void
    {
        // Sem BACKUP_CLOUD_DISK definido → só local (regressão do backup actual)
        $this->assertSame(
            ['local'],
            config('backup.backup.source.destination.disks')
        );
    }

    public function test_backup_adds_cloud_disk_when_env_enabled(): void
    {
        putenv('BACKUP_CLOUD_DISK=s3');
        $_ENV['BACKUP_CLOUD_DISK'] = 's3';
        $_SERVER['BACKUP_CLOUD_DISK'] = 's3';

        $config = require base_path('config/backup.php');

        putenv('BACKUP_CLOUD_DISK');
        unset($_ENV['BACKUP_CLOUD_DISK'], $_SERVER['BACKUP_CLOUD_DISK']);

        $this->assertSame(
            ['local', 'backup-cloud'],
            $config['backup']['source']['destination']['disks']
        );
        // Local mantém-se SEMPRE — o externo é uma cópia extra
        $this->assertContains('local', $config['backup']['source']['destination']['disks']);
    }

    public function test_backup_cloud_disk_is_configured_for_s3_compatible_providers(): void
    {
        $disk = config('filesystems.disks.backup-cloud');

        $this->assertSame('s3', $disk['driver']);
        $this->assertTrue($disk['use_path_style_endpoint'], 'Wasabi/B2/R2/MinIO exigem path-style');
        $this->assertSame('us-east-1', $disk['region']);

        // Credenciais vindas das variáveis BACKUP_CLOUD_* (não das AWS_*)
        $this->assertSame(env('BACKUP_CLOUD_KEY'), $disk['key']);
        $this->assertSame(env('BACKUP_CLOUD_SECRET'), $disk['secret']);
        $this->assertSame(env('BACKUP_CLOUD_BUCKET'), $disk['bucket']);
        $this->assertSame(env('BACKUP_CLOUD_ENDPOINT'), $disk['endpoint']);
    }

    public function test_env_template_documents_backup_cloud_variables(): void
    {
        $template = file_get_contents(base_path('.env.production.example'));

        foreach ([
            'BACKUP_CLOUD_DISK',
            'BACKUP_CLOUD_ENDPOINT',
            'BACKUP_CLOUD_REGION',
            'BACKUP_CLOUD_BUCKET',
            'BACKUP_CLOUD_KEY',
            'BACKUP_CLOUD_SECRET',
            'BACKUP_CLOUD_PATH_STYLE',
        ] as $var) {
            $this->assertStringContainsString($var, $template, "template .env.production.example não documenta $var");
        }
    }
}
