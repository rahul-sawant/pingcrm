<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('endpoints', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uuid');
            $table->string('title');
            $table->string('location');
            $table->string('stream_key');
            $table->string('ip_addr');
            $table->string('port');
            $table->string('type')->default('custom-golang');
            $table->string('organization_id')->references('id')->on('organizations');
            $table->datetime('last_online')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endpoints');
    }
};
