<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('health_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token_address');
            $table->string('token_name');
            $table->string('token_symbol');
            $table->decimal('balance', 36, 18)->default(0);
            $table->decimal('locked_amount', 36, 18)->default(0);
            $table->decimal('staked_amount', 36, 18)->default(0);
            $table->decimal('rewards_earned', 36, 18)->default(0);
            $table->dateTime('last_reward_calculation')->nullable();
            $table->json('token_metadata')->nullable();
            $table->string('blockchain_network');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('token_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_token_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // mint, burn, transfer, stake, unstake, reward
            $table->decimal('amount', 36, 18);
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->string('transaction_hash');
            $table->json('metadata')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('staking_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_token_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 36, 18);
            $table->decimal('apy_rate', 8, 4);
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->decimal('rewards_earned', 36, 18)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staking_positions');
        Schema::dropIfExists('token_transactions');
        Schema::dropIfExists('health_tokens');
    }
}; 