<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionRecordsTable extends Migration{
  public function up(){
    Schema::create('production_records', function (Blueprint $table) {
      $table->id();
      $table->string('mps')->nullable();
      $table->unsignedBigInteger('step_id');
      $table->integer('quantity')->nullable();
      $table->timestamp('start')->nullable();
      $table->timestamp('end')->nullable();
      $table->unsignedBigInteger('controller_id');
      $table->unsignedBigInteger('user_id');
      $table->text('description')->nullable();
      $table->timestamps();

      $table->foreign('step_id')->references('id')->on('production_steps')->onDelete('cascade');
      $table->foreign('controller_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
  }

  public function down(){
    Schema::dropIfExists('production_records');
  }
}
