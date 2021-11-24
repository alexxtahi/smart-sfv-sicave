<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMouvementStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mouvement_stocks', function (Blueprint $table) {
            $table->id();
            $table->date('date_mouvement');
            $table->date('date_peremption')->nullable();
            $table->integer('article_id')->unsigned();
            $table->integer('depot_id')->unsigned();
            $table->integer('unite_id')->unsigned()->nullable();
            $table->integer('quantite_initiale')->unsigned()->default(0);
            $table->integer('quantite_approvisionnee')->unsigned()->default(0);
            $table->integer('quantite_vendue')->unsigned()->default(0);
            $table->integer('quantite_destocker')->unsigned()->default(0);
            $table->integer('quantite_transferee')->unsigned()->default(0);
            $table->integer('quantite_retoutnee')->unsigned()->default(0);
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
        Schema::dropIfExists('mouvement_stocks');
    }
}
