<?php

namespace Clavel\NotificationBroker\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use SoapClient;

class LinkmobilitySMSCertified extends LinkmobilitySMSBase
{
    public function __construct()
    {
        parent::__construct();

        $this->config = Config::get('notificationbroker.brokers.linkmobility-certified');
        $this->user = trim($this->config['user']);
        $this->password = trim($this->config['pwd']);
        $this->sender = trim($this->config['sender']);
        $this->host = trim($this->config['host']);
        $this->certified = trim($this->config['email']);
    }

    /**
     * Send SMS http://extranet.linkmobility.es/documentacion/20180329/LINKMulticanal-ServicioWeb-v28%202019-03-28.pdf
     * @param $to
     * @param $message
     * @param $id
     * @return array
     */
    public function send($to, $message, $id)
    {
        $responseData = [];

        try {
            $client = new SoapClient($this->host, array(
                'trace' => false,
                'cache_wsdl' => 0
            ));

            $response = $client->multienvio07(
                $this->user, // login
                md5($this->password), // password
                '1',  // tipoProgramacion 1=Inmediato
                '', // dia
                '', // mes
                '', // ano
                '', // hora
                '', // departamento
                $id, // referencia
                '', // nombreListas
                '', // listaLocal
                $to, // destinatarioPuntual
                '0', // enviarFax
                '0', // enviarEmail
                '1', // enviarSms
                '', // remitenteEmail
                '', // emailResponderA
                $this->sender, // remitenteSms
                '', // asunto
                $message, // textoSms
                '', // textoEmail
                '', // textoFax
                '', // adjuntos
                '1', // certificado Indica si se solicita la certificación del envío (sólo para email y SMS).
                // Puede ser “0” o “1”. El valor por defecto es 0
                $this->certified, // certificadoA si el parámetro ‘certificado’ es 1, permite indicar una o más
                // direcciones de email adicionales a las que enviar el certificado.
                '', // expiracion
                '', // idPlantilla
                '', // guardarPlantilla
                '' // nombrePlantilla
            );

            /*
            {"resultado":"50029","descripcion":"Faltan par\u00c3\u00a1metros","refWeb":""}
            {"resultado":"50000","descripcion":"Env\u00c3\u00ado realizado correctamente -
                    Ref Web: 200216W2B65FD9","refWeb":"200216W2B65FD9"}

            50000: Envío realizado correctamente - Ref Web: XXXXX
            50000: Plantilla guardada correctamente
            50001: En el fichero local de lista falta una columna con la cabecera EMAIL, FAX o SMS
            50004: La selección de listas no tiene ningún destino
            50006: No hay destinatarios
            50007: No hay ninguna lista o filtro llamado [lista]
            50008: No ha seleccionado el tipo del envío
            50009: Error, debe escribir un texto o adjuntar un fichero para realizar el envío
            50010: Error al generar los parámetros de envío
            50011: Error al generar la BBDD
            50012: Error al generar la BBDD
            50013: Error al generar la BBDD
            50014: Error al generar el texto del email
            50015: Error al generar el texto del SMS
            50016: Error al generar el texto del fax
            50025: Error procesando el archivo local de lista
            50027: La fecha indicada no es válida
            50028: La hora indicada no es válida
            50029: Faltan parámetros
            50030: Sólo puede haber un destino SMS
            50031: El usuario no es válido
            50032: Sólo puede haber destinos SMS
            50033: Error conectando a bdd
            50034: El destino ‘destino’ no está permitido
            50035: Error comprobando destino
            50036: No hay suficientes créditos
            50037: Error comprobando créditos
            50038: No hay destinos SMS válidos
            50039: El parametro 'login' es obligatorio.
            50040: El parametro 'pass' es obligatorio.
            50041: El parametro 'login' contiene caracteres no permitidos
            50042: El parametro 'pass' contiene caracteres no permitidos
            50043: El parametro 'adjuntos' no es correcto
            50044: El parametro 'adjuntos' no es correcto
            50045: Error procesando adjunto
            50130: La fecha de expiración no es válida
            50131: La fecha de finalización no es válida
            50140: El parametro guardarPlantilla tiene un valor incorrecto
            50142: La plantilla solicitada no existe
            50143: La plantilla solicitada no existe
            50145: Error guardando plantilla
            50___: Error interno

            */
            if ($response["resultado"] == 50000) {
                $responseData['code'] = 0;
                $responseData['message'] = $response["descripcion"];
                $responseData['status'] = 'success';
                $responseData['data'] = [
                    'id' => $response["refWeb"]
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: ' . $response["descripcion"];
                $responseData['status'] = 'error';
            }
        } catch (Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: ' . $e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }

    /**
     * Send SMS http://extranet.linkmobility.es/documentacion/20180329/LINKMulticanal-ServicioWeb-v28%202019-03-28.pdf
     * @param $id
     * @param string $to
     * @return mixed
     */
    public function getStatus($id, $to = "")
    {
        try {
            $client = new SoapClient($this->host, array(
                'trace' => false,
                'cache_wsdl' => 0,
                'verifypeer' => false,
                'verifyhost' => false,
                "connection_timeout" => 3600,
            ));

            $response = $client->consultaMensaje(
                $this->user, // login
                md5($this->password), // password
                '', // ref_gms
                $id, // ref_web
                $to, // ref_cliente
                '', // Departamento
                '', // fec_ini
                '' // fec_fin
            );

            if (starts_with($response["resultado"], "55000")) {
                $code = -2;
                $status = 'error';
                $credits = 0;
                $timestamp = '';

                $data = str_getcsv($response["datos"], "\n"); //parse the rows
                foreach ($data as &$row) {
                    $row = str_getcsv($row, ";");
                } //parse the items in rows

                $responseStatus = strtolower($data[1][5]);

                /*
                {"resultado":"55000: Ok","datos":
                "Fecha Entrada;Fecha Salida;Ref.Cliente;Nombre de la lista;Asunto;Estado;
                Ref.Web;Ref.GMS;Usuario;Dpto;Entregados;Destinos;Nombre Doc
                16\/02-17:41;16\/02-17:41;26a69b70-50d3-11ea-93ab-4344b1;667786621;;
                CONFIRMADO;200216W2B66595;M213266d;;;;;"}

                */
                switch ($responseStatus) {
                    case "confirmado":
                        $code = 0;
                        $status = 'success';
                        $credits = $data[1][10];
                        $timestamp = $data[1][0];
                        break;
                    default:
                        $code = -2;
                        $status = 'error';
                        break;
                }
                $responseData['code'] = $code;
                $responseData['message'] = $data[1][5];
                $responseData['status'] = $status;
                $responseData['data'] = [
                    'credits' => $credits,
                    'timestamp' => $timestamp,
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: ' . $response["resultado"];
                $responseData['status'] = 'error';
            }
        } catch (Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: ' . $e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }

    /**
     * @inheritDoc
     */
    public function getCrt($id, $to = "")
    {
        try {
            $client = new SoapClient($this->host, array(
                'trace' => false,
                'cache_wsdl' => 0,
                'verifypeer' => false,
                'verifyhost' => false,
                "connection_timeout" => 3600,
            ));

            $response = $client->descargarInformeCertificado(
                $this->user, // login
                md5($this->password), // password
                $id // ref_web
            );

            if (starts_with($response["resultado"], "62000")) {
                $code = -2;
                $status = 'error';
                $crt = null;

                // Miramos si viene con el documento de certificado adjunto
                if (isset($response["certificado"])) {
                    try {
                        $code = 0;
                        $status = 'success';
                        $crt = $response["certificado"];
                    } catch (Exception $ex) {
                        $code = -2;
                        $status = 'error';
                        $crt = null;
                    }
                }

                $responseData['code'] = $code;
                $responseData['message'] = $response["resumen"];
                $responseData['status'] = $status;
                $responseData['data'] = [
                    'crt' => $crt
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: ' . $response->message;
                $responseData['status'] = 'error';
            }
        } catch (Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: ' . $e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }
}
