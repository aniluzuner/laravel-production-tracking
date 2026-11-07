<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller{
  public function Login(Request $request){
    $request->validate([
      'username' => 'required|string',
      'password' => 'required|string',
    ]);

    $user = User::where('username', $request->username)->first();

    if ($user && $request->password === $user->password) {
      Auth::login($user);
      return redirect()->route('uretim');
    }

    return redirect()->route('login')
                     ->withErrors(['username' => 'Kullanıcı adı ya da şifre yanlış!'])
                     ->withInput();
  }

  public function Logout(){
    Auth::logout();
    return redirect()->route('/');
  }
}
