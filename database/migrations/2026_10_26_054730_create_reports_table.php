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
    Schema::create('reports', function (Blueprint $table) {
        $table->id();
        $table->foreignId('fulfillment_id')->constrained()->cascadeOnDelete();
        $table->text('notes')->nullable();
        $table->string('animal_photo')->nullable();
        $table->string('slaughter_photo')->nullable();
        $table->string('distribution_photo')->nullable();
        $table->timestamp('published_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
