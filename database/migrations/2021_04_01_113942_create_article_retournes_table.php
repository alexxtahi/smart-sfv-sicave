<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleRetournesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_retournes', function (Blueprint $table) {
            $table->id();
            $table->integer('retour_article_id');
            $table->integer('article_id');
            $table->integer('unite_id')->nullable();
            $table->integer('quantite_vendue')->default(0);
            $table->integer('quantite');
            $table->integer('prix_unitaire');
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
        Schema::dropIfExists('article_retournes');
    }
}
