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
        Schema::create('display_messages', function (Blueprint $table) {
            $table->id();
            $table->string('eventName');
            $table->timestamp('eventTime');
            $table->index('eventTime');
            $table->string('formattedMessage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('display_messages');
    }
};
