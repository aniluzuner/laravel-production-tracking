<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
  public function up(): void{
    Schema::table('production_records', function (Blueprint $table) {
      $table->integer('elapsed_time')->nullable();
      $table->string('status')->nullable();
      $table->dropColumn('end');
    });
  }

  public function down(): void{
    Schema::table('production_records', function (Blueprint $table) {
      $table->dropColumn('elapsed_time');
      $table->dropColumn('status');
      $table->timestamp('end')->nullable();
    });
  }
};
