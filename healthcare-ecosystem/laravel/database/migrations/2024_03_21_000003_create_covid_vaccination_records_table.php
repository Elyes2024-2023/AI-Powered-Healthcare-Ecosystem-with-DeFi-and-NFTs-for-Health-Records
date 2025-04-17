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
        Schema::create('covid_vaccination_records', function (Blueprint $table) {
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
            $table->string('nft_token_id')->nullable();
            $table->string('ipfs_hash')->nullable();
            $table->string('encryption_key')->nullable();
            $table->json('metadata')->nullable();
            $table->string('smart_contract_address')->nullable();
            $table->decimal('coverage_amount', 12, 2)->nullable();
            $table->string('blockchain_transaction_hash')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('covid_vaccination_records');
    }
}; 