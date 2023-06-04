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
            $table->string('Role')->unique();
            $table->string('EbsType')->unique();
            $table->string('BriefDescription');
            $table->string('EscalatesToRoleID');
            $table->string('OfficialDesignation');
            $table->string('AdministrativeLevel');
            $table->string('FacilityID');
            $table->timestamps();
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
