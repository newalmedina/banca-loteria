<?php


namespace Clavel\Elearning\Services;

use App\Services\StoragePathWork;
use Clavel\Basic\Models\Media;
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
use File;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class ExportImportAsignaturaService
{
    protected $asignatura_id;
    protected $basePath = "/exportimport/asignaturas/";
    protected $directorioExportacion;
    protected $guid;

    public function export($asignatura_id)
    {
        $this->asignatura_id = $asignatura_id;
        $this->guid = Uuid::uuid4()->toString();
        $this->directorioExportacion = $this->basePath . $this->guid . "/";

        $asignatura = Asignatura::findOrFail($this->asignatura_id);

        // Exportamos cada uno de los elementos
        $media = [];
        $data = $this->exportarAsignatura($asignatura, $media);
        if (empty($data)) {
            return false;
        }

        // Exportamos los datos
        Storage::disk('local')->put(
            $this->directorioExportacion.$this->guid."_asignatura.json",
            json_encode($data, JSON_PRETTY_PRINT)
        );

        // Exportamos los contenidos multimedia
        Storage::disk('local')->put(
            $this->directorioExportacion.$this->guid."_asignatura_media.json",
            json_encode($media, JSON_PRETTY_PRINT)
        );

        return $this->generateZip();
    }

    public function import($zip_file)
    {
        if (!empty($zip_file)) {
            Storage::disk('local')->put(
                $this->basePath . "/" . $zip_file->getClientOriginalName(),
                File::get($zip_file)
            );

            if (!$this->extractZip($zip_file->getClientOriginalName())) {
                return false;
            }

            // Importamos cada uno de los elementos
            $mediaFiles = $this->importarMedia();

            // Importamos cada uno de los elementos
            if (!$this->importarAsignatura($mediaFiles)) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    private function exportarAsignatura(Asignatura $asignatura, &$media)
    {
        try {
            // Asignatura y traducciones
            $asignaturaExport = $asignatura->toArray();

            $modulos = $this->exportarModulos($asignatura, $media);
            if (empty($modulos)) {
                return false;
            }

            $asignaturaExport['modulos'] = $modulos;
            // Copia de la imagen
            if ($asignatura->image != '') {
                $imageFile = "/asignaturas/" . $asignatura->id . "/" . $asignatura->image;
                if (Storage::disk('local')->exists($imageFile)) {
                    Storage::disk('local')
                        ->copy(
                            $imageFile,
                            $this->directorioExportacion . "images/" . $asignatura->image
                        );
                }
            }

            return $asignaturaExport;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function exportarModulos(Asignatura $asignatura, &$media)
    {
        try {
            // Recorro los módulos de la $asinatura
            $modulos = [];
            foreach ($asignatura->modulos as $modulo) {
                $moduloExport = $modulo->toArray();
                $contenidosExport = $this->exportarContenidos($asignatura, $modulo, $media);

                // Si hay contenidos vacios igualmente exportamos sin error
                $moduloExport['contenidos'] = $contenidosExport;

                $modulos[] = $moduloExport;

                // Copia de la imagen
                if ($modulo->image != '') {
                    $imageFile = "/modulos/" . $modulo->asignatura_id . "/" . $modulo->image;
                    if (Storage::disk('local')->exists($imageFile)) {
                        Storage::disk('local')
                            ->copy(
                                $imageFile,
                                $this->directorioExportacion . "images/modulos/" .
                                    $modulo->asignatura_id . "/" . $modulo->image
                            );
                    }
                }
            }

            return $modulos;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function exportarContenidos(Asignatura $asignatura, Modulo $modulo, &$media)
    {
        try {
            $contenidos = [];
            $mediaIds = [];
            // Por cada módulo, recorro sus contenidos...
            foreach ($modulo->contenidos as $contenido) {
                $ids = $this->getMediaIds($contenido);
                if (!empty($ids)) {
                    $mediaIds = array_merge($mediaIds, $ids);
                }

                $contenidoExport = $contenido->toArray();

                $evaluaciones = [];

                // Miramos si hay evaluación para guardar sus datos
                foreach ($contenido->evaluacion()->get() as $evaluacion) {
                    if (!empty($evaluacion)) {
                        $evaluaciones[] = $evaluacion->toArray();
                    }
                }
                $contenidoExport['evaluaciones'] = $evaluaciones;

                // Preguntas
                $preguntasExport = $this->exportarPreguntas($asignatura, $modulo, $contenido);
                $contenidoExport['preguntas'] = $preguntasExport;

                $contenidos[] = $contenidoExport;

                // traducciones
                /*
                $contenido_trans =  ContenidoTranslation::where("contenido_id", "=", $contenido->id)->get();
                foreach ($contenido_trans as $trans) {

                    // Copia de la imagen
                    if ($trans->mp4 != '' || $trans->webm != '' || $trans->vtt != '') {
                        $myServiceSPW = new StoragePathWork("");
                        $myServiceSPW->pathConnection = "custom";
                        try {
                            if ($trans->mp4 != '') {
                                $myServiceSPW->copyFile($trans->mp4, "/" . $contenido->id,
                                $this->directorioExportacion."images/contenidos/" . $contenido->id."/".$trans->mp4);
                            }
                        } catch (FileNotFoundException $e) {
                            continue;
                        }
                        try {
                            if ($trans->webm != '') {
                                $myServiceSPW->copyFile($trans->webm, "/" .
                                $contenido->id, $this->directorioExportacion."images/contenidos/" .
                                $contenido->id."/".$trans->webm);
                            }
                        } catch (FileNotFoundException $e) {
                            continue;
                        }
                        try {
                            if ($trans->vtt != '') {
                                $myServiceSPW->copyFile($trans->vtt, "/" .
                                $contenido->id, $this->directorioExportacion."images/contenidos/" .
                                $contenido->id."/".$trans->vtt);
                            }
                        } catch (FileNotFoundException $e) {
                            continue;
                        }
                    }
                }
                */
            }

            // Exportamos los medias
            foreach ($mediaIds as $m) {
                $mediaFile = Media::find($m);
                if (!empty($mediaFile)) {
                    $media[] = $mediaFile->toArray();

                    $imageFile =  $mediaFile->path."/".$mediaFile->filename;
                    if (Storage::disk('local')->exists($imageFile)
                        &&
                        !Storage::disk('local')->exists($this->directorioExportacion."images/media/" . $imageFile)
                    ) {
                        Storage::disk('local')
                            ->copy(
                                $imageFile,
                                $this->directorioExportacion."images/media/" . $imageFile
                            );
                    }
                }
            }


            return $contenidos;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getMediaIds($contenido)
    {
        $mediaIds = [];
        switch ($contenido->tipo_contenido_id) {
            case 4:
                $mediaPath = $contenido->storepath;
                $medias = Media::where('path', $mediaPath)
                    ->get();
                foreach ($medias as $media) {
                    $mediaIds[] = $media->id;
                }
                /*
                SELECT *
FROM media
WHERE path = '/media/introduccion3'
*/
                break;
            case 5:
                // Buscando los id por https://www.xxxx.com/media/getAnnex/84
                preg_match_all(
                    "/\/media\/getAnnex\/([0-9]+)$/",
                    $contenido->media_url,
                    $coincidencias,
                    PREG_OFFSET_CAPTURE
                );
                $ids = $coincidencias[1];
                foreach ($ids as $id) {
                    $mediaIds[] = $id[0];
                }
                break;
            default:
                // Buscando los id por "/media/getAnnex/1"
                preg_match_all(
                    "/\"\/media\/getAnnex\/(.*?)\"/",
                    $contenido->contenido,
                    $coincidencias,
                    PREG_OFFSET_CAPTURE
                );
                $ids = $coincidencias[1];
                foreach ($ids as $id) {
                    $mediaIds[] = $id[0];
                }
        }


        return $mediaIds;
    }

    public function replaceMediaIds($contenido, $idsToReplace)
    {
        if (empty($idsToReplace)) {
            return $contenido;
        }
        $mediaIds = [];
        preg_match_all("/\"\/media\/getAnnex\/(.*?)\"/", $contenido, $coincidencias, PREG_OFFSET_CAPTURE);
        $ids = $coincidencias[1];
        foreach ($ids as $id) {
            $mediaIds[] = $id[0];
        }

        foreach ($mediaIds as $id) {
            $contenido = str_replace("\"/media/getAnnex/" .
                $id . "\"", "\"/media/getAnnex/" . $idsToReplace[$id] . "\"", $contenido);
        }

        return $contenido;
    }

    public function replaceUrlMediaIds($contenido, $idsToReplace)
    {
        if (empty($idsToReplace) || empty($contenido)) {
            return $contenido;
        }


        $array = explode("/", $contenido);
        if (!empty($array)) {
            $id = intval(end($array));
            if (!empty($id)) {
                $contenido = env('APP_URL')."/media/getAnnex/" . $idsToReplace[$id];
            }
        }

        return $contenido;
    }

    private function exportarPreguntas(Asignatura $asignatura, Modulo $modulo, Contenido $contenido)
    {
        try {
            $preguntas = [];
            // Por cada contenido, compruebo que tiene preguntas y recorro las mismas recorro sus contenidos...
            if (!empty($contenido->preguntas)) {
                foreach ($contenido->preguntas as $pregunta) {
                    $preguntasExport = $pregunta->toArray();

                    // Respuestas
                    $RespuestasExport = $this->exportarRespuestas($asignatura, $modulo, $contenido, $pregunta);
                    if (empty($RespuestasExport)) {
                        return false;
                    }
                    $preguntasExport['respuestas'] = $RespuestasExport;

                    $preguntas[] = $preguntasExport;
                }
            }

            return $preguntas;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function exportarRespuestas(
        Asignatura $asignatura,
        Modulo $modulo,
        Contenido $contenido,
        Pregunta $pregunta
    ) {
        try {
            $respuestas = [];
            // Recorro todas las respuestas de la pregunta
            foreach ($pregunta->respuestas as $respuesta) {
                $respuestasExport = $respuesta->toArray();

                $respuestas[] = $respuestasExport;
            }

            return $respuestas;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function generateZip()
    {
        $zip_name = $this->guid . '.zip';
        $zip_file = storage_path() . "/app" . $this->basePath . $zip_name;
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE);

        $path = storage_path('app' . $this->directorioExportacion);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();

                // extracting filename with substr/strlen
                $relativePath = str_replace($path, "", $filePath);

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return response()->download($zip_file);
    }

    private function extractZip($filename)
    {
        $zip_file = storage_path() . "/app" . $this->basePath . $filename;
        $this->guid = str_replace(".zip", "", $filename);
        $dir_extract = storage_path() . "/app" . $this->basePath . $this->guid;
        $zip = new \ZipArchive();
        $x = $zip->open($zip_file);
        if ($x === true) {
            $zip->extractTo($dir_extract);
            $zip->close();
        }

        return true;
    }

    private function importarMedia()
    {
        $mediaFiles = [];

        $dir_import = $this->basePath . $this->guid;
        $filePath = $dir_import . "/" . $this->guid . "_asignatura_media.json";
        if (!Storage::disk('local')->exists($filePath)) {
            return $mediaFiles;
        }

        try {
            $mediaData = Storage::disk('local')->get($filePath);
            $mediaJson = json_decode($mediaData, true);

            foreach ($mediaJson as $m) {
                // Nuevos valores
                $media = new Media();
                $media->user_id = auth()->user()->id;
                $media->filename = $m['filename'];
                $media->mime = $m['mime'];
                $media->original_filename = $m['original_filename'];
                $media->path = $m['path'];
                $media->size = $m['size'];
                $media->save();

                $mediaFiles[$m['id']] = $media->id;

                // Copia de la imagen
                if (Storage::disk('local')->exists($dir_import .
                    "/images/media/" . $m['path'] . "/" . $m['filename'])) {
                    Storage::delete($m['path'] . "/" . $m['filename']);

                    Storage::disk('local')
                        ->copy(
                            $dir_import . "/images/media/" . $m['path'] . "/" . $m['filename'],
                            $m['path'] . "/" . $m['filename']
                        );
                }
            }
        } catch (\Exception $e) {
        }
        return $mediaFiles;
    }


    private function importarAsignatura($mediaFiles)
    {
        $dir_import = $this->basePath . $this->guid;
        $filePath = $dir_import . "/" . $this->guid . "_asignatura.json";
        if (!Storage::disk('local')->exists($filePath)) {
            return false;
        }

        try {
            $asignaturaData = Storage::disk('local')->get($filePath);
            $asignaturaJson = json_decode($asignaturaData, true);

            // Nuevos valores
            $asignatura = new Asignatura();
            $asignatura->image = $asignaturaJson['image'];
            $asignatura->activo = false;
            $asignatura->obligatorio_id = $asignaturaJson['obligatorio_id'];
            $asignatura->parent_id = null; //$asignaturaJson['id'];
            $asignatura->origin_id = null;
            // (empty($asignaturaJson['origin_id'])) ? $asignaturaJson['id'] : $asignaturaJson['origin_id'];

            $asignatura->save();

            $asignaturaTransJson =  $asignaturaJson['translations'];

            foreach ($asignaturaTransJson as $trans) {
                $itemTrans = new AsignaturaTranslation();
                $itemTrans->asignatura_id = $asignatura->id;
                $itemTrans->locale = $trans['locale'];
                $itemTrans->titulo = $trans['titulo'] . " (cloned)";
                $itemTrans->url_amigable = $trans['url_amigable'];
                $itemTrans->breve = $trans['breve'];
                $itemTrans->descripcion = $trans['descripcion'];
                $itemTrans->creditos = $trans['creditos'];
                $itemTrans->academico = $trans['academico'];
                $itemTrans->caracteristica = $trans['caracteristica'];
                $itemTrans->plazas = $trans['plazas'];
                $itemTrans->admision = $trans['admision'];
                $itemTrans->coordinacion = $trans['coordinacion'];
                $itemTrans->estudiantes = $trans['estudiantes'];
                $itemTrans->save();
            }

            // Copia de la imagen
            if ($asignaturaJson['image'] != '') {
                if (Storage::disk('local')->exists($dir_import . "/images/" . $asignaturaJson['image'])) {
                    Storage::delete("/asignaturas/" .  $asignatura->id . "/" . $asignaturaJson['image']);

                    Storage::disk('local')
                        ->copy(
                            $dir_import . "/images/" . $asignaturaJson['image'],
                            "/asignaturas/" .  $asignatura->id . "/" . $asignaturaJson['image']
                        );
                }
            }

            // Importar modulos
            $modulos = $asignaturaJson['modulos'];
            if (!$this->importarModulos($asignatura, $modulos, $mediaFiles)) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function importarModulos(Asignatura $asignatura, $modulos, $mediaFiles)
    {
        $dir_import = $this->basePath . $this->guid;

        try {
            foreach ($modulos as $modulo) {
                $new_modulo = new Modulo();
                $new_modulo->activo = $modulo['activo'];
                $new_modulo->fondo = $modulo['fondo'];
                $new_modulo->image = $modulo['image'];
                $new_modulo->asignatura_id = $asignatura->id;
                $new_modulo->tipo_modulo_id = $modulo['tipo_modulo_id'];
                $new_modulo->puntua = $modulo['puntua'];
                $new_modulo->peso = $modulo['peso'];
                $new_modulo->orden = $modulo['orden'];
                $new_modulo->save();

                $moduloTransJson =  $modulo['translations'];

                foreach ($moduloTransJson as $trans) {
                    $itemTrans = new ModuloTranslation();
                    $itemTrans->modulo_id = $new_modulo->id;
                    $itemTrans->locale = $trans['locale'];
                    $itemTrans->nombre = $trans['nombre'];
                    $itemTrans->url_amigable = $trans['url_amigable'];
                    $itemTrans->descripcion = $trans['descripcion'];
                    $itemTrans->coordinacion = $trans['coordinacion'];
                    $itemTrans->save();
                }

                // Copia de la imagen
                if ($modulo['image'] != '') {
                    if (Storage::disk('local')->exists($dir_import . "/images/modulos/" .
                        $modulo['asignatura_id'] . "/" . $modulo['image'])) {
                        Storage::delete("/modulos/" . $asignatura->id . "/" . $modulo['image']);
                        Storage::disk('local')
                            ->copy(
                                $dir_import . "/images/modulos/" . $modulo['asignatura_id'] . "/" .
                                    $modulo['image'],
                                "/modulos/" . $asignatura->id . "/" . $modulo['image']
                            );
                    }
                }

                // Importar modulos
                $contenidos = $modulo['contenidos'];
                if (!$this->importarContenidos($asignatura, $new_modulo, $contenidos, $mediaFiles)) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function importarContenidos(Asignatura $asignatura, Modulo $modulo, $contenidos, $mediaFiles)
    {
        $dir_import = $this->basePath . $this->guid;

        try {
            $a_parent_id = [];
            foreach ($contenidos as $contenido) {
                // Creamos el contenido dentro del módulo
                $new_contenido = new Contenido();
                $new_contenido->activo = $contenido['activo'];
                $new_contenido->obligatorio = $contenido['obligatorio'];
                $new_contenido->modulo_id = $modulo->id;
                $new_contenido->tipo_contenido_id = $contenido['tipo_contenido_id'];
                $new_contenido->pantalla_completa = $contenido['pantalla_completa'];
                $new_contenido->descargar_pdf = $contenido['descargar_pdf'];
                $new_contenido->generar_pdf = $contenido['generar_pdf'];
                $new_contenido->pdf_archivo = $contenido['pdf_archivo'];
                $new_contenido->modal = $contenido['modal'];
                $new_contenido->storepath = $contenido['storepath'];
                $new_contenido->media_url = $this->replaceUrlMediaIds($contenido['media_url'], $mediaFiles);
                $new_contenido->save();
                $a_parent_id[$contenido['id']] = $new_contenido->id;

                // Idiomas
                $contenido_trans =  $contenido['translations'];
                foreach ($contenido_trans as $trans) {
                    // Sustituimos los media por los nuevos

                    $mediaIdNuevos = $this->replaceMediaIds($trans['contenido'], $mediaFiles);

                    $itemTrans = new ContenidoTranslation();
                    $itemTrans->contenido_id = $new_contenido->id;
                    $itemTrans->locale = $trans['locale'];
                    $itemTrans->nombre = $trans['nombre'];
                    $itemTrans->contenido = $mediaIdNuevos;
                    $itemTrans->contenido_aprobado = $trans['contenido_aprobado'];
                    $itemTrans->contenido_suspendido = $trans['contenido_suspendido'];
                    $itemTrans->url_amigable = $trans['url_amigable'];
                    $itemTrans->mp4 = $trans['mp4'];
                    $itemTrans->webm = $trans['webm'];
                    $itemTrans->vtt = $trans['vtt'];
                    $itemTrans->save();

                    /*
                    // Copia de la imagen
                    if ($trans->mp4 != '' || $trans->webm != '' || $trans->vtt != '') {
                        $myServiceSPW = new StoragePathWork("");
                        $myServiceSPW->pathConnection = "custom";
                        try {
                            if ($trans->mp4 != '') {
                                $myServiceSPW->copyFile($trans->mp4, "/" . $contenido->id, "/" . $new_contenido->id);
                            }
                        } catch (FileNotFoundException $e) {
                            continue;
                        }
                        try {
                            if ($trans->webm != '') {
                                $myServiceSPW->copyFile($trans->webm, "/" . $contenido->id, "/" . $new_contenido->id);
                            }
                        } catch (FileNotFoundException $e) {
                            continue;
                        }
                        try {
                            if ($trans->vtt != '') {
                                $myServiceSPW->copyFile($trans->vtt, "/" . $contenido->id, "/" . $new_contenido->id);
                            }
                        } catch (FileNotFoundException $e) {
                            continue;
                        }
                    }
                    */
                }


                // Miramos si hay evaluación para guardar sus datos
                $evaluaciones =  $contenido['evaluaciones'];
                foreach ($evaluaciones as $evaluacion) {
                    if (!empty($evaluacion)) {
                        $new_evaluacion = new ContenidoEvaluacion();
                        $new_evaluacion->contenido_id = $new_contenido->id;
                        $new_evaluacion->mostrar_respuesta = $evaluacion['mostrar_respuesta'];
                        $new_evaluacion->mostrar_resultado = $evaluacion['mostrar_resultado'];
                        $new_evaluacion->evaluacion_final = $evaluacion['evaluacion_final'];
                        $new_evaluacion->peso_final = $evaluacion['peso_final'];
                        $new_evaluacion->limitante = $evaluacion['limitante'];
                        $new_evaluacion->porcentaje_aprobado = $evaluacion['porcentaje_aprobado'];
                        $new_evaluacion->permitir_resetear = $evaluacion['permitir_resetear'];
                        $new_evaluacion->preguntas_aleatorias = $evaluacion['preguntas_aleatorias'];
                        $new_evaluacion->respuestas_aleatorias = $evaluacion['respuestas_aleatorias'];
                        $new_evaluacion->numero_resets = $evaluacion['numero_resets'];
                        $new_evaluacion->numero_preguntas_visibles = $evaluacion['numero_preguntas_visibles'];
                        $new_evaluacion->presencial = $evaluacion['presencial'];
                        $new_evaluacion->peso = $evaluacion['peso'];
                        $new_evaluacion->puntua = $evaluacion['puntua'];
                        $new_evaluacion->save();
                    }
                }

                // Importar preguntas
                $preguntas = $contenido['preguntas'];
                if (!$this->importarPreguntas($asignatura, $modulo, $new_contenido, $preguntas)) {
                    return false;
                }
            }

            // Una vez acabados asigno a los contendios del árbol de Celko su nuevo id
            foreach ($contenidos as $contenido) {
                $new_contenido = Contenido::find($a_parent_id[$contenido['id']]);
                if (empty($contenido['parent_id'])) {
                    $new_contenido->makeRoot();
                } else {
                    $parent = Contenido::find($a_parent_id[$contenido['parent_id']]);
                    $new_contenido->makeLastChildOf($parent);
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function importarPreguntas(Asignatura $asignatura, Modulo $modulo, Contenido $contenido, $preguntas)
    {
        $dir_import = $this->basePath . $this->guid;

        try {
            foreach ($preguntas as $pregunta) {
                $new_pregunta = new Pregunta();
                $new_pregunta->activa = $pregunta['activa'];
                $new_pregunta->tipo_pregunta_id = $pregunta['tipo_pregunta_id'];
                $new_pregunta->contenido_id = $contenido->id;
                $new_pregunta->orden = $pregunta['orden'];
                $new_pregunta->obligatoria = $pregunta['obligatoria'];
                $new_pregunta->save();

                // Idiomas
                $preguntas_trans = $pregunta['translations'];
                foreach ($preguntas_trans as $trans) {
                    $itemTrans = new PreguntaTranslation();
                    $itemTrans->pregunta_id = $new_pregunta->id;
                    $itemTrans->locale = $trans['locale'];
                    $itemTrans->nombre = $trans['nombre'];
                    $itemTrans->save();
                }

                // Importar respuestas
                $respuestas = $pregunta['respuestas'];
                if (!$this->importarRespuestas($asignatura, $modulo, $contenido, $new_pregunta, $respuestas)) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function importarRespuestas(
        Asignatura $asignatura,
        Modulo $modulo,
        Contenido $contenido,
        Pregunta $pregunta,
        $respuestas
    ) {
        $dir_import = $this->basePath . $this->guid;

        try {
            foreach ($respuestas as $respuesta) {
                $new_respuesta = new Respuesta();
                $new_respuesta->activa = $respuesta['activa'];
                $new_respuesta->correcta = $respuesta['correcta'];
                $new_respuesta->pregunta_id = $pregunta->id;
                $new_respuesta->orden = $respuesta['orden'];
                $new_respuesta->puntos_correcta = $respuesta['puntos_correcta'];
                $new_respuesta->puntos_incorrecta = $respuesta['puntos_incorrecta'];
                $new_respuesta->save();

                // Idiomas
                $preguntas_trans = $respuesta['translations'];
                foreach ($preguntas_trans as $trans) {
                    $itemTrans = new RespuestaTranslation();
                    $itemTrans->respuesta_id = $new_respuesta->id;
                    $itemTrans->locale = $trans['locale'];
                    $itemTrans->nombre = $trans['nombre'];
                    $itemTrans->comentario = $trans['comentario'];
                    $itemTrans->save();
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
