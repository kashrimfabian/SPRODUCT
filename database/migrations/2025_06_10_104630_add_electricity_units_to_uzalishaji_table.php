<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('uzalishaji', function (Blueprint $table) {
            $table->decimal('initial_unit', 10, 2)->default(0.0)->after('mashudu');
            $table->decimal('final_unit', 10, 2)->default(0.0)->after('initial_unit');
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('uzalishaji');
    }
};
