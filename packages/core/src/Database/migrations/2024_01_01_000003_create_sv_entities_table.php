<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sv_entities', function (Blueprint $table) {
            $table->id();
            $table->string('driver')->index();
            $table->string('entity_type')->index();
            $table->string('entity_id')->index();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->unique(['driver', 'entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sv_entities');
    }
};
