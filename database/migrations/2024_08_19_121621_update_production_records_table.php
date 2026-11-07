<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProductionRecordsTable extends Migration{
  public function up(){
    Schema::table('production_records', function (Blueprint $table) {
      $table->dropForeign(['user_id']);
      $table->dropColumn('user_id');
      $table->string('employees');
    });
  }

  public function down(){
    Schema::table('production_records', function (Blueprint $table) {
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->integer('user_id');
      $table->dropColumn('employees');
    });
  }
}

