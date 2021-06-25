<?php
/**
 * Created by PhpStorm.
 * User: jjcalvo
 * Date: 7/10/18
 * Time: 20:42
 */

/*


        // ---------------------------------------------------------------------------------------------------------
        // Module Newsletter
        // Módulo de Plantillas de newsletter
        $adminRole = new Permission();
        $adminRole->display_name = 'Mailing';
        $adminRole->name = str_slug('admin-newsletter-gral');
        $adminRole->description = 'Mailing - Módulo';
        $adminRole->save();
        $childAdminNewsletters = $this->childAdmin->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Plantillas';
        $adminRole->name = str_slug('admin-templates');
        $adminRole->description = 'Plantillas - Módulo';
        $adminRole->save();
        $childAdminPlantillas = $childAdminNewsletters->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Plantillas - listado';
        $adminRole->name = str_slug('admin-templates-list');
        $adminRole->description = 'Plantillas - lista';
        $adminRole->save();
        $childAdminPlantillas->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Plantillas - crear';
        $adminRole->name = str_slug('admin-templates-create');
        $adminRole->description = 'Plantillas - crear';
        $adminRole->save();
        $childAdminPlantillas->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Plantillas - actualizar';
        $adminRole->name = str_slug('admin-templates-update');
        $adminRole->description = 'Plantillas - actualizar';
        $adminRole->save();
        $childAdminPlantillas->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Plantillas - diseño';
        $adminRole->name = str_slug('admin-templates-design');
        $adminRole->description = 'Plantillas - diseño';
        $adminRole->save();
        $childAdminPlantillas->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Plantillas - borrar';
        $adminRole->name = str_slug('admin-templates-delete');
        $adminRole->description = 'Plantillas - borrar';
        $adminRole->save();
        $childAdminPlantillas->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Plantillas - ver';
        $adminRole->name = str_slug('admin-templates-read');
        $adminRole->description = 'Plantillas - ver';
        $adminRole->save();
        $childAdminPlantillas->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;


        // Module Newsletter
        $permissions = [
            [
                'display_name' => 'Diseñador',
                'name' => str_slug('admin-newsletter'),
                'description' => 'Diseñador - Módulo'
            ],
            [
                'display_name' => 'Diseñador - listado',
                'name' => str_slug('admin-newsletter-list'),
                'description' => 'Diseñador - lista'
            ],
            [
                'display_name' => 'Diseñador - crear',
                'name' => str_slug('admin-newsletter-create'),
                'description' => 'Diseñador - crear'
            ],
            [
                'display_name' => 'Diseñador - actualizar',
                'name' => str_slug('admin-newsletter-update'),
                'description' => 'Diseñador - actualizar'
            ],
            [
                'display_name' => 'Diseñador - borrar',
                'name' => str_slug('admin-newsletter-delete'),
                'description' => 'Diseñador - borrar'
            ],
            [
                'display_name' => 'Diseñador - ver',
                'name' => str_slug('admin-newsletter-read'),
                'description' => 'Diseñador - ver'
            ]
        ];

        $$MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        $permissions = [
            [
                'display_name' => 'Listas de distribución',
                'name' => str_slug('admin-newsletter-lists'),
                'description' => 'Listas de distribución - Módulo'
            ],
            [
                'display_name' => 'Listas de distribución - listado',
                'name' => str_slug('admin-newsletter-lists-list'),
                'description' => 'Listas de distribución - lista'
            ],
            [
                'display_name' => 'Listas de distribución - crear',
                'name' => str_slug('admin-newsletter-lists-create'),
                'description' => 'Listas de distribución - crear'
            ],
            [
                'display_name' => 'Listas de distribución - actualizar',
                'name' => str_slug('admin-newsletter-lists-update'),
                'description' => 'Listas de distribución - actualizar'
            ],
            [
                'display_name' => 'Listas de distribución - borrar',
                'name' => str_slug('admin-newsletter-lists-delete'),
                'description' => 'Listas de distribución - borrar'
            ],
            [
                'display_name' => 'Listas de distribución - ver',
                'name' => str_slug('admin-newsletter-lists-read'),
                'description' => 'Listas de distribución - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        $permissions = [
            [
                'display_name' => 'Suscriptores',
                'name' => str_slug('admin-newsletter-subscribers'),
                'description' => 'Suscriptores - Módulo'
            ],
            [
                'display_name' => 'Suscriptores - listado',
                'name' => str_slug('admin-newsletter-subscribers-list'),
                'description' => 'Suscriptores - lista'
            ],
            [
                'display_name' => 'Suscriptores - crear',
                'name' => str_slug('admin-newsletter-subscribers-create'),
                'description' => 'Suscriptores - crear'
            ],
            [
                'display_name' => 'Suscriptores - actualizar',
                'name' => str_slug('admin-newsletter-subscribers-update'),
                'description' => 'Suscriptores - actualizar'
            ],
            [
                'display_name' => 'Suscriptores - borrar',
                'name' => str_slug('admin-newsletter-subscribers-delete'),
                'description' => 'Suscriptores - borrar'
            ],
            [
                'display_name' => 'Suscriptores - ver',
                'name' => str_slug('admin-newsletter-subscribers-read'),
                'description' => 'Suscriptores - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);

        $permissions = [
            [
                'display_name' => 'Campañas',
                'name' => str_slug('admin-newsletter-campaigns'),
                'description' => 'Campañas - Módulo'
            ],
            [
                'display_name' => 'Campañas - listado',
                'name' => str_slug('admin-newsletter-campaigns-list'),
                'description' => 'Campañas - lista'
            ],
            [
                'display_name' => 'Campañas - crear',
                'name' => str_slug('admin-newsletter-campaigns-create'),
                'description' => 'Campañas - crear'
            ],
            [
                'display_name' => 'Campañas - actualizar',
                'name' => str_slug('admin-newsletter-campaigns-update'),
                'description' => 'Campañas - actualizar'
            ],
            [
                'display_name' => 'Campañas - borrar',
                'name' => str_slug('admin-newsletter-campaigns-delete'),
                'description' => 'Campañas - borrar'
            ],
            [
                'display_name' => 'Campañas - ver',
                'name' => str_slug('admin-newsletter-campaigns-read'),
                'description' => 'Campañas - ver'
            ]
        ];

        $MenuChild = $this->insertPermissions($permissions, $childAdminStruct, $this->a_permission_admin);




 */
