<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMashuduTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mashudu', function (Blueprint $table) {
            $table->id('mashudu_id'); // Auto-incrementing primary key
            $table->date('tarehe'); // Date field for tarehe
            $table->decimal('mashudu', 10, 2); // Field for mashudu (quantity in kilograms)
            $table->decimal('price', 10, 2); // Field for price (user input)
            $table->decimal('discount', 10, 2)->nullable(); // Nullable discount field
            $table->unsignedBigInteger('alizeti_id'); // Foreign key column for alizeti
            $table->unsignedBigInteger('user_id');    // Foreign key column for users

            // Foreign key constraints
            $table->foreign('alizeti_id', 'fk_mashudu_alizeti')
                  ->references('alizeti_id')->on('alizeti')->onDelete('cascade');
                  
            $table->foreign('user_id', 'fk_mashudu_users')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->timestamps(); // created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mashudu');
    }
}
