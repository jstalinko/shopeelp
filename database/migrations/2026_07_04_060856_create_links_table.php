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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_name');
            $table->string('target_urls');
            $table->string('campaign_method'); // redirect, landingpage.
            $table->string('template');
            $table->text('lock_country')->nullable();
            $table->text('lock_device')->nullable(); // mobile , desktop, tablet..
            $table->text('lock_browser')->nullable(); // opera,chrome,fbbrowser,Samsung Internet..

            $table->integer('clicks')->default(0);
            $table->boolean('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
