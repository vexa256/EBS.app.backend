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
        Schema::create('ebs_signals', function (Blueprint $table) {
            $table->id();
            $table->string('ReportID');
            $table->string('EbsType');
            $table->string('EbsSignal')->unique();
            $table->integer('SignalNumber')->unique();
            $table->string('SignalID')->unique();
            $table->string('EbsSignalCategoryID');
            $table->string('DisplayPicture');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebs_signals');
    }
};
