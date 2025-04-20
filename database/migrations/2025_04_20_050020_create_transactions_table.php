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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->references('id')->on('payments');
            $table->unsignedBigInteger('amount')->nullable();
            $table->string('gateway');              //zarinpal,shepa,zibal
            $table->string('token');
            $table->string('link');
            $table->enum('status',['pending','success','failed','reversed'])->default('pending');
            $table->string('tracking_code')->nullable();
            $table->text('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
