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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'site_name', 'value' => config('app.name'), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'site_email', 'value' => config('mail.from.address'), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'timezone', 'value' => config('app.timezone'), 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'time_format', 'value' => 'H:i', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
