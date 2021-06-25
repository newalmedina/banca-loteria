<?php
$newsletter = file_get_contents(storage_path("/newsletter/processed/".$file));
$newsletter = str_replace("##NOMBRE##", $recipient->subscriber->userProfile->first_name, $newsletter);
$newsletter = str_replace("##APELLIDOS##", $recipient->subscriber->userProfile->last_name, $newsletter);
$newsletter = str_replace("##FECHA##", $campaing->sent_at_date_formatted, $newsletter);
$newsletter = str_replace("##NEWSLETTER_NAME##", $campaing->newsletter->{'subject:'.$recipient->subscriber->userProfile->user_lang}, $newsletter);
$newsletter = str_replace("##CAMPAIGN_NAME##", $campaing->name, $newsletter);
echo $newsletter;
