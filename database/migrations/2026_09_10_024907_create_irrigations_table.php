<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('irrigations', function (Blueprint $table) {
            $table->id();
            $table->float('suhu_udara')->default(0);
            $table->float('kelembapan_udara')->default(0);
            $table->float('kelembapan_tanah')->default(0);
            $table->float('suhu_tanah')->default(0);
            $table->string('mode')->default('otomatis');
            $table->string('status_pompa')->default('OFF');
            $table->timestamp('pompa_dinyalakan_pada')->nullable();
            $table->integer('durasi_penyiraman')->default(5);
            $table->time('jadwal_pagi')->default('06:00:00');
            $table->time('jadwal_sore')->default('16:30:00');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('irrigations');
    }
};