<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable{
  use Notifiable;

  protected $table = 'users';

  protected $primaryKey = 'id';

  public $timestamps = true;

  protected $fillable = [
    'username',
    'fullname',
    'email',
    'phone',
    'role',
    'password'
  ];

  public function controlledRecords(){
    return $this->hasMany(ProductionRecord::class, 'controller_id');
  }
}
