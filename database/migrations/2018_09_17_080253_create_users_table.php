<?php

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('stripe_active')->change();
            $table->integer('stripe_id')->change();
            $table->string('stripe_subscription')->change();
            $table->string('stripe_plan')->change();
            $table->string('last_four')->change();
            $table->string('trial_end_at')->change();
            $table->string('subscription_end_at')->change();
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
