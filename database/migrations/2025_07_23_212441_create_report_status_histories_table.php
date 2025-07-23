<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'reviewing', 'investigating', 'resolved', 'rejected']);
            $table->text('notes')->nullable();
            $table->string('changed_by')->nullable(); // Admin yang mengubah
            $table->timestamps();

            $table->index(['report_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_status_histories');
    }
};
