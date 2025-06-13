<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('inventory', function (Blueprint $table) {
        $table->softDeletes();  // Adds a 'deleted_at' column
    });
}

public function down()
{
    Schema::table('inventory', function (Blueprint $table) {
        $table->dropSoftDeletes();  // Removes the 'deleted_at' column
    });
}

};
