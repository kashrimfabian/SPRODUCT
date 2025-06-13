<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlizetiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alizeti', function (Blueprint $table) {
            $table->id('alizeti_id'); // Primary key with a custom name
            $table->date('tarehe'); // Date of record
            $table->foreignId('user_id') // Foreign key referencing the users table
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('batch_no')->unique(); // Unique batch number
            $table->decimal('al_kilogram', 10, 2); // Kilograms of alizeti
            $table->integer('gunia_total'); // Total gunias
            $table->decimal('price_per_kilo', 10, 2); // Price per kilo
            $table->decimal('total_price', 10, 2); // Total price
            $table->decimal('mafu_machafu', 10, 2)->nullable(); // Optional attribute
            $table->decimal('shudu', 10, 2)->nullable(); // Optional attribute
            $table->decimal('mafu_masafi', 10, 2)->nullable(); // Optional attribute
            $table->decimal('ugido', 10, 2)->nullable(); // Optional attribute
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alizeti');
    }
}
