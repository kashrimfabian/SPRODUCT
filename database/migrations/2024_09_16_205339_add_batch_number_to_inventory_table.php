<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchNumberToInventoryTable extends Migration
{
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->string('batch_number')->nullable(); // Add the batch_number column
            $table->foreign('batch_number')->references('batch_number')->on('production_batches'); // Add foreign key constraint
        });
    }

    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['batch_number']); // Drop foreign key
            $table->dropColumn('batch_number'); // Drop the batch_number column
        });
    }
}
