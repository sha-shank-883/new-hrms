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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('assigned_by');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
            $table->date('due_date')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};