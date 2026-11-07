<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionsTable extends Migration{
  public function up(){
    Schema::create('productions', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->string('stock_code')->nullable();
      $table->timestamps();
    });
  }

  public function down(){
    Schema::dropIfExists('productions');
  }
}
