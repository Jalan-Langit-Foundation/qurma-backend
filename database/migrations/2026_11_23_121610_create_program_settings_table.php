<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_settings', function (Blueprint $table) {
            $table->id();
            $table->date('idul_adha_date')->nullable();
            $table->unsignedTinyInteger('payoff_lead_days')->default(10);
            $table->date('registration_deadline');
            $table->boolean('last_month_in_deadline')->default(true);
            $table->timestamps();
        });

        DB::table('program_settings')->insert([
            'idul_adha_date'         => null,
            'payoff_lead_days'       => 10,
            'registration_deadline'  => '2027-04-30',
            'last_month_in_deadline' => true,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('program_settings');
    }
};