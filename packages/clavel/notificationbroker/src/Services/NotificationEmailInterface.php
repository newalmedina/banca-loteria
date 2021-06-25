<?php namespace Clavel\NotificationBroker\Services;

// La interfaz de Notification nos obligara a crear los métodos minimos
// De esta manera las operaciones segun el principio de Solid Open/Close estara en la clase correspondiente
use Clavel\NotificationBroker\Models\Notification;

interface NotificationEmailInterface
{
    /**
     * Enviamos el mensaje
     * @param Notification $notification
     * @return object
     */
    public function send(Notification $notification);

    /**
     * Obtenemos el status del mensaje
     * @param $id
     * @param string $to
     * @return object
     */
    public function getStatus(Notification $notification);

    /**
     * Obtenemos el certificado
     * @param Notification $notification
     * @return object
     */
    public function getCrt(Notification $notification);
}
