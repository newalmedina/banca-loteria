<?php

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTestCase extends TestCase
{
    // Para usar la base de datos
    use RefreshDatabase, WithFaker;


    protected $user;

    protected function setUp(): void {

        parent::setUp();

        Artisan::call('migrate');
        Artisan::call('db:seed', ['--class' => 'DatabaseSeeder']);

        $this->user = User::where('username', 'admin')->first();
    }

}
