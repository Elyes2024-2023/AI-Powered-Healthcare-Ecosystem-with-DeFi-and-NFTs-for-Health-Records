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
        Schema::create('vaccination_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('insurance_policy_id')->nullable()->constrained()->onDelete('set null');
            $table->string('vaccine_name');
            $table->string('vaccine_batch');
            $table->integer('dose_number');
            $table->date('vaccination_date');
            $table->date('next_dose_date')->nullable();
            $table->string('vaccination_center');
            $table->string('healthcare_provider');
            $table->string('vaccination_proof');
            $table->json('side_effects')->nullable();
            $table->json('notes')->nullable();
            $table->string('smart_contract_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaccination_records');
    }
}; 