<?php

use App\Models\Role;
use Illuminate\Support\Str;
use Clavel\Basic\Models\Menu;
use Illuminate\Database\Seeder;
use Clavel\Basic\Models\MenuItem;
use Illuminate\Support\Facades\DB;
use Clavel\Basic\Models\MenuItemRoles;
use Clavel\Basic\Models\MenuItemTypes;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->delete();
        DB::table('menu_item_types')->delete();

        $type_ids = [];
        $menuType = new MenuItemTypes;
        $menuType->title = "Página";
        $menuType->slug = "pagina";
        $menuType->ico = "fa-file-o";
        $menuType->save();
        $type_ids[$menuType->slug] = $menuType->id;

        $menuType = new MenuItemTypes;
        $menuType->title = "Módulo";
        $menuType->slug = "modulo";
        $menuType->ico = "fa-dropbox";
        $menuType->save();
        $type_ids[$menuType->slug] = $menuType->id;

        $menuType = new MenuItemTypes;
        $menuType->title = "Enlace interno";
        $menuType->slug = "interno";
        $menuType->ico = "fa-link";
        $menuType->save();
        $type_ids[$menuType->slug] = $menuType->id;

        $menuType = new MenuItemTypes;
        $menuType->slug = "externo";
        $menuType->title = "Enlace externo";
        $menuType->ico = "fa-external-link";
        $menuType->save();
        $type_ids[$menuType->slug] = $menuType->id;

        $menuType = new MenuItemTypes;
        $menuType->slug = "system";
        $menuType->title = "Sistema";
        $menuType->ico = "fa-rocket";
        $menuType->save();
        $type_ids[$menuType->slug] = $menuType->id;

        // Primary menu
        $menu = new Menu;
        $menu->name = "Principal";
        $menu->slug = Str::slug("navbar");
        $menu->primary = 1;
        $menu->save();

        $menu_main_id = $menu->id;

        // Right menu
        $menu = new Menu;
        $menu->name = "Principal derecha";
        $menu->slug = Str::slug("navbar-right");
        $menu->primary = 1;
        $menu->save();

        $menu_right_id = $menu->id;

        // Añadimos elementos al menú Principal
        // Home
        $menuItem = MenuItem::create([
            'menu_id' => $menu_main_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => '/'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "es",
            'title' => "<i class=\"fa fa-home\" aria-hidden=\"true\"></i>"
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "en",
            'title' => "<i class=\"fa fa-home\" aria-hidden=\"true\"></i>"
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);


        // Contact Us
        $menuItem = MenuItem::create([
            'menu_id' => $menu_main_id,
            'item_type_id' => $type_ids["modulo"],
            'status' => 1,
            'uri' => 'contactus',
            'module_name' => 'contactus'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "es",
            'title' => "Contáctanos",
            'generate_url' => 'contactus'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "en",
            'title' => "Contact us",
            'generate_url' => 'contactus'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);


        // Ahora añadimos elementos al menu de la derecha
        // Idiomas
        $menuItem = MenuItem::create([
            'menu_id' => $menu_right_id,
            'item_type_id' => $type_ids["system"],
            'status' => 1,
            'uri' => '#',
            'permission' => 0,
            'module_name' => 'language'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "es",
            'title' => "<i class=\"fa fa-language\"></i> Selecciona un idioma",
            'generate_url' => '#'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "en",
            'title' => "<i class=\"fa fa-language\"></i> Select language",
            'generate_url' => '#'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        // Login
        // El login tiene que estar accesible solo para usuarios invitados
        $menuItem = MenuItem::create([
            'menu_id' => $menu_right_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => 'login',
            'permission' => 2, // Usuarios no autenticados
            'permission_name' => 'front-menus-items-login'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "es",
            'title' => "Login",
            'generate_url' => 'login'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "en",
            'title' => "Login",
            'generate_url' => 'login'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        // Register
        $menuItem = MenuItem::create([
            'menu_id' => $menu_right_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => 'register',
            'permission' => 2, // Usuarios no autenticados
            'permission_name' => 'front-menus-items-registro'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "es",
            'title' => "Registro",
            'generate_url' => 'register'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "en",
            'title' => "Register",
            'generate_url' => 'register'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        // Nombre del usuario
        $menuItem = MenuItem::create([
            'menu_id' => $menu_right_id,
            'item_type_id' => $type_ids["system"],
            'status' => 1,
            'uri' => '#',
            'permission' => 1,
            'module_name' => 'profile_name'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "es",
            'title' => "#Nombre del usuario",
            'generate_url' => '#'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "en",
            'title' => "#User name#",
            'generate_url' => '#'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        // Profile
        // Es hijo de nombre de usuario

        $menuItemChild = $menuItem->children()->create([
            'menu_id' => $menu_right_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => 'profile',
            'permission' => 1, // Usuarios autenticados
            'permission_name' => 'front-menus-items-perfil'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "es",
            'title' => "Perfil",
            'generate_url' => 'profile'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "en",
            'title' => "Profile",
            'generate_url' => 'profile'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        // Administration
        // Creamos un punto de menu solo para los adminsitradores
        // Es hijo de nombre de usuario

        $menuItemChild = $menuItem->children()->create([
            'menu_id' => $menu_right_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => 'admin',
            'permission' => 1, // Usuarios autenticados
            'permission_name' => 'front-menus-items-administracion'
        ]);

        // Buscamos el role de administrador y se los asignamos
        $roleAdmin = Role::where('name', 'admin')->first();
        $menuItem->roles()->sync([$roleAdmin->id]);


        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "es",
            'title' => "Administración",
            'generate_url' => 'admin'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "en",
            'title' => "Administration",
            'generate_url' => 'admin'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        // Divisor
        // Es hijo de nombre de usuario

        $menuItemChild = $menuItem->children()->create([
            'menu_id' => $menu_right_id,
            'item_type_id' => $type_ids["system"],
            'status' => 1,
            'uri' => '#',
            'permission' => 1, // Usuarios autenticados
            'permission_name' => 'front-menus-items-divisor',
            'module_name' => 'divider'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "es",
            'title' => "-",
            'generate_url' => '#'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "en",
            'title' => "-",
            'generate_url' => '#'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);


        // Logout
        // Es hijo de nombre de usuario

        $menuItemChild = $menuItem->children()->create([
            'menu_id' => $menu_right_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => 'logout',
            'permission' => 1, // Usuarios autenticados
            'permission_name' => 'front-menus-items-cerrar-sesion'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "es",
            'title' => "Cerrar sesión",
            'generate_url' => 'logout'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "en",
            'title' => "Logout",
            'generate_url' => 'logout'
        );
        DB::table('menu_item_translations')->insertGetId($menu_translation);









        /*
        // Home
        $menuItem = MenuItem::create([
            'menu_id' => $menu_main_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => '/'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "es",
            'title' => "Inicio"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "en",
            'title' => "Home"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );

        $menuItem = MenuItem::create([
            'menu_id' => $menu_main_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => '/'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "es",
            'title' => "Acerca"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "en",
            'title' => "About us"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );


        $menuItem = MenuItem::create([
            'menu_id' => $menu_main_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => '/'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "es",
            'title' => "Desplegable"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "en",
            'title' => "Drowpdown"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );

        $menuItemChild = $menuItem->children()->create([
            'menu_id' => $menu_main_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => '/'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "es",
            'title' => "Desplegable 1"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "en",
            'title' => "Drowpdown 1"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );



        $menuItemChild2 = $menuItemChild->children()->create([
            'menu_id' => $menu_main_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => '/'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItemChild2->id,
            'locale' => "es",
            'title' => "Desplegable 1.1"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );

        $menu_translation = array(
            'menu_item_id' => $menuItemChild2->id,
            'locale' => "en",
            'title' => "Drowpdown 1.1"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );




        $menuItemChild = $menuItem->children()->create([
            'menu_id' => $menu_main_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => '/'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "es",
            'title' => "Desplegable 2"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );

        $menu_translation = array(
            'menu_item_id' => $menuItemChild->id,
            'locale' => "en",
            'title' => "Drowpdown 2"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );

        $menuItem = MenuItem::create([
            'menu_id' => $menu_main_id,
            'item_type_id' => $type_ids["interno"],
            'status' => 1,
            'uri' => '/'
        ]);

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "es",
            'title' => "Contacto"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );

        $menu_translation = array(
            'menu_item_id' => $menuItem->id,
            'locale' => "en",
            'title' => "Contact us"
        );
        DB::table('menu_item_translations')->insertGetId( $menu_translation );
*/
    }
}
