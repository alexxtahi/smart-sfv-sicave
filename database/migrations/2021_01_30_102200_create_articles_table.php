<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('libelle_article');
            $table->integer('categorie_id')->nullable();
            $table->integer('sous_categorie_id')->nullable();
            $table->string('code_barre')->nullable();
            $table->string('code_article')->nullable();
            $table->integer('rayon_id')->nullable();
            $table->integer('rangee_id')->nullable();
            $table->integer('unite_id')->nullable();
            $table->integer('taille_id')->nullable();
            $table->integer('param_tva_id')->nullable();
            $table->integer('taux_airsi_achat')->nullable();
            $table->integer('taux_airsi_vente')->nullable();
            $table->integer('poids_net')->nullable();
            $table->integer('poids_brut')->nullable();
            $table->integer('stock_mini')->nullable();
            $table->integer('stock_max')->nullable();
            $table->integer('volume')->nullable();
            $table->integer('prix_achat_ttc')->nullable();
            $table->integer('prix_vente_ttc_base')->nullable();
            $table->integer('prix_vente_en_gros_base')->nullable();
            $table->integer('prix_vente_demi_gros_base')->nullable();
            $table->integer('prix_pond_ttc')->nullable();
            $table->string('image_article')->nullable();
            $table->boolean('non_stockable')->default(0);
            $table->json('fournisseurs')->nullable();
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
        Schema::dropIfExists('articles');
    }
}
