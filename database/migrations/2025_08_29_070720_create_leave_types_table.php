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
        // Schema::create('leave_types', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->text('description')->nullable();
        //     $table->integer('default_days')->default(0);
        //     $table->boolean('requires_approval')->default(true);
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
        Schema::dropIfExists('leave_types');
    }
};