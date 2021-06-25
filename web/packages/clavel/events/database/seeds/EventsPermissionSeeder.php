<?php
/**
 * Created by PhpStorm.
 * User: jjcalvo
 * Date: 7/10/18
 * Time: 20:41
 */
/*



        //------------------------------------------------------------------------------------------------
        // Eventos
        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos';
        $adminRole->name = str_slug('admin-events');
        $adminRole->description = 'Eventos - Módulo';
        $adminRole->save();
        $childAdminEventos = $this->childAdmin->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos - Tags';
        $adminRole->name = str_slug('admin-events-tags');
        $adminRole->description = 'Eventos - Tags - Módulo';
        $adminRole->save();
        $childAdminTags = $childAdminEventos->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos - Tags - listado';
        $adminRole->name = str_slug('admin-events-tags-list');
        $adminRole->description = 'Eventos - Tags - lista';
        $adminRole->save();
        $childAdminTags->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos - Tags - crear';
        $adminRole->name = str_slug('admin-events-tags-create');
        $adminRole->description = 'Eventos - Tags - crear';
        $adminRole->save();
        $childAdminTags->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos - Tags - actualizar';
        $adminRole->name = str_slug('admin-events-tags-update');
        $adminRole->description = 'Eventos - Tags - actualizar';
        $adminRole->save();
        $childAdminTags->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos - Tags - Borrar';
        $adminRole->name = str_slug('admin-events-tags-delete');
        $adminRole->description = 'Eventos - Tags - Borrar';
        $adminRole->save();
        $childAdminTags->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos - listado';
        $adminRole->name = str_slug('admin-events-list');
        $adminRole->description = 'Eventos - lista';
        $adminRole->save();
        $childAdminEventos->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos - crear';
        $adminRole->name = str_slug('admin-events-create');
        $adminRole->description = 'Eventos - crear';
        $adminRole->save();
        $childAdminEventos->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos - actualizar';
        $adminRole->name = str_slug('admin-events-update');
        $adminRole->description = 'Eventos - actualizar';
        $adminRole->save();
        $childAdminEventos->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos - borrar';
        $adminRole->name = str_slug('admin-events-delete');
        $adminRole->description = 'Eventos - borrar';
        $adminRole->save();
        $childAdminEventos->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;

        $adminRole = new Permission();
        $adminRole->display_name = 'Eventos - ver';
        $adminRole->name = str_slug('admin-events-read');
        $adminRole->description = 'Eventos - ver';
        $adminRole->save();
        $childAdminEventos->children()->create(['permissions_id' => $adminRole->id]);
        $this->a_permission_admin[] = $adminRole->id;



        // Modulo de events Front End
        $adminBloque = new Permission();
        $adminBloque->display_name = 'Eventos con permisos';
        $adminBloque->name = str_slug('front-events');
        $adminBloque->description = 'Eventos con permisos';
        $adminBloque->save();
        $this->childWeb->children()->create(['permissions_id' => $adminBloque->id]);
        $this->a_permission_admin[] = $adminBloque->id;
        $this->a_permission_front[] = $adminBloque->id;

 */
