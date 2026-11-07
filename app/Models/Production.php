<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Production extends Model{
  protected $table = 'productions';
  
  protected $primaryKey = 'id';

  public $timestamps = true;

  protected $fillable = [
    'title',
    'stock_code',
  ];

  public function steps(){
    return $this->hasMany(ProductionStep::class, 'production_id');
  }
}
