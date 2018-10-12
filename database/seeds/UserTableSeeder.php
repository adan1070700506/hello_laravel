<?php

use Illuminate\Database\Seeder;
use App\Models\User;
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        
        $users = factory(User::class)->times(50)->make();
        User::insert($users->makeVisible(['password', 'remember_token'])->toArray());
        $user = User::find(1);
        $user->name = 'sven';
        $user->is_admin = true;
        $user->activated = true;
        $user->email = '1070700506@qq.com';
        $user->password = bcrypt('123123');
        $user->save();
    }
}
