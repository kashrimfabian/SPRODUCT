<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id('loanPy_id');
            $table->unsignedBigInteger('loan_id');
            $table->date('payment_date');
            $table->decimal('amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('loan_id')->references('loan_id')->on('loans')->onDelete('cascade');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};
