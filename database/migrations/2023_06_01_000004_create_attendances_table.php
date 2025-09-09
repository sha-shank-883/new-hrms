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
        // Schema::create('attendances', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     $table->date('date');
        //     $table->time('check_in')->nullable();
        //     $table->time('check_out')->nullable();
        //     $table->enum('status', ['present', 'absent', 'late', 'half_day'])->default('present');
        //     $table->float('working_hours')->default(0);
        //     $table->boolean('is_late')->default(false);
        //     $table->text('notes')->nullable();
        //     $table->timestamps();
            
        //     // Unique constraint to ensure one attendance record per user per date
        //     $table->unique(['tenant_id', 'user_id', 'date']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};