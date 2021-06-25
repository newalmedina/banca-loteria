<?php

namespace App\Modules\Idiomas\Tests\Feature;

use App\Models\Idioma;
use Tests\AdminTestCase;
use Illuminate\Support\Facades\DB;

// Para llamar directamente a esta prueba
// ./vendor/bin/phpunit app/Modules/Idiomas/Tests/Feature/IdiomasTest.php
//  ./vendor/bin/phpunit --filter IdiomasTest

class IdiomasTest extends AdminTestCase
{
    /** @test */
    public function usuario_no_autenticado_no_puede_acceder()
    {
        // Debo ser invitado
        $this->assertGuest();

        // Llamamos a la ruta de listado
        $response = $this->get(route('idiomas.index'));

        // La respuesta debe ser 200 o 302
        // Si solo hay admin hay una redirección por lo que la respuesta es 302
        $this->assertContains($response->getStatusCode(), array(200,302));

        // Miramos si la vista es la correcta
        $response->assertRedirect('/admin/login');
    }

    /** @test */
    public function usuario_puede_leer_idiomas()
    {
        // Tenemos al menos un objeto en la base de datos
        $idioma = factory('App\Models\Idioma')->create(
            ['name:es' => $this->faker->word()]
        );

        // El usuario visita el listado de objetos
        $response = $this->actingAs($this->user)->post('admin/idiomas/list');


        // Veremos en el listado el objeto creado
        $response->assertSee($idioma->code)
            ->assertSee($idioma->name);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_crear_un_idioma()
    {
        // Debo ser invitado
        $this->assertGuest();

        // Creamos un objeto pero no lo insertamos
        $idioma = factory('App\Models\Idioma')->make();

        //  El usuario crea el objeto y deberia enviarnos a login
        $this->post('admin/idiomas', $idioma->toArray())
            ->assertRedirect('/admin/login');
    }

    /** @test */
    public function usuario_puede_crear_un_idioma()
    {
        // Limpiamos cualquier dato anterior
        DB::table('idiomas')->delete();

        $name = $this->faker->word();
        // Creamos un objeto pero no lo insertamos
        $idioma = factory('App\Models\Idioma')->make(
            ['name:es' => $name]
        );

        $idioma_array =  $idioma->toArray();
        $idioma_array['lang']['es']['name'] = $name;

        //  El usuario crea el objeto
        $this->actingAs($this->user)
            ->post('admin/idiomas', $idioma_array)
            ->assertSessionDoesntHaveErrors();


        // Leemos el numero de objetos en la base de datos
        $this->assertEquals(1, Idioma::all()->count());

        // Verificamos que en la base de datos hay un registro con los datos creados
        $this->assertDatabaseHas('idiomas', [
            'code' => $idioma->code
        ])->assertDatabaseHas('idioma_translations', [
            'name' =>$name
        ]);
    }

    /** @test */
    public function usuario_no_puede_crear_un_idioma_datos_incompletos()
    {
        // Creamos un objeto con datos incompletos
        $idioma = new Idioma();

        //  El usuario crea el objeto pero le dice que faltan los required
        $this->actingAs($this->user)->post('admin/idiomas', $idioma->toArray())->assertSessionHasErrors([
                "active",
                "code",
                "lang.es.name",
                "default"
            ]);
    }

    /** @test */
    public function usuario_puede_editar_un_idioma()
    {
        // Tenemos al menos un objeto en la base de datos
        $idioma = factory('App\Models\Idioma')->create(
            ['name:es' => $this->faker->word()]
        );


        // El usuario edita el objeto
        $response = $this->actingAs($this->user)->get('admin/idiomas/'.$idioma->id."/edit");

        // Vemos que se ven los datos
        $response->assertSee($idioma->code);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_editar_un_idioma()
    {

        // Debo ser invitado
        $this->assertGuest();

        // Creamos un objeto
        $idioma = factory('App\Models\Idioma')->create(
            ['name:es' => $this->faker->word()]
        );

        //  El usuario crea el objeto y deberia enviarnos a login
        $response = $this->put('/admin/idiomas/'.$idioma->id, $idioma->toArray())
            ->assertRedirect('/admin/login');

        $this->assertContains($response->getStatusCode(), array(200,302));
    }

    /** @test */
    public function usuario_puede_modificar_un_idioma()
    {
        // Creamos un objeto
        $name = $this->faker->word();
        $idioma = factory('App\Models\Idioma')->create(
            ['name:es' => $name]
        );

        // Modificamos algunos datos
        $idioma->code = $this->faker->name;

        $idioma_array =  $idioma->toArray();
        $idioma_array['lang']['es']['name'] = $name;


        //  El usuario crea el objeto
        $this->actingAs($this->user)->put('/admin/idiomas/'.$idioma->id, $idioma_array);

        // Verificamos que en la base de datos hay un registro con los datos modificados
        $this->assertDatabaseHas(
            'idiomas',
            [
                'id' => $idioma->id,
                "active" => $idioma->active,
                "code" => config('app.locale'),
                "default" => $idioma->default
            ]
        )->assertDatabaseHas(
            'idioma_translations',
            [
                'name' => $idioma->name
            ]
        );
    }


    /** @test */
    public function usuario_no_puede_modificar_un_idioma_datos_incompletos()
    {
        // Creamos un objeto
        $idioma = factory('App\Models\Idioma')->create(
            ['name:es' => $this->faker->word()]
        );

        // Modificamos algunos datos
        $idioma->active = "";
        $idioma->code = "";
        $idioma->name = "";
        $idioma->default = "";


        //  El usuario crea el objeto pero le dice que faltan los required
        $this->actingAs($this->user)->post('admin/idiomas', $idioma->toArray())
            ->assertSessionHasErrors([
                "active",
                "code",
                "lang.es.name",
                "default"
            ]);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_borrar_un_idioma()
    {
        // Debo ser invitado
        $this->assertGuest();

        // Creamos un objeto
        $idioma = factory('App\Models\Idioma')->create();

        //  El usuario crea el objeto y deberia enviarnos a login
        $response =  $this->delete('/admin/idiomas/'.$idioma->id)
            ->assertRedirect('/admin/login');

        // Verificamos que en la base de datos sigue estando el registro
        $this->assertDatabaseHas(
            'idiomas',
            [
                'id' => $idioma->id
            ]
        );

        $this->assertContains($response->getStatusCode(), array(200,302));
    }

    /** @test */
    public function usuario_puede_borrar_un_idioma()
    {
        // Creamos un objeto
        $idioma = factory('App\Models\Idioma')->create(
            ['name:es' => $this->faker->word()]
        );

        //  El usuario crea el objeto y deberia enviarnos a login
        $this->actingAs($this->user)->delete('/admin/idiomas/'.$idioma->id) ;

        // Verificamos que en la base de datos no esta el registro
        $this->assertDatabaseMissing('idiomas', ['id'=> $idioma->id]);
    }
}
