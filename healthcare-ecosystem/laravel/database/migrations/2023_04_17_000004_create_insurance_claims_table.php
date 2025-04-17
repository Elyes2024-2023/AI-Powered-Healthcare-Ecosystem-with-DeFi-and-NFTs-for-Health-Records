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
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('insurance_policy_id')->constrained()->onDelete('cascade');
            $table->string('claim_number')->unique();
            $table->string('smart_contract_address')->nullable();
            $table->decimal('claim_amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->text('description');
            $table->json('supporting_documents')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
}; 