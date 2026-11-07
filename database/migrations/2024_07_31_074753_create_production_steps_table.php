<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionStepsTable extends Migration{
  public function up(){
    Schema::create('production_steps', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('production_id');
      $table->string('title');
      $table->integer('time');
      $table->timestamps();

      $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
    });
  }

  public function down(){
    Schema::dropIfExists('production_steps');
  }
}
