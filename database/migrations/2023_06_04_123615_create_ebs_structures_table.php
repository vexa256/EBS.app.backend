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
        Schema::create('ebs_structures', function (Blueprint $table) {
            $table->id();
            $table->string('RoleID')->unique();
            $table->string('Role');
            $table->string('EbsType');
            $table->string('BriefDescription');
            $table->string('Name')->unique();
            $table->string('ChvGroupID');
            $table->string('PhoneNumber')->unique();
            $table->string('Email')->nullable();
            $table->string('EscalatesToRoleID');
            $table->string('ReportsToLevel');
            $table->string('OfficialDesignation');
            $table->string('AdministrativeLevel');
            $table->string('FacilityID');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebs_structures');
    }
};
