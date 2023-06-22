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
        Schema::create('inat_observations', function (Blueprint $table) {
            $table->id();
            $table->string('observed_on')->nullable();
            $table->string('inat_created_at')->nullable();
            $table->string('inat_updated_at')->nullable();
            $table->string('quality_grade')->nullable();
            $table->string('license')->nullable();
            $table->text('image_url')->nullable();
            $table->integer('num_identification_agreements')->nullable();
            $table->integer('num_identification_disagreements')->nullable();
            $table->string('oauth_application_id')->nullable();
            $table->integer('nmw')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('taxa_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("inat_users");
            $table->foreign("taxa_id")->references("id")->on("inat_taxas");
            $table->foreign("location_id")->references("id")->on("inat_locations");

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inat_observations');
    }
};
