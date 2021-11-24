<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailInventairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_inventaires', function (Blueprint $table) {
            $table->id();
            $table->integer('article_id')->unsigned();
            $table->integer('unite_id')->unsigned();
            $table->integer('inventaire_id')->unsigned();
            $table->integer('quantite_denombree')->unsigned();
            $table->integer('quantite_en_stocke')->unsigned();
            $table->date('date_peremption')->nullable();
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
        Schema::dropIfExists('detail_inventaires');
    }
}
