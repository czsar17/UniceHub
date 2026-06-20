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
        if (! Schema::hasColumn('projeto_user', 'status')) {
            Schema::table('projeto_user', function (Blueprint $table) {
                $table->string('status')->default('pendente')->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('projeto_user', 'status')) {
            Schema::table('projeto_user', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
