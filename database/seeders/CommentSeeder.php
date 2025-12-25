<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id')->toArray();
        $moderador = User::where('email', 'moderador@radar.test')->first();
        $reports = Report::all();

        if (empty($users) || $reports->isEmpty()) {
            $this->command->warn('⚠️ No hay usuarios o reportes para crear comentarios');
            return;
        }

        $commentsCreated = 0;

        // Comentarios de ciudadanos
        $citizenComments = [
            'Confirmo, yo también vi lo mismo ayer.',
            'Es urgente que atiendan esto, por favor.',
            'Gracias por reportar, somos varios afectados.',
            'Ya va más de una semana y nada...',
            'Esperemos que hagan algo pronto.',
            'Mi vecino también se quejó de lo mismo.',
            'Deberían poner esto como prioridad.',
            '¿Alguien sabe si ya vino alguien a revisar?',
            'Yo llamé a la alcaldía y dijeron que iban a venir.',
            'Ojalá lo solucionen antes de que pase algo peor.',
        ];

        // Comentarios oficiales (funcionario)
        $officialComments = [
            'Hemos recibido su reporte y está siendo evaluado.',
            'Se ha programado una inspección para esta semana.',
            'El área técnica ya fue notificada.',
            'Gracias por su reporte, estamos trabajando en ello.',
            'Se ha asignado una cuadrilla para atender el caso.',
        ];

        foreach ($reports as $report) {
            // Algunos reportes tienen comentarios, otros no
            if (rand(0, 10) < 7) { // 70% tienen comentarios
                
                // 1-3 comentarios de ciudadanos
                $numComments = rand(1, 3);
                for ($i = 0; $i < $numComments; $i++) {
                    Comment::create([
                        'user_id' => $users[array_rand($users)],
                        'report_id' => $report->id,
                        'content' => $citizenComments[array_rand($citizenComments)],
                        'is_official' => false,
                        'created_at' => Carbon::now()->subDays(rand(0, 5)),
                    ]);
                    $commentsCreated++;
                }

                // Si está en revisión o atendido, agregar comentario oficial
                if (in_array($report->status, ['en_revision', 'atendido']) && $moderador) {
                    Comment::create([
                        'user_id' => $moderador->id,
                        'report_id' => $report->id,
                        'content' => $officialComments[array_rand($officialComments)],
                        'is_official' => true,
                        'created_at' => Carbon::now()->subDays(rand(0, 2)),
                    ]);
                    $commentsCreated++;
                }
            }
        }

        $this->command->info("✅ Se crearon {$commentsCreated} comentarios de prueba");
    }
}
