<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('full_name');
            $table->string('contact');
            $table->string('email')->unique()->nullable();
            $table->string('login')->unique()->nullable();
            $table->string('role');
            $table->integer('agence_id')->unsigned()->nullable();
            $table->integer('localite_id')->unsigned()->nullable();
            $table->integer('depot_id')->unsigned()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->datetime('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('confirmation_token')->nullable();
            $table->boolean('statut_compte')->default(1);
            $table->boolean('etat_user')->default(0);
            $table->dateTime('deleted_at')->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('created_by')->unsigned()->nullable();
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
