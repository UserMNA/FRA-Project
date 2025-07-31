<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('name');
            $table->string('label');
            $table->float('confidence');
            $table->timestamp('scanned_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
