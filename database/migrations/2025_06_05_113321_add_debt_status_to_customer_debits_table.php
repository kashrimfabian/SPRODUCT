<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('customer_debits', function (Blueprint $table) {
            $table->string('debt_status')->default('not payed')->after('balance');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('customer_debits');
    
    }
};
