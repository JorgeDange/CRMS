<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AgendaReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agenda:reminder
                            {--date= : Data específica para o lembrete (Y-m-d, default=amanhã)}
                            {--dry-run : Executa sem enviar emails/notificações}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia lembrete diário da agenda (actividades do dia seguinte) por email e notificação no sistema';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateString = $this->option('date') ?? now()->addDay()->toDateString();
        $date = Carbon::parse($dateString);
        $dryRun = $this->option('dry-run');

        $this->info("Lembrete de agenda para {$date->format('d/m/Y')}");

        // Buscar actividades do dia (status pendente + concluida, sem canceladas)
        $activities = Activity::with(['opportunity', 'user'])
            ->where('data', $date->toDateString())
            ->whereIn('status', ['pendente', 'concluida'])
            ->orderBy('hora')
            ->orderBy('id')
            ->get();

        if ($activities->isEmpty()) {
            $this->warn('Nenhuma actividade agendada para este dia.');

            return self::SUCCESS;
        }

        $this->info("Encontradas {$activities->count()} actividade(s).");

        // Email do destinatário (do settings ou utilizador único)
        $emailDestino = Setting::get('alertas.email_destino');
        if (! $emailDestino) {
            $user = User::query()->first();
            $emailDestino = $user?->email;
        }

        if (! $emailDestino) {
            $this->error('Email de destino não configurado (settings alertas.email_destino ou utilizador).');

            return self::FAILURE;
        }

        // Preparar conteúdo do email
        $subject = "ADG — Agenda de {$date->format('d/m/Y')}";
        $body = $this->buildEmailBody($activities, $date);

        // Enviar email
        if (! $dryRun) {
            try {
                Mail::raw($body, function ($message) use ($emailDestino, $subject) {
                    $message->to($emailDestino)
                        ->subject($subject);
                });
                $this->info("Email enviado para {$emailDestino}");
            } catch (\Throwable $e) {
                $this->error("Falha ao enviar email: {$e->getMessage()}");
                Log::error('AgendaReminder email error', ['exception' => $e]);

                return self::FAILURE;
            }
        } else {
            $this->info("[DRY-RUN] Email seria enviado para {$emailDestino}");
            $this->line($body);
        }

        // Criar notificações no sistema (uma por utilizador/actividade ou agrupada)
        // Para simplificar, criar uma notificação única para o utilizador logado/admin
        if (! $dryRun) {
            $this->createSystemNotifications($activities, $date);
            $this->info('Notificações no sistema criadas.');
        } else {
            $this->info('[DRY-RUN] Notificações no sistema seriam criadas.');
        }

        return self::SUCCESS;
    }

    /**
     * Constrói o corpo do email com a lista de actividades.
     */
    private function buildEmailBody(Collection $activities, Carbon $date): string
    {
        $lines = [];
        $lines[] = "Agenda Comercial — {$date->format('d/m/Y')} ({$date->translatedFormat('l')})";
        $lines[] = str_repeat('=', 60);
        $lines[] = '';

        $total = $activities->count();
        $pendentes = $activities->where('status', 'pendente')->count();
        $concluidas = $activities->where('status', 'concluida')->count();

        $lines[] = "Resumo: {$total} actividade(s) — {$pendentes} pendente(s), {$concluidas} concluída(s)";
        $lines[] = '';

        foreach ($activities as $activity) {
            $hora = $activity->hora ? $activity->horaCurta() : '—';
            $tipo = $activity->tipo->label();
            $cliente = $activity->opportunity?->nome ?? $activity->opportunity?->empresa ?? '—';
            $descricao = $activity->descricao;
            $status = $activity->status->label();
            $proxima = $activity->proxima_accao ? " → Próxima: {$activity->proxima_accao}" : '';

            $lines[] = "[{$hora}] {$tipo} — {$cliente}";
            $lines[] = "    {$descricao} [{$status}]{$proxima}";
            $lines[] = '';
        }

        $lines[] = str_repeat('-', 60);
        $lines[] = "Enviado automaticamente pelo ADG às {$date->format('H:i')}";
        $lines[] = 'Não responda a este email.';

        return implode("\n", $lines);
    }

    /**
     * Cria notificações no sistema para o(s) utilizador(es).
     * Como é utilizador único, cria para o primeiro utilizador.
     */
    private function createSystemNotifications(Collection $activities, Carbon $date): void
    {
        $user = User::query()->first();

        if (! $user) {
            return;
        }

        // Usar a tabela alerts (será criada no Sprint 10) ou uma tabela de notificações simples
        // Por agora, apenas log — Sprint 10 implementará alerts completos
        Log::info('AgendaReminder system notification', [
            'user_id' => $user->id,
            'date' => $date->toDateString(),
            'activities_count' => $activities->count(),
            'activities' => $activities->map(fn ($a) => [
                'id' => $a->id,
                'tipo' => $a->tipo->value,
                'descricao' => $a->descricao,
                'hora' => $a->hora,
                'cliente' => $a->opportunity?->nome,
            ])->toArray(),
        ]);

        // TODO Sprint 10: criar registos na tabela alerts
        // Alert::create([...]);
    }
}
