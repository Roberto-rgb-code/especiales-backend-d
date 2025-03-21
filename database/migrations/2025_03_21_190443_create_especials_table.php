<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('especials', function (Blueprint $table) {
      $table->id();
      $table->string('nombre');
      $table->text('descripcion');
      $table->string('categoria');
      $table->string('foto_path')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('especials');
  }
};