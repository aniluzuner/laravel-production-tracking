<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductionRecord extends Model{
  protected $table = 'production_records';

  protected $fillable = [
    'mps',
    'step_id',
    'quantity',
    'start',
    'elapsed_time',
    'employees',
    'controller_id',
    'description',
    'status'
  ];

  public function step(){
    return $this->belongsTo(ProductionStep::class, 'step_id');
  }

  public function controller(){
    return $this->belongsTo(User::class, 'controller_id');
  }
}
