<?php

namespace Clavel\Elearning\Services;

use App\Services\StoragePathWork;
use Clavel\Elearning\Models\Asignatura;
use Clavel\Elearning\Models\AsignaturaTranslation;
use Clavel\Elearning\Models\Contenido;
use Clavel\Elearning\Models\ContenidoEvaluacion;
use Clavel\Elearning\Models\ContenidoTranslation;
use Clavel\Elearning\Models\Modulo;
use Clavel\Elearning\Models\ModuloTranslation;
use Clavel\Elearning\Models\Pregunta;
use Clavel\Elearning\Models\PreguntaTranslation;
use Clavel\Elearning\Models\Respuesta;
use Clavel\Elearning\Models\RespuestaTranslation;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class CloneAsignaturaService
{
    protected $asignatura_id;

    public function __construct($asignatura_id)
    {
        $this->asignatura_id = $asignatura_id;
    }

    public function clonar()
    {
        $new_asignatura = new Asignatura();
        $asignatura = Asignatura::findOrFail($this->asignatura_id);
        $a_idAcabarModules = array();
        $a_idAcabarContenidos = array();
        $a_idAcabarPreguntas = array();

        // Se clona la información general de la asignatura....
        // No se puede usar el replicate de laravel ya que es multiidoma y
        // solamente copia el idioma en el que esta la web, así que hay que clonar a mano...
        if (!$this->saveAsignatura($new_asignatura, $asignatura)) {
            return false;
        }

        // Guardamos los módulos, pasa exactamente lo mismo, no funciona el multiidioma
        // con el replicate de laravel y tengo que hacerlo a mano
        if (!$this->saveModules($new_asignatura, $asignatura, $a_idAcabarModules)) {
            return false;
        }

        // Guardamos los contenidos, idem anteriores
        if (!$this->saveContenidos($asignatura, $a_idAcabarModules, $a_idAcabarContenidos)) {
            return false;
        }

        // Guardamos las preguntas, idem anteriores
        if (!$this->savePreguntas($asignatura, $a_idAcabarContenidos, $a_idAcabarPreguntas)) {
            return false;
        }

        // Guardamos las respuestas, idem anteriores
        if (!$this->saveRespuestas($asignatura, $a_idAcabarPreguntas)) {
            return false;
        }

        return true;
    }

    private function saveAsignatura($new_asignatura, $asignatura)
    {
        try {
            // Nuevos valores
            $new_asignatura->image = $asignatura->image;
            $new_asignatura->activo = false;
            $new_asignatura->obligatorio_id = $asignatura->obligatorio_id;
            $new_asignatura->parent_id = $asignatura->id;
            $new_asignatura->origin_id = (empty($asignatura->origin_id)) ? $asignatura->id : $asignatura->origin_id;

            $new_asignatura->save();

            // Copia de la imagen
            if ($asignatura->image != '') {
                $myServiceSPW = new StoragePathWork("asignaturas");
                $myServiceSPW->copyFile($asignatura->image, "/" . $asignatura->id, "/" . $new_asignatura->id);
            }

            // traducciones
            $asignatura_trans = AsignaturaTranslation::where("asignatura_id", "=", $asignatura->id)->get();
            foreach ($asignatura_trans as $trans) {
                $itemTrans = new AsignaturaTranslation();
                $itemTrans->asignatura_id = $new_asignatura->id;
                $itemTrans->locale = $trans->locale;
                $itemTrans->titulo = $trans->titulo . " (cloned)";
                $itemTrans->url_amigable = $trans->url_amigable;
                $itemTrans->breve = $trans->breve;
                $itemTrans->descripcion = $trans->descripcion;
                $itemTrans->creditos = $trans->creditos;
                $itemTrans->academico = $trans->academico;
                $itemTrans->caracteristica = $trans->caracteristica;
                $itemTrans->plazas = $trans->plazas;
                $itemTrans->admision = $trans->admision;
                $itemTrans->coordinacion = $trans->coordinacion;
                $itemTrans->estudiantes = $trans->estudiantes;
                $itemTrans->save();
            }

            // Cursos en los que esta asociada la asignatura
            if ($asignatura->cursoPivot()->count() > 0) {
                $new_asignatura->cursoPivot()->sync($asignatura->cursoPivot()->get());
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function saveModules($new_asignatura, $asignatura, &$a_idAcabarModules)
    {
        try {
            $a_idAcabar = array();
            // Recorro los módulos de la $asinatura
            foreach ($asignatura->modulos as $modulo) {
                $new_modulo = new Modulo();
                $new_modulo->activo = $modulo->activo;
                $new_modulo->fondo = $modulo->fondo;
                $new_modulo->image = $modulo->image;
                $new_modulo->asignatura_id = $new_asignatura->id;
                $new_modulo->tipo_modulo_id = $modulo->tipo_modulo_id;
                $new_modulo->puntua = $modulo->puntua;
                $new_modulo->peso = $modulo->peso;
                $new_modulo->orden = $modulo->orden;
                $new_modulo->save();
                $a_idAcabar[$modulo->id] = $new_modulo->id;

                // Copia de la imagen
                if ($modulo->image != '') {
                    $myServiceSPW = new StoragePathWork("asignaturas");
                    $myServiceSPW->copyFile($modulo->image, "/" . $modulo->id, "/" . $new_modulo->id);
                }

                // Idiomas
                $modulo_trans = ModuloTranslation::where("modulo_id", "=", $modulo->id)->get();
                foreach ($modulo_trans as $trans) {
                    $itemTrans = new ModuloTranslation();
                    $itemTrans->modulo_id = $new_modulo->id;
                    $itemTrans->locale = $trans->locale;
                    $itemTrans->nombre = $trans->nombre;
                    $itemTrans->url_amigable = $trans->url_amigable;
                    $itemTrans->descripcion = $trans->descripcion;
                    $itemTrans->coordinacion = $trans->coordinacion;
                    $itemTrans->save();
                }
            }

            // Una vez acabados asigno a los módulos obligatorios su nuevo id
            foreach ($asignatura->modulos as $modulo) {
                if ($modulo->obligatorio_id != '') {
                    $new_modulo = Modulo::find($a_idAcabar[$modulo->id]);
                    $new_modulo->obligatorio_id = $a_idAcabar[$modulo->obligatorio_id];
                    $new_modulo->save();
                }
            }
            $a_idAcabarModules = $a_idAcabar;

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function saveContenidos($asignatura, $a_idAcabarModules, &$a_idAcabarContenidos)
    {
        try {
            $a_parent_id = array();
            // Recorro los modulos de la asingatura a clonar
            foreach ($asignatura->modulos as $modulo) {
                // Por cada módulo, recorro sus contendios...
                foreach ($modulo->contenidos as $contenido) {
                    // Creamos el contenido dentro del módulo
                    $new_contenido = new Contenido();
                    $new_contenido->activo = $contenido->activo;
                    $new_contenido->obligatorio = $contenido->obligatorio;
                    $new_contenido->modulo_id = $a_idAcabarModules[$modulo->id];
                    $new_contenido->tipo_contenido_id = $contenido->tipo_contenido_id;
                    $new_contenido->pantalla_completa = $contenido->pantalla_completa;
                    $new_contenido->descargar_pdf = $contenido->descargar_pdf;
                    $new_contenido->generar_pdf = $contenido->generar_pdf;
                    $new_contenido->pdf_archivo = $contenido->pdf_archivo;
                    $new_contenido->modal = $contenido->modal;
                    $new_contenido->storepath = $contenido->storepath;
                    $new_contenido->save();
                    $a_parent_id[$contenido->id] = $new_contenido->id;

                    // Idiomas
                    $contenido_trans = ContenidoTranslation::where("contenido_id", "=", $contenido->id)->get();
                    foreach ($contenido_trans as $trans) {
                        $itemTrans = new ContenidoTranslation();
                        $itemTrans->contenido_id = $new_contenido->id;
                        $itemTrans->locale = $trans->locale;
                        $itemTrans->nombre = $trans->nombre;
                        $itemTrans->contenido = $trans->contenido;
                        $itemTrans->contenido_aprobado = $trans->contenido_aprobado;
                        $itemTrans->contenido_suspendido = $trans->contenido_suspendido;
                        $itemTrans->url_amigable = $trans->url_amigable;
                        $itemTrans->mp4 = $trans->mp4;
                        $itemTrans->webm = $trans->webm;
                        $itemTrans->vtt = $trans->vtt;
                        $itemTrans->save();

                        // Copia de la imagen
                        if ($trans->mp4 != '' || $trans->webm != '' || $trans->vtt != '') {
                            $myServiceSPW = new StoragePathWork("");
                            $myServiceSPW->pathConnection = "custom";
                            try {
                                if ($trans->mp4 != '') {
                                    $myServiceSPW->copyFile($trans->mp4, "/" .
                                        $contenido->id, "/" . $new_contenido->id);
                                }
                            } catch (FileNotFoundException $e) {
                                continue;
                            }
                            try {
                                if ($trans->webm != '') {
                                    $myServiceSPW->copyFile($trans->webm, "/" .
                                        $contenido->id, "/" . $new_contenido->id);
                                }
                            } catch (FileNotFoundException $e) {
                                continue;
                            }
                            try {
                                if ($trans->vtt != '') {
                                    $myServiceSPW->copyFile($trans->vtt, "/" .
                                        $contenido->id, "/" . $new_contenido->id);
                                }
                            } catch (FileNotFoundException $e) {
                                continue;
                            }
                        }
                    }

                    // Miramos si hay evaluación para guardar sus datos
                    foreach ($contenido->evaluacion()->get() as $evaluacion) {
                        if (!empty($evaluacion)) {
                            $new_evaluacion = new ContenidoEvaluacion();
                            $new_evaluacion->contenido_id = $new_contenido->id;
                            $new_evaluacion->mostrar_respuesta = $evaluacion->mostrar_respuesta;
                            $new_evaluacion->mostrar_resultado = $evaluacion->mostrar_resultado;
                            $new_evaluacion->evaluacion_final = $evaluacion->evaluacion_final;
                            $new_evaluacion->peso_final = $evaluacion->peso_final;
                            $new_evaluacion->limitante = $evaluacion->limitante;
                            $new_evaluacion->porcentaje_aprobado = $evaluacion->porcentaje_aprobado;
                            $new_evaluacion->permitir_resetear = $evaluacion->permitir_resetear;
                            $new_evaluacion->preguntas_aleatorias = $evaluacion->preguntas_aleatorias;
                            $new_evaluacion->respuestas_aleatorias = $evaluacion->respuestas_aleatorias;
                            $new_evaluacion->numero_resets = $evaluacion->numero_resets;
                            $new_evaluacion->numero_preguntas_visibles = $evaluacion->numero_preguntas_visibles;
                            $new_evaluacion->presencial = $evaluacion->presencial;
                            $new_evaluacion->peso = $evaluacion->peso;
                            $new_evaluacion->puntua = $evaluacion->puntua;
                            $new_evaluacion->modulo_id = $a_idAcabarModules[$modulo->id];
                            $new_evaluacion->save();
                        }
                    }
                }

                // Una vez acabados asigno a los contendios del árbol de Celko su nuevo id
                foreach ($modulo->contenidos as $contenido) {
                    $new_contenido = Contenido::find($a_parent_id[$contenido->id]);
                    if (empty($contenido->parent_id)) {
                        $new_contenido->makeRoot();
                    } else {
                        $parent = Contenido::find($a_parent_id[$contenido->parent_id]);
                        $new_contenido->makeLastChildOf($parent);
                    }
                }
            }

            $a_idAcabarContenidos = $a_parent_id;

            return true;
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }

    private function savePreguntas($asignatura, $a_idAcabarContenidos, &$a_idAcabarPreguntas)
    {
        try {
            $a_parent_id = array();
            // Recorro los modulos de la asingatura a clonar
            foreach ($asignatura->modulos as $modulo) {
                // Por cada módulo, recorro sus contendios...
                foreach ($modulo->contenidos as $contenido) {
                    // Por cada contenido, compruebo que tiene preguntas y recorro las
                    // mismas recorro sus contendios...
                    if (!empty($contenido->preguntas)) {
                        foreach ($contenido->preguntas as $pregunta) {
                            $new_pregunta = new Pregunta();
                            $new_pregunta->activa = $pregunta->activa;
                            $new_pregunta->tipo_pregunta_id = $pregunta->tipo_pregunta_id;
                            $new_pregunta->contenido_id = $a_idAcabarContenidos[$contenido->id];
                            $new_pregunta->orden = $pregunta->orden;
                            $new_pregunta->obligatoria = $pregunta->obligatoria;
                            $new_pregunta->save();
                            $a_parent_id[$pregunta->id] = $new_pregunta->id;

                            // Idiomas
                            $preguntas_trans = PreguntaTranslation::where("pregunta_id", "=", $pregunta->id)
                                ->get();
                            foreach ($preguntas_trans as $trans) {
                                $itemTrans = new PreguntaTranslation();
                                $itemTrans->pregunta_id = $new_pregunta->id;
                                $itemTrans->locale = $trans->locale;
                                $itemTrans->nombre = $trans->nombre;
                                $itemTrans->save();
                            }
                        }
                    }
                }
            }

            $a_idAcabarPreguntas = $a_parent_id;

            return true;
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }

    private function saveRespuestas($asignatura, $a_idAcabarPreguntas)
    {
        try {
            // Recorro los modulos de la asingatura a clonar
            foreach ($asignatura->modulos as $modulo) {
                // Por cada módulo, recorro sus contendios...
                foreach ($modulo->contenidos as $contenido) {
                    // Por cada contenido, compruebo que tiene preguntas y recorro las mismas recorro
                    // sus contendios...
                    if (!empty($contenido->preguntas)) {
                        foreach ($contenido->preguntas as $pregunta) {
                            // Recorro todas las respuestas de la pregunta
                            foreach ($pregunta->respuestas as $respuesta) {
                                $new_respuesta = new Respuesta();
                                $new_respuesta->activa = $respuesta->activa;
                                $new_respuesta->correcta = $respuesta->correcta;
                                $new_respuesta->pregunta_id = $a_idAcabarPreguntas[$pregunta->id];
                                $new_respuesta->orden = $respuesta->orden;
                                $new_respuesta->puntos_correcta = $respuesta->puntos_correcta;
                                $new_respuesta->puntos_incorrecta = $respuesta->puntos_incorrecta;
                                $new_respuesta->save();

                                // Idiomas
                                $preguntas_trans = RespuestaTranslation::where("respuesta_id", "=", $respuesta->id)
                                    ->get();
                                foreach ($preguntas_trans as $trans) {
                                    $itemTrans = new RespuestaTranslation();
                                    $itemTrans->respuesta_id = $new_respuesta->id;
                                    $itemTrans->locale = $trans->locale;
                                    $itemTrans->nombre = $trans->nombre;
                                    $itemTrans->comentario = $trans->comentario;
                                    $itemTrans->save();
                                }
                            }
                        }
                    }
                }
            }

            return true;
        } catch (\Exception $e) {
            dd($e);
            return false;
        }
    }
}
