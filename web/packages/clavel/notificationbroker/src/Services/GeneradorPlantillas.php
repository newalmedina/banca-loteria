<?php

namespace Clavel\NotificationBroker\Services;

use Clavel\NotificationBroker\Models\NotificationType;
use Clavel\NotificationBroker\Models\NotificationTypeTranslation;
use Clavel\NotificationBroker\Models\Plantilla;
use Illuminate\Support\Facades\Storage;

class GeneradorPlantillas
{
    protected $plantilla = null;
    protected $filename = "";
    protected $templateSlug = "";

    protected $tipoNotifiacion = array(
        "sms" => array("path" => "sms", "header" => "", "footer" => ""),
        "email" => array("path" => "emails", "header" => "", "footer" => "")
    );

    public function __construct($plantilla)
    {
        $this->plantilla = $plantilla;
        $this->setDataForEmails();
    }

    public function createTemplate()
    {
        // Primero borramos la plantilla en el caso que la haya
        if ($this->plantilla->archivo != '') {
            $this->deleteIfExistsFile();
        }

        // Generamos el archivo blade
        $filename = $this->generateInfoNewFile();
        if ($filename === false) {
            return false;
        }

        // Guardamos en BBDD
        return $this->saveTemplate($filename);
    }

    public function deleteIfExistsFile()
    {
        try {
            if (Storage::disk('templates')
                ->exists(
                    "/" . $this->tipoNotifiacion[$this->plantilla->tipo]["path"] . "/" . $this->plantilla->archivo
                )
            ) {
                Storage::disk('templates')
                    ->delete(
                        "/" . $this->tipoNotifiacion[$this->plantilla->tipo]["path"] . "/" . $this->plantilla->archivo
                    );
            }

            $this->filename = $this->plantilla->archivo;
            $this->templateSlug = str_replace(".blade.php", "", $this->filename);
            $this->dropFileBBDD();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /* FUNCIONALIDADES PRIVADAS DEL SERVICIO */
    /* ------------------------------------- */
    private function setDataForEmails()
    {
        $strHeader = "@extends('notificationbroker::front.email.broker.default')\n\n";
        $strHeader .= "@section('title')\n";
        $strHeader .= "      @parent {{ @\$payload['to'] }}\n";
        $strHeader .= "@stop\n\n";
        $strHeader .= "@section('content')\n\n";
        $strHeader .= "      @include('notificationbroker::front.email.broker.articleStart')\n\n";

        $strFooter = "      @include('notificationbroker::front.email.broker.articleEnd')\n\n";
        $strFooter .= "@endsection";

        $this->tipoNotifiacion["email"]["header"] = $strHeader;
        $this->tipoNotifiacion["email"]["footer"] = $strFooter;
    }

    private function generateInfoNewFile()
    {
        $strReturn = "";

        try {
            // Introducimos su cabecera
            $strReturn .= $this->tipoNotifiacion[$this->plantilla->tipo]["header"];

            // Introducimos el cuerpo del mensaje
            $strReturn .= $this->formatMsg() . "\n\n";

            // Introducimos el footer
            $strReturn .= $this->tipoNotifiacion[$this->plantilla->tipo]["footer"];

            // Guardamos en donde toque el nuevo blade, según el tipo de notificación que sea...
            // Para obtener el nombre del fichero, primero hacemos una llamada al getLastFileName();
            $filename = $this->getLastFileName();

            Storage::disk('templates')
                ->put("/" . $this->tipoNotifiacion[$this->plantilla->tipo]["path"] . "/" . $filename, $strReturn);

            return $filename;
        } catch (\Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }

    private function getLastFileName()
    {
        // Si estoy en la modificación de una plantilla ya existente, devuelvo el mismo
        // nombre sin necesidad de buscar nada
        if ($this->filename != '') {
            return $this->filename;
        }
        // Buscamos en la base de datos y en los ficheros el máximo numero de plantilla
        $num_files = [];
        // Obtenemos todos los archivos según el tipo
        $files = Storage::disk('templates')->allFiles("/" . $this->tipoNotifiacion[$this->plantilla->tipo]["path"]);
        $num_file = 0;
        foreach ($files as $file) {
            preg_match_all('!\d+!', $file, $matches);
            foreach ($matches as $key => $value) {
                if (!empty($value)) {
                    $num_files[] = intval($value[0]);
                }
            }
        }
        // Hago lo mismo en base de datos ya que puede ocurrir que no esten las plantillas generadas
        $plantillas = Plantilla::select('archivo')
            ->where('archivo', 'LIKE', $this->plantilla->tipo . "%")
            ->orderBy('archivo', 'DESC')
            ->get();
        foreach ($plantillas as $plantilla) {
            preg_match_all('!\d+!', $plantilla->archivo, $matches);
            foreach ($matches as $key => $value) {
                if (!empty($value)) {
                    $num_files[] = intval($value[0]);
                }
            }
        }
        if (!empty($num_files)) {
            $num_file = max($num_files) + 1;
        }
        // devuelvo el nuevo nombre para el blade con el tipo y numero quedando por ejemplo email-2.blade.php
        $this->templateSlug = $this->plantilla->tipo . "-" . $num_file;
        return $this->plantilla->tipo . "-" . $num_file . ".blade.php";
    }

    private function formatMsg()
    {
        // Para que sean parametros deben ponerlos de la siguiente maneras {## variable ##}
        // Ojo porque puede venir {##variable##} o {## variable##} o {##variable ##}
        $texto  = $this->plantilla->mensaje;
        $codigos = array("{## ", "{##", " ##}", "##}");
        $variables   = array("{{ @\$payload['", "{{ @\$payload['", "'] }}", "'] }}");
        return str_replace($codigos, $variables, $texto);
    }

    private function saveTemplate($filename)
    {
        try {
            $notificacionType = new NotificationType();
            $notificacionType->is_fixed = true;
            $notificacionType->is_modifiable = false;
            $notificacionType->slug = $this->templateSlug;
            $notificacionType->save();

            $notficacionTypeTrans = new NotificationTypeTranslation();
            $notficacionTypeTrans->notification_type_id = $notificacionType->id;
            $notficacionTypeTrans->locale = 'es';
            $notficacionTypeTrans->title = $this->plantilla->titulo;
            $notficacionTypeTrans->subject = $this->plantilla->subject;
            $notficacionTypeTrans->body = null;
            $notficacionTypeTrans->save();

            $this->plantilla->archivo = $filename;
            $this->plantilla->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function dropFileBBDD()
    {
        try {
            // Quitamos la plantilla de la tabla de notificaciones type
            $notificacionType = NotificationType::where("slug", "=", $this->templateSlug)->first();
            if (!empty($notificacionType)) {
                $notificacionType->delete();
            }

            $this->plantilla->archivo = '';
            $this->plantilla->save();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
