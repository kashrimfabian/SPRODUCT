<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('customer_debit_payments', function (Blueprint $table) {
            $table->id('debitpay_id');
            $table->unsignedBigInteger('debit_id');
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('debit_id')->references('debit_id')->on('customer_debits')->onDelete('cascade');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('customer_debit_payments');
    }
};
