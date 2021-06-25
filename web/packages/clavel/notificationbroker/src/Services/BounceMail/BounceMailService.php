<?php

namespace Clavel\NotificationBroker\Services\BounceMail;

use Clavel\NotificationBroker\Models\BouncedEmail;
use Exception;
use Illuminate\Support\Facades\Config;
use PhpImap\Mailbox;

class BounceMailService
{
    protected $config = null;
    public $user = '';
    public $password = '';
    public $host = '';


    public function __construct()
    {
        $this->config = Config::get('notificationbroker.brokers.mail');
        $this->user = trim($this->config['username']);
        $this->password = trim($this->config['password']);
        $this->host = trim($this->config['imap_host']);
    }

    public function verify()
    {
        if (empty($this->user) || empty($this->password)  || empty($this->host)) {
            return;
        }

        set_time_limit(0);
        // Configuration for the Mailbox class
        $attachdir = false; //'attachments';

        // Construct the $mailbox handle
        try {
            $mailbox = new Mailbox($this->host, $this->user, $this->password, $attachdir);

            $bounceMailHandler = new BounceMailHandlerService($mailbox);


            // get the list of folders/mailboxes
            $folders = $mailbox->getMailboxes('*');

            $main_folder = null;
            $hard_bounce_folder = null;
            $soft_bounce = null;
            $unprocessed_folder = null;
            $working_folder = null;
            // loop through mailboxs
            foreach ($folders as $folder) {
                switch ($folder['shortpath']) {
                    case "INBOX":
                        $main_folder = $folder;
                        break;
                    case "INBOX.hard-bounce":
                        $hard_bounce_folder = $folder;
                        break;
                    case "INBOX.soft-bounce":
                        $soft_bounce_folder = $folder;
                        break;
                    case "INBOX.working-folder":
                        $working_folder = $folder;
                        break;
                    case "INBOX.unprocessed":
                        $unprocessed_folder = $folder;
                        break;
                }
            }

            if (empty($hard_bounce_folder)) {
                $mailbox->createMailbox("hard-bounce");
            }

            if (empty($soft_bounce_folder)) {
                $mailbox->createMailbox("soft-bounce");
            }

            if (empty($unprocessed_folder)) {
                $mailbox->createMailbox("unprocessed");
            }

            if (empty($working_folder)) {
                $mailbox->createMailbox("working-folder");
            }

            // switch to main mailbox
            $mailbox->switchMailbox($main_folder['fullpath']);
            //$mailbox->switchMailbox($working_folder['fullpath']);

            // Get INBOX emails
            $mailsIds = $mailbox->searchMailbox('ALL');
            if (!$mailsIds) {
                echo('Mailbox is empty');
                $mailbox->disconnect();
                return;
            }

            // Show the total number of emails loaded
            echo('n= ' . count($mailsIds). "\n<br>");

            // Put the latest email on top of listing
            //rsort($mailsIds);

            // Get the last 15 emails only
            //array_splice($mailsIds, 15);

            // Loop through emails one by one
            $nX=0;
            // Ponemos limite para no colapsar
            $max_processed = 10000;
            foreach ($mailsIds as $num) {
                // Show header with subject and data on this email

                /*
                $head = $mailbox->getMailHeader($num);

                echo("\n<br>");
                echo $head->subject.' (';
                if     (isset($head->fromName))    echo 'by '.$head->fromName.' on ';
                elseif (isset($head->fromAddress)) echo 'by '.$head->fromAddress.' on ';
                echo $head->date.')';
                echo("\n<br>");

                // Show the main body message text
                // Do not mark email as seen


               $markAsSeen = false;
               $mail = $mailbox->getMail($num, $markAsSeen);

               echo $mail->subject.' (';
               if     (isset($mail->fromName))    echo 'by '.$mail->fromName.' on ';
               elseif (isset($mail->fromAddress)) echo 'by '.$mail->fromAddress.' on ';
               echo $mail->date.')';


                if ($mail->textHtml)
                    echo $mail->textHtml;
                else
                    echo $mail->textPlain;
                echo "\n<br>";
                */

                // Procesamos el email de entrada para ver si es un rebote o no
                /*
                * string  $bounce_type   the bounce type:
                * string  $email         the target email address
                * string  $subject       the subject, ignore now
                * 1 or 0  $remove        delete status, 0 is not deleted, 1 is deleted
                * string  $rule_no       bounce mail detect rule no.
                * string  $rule_cat      bounce mail detect rule category
                */
                $processedResult = $bounceMailHandler->validateMail($num);

                if ($processedResult !== false) {
                    $displayData = $bounceMailHandler->prepData(
                        $processedResult['email'],
                        $processedResult['bounce_type'],
                        $processedResult['remove']
                    );
                    $bounceType = $displayData['bounce_type'];
                    $emailName = $displayData['emailName'];
                    $emailAddy = $displayData['emailAddy'];
                    $remove = $displayData['removestat'];
                    echo $processedResult['mailId'] . ': ' .
                        $processedResult['rule_no'] . ' | ' .
                        $processedResult['rule_cat'] . ' | ' .
                        $bounceType . ' | ' .
                        $remove . ' | ' .
                        $emailName . ' | ' .
                        $emailAddy . ' | ' .
                        $processedResult['email'] . "\n<br>";

                    switch ($bounceType) {
                        case "hard":
                            // Grabamos el rebote
                            $bounce = BouncedEmail::where('email', $processedResult['email'])->first();
                            if (empty($bounce)) {
                                $bounce = new BouncedEmail();
                                $bounce->bounce_type_id = 2;
                                $bounce->email = $processedResult['email'];
                                $bounce->description = $processedResult['subject'];
                                $bounce->bounce_code = $processedResult['rule_no']."|".$processedResult['rule_cat'];
                                $bounce->save();
                            }
                            $mailbox->moveMail($num, $hard_bounce_folder['shortpath']);

                            break;
                        case "soft":
                            // Grabamos el rebote
                            $bounce = BouncedEmail::where('email', $processedResult['email'])->first();
                            if (empty($bounce)) {
                                $bounce = new BouncedEmail();
                                $bounce->bounce_type_id =3;
                                $bounce->email = $processedResult['email'];
                                $bounce->description = $processedResult['subject'];
                                $bounce->bounce_code = $processedResult['rule_no']."|".$processedResult['rule_cat'];
                                $bounce->save();
                            }
                            $mailbox->moveMail($num, $soft_bounce_folder['shortpath']);
                            break;
                        default:
                            $mailbox->moveMail($num, $unprocessed_folder['shortpath']);
                    }
                } else {
                    die('ERROR');
                }

                // Load eventual attachment into attachments directory
                //$mail->getAttachments();

                //$mailbox->moveMail($num, $hard_bounce_folder[shortpath]);

                $nX++;
                if ($nX>=$max_processed) {
                    break;
                }
            }
            $mailbox->disconnect();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
