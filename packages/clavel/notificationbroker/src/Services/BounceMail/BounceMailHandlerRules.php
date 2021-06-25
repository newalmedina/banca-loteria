<?php

/**
 * Created by PhpStorm.
 * User: Jose Juan
 * Date: 31/03/2019
 * Time: 11:37
 */

namespace Clavel\NotificationBroker\Services\BounceMail;

class BounceMailHandlerRules
{
    public static $rule_categories = array(
        'antispam' => array('remove' => 0, 'bounce_type' => 'blocked'),
        'autoreply' => array('remove' => 0, 'bounce_type' => 'autoreply'),
        'concurrent' => array('remove' => 0, 'bounce_type' => 'soft'),
        'content_reject' => array('remove' => 0, 'bounce_type' => 'soft'),
        'command_reject' => array('remove' => 1, 'bounce_type' => 'hard'),
        'internal_error' => array('remove' => 0, 'bounce_type' => 'temporary'),
        'defer' => array('remove' => 0, 'bounce_type' => 'soft'),
        'delayed' => array('remove' => 0, 'bounce_type' => 'temporary'),
        'dns_loop' => array('remove' => 1, 'bounce_type' => 'hard'),
        'dns_unknown' => array('remove' => 1, 'bounce_type' => 'hard'),
        'full' => array('remove' => 0, 'bounce_type' => 'soft'),
        'inactive' => array('remove' => 1, 'bounce_type' => 'hard'),
        'latin_only' => array('remove' => 0, 'bounce_type' => 'soft'),
        'other' => array('remove' => 1, 'bounce_type' => 'generic'),
        'oversize' => array('remove' => 0, 'bounce_type' => 'soft'),
        'outofoffice' => array('remove' => 0, 'bounce_type' => 'soft'),
        'unknown' => array('remove' => 1, 'bounce_type' => 'hard'),
        'unrecognized' => array('remove' => 0, 'bounce_type' => false,),
        'user_reject' => array('remove' => 1, 'bounce_type' => 'hard'),
        'warning' => array('remove' => 0, 'bounce_type' => 'soft'),
    );

    /*
     * var for new line ending
     */
    private static $bmh_newline = "<br />\n";

    /**
     * Defined bounce parsing rules for non-standard DSN
     *
     * @param string $body body of the email
     * @param string $structure message structure
     * @param boolean $debug_mode show debug info. or not
     *
     * @return array    $result an array include the following fields: 'email', 'bounce_type','remove',
     *                  'rule_no','rule_cat'
     *                      if we could NOT detect the type of bounce, return rule_no = '0000'
     */
    public static function bmhBodyRules(
        $body,
        /** @noinspection PhpUnusedParameterInspection */
        $structure,
        $debug_mode = false
    ) {

        // initialize the result array
        $result = array(
            'email' => '',
            'bounce_type' => false,
            'remove' => 0,
            'rule_cat' => 'unrecognized',
            'rule_no' => '0000',
            'status_code' => '',
            'action' => '',
            'diagnostic_code' => '',
        );

        // ======== rules =========

        /* rule: dns_unknown
         * sample:
         *   Technical details of permanent failure:
         *   DNS Error: Domain name not found
         */
        if (preg_match("/domain\s+name\s+not\s+found/i", $body, $match)) {
            $result['rule_cat'] = 'dns_unknown';
            $result['rule_no'] = '0999';
        } elseif (preg_match("/no\s+such\s+address\s+here/i", $body, $match)) {
            /* rule: unknown
   * sample:
   *   xxxxx@yourdomain.com
   *   no such address here
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0237';
        } elseif (strpos(
            $body,
            'Technical details of permanent failure'
        ) === false // if there are technical details, try another test-case
            &&
            preg_match(
                "/Delivery to the following (?:recipient|recipients) failed permanently\X*?(\S+@\S+\w)/ui",
                $body,
                $match
            )
        ) {
            /* Gmail Bounce Error
   * rule: unknown
   * sample:
   *   Delivery to the following recipient failed permanently:
   *   xxxxx@yourdomain.com
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0998';
            $result['email'] = $match[1];
        } elseif (preg_match("/user.+?not\s+exist/i", $body, $match)) {
            /*
   * rule: unknown
   * sample:
   * <xxxxx@yourdomain.com>: host mail-host[111.111.111.111]
    said: 550 5.1.1 This user does not exist
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '02361';
        } elseif (preg_match("/user\s+unknown/i", $body, $match)) {
            /* rule: unknown
   * sample:
   *   <xxxxx@yourdomain.com>:
   *   111.111.111.111 does not like recipient.
   *   Remote host said: 550 User unknown
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0236';
        } elseif (preg_match("/unknown\s+user/i", $body, $match)) {
            /* rule: unknown
   * sample:
   *
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0249';
        } elseif (preg_match("/no\s+mailbox/i", $body, $match)) {
            /* rule: unknown
   * sample:
   *   <xxxxx@yourdomain.com>:
   *   Sorry, no mailbox here by that name. vpopmail (#5.1.1)
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0157';
        } elseif (preg_match("/can't\s+find.*mailbox/i", $body, $match)) {
            /* rule: unknown
   * sample:
   *   xxxxx@yourdomain.com<br>
   *   local: Sorry, can't find user's mailbox. (#5.1.1)<br>
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0164';
        } elseif (preg_match("/Can't\s+create\s+output.*<(\S+@\S+\w)>/is", $body, $match)) {
            /* rule: unknown
   * sample:
   *   ##########################################################
   *   #  This is an automated response from a mail delivery    #
   *   #  program.  Your message could not be delivered to      #
   *   #  the following address:                                #
   *   #                                                        #
   *   #      "|/usr/local/bin/mailfilt -u #dkms"               #
   *   #        (reason: Can't create output)                   #
   *   #        (expanded from: <xxxxx@yourdomain.com>)         #
   *   #                                                        #
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0169';
            $result['email'] = $match[1];
        } elseif (preg_match('/=D5=CA=BA=C5=B2=BB=B4=E6=D4=DA/i', $body, $match)) {
            /* rule: unknown
   * sample:
   *   ????????????????:
   *   xxxxx@yourdomain.com : ????, ?????.
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0174';
        } elseif (preg_match("/Unrouteable\s+address/i", $body, $match)) {
            /* rule: unknown
   * sample:
   *   xxxxx@yourdomain.com
   *   Unrouteable address
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0179';
        } elseif (preg_match("/delivery[^\n\r]+failed\S*\s+(\S+@\S+\w)\s/i", $body, $match)) {
            /* rule: unknown
   * sample:
   *   Delivery to the following recipients failed.
   *   xxxxx@yourdomain.com
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0013';
            $result['email'] = $match[1];
        } elseif (preg_match("/unknown\s+local-part/i", $body, $match)) {
            /* rule: unknown
   * sample:
   *   A message that you sent could not be delivered to one or more of its
   *   recipients. This is a permanent error. The following address(es) failed:
   *
   *   xxxxx@yourdomain.com
   *   unknown local-part "xxxxx" in domain "yourdomain.com"
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0232';
        } elseif (preg_match(
            "/Invalid.*(?:alias|account|recipient|address|email|mailbox|user).*<(\S+@\S+\w)>/is",
            $body,
            $match
        )) {
            /* rule: unknown
   * sample:
   *   <xxxxx@yourdomain.com>:
   *   111.111.111.11 does not like recipient.
   *   Remote host said: 550 Invalid recipient: <xxxxx@yourdomain.com>
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0233';
            $result['email'] = $match[1];
        } elseif (preg_match(
            "/No\s+such.*(?:alias|account|recipient|address|email|mailbox|user).*<(\S+@\S+\w)>/is",
            $body,
            $match
        )) {
            /* rule: unknown
   * sample:
   *   Sent >>> RCPT TO: <xxxxx@yourdomain.com>
   *   Received <<< 550 xxxxx@yourdomain.com... No such user
   *
   *   Could not deliver mail to this user.
   *   xxxxx@yourdomain.com
   *   *****************     End of message     ***************
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0234';
            $result['email'] = $match[1];
        } elseif (preg_match('/not unique.\s+Several matches found/i', $body, $match)) {
            /* rule: unknown
   * sample:
   *   Diagnostic-Code: X-Notes; Recipient user name info (a@b.c) not unique.
   * Several matches found in Domino Directory.
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0254';
        } elseif (preg_match('/over.*quota/i', $body, $match)) {
            /* rule: full
   * sample 1:
   *   <xxxxx@yourdomain.com>:
   *   This account is over quota and unable to receive mail.
   *   sample 2:
   *   <xxxxx@yourdomain.com>:
   *   Warning: undefined mail delivery mode: normal (ignored).
   *   The users mailfolder is over the allowed quota (size). (#5.2.2)
   */
            $result['rule_cat'] = 'full';
            $result['rule_no'] = '0182';
        } elseif (preg_match("/quota\s+exceeded.*<(\S+@\S+\w)>/is", $body, $match)) {
            /* rule: full
   * sample:
   *   ----- Transcript of session follows -----
   *   mail.local: /var/mail/2b/10/kellen.lee: Disc quota exceeded
   *   554 <xxxxx@yourdomain.com>... Service unavailable
   */
            $result['rule_cat'] = 'full';
            $result['rule_no'] = '0126';
            $result['email'] = $match[1];
        } elseif (preg_match("/quota\s+exceeded/i", $body, $match)) {
            /* rule: full
   * sample:
   *   Hi. This is the qmail-send program at 263.domain.com.
   *   <xxxxx@yourdomain.com>:
   *   - User disk quota exceeded. (#4.3.0)
   */
            $result['rule_cat'] = 'full';
            $result['rule_no'] = '0158';
        } elseif (preg_match('/mailbox.*full/i', $body, $match)) {
            /* rule: full
   * sample:
   *   xxxxx@yourdomain.com
   *   mailbox is full (MTA-imposed quota exceeded while writing to file
   * /mbx201/mbx011/A100/09/35/A1000935772/mail/.inbox):
   */
            $result['rule_cat'] = 'full';
            $result['rule_no'] = '0166';
        } elseif (preg_match("/The message to (\S+@\S+\w)\s.*bounce.*Quota exceed/i", $body, $match)) {
            /* rule: full
   * sample:
   *   The message to xxxxx@yourdomain.com is bounced because : Quota exceed the hard limit
   */
            $result['rule_cat'] = 'full';
            $result['rule_no'] = '0168';
            $result['email'] = $match[1];
        } elseif (preg_match("/not\s+enough\s+storage\s+space/i", $body, $match)) {
            /* rule: full
   * sample:
   *   Message rejected. Not enough storage space in user's mailbox to accept message.
   */
            $result['rule_cat'] = 'full';
            $result['rule_no'] = '0253';
        } elseif (preg_match('/user is inactive/i', $body, $match)) {
            /* rule: inactive
   * sample:
   *   xxxxx@yourdomain.com<br>
   *   553 user is inactive (eyou mta)
   */
            $result['rule_cat'] = 'inactive';
            $result['rule_no'] = '0171';
        } elseif (preg_match("/(\S+@\S+\w).*n? is restricted/i", $body, $match)) {
            /*
   * <xxxxx@xxx.xxx> is restricted
   */
            $result['rule_cat'] = 'inactive';
            $result['rule_no'] = '0201';
            $result['email'] = $match[1];
        } elseif (preg_match('/inactive account/i', $body, $match)) {
            /* rule: inactive
   * sample:
   *   xxxxx@yourdomain.com [Inactive account]
   */
            $result['rule_cat'] = 'inactive';
            $result['rule_no'] = '0181';
        } elseif (preg_match("/<(\S+@\S+\w)>.*\n.*mailbox unavailable/i", $body, $match)) {
            /*
   *<xxxxxx@xxxx.xxx>: host mx3.HOTMAIL.COM said: 550
   * Requested action not taken: mailbox unavailable (in reply to RCPT TO command)
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '124';
            $result['email'] = $match[1];
        } elseif (preg_match(
            "/<(\S+@\S+\w)>.*\n?.*\n?.*account that you tried to reach does not exist/i",
            $body,
            $match
        )) {
            /*
   * rule: mailbox unknown;
   * sample:
   * xxxxx@yourdomain.com
   * 550-5.1.1 The email
   * account that you tried to reach does not exist. Please try 550-5.1.1
   * double-checking the recipient's email address for typos or 550-5.1.1
   * unnecessary spaces. Learn more at 550 5.1.1
   * http://support.google.com/mail/bin/answer.py?answer=6596 n7si4762785wiy.46
   * (in reply to RCPT TO command)
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '7770';
            $result['email'] = $match[1];
        } elseif (preg_match(
            '/Technical details of permanent failure:\s+TEMP_FAILURE:' .
                ' Could not initiate SMTP conversation with any hosts/i',
            $body,
            $match
        )) {
            /* rule: dns_unknown
   * sample1:
   *   Delivery to the following recipient failed permanently:
   *
   *     a@b.c
   *
   *   Technical details of permanent failure:
   *   TEMP_FAILURE: Could not initiate SMTP conversation with any hosts:
   *   [b.c (1): Connection timed out]
   * sample2:
   *   Delivery to the following recipient failed permanently:
   *
   *     a@b.c
   *
   *   Technical details of permanent failure:
   *   TEMP_FAILURE: Could not initiate SMTP conversation with any hosts:
   *   [pop.b.c (1): Connection dropped]
   */
            $result['rule_cat'] = 'dns_unknown';
            $result['rule_no'] = '0251';
        } elseif (preg_match(
            '/Technical details of temporary failure:\s+TEMP_FAILURE: Could not' .
                ' initiate SMTP conversation with any hosts/i',
            $body,
            $match
        )) {
            /* rule: delayed
   * sample:
   *   Delivery to the following recipient has been delayed:
   *
   *     a@b.c
   *
   *   Message will be retried for 2 more day(s)
   *
   *   Technical details of temporary failure:
   *   TEMP_FAILURE: Could not initiate SMTP conversation with any hosts:
   *   [b.c (50): Connection timed out]
   */
            $result['rule_cat'] = 'delayed';
            $result['rule_no'] = '0252';
        } elseif (preg_match(
            '/Technical details of temporary failure:\s+TEMP_FAILURE: The recipient' .
                ' server did not accept our requests to connect./i',
            $body,
            $match
        )) {
            /* rule: delayed
   * sample:
   *   Delivery to the following recipient has been delayed:
   *
   *     a@b.c
   *
   *   Message will be retried for 2 more day(s)
   *
   *   Technical details of temporary failure:
   *   TEMP_FAILURE: The recipient server did not accept our requests to connect. Learn more at ...
   *   [b.c (10): Connection dropped]
   */
            $result['rule_cat'] = 'delayed';
            $result['rule_no'] = '0256';
        } elseif (preg_match("/input\/output error/i", $body, $match)) {
            /* rule: internal_error
   * sample:
   *   <xxxxx@yourdomain.com>:
   *   Unable to switch to /var/vpopmail/domains/domain.com: input/output error. (#4.3.0)
   */
            $result['rule_cat'] = 'internal_error';
            $result['rule_no'] = '0172';
            $result['bounce_type'] = 'hard';
            $result['remove'] = 1;
        } elseif (preg_match('/can not open new email file/i', $body, $match)) {
            /* rule: internal_error
   * sample:
   *   <xxxxx@yourdomain.com>:
   *   can not open new email file errno=13
   * file=/home/vpopmail/domains/fromc.com/0/domain/Maildir/tmp/1155254417.28358.mx05,S=212350
   */
            $result['rule_cat'] = 'internal_error';
            $result['rule_no'] = '0173';
            $result['bounce_type'] = 'hard';
            $result['remove'] = 1;
        } elseif (preg_match('/Resources temporarily unavailable/i', $body, $match)) {
            /* rule: defer
   * sample:
   *   <xxxxx@yourdomain.com>:
   *   111.111.111.111 failed after I sent the message.
   *   Remote host said: 451 mta283.mail.scd.yahoo.com Resources temporarily
   * unavailable. Please try again later [#4.16.5].
   */
            $result['rule_cat'] = 'defer';
            $result['rule_no'] = '0163';
        } elseif (preg_match("/^AutoReply message from (\S+@\S+\w)/i", $body, $match)) {
            /* rule: autoreply
   * sample:
   *   AutoReply message from xxxxx@yourdomain.com
   */
            $result['rule_cat'] = 'autoreply';
            $result['rule_no'] = '0167';
            $result['email'] = $match[1];
        } elseif (preg_match("/Your message \([^)]+\) was blocked by/i", $body, $match)) {
            /* rule: block
   * sample:
   *   Delivery to the following recipient failed permanently:
   *     a@b.c
   *   Technical details of permanent failure:
   *   PERM_FAILURE: SMTP Error (state 9): 550 5.7.1 Your message (sent through 209.85.132.244)
   * was blocked by ROTA DNSBL. If you are not a spammer, open http://www.rota.lv/DNSBL and follow
   * instructions or call +371 7019029, or send an e-mail message from another address to dz@ROTA.lv
   *  with the blocked sender e-mail name.
   */
            $result['rule_cat'] = 'antispam';
            $result['rule_no'] = '0250';
        } elseif (preg_match("/Messages\s+without\s+\S+\s+fields\s+are\s+not\s+accepted\s+here/i", $body, $match)) {
            /* rule: content_reject
   * sample:
   *   Failed to deliver to '<a@b.c>'
   *   Messages without To: fields are not accepted here
   */
            $result['rule_cat'] = 'content_reject';
            $result['rule_no'] = '0248';
        } elseif (preg_match(
            "/(?:alias|account|recipient|address|email|mailbox|user).*no\s+longer\s+accepts\s+mail/i",
            $body,
            $match
        )) {
            /* rule: inactive
           * sample:
           *   <xxxxx@yourdomain.com>:
           *   This address no longer accepts mail.
           */
            $result['rule_cat'] = 'inactive';
            $result['rule_no'] = '0235';
        } elseif (preg_match("/does not accept[^\r\n]*non-Western/i", $body, $match)) {
            /* rule: western chars only
   * sample:
   *   <xxxxx@yourdomain.com>:
   *   The user does not accept email in non-Western (non-Latin) character sets.
   */
            $result['rule_cat'] = 'latin_only';
            $result['rule_no'] = '0043';
        } elseif (preg_match("/554.*delivery error.*this user.*doesn't have.*account/is", $body, $match)) {
            /* rule: unknown
   * sample:
   *   554 delivery error
   *   This user doesn't have a yahoo.com account
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0044';
        } elseif (preg_match('/550.*Requested.*action.*not.*taken:.*mailbox.*unavailable/is', $body, $match)) {
            /* rule: unknown
   * sample:
   *   550 hotmail.com
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0045';
        } elseif (preg_match("/550 5\.1\.1.*Recipient address rejected/is", $body, $match)) {
            /* rule: unknown
   * sample:
   *   550 5.1.1 aim.com
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0046';
        } elseif (preg_match('/550.*in reply to end of DATA command/i', $body, $match)) {
            /* rule: unknown
   * sample:
   *   550 .* (in reply to end of DATA command)
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0047';
        } elseif (preg_match('/550.*in reply to RCPT TO command/i', $body, $match)) {
            /* rule: unknown
   * sample:
   *   550 .* (in reply to RCPT TO command)
   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0048';
        } elseif (preg_match("/unrouteable\s+mail\s+domain/i", $body, $match)) {
            /* rule: dns_unknown
   * sample:
   *    a@b.c:
   *      unrouteable mail domain "b.c"
   */
            $result['rule_cat'] = 'dns_unknown';
            $result['rule_no'] = '0247';
        } elseif (preg_match(
            "/account that you tried to reach does not exist/i",
            $body,
            $match
        )) {
            /*
                   * rule: mailbox unknown;
                   * sample:
                   * The email account that you tried to reach does not exist. Please try double-checking the
                   * recipient's email address for typos or unnecessary spaces. Learn more at
                   * https://support.google.com/mail/?p=NoSuchUser x3sor8212147wmk.9 - gsmtp
                   */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '7771';
        } elseif (preg_match(
            "/The recipient server did not accept our requests to connect/i",
            $body,
            $match
        )) {
            /*
                  * rule: mailbox warning;
                  * sample:
                  * The recipient server did not accept our requests to connect. Learn more at
                  https://support.google.com/mail/answer/7720
                  */
            $result['rule_cat'] = 'warning';
            $result['rule_no'] = '7720';
        } elseif (preg_match("/DNS type.*lookup of.*responded/i", $body, $match)) {
            /* rule: dns_unknown
                 * sample:
                 *   DNS Error: 3736752 DNS type 'mx' lookup of naturmail.com responded with code
                 * NOERROR 3736752 DNS type 'aaaa' lookup of mailhub1.emailpersonal.com. responded with cod
                 *   DNS Error: 4872806 DNS type 'mx' lookup of naturmail.com responded with code
                 *  NOERROR 4872806 DNS type 'aaaa' lookup of mailhub1.emailpersonal.com. resp
                */
            $result['rule_cat'] = 'dns_unknown';
            $result['rule_no'] = '7721';
        } elseif (preg_match("/550 Account disabled/i", $body, $match)) {
            /* rule: unknown
                * sample:
                *   550 Account disabled
                */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '7722';
        } elseif (preg_match("/5\.1\.2 error/i", $body, $match)) {
            /* rule: unknown
            * sample:
            *   5.1.2 error.
            */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '7723';
        } elseif (preg_match("/(?:User unknown|Unknown user|User not found|RecipNotFound.*not'.
        ' found|RecipientNotFound.*not found)/i", $body, $match)) {
            // Followings are wind-up rule: must be the last one
            //   many other rules msg end up with "550 5.1.1 ... User unknown"
            //   many other rules msg end up with "554 5.0.0 Service unavailable"
            //   many other rules msg end up with "550 5.1.1 RESOLVER.ADR.RecipNotFound; not found'"
            //   many other rules msg end up with "550 5.1.10 RESOLVER.ADR.RecipientNotFound; Recipient
            //  not found by SMTP address lookup"

            /* rule: unknown
             * sample 1:
             *   ----- The following addresses had permanent fatal errors -----
             *   <xxxxx@yourdomain.com>
             *   (reason: User unknown)
             * sample 2:
             *   550 5.1.1 xxxxx@yourdomain.com... User unknown
             *   550 5.1.1 User not found
             *   550 5.1.1 RESOLVER.ADR.RecipNotFound; not found'
             */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '7724';
        } elseif (preg_match("/Your message to.*couldn't be delivered/i", $body, $match)) {
            /* rule: unknown
         * sample:
         * Your message to xxxxx@yourdomain.com couldn't be delivered
         */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '7724';
        } elseif (preg_match("/(?:Address rejected|recipient address rejected)/i", $body, $match)) {
            /* rule: unknown
           * sample:
           *   550 #5.1.0 Address rejected.
           */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '7725';
        } elseif (preg_match(
            "/(?:alias|account|recipient|address|email|mailbox|user).*(?:un|not\s+)available/is",
            $body,
            $match
        )) {
            /* rule: unknown
                 * sample 1:
                 *   Diagnostic-Code: SMTP; 450 mailbox unavailable.
                 * sample 2:
                 *   Diagnostic-Code: SMTP; 550 5.7.1 Requested action not taken: mailbox not available
                 */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0122';
        } elseif (preg_match(
            '/(?:alias|account|recipient|address|email|mailbox|user).*(?:disabled|discontinued)/is',
            $body,
            $match
        )) {
            /* rule: unknown
                        * sample:
                        *   Diagnostic-Code: SMTP; 554 delivery error: dd Sorry your message to xxxxx@yourdomain.com
                        cannot be delivered. This account has been disabled or discontinued [#102].
                        - mta173.mail.tpe.domain.com
                        */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0114';
        } elseif (preg_match("/message is not for a local domain/i", $body, $match)) {
            /* rule: unknown
         * sample:
         * 550 message is not for a local domain
         */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '7726';
        } elseif (preg_match("/Usuario\s+desconocido/i", $body, $match)) {
            /* rule: unknown
           * sample: 550 Usuario desconocido
           *
           */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '7727';
        } elseif (preg_match("/(?:mailbox unavailable|mailbox unav)/i", $body, $match)) {
            /* rule: unknown
           * sample:
           *    550 5.5.0 Requested action not taken: mailbox unavailable.
           * 550 5.5.0 Requested action not taken: mailbox unava=
               ilable.
           */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '7728';
        } elseif (preg_match('/connection refused/i', $body, $match)) {
            /* rule: command_reject
         * sample:
         *   550 5.4.316 Message expired, connection refused(Socket error code 10061)
         */
            $result['rule_cat'] = 'command_reject';
            $result['rule_no'] = '0175';
        } elseif (preg_match('/Requested mail action aborted/i', $body, $match)) {
            /* rule: command_reject
         * sample:
         *   554 delivery error: dd Requested mail action aborted - mta1004.mail.ir2.yahoo.com
         */
            $result['rule_cat'] = 'command_reject';
            $result['rule_no'] = '7729';
        } elseif (preg_match('/Relay.*(?:denied|prohibited)/is', $body, $match)) {
            /* rule: unknown
                 * sample 1:
                 *   Diagnostic-Code: SMTP; 550 Relaying denied.
                 * sample 2:
                 *   Diagnostic-Code: SMTP; 554 <xxxxx@yourdomain.com>: Relay access denied
                 * sample 3:
                 *   Diagnostic-Code: SMTP; 550 relaying to <xxxxx@yourdomain.com> prohibited by administrator
                 */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0108';
        } elseif (preg_match(
            '/(?:alias|account|recipient|address|email|mailbox|user).*NOT FOUND/is',
            $body,
            $match
        )) {
            /* rule: unknown
                        * sample 1:
                        *   Diagnostic-Code: SMTP; 550 MAILBOX NOT FOUND
                        * sample 2:
                        *   Diagnostic-Code: SMTP; 550 Mailbox ( xxxxx@yourdomain.com ) not found or inactivated
                        */
            $result['rule_cat'] = 'unknown';
            $result['rule_no'] = '0136';
        } elseif (preg_match(
            '/relay.*not.*(?:permit|allow)/is',
            $body,
            $match
        )) {
            /* rule: command_reject
                        * sample 1:
                        *   Diagnostic-Code: SMTP; 550 relay not permitted
                        * sample 2:
                        *   Diagnostic-Code: SMTP; 530 5.7.1 Relaying not allowed: xxxxx@yourdomain.com
                        */
            $result['rule_cat'] = 'command_reject';
            $result['rule_no'] = '0241';
        } elseif (preg_match(
            "/exceed.*\n?.*quota/i",
            $body,
            $match
        )) {
            /* rule: full
                        * sample:
                        *   The user to whom this message was addressed has exceeded the allowed mailbox
                        *   quota. Please resend the message at a later time.
                        */
            $result['rule_cat'] = 'full';
            $result['rule_no'] = '0187';
        } elseif (preg_match('/undeliverable and rejected/i', $body, $match)) {
            /* rule: command_reject
         * sample:
         *   This message was undeliverable and rejected by the intended recipient.
         */
            $result['rule_cat'] = 'command_reject';
            $result['rule_no'] = '7733';
        } elseif (preg_match(
            "/prevents.*\n?.*delivered/i",
            $body,
            $match
        )) {
            /* rule: full
                 * sample:
                 *   450 4.2.1 The user you are trying to contact is receiving mail at a rate that prevents additional
                 * messages from being delivered. Please resend your message at a later time. If the user is able to
                 * receive mail at that time, your message will be delivered. For more information,
                 * please visit https://support.google.com/mail/?p=ReceivingRate k12sor476811wmc.15 - gsmtp
                 */
            $result['rule_cat'] = 'full';
            $result['rule_no'] = '0187';
        }
        // elseif (preg_match("/(\S+@\S+\w).*n? The email address you entered/i", $body, $match)) {
        //     /* rule: unknown
        //     * sample:
        //     * <xxxxx@xxx.xxx>
        //     * The email address you entered couldn't be found. Please check the recipient's email address and try to
        // resend the message. If the problem continues, please contact your helpdesk.
        //     */
        //     $result['rule_cat'] = 'inactive';
        //     $result['rule_no'] = '7730';
        //     $result['email'] = $match[0];
        // }


        // elseif (preg_match("/(\S+@\S+\w).*n? No se ha encontrado la direcc/i", $body, $match)) {
        //     /* rule: unknown
        //     * sample:
        //     * <xxxxx@xxx.xxx>
        //     * No se ha encontrado la dirección de correo electrónico que ha escrito. Compruebe la dirección
        // de correo electrónico del destinatario e intente reenviar el mensaje. Si el problema persiste, póngase en
        // contacto con el administrador de correo electrónico.
        //     */
        //     $result['rule_cat'] = 'inactive';
        //     $result['rule_no'] = '7731';
        //     $result['email'] = $match[0];
        // }


        if ($result['rule_no'] !== '0000' && $result['email'] === '') {
            $preBody = substr($body, 0, strpos($body, $match[0]));

            $count = preg_match_all('/(\S+@\S+)/', $preBody, $match);
            if ($count) {
                $result['email'] = trim($match[1][$count - 1], "'\"()<>.:; \t\r\n\0\x0B");
            }
        }

        if ($result['rule_no'] == '0000') {
            if ($debug_mode) {
                echo 'Body:' . self::$bmh_newline . $body . self::$bmh_newline;
                echo self::$bmh_newline;
            }
        } else {
            if ($result['bounce_type'] === false) {
                $result['bounce_type'] = self::$rule_categories[$result['rule_cat']]['bounce_type'];
                $result['remove'] = self::$rule_categories[$result['rule_cat']]['remove'];
            }
        }

        return $result;
    }

    /**
     * Defined bounce parsing rules for standard DSN (Delivery Status Notification)
     *
     * @param string $dsn_msg human-readable explanation
     * @param string $dsn_report delivery-status report
     * @param boolean $debug_mode show debug info. or not
     *
     * @return array    $result an array include the following fields: 'email', 'bounce_type',
     *                  'remove','rule_no','rule_cat'
     *                      if we could NOT detect the type of bounce, return rule_no = '0000'
     */
    public static function bmhDSNRules($dsn_msg, $dsn_report, $debug_mode = false)
    {
        // initialize the result array
        $result = array(
            'email' => '',
            'bounce_type' => false,
            'remove' => 0,
            'rule_cat' => 'unrecognized',
            'rule_no' => '0000',
            'status_code' => '',
            'action' => '',
            'diagnostic_code' => '',
        );
        $action = false;
        $status_code = false;
        $diag_code = false;

        // ======= parse $dsn_report ======
        // get the recipient email
        if (preg_match('/Original-Recipient: rfc822;(.*)/i', $dsn_report, $match)) {
            $email = trim($match[1], "<> \t\r\n\0\x0B");
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            $email_arr = @imap_rfc822_parse_adrlist($email, 'default.domain.name');
            if (isset($email_arr[0]->host) && $email_arr[0]->host != '.SYNTAX-ERROR.' &&
                $email_arr[0]->host != 'default.domain.name'
            ) {
                $result['email'] = $email_arr[0]->mailbox . '@' . $email_arr[0]->host;
            }
        } elseif (preg_match('/Final-Recipient: rfc822;(.*)/i', $dsn_report, $match)) {
            $email = trim($match[1], "<> \t\r\n\0\x0B");
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            $email_arr = @imap_rfc822_parse_adrlist($email, 'default.domain.name');
            if (isset($email_arr[0]->host) && $email_arr[0]->host != '.SYNTAX-ERROR.' &&
                $email_arr[0]->host != 'default.domain.name'
            ) {
                $result['email'] = $email_arr[0]->mailbox . '@' . $email_arr[0]->host;
            }
        }

        if (preg_match('/Action: (.+)/i', $dsn_report, $match)) {
            $action = strtolower(trim($match[1]));
            $result['action'] = $action;
        }

        if (preg_match("/Status: ([0-9\.]+)/i", $dsn_report, $match)) {
            $status_code = $match[1];
            $result['status_code'] = $status_code;
        }

        // Could be multi-line , if the new line is beginning with SPACE or HTAB
        if (preg_match("/Diagnostic-Code:((?:[^\n]|\n[\t ])+)(?:\n[^\t ]|$)/i", $dsn_report, $match)) {
            $diag_code = $match[1];
        }

        // No Diagnostic-Code in email, use dsn message
        if (empty($diag_code)) {
            $diag_code = $dsn_msg;
        }

        $result['diagnostic_code'] = $diag_code;

        // ======= rules ======

        if (empty($result['email'])) {
            /* email address is empty
             * rule: full
             * sample:   DSN Message only
             * User quota exceeded: SMTP <xxxxx@yourdomain.com>
             */
            if (preg_match("/quota exceed.*<(\S+@\S+\w)>/is", $dsn_msg, $match)) {
                $result['rule_cat'] = 'full';
                $result['rule_no'] = '0161';
                $result['email'] = $match[1];
            }
        } else {
            /* action could be one of them as RFC:1894
             * "failed" / "delayed" / "delivered" / "relayed" / "expanded"
             */
            switch ($action) {
                case 'failed':
                    /* rule: full
                     * sample:
                     *   Diagnostic-Code: X-Postfix; me.domain.com platform: said: 552 5.2.2 Over
                     *     quota (in reply to RCPT TO command)
                     */
                    if (preg_match('/over.*quota/is', $diag_code)) {
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0105';
                    } elseif (preg_match('/exceed.*quota/is', $diag_code)) {
                        /* rule: full
         * sample:
         *   Diagnostic-Code: SMTP; 552 Requested mailbox exceeds quota.
         */
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0129';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*full/is',
                        $diag_code
                    )) {
                        /* rule: full
                              * sample 1:
                              *   Diagnostic-Code: smtp;552 5.2.2 This message is larger than the current system
                              limit or the recipient's mailbox is full. Create a shorter message body or remove
                              attachments and try sending it again.
                              * sample 2:
                              *   Diagnostic-Code: X-Postfix; host mta5.us4.domain.com.int[111.111.111.111] said:
                              *     552 recipient storage full, try again later (in reply to RCPT TO command)
                              * sample 3:
                              *   Diagnostic-Code: X-HERMES; host 127.0.0.1[127.0.0.1] said: 551 bounce as<the
                              *     destination mailbox <xxxxx@yourdomain.com> is full> queue as
                              *     100.1.ZmxEL.720k.1140313037.xxxxx@yourdomain.com (in reply to end of
                              *     DATA command)
                              */
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0145';
                    } elseif (preg_match('/Insufficient system storage/i', $diag_code)) {
                        /* rule: full
         * sample:
         *   Diagnostic-Code: SMTP; 452 Insufficient system storage
         */
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0134';
                    } elseif (preg_match('/Benutzer hat zuviele Mails auf dem Server/i', $diag_code)) {
                        /* rule: full
         * sample:
         *   Diagnostic-Code: SMTP; 422 Benutzer hat zuviele Mails auf dem Server
         */
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0998';
                    } elseif (preg_match('/exceeded storage allocation/i', $diag_code)) {
                        /* rule: full
         * sample:
         *   Diagnostic-Code: SMTP; 422 exceeded storage allocation
         */
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0997';
                    } elseif (preg_match('/Mailbox quota usage exceeded/i', $diag_code)) {
                        /* rule: full
         * sample:
         *   Diagnostic-Code: SMTP; 422 Mailbox quota usage exceeded
         */
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0996';
                    } elseif (preg_match('/User has exhausted allowed storage space/i', $diag_code)) {
                        /* rule: full
         * sample:
         *   Diagnostic-Code: SMTP; 422 User has exhausted allowed storage space
         */
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0995';
                    } elseif (preg_match('/User mailbox exceeds allowed size/i', $diag_code)) {
                        /* rule: full
         * sample:
         *   Diagnostic-Code: SMTP; 422 User mailbox exceeds allowed size
         */
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0994';
                    } elseif (preg_match("/not.*enough\s+space/i", $diag_code)) {
                        /* rule: full
         * sample:
         *   Diagnostic-Code: smpt; 552 Account(s) <a@b.c> does not have enough space
         */
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0246';
                    } elseif (preg_match('/File too large/i', $diag_code)) {
                        /* rule: full
         * sample 1:
         *   Diagnostic-Code: X-Postfix; cannot append message to destination file
         *     /var/mail/dale.me89g: error writing message: File too large
         * sample 2:
         *   Diagnostic-Code: X-Postfix; cannot access mailbox /var/spool/mail/b8843022 for
         *     user xxxxx. error writing message: File too large
         */
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0192';
                    } elseif (preg_match('/larger than.*limit/is', $diag_code)) {
                        /* rule: oversize
         * sample:
         *   Diagnostic-Code: smtp;552 5.2.2 This message is larger than the current system limit or the
         * recipient's mailbox is full. Create a shorter message body or remove attachments and try sending it again.
         */
                        $result['rule_cat'] = 'oversize';
                        $result['rule_no'] = '0146';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user)(.*)not(.*)list/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Diagnostic-Code: X-Notes; User xxxxx (xxxxx@yourdomain.com) not listed in public
                              Name & Address Book
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0103';
                    } elseif (preg_match('/user path no exist/i', $diag_code)) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: smtp; 450 user path no exist
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0106';
                    } elseif (preg_match('/Relay.*(?:denied|prohibited)/is', $diag_code)) {
                        /* rule: unknown
         * sample 1:
         *   Diagnostic-Code: SMTP; 550 Relaying denied.
         * sample 2:
         *   Diagnostic-Code: SMTP; 554 <xxxxx@yourdomain.com>: Relay access denied
         * sample 3:
         *   Diagnostic-Code: SMTP; 550 relaying to <xxxxx@yourdomain.com> prohibited by administrator
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0108';
                    } elseif (preg_match(
                        '/no.*valid.*(?:alias|account|recipient|address|email|mailbox|user)/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                            * sample:
                            *   Diagnostic-Code: SMTP; 554 qq Sorry, no valid recipients (#5.1.3)
                            */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0185';
                    } elseif (preg_match(
                        '/Invalid.*(?:alias|account|recipient|address|email|mailbox|user)/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample 1:
                              *   Diagnostic-Code: SMTP; 550 «Dªk¦a§} - invalid address (#5.5.0)
                              * sample 2:
                              *   Diagnostic-Code: SMTP; 550 Invalid recipient: <xxxxx@yourdomain.com>
                              * sample 3:
                              *   Diagnostic-Code: SMTP; 550 <xxxxx@yourdomain.com>: Invalid User
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0111';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*(?:disabled|discontinued)/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Diagnostic-Code: SMTP; 554 delivery error: dd Sorry your message
                              to xxxxx@yourdomain.com
                              cannot be delivered. This account has been disabled or discontinued [#102]. -
                               mta173.mail.tpe.domain.com
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0114';
                    } elseif (preg_match("/user doesn't have.*account/is", $diag_code)) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: SMTP; 554 delivery error: dd This user doesn't have a domain.com account
         * (www.xxxxx@yourdomain.com) [0] - mta134.mail.tpe.domain.com
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0127';
                    } elseif (preg_match(
                        '/(?:unknown|illegal).*(?:alias|account|recipient|address|email|mailbox|user)/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Diagnostic-Code: SMTP; 550 5.1.1 unknown or illegal alias: xxxxx@yourdomain.com
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0128';
                    } elseif (preg_match(
                        "/(?:alias|account|recipient|address|email|mailbox|user).*(?:un|not\s+)available/is",
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample 1:
                              *   Diagnostic-Code: SMTP; 450 mailbox unavailable.
                              * sample 2:
                              *   Diagnostic-Code: SMTP; 550 5.7.1 Requested action not taken: mailbox not available
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0122';
                    } elseif (preg_match('/no (?:alias|account|recipient|address|email|mailbox|user)/i', $diag_code)) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: SMTP; 553 sorry, no mailbox here by that name (#5.7.1)
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0123';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*unknown/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample 1:
                              *   Diagnostic-Code: SMTP; 550 User (xxxxx@yourdomain.com) unknown.
                              * sample 2:
                              *   Diagnostic-Code: SMTP; 553 5.3.0 <xxxxx@yourdomain.com>...
                              Addressee unknown, relay=[111.111.111.000]
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0125';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*disabled/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample 1:
                              *   Diagnostic-Code: SMTP; 550 user disabled
                              * sample 2:
                              *   Diagnostic-Code: SMTP; 452 4.2.1 mailbox temporarily disabled: xxxxx@yourdomain.com
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0133';
                    } elseif (preg_match(
                        '/No such (?:alias|account|recipient|address|email|mailbox|user)/i',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Diagnostic-Code: SMTP; 550 <xxxxx@yourdomain.com>: Recipient address rejected:
                               No such user (xxxxx@yourdomain.com)
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0143';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*NOT FOUND/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample 1:
                              *   Diagnostic-Code: SMTP; 550 MAILBOX NOT FOUND
                              * sample 2:
                              *   Diagnostic-Code: SMTP; 550 Mailbox ( xxxxx@yourdomain.com ) not found or inactivated
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0136';
                    } elseif (preg_match(
                        '/deactivated (?:alias|account|recipient|address|email|mailbox|user)/i',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *    Diagnostic-Code: X-Postfix; host m2w-in1.domain.com[111.111.111.000] said: 551
                              *    <xxxxx@yourdomain.com> is a deactivated mailbox (in reply to RCPT TO
                              *    command)
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0138';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*reject/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Diagnostic-Code: SMTP; 550 <xxxxx@yourdomain.com> recipient rejected
                              *   ...
                              *   <<< 550 <xxxxx@yourdomain.com> recipient rejected
                              *   550 5.1.1 xxxxx@yourdomain.com... User unknown
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0148';
                    } elseif (preg_match('/bounce.*administrator/is', $diag_code)) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: smtp; 5.x.0 - Message bounced by administrator  (delivery attempts: 0)
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0151';
                    } elseif (preg_match('/<.*>.*disabled/is', $diag_code)) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: SMTP; 550 <maxqin> is now disabled with MTA service.
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0152';
                    } elseif (preg_match('/not our customer/i', $diag_code)) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: SMTP; 551 not our customer
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0154';
                    } elseif (preg_match(
                        '/Wrong (?:alias|account|recipient|address|email|mailbox|user)/i',
                        $diag_code
                    )) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: smtp; 5.1.0 - Unknown address error 540-'Error: Wrong recipients' (delivery attempts: 0)
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0159';
                    } elseif (preg_match(
                        '/(?:unknown|bad).*(?:alias|account|recipient|address|email|mailbox|user)/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Diagnostic-Code: smtp; 5.1.0 - Unknown address error 540-'Error: Wrong recipients'
                              (delivery attempts: 0)
                              * sample 2:
                              *   Diagnostic-Code: SMTP; 501 #5.1.1 bad address xxxxx@yourdomain.com
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0160';
                    } elseif (preg_match(
                        '/(?:unknown|bad).*(?:alias|account|recipient|address|email|mailbox|user)/is',
                        $status_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Status: 5.1.1 (bad destination mailbox address)
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '01601';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*not OK/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Diagnostic-Code: SMTP; 550 Command RCPT User <xxxxx@yourdomain.com> not OK
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0186';
                    } elseif (preg_match('/Access.*Denied/is', $diag_code)) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: SMTP; 550 5.7.1 Access-Denied-XM.SSR-001
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0189';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*lookup.*fail/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Diagnostic-Code: SMTP; 550 5.1.1 <xxxxx@yourdomain.com>... email address lookup
                              in domain map failed
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0195';
                    } elseif (preg_match(
                        '/(?:recipient|address|email|mailbox|user).*not.*member of domain/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                            * sample:
                            *   Diagnostic-Code: SMTP; 550 User not a member of domain: <xxxxx@yourdomain.com>
                            */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0198';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*cannot be verified/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Diagnostic-Code: SMTP; 550-"The recipient cannot be verified.
                              Please check all recipients of this
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0202';
                    } elseif (preg_match('/Unable to relay/i', $diag_code)) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: SMTP; 550 Unable to relay for xxxxx@yourdomain.com
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0203';
                    } elseif (preg_match(
                        "/(?:alias|account|recipient|address|email|mailbox|user).*(?:n't|not) exist/is",
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample 1:
                              *   Diagnostic-Code: SMTP; 550 xxxxx@yourdomain.com:user not exist
                              * sample 2:
                              *   Diagnostic-Code: SMTP; 550 sorry, that recipient doesn't exist (#5.7.1)
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0205';
                    } elseif (preg_match('/not have an account/i', $diag_code)) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: SMTP; 550-I'm sorry but xxxxx@yourdomain.com does not have an account here. I will not
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0207';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*is not allowed/is',
                        $diag_code
                    )) {
                        /* rule: unknown
                              * sample:
                              *   Diagnostic-Code: SMTP; 550 This account is not allowed...xxxxx@yourdomain.com
                              */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0220';
                    } elseif (preg_match('/not unique.\s+Several matches found/i', $diag_code)) {
                        /* rule: unknown
         * sample:
         *   Diagnostic-Code: X-Notes; Recipient user name info (a@b.c) not unique.
         * Several matches found in Domino Directory.
         */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0255';
                    } elseif (preg_match(
                        '/inactive.*(?:alias|account|recipient|address|email|mailbox|user)/is',
                        $diag_code
                    )) {
                        /* rule: inactive
                              * sample:
                              *   Diagnostic-Code: SMTP; 550 <xxxxx@yourdomain.com>: inactive user
                              */
                        $result['rule_cat'] = 'inactive';
                        $result['rule_no'] = '0135';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*Inactive/is',
                        $diag_code
                    )) {
                        /* rule: inactive
                            * sample:
                            *   Diagnostic-Code: SMTP; 550 xxxxx@yourdomain.com Account Inactive
                            */
                        $result['rule_cat'] = 'inactive';
                        $result['rule_no'] = '0155';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user) closed due to inactivity/i',
                        $diag_code
                    )) {
                        /* rule: inactive
                            * sample:
                            *   Diagnostic-Code: SMTP; 550 <xxxxx@yourdomain.com>: Recipient address rejected:
                            Account closed due to inactivity. No forwarding information is available.
                            */
                        $result['rule_cat'] = 'inactive';
                        $result['rule_no'] = '0170';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user) not activated/i',
                        $diag_code
                    )) {
                        /* rule: inactive
                            * sample:
                            *   Diagnostic-Code: SMTP; 550 <xxxxx@yourdomain.com>... User account not activated
                            */
                        $result['rule_cat'] = 'inactive';
                        $result['rule_no'] = '0177';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*(?:suspend|expire)/is',
                        $diag_code
                    )) {
                        /* rule: inactive
                              * sample 1:
                              *   Diagnostic-Code: SMTP; 550 User suspended
                              * sample 2:
                              *   Diagnostic-Code: SMTP; 550 account expired
                              */
                        $result['rule_cat'] = 'inactive';
                        $result['rule_no'] = '0183';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*no longer exist/is',
                        $diag_code
                    )) {
                        /* rule: inactive
                              * sample:
                              *   Diagnostic-Code: SMTP; 553 5.3.0 <xxxxx@yourdomain.com>...
                              Recipient address no longer exists
                              */
                        $result['rule_cat'] = 'inactive';
                        $result['rule_no'] = '0184';
                    } elseif (preg_match('/(?:forgery|abuse)/i', $diag_code)) {
                        /* rule: inactive
         * sample:
         *   Diagnostic-Code: SMTP; 553 VS10-RT Possible forgery or deactivated due to abuse (#5.1.1) 111.111.111.211
         */
                        $result['rule_cat'] = 'inactive';
                        $result['rule_no'] = '0196';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*restrict/is',
                        $diag_code
                    )) {
                        /* rule: inactive
                              * sample:
                              *   Diagnostic-Code: SMTP; 553 mailbox xxxxx@yourdomain.com is restricted
                              */
                        $result['rule_cat'] = 'inactive';
                        $result['rule_no'] = '0209';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*locked/is',
                        $diag_code
                    )) {
                        /* rule: inactive
                            * sample:
                            *   Diagnostic-Code: SMTP; 550 <xxxxx@yourdomain.com>: User status is locked.
                            */
                        $result['rule_cat'] = 'inactive';
                        $result['rule_no'] = '0228';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user) refused/i',
                        $diag_code
                    )) {
                        /* rule: user_reject
                            * sample:
                            *   Diagnostic-Code: SMTP; 553 User refused to receive this mail.
                            */
                        $result['rule_cat'] = 'user_reject';
                        $result['rule_no'] = '0156';
                    } elseif (preg_match('/sender.*not/is', $diag_code)) {
                        /* rule: user_reject
         * sample:
         *   Diagnostic-Code: SMTP; 501 xxxxx@yourdomain.com Sender email is not in my domain
         */
                        $result['rule_cat'] = 'user_reject';
                        $result['rule_no'] = '0206';
                    } elseif (preg_match('/Message refused/i', $diag_code)) {
                        /* rule: command_reject
         * sample:
         *   Diagnostic-Code: SMTP; 554 Message refused
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0175';
                    } elseif (preg_match('/No permit/i', $diag_code)) {
                        /* rule: command_reject
         * sample:
         *   Diagnostic-Code: SMTP; 550 5.0.0 <xxxxx@yourdomain.com>... No permit
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0190';
                    } elseif (preg_match("/domain isn't in.*allowed rcpthost/is", $diag_code)) {
                        /* rule: command_reject
         * sample:
         *   Diagnostic-Code: SMTP; 553 sorry, that domain isn't in my list of allowed rcpthosts (#5.5.3 - chkuser)
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0191';
                    } elseif (preg_match('/AUTH FAILED/i', $diag_code)) {
                        /* rule: command_reject
         * sample:
         *   Diagnostic-Code: SMTP; 553 AUTH FAILED - xxxxx@yourdomain.com
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0197';
                    } elseif (preg_match('/relay.*not.*(?:permit|allow)/is', $diag_code)) {
                        /* rule: command_reject
         * sample 1:
         *   Diagnostic-Code: SMTP; 550 relay not permitted
         * sample 2:
         *   Diagnostic-Code: SMTP; 530 5.7.1 Relaying not allowed: xxxxx@yourdomain.com
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0241';
                    } elseif (preg_match('/not local host/i', $diag_code)) {
                        /* rule: command_reject
         * sample:
         *   Diagnostic-Code: SMTP; 550 not local host domain.com, not a gateway
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0204';
                    } elseif (preg_match('/Unauthorized relay/i', $diag_code)) {
                        /* rule: command_reject
         * sample:
         *   Diagnostic-Code: SMTP; 500 Unauthorized relay msg rejected
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0215';
                    } elseif (preg_match('/Transaction.*fail/is', $diag_code)) {
                        /* rule: command_reject
         * sample:
         *   Diagnostic-Code: SMTP; 554 Transaction failed
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0221';
                    } elseif (preg_match('/Invalid data/i', $diag_code)) {
                        /* rule: command_reject
         * sample:
         *   Diagnostic-Code: smtp;554 5.5.2 Invalid data in message
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0223';
                    } elseif (preg_match('/Local user only/i', $diag_code)) {
                        /* rule: command_reject
         * sample:
         *   Diagnostic-Code: SMTP; 550 Local user only or Authentication mechanism
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0224';
                    } elseif (preg_match('/not.*permit.*to/is', $diag_code)) {
                        /* rule: command_reject
         * sample:
         *   Diagnostic-Code: SMTP; 550-ds176.domain.com [111.111.111.211] is currently not permitted to
         *   relay through this server. Perhaps you have not logged into the pop/imap
         *   server in the last 30 minutes or do not have SMTP Authentication turned on
         *   in your email client.
         */
                        $result['rule_cat'] = 'command_reject';
                        $result['rule_no'] = '0225';
                    } elseif (preg_match('/Content reject/i', $diag_code)) {
                        /* rule: content_reject
         * sample:
         *   Diagnostic-Code: SMTP; 550 Content reject. FAAAANsG60M9BmDT.1
         */
                        $result['rule_cat'] = 'content_reject';
                        $result['rule_no'] = '0165';
                    } elseif (preg_match("/MIME\/REJECT/i", $diag_code)) {
                        /* rule: content_reject
         * sample:
         *   Diagnostic-Code: SMTP; 552 MessageWall: MIME/REJECT: Invalid structure
         */
                        $result['rule_cat'] = 'content_reject';
                        $result['rule_no'] = '0212';
                    } elseif (preg_match('/MIME error/i', $diag_code)) {
                        /* rule: content_reject
         * sample:
         *   Diagnostic-Code: smtp; 554 5.6.0 Message with invalid header rejected, id=13462-01 - MIME error:
         * error: UnexpectedBound: part didn't end with expected boundary [in multipart message];
         *  EOSToken: EOF; EOSType: EOF
         */
                        $result['rule_cat'] = 'content_reject';
                        $result['rule_no'] = '0217';
                    } elseif (preg_match('/Mail data refused.*AISP/is', $diag_code)) {
                        /* rule: content_reject
         * sample:
         *   Diagnostic-Code: SMTP; 553 Mail data refused by AISP, rule [169648].
         */
                        $result['rule_cat'] = 'content_reject';
                        $result['rule_no'] = '0218';
                    } elseif (preg_match('/Host unknown/i', $diag_code)) {
                        /* rule: dns_unknown
         * sample:
         *   Diagnostic-Code: SMTP; 550 Host unknown
         */
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0130';
                    } elseif (preg_match('/Specified domain.*not.*allow/is', $diag_code)) {
                        /* rule: dns_unknown
         * sample:
         *   Diagnostic-Code: SMTP; 553 Specified domain is not allowed.
         */
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0180';
                    } elseif (preg_match('/No route to host/i', $diag_code)) {
                        /* rule: dns_unknown
         * sample:
         *   Diagnostic-Code: X-Postfix; delivery temporarily suspended: connect to
         *   111.111.11.112[111.111.11.112]: No route to host
         */
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0188';
                    } elseif (preg_match('/unrouteable address/i', $diag_code)) {
                        /* rule: dns_unknown
         * sample:
         *   Diagnostic-Code: SMTP; 550 unrouteable address
         */
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0208';
                    } elseif (preg_match('/Host or domain name not found/i', $diag_code)) {
                        /* rule: dns_unknown
         * sample:
         *   Diagnostic-Code: X-Postfix; Host or domain name not found. Name service error
         *     for name=aaaaaaaaaaa type=A: Host not found
         */
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0238';
                    } elseif (preg_match('/loops back to myself/i', $diag_code)) {
                        /* rule: dns_loop
         * sample:
         *   Diagnostic-Code: X-Postfix; mail for mta.example.com loops back to myself
         */
                        $result['rule_cat'] = 'dns_loop';
                        $result['rule_no'] = '0245';
                    } elseif (preg_match('/System.*busy/is', $diag_code)) {
                        /* rule: defer
         * sample:
         *   Diagnostic-Code: SMTP; 451 System(u) busy, try again later.
         */
                        $result['rule_cat'] = 'defer';
                        $result['rule_no'] = '0112';
                    } elseif (preg_match('/Resources temporarily unavailable/i', $diag_code)) {
                        /* rule: defer
         * sample:
         *   Diagnostic-Code: SMTP; 451 mta172.mail.tpe.domain.com Resources temporarily unavailable.
         * Please try again later.  [#4.16.4:70].
         */
                        $result['rule_cat'] = 'defer';
                        $result['rule_no'] = '0116';
                    } elseif (preg_match('/sender is rejected/i', $diag_code)) {
                        /* rule: antispam, deny ip
         * sample:
         *   Diagnostic-Code: SMTP; 554 sender is rejected: 0,mx20,wKjR5bDrnoM2yNtEZVAkBg==.32467S2
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0101';
                    } elseif (preg_match('/Client host rejected/i', $diag_code)) {
                        /* rule: antispam, deny ip
         * sample:
         *   Diagnostic-Code: SMTP; 554 <unknown[111.111.111.000]>: Client host rejected: Access denied
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0102';
                    } elseif (preg_match('/MAIL FROM(.*)mismatches client IP/is', $diag_code)) {
                        /* rule: antispam, mismatch ip
         * sample:
         *   Diagnostic-Code: SMTP; 554 Connection refused(mx). MAIL FROM [xxxxx@yourdomain.com] mismatches
         * client IP [111.111.111.000].
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0104';
                    } elseif (false !== stripos($diag_code, "denyip")) {
                        /* rule: antispam, deny ip
         * sample:
         *   Diagnostic-Code: SMTP; 554 Please visit http:// antispam.domain.com/denyip.php?IP=111.111.111.000
         * (#5.7.1)
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0144';
                    } elseif (preg_match('/client host.*blocked/is', $diag_code)) {
                        /* rule: antispam, deny ip
         * sample:
         *   Diagnostic-Code: SMTP; 554 Service unavailable; Client host [111.111.111.211] blocked using
         * dynablock.domain.com; Your message could not be delivered due to complaints we received regarding
         * the IP address you're using or your ISP. See http:// blackholes.domain.com/ Error: WS-02
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0242';
                    } elseif (preg_match('/mail.*reject/is', $diag_code)) {
                        /* rule: antispam, reject
         * sample:
         *   Diagnostic-Code: SMTP; 550 Requested action not taken: mail IsCNAPF76kMDARUY.56621S2 is rejected,mx3,BM
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0147';
                    } elseif (preg_match('/spam.*detect/is', $diag_code)) {
                        /* rule: antispam
         * sample:
         *   Diagnostic-Code: SMTP; 552 sorry, the spam message is detected (#5.6.0)
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0162';
                    } elseif (preg_match('/reject.*spam/is', $diag_code)) {
                        /* rule: antispam
         * sample:
         *   Diagnostic-Code: SMTP; 554 5.7.1 Rejected as Spam see:
         * http:// rejected.domain.com/help/spam/rejected.html
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0216';
                    } elseif (false !== stripos($diag_code, "SpamTrap")) {
                        /* rule: antispam
         * sample:
         *   Diagnostic-Code: SMTP; 553 5.7.1 <xxxxx@yourdomain.com>... SpamTrap=reject mode, dsn=5.7.1,
         * Message blocked by BOX Solutions (www.domain.com) SpamTrap Technology, please contact the domain.com
         * site manager for help: (ctlusr8012).
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0200';
                    } elseif (preg_match('/Verify mailfrom failed/i', $diag_code)) {
                        /* rule: antispam, mailfrom mismatch
         * sample:
         *   Diagnostic-Code: SMTP; 550 Verify mailfrom failed,blocked
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0210';
                    } elseif (preg_match('/MAIL.*FROM.*mismatch/is', $diag_code)) {
                        /* rule: antispam, mailfrom mismatch
         * sample:
         *   Diagnostic-Code: SMTP; 550 Error: MAIL FROM is mismatched with message header from address!
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0226';
                    } elseif (preg_match('/spam scale/i', $diag_code)) {
                        /* rule: antispam
         * sample:
         *   Diagnostic-Code: SMTP; 554 5.7.1 Message scored too high on spam scale.  For help,
         * please quote incident ID 22492290.
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0211';
                    } elseif (preg_match('/Client host bypass/i', $diag_code)) {
                        /* rule: antispam
         * sample:
         *   Diagnostic-Code: SMTP; 554 5.7.1 reject: Client host bypassing service provider's mail relay:
         * ds176.domain.com
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0229';
                    } elseif (preg_match('/junk mail/i', $diag_code)) {
                        /* rule: antispam
         * sample:
         *   Diagnostic-Code: SMTP; 550 sorry, it seems as a junk mail
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0230';
                    } elseif (preg_match('/message filtered/i', $diag_code)) {
                        /* rule: antispam
         * sample:
         *   Diagnostic-Code: SMTP; 553-Message filtered. Please see the FAQs section on spam
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0243';
                    } elseif (preg_match('/subject.*consider.*spam/is', $diag_code)) {
                        /* rule: antispam, subject filter
         * sample:
         *   Diagnostic-Code: SMTP; 554 5.7.1 The message from (<xxxxx@yourdomain.com>) with
         * the subject of ( *(ca2639) 7|-{%2E* : {2"(%EJ;y} (SBI$#$@<K*:7s1!=l~) matches a profile
         *  the Internet community may consider spam. Please revise your message before resending.
         */
                        $result['rule_cat'] = 'antispam';
                        $result['rule_no'] = '0222';
                    } elseif (preg_match('/Temporary local problem/i', $diag_code)) {
                        /* rule: internal_error
         * sample:
         *   Diagnostic-Code: SMTP; 451 Temporary local problem - please try later
         */
                        $result['rule_cat'] = 'internal_error';
                        $result['rule_no'] = '0142';
                    } elseif (preg_match('/system config error/i', $diag_code)) {
                        /* rule: internal_error
         * sample:
         *   Diagnostic-Code: SMTP; 553 5.3.5 system config error
         */
                        $result['rule_cat'] = 'internal_error';
                        $result['rule_no'] = '0153';
                    } elseif (preg_match('/delivery.*suspend/is', $diag_code)) {
                        /* rule: delayed
         * sample:
         *   Diagnostic-Code: X-Postfix; delivery temporarily suspended: conversation with
         *   111.111.111.11[111.111.111.11] timed out while sending end of data -- message may be
         *   sent more than once
         */
                        $result['rule_cat'] = 'delayed';
                        $result['rule_no'] = '0213';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user)(?:.*)invalid/i',
                        $dsn_msg
                    )) {
                        // =========== rules based on the dsn_msg ===============

                        /* rule: unknown
                     * sample:
                     *   ----- The following addresses had permanent fatal errors -----
                     *   <xxxxx@yourdomain.com>
                     *   ----- Transcript of session follows -----
                     *   ... while talking to mta1.domain.com.:
                     *   >>> DATA
                     *   <<< 503 All recipients are invalid
                     *   554 5.0.0 Service unavailable
                     */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0107';
                    } elseif (preg_match('/Deferred.*No such.*(?:file|directory)/i', $dsn_msg)) {
                        /* rule: unknown
* sample:
*   ----- Transcript of session follows -----
*   xxxxx@yourdomain.com... Deferred: No such file or directory
*/
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0141';
                    } elseif (preg_match('/mail receiving disabled/i', $dsn_msg)) {
                        /* rule: unknown
* sample:
*   Failed to deliver to '<xxxxx@yourdomain.com>'
*   LOCAL module(account xxxx) reports:
*   mail receiving disabled
*/
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0194';
                    } elseif (preg_match(
                        '/bad.*(?:alias|account|recipient|address|email|mailbox|user)/i',
                        $status_code
                    )) {
                        /* rule: unknown
* sample:
*   - These recipients of your message have been processed by the mail server:
*   xxxxx@yourdomain.com; Failed; 5.1.1 (bad destination mailbox address)
*/
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '02441';
                    } elseif (preg_match(
                        '/bad.*(?:alias|account|recipient|address|email|mailbox|user)/i',
                        $dsn_msg
                    )
                    ) {
                        /* rule: unknown
* sample:
*   - These recipients of your message have been processed by the mail server:
*   xxxxx@yourdomain.com; Failed; 5.1.1 (bad destination mailbox address)
*/
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0244';
                    } elseif (preg_match('/over.*quota/i', $dsn_msg)) {
                        /* rule: full
* sample 1:
*   This Message was undeliverable due to the following reason:
*   The user(s) account is temporarily over quota.
*   <xxxxx@yourdomain.com>
* sample 2:
*   Recipient address: xxxxx@yourdomain.com
*   Reason: Over quota
*/
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0131';
                    } elseif (preg_match('/quota.*exceeded/i', $dsn_msg)) {
                        /* rule: full
* sample:
*   Sorry the recipient quota limit is exceeded.
*   This message is returned as an error.
*/
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0150';
                    } elseif (preg_match("/exceed.*\n?.*quota/i", $dsn_msg)) {
                        /* rule: full
* sample:
*   The user to whom this message was addressed has exceeded the allowed mailbox
*   quota. Please resend the message at a later time.
*/
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0187';
                    } elseif (preg_match(
                        '/(?:alias|account|recipient|address|email|mailbox|user).*full/i',
                        $dsn_msg
                    )
                    ) {
                        /* rule: full
* sample 1:
*   Failed to deliver to '<xxxxx@yourdomain.com>'
*   LOCAL module(account xxxxxx) reports:
*   account is full (quota exceeded)
* sample 2:
*   Error in fabiomod_sql_glob_init: no data source specified - database access disabled
*   [Fri Feb 17 23:29:38 PST 2006] full error for caltsmy:
*   that member's mailbox is full
*   550 5.0.0 <xxxxx@yourdomain.com>... Can't create output
*/
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0132';
                    } elseif (preg_match('/space.*not.*enough/i', $dsn_msg)) {
                        /* rule: full
* sample:
*   gaosong "(0), ErrMsg=Mailbox space not enough (space limit is 10240KB)
*/
                        $result['rule_cat'] = 'full';
                        $result['rule_no'] = '0219';
                    } elseif (preg_match('/Deferred.*Connection (?:refused|reset)/i', $dsn_msg)) {
                        /* rule: defer
* sample 1:
*   ----- Transcript of session follows -----
*   xxxxx@yourdomain.com... Deferred: Connection refused by nomail.tpe.domain.com.
*   Message could not be delivered for 5 days
*   Message will be deleted from queue
* sample 2:
*   451 4.4.1 reply: read error from www.domain.com.
*   xxxxx@yourdomain.com... Deferred: Connection reset by www.domain.com.
*/
                        $result['rule_cat'] = 'defer';
                        $result['rule_no'] = '0115';
                    } elseif (preg_match('/Invalid host name/i', $dsn_msg)) {
                        /* rule: dns_unknown
* sample:
*   ----- The following addresses had permanent fatal errors -----
*   Tan XXXX SSSS <xxxxx@yourdomain..com>
*   ----- Transcript of session follows -----
*   553 5.1.2 XXXX SSSS <xxxxx@yourdomain..com>... Invalid host name
*/
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0239';
                    } elseif (preg_match('/Deferred.*No route to host/i', $dsn_msg)) {
                        /* rule: dns_unknown
* sample:
*   ----- Transcript of session follows -----
*   xxxxx@yourdomain.com... Deferred: mail.domain.com.: No route to host
*/
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0240';
                    } elseif (preg_match('/Host unknown/i', $dsn_msg)) {
                        /* rule: dns_unknown
* sample:
*   ----- Transcript of session follows -----
*   550 5.1.2 xxxxx@yourdomain.com... Host unknown (Name server: .: no data known)
*/
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0140';
                    } elseif (preg_match('/Name server timeout/i', $dsn_msg)) {
                        /* rule: dns_unknown
* sample:
*   ----- Transcript of session follows -----
*   451 HOTMAIL.com.tw: Name server timeout
*   Message could not be delivered for 5 days
*   Message will be deleted from queue
*/
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0118';
                    } elseif (preg_match('/Deferred.*Connection.*tim(?:e|ed).*out/i', $dsn_msg)) {
                        /* rule: dns_unknown
* sample:
*   ----- Transcript of session follows -----
*   xxxxx@yourdomain.com... Deferred: Connection timed out with hkfight.com.
*   Message could not be delivered for 5 days
*   Message will be deleted from queue
*/
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0119';
                    } elseif (preg_match('/Deferred.*host name lookup failure/i', $dsn_msg)) {
                        /* rule: dns_unknown
* sample:
*   ----- Transcript of session follows -----
*   xxxxx@yourdomain.com... Deferred: Name server: domain.com.: host name lookup failure
*/
                        $result['rule_cat'] = 'dns_unknown';
                        $result['rule_no'] = '0121';
                    } elseif (preg_match('/MX list.*point.*back/i', $dsn_msg)) {
                        /* rule: dns_loop
* sample:
*   ----- Transcript of session follows -----
*   554 5.0.0 MX list for znet.ws. points back to mail01.domain.com
*   554 5.3.5 Local configuration error
*/
                        $result['rule_cat'] = 'dns_loop';
                        $result['rule_no'] = '0199';
                    } elseif (preg_match("/I\/O error/i", $dsn_msg)) {
                        /* rule: internal_error
* sample:
*   ----- Transcript of session follows -----
*   451 4.0.0 I/O error
*/
                        $result['rule_cat'] = 'internal_error';
                        $result['rule_no'] = '0120';
                    } elseif (preg_match('/connection.*broken/i', $dsn_msg)) {
                        /* rule: internal_error
* sample:
*   Failed to deliver to 'xxxxx@yourdomain.com'
*   SMTP module(domain domain.com) reports:
*   connection with mx1.mail.domain.com is broken
*/
                        $result['rule_cat'] = 'internal_error';
                        $result['rule_no'] = '0231';
                    } elseif (preg_match(
                        "/Delivery to the following recipients failed.*\n.*\n.*" . $result['email'] . '/i',
                        $dsn_msg
                    )) {
                        /* rule: other
* sample:
*   Delivery to the following recipients failed.
*   xxxxx@yourdomain.com
*/
                        $result['rule_cat'] = 'other';
                        $result['rule_no'] = '0176';
                    } elseif (preg_match('/(?:User unknown|Unknown user)/i', $dsn_msg)) {
                        // Followings are wind-up rule: must be the last one
                        //   many other rules msg end up with "550 5.1.1 ... User unknown"
                        //   many other rules msg end up with "554 5.0.0 Service unavailable"

                        /* rule: unknown
 * sample 1:
 *   ----- The following addresses had permanent fatal errors -----
 *   <xxxxx@yourdomain.com>
 *   (reason: User unknown)
 * sample 2:
 *   550 5.1.1 xxxxx@yourdomain.com... User unknown
 */
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0193';
                    } elseif (preg_match('/Service unavailable/i', $dsn_msg)) {
                        /* rule: unknown
* sample:
*   554 5.0.0 Service unavailable
*/
                        $result['rule_cat'] = 'unknown';
                        $result['rule_no'] = '0214';
                    }
                    break;

                case 'delayed':
                    $result['rule_cat'] = 'delayed';
                    $result['rule_no'] = '0110';
                    break;

                case 'delivered':
                case 'relayed':
                case 'expanded': // unhandled cases
                    break;

                default:
                    break;
            }
        }

        if ($result['rule_no'] == '0000') {
            if ($debug_mode) {
                echo 'email: ' . $result['email'] . self::$bmh_newline;
                echo 'Action: ' . $action . self::$bmh_newline;
                echo 'Status: ' . $status_code . self::$bmh_newline;
                echo 'Diagnostic-Code: ' . $diag_code . self::$bmh_newline;
                echo "DSN Message:<br />\n" . $dsn_msg . self::$bmh_newline;
                echo self::$bmh_newline;
            }
        } else {
            if ($result['bounce_type'] === false) {
                $result['bounce_type'] = self::$rule_categories[$result['rule_cat']]['bounce_type'];
                $result['remove'] = self::$rule_categories[$result['rule_cat']]['remove'];
            }
        }

        return $result;
    }
}
