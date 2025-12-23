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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->timestamps();
            
            // Un usuario solo puede votar una vez por reporte
            $table->unique(['user_id', 'report_id']);
            $table->index('report_id'); // Índice para contar votos rápido
        });

        // Agregar contador de votos a la tabla reports
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedInteger('votes_count')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('votes_count');
        });
        
        Schema::dropIfExists('votes');
    }
};
