<?php

// Rutas de inicio

Route::group(array('namespace' => 'Clavel\Elearning\Controllers', 'middleware' => ['web']), function () {
    if (config("elearning.basicos.TIENE_HOME")) {
        Route::get('/', 'HomeElearning\HomeElearningController@index')->name('home');
    }


    if (!config("elearning.basicos.TIENE_HOME")) {
        Route::get('/', 'Asignaturas\FrontAsignaturasController@index')->name('home');
    }
});



// Usuarios de la web
Route::group(array('namespace' => 'Clavel\Elearning\Controllers\User', 'middleware' => ['web']), function () {
    Route::get('usuarios/registro/{field}/{id}', 'FrontUserController@updateFields');
    // Registro en la web
    Route::group(['middleware' => 'guest'], function () {
        Route::get("usuarios/confirmar/{id}", 'FrontUserController@confirmar');
        Route::get(
            'usuarios/registro/confirmed',
            ['as' => 'usuarios.registro.confirmed', "uses" => "FrontUserController@confirmed"]
        );
        Route::get(
            'usuarios/registro/saved',
            ['as' => 'usuarios.registro.saved', "uses" => "FrontUserController@saved"]
        );
        Route::get("usuarios/registro/{ajax?}", 'FrontUserController@getIndex');
        Route::post('usuarios/registro/{ajax?}', 'FrontUserController@postIndex');
        Route::post('usuarios/registro/exists/code', 'FrontUserController@checkCode');
        /*Route::post('usuarios/registro/exists/login', 'FrontUserController@checkLoginExists');
        Route::post('usuarios/registro/generate/pass', 'FrontUserController@generatePassword');*/
        Route::post('usuarios/send_confirmar_mail', 'FrontUserController@sendConfirmarMail');
    });
});


// Cursos
Route::group(array('namespace' => 'Clavel\Elearning\Controllers\Cursos', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin',  "as" => "admin.", 'middleware' => ['web']), function () {
        Route::get('cursos/cambiar_estado/{id}', 'AdminCursosController@setChangeState')
            ->where('id', '[0-9]+');
        Route::get('cursos/{id}/destroy', 'AdminCursosController@destroy')
            ->where('id', '[0-9]+');
        Route::post("cursos/getData", 'AdminCursosController@getData');
        Route::resource('cursos', 'AdminCursosController');
    });
});

// Códigos de acceso
Route::group(array('namespace' => 'Clavel\Elearning\Controllers\Codigos'), function () {
    Route::group(array('prefix' => 'admin', "as" => "admin.", 'middleware' => ['web']), function () {
        Route::post("codigos/getData", 'AdminCodigosController@getData');
        Route::post("codigos/importCodigos", 'AdminCodigosController@importCodigos');
        Route::get('codigos/cambiar_estado/{id}', 'AdminCodigosController@setChangeState')
            ->where('id', '[0-9]+');
        Route::get('codigos/cambiar_ilimitado/{id}', 'AdminCodigosController@setChangeIlimitado')
            ->where('id', '[0-9]+');
        Route::get('codigos/generateExcel', 'AdminCodigosController@generateExcel')
            ->where('id', '[0-9]+');
        Route::get('codigos/generateExcelQrCode', 'AdminCodigosController@generateExcelQrCode')
            ->where('id', '[0-9]+');
        Route::get('codigos/generateExcel_plantilla', 'AdminCodigosController@generateExcelPlantilla')
            ->where('id', '[0-9]+');
        Route::get('codigos/{id}/destroy', 'AdminCodigosController@destroy')
            ->where('id', '[0-9]+');
        Route::get('codigos/create_massive', 'AdminCodigosController@createMassive');
        Route::post('codigos/create_massive', 'AdminCodigosController@storeMassive')
            ->name('codigos.store_massive');
        Route::get('codigos/{id}/qrcode', 'AdminCodigosController@getQR')
            ->where('id', '[0-9]+');
        Route::resource('codigos', 'AdminCodigosController');
    });
});

// Certificados
Route::group(array('namespace' => 'Clavel\Elearning\Controllers\Certificados', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin', "as" => "admin.", 'middleware' => ['web']), function () {
        Route::get('certificados/cambiar_estado/{id}', 'AdminCertificadosController@setChangeState')
            ->where('id', '[0-9]+');

        // Para los elementos
        Route::get(
            'certificados/elements/{idioma}/{idplantilla}/{top?}/{left?}',
            'AdminCertificadosController@getElements'
        );
        Route::get('certificados/{id}/destroy', 'AdminCertificadosController@destroy')
            ->where('id', '[0-9]+');

        //Previsualización de certificados
        Route::get('certificados/pdf/{id}', 'AdminCertificadosController@pdfCertificado')
            ->where('id', '[0-9]+');

        Route::post("certificados/getData", 'AdminCertificadosController@getData');
        Route::post(
            'certificados/createelement',
            ['as' => 'certificados.createelement', "uses" => 'AdminCertificadosController@postElements']
        );
        Route::post('certificados/move-element', 'AdminCertificadosController@moveElement');
        Route::post('certificados/plantilla/', 'AdminCertificadosController@postPlantilla');
        //
        Route::delete('certificados/{id}/delelement', 'AdminCertificadosController@destroyElement')
            ->where('id', '[0-9]+');
        Route::get('certificados/editelement/{id}', 'AdminCertificadosController@getElement')
            ->where('id', '[0-9]+');
        Route::resource('/certificados', 'AdminCertificadosController');
    });
});


// Asignaturas
Route::group(array(
    'namespace' => 'Clavel\Elearning\Controllers\Asignaturas',
    'middleware' => ['web']
), function () {
    //Certificado curso
    Route::get("/curso/{curso}/certificado-curso", "FrontAsignaturasController@certificadoCurso")
        ->where('curso', '[0-9]+');

    Route::get("asignaturas/codigo/{id}", 'FrontAsignaturasController@codigo')
        ->where('id', '[0-9]+');
    Route::post("asignaturas/codigo", 'FrontAsignaturasController@setCodigo');

    Route::get('asignaturas/{filtro?}', 'FrontAsignaturasController@index');
    Route::get("/asignaturas/detalle/{slug}/{id}", "FrontAsignaturasController@detalle")
        ->where('id', '[0-9]+');
    Route::get("/asignaturas/contenido/{slug}/{id}", "FrontAsignaturasController@contenido")
        ->where('id', '[0-9]+');
    Route::get("/asignatura/{slug}/{id}/generarCertificado", "FrontAsignaturasController@certificado")
        ->where('id', '[0-9]+');
    Route::get("asignaturas/openImage/{id}", 'FrontAsignaturasController@openImage')
        ->where('id', '[0-9]+');




    Route::group(array(
        'prefix' => 'admin/asignaturas/convocatorias',
        "as" => "admin.asignaturas.convocatorias."
    ), function () {
        Route::get('{asignatura_id}/listado', 'AdminConvocatoriasController@index');
        Route::get('{asignatura_id}/formulario/{id?}', 'AdminConvocatoriasController@getFormulario');
        Route::post(
            '{asignatura_id}/formulario/{id?}',
            ['as' => 'formulario', "uses" => 'AdminConvocatoriasController@postFormulario']
        );
        Route::post('{id}/destroy', 'AdminConvocatoriasController@destroy')
            ->where('id', '[0-9]+');
    });

    Route::group(array('prefix' => 'admin', "as" => "admin."), function () {
        Route::get('asignaturas/cambiar_estado/{id}', 'AdminAsignaturasController@setChangeState')
            ->where('id', '[0-9]+');
        Route::get('asignaturas/{id}/destroy', 'AdminAsignaturasController@destroy')
            ->where('id', '[0-9]+');
        Route::get('asignaturas/{id}/clonelesson', 'AdminAsignaturasController@clonelesson')
            ->where('id', '[0-9]+');
        Route::get('asignaturas/{id}/exportarasignatura', 'AdminAsignaturasController@exportarAsignatura')
            ->where('id', '[0-9]+');
        Route::post('asignaturas/importarasignatura', 'AdminAsignaturasController@importarAsignatura');
        Route::post("asignaturas/getData", 'AdminAsignaturasController@getData');
        Route::post("asignaturas/reordenar", 'AdminAsignaturasController@reordenar');
        Route::resource('/asignaturas', 'AdminAsignaturasController');
    });
});

Route::group(array(
    'namespace' => 'Clavel\Elearning\Controllers\MisAsignaturas',
    'middleware' => ['web']
), function () {
    Route::get("mis-asignaturas", 'FrontMisAsignaturasController@index');
    Route::post("mis-asignaturas/getData", 'FrontMisAsignaturasController@getData');
    Route::post("mis-cursos/getData", 'FrontMisAsignaturasController@getDataCursos');
});


// Módulos
Route::group(array(
    'namespace' => 'Clavel\Elearning\Controllers\Modulos',
    'middleware' => ['web']
), function () {
    Route::get("modulos/openImage/{id}", 'FrontModulosController@openImage')
        ->where('id', '[0-9]+');
    Route::get("/modulos/detalle_modulo/{slug}/{id?}", "FrontModulosController@detalle")
        ->where('id', '[0-9]+');


    Route::group(array(
        'prefix' => 'admin/modulos/convocatorias',
        "as" => "admin.modulos.convocatorias."
    ), function () {
        Route::get('{modulo_id}/listado', 'AdminConvocatoriasModulosController@index');
        Route::get('{modulo_id}/formulario/{id?}', 'AdminConvocatoriasModulosController@getFormulario');
        Route::post(
            '{modulo_id}/formulario/{id?}',
            ['as' => 'formulario', "uses" => 'AdminConvocatoriasModulosController@postFormulario']
        );
    });

    Route::group(array(
        'as' => 'admin.asignaturas.',
        'prefix' => 'admin/asignaturas'
    ), function () {
        Route::get('modulos/cambiar_estado/{id}', 'AdminModulosController@setChangeState')
            ->where('id', '[0-9]+');
        Route::get('modulos/{id}/destroy', 'AdminModulosController@destroy')
            ->where('id', '[0-9]+');
        Route::post("{asignatura_id}/modulos/getData", 'AdminModulosController@getData');
        Route::post("{asignatura_id}/modulos/reordenar", 'AdminModulosController@reordenar');
        Route::post(
            '{asignatura_id}/modulos/store',
            ['as' => 'modulos.store', "uses" => "AdminModulosController@store"]
        );
        Route::post(
            '{asignatura_id}/modulos/update',
            ['as' => 'modulos.update', "uses" => "AdminModulosController@update"]
        );
        Route::resource('{asignatura_id}/modulos', 'AdminModulosController');
    });
});

// Contenidos
Route::group(array(
    'namespace' => 'Clavel\Elearning\Controllers\Contenidos',
    'middleware' => ['web']
), function () {
    Route::get("/contenido/detalle-contenido/openPDF/{id?}", "FrontContenidoController@openPDF")
        ->where('id', '[0-9]+');
    Route::get("/contenido/detalle-contenido/{slug}/{id?}", "FrontContenidoController@detalle")
        ->where('id', '[0-9]+');
    Route::post("/contenido/track-video", "FrontContenidoController@trackVideo");
    Route::get("/contenido/trackGaleria/{id}", "FrontContenidoController@trackGaleria");

    // ¿¿¿¿Rutas de mantenimiento de respuestas del usuario a examenes????
    Route::get('/contenido/detalle-contenido/{slug}/{id?}/destroy', "FrontContenidoController@destroy")
        ->where('id', '[0-9]+');
    Route::post(
        '/contenido/detalle-contenido/{slug}/{id?}/store',
        ['as' => 'front.contenidos.store', "uses" => "FrontContenidoController@store"]
    );


    Route::group(array(
        'as' => 'admin.modulos.',
        'prefix' => 'admin/modulos/'
    ), function () {
        Route::get(
            "{modulo_id}/reordenarArbol/{node_id}/{parent_id}/{prev_id}",
            "AdminContenidosController@reordenarArbol"
        )
            ->where('modulo_id', '[0-9]+')
            ->where("node_id", '[0-9]+')
            ->where("parent_id", '[0-9]+');
        Route::get('{modulo_id}/contenidos/cambiar_estado/{id}', 'AdminContenidosController@setChangeState')
            ->where('id', '[0-9]+');
        Route::get("{modulo_id}/contenidos/{contenido_id}/getVista/{id}", 'AdminContenidosController@getVista');
        Route::get('{modulo_id}/contenidos/{id}/destroy', 'AdminContenidosController@destroy')
            ->where('id', '[0-9]+');
        Route::resource('{modulo_id}/contenidos', 'AdminContenidosController');
    });

    Route::group(array('as' => 'admin.contenidos.', 'prefix' => 'admin/contenidos/'), function () {
        Route::get('{contenido_id}/preguntas/cambiar_estado/{id}', 'AdminEvaluacionController@setChangeState')
            ->where('id', '[0-9]+');
        Route::get(
            '{contenido_id}/preguntas/cambiar_estado_obligatorio/{id}',
            'AdminEvaluacionController@setObligatorio'
        )
            ->where('id', '[0-9]+');
        Route::get('{contenido_id}/preguntas/{id}/destroy', 'AdminEvaluacionController@destroy')
            ->where('id', '[0-9]+');
        Route::post("{contenido_id}/preguntas/getData", 'AdminEvaluacionController@getData');
        Route::get("{contenido_id}/preguntas/grupo/{id}", 'AdminEvaluacionController@getGruposPreguntas');
        Route::post("{contenido_id}/preguntas/grupo/{id}", 'AdminEvaluacionController@setGruposPreguntas')
            ->name('preguntas.grupo');

        // Wizard de creación de preguntas
        Route::get('{contenido_id}/preguntas/wizard', 'AdminEvaluacionWizardController@index')
            ->where('contenido_id', '[0-9]+');
        Route::post('preguntas/generate', "AdminEvaluacionWizardController@generate");


        Route::resource('{contenido_id}/preguntas', 'AdminEvaluacionController');

        Route::post("{contenido_id}/grupos_preguntas/getData", 'AdminEvaluacionGruposPreguntasController@getData');
        Route::resource('{modulo_id}/grupos_preguntas', 'AdminEvaluacionGruposPreguntasController');
    });

    Route::group(array('as' => 'admin.preguntas.', 'prefix' => 'admin/preguntas/'), function () {
        Route::get('{pregunta_id}/respuestas/cambiar_estado/{id}', 'AdminEvaluacionRespController@setChangeState')
            ->where('id', '[0-9]+');
        Route::get(
            '{pregunta_id}/respuestas/cambiar_estado_correcta/{id}',
            'AdminEvaluacionRespController@setChangeCorrect'
        )
            ->where('id', '[0-9]+');
        Route::get('{pregunta_id}/respuestas/{id}/destroy', 'AdminEvaluacionRespController@destroy')
            ->where('id', '[0-9]+');
        Route::post("{pregunta_id}/respuestas/getData", 'AdminEvaluacionRespController@getData');
        Route::resource('{pregunta_id}/respuestas', 'AdminEvaluacionRespController');
    });
});

// Grupos
Route::group(array('namespace' => 'Clavel\Elearning\Controllers\Grupos'), function () {
    Route::group(array('as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['web']), function () {
        Route::get('grupos/cambiar_estado/{id}', 'AdminGruposController@setChangeState')
            ->where('id', '[0-9]+');
        Route::get('grupos/{id}/destroy', 'AdminGruposController@destroy')
            ->where('id', '[0-9]+');
        Route::get('grupos/{id}/usuarios', 'AdminGruposController@usuarios')
            ->where('id', '[0-9]+');
        Route::post("grupos/getData", 'AdminGruposController@getData');
        Route::resource('/grupos', 'AdminGruposController');
    });
});


// Zona Profesor
Route::group(array('namespace' => 'Clavel\Elearning\Controllers\Profesor'), function () {
    Route::group(array('as' => 'admin.', 'prefix' => 'admin/', 'middleware' => ['web']), function () {
        Route::get('profesor', 'AdminProfesorController@index');

        Route::get("profesor/detalle/asignatura/{id?}", "AdminProfesorController@detalle")
            ->where('id', '[0-9]+');
        Route::get("profesor/detalle/modulo/{id?}", "AdminProfesorController@detalleModulo")
            ->where('id', '[0-9]+');
        Route::get("profesor/detalle/modulo/contenido/{id}", "AdminProfesorController@detalleContenido")
            ->where('id', '[0-9]+');
        Route::get(
            "profesor/detalle/modulo/contenido/{id}/examen/{track_id}/{user_id}",
            "AdminProfesorController@detalleContenidoExamen"
        )
            ->where('id', '[0-9]+')
            ->where('track_id', '[0-9]+')
            ->where('user_id', '[0-9]+');
        Route::post(
            'profesor/detalle/modulo/contenido/examen/store',
            ['as' => 'profesor.examen.store', "uses" => "AdminProfesorController@detalleContenidoExamenStore"]
        );


        Route::get("profesor/generateExcel", 'AdminProfesorController@generateExcelGeneral');

        Route::get(
            "profesor/asignatura/{asignatura_id}/user-stats/{user_id}",
            'AdminProfesorController@userStatsAsignatura'
        )
            ->where('asignatura_id', '[0-9]+')
            ->where('user_id', '[0-9]+');
        Route::get("profesor/modulo/{modulo_id}/user-stats/{user_id}", 'AdminProfesorController@userStatsModulo')
            ->where('modulo_id', '[0-9]+')
            ->where('user_id', '[0-9]+');
        Route::get("profesor/user-stats/{user}", 'AdminProfesorController@userStats')
            ->where('user', '[0-9]+');


        Route::get(
            "profesor/user-stats/{user}/{scope}/{asignatura_id}/{convocatoria_id}",
            'AdminProfesorController@userScopeStats'
        )
            ->where('user', '[0-9]+')
            ->where('asignatura_id', '[0-9]+')
            ->where('convocatoria_id', '[0-9]+');

        Route::get(
            "profesor/asignatura/{asignatura_id}/generateExcel/{modulo_id?}",
            'AdminProfesorController@generateExcel'
        )
            ->where('asignatura_id', '[0-9]+')
            ->where('modulo_id', '[0-9]+');
        Route::get(
            "profesor/asignatura/{asignatura_id}/generateExcelPreguntas/{modulo_id}/contenido/{contenido_id}",
            'AdminProfesorController@generateExcelExamen'
        )
            ->where('asignatura_id', '[0-9]+')
            ->where('modulo_id', '[0-9]+')
            ->where('contenido_id', '[0-9]+');

        Route::post(
            "profesor/asignatura/{asignatura_id}/getDataUsers/{modulo_id?}",
            'AdminProfesorController@getDataUsers'
        )
            ->where('asignatura_id', '[0-9]+')
            ->where('modulo_id', '[0-9]+');
        Route::post(
            "profesor/asignatura/{asignatura_id}/getDataUsersContenido/{modulo_id}/{contenido_id}",
            'AdminProfesorController@getDataUsersContenido'
        )
            ->where('asignatura_id', '[0-9]+')
            ->where('modulo_id', '[0-9]+')
            ->where('contenido_id', '[0-9]+');
        Route::post(
            "profesor/asignatura/{asignatura_id}/getDataModules",
            'AdminProfesorController@getDataModules'
        )
            ->where('asignatura_id', '[0-9]+');
        Route::post(
            "profesor/asignatura/{asignatura_id}/getDataContenidos/{modulo_id}",
            'AdminProfesorController@getDataContenidos'
        )
            ->where('modulo_id', '[0-9]+');

        Route::post("profesor/asignatura/resetear/{id}", 'AdminProfesorController@reseterAsignatura')
            ->where('id', '[0-9]+');
        Route::post("profesor/modulo/resetear/{id}", 'AdminProfesorController@resetearModulo')
            ->where('id', '[0-9]+');
        Route::post("profesor/contenido/{id}/reset/{user_id}", 'AdminProfesorController@resetContenido')
            ->where('id', '[0-9]+')
            ->where('id', '[0-9]+');


        Route::get("profesor/contenido/recalcular/{track_eval}", 'AdminProfesorController@recalcularNota')
            ->where('track_eval', '[0-9]+');
        Route::get("profesor/contenido/recalcular/general/{contenido}", 'AdminProfesorController@recalcularNotaGeneral')
            ->where('contenido', '[0-9]+');

        Route::post("profesor/asignatura/{asignatura_id}/reloadStats", 'AdminProfesorController@reloadStats')
            ->where('asignatura_id', '[0-9]+');
        Route::post("profesor/modulo/{modulo_id}/reloadStats", 'AdminProfesorController@reloadStatsModulo')
            ->where('modulo_id', '[0-9]+');
        Route::post("profesor/contenido/{contenido_id}/reloadStats", 'AdminProfesorController@reloadStatsContenido')
            ->where('contenido_id', '[0-9]+');
    });
});

// Foros de la web
Route::group(array(
    "namespace" => 'Clavel\Elearning\Controllers\Foro',
    "prefix" => "foro/",
    "as" => "foro.",
    'middleware' => ['web']
), function () {
    Route::get("{asignatura_id?}/{modulo_id?}/{contenido_id?}", "FrontForoController@index")
        ->where('asignatura_id', '[0-9]+')
        ->where('modulo_id', '[0-9]+')
        ->where('contenido_id', '[0-9]+')->name("index");
    Route::post("create", "FrontForoController@create")->name("create");
    Route::post("store", "FrontForoController@store")->name("store");
    Route::patch("update/{id}", "FrontForoController@update")
        ->where('id', '[0-9]+')->name("update");
    Route::get("destroy/{id}", "FrontForoController@destroy")
        ->where('id', '[0-9]+')->name("destroy");
    Route::get("show/{id}", "FrontForoController@show")
        ->where('id', '[0-9]+')->name("show");
    Route::get("edit/{id}", "FrontForoController@edit")
        ->where('id', '[0-9]+')->name("edit");
});

// Alumnos
Route::group(array('namespace' => 'Clavel\Elearning\Controllers\Alumnos'), function () {
    Route::group(array('as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['web']), function () {
        Route::post("alumnos/getData", 'AdminAlumnosController@getData');
        Route::get('alumnos/setListado', 'AdminAlumnosController@setListado');
        Route::post("alumnos/importAlumnos", 'AdminAlumnosController@importAlumnos');
        Route::get("alumnos/generateExcel", 'AdminAlumnosController@generateExcel');
        Route::post("alumnos/getGrupos", 'AdminAlumnosController@getGrupos');
        Route::post('alumnos/saveFilter', "AdminAlumnosController@saveFilter");
        Route::get('alumnos/clearFilter', "AdminAlumnosController@clearFilter");
        Route::post(
            'alumnos/storeGrupos',
            ['as' => 'alumnos.storeGrupos', "uses" => "AdminAlumnosController@storeGrupos"]
        );
        Route::resource('/alumnos', 'AdminAlumnosController');
    });
});

// Alumnos - Directorios
Route::group(array('namespace' => 'Clavel\Elearning\Controllers\Alumnos'), function () {
    Route::group(array('as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['web']), function () {
        Route::get(
            'alumnos-directory/{id}/change-directory/{directory}',
            'AdminAlumnosDirectoryController@changeDirectory'
        )
            ->where('id', '[0-9]+');
        Route::post('alumnos-directory/upload', 'AdminAlumnosDirectoryController@upload');
        Route::post('alumnos-directory/delete', 'AdminAlumnosDirectoryController@delete');
        Route::get('alumnos-directory/media/{media_id}', 'AdminAlumnosDirectoryController@getMedia');
        Route::resource('alumnos-directory', 'AdminAlumnosDirectoryController');
    });

    Route::group(array('middleware' => ['web']), function () {
        Route::get(
            'alumnos-directory/change-directory/{directory}',
            'FrontAlumnosDirectoryController@changeDirectory'
        );
        Route::post('alumnos-directory/upload', 'FrontAlumnosDirectoryController@upload');
        Route::post('alumnos-directory/delete', 'FrontAlumnosDirectoryController@delete');
        Route::get('alumnos-directory/media/{media_id}', 'FrontAlumnosDirectoryController@getMedia');
        Route::resource('alumnos-directory', 'FrontAlumnosDirectoryController');
    });
});
