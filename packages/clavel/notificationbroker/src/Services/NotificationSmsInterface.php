<?php namespace Clavel\NotificationBroker\Services;

// La interfaz de Notification nos obligara a crear los métodos minimos
// De esta manera las operaciones segun el principio de Solid Open/Close estara en la clase correspondiente
interface NotificationSmsInterface
{
    /**
     * Enviamos el mensaje
     * @param $to
     * @param $message
     * @param $id
     * @return object
     */
    public function send($to, $message, $id);

    /**
     * Obtenemos el status del mensaje
     * @param $id
     * @param string $to
     * @return object
     */
    public function getStatus($id, $to = "");

    /**
     * Obtenemos los créditos
     * @return object
     */
    public function getCredits();

    /**
     * Obtenemos el certificado
     * @param $id
     * @return object
     */
    public function getCrt($id, $to = "");
}
