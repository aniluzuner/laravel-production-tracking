<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller{
  public function Users(){
    $users = User::where('role','!=','Silindi')->get();
    return view('Admin/users', compact('users'));
  }

  public function GetUser($id){
    $user = User::find($id);
    
    if (!$user)
      return response()->json(['error' => 'User not found.'], 404);
        
    return response()->json($user);
  }

  public function AddUser(Request $request){
    $validatedData = $request->validate([
      'username' => 'required|string|max:255|unique:users',
      'fullname' => 'required|string|max:255',
      'email' => 'nullable|string|max:255',
      'phone' => 'nullable|string|max:20',
      'role' => 'required|string|max:50',
      'password' => 'required|string|min:8',
    ]);

    $user = User::create([
      'username' => $validatedData['username'],
      'fullname' => $validatedData['fullname'],
      'email' => $validatedData['email'],
      'phone' => $validatedData['phone'],
      'role' => $validatedData['role'],
      'password' => $validatedData['password']
    ]);
    
    return redirect()->route('calisanlar')->with('success', 'Kullanıcı başarıyla eklendi.');
  }

  public function EditUser(Request $request){
    $validatedData = $request->validate([
      'id' => 'required|integer',
      'username' => 'required|string|max:255',
      'fullname' => 'required|string|max:255',
      'email' => 'nullable|string|max:255',
      'phone' => 'nullable|string|max:20',
      'role' => 'required|string|max:50',
      'password' => 'required|string|min:8',
    ]);

    $user = User::find($validatedData['id']);
    if (!$user)
        return response()->json(['error' => 'User not found.'], 404);

    $user->update([
      'username' => $validatedData['username'],
      'fullname' => $validatedData['fullname'],
      'email' => $validatedData['email'],
      'phone' => $validatedData['phone'],
      'role' => $validatedData['role'],
      'password' => $validatedData['password']
    ]);

    return redirect()->route('calisanlar')->with('success', 'Kullanıcı başarıyla güncellendi.');
  }

  public function DeleteUser($id){
    $user = User::find($id);
    if (!$user)
      return response()->json(['error' => 'User not found.'], 404);
    
    $user->update(['role' => "Silindi"]);

    return response()->json(['success' => 'Kullanıcı başarıyla silindi.']);
  }
}
