<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('customer_debits', function (Blueprint $table) {
            $table->id('debit_id');
            $table->unsignedBigInteger('mauzo_id'); 
            $table->string('customer_name');
            $table->string('phone')->nullable();
            $table->decimal('total_amount', 10, 2); 
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2); 
            $table->timestamps();

            $table->foreign('mauzo_id')->references('mauzo_id')->on('mauzo')->onDelete('cascade');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('customer_debits');
    }
};
