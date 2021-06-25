<?php namespace Clavel\NotificationBroker\Services;

// El patron Factory nos permitia crear una instancia de una notificacion concreta
use Exception;

class NotificationFactory
{
    public static function create($broker, $isCertified, $type)
    {
        if ($type == "sms") {
            if (!$isCertified) {
                switch ($broker) {
                    case 'linkmobility-didimo':
                        return new LinkmobilityDidimoSMS();
                        break;
                    case 'linkmobility':
                        return new LinkmobilitySMS();
                        break;
                    case 'labsmobile':
                        return new LabsmobileSMS();
                        break;
                    default:
                        throw new Exception("No broker found");
                }
            } else {
                switch ($broker) {
                    case 'linkmobility-didimo':
                        return new LinkmobilityDidimoSMSCertified();
                        break;
                    case 'linkmobility':
                    case 'linkmobility-certified':
                        return new LinkmobilitySMSCertified();
                        break;
                    case 'labsmobile':
                        return new LabsmobileSMSCertified();
                        break;
                    default:
                        throw new Exception("No broker found");
                }
            }
        } elseif ($type=="email") {
            if (!$isCertified) {
                switch ($broker) {
                    case 'mail':
                        return new MailNativo();
                        break;
                    default:
                        throw new Exception("No broker found");
                }
            } else {
                switch ($broker) {
                    case 'mailcertificado':
                        return new MailCertificadoCertified();
                        break;
                    default:
                        throw new Exception("No broker found");
                }
            }
        } elseif ($type=="whatsapp") {
            switch ($broker) {
                case 'twillio-whatsapp':
                    return new TwillioWhatsapp();
                    break;
                default:
                    throw new Exception("No broker found");
            }
        } else {
            return new Exception("No broker found");
        }
    }

    public function getType()
    {
        return get_class($this);
    }
}
