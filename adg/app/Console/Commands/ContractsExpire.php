<?php

namespace App\Console\Commands;

use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Services\ContractService;
use Illuminate\Console\Command;

class ContractsExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:expire
                            {--dry-run : Executa sem alterar estados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muda para expirado os contratos activos cuja data_fim já passou (suposição §19.12)';

    /**
     * Execute the console command.
     */
    public function handle(ContractService $service): int
    {
        $dryRun = $this->option('dry-run');
        $hoje = now()->toDateString();

        $contratos = Contract::where('estado', ContractStatus::ACTIVO)
            ->whereNotNull('data_fim')
            ->where('data_fim', '<', $hoje)
            ->get();

        if ($contratos->isEmpty()) {
            $this->info('Nenhum contrato activo expirado.');

            return self::SUCCESS;
        }

        foreach ($contratos as $contrato) {
            $this->line("{$contrato->numero_contrato} — data_fim {$contrato->data_fim->format('d/m/Y')}");

            if (! $dryRun) {
                $service->setStatus($contrato, ContractStatus::EXPIRADO);
            }
        }

        $total = $contratos->count();
        $this->info($dryRun
            ? "[DRY-RUN] {$total} contrato(s) SERIAM marcados como expirado."
            : "{$total} contrato(s) marcado(s) como expirado.");

        return self::SUCCESS;
    }
}
