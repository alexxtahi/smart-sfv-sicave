<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleTransfertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_transferts', function (Blueprint $table) {
            $table->id();
            $table->integer('article_id')->unsigned();
            $table->integer('quantite_depart')->unsigned();
            $table->integer('quantite_reception')->unsigned();
            $table->integer('unite_depart')->unsigned();
            $table->integer('unite_reception')->unsigned();
            $table->integer('transfert_stock_id')->unsigned();
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
        Schema::dropIfExists('article_transferts');
    }
}
