<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTablesElearning extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Códigos
        Schema::create('codigos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo')->unique();
            $table->boolean('ilimitado')->default(false);
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Schema::create('codigo_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('codigo_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->unique(['codigo_id','role_id']);
            $table->foreign('codigo_id')->references('id')->on('codigos')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        // Asignaturas
        Schema::create('asignaturas', function ($table) {
            $table->increments('id');
            $table->string('image')->default('');
            $table->boolean('activo')->default(0);
            $table->integer('obligatorio_id')->unsigned()->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('origin_id')->unsigned()->nullable();

            $table->timestamps();
        });

        Schema::create('codigo_asignaturas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('codigo_id')->unsigned();
            $table->integer('asignatura_id')->unsigned();

            $table->unique(['codigo_id','asignatura_id']);
            $table->foreign('codigo_id')->references('id')->on('codigos')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
        });

        Schema::table('users', function ($table) {
            if (Schema::hasColumn('users', 'codigo_id') == false) {
                $table->integer('codigo_id')->after('email')->unsigned()->nullable();
            }

            $table->foreign('codigo_id')
                ->references('id')->on('codigos')
                ->onDelete('cascade');
        });

        // Cursos
        Schema::create('cursos', function ($table) {
            $table->increments('id');
            $table->boolean('activo')->default(0);
            $table->timestamps();
        });

        Schema::create('curso_translations', function ($table) {
            $table->increments('id');
            $table->integer('curso_id')->unsigned();
            $table->string('locale')->index();
            $table->string('nombre')->nullable();
            $table->string('url_amigable')->default('');

            $table->unique(['curso_id','locale']);
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
        });

        // Certificados
        Schema::create('certificados', function ($table) {
            $table->increments('id');
            $table->string('nombre');
            $table->integer('paginas')->nullable();
            $table->boolean('activo')->default(0);
            $table->timestamps();
        });

        Schema::create('certificado_paginas', function ($table) {
            $table->increments('id');
            $table->integer('certificado_id')->unsigned();
            $table->integer('pagina');

            $table->unique(['certificado_id','pagina']);
            $table->foreign('certificado_id')->references('id')->on('certificados')->onDelete('cascade');
        });

        Schema::create('certificado_pagina_translations', function ($table) {
            $table->increments('id');
            $table->integer('certificado_pagina_id')->unsigned();
            $table->string('locale')->index();
            $table->string('plantilla');
            $table->text('body')->nullable();

            $table->unique(['certificado_pagina_id','locale'], 'certificado_trans_unique');
            $table->foreign('certificado_pagina_id', 'certificado_trans_fk')->references('id')->on('certificado_paginas')->onDelete('cascade');
        });

        Schema::create('certificado_pagina_translations_elementos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('certificado_pagina_translation_id')->unsigned();
            $table->string('fontfamily');
            $table->string('fontsize');
            $table->string('fontcolor');
            $table->string('name');
            $table->integer("mtop");
            $table->integer("mleft");
            $table->integer("width");
            $table->integer("height");


            $table->foreign('certificado_pagina_translation_id', 'pagina_elemento_fk')
                ->references('id')
                ->on('certificado_pagina_translations')
                ->onDelete('cascade');
        });

        Schema::create('asignatura_translations', function ($table) {
            $table->increments('id');
            $table->integer('asignatura_id')->unsigned();
            $table->string('locale')->index();
            $table->string('titulo')->nullable();
            $table->string('url_amigable')->default('');
            $table->text('breve')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('creditos')->nullable();
            $table->string('academico')->nullable();
            $table->string('caracteristica')->nullable();
            $table->string('plazas')->nullable();
            $table->string('admision')->nullable();
            $table->string('coordinacion')->nullable();
            $table->string('estudiantes')->nullable();

            $table->unique(['asignatura_id','locale']);
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
        });

        Schema::create('asignatura_cursos', function ($table) {
            $table->increments('id');
            $table->integer('curso_id')->unsigned();
            $table->integer('asignatura_id')->unsigned();
            $table->timestamps();

            $table->unique(['asignatura_id','curso_id']);
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
        });

        // Grupos
        Schema::create('grupos', function ($table) {
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->boolean('activo')->default(0);
            $table->string('codigo')->nullable();
            $table->timestamps();
        });

        Schema::create('grupo_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grupo_id')->unsigned();
            $table->unsignedBigInteger('user_id');

            $table->unique(['grupo_id','user_id']);
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Convocatorias
        Schema::create('asignatura_convocatorias', function ($table) {
            $table->increments('id');
            $table->string('nombre')->nullable();
            $table->integer('asignatura_id')->unsigned();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('consultar')->default(0);
            $table->integer('porcentaje')->nullable();
            $table->integer('creditos')->nullable();
            $table->integer('certificado_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
            $table->foreign('certificado_id')->references('id')->on('certificados')->onDelete('cascade');
        });

        Schema::create('asignatura_convocatoria_grupos', function ($table) {
            $table->increments('id');
            $table->integer('convocatoria_id')->unsigned();
            $table->integer('grupo_id')->unsigned();

            $table->foreign('convocatoria_id')->references('id')->on('asignatura_convocatorias')->onDelete('cascade');
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
        });

        // Modulos
        //Creamos la tabla de tipo de modulos
        Schema::create('tipo_modulos', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('active');
        });

        Schema::create('tipo_modulo_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tipo_modulo_id')->unsigned();
            $table->string('nombre');
            $table->string("url_amigable")->default('');
            $table->string("locale");

            $table->foreign('tipo_modulo_id', 'tipo_modulo_fk')->references('id')->on('tipo_modulos')->onDelete('cascade');
        });


        Schema::create('modulos', function ($table) {
            $table->increments('id');
            $table->integer('asignatura_id')->unsigned();
            $table->boolean('activo')->default(0);
            $table->string('image')->nullable();
            $table->string('fondo')->nullable();
            $table->integer('obligatorio_id')->unsigned()->nullable();
            $table->timestamps();
            $table->integer('tipo_modulo_id')->unsigned()->nullable();
            $table->boolean('puntua');
            $table->float('peso')->nullable();
            $table->integer('orden')->nullable();

            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
            $table->foreign('obligatorio_id')->references('id')->on('modulos')->onDelete('cascade');
            $table->foreign('tipo_modulo_id')->references('id')->on('tipo_modulos')->onDelete('cascade');
        });

        Schema::create('modulo_translations', function ($table) {
            $table->increments('id');
            $table->integer('modulo_id')->unsigned();
            $table->string('locale')->index();
            $table->string('nombre')->nullable();
            $table->string('url_amigable')->default('');
            $table->text('descripcion')->nullable();
            $table->string('coordinacion')->nullable();

            $table->unique(['modulo_id','locale']);
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade');
        });

        Schema::create('modulo_convocatorias', function ($table) {
            $table->increments('id');
            $table->integer('modulo_id')->unsigned();
            $table->integer('convocatoria_id')->unsigned();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('consultar')->default(0);
            $table->integer('porcentaje');

            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade');
            $table->foreign('convocatoria_id')->references('id')->on('asignatura_convocatorias')->onDelete('cascade');
        });

        // Contenidos
        //Creamos la tabla de tipo de contenidos
        Schema::create('tipo_contenidos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tablename')->default('');
            $table->string('idintable');
            $table->string('vista');
            $table->string('vista_front');
            $table->string('slug');
        });

        Schema::create('tipo_contenidos_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tipo_contenido_id')->unsigned();
            $table->string('nombre');
            $table->string("locale");
            $table->string("icono");

            $table->foreign('tipo_contenido_id', 'tipo_contenidos_fk')->references('id')->on('tipo_contenidos')->onDelete('cascade');
        });

        //Contenidos
        Schema::create('contenidos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('modulo_id')->unsigned();
            $table->integer('tipo_contenido_id')->unsigned();
            $table->boolean('activo')->default(0);
            $table->boolean('obligatorio')->default(0);
            $table->integer('contenido_id_relacionado')->unsigned()->nullable();
            $table->integer('contenido_id_obligatorio')->unsigned()->nullable();
            $table->boolean('modal')->default(false);
            $table->string('storepath')->nullable();
            $table->integer('parent_id')->nullable()->index();
            $table->integer('lft')->nullable()->index();
            $table->integer('rgt')->nullable()->index();
            $table->integer('depth')->nullable();
            $table->boolean('pantalla_completa')->default(0);
            $table->boolean('descargar_pdf')->default(0);
            $table->boolean('generar_pdf')->default(0);
            $table->string('pdf_archivo')->nullable();
            $table->string('media_url')->nullable();

            $table->timestamps();

            $table->foreign('modulo_id', 'modulo_fk')->references('id')->on("modulos")->onDelete('cascade');
            $table->foreign('contenido_id_relacionado', 'relacionado_fk')->references('id')->on('contenidos')->onDelete('cascade');
            $table->foreign('contenido_id_obligatorio', 'obligatorio_fk')->references('id')->on('contenidos')->onDelete('cascade');
        });

        Schema::create('contenidos_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contenido_id')->unsigned();
            $table->string('nombre')->nullable();
            $table->text('contenido')->nullable();
            $table->string("url_amigable")->default('');
            $table->string("locale");
            $table->string('mp4')->nullable();
            $table->string('webm')->nullable();
            $table->string('vtt')->nullable();
            $table->longText('contenido_aprobado')->nullable();
            $table->longText('contenido_suspendido')->nullable();

            $table->foreign('contenido_id', 'contenido_translation_fk')->references('id')->on("contenidos")->onDelete('cascade');
        });


        Schema::create('contenidos_link', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contenido_id')->unsigned();
            $table->string('link');
            $table->boolean('nueva_ventana')->default(0);

            $table->foreign('contenido_id', 'contenido_link_fk')->references('id')->on("contenidos")->onDelete('cascade');
        });

        Schema::create('contenidos_evaluacion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contenido_id')->unsigned();
            $table->boolean('mostrar_respuesta')->default(0);
            $table->boolean('mostrar_resultado')->default(0);
            $table->boolean('evaluacion_final')->default(0);
            $table->double('peso_final')->nullable();
            $table->boolean('limitante')->default(0);
            $table->integer('porcentaje_aprobado')->default(0);
            $table->boolean('permitir_resetear')->default(0);
            $table->boolean('preguntas_aleatorias')->default(0);
            $table->boolean('respuestas_aleatorias')->default(0);
            $table->integer('numero_resets')->default(1);
            $table->integer('numero_preguntas_visibles')->nullable();
            $table->boolean('presencial')->default(0);
            $table->float("peso")->nullable();
            $table->integer("puntua")->nullable();
            $table->integer("modulo_id")->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('contenido_id', 'contenido_evaluacion_fk')->references('id')->on("contenidos")->onDelete('cascade');
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade');
        });

        // Evaluacion
        //Creamos las tablas asociadas a las preguntas
        Schema::create('tipo_preguntas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->boolean('activa');
        });

        Schema::create('preguntas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contenido_id')->unsigned();
            $table->integer('tipo_pregunta_id')->unsigned();
            $table->integer('orden');
            $table->boolean('activa');
            $table->boolean('obligatoria');
            $table->timestamps();

            $table->foreign("contenido_id")->references("id")->on("contenidos")->onDelete("cascade");
            $table->foreign("tipo_pregunta_id")->references("id")->on("tipo_preguntas")->onDelete("cascade");
        });

        Schema::create('pregunta_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pregunta_id')->unsigned();
            $table->text('nombre');
            $table->string("locale");

            $table->foreign('pregunta_id')->references('id')->on('preguntas')->onDelete('cascade');
        });

        Schema::create('respuestas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pregunta_id')->unsigned();
            $table->integer('orden');
            $table->boolean('correcta');
            $table->float('puntos_correcta');
            $table->float('puntos_incorrecta')->default(0);
            $table->boolean('activa');
            $table->timestamps();

            $table->foreign("pregunta_id")->references("id")->on("preguntas")->onDelete("cascade");
        });

        Schema::create('respuesta_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('respuesta_id')->unsigned();
            $table->text('nombre');
            $table->text('comentario');
            $table->string("locale");

            $table->foreign('respuesta_id')->references('id')->on('respuestas')->onDelete('cascade');
        });

        Schema::create('respuesta_resultados', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('respuesta_id')->unsigned();
            $table->integer('pregunta_id')->unsigned();
            $table->unsignedBigInteger('user_id');
            $table->integer("contenido_id")->unsigned();
            $table->integer("modulo_id")->unsigned();
            $table->integer("asignatura_id")->unsigned();
            $table->integer("convocatoria_id")->unsigned();
            $table->boolean('marcada');
            $table->boolean('correcta');
            $table->float('puntos_correcta');
            $table->float('puntos_incorrecta');
            $table->float('puntos_obtenidos');
            $table->longText('observaciones')->nullable();
            $table->longText('observaciones_prof')->nullable();

            $table->timestamps();

            $table->unique(array("respuesta_id","pregunta_id","user_id","contenido_id","modulo_id","asignatura_id","convocatoria_id"), "unico_resultado");


            $table->foreign("respuesta_id")->references("id")->on("respuestas")->onDelete("cascade");
            $table->foreign("pregunta_id")->references("id")->on("preguntas")->onDelete("cascade");
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->foreign('convocatoria_id')->references('id')->on('asignatura_convocatorias')->onDelete('cascade');
            $table->foreign('contenido_id')->references('id')->on('contenidos')->onDelete('cascade');
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
        });


        // Tracking
        //Creamos las tablas de tracking
        Schema::create('track_contenido', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("contenido_id")->unsigned();
            $table->integer("convocatoria_id")->unsigned();
            $table->unsignedBigInteger('user_id');
            $table->dateTime('fecha_lectura')->nullable();
            $table->boolean('validado');
            $table->boolean('obligatorio');
            $table->boolean('completado');
            $table->integer("modulo_id")->unsigned();
            $table->integer("asignatura_id")->unsigned();


            $table->foreign('contenido_id')->references('id')->on('contenidos')->onDelete('cascade');
            $table->foreign('convocatoria_id')->references('id')->on('asignatura_convocatorias')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
        });

        Schema::create('track_modulo', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("modulo_id")->unsigned();
            $table->integer("asignatura_id")->unsigned();
            $table->integer("convocatoria_id")->unsigned();
            $table->unsignedBigInteger('user_id');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->float('nota')->default(0);
            $table->boolean('aprobado');
            $table->boolean('completado');

            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
            $table->foreign('convocatoria_id')->references('id')->on('asignatura_convocatorias')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('track_asignatura', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("asignatura_id")->unsigned();
            $table->unsignedBigInteger('user_id');
            $table->integer("convocatoria_id")->unsigned();
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->float('nota')->default(0);
            $table->boolean('aprobado');
            $table->boolean('completado');

            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
            $table->foreign('convocatoria_id')->references('id')->on('asignatura_convocatorias')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('track_contenido_evaluacion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("contenido_id")->unsigned();
            $table->integer("modulo_id")->unsigned();
            $table->integer("asignatura_id")->unsigned();
            $table->integer("convocatoria_id")->unsigned();
            $table->unsignedBigInteger('user_id');
            $table->integer("numero_intento")->unsigned();
            $table->dateTime("fecha_intento")->nullable();
            $table->boolean("validado");
            $table->float("puntuacion_obtenida");
            $table->float("puntuacion_maxima");
            $table->float("nota")->default(0);
            $table->boolean("aprobado");

            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('cascade');
            $table->foreign('asignatura_id')->references('id')->on('asignaturas')->onDelete('cascade');
            $table->foreign('contenido_id')->references('id')->on('contenidos')->onDelete('cascade');
            $table->foreign('convocatoria_id')->references('id')->on('asignatura_convocatorias')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('track_video', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("contenido_id")->unsigned();
            $table->integer("modulo_id")->unsigned();
            $table->integer("asignatura_id")->unsigned();
            $table->unsignedBigInteger('user_id');
            $table->float("user_stop")->nullable();
            $table->float("user_progress")->nullable();
            $table->float("total_video_seconds")->nullable();
            $table->timestamps();
        });

        /* Tablas opcionales de provincia y municipios */
        Schema::create('provincias', function ($table) {
            $table->increments('id');
            $table->string('nombre');
            $table->string('aux');
            $table->string('ccaa');
            $table->integer('showOrder');
            $table->boolean('fixed');
        });

        Schema::create('municipios', function ($table) {
            $table->increments('id');
            $table->integer('provincia_id')->unsigned();
            $table->integer('cod_municipio');
            $table->integer('DC');
            $table->string('nombre');

            $table->foreign('provincia_id')->references('id')->on('provincias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Códigos
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['codigo_id']);
            $table->dropColumn('codigo_id');
        });

        Schema::dropIfExists('codigo_asignaturas');
        Schema::dropIfExists('codigo_roles');
        Schema::dropIfExists('codigos');

        // Matriculación usuarios a asignaturas
        Schema::drop('asignatura_user');

        // Tracking
        Schema::drop('track_contenido_evaluacion');
        Schema::drop('track_asignatura');
        Schema::drop('track_modulo');
        Schema::drop('track_contenido');
        Schema::dropIfExists('track_video');

        // Evaluacion
        Schema::drop('respuesta_resultados');
        Schema::drop('respuesta_translations');
        Schema::drop('respuestas');
        Schema::drop('pregunta_translations');
        Schema::drop('preguntas');
        Schema::drop('tipo_preguntas');

        // Contenidos
        Schema::drop('contenidos_link');
        Schema::drop('contenidos_evaluacion');
        Schema::drop('contenidos_translations');
        Schema::drop('contenidos');
        Schema::drop('tipo_contenidos_translations');
        Schema::drop('tipo_contenidos');

        // Modulos
        Schema::drop('modulo_convocatorias');
        Schema::drop('modulo_translations');
        Schema::drop('modulos');
        Schema::drop('tipo_modulo_translations');
        Schema::drop('tipo_modulos');

        // Convocatorias
        Schema::drop('asignatura_convocatoria_grupos');
        Schema::drop('asignatura_convocatorias');

        // Grupos
        Schema::drop('grupo_users');
        Schema::drop('grupos');

        // Asignaturas
        Schema::drop('asignatura_cursos');
        Schema::drop('asignatura_translations');
        Schema::drop('asignaturas');

        // Certificados
        Schema::drop('certificado_pagina_translations_elementos');
        Schema::drop('certificado_pagina_translations');
        Schema::drop('certificado_paginas');
        Schema::drop('certificados');

        // Cursos
        Schema::drop('curso_translations');
        Schema::drop('cursos');

        //Provincias y Municipios
        Schema::drop('municipios');
        Schema::drop('provincias');
    }
}
