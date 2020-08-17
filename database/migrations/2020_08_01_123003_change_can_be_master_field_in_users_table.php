<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User;

class ChangeCanBeMasterFieldInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('can_be_master');

            $table
                ->boolean("is_master")
                ->default(false)
                ->after("remember_token");
        });

        $users = User::all();

        foreach ($users as $user) {
            $user->load(
                "subscriptions.transaction.main_service",
                "subscriptions.transaction.additional_services"
            );

            $user->is_master = $user->subscriptions->contains(function ($subscription) use ($user) {
                $main_package_is_master = $subscription->transaction->main_service !== null && $subscription->transaction->main_service->key === "full";

                $additional_packages_has_master = $subscription->transaction->additional_services->contains(function ($service) {
                    return $service->key === "game_practice";
                });

                return $main_package_is_master || $additional_packages_has_master;
            });

            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
