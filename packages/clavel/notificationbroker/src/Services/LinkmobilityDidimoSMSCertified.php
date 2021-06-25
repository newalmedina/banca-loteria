<?php namespace Clavel\NotificationBroker\Services;

use Exception;

class LinkmobilityDidimoSMSCertified extends LinkmobilityDidimoSMSBase
{
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
            $response = $this->sms->post('CreateCertifiedMessage', $data, true);

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
                ;
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
    public function getCrt($id, $to = "")
    {
        try {
            $data =
                [
                    "UserName" => $this->user,
                    "Password" =>  $this->password,
                    "Id" => $id
                ];
            $response = $this->sms->post('GetCertifyFile', $data, true, true);

            $responseData['code'] = 0;
            $responseData['message'] = 'Operation Success';
            $responseData['status'] = 'success';
            $responseData['data'] = [
                'crt' => $response
            ];
        } catch (Exception $e) {
            $responseData['code'] = -1;
            $responseData['message'] = 'Error: '.$e->getMessage();
            $responseData['status'] = 'error';
        }

        return $responseData;
    }
}
