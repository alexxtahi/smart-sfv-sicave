<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleDevisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_devis', function (Blueprint $table) {
            $table->id();
            $table->string('quantite');
            $table->integer('prix');
            $table->integer('devis_id');
            $table->integer('article_id')->unsigned();
            $table->integer('depot_id')->unsigned();
            $table->string('choix_prix')->nullable();
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
        Schema::dropIfExists('article_devis');
    }
}
