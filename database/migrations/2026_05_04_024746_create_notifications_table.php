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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipient_id');
            $table->string('recipient_type'); // 'customer','vendor','courier'
            $table->string('channel'); // 'email','sms','push','whatsapp'
            $table->string('subject');
            $table->text('content');
            $table->boolean('is_encrypted')->default(false);
            $table->boolean('is_logged')->default(false);
            $table->boolean('is_signed')->default(false);
            $table->boolean('sent')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
