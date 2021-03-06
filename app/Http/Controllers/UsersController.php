<?php

namespace App\Http\Controllers;

use Mail;
use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\ResetPassword;

class UsersController extends Controller {

    public function __construct() {
        $this->middleware('auth', [
            'except' => [
                'show', 'create', 'store', 'index', 'confirmEmail'
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
        $statuses = $user->statuses()
                ->orderBy('created_at', 'desc')
                ->paginate(30);
        return view('users.show', compact('user', 'statuses'));
    }

    /**
     * 注册处理
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
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
        //Auth::login($user);
        //session()->flash('success', '欢迎！');
        //return redirect()->route('users.show', [$user->id]);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
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

    /**
     * 发送激活邮件
     * @param $user 用户实例
     */
    protected function sendEmailConfirmationTo($user) {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = '1070700506@yousails.com';
        $name = '魔剑客';
        $to = $user->email;
        $subject = "感谢注册 Sample 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }

    /**
     * 账号激活
     * @param $token 激活码
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmEmail($token) {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }


    public function sendPasswordResetNotification($token) {
        $this->notify(new ResetPassword($token));
    }

    public function followings(User $user){
        $users = $user->followings()->paginate(30);
        $title = "关注的人";
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user){
        $users = $user->followings()->paginate(30);
        $title = "关注的人";
        return view('users.show_follow', compact('users', 'title'));
    }
}
