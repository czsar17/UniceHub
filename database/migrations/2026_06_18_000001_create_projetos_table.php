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
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('categoria')->nullable();
            $table->json('tecnologias')->nullable();
            $table->string('repo_url')->nullable();
            $table->string('capa')->nullable();
            $table->string('status')->default('Em desenvolvimento');
            $table->timestamps();
        });

        Schema::create('projeto_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pendente');
            $table->timestamps();

            $table->unique(['projeto_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projeto_user');
        Schema::dropIfExists('projetos');
    }
};