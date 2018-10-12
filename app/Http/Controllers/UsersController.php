<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller {

    public function __construct() {
        $this->middleware('auth', [
            'except' => [
                'show', 'create', 'store', 'index'
            ],
        ]);
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    //注册
    public function create() {
        return view('users.create');
    }

    //个人页
    public function show(User $user) {
        return view('users.show', compact('user'));
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
        Auth::login($user);
        session()->flash('success', '欢迎！');
        return redirect()->route('users.show', [$user->id]);
    }

    public function edit(User $user) {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(User $user, Request $request) {
        $this->authorize('update', $user);
        $this->validate($request, [
            'name' => 'required|max:255',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flush("success", "更新成功");
        return redirect()->route('users.show', [$user->id]);
    }
    
    public function index() {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }
    
    public function destroy(User $user) {
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success', "删除成功");
        return back();
    }

}
