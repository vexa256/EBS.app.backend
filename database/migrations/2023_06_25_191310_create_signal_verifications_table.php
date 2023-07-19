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
        Schema::create('signal_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('ReportID');
            $table->string('VerificationID');
            $table->string('HealthFacilityID');
            $table->string('VerifyingUserID');
            $table->string('ProvinceID');
            $table->string('DistrictID');
            $table->string('ConstituencyID');
            $table->string('WardID');
            $table->string('VillageID');
            $table->string('WhatIsTheSignalSource');
            $table->text('ShortDescriptionOfTheSignal');
            $table->date('DateOfOccurrence');

            $table->string('TheEventIsAPublicHealthThreatTo');
            $table->string('SignalVerificationDate');
            $table->string('DateOfInformingTheNextLevelForAction');
            $table->string('DateOfVerification');
            $table->string('DelayedVerificationStatus')
                ->default('pending verification');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signal_verifications');
    }
};
