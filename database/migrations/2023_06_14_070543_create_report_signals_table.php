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
        Schema::create('report_signals', function (Blueprint $table) {
            $table->id();
            $table->string('ReportID');
            $table->string('SignalID');
            $table->string('EbsSignalCategoryID');
            $table->string('UserID');
            $table->string('EbsType');
            $table->string('SignalNumber');
            $table->string('WardID');
            $table->string('ConstituencyID');
            $table->string('DistrictID');
            $table->string('ProvinceID');
            $table->string('HealthFacilityID');
            $table->string('AdministrativeLevel');
            $table->string('OfficialDesignation');
            $table->string('ChvGroupID')->nullable();
            $table->string('VillageID')->nullable();
        


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_signals');
    }
};
