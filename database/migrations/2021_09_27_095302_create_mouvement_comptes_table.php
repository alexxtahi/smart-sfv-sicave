<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMouvementComptesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mouvement_comptes', function (Blueprint $table) {
            $table->id();
            $table->integer('compte_id');
            $table->bigInteger('initiale')->unsigned()->nullable();
            $table->bigInteger('entree')->unsigned()->nullable();
            $table->bigInteger('sortie')->unsigned()->nullable();
            $table->dateTime('date_mouvement');
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
        Schema::dropIfExists('mouvement_cartes');
    }
}
