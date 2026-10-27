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
        Schema::create('savings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('package_code', 20);
    $table->unsignedInteger('target_amount');
    $table->unsignedTinyInteger('tenor_months')->default(7);
    $table->date('start_month');
    $table->unsignedSmallInteger('program_year');
    $table->string('status', 20)->default('active');
    $table->timestamps();

    $table->index(['user_id', 'status']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};
