<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\AdminTestCase;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

// ./vendor/bin/phpunit tests/Unit/UserTest.php

class UserTest extends AdminTestCase
{

    /** @test */
    public function usuario_puede_ser_obtenido_por_username()
    {
        $createdUser = factory(User::class)->create(['username' => 'janedoe']);

        $foundUser = User::findByUserName('janedoe');


        $this->assertEquals($createdUser->id, $foundUser->id);
        $this->assertEquals($foundUser->username, 'janedoe');
    }
}
