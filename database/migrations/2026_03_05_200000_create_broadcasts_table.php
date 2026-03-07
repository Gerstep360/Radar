<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                'notification',   // Push notification estándar
                'announcement',   // Banner / anuncio en la app
                'alert',          // Alerta de emergencia
                'update',         // Aviso de nueva versión
                'maintenance',    // Aviso de mantenimiento
                'custom',         // Msg personalizado
            ])->default('notification');
            $table->string('title');
            $table->text('body');
            $table->string('action_url')->nullable();  // URL que la app procesará (ej: /api/app/download)
            $table->string('icon')->nullable();         // Nombre de ícono (ej: "bell", "download", "warning")
            $table->string('color', 7)->nullable();     // Hex del color del banner (ej: "#FF5733")
            $table->timestamp('expires_at')->nullable(); // Cuándo dejar de mostrarse
            $table->boolean('is_active')->default(true);
            $table->string('sent_by')->default('Admin'); // Nombre del admin que lo envió
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
