<?php

namespace App\Modules\Newsletter\Jobs;

use App\Modules\Newsletter\Models\NewsletterCampaign;
use App\Modules\Newsletter\Models\NewsletterCampaignState;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsletterSendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaing;

    public function __construct(NewsletterCampaign $campaign)
    {
        $this->campaing = $campaign;
    }

    public function handle()
    {
        ignore_user_abort(true);
        set_time_limit(0);
        $subscribers = $this->campaing->recipients()->where("is_sent", 0)->get();
        $code = NewsletterCampaignState::where("code", 3)->first();

        foreach ($subscribers as $recipient) {
            $email = $recipient->subscriber->email;
            $file = $recipient->subscriber->userProfile->user_lang . "/" . $this->campaing->newsletter->id . ".html";
            $id = $recipient->subscriber->id;
            $subject = $this->campaing->newsletter->{'subject:' . $recipient->subscriber->userProfile->user_lang};


            $response = Mail::send(
                'Newsletter::newsletter_sender.newsletter',
                array('file' => $file, 'recipient' => $recipient, 'campaing' => $this->campaing),
                function ($message) use ($email, $subject, $recipient) {
                    try {
                        $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
                        $message->to($email)->subject($subject);
                        $recipient->is_sent = true;
                    } catch (NotFoundHttpException $e) {
                        $recipient->send_result = $e->getMessage();
                    }
                }
            );
            if ($recipient->send_result != '') {
                $recipient->send_result = $response;
            }
            $recipient->save();
        }

        $this->campaing->sent_count++;
        $this->campaing->newsletter_campaign_state_id = $code->id;
        $this->campaing->save();
    }
}
