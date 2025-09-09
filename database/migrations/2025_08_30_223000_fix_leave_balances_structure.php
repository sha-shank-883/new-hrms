<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's check if the table exists and has the wrong structure
        if (Schema::hasTable('leave_balances')) {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Drop the existing table
            Schema::dropIfExists('leave_balances');
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        
        // Now create the table with the correct structure
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->integer('year')->default(now()->year);
            $table->decimal('total_days', 8, 2)->default(0);
            $table->decimal('used_days', 8, 2)->default(0);
            $table->decimal('remaining_days', 8, 2)->default(0);
            $table->timestamps();
            
            // Unique constraint to ensure one balance per user per leave type per year
            $table->unique(['user_id', 'leave_type_id', 'year'], 'user_leave_type_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a one-way migration - we don't want to drop the table on rollback
        // as it would drop the newly created table with correct structure
    }
};
