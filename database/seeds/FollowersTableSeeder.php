<?php

use Illuminate\Database\Seeder;
use App\Models\User;
class FollowersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        
        $users = User::all();
        $user = User::first();
        $user_id = $user->id;

        //去除1以外的用户集合
        $followers= $users->slice(1);
        $follower_ids = $followers->pluck('id')->toArray();

        $user->follow($follower_ids);

        foreach ($followers as $follower){
            $follower->follow($user_id);
        }
    }
}
