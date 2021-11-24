<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvoirDoitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avoir_doits', function (Blueprint $table) {
            $table->id();
            $table->date('date_operation');
            $table->bigInteger('montant');
            $table->integer('bon_commande_id')->unsigned()->nullable();
            $table->integer('vente_id')->unsigned()->nullable();
            $table->bigInteger('regler')->unsigned()->default(0);
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
        Schema::dropIfExists('avoir_doits');
    }
}
