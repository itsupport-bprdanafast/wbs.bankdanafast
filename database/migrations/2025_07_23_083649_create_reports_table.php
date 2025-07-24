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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique()->index();
            $table->enum('type', ['pengaduan', 'gratifikasi', 'saran_keluhan']);
            $table->string('reporter_name')->nullable();
            $table->string('reporter_email')->nullable();
            $table->string('reporter_phone')->nullable();
            $table->text('description');
            $table->string('reported_employees')->nullable();
            $table->json('attachments')->nullable();
            $table->date('date_incidents')->nullable();
            $table->decimal('gratification_value', 12, 2, true)->nullable();
            $table->enum('status', ['pending', 'reviewing', 'investigating', 'resolved', 'rejected'])->default('pending');
            $table->longText('admin_notes')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
