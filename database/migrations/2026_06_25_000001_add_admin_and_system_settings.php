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
            $table->boolean('is_admin')->default(false)->after('tipo');
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });

        $firstUserId = DB::table('users')->orderBy('id')->value('id');

        if ($firstUserId) {
            DB::table('users')->where('id', $firstUserId)->update(['is_admin' => true]);
        }

        DB::table('system_settings')->insert([
            'key' => 'theme',
            'value' => json_encode([
                'primary_color' => '#2D6A63',
                'secondary_color' => '#FF7A1A',
                'accent_color' => '#73B98F',
                'background_color' => '#F4FBF8',
                'section_color' => '#FFFFFF',
                'text_color' => '#21433D',
                'text_secondary_color' => '#6D7D78',
                'input_background_color' => '#F8FAFC',
                'font_family' => 'Inter, Segoe UI, Arial, sans-serif',
                'font_size' => '16',
                'title_font_family' => 'Inter, Segoe UI, Arial, sans-serif',
                'layout_style' => 'glass',
                'border_style' => 'solid',
                'contrast' => '50',
                'soft_shadows' => true,
                'smooth_animations' => true,
                'gradients' => false,
                'high_contrast' => false,
                'reduce_motion' => false,
                'logo_path' => 'images/LOGOUNICEHUB-removebg-preview.png',
                'background_path' => 'images/themes/image-78.png',
                'auth_background_path' => 'images/themes/image-78.png',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
