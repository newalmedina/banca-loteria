<?php
// Utilizando reglas de https://github.com/voku/PHPMailer-BMH
// Mas reglas https://github.com/twisted1919/bounce-handler/blob/master/BounceHandler/rules.php

namespace Clavel\NotificationBroker\Services\BounceMail;

use PhpImap\Mailbox;

class BounceMailHandlerService
{
    protected $mailbox = null;
    protected $mailboxLink = false;

    /**
     * Control the method to process the mail header
     * if set true, uses the imap_fetchstructure function
     * otherwise, detect message type directly from headers,
     * a bit faster than imap_fetchstructure function and take less resources.
     *
     * however - the difference is negligible
     *
     * @var boolean
     */
    public $useFetchstructure = true;

    public function __construct(Mailbox $mailbox)
    {
        $this->mailbox = $mailbox;
        $this->mailboxLink = $mailbox->getImapStream();
    }

    public function validateMail($mailId)
    {
        // fetch the messages one at a time
        if ($this->useFetchstructure) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            $structure = $this->mailbox->imap('fetchstructure', [$mailId, FT_UID]);

            if ($structure
                &&
                \is_object($structure)
                &&
                $structure->type == 1
                &&
                $structure->ifsubtype
                &&
                $structure->ifparameters
                &&
                \strtoupper($structure->subtype) == 'REPORT'
                &&
                $this->isParameter($structure->parameters, 'REPORT-TYPE', 'delivery-status')
            ) {
                $processedResult = $this->processBounce($mailId, 'DSN');
            } else {
                $processedResult = $this->processBounce($mailId, 'BODY');
            }
        } else {
            $header = $this->mailbox->imap('fetchheader', [$mailId, FT_UID]);

            // Could be multi-line, if the new line begins with SPACE or HTAB
            if (\preg_match("/Content-Type:((?:[^\n]|\n[\t ])+)(?:\n[^\t ]|$)/i", $header, $match)) {
                if (\preg_match("/multipart\/report/i", $match[1])
                    &&
                    \preg_match("/report-type=[\"']?delivery-status[\"']?/i", $match[1])
                ) {
                    // standard DSN msg
                    $processedResult = $this->processBounce($mailId, 'DSN');
                } else {
                    $processedResult = $this->processBounce($mailId, 'BODY');
                }
            } else {
                $processedResult = $this->processBounce($mailId, 'BODY');
            }
        }

        return $processedResult;
    }

    /**
     * Function to process each individual message.
     *
     * @param int $mailId message Id number
     * @param string $type DNS or BODY type
     *
     * @return false|array <p>"$result"-array or false</p>
     * @throws \PhpImap\Exception
     */
    public function processBounce(int $mailId, string $type)
    {
        // https://www.glenpritchard.com/php-imap-using-uid-to-get-header-info/
        //$header = $this->mailbox->imap('headerinfo', [$mailId, FT_UID]);
        $headersRaw = $this->mailbox->imap('fetchheader', [$mailId, FT_UID]);
        $header = imap_rfc822_parse_headers($headersRaw);
        $subject = isset($header->subject) ? \strip_tags($header->subject) : '[NO SUBJECT]';
        $body = '';
        $headerFull = $this->mailbox->imap('fetchheader', [$mailId, FT_UID]);
        $bodyFull = $this->mailbox->imap('body', [$mailId, FT_UID]);

        if ($type == 'DSN') {
            // first part of DSN (Delivery Status Notification), human-readable explanation
            $dsnMsg = $this->mailbox->imap('fetchbody', [$mailId, '1', FT_UID]);

            $dsnMsgStructure = $this->mailbox->imap('bodystruct', [imap_msgno($this->mailboxLink, $mailId), '1']);

            if ($dsnMsgStructure->encoding == 4) {
                $dsnMsg = \quoted_printable_decode($dsnMsg);
            } elseif ($dsnMsgStructure->encoding == 3) {
                $dsnMsg = \base64_decode($dsnMsg);
            }

            // second part of DSN (Delivery Status Notification), delivery-status
            $dsnReport = $this->mailbox->imap('fetchbody', [$mailId, '2', FT_UID]);

            // process bounces by rules
            $result = BounceMailHandlerRules::bmhBodyRules($dsnMsg, $dsnReport);
        } elseif ($type == 'BODY') {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            $structure = $this->mailbox->imap('fetchstructure', [$mailId, FT_UID]);

            if (!\is_object($structure)) {
                return false;
            }
            switch ($structure->type) {
                case 0: // Content-type = text
                    $body = $this->mailbox->imap('fetchbody', [$mailId, '1', FT_UID]);
                    $result = BounceMailHandlerRules::bmhBodyRules($body, $structure);
                    break;

                case 1: // Content-type = multipart
                    $body = $this->mailbox->imap('fetchbody', [$mailId, '1', FT_UID]);

                    // Detect encoding and decode - only base64
                    if ($structure->parts[0]->encoding == 4) {
                        $body = \quoted_printable_decode($body);
                    } elseif ($structure->parts[0]->encoding == 3) {
                        $body = \base64_decode($body);
                    }

                    $result = BounceMailHandlerRules::bmhBodyRules($body, $structure);
                    break;

                case 2: // Content-type = message
                    $body = $this->mailbox->imap('body', [$mailId, FT_UID]);

                    if ($structure->encoding == 4) {
                        $body = \quoted_printable_decode($body);
                    } elseif ($structure->encoding == 3) {
                        $body = \base64_decode($body);
                    }

                    $body = \substr($body, 0, 1000);
                    $result = BounceMailHandlerRules::bmhBodyRules($body, $structure);
                    break;

                default: // un-support Content-type
                    return false;
            }
        } else {
            // internal error
            return false;
        }

        $email = $result['email'];

        $ruleNumber = $result['rule_no'];

        if ($ruleNumber === '0000') {
            // unrecognized
            if (\trim($email) == ''
                &&
                \property_exists($header, 'fromaddress') === true
            ) {
                $email = $header->fromaddress;
            }
        }

        // workaround: I think there is a error in one of the reg-ex in "phpmailer-bmh_rules.php".
        if ($email && strpos($email, 'TO:<') !== false) {
            $email = str_replace('TO:<', '', $email);
        }
        // Otras limpiezas
        $email = str_replace('&gt', '', $email);
        $email = str_replace('&lt;', '', $email);
        $email = str_replace('</strong></a', '', $email);
        $email = str_replace('b>', '', $email);


        $result['email'] = $email;

        $result['mailId'] = $mailId;
        $result['subject'] = $subject;
        $result['body'] = $body;
        $result['headerFull'] = $headerFull;
        $result['bodyFull'] = $bodyFull;

        return $result;
    }

    /**
     * Function to determine if a particular value is found in a imap_fetchstructure key.
     *
     * @param array  $currParameters imap_fetstructure parameters
     * @param string $varKey         imap_fetstructure key
     * @param string $varValue       value to check for
     *
     * @return bool
     */
    public function isParameter(array $currParameters, string $varKey, string $varValue): bool
    {
        foreach ($currParameters as $object) {
            if (\strtoupper($object->attribute) == \strtoupper($varKey)
                &&
                \strtoupper($object->value) == \strtoupper($varValue)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Function to clean the data from the Callback Function for optimized display
     *
     * @param $email
     * @param $bounceType
     * @param $remove
     *
     * @return mixed
     */
    public function prepData($email, $bounceType, $remove)
    {
        $data['bounce_type'] = trim($bounceType);
        $data['email'] = '';
        $data['emailName'] = '';
        $data['emailAddy'] = '';
        $data['remove'] = '';
        if (strpos($email, '<') !== false) {
            $pos_start = strpos($email, '<');
            $data['emailName'] = trim(substr($email, 0, $pos_start));
            $data['emailAddy'] = substr($email, $pos_start + 1);
            $pos_end = strpos($data['emailAddy'], '>');
            if ($pos_end) {
                $data['emailAddy'] = substr($data['emailAddy'], 0, $pos_end);
            }
        }
        // replace the < and > able so they display on screen
        $email = str_replace(array('<', '>'), array('&lt;', '&gt;'), $email);
        // replace the "TO:<" with nothing
        $email = str_ireplace('TO:<', '', $email);
        $data['email'] = $email;
        // account for legitimate emails that have no bounce type
        if (trim($bounceType) == '') {
            $data['bounce_type'] = 'none';
        }
        // change the remove flag from true or 1 to textual representation
        if (stripos($remove, 'moved') !== false && stripos($remove, 'hard') !== false) {
            $data['removestat'] = 'moved (hard)';
            $data['remove'] = '<span style="color:red;">' . 'moved (hard)' . '</span>';
        } elseif (stripos($remove, 'moved') !== false && stripos($remove, 'soft') !== false) {
            $data['removestat'] = 'moved (soft)';
            $data['remove'] = '<span style="color:gray;">' . 'moved (soft)' . '</span>';
        } elseif ($remove == true || $remove == '1') {
            $data['removestat'] = 'deleted';
            $data['remove'] = '<span style="color:red;">' . 'deleted' . '</span>';
        } else {
            $data['removestat'] = 'not deleted';
            $data['remove'] = '<span style="color:gray;">' . 'not deleted' . '</span>';
        }
        return $data;
    }
}
