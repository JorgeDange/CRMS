<?php

use Spatie\Backup\Notifications\Notifiable;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification;
use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification;
use Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes;
use Spatie\DbDumper\Compressors\GzipCompressor;

return [

    'backup' => [

        'name' => env('APP_NAME', 'ADG'),

        'source' => [
            'files' => [
                'include' => [
                    base_path(),
                    // storage_path(), // Include if you use zero downtime deployments and don't follow symlinks
                ],

                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                    base_path('public/build'),
                    storage_path('framework'),
                    storage_path('logs'),
                    storage_path('debugbar'),
                ],

                'follow_links' => false,

                'ignore_unreadable_directories' => false,

                'relative_path' => base_path(),
            ],

            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],

            'database_dump_compressor' => GzipCompressor::class,

            'database_dump_file_timestamp_format' => 'Y-m-d-H-i-s',

            'database_dump_filename_base' => 'database',

            'database_dump_file_extension' => '',

            'destination' => [
                'compression_method' => ZipArchive::CM_DEFAULT,
                'compression_level' => 9,
                'filename_prefix' => 'ADG_',
                // Local sempre. Externo (Wasabi/B2/R2/S3) só se
                // BACKUP_CLOUD_DISK=s3 — fase.md PENDÊNCIA 4.
                'disks' => env('BACKUP_CLOUD_DISK') === 's3'
                    ? ['local', 'backup-cloud']
                    : ['local'],
                'continue_on_failure' => false,
            ],

            'temporary_directory' => storage_path('app/backup-temp'),

            'password' => env('BACKUP_ARCHIVE_PASSWORD'),

            'encryption' => 'default',

            'verify_backup' => true,

            'tries' => 3,

            'retry_delay' => 300,
        ],

        'notifications' => [
            'notifications' => [
                BackupHasFailedNotification::class => ['mail'],
                UnhealthyBackupWasFoundNotification::class => ['mail'],
                CleanupHasFailedNotification::class => ['mail'],
                BackupWasSuccessfulNotification::class => ['mail'],
                HealthyBackupWasFoundNotification::class => ['mail'],
                CleanupWasSuccessfulNotification::class => ['mail'],
            ],

            'notifiable' => Notifiable::class,

            'mail' => [
                'to' => env('BACKUP_NOTIFICATION_EMAIL', env('MAIL_FROM_ADDRESS', 'adailson@otimize.co.ao')),

                'from' => [
                    'address' => env('MAIL_FROM_ADDRESS', 'adailson@otimize.co.ao'),
                    'name' => env('MAIL_FROM_NAME', 'ADG Backup'),
                ],
            ],

            'slack' => [
                'webhook_url' => env('BACKUP_SLACK_WEBHOOK_URL', ''),

                'channel' => null,

                'username' => null,

                'icon' => null,
            ],

            'discord' => [
                'webhook_url' => env('BACKUP_DISCORD_WEBHOOK_URL', ''),

                'username' => '',

                'avatar_url' => '',
            ],

            'webhook' => [
                'url' => env('BACKUP_WEBHOOK_URL', ''),
            ],
        ],

        'log_channel' => 'backup',

        'monitor_backups' => [
            [
                'name' => env('APP_NAME', 'ADG'),
                'disks' => ['local'],
                'health_checks' => [
                    MaximumAgeInDays::class => 2,
                    MaximumStorageInMegabytes::class => 5000,
                ],
            ],
        ],

        'cleanup' => [
            'strategy' => DefaultStrategy::class,

            'default_strategy' => [
                'keep_all_backups_for_days' => 7,

                'keep_daily_backups_for_days' => 16,

                'keep_weekly_backups_for_weeks' => 8,

                'keep_monthly_backups_for_months' => 6,

                'keep_yearly_backups_for_years' => 2,

                'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
            ],
        ],

        'tries' => 3,

        'retry_delay' => 300,
    ],

];
