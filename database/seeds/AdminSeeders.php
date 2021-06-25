<?php

use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker =  Faker::create('es_ES');
        DB::table('users')->delete();

        // Creamos un usuario controlado de administración y de frontend
        $user = array(
            'username' => 'admin',
            'email' => 'info@aduxia.com',
            'remember_token' =>  Str::random(10),
            'password' => Hash::make('admin'),
            'confirmed' => true,
            'email_verified_at' => now(),
            'active' => true,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        );

        $iduser = DB::table('users')->insertGetId( $user );

        $user_profile = array(
            'user_id' => $iduser,
            'first_name' => 'Administrador',
            'last_name' => 'CMS',
            'gender' =>  $faker->randomElement(['male', 'female']),
            'photo' => '',
            'phone' => $faker->phoneNumber(),
            'mobile' => $faker->phoneNumber(),
            'user_lang' => 'es',
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        );

        DB::table('user_profiles')->insert( $user_profile );
    }
}
