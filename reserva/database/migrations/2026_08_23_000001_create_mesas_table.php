<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->string('ubicacion', 1);
            $table->unsignedSmallInteger('numero');
            $table->unsignedSmallInteger('capacidad');
            $table->timestamps();
            $table->unique(['ubicacion', 'numero']);
            $table->index(['ubicacion', 'capacidad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
