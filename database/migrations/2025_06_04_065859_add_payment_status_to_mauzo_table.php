<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('mauzo', function (Blueprint $table) {
            $table->string('payment_status')->nullable()->after('is_confirmed');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('mauzo');
    
    }
};
