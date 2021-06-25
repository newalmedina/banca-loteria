<?php

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;

class ElearningPermissionSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();



        // Agrupador de puntos de menu - Estucctura de la web
        $adminStruct = new Permission();
        $adminStruct->display_name = 'Elearning';
        $adminStruct->name = str_slug('admin-elearning');
        $adminStruct->description = 'Elearning - Módulo';
        $adminStruct->save();
        $childAdminStruct = $this->childAdmin->children()->create(['permissions_id' => $adminStruct->id]);
        $this->a_permission_admin[] = $adminStruct->id;


        // Módulo de Menús
        $permissions = [
            [
                'display_name' => 'Cursos',
                'name' => str_slug('admin-cursos'),
                'description' => 'Cursos - Módulo'
            ],
            [
                'display_name' => 'Cursos - listado',
                'name' => str_slug('admin-cursos-list'),
                'description' => 'Cursos - lista'
            ],
            [
                'display_name' => 'Cursos - crear',
                'name' => str_slug('admin-cursos-create'),
                'description' => 'Cursos - crear'
            ],
            [
                'display_name' => 'Cursos - actualizar',
                'name' => str_slug('admin-cursos-update'),
                'description' => 'Cursos - actualizar'
            ],
            [
                'display_name' => 'Cursos - borrar',
                'name' => str_slug('admin-cursos-delete'),
                'description' => 'Cursos - borrar'
            ],
            [
                'display_name' => 'Cursos - ver',
                'name' => str_slug('admin-cursos-read'),
                'description' => 'Cursos - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);



        // Module Certificados
        $permissions = [
            [
                'display_name' => 'Certificados',
                'name' => str_slug('admin-certificados'),
                'description' => 'Certificados - Módulo'
            ],
            [
                'display_name' => 'Certificados - listado',
                'name' => str_slug('admin-certificados-list'),
                'description' => 'Certificados - lista'
            ],
            [
                'display_name' => 'Certificados - crear',
                'name' => str_slug('admin-certificados-create'),
                'description' => 'Certificados - crear'
            ],
            [
                'display_name' => 'Certificados - actualizar',
                'name' => str_slug('admin-certificados-update'),
                'description' => 'Certificados - actualizar'
            ],
            [
                'display_name' => 'Certificados - borrar',
                'name' => str_slug('admin-certificados-delete'),
                'description' => 'Certificados - borrar'
            ],
            [
                'display_name' => 'Certificados - ver',
                'name' => str_slug('admin-certificados-read'),
                'description' => 'Certificados - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);



        // Module Asignaturas
        $permissions = [
            [
                'display_name' => 'Asignaturas',
                'name' => str_slug('admin-asignaturas'),
                'description' => 'Asignaturas - Módulo'
            ],
            [
                'display_name' => 'Asignaturas - listado',
                'name' => str_slug('admin-asignaturas-list'),
                'description' => 'Asignaturas - lista'
            ],
            [
                'display_name' => 'Asignaturas - crear',
                'name' => str_slug('admin-asignaturas-create'),
                'description' => 'Asignaturas - crear'
            ],
            [
                'display_name' => 'Asignaturas - actualizar',
                'name' => str_slug('admin-asignaturas-update'),
                'description' => 'Asignaturas - actualizar'
            ],
            [
                'display_name' => 'Asignaturas - borrar',
                'name' => str_slug('admin-asignaturas-delete'),
                'description' => 'Asignaturas - borrar'
            ],
            [
                'display_name' => 'Asignaturas - ver',
                'name' => str_slug('admin-asignaturas-read'),
                'description' => 'Páginas - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);



        // Module Convocatorias
        $permissions = [
            [
                'display_name' => 'Convocatorias',
                'name' => str_slug('admin-asignaturas-convocatorias'),
                'description' => 'Convocatorias - Módulo'
            ],
            [
                'display_name' => 'Convocatorias - listado',
                'name' => str_slug('admin-asignaturas-convocatorias-list'),
                'description' => 'Convocatorias - lista'
            ],
            [
                'display_name' => 'Convocatorias - crear',
                'name' => str_slug('admin-asignaturas-convocatorias-create'),
                'description' => 'Convocatorias - crear'
            ],
            [
                'display_name' => 'Convocatorias - actualizar',
                'name' => str_slug('admin-asignaturas-convocatorias-update'),
                'description' => 'Convocatorias - actualizar'
            ],
            [
                'display_name' => 'Convocatorias - borrar',
                'name' => str_slug('admin-asignaturas-convocatorias-delete'),
                'description' => 'Convocatorias - borrar'
            ],
            [
                'display_name' => 'Convocatorias - ver',
                'name' => str_slug('admin-asignaturas-convocatorias-read'),
                'description' => 'Páginas - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);



        // Module Módulos
        $permissions = [
            [
                'display_name' => 'Módulos',
                'name' => str_slug('admin-modulos'),
                'description' => 'Módulos - Módulo'
            ],
            [
                'display_name' => 'Módulos - listado',
                'name' => str_slug('admin-modulos-list'),
                'description' => 'Módulos - lista'
            ],
            [
                'display_name' => 'Módulos - crear',
                'name' => str_slug('admin-modulos-create'),
                'description' => 'Módulos - crear'
            ],
            [
                'display_name' => 'Módulos - actualizar',
                'name' => str_slug('admin-modulos-update'),
                'description' => 'Módulos - actualizar'
            ],
            [
                'display_name' => 'Módulos - borrar',
                'name' => str_slug('admin-modulos-delete'),
                'description' => 'Módulos - borrar'
            ],
            [
                'display_name' => 'Módulos - ver',
                'name' => str_slug('admin-modulos-read'),
                'description' => 'Módulos - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);



        // Module Contenidos
        $permissions = [
            [
                'display_name' => 'Contenidos',
                'name' => str_slug('admin-contenidos'),
                'description' => 'Contenidos - Módulo'
            ],
            [
                'display_name' => 'Contenidos - listado',
                'name' => str_slug('admin-contenidos-list'),
                'description' => 'Contenidos - lista'
            ],
            [
                'display_name' => 'Contenidos - crear',
                'name' => str_slug('admin-contenidos-create'),
                'description' => 'Contenidos - crear'
            ],
            [
                'display_name' => 'Contenidos - actualizar',
                'name' => str_slug('admin-contenidos-update'),
                'description' => 'Contenidos - actualizar'
            ],
            [
                'display_name' => 'Contenidos - borrar',
                'name' => str_slug('admin-contenidos-delete'),
                'description' => 'Contenidos - borrar'
            ],
            [
                'display_name' => 'Contenidos - ver',
                'name' => str_slug('admin-contenidos-read'),
                'description' => 'Contenidos - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);



        // Module Códigos
        $permissions = [
            [
                'display_name' => 'Códigos',
                'name' => str_slug('admin-codigos'),
                'description' => 'Códigos - Módulo'
            ],
            [
                'display_name' => 'Códigos - listado',
                'name' => str_slug('admin-codigos-list'),
                'description' => 'Códigos - lista'
            ],
            [
                'display_name' => 'Códigos - crear',
                'name' => str_slug('admin-codigos-create'),
                'description' => 'Códigos - crear'
            ],
            [
                'display_name' => 'Códigos - actualizar',
                'name' => str_slug('admin-codigos-update'),
                'description' => 'Códigos - actualizar'
            ],
            [
                'display_name' => 'Códigos - borrar',
                'name' => str_slug('admin-codigos-delete'),
                'description' => 'Códigos - borrar'
            ],
            [
                'display_name' => 'Códigos - ver',
                'name' => str_slug('admin-codigos-read'),
                'description' => 'Códigos - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);



        // Module Grupos
        $permissions = [
            [
                'display_name' => 'Grupos',
                'name' => str_slug('admin-grupos'),
                'description' => 'Grupos - Módulo'
            ],
            [
                'display_name' => 'Grupos - listado',
                'name' => str_slug('admin-grupos-list'),
                'description' => 'Grupos - lista'
            ],
            [
                'display_name' => 'Grupos - crear',
                'name' => str_slug('admin-grupos-create'),
                'description' => 'Grupos - crear'
            ],
            [
                'display_name' => 'Grupos - actualizar',
                'name' => str_slug('admin-grupos-update'),
                'description' => 'Grupos - actualizar'
            ],
            [
                'display_name' => 'Grupos - borrar',
                'name' => str_slug('admin-grupos-delete'),
                'description' => 'Grupos - borrar'
            ],
            [
                'display_name' => 'Grupos - ver',
                'name' => str_slug('admin-grupos-read'),
                'description' => 'Grupos - ver'
            ]
        ];


        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);


        // Zona del profesor
        $permissions = [
            [
                'display_name' => 'Zona Profesor',
                'name' => str_slug('admin-profesor'),
                'description' => 'Zona del Profesor'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);



        // Rol de administrador
        $roleAdmin = Role::where("name", "=", str_slug('admin'))->first();
        $roleAdmin->attachPermissions($this->a_permission_admin);
        $roleUser = Role::where("name", "=", str_slug('usuario-front'))->first();
        $roleUser->attachPermissions($this->a_permission_front);
    }
}
