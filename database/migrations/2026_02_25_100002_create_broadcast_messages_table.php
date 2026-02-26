<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('phone');
            $table->text('message');
            $table->string('waba_message_id')->nullable();
            $table->string('delivery_status')->default('sent'); // sent, delivered, failed
            $table->string('source')->default('kaspi'); // kaspi, manual
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_messages');
    }
};
