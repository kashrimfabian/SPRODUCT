<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('uchujaji', function (Blueprint $table) {
            $table->id('uchujaji_id');

            $table->unsignedBigInteger('alizeti_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');

            $table->date('tarehe');
            $table->decimal('mafuta_machafu', 10, 2);
            $table->decimal('mafuta_masafi', 10, 2)->nullable();
            $table->decimal('ugido', 10, 2)->nullable();

            $table->timestamps();

            // Explicit foreign key definitions
            $table->foreign('alizeti_id', 'fk_uchujaji_alizeti')->references('ali_id')->on('alizeti')->onDelete('cascade');

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('uchujaji');
    }
};
