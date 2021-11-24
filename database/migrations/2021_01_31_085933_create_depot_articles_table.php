<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepotArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('depot_articles', function (Blueprint $table) {
            $table->id();
            $table->integer('article_id');
            $table->integer('depot_id');
            $table->integer('unite_id')->nullable();
            $table->integer('quantite_disponible')->default(0);
            $table->integer('prix_vente_detail')->default(0);
            $table->integer('prix_vente_demi_gros')->default(0);
            $table->integer('prix_vente_gros')->default(0);
            $table->date('date_peremption')->nullable();
            $table->date('date_debut_promotion')->nullable();
            $table->date('date_fin_promotion')->nullable();
            $table->boolean('promotion')->default(0);
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
        Schema::dropIfExists('depot_articles');
    }
}
