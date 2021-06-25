<?php

namespace Clavel\NotificationBroker\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use SoapClient;

class LinkmobilitySMSBase extends NotificationFactory implements NotificationSmsInterface
{
    protected $config = null;
    public $user = '';
    public $password = '';
    public $host = '';
    public $sender = 'Clavel';


    public function __construct()
    {
        $this->config = Config::get('notificationbroker.brokers.linkmobility');
        $this->user = trim($this->config['user']);
        $this->password = trim($this->config['pwd']);
        $this->sender = trim($this->config['sender']);
        $this->host = trim($this->config['host']);
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

            $response = $client->envioSmsDirecto02(
                $this->user, // login
                md5($this->password), // password
                '',  // idCuenta
                '', // departamento
                $id, // referencia
                $to, // destinatario
                $this->sender, // remitente
                "ISO-8859-15", // Charset
                $message, // texto
                '1', // solicitarDR
                '' // ‘expiracion’
            );

            /*
            array:3 [▼
              "resultado" => "62000"
              "descripcion" => "Envio realizado correctamente - Ref Web: 200216S5158f40a"
              "refWeb" => "200216S5158f40a"
            ]

            62000: Envío realizado correctamente - Ref Web:XXXXX
            62001: El número de parámetros no es correcto
            62002: El parámetro 'login' es obligatorio
            62003: El parámetro 'password' es obligatorio
            62004: El parámetro 'login' contiene caracteres no permitidos
            62005: El parámetro 'password' contiene caracteres no permitidos
            62006: El usuario no es válido
            62007: El usuario no tiene permiso para utilizar este servicio web
            62008: Error en la configuración del perfil
            62009: Error en la configuración del perfil
            62101: El remitente no está permitido
            62102: El remitente no está permitido
            62104: El destino no está permitido
            62106: No tiene créditos
            62110: El parámetro 'destino' es obligatorio
            62111: El charset especificado no es valido
            62112: Error interno
            62130: La fecha de expiración no es válida
            62150: El destino esta restringido
            62___: Error interno
            */
            if ($response["resultado"] == 62000) {
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
                'cache_wsdl' => 0
            ));

            $response = $client->consultaEstadoSms(
                $this->user, // login
                md5($this->password), // password
                '', // idCuenta
                $id // ref_web
            );

            if ($response["resultado"] == "65000") {
                $code = -2;
                $status = 'error';
                $credits = 0;
                $timestamp = '';
                $message = '';
                $data = str_getcsv($response["estado"], "\n"); //parse the rows
                foreach ($data as &$row) {
                    $row = str_getcsv($row, ";");
                } //parse the items in rows

                $responseStatus = strtolower($data[0][1]);

                /*
                {"resultado":"65000","descripcion":"Datos devueltos",
                    "estado":"200216S5158f503;Entregado;1;2020-02-16 16:15:00;1"}

                // "referenciaSms;estadoDelMensaje;numeroDeMensajes;fechaFinalización;códigoDeResultado".
                // 190905S4ce2df4d;Entregado;2;2019-09-05 16:22:00;1

                if (strtolower($data[0][1]) == "entregado" || strtolower($data[0][1]) == "enviado") {
                    $response = '{"code": "0", "message": "", "credits": '.$data[0][2].' }';
                } else {
                    $response = '{"code": "-1", "message": "' . $data[0][1] . '" }';
                }
                */
                switch ($responseStatus) {
                    case "pendiente":
                    case "enviado":
                        $code = 1;
                        $status = 'pending';
                        $message .= "" . $responseStatus;
                        break;
                    case "expirado":
                    case "rechazado":
                        $code = -2;
                        $status = 'error';
                        $message .= "" . $responseStatus;
                        break;
                    case "entregado":
                        $code = 0;
                        $status = 'success';
                        $credits = $data[0][2];
                        $timestamp = $data[0][3];
                        break;
                }
                $responseData['code'] = $code;
                $responseData['message'] = $message;
                $responseData['status'] = $status;
                $responseData['data'] = [
                    'credits' => $credits,
                    'timestamp' => $timestamp,
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: ' . $response['descripcion'];
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
     */
    public function getCredits()
    {
        try {
            $client = new SoapClient($this->host, array(
                'trace' => false,
                'cache_wsdl' => 0
            ));

            $response = $client->consultarSaldoSms(
                $this->user, // login
                md5($this->password) // password
            );

            if ($response["resultado"] == "69000") {
                $responseData['code'] = 0;
                $responseData['message'] = '';
                $responseData['status'] = 'success';
                $responseData['data'] = [
                    'credits' => $response['saldo']
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: ' . $response['descripcion'];
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
        return null;
    }
}
