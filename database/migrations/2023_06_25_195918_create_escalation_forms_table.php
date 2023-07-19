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
        Schema::create('escalation_forms', function (Blueprint $table) {
            $table->id();
            $table->string('ReportID');
            $table->string('ResponseID');
            $table->string('EscalationID');
            $table->string('HealthFacilityID');
            $table->string('EscalatingUserID');
            $table->string('ProvinceID');
            $table->string('DistrictID');
            $table->string('ConstituencyID');
            $table->string('WardID');
            $table->string('VillageID');
            $table->string('EscalatedToFacilityID');
            $table->string('EscalatedToProvinceID');
            $table->date('DateOfEscalation');
            $table->string('ReasonForEscalationOne');
            $table->string('ReasonForEscalationTwo')->nullable();
            $table->string('ReasonForEscalationThree')->nullable();
            $table->string('ReasonForEscalationFour')->nullable();
            $table->string('ReasonForEscalationFive')->nullable();
            $table->string('ReasonForEscalationSix')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalation_forms');
    }
};
