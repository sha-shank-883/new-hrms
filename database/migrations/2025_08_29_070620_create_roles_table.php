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
        // Schema::create('roles', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('slug')->unique();
        //     $table->text('description')->nullable();
        //     $table->unsignedBigInteger('tenant_id')->nullable();
        //     $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
