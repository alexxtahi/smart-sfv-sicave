<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('objet_operation');
            $table->bigInteger('montant_operation')->unsigned();
            $table->integer('caisse_ouverte_id')->unsigned();
            $table->dateTime('date_operation');
            $table->string('type_operation');
            $table->string('concerne')->nullable();
            $table->integer('vente_id')->nullable();
            $table->integer('bon_commande_id')->nullable();
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
        Schema::dropIfExists('operations');
    }
}
