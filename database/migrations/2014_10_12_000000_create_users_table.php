<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('role', User::$roles)->default(User::USER_ROLE);
            $table->string('name');
            $table->string('surname');
            $table->string('phone');
            $table->date('birth_date');
            $table->string('city');
            $table->string('email')->unique();
            $table->string('photo')->default("/storage/photos/default.png");
            $table->unsignedBigInteger('referral');
            $table->foreign('referral')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->unsignedInteger('available_amount')->default(0);
            $table->unsignedInteger('current_amount')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
