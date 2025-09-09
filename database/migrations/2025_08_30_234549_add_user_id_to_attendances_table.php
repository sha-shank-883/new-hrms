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
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('employee_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Update existing records to set user_id based on employee_id
        \DB::statement('UPDATE attendances a JOIN employees e ON a.employee_id = e.id SET a.user_id = e.user_id');
        
        // Make user_id not nullable after populating it
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
