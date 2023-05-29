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
        Schema::create('inat_taxas', function (Blueprint $table) {
            $table->id();
            $table->string('scientific_name')->nullable();
            $table->string('common_name')->nullable();
            $table->string('order')->nullable();
            $table->string('superfamily')->nullable();
            $table->string('family')->nullable();
            $table->string('subfamily')->nullable();
            $table->string('tribe')->nullable();
            $table->string('subtribe')->nullable();
            $table->string('genus')->nullable();
            $table->string('species')->nullable();
            $table->string('subspecies')->nullable();
            $table->string('variety')->nullable();
            $table->string('form')->nullable();
            $table->string('rank')->nullable();
            $table->string('ancestry')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inat_taxas');
    }
};
