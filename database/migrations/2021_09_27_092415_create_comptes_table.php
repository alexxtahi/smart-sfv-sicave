<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComptesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comptes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_compte');
            $table->integer('client_id')->nullable();
            $table->integer('fournisseur_id')->nullable();
            $table->integer('carte_id')->nullable();
            $table->bigInteger('entree')->unsigned()->nullable();
            $table->bigInteger('sortie')->unsigned()->nullable();
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
        Schema::dropIfExists('compte_clients');
    }
}
