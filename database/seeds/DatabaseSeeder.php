<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

         $this->call(UserTableSeeder::class);

        Model::reguard();
    }
}

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        \App\User::create(['email' => 'admin@admin.lo', 'username' => 'admin', 'password' => bcrypt('admin')]);
        \App\User::create(['email' => 'test1@test.lo', 'username' => 'test1', 'password' => bcrypt('123')]);
        \App\User::create(['email' => 'test2@test.lo', 'username' => 'test2', 'password' => bcrypt('123')]);
        \App\User::create(['email' => 'test3@test.lo', 'username' => 'test3', 'password' => bcrypt('123')]);
    }

}
