<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('report_date');
            $table->string('tugas_harian');
            $table->text('deskripsi');
            $table->text('kendala')->nullable();
            $table->enum('status', ['Dalam Pengerjaan', 'Selesai']);
            $table->text('catatan_tambahan')->nullable();
            $table->string('bukti_file')->nullable();
            $table->string('file_location')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk performa
            $table->index('report_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};