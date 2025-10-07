<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->string('provider', 20); // stripe, razorpay
            $table->string('provider_payment_id')->nullable();
            $table->string('currency', 10)->default('INR');
            $table->unsignedBigInteger('amount'); // store in smallest currency unit (paise/cents)
            $table->string('status', 20)->default('pending'); // pending, succeeded, failed, refunded
            $table->json('meta')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamps();
            $table->index(['provider', 'provider_payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

