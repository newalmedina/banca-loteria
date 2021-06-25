<?php

namespace App\Modules\Newsletter\Console\Commands;

use App\Modules\Newsletter\Contracts\NewsletterPublished;
use Illuminate\Console\Command;

class NewsletterPublishedNotifierCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:notify-newsletter-subscribers
                        {newsletterId : The ID of the newsletter}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify new newsletter published to subscribers';
    /**
     * @var NewsletterPublished
     */
    private $newsletterPublishedNotifier;

    /**
     * Create a new command instance.
     *
     * @param NewsletterPublished $newsletterPublishedNotifier
     */
    public function __construct(NewsletterPublished $newsletterPublishedNotifier)
    {
        parent::__construct();
        $this->newsletterPublishedNotifier = $newsletterPublishedNotifier;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $newsletter = $this->getNewsletter();

        //$body = \View::make('newsletter.custom', $data)->redner();
        $this->newsletterPublishedNotifier->notify($newsletter['title'], $newsletter['body']);
    }

    /**
     * Returns the data from the newsletter to send
     */
    private function getNewsletter()
    {
        //return Newsletter::findOrFiail($this->argument('newsletterId'));
        return [
          'title' => 'Newsletter title',
          'body' => 'Newsletter body'
        ];
    }
}
