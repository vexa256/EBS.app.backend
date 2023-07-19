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
        Schema::create('event_response_forms', function (Blueprint $table) {
            $table->id();
            $table->string('ReportID');
            $table->string('ResponseID');
            $table->string('ResponseFiledByUserID');
            $table->string('ProvinceID');
            $table->string('DistrictID');
            $table->string('ConstituencyID');
            $table->string('WardID');
            $table->string('VillageID');
            $table->string('HealthFacilityID');
            $table->date('DateResponseTaken');
            $table->string('ResponseTakenOne');
            $table->string('ResponseTakenTwo')->nullable();
            $table->string('ResponseTakenThree')->nullable();
            $table->string('ResponseTakenFour')->nullable();
            $table->string('ResponseTakenFive')->nullable();
            $table->string('ResponseTakenSix')->nullable();
            $table->string('ResponseTakenSeven')->nullable();
            $table->string('OutComeOfResponse');
            $table->string('IfOngoingActionTaken')->nullable();
            $table->string('Escalate')->default('No');
            $table->date('IfYesEscalationDate')->nullable();
            $table->date('DateOfResponseReport');
            $table->text('AdditionalInformation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_response_forms');
    }
};
