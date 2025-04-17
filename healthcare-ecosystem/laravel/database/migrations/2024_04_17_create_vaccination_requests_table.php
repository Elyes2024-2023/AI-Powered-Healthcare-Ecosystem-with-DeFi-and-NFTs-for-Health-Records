<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vaccination_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('insurance_policy_id')->nullable()->constrained()->onDelete('set null');
            $table->string('vaccine_name');
            $table->string('vaccine_batch');
            $table->integer('dose_number');
            $table->dateTime('vaccination_date');
            $table->dateTime('next_dose_date')->nullable();
            $table->string('vaccination_center');
            $table->string('healthcare_provider');
            $table->string('vaccination_proof')->nullable();
            $table->json('side_effects')->nullable();
            $table->json('notes')->nullable();
            $table->string('status')->default('pending'); // pending, approved, completed, rejected
            $table->string('nft_token_id')->nullable();
            $table->string('ipfs_hash')->nullable();
            $table->json('metadata')->nullable();
            $table->string('blockchain_transaction_hash')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vaccination_requests');
    }
}; 