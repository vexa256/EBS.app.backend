<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('risk_assements', function (Blueprint $table) {
            $table->id();
            $table->string('ReportID');
            $table->string('RiskAssessmentStatus')->default('Not Verified');
            $table->string('DelayStatus')->default('Pending RiskAssessment');
            $table->string('RiskAssessmentDate')->nullable();
            $table->string('RiskAssessmentByUserID')->nullable();
            $table->text('RiskAssessmentDetails')->nullable();
            $table->text('RecommendedAction')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_assements');
    }
};
