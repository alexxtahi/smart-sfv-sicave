<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonCommandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bon_commandes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_bon');
            $table->string('scan_facture')->nullable();
            $table->date('date_bon');
            $table->date('date_reception')->nullable();
            $table->integer('fournisseur_id')->unsigned();
            $table->bigInteger('accompte')->unsigned()->default(0);

            //**Etat du bon** : 1=Brouillon, 2=Enregistré, 3=Refusé, 4=Receptionné, 5=Facturé,
            $table->boolean('etat')->default(1);

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
        Schema::dropIfExists('bon_commandes');
    }
}
