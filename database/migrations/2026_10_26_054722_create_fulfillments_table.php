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
        Schema::create('fulfillments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('saving_id')->unique()->constrained()->cascadeOnDelete();
    $table->string('status', 30)->default('animal_preparing');
    $table->string('vendor_name')->nullable();
    $table->unsignedSmallInteger('animal_weight')->nullable();
    $table->date('slaughter_date')->nullable();
    $table->string('location')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fulfillments');
    }
};
