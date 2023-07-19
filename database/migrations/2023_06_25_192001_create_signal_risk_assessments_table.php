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
        Schema::create('signal_risk_assessments', function (Blueprint $table) {
            $table->string('ReportID');
            $table->string('RiskAssessmentID');
            $table->string('RiskAssessmentByUserID');
            $table->string('HealthFacilityID');
            $table->string('ProvinceID');
            $table->string('DistrictID');
            $table->string('ConstituencyID');
            $table->string('WardID');
            $table->string('VillageID');
            $table->date('RiskAssessmentDate');
            $table->date('StartDate');
            $table->text('BriefDescription');
            $table->integer('AffectedPeopleOrAnimals');
            $table->integer('CasesHospitalized');
            $table->integer('DeadHumansOrAnimals')->nullable();
            $table->string('IsCauseKnown')->nullable();
            $table->string('Cause')->nullable();
            $table->string('LabSamplesTaken')->nullable();
            $table->string('SampleCollectionDate')->nullable();
            $table->text('LabResults')->nullable();
            $table->date('LabResultsReceivedDate')->nullable();
            $table->string('NewCasesBeingReported')->nullable();
            $table->string('SameAreaOrOtherAreasAffected')->nullable();
            $table->string('EventSettingPromotesTransmission')->nullable();
            $table->string('AdditionalInformation')->nullable();
            $table->string('EventRiskClassification')->nullable();
            $table->string('RecommendedResponse')->nullable();
            $table->string('RiskAssessmentDelay')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signal_risk_assessments');
    }
};
