<?php

use Illuminate\Database\Seeder;
use App\User as User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        $users = array(
        	['name' => 'Agust Lofianto', 'email' => 'agust.lofianto@gmail.com', 'password' => Hash::make('1234')],
        );

        foreach ($users as $user) {
        	User::create($user);
        }
    }
}
