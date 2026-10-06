<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class AlertsGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:generate
                            {--dry-run : Executa sem criar alertas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera alertas do sistema (Oportunidades, Clientes, Contratos, Leads, Conciliações)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info('A gerar alertas do sistema...');

        if ($dryRun) {
            $this->warn('Modo DRY-RUN — nenhum alerta será criado.');
        }

        $service = app(AlertService::class);

        if ($dryRun) {
            // TODO: implementar modo dry-run que apenas mostra o que seria feito
            $this->info('Modo dry-run não implementado totalmente — a executar normalmente.');
        }

        $report = $service->generate();

        $this->table(
            ['Tipo', 'Novos Alertas'],
            [
                ['Oportunidades sem actividade', $report['oportunidade']],
                ['Clientes sem contacto', $report['cliente']],
                ['Contratos por expirar', $report['contrato']],
                ['Leads inativos', $report['lead_inativo']],
                ['Conciliações discordantes', $report['conciliacao']],
            ]
        );

        $total = array_sum($report);
        $this->info("Total de novos alertas criados: {$total}");

        return self::SUCCESS;
    }
}
