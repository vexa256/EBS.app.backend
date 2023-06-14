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
        Schema::create('ebs_signal_categories', function (Blueprint $table) {
            $table->id();
            $table->string('EbsSignalCategoryID');
            $table->string('EbsSignalCategory');
            $table->string('CategoryDescription');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebs_signal_categories');
    }
};
