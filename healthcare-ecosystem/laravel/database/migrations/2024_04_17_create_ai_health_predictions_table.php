<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_health_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('health_record_id')->constrained()->onDelete('cascade');
            $table->string('prediction_type');
            $table->json('prediction_result');
            $table->decimal('confidence_score', 5, 4);
            $table->json('input_parameters');
            $table->string('model_version');
            $table->json('recommendations');
            $table->json('risk_factors');
            $table->dateTime('next_checkup_date')->nullable();
            $table->json('ai_model_metadata');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_health_predictions');
    }
}; 