<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_facture')->nullable();
            $table->string('numero_ticket')->nullable();
            $table->dateTime('date_vente');
            $table->integer('depot_id')->nullable();
            $table->integer('client_id')->unsigned()->nullable();
            $table->integer('caisse_ouverte_id')->unsigned()->nullable();
            $table->bigInteger('acompte_facture')->default(0);
            $table->bigInteger('montant_a_payer')->default(0);
            $table->bigInteger('montant_payer')->default(0);
            $table->bigInteger('montant_carte_fidelite')->default(0);
            $table->integer('moyen_reglement_id')->nullable();
            $table->boolean('proformat')->default(0);
            $table->boolean('attente')->default(0);
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
        Schema::dropIfExists('ventes');
    }
}
