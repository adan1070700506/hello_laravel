<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    //注册
    public function create() {
        return view('users.create');
    }
    
    //个人页
    public function show(User $user) {
        return view('users.show',compact('user'));
    }
    
    //注册处理
    
    public function store(Request $request) {
        
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        session()->flash('success','欢迎！');
        return redirect()->route('users.show',[$user->id]);
    }
}
