<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Para llamar directamente a esta prueba
// ./vendor/bin/phpunit tests/Feature/ExampleTest.php

class ExampleTest extends TestCase
{
    // Para usar la base de datos
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $response = $this->get('/');

        // Si solo hay admin hay una redirección por lo que la respuesta es 302
        $this->assertContains($response->getStatusCode(), array(200,302));
        //$response->assertStatus(200);
    }
}
