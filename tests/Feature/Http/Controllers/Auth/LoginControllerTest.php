<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

// https://www.laraveltip.com/haciendo-pruebas-automatizadas-en-laravel/
// https://www.laraveltip.com/haciendo-pruebas-automatizadas-en-laravel-unit-test/
// https://medium.com/@DCzajkowski/testing-laravel-authentication-flow-573ea0a96318
// Para llamar directamente a esta prueba
// ./vendor/bin/phpunit tests/Feature/Http/Controllers/Auth/LoginControllerTest.php

class LoginControllerTest extends TestCase
{
    // Para usar la base de datos
    use RefreshDatabase, WithFaker;

    /** @test */
    public function login_muestra_formulario_de_admin_login()
    {
        // Llamamos a la ruta de login de administracion
        $response = $this->get(route('admin.login'));

        // La respuesta debe ser 200
        $response->assertStatus(200);

        // Miramos si la vista es la correcta
        $response->assertViewIs('modules.auth.admin_login');
    }

    /** @test */
    public function login_muestra_error_de_validacion()
    {
        // Llamamos a la ruta de login de administracion sin parametros
        $response = $this->post(route('admin.login'), []);

        // La respuesta debe ser 302 ya que es un error y esta redirigido
        $response->assertStatus(302);

        // Miramos si en la sesion hay un error del username
        $response->assertSessionHasErrors('username');
    }

    /** @test */
    public function usuario_no_puede_logarse_con_password_incorrecto()
    {
        // Cremos un usuario
        $user = factory(User::class)->create([
            'password' => bcrypt('i-love-laravel'),
        ]);

        // Accedemos al formulario de login y lanzamos un post con un usuario correcto y contraseña incorrecta
        $response = $this->from('/admin/login')->post('/admin/login', [
            'username' => $user->username,
            'password' => 'invalid-password',
        ]);

        // Volvemos a login
        $response->assertRedirect('/admin/login');
        // Tenemos en la sesion un error de usuario
        $response->assertSessionHasErrors('username');
        // Tiene el usuario anterior
        $this->assertTrue(session()->hasOldInput('username'));
        // No debe tener la contraseña
        $this->assertFalse(session()->hasOldInput('password'));
        // Seguimos siendo invitados
        $this->assertGuest();
    }

    /** @test */
    public function hacemos_login_y_redirigimos_al_usuario_a_home_admin()
    {
        // Debo ser invitado
        $this->assertGuest();

        // Creamos un usuario que debe estar confirmado y permitir acceso
        $user = factory(User::class)->create([
            'password' => bcrypt($password = 'i-love-laravel'),
            'confirmed' => true,
            'active' => true
        ]);

        // Llamamos a la ruta de login de administracion con los datos correctos
        $response = $this->post('/admin/login', [
            'username' => $user->username,
            'password' => $password,
        ]);

        // No debe haber errores en la sesion
        $response->assertSessionHasNoErrors();

        // Debe haber una redireccion a home
        $response->assertStatus(302);
        // La respuesta debe ser la pagina de home de admin
        $response->assertRedirect('admin');

        // Verificamos que estemos autenticados con el usuario
        $this->assertAuthenticatedAs($user);

    }

    /** @test */
    public function usuario_no_puede_ver_login_si_autenticado()
    {
        // Creamos un usuario
        $user = factory(User::class)->make();

        // Si soy usuario y accedo a admin login
        $response = $this->actingAs($user)->get('/admin/login');

        // Verificamos que estemos autenticados con el usuario
        $this->assertAuthenticatedAs($user);

        // Me redirige a home
        $response->assertRedirect('/');
    }

}
