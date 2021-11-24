<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFournisseursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('full_name_fournisseur');
            $table->string('code_fournisseur');
            $table->string('contact_fournisseur');
            $table->string('email_fournisseur')->nullable();
            $table->string('compte_banque_fournisseur')->nullable();
            $table->string('compte_contribuable_fournisseur')->nullable();
            $table->string('boite_postale_fournisseur')->nullable();
            $table->string('adresse_fournisseur')->nullable();
            $table->string('fax_fournisseur')->nullable();
            $table->integer('nation_id')->unsigned();
            $table->integer('banque_id')->unsigned()->nullable();
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
        Schema::dropIfExists('fournisseurs');
    }
}
