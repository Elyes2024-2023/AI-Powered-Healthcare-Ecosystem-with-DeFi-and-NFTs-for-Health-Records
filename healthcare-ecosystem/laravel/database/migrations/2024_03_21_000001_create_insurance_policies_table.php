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
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('policy_type');
            $table->decimal('coverage_amount', 12, 2);
            $table->decimal('premium_amount', 12, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->json('beneficiaries');
            $table->text('terms_and_conditions');
            $table->string('status')->default('active');
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
        Schema::dropIfExists('insurance_policies');
    }
}; 