<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id('loan_id');
            $table->string('lender_name');
            $table->date('loan_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('outstanding_balance', 10, 2);
            $table->date('due_date')->nullable();
            $table->decimal('interest_rate', 5, 2)->default(0.0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
