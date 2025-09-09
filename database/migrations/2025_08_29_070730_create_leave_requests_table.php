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
        // Schema::create('leave_requests', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('employee_id');
        //     $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        //     $table->unsignedBigInteger('leave_type_id');
        //     $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
        //     $table->date('start_date');
        //     $table->date('end_date');
        //     $table->integer('days');
        //     $table->text('reason')->nullable();
        //     $table->string('status')->default('pending'); // pending, approved, rejected
        //     $table->text('rejection_reason')->nullable();
        //     $table->unsignedBigInteger('approved_by')->nullable();
        //     $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        //     $table->timestamp('approved_at')->nullable();
        //     $table->unsignedBigInteger('tenant_id');
        //     $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};