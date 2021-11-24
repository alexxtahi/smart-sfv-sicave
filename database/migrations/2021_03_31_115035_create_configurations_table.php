<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('nom_compagnie');
            $table->string('commune_compagnie');
            $table->string('nom_responsable');
            $table->string('contact_responsable');
            $table->string('logo')->nullable();
            $table->string('cellulaire')->nullable();
            $table->string('telephone_fixe')->nullable();
            $table->string('telephone_faxe')->nullable();
            $table->string('site_web_compagnie')->nullable();
            $table->string('adresse_compagnie')->nullable();
            $table->string('email_compagnie')->nullable();
            $table->string('type_compagnie')->nullable();
            $table->bigInteger('capital')->nullable();
            $table->string('rccm')->nullable();
            $table->string('ncc')->nullable();
            $table->string('numero_compte_banque')->nullable();
            $table->string('nc_tresor')->nullable();
            $table->string('banque')->nullable();
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
        Schema::dropIfExists('configurations');
    }
}
