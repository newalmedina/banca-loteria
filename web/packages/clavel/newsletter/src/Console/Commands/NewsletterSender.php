<?php

namespace App\Modules\Newsletter\Console\Commands;

use App\Modules\Newsletter\Jobs\NewsletterSendCampaign;
use App\Modules\Newsletter\Models\NewsletterCampaign;
use App\Modules\Newsletter\Models\NewsletterCampaignState;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NewsletterSender extends Command
{
    protected $signature = 'newsletter_sender';
    protected $description = 'Comprobación de envíos para la newsletter';



    public function handle()
    {
        $date_programa = Carbon::now();
        $code = NewsletterCampaignState::where("code", 2)->first();
        $this->comment("Inicio de comprobacion envios pendientes => ".$date_programa->format("d/m/Y H:i:s"));
        $campaigns = NewsletterCampaign
                        ::where(function ($q) use ($date_programa) {
                            $q->where('is_scheduled', 1);
                            $q->where("scheduled_for", "<=", $date_programa);
                        })
                        ->orWhere('is_scheduled', 0)
                        ->onlyPending()
                        ->get();

        foreach ($campaigns as $campaign) {
            $this->comment("Enviando campaña => ".$campaign->name);
            $campaign->sent_at = $date_programa;
            $campaign->newsletter_campaign_state_id = $code->id;
            $campaign->save();
            NewsletterSendCampaign::dispatch($campaign);
        }

        $this->comment("Fin de comprobacion envios pendientes");
    }
}
