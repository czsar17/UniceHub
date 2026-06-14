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
        Schema::create('followers', function (Blueprint $table) {

            $table->id();

            // QUEM ENVIOU A SOLICITAÇÃO
            $table->foreignId('seguidor_id')
                ->constrained('users')
                ->onDelete('cascade');

            // QUEM RECEBEU A SOLICITAÇÃO
            $table->foreignId('seguido_id')
                ->constrained('users')
                ->onDelete('cascade');

            // STATUS DA SOLICITAÇÃO
            $table->enum('status', [
                'pendente',
                'aceito',
                'recusado'
            ])->default('pendente');

            $table->timestamps();

            // EVITA DUPLICAR SOLICITAÇÕES
            $table->unique([
                'seguidor_id',
                'seguido_id'
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};