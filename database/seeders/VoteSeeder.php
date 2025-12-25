<?php

namespace Database\Seeders;

use App\Models\Vote;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class VoteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id')->toArray();
        $reports = Report::all();
        
        if (empty($users) || $reports->isEmpty()) {
            $this->command->warn('⚠️ No hay usuarios o reportes para crear votos');
            return;
        }

        $votesCreated = 0;

        foreach ($reports as $report) {
            // Determinar cuántos votos según el tipo de reporte
            $voteCount = $this->getVoteCountForReport($report);
            
            // Seleccionar usuarios aleatorios para votar
            $voterIds = collect($users)->shuffle()->take($voteCount);
            
            foreach ($voterIds as $userId) {
                // Evitar duplicados
                $exists = Vote::where('user_id', $userId)
                              ->where('report_id', $report->id)
                              ->exists();
                
                if (!$exists) {
                    Vote::create([
                        'user_id' => $userId,
                        'report_id' => $report->id,
                    ]);
                    $votesCreated++;
                }
            }
        }

        $this->command->info("✅ Se crearon {$votesCreated} votos de prueba");
    }

    /**
     * Determina la cantidad de votos según características del reporte
     */
    private function getVoteCountForReport(Report $report): int
    {
        $categoryName = $report->category?->name ?? '';
        $status = $report->status;
        
        // 🔴 Reportes de seguridad/emergencia = MUCHOS votos (>10 para activar urgente)
        if (str_contains(strtolower($categoryName), 'seguridad') ||
            str_contains(strtolower($categoryName), 'quema') ||
            str_contains(strtolower($categoryName), 'emergencia')) {
            return rand(12, 25); // Más de 10 = URGENTE (rojo pulsante)
        }
        
        // 🟢 Reportes atendidos = votos moderados
        if ($status === 'atendido') {
            return rand(5, 15);
        }
        
        // 🔵 En revisión = algunos votos
        if ($status === 'en_revision') {
            return rand(3, 8);
        }
        
        // ⚫ Desestimados = pocos votos
        if ($status === 'desestimado') {
            return rand(0, 2);
        }
        
        // 🟡 Pendientes = variado
        return rand(1, 10);
    }
}
