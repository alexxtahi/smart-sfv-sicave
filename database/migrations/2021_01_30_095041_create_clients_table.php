<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name_client');
            $table->string('code_client');
            $table->string('contact_client');
            $table->integer('nation_id')->unsigned();
            $table->integer('regime_id')->unsigned()->nullable();
            $table->string('email_client')->nullable();
            $table->bigInteger('plafond_client')->unsigned()->default(0);
            $table->string('compte_contribuable_client')->nullable();
            $table->string('boite_postale_client')->nullable();
            $table->string('adresse_client')->nullable();
            $table->string('fax_client')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('created_by')->unsigned()->nullable();
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
        Schema::dropIfExists('clients');
    }
}
