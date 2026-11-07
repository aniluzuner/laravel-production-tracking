<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller{
  public function GetEmployees(){
    $calisanlar = User::where('role', 'Çalışan')->get();
    return response()->json($calisanlar);
  }
}
