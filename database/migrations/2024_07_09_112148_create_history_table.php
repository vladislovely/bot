<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('history', static function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->references('id')
                ->on('users');
            $table->text(column: 'message')->nullable();
            $table->string(column: 'action', length: 100)->nullable();
            $table->integer(column: 'chat_id');
            $table->integer(column: 'message_id');
            $table->json(column: 'additional_information')->nullable();
            $table->boolean(column: 'is_deleted')->default(value: false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history');
    }
};
