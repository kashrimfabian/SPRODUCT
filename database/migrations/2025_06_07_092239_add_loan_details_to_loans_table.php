<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('original_loan_amount', 10, 2)->after('loan_date')->default(0.00); 
            $table->string('loan_status')->default('not paid')->after('notes');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
