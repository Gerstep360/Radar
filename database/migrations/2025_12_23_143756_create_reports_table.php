<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
                $table->id();
                // Relaciones (asumiendo que users ya existe)
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('category_id')->constrained();

                $table->string('title', 100);
                $table->text('description');

                // GPS EXACTO: 10,8 para lat y 11,8 para long. 
                // No uses Float, pierde precisión en mapas.
                $table->decimal('latitude', 10, 8);
                $table->decimal('longitude', 11, 8);

                // Estado del trámite
                $table->enum('status', ['pendiente', 'en_revision', 'atendido', 'desestimado'])
                    ->default('pendiente');

                $table->timestamps();
                
                // Indices para que las consultas vuelen cuando tengas 10k reportes
                $table->index('status'); 
                $table->index('user_id');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
