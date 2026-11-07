<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductionStep extends Model{
  protected $table = 'production_steps';

  protected $fillable = [
    'production_id',
    'title',
    'time',
  ];

  public function production(){
    return $this->belongsTo(Production::class);
  }

  public function records(){
    return $this->hasMany(ProductionRecord::class, 'step_id');
  }
}
