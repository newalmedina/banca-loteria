<?php

namespace Clavel\Sms\lib;

class Broker extends Module
{

    /**
     * Send SMS https://apidocs.labsmobile.com/#send-sms
     * @param  string $msisdn   Variable that includes a mobile number recipient.
     *          The number must include the country code without ‘+’ ó ‘00’. Each customer account has a
     *           maximum number of msisdn per sending. See the terms of your account to see this limit.
     * @param  string $message  The message to be sent. The maximum message length is 160 characters.
     *          Only characters in the GSM 3.38 7bit alphabet, found at the end of this document, are valid.
     *          Otherwise you must send ucs2 variable.
     * @param  string $subid    Message ID included in the ACKs (delivery confirmations).
     *          It is a unique delivery ID issued by the API client. It has a maximum length of 20 characters.
     * @return object
     */
    public function send($msisdn, $message, $subid = '')
    {
        $data =
            [
                'message' => $message,
                'tpoa' => $this->sms->sender, // Sender of the message. May have a numeric (maximum length, 14 digits)
                                            // or an alphanumeric (maximum capacity, 11 characters) value.
                                            // The messaging platform assigns a default sender if this variable is
                                            // not included. By including the sender's mobile number, the message
                                            // recipient can easily respond from their mobile phone with the
                                            // "Reply" button. The sender can only be defined in some countries due
                                            // to the restrictions of the operators. Otherwise the sender is a random
                                            // numeric value.
                'subid' => $subid,
                'recipient' => [
                    [
                        "msisdn" => $msisdn
                    ]
                ]
            ];

        return $this->sms->post('send', $data);
    }
}
