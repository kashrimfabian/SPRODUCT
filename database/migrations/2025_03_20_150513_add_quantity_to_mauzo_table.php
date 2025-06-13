<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('mauzo', function (Blueprint $table) {
        $table->decimal('quantity', 10, 2)->after('sale_type');
    });
}

public function down(): void
{
    Schema::dropIfExists('mauzo');
}

};
