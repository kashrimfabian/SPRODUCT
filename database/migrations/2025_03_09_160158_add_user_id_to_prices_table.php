<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('prices_id'); // Assuming 'prices_id' is your primary key
            $table->foreign('user_id','fk_prices_users')->references('id')->on('users')->onDelete('cascade');
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};