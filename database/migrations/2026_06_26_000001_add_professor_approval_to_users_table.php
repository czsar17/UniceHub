<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('approval_status', 20)->default('approved')->after('is_admin');
            $table->timestamp('approval_requested_at')->nullable()->after('approval_status');
            $table->timestamp('approval_reviewed_at')->nullable()->after('approval_requested_at');
            $table->foreignId('approval_reviewed_by')->nullable()->after('approval_reviewed_at')->constrained('users')->nullOnDelete();
        });

        DB::table('users')
            ->where('tipo', 'professor')
            ->where('is_admin', false)
            ->update(['approval_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approval_reviewed_by');
            $table->dropColumn(['approval_status', 'approval_requested_at', 'approval_reviewed_at']);
        });
    }
};
