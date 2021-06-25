<?php namespace Clavel\NotificationBroker\Services;

use Clavel\NotificationBroker\Services\RestHelper;
use Exception;
use Illuminate\Support\Facades\Config;

class LinkmobilityDidimoSMSBase extends NotificationFactory implements NotificationSmsInterface
{
    protected $config = null;
    public $user = '';
    public $password = '';
    public $sender = 'Clavel';

    protected $sms = null;
    public function __construct()
    {
        $this->config = Config::get('notificationbroker.brokers.linkmobility-didimo');
        $this->user = trim($this->config['user']);
        $this->password = trim($this->config['pwd']);
        $this->sender = trim($this->config['sender']);

        $this->sms = new RestHelper($this->config, trim($this->config['host']), 'rest');
    }

    /**
     * @inheritDoc
     */
    public function send($to, $message, $id)
    {
        $responseData = [];

        try {
            $data =
                [
                    "UserName" => $this->user,
                    "Password" =>  $this->password ,
                    "Name" => $id,
                    //"ScheduleDate" => "Contenido de la cadena",
                    "Sender" => $this->sender,
                    //"EncryptText" => "Contenido de la cadena",
                    "Id" => $id,
                    //"IsUnicode" => "Contenido de la cadena",
                    "Mobile" => $to,
                    //"RemoveEncryptedText" => "Contenido de la cadena",
                    "Text" => $message
                ];
            $response = $this->sms->post('CreateMessage', $data, true);

            /*
            +"ResponseCode": 0
            +"ResponseMessage": "Operation Success"
            +"Id": "884cbcd0-4cd9-11ea-a599-5b5158df2f4b"
            */
            if ($response->ResponseCode == 0) {
                $responseData['code'] = 0;
                $responseData['message'] = $response->ResponseMessage;
                $responseData['status'] = 'success';
                $responseData['data'] = [
                    'id' => $response->Id
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: '.$response->ResponseMessage;
                $responseData['status'] = 'error';
            }
        } catch (Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: '.$e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }

    /**
     * @inheritDoc
     */
    public function getStatus($id, $to = "")
    {
        try {
            $data =
                [
                    "UserName" => $this->user,
                    "Password" =>  $this->password,
                    "Id" => $id
                ];
            $response =  $this->sms->post('GetMessageStatus', $data, true);
            if ($response->ResponseCode == 0) {
                $code = -2;
                $status = 'error';
                $credits = 0;
                $timestamp = '';
                switch ($response->StatusCode) {
                    case "PT0001":
                    case "PT0009":
                    case "PT1001":
                    case "PT1004":
                    case "OP0001":
                    case "OP0002":
                    case "OP0003":
                    case "OP0004":
                        $code = 1;
                        $status = 'pending';
                        break;
                    case "PT0002":
                    case "PT0003":
                    case "PT0004":
                    case "PT0005":
                    case "PT0006":
                    case "PT0007":
                    case "PT0008":
                    case "PT0010":
                    case "PT0011":
                    case "PT1002":
                    case "PT1003":
                    case "PT2001":
                    case "OP0005":
                    case "OP0006":
                    case "OP0007":
                    case "OP0008":
                    case "OP0009":
                    case "OP0010":
                    case "OP0011":
                    case "OP0012":
                    case "OP0013":
                    case "OP0014":
                    case "OP0015":
                    case "OP0016":
                    case "OP0017":
                    case "OP0018":
                    case "OP0019":
                    case "OP0020":
                    case "OP0021":
                    case "OP0022":
                    case "OP0023":
                    case "OP0024":
                    case "OP0025":
                        $code = -2;
                        $status = 'error';
                        break;
                    case "PT2003":
                    case "OP0026":
                        $code = 0;
                        $status = 'success';
                        break;
                }
                $responseData['code'] = $code;
                $responseData['message'] = $response->ResponseMessage. " [".$response->StatusDescription."]";
                $responseData['status'] = $status;
                $responseData['data'] = [
                    'credits' => $credits,
                    'timestamp' => $timestamp,
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: '.$response->ResponseMessage;
                $responseData['status'] = 'error';
            }
        } catch (Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: '.$e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }

    /**
     * @inheritDoc
     */
    public function getCredits()
    {
        try {
            $data =
                [
                    "UserName" => $this->user,
                    "Password" =>  $this->password,
                ];
            $response = $this->sms->post('GetCredits', $data, true);

            if ($response->ResponseCode == 0) {
                $responseData['code'] = 0;
                $responseData['message'] = $response->ResponseMessage;
                $responseData['status'] = 'success';
                $responseData['data'] = [
                    'credits' => $response->Credits
                ];
            } else {
                $responseData['code'] = -2;
                $responseData['message'] =  'Error: '.$response->ResponseMessage;
                $responseData['status'] = 'error';
            }
        } catch (Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Operation error: '.$e->getMessage();
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
