<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFieldTableUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_field_table_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('table_user_id');
            $table->foreign('table_user_id')
                ->references('id')->on('table_user')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedBigInteger('table_field_id');
            $table->foreign('table_field_id')
                ->references('id')->on('table_fields')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->text('answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_field_table_user');
    }
}
