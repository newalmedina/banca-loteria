<?php namespace Clavel\NotificationBroker\Services;

// La interfaz de Notification nos obligara a crear los métodos minimos
// De esta manera las operaciones segun el principio de Solid Open/Close estara en la clase correspondiente
use Clavel\NotificationBroker\Models\Notification;

interface NotificationWhatsappInterface
{
    /**
     * Enviamos el mensaje
     * @param Notification $notification
     * @return object
     */
    public function send(Notification $notification);
}
