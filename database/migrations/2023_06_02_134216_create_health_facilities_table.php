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
        Schema::create('health_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('HFID')->unique();
            $table->string('HealthFacilityName')->unique();
            $table->string('FacilityCategory');
            $table->string('ContactPersonsName');
            $table->string('ContactPersonsPhone');
            $table->string('BriefDescription');
            $table->string('WardID');
            $table->string('AdministrativeStructureLevel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_facilities');
    }
};
