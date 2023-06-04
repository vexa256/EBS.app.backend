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
        Schema::create('chv_groups', function (Blueprint $table) {
            $table->id();
            $table->string('ProvinceID')->nullable();
            $table->string('DistrictID')->nullable();
            $table->string('ConstituencyID')->nullable();
            $table->string('WardID')->nullable();
            $table->string('VillageID');
            $table->string('ChvGroupID')->unique();
            $table->string('ChvGroupName')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chv_groups');
    }
};
