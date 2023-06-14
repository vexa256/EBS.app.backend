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
            $table->string('SignalID');
            $table->string('EbsSignalCategoryID');
            $table->string('UserID');
            $table->string('EbsType');
            $table->string('SignalNumber');
            $table->string('WardID');



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
