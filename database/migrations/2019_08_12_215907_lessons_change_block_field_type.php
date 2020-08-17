<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LessonsChangeBlockFieldType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('block');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->unsignedBigInteger('block_id');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->foreign('block_id')
                ->references('id')->on('blocks')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
