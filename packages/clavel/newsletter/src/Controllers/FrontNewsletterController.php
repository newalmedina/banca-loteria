<?php

namespace App\Modules\Newsletter\Controllers;

use App\Http\Controllers\FrontController;
use App\Modules\Newsletter\Contracts\NewsletterList;
use App\Modules\Newsletter\Models\NewsletterMailingList;

class FrontNewsletterController extends FrontController
{

    /**
     * @var NewsletterList
     */
    private $newsletterList;

    public function __construct(NewsletterList $newsletterList)
    {
        parent::__construct();


        $this->newsletterList = $newsletterList;
    }

    public function index($id = null)
    {
        $page_title = trans("home/front_lang.inicio");
        $a_news = array();
        $listado = NewsletterMailingList::getBySlug('newsletterSubscribers')
            ->campaigns()
            ->onlySent()
            ->orderBy("sent_at", "DESC");
        if ($id != '') {
            $listado = $listado->where("newsletter_id", $id);
        }
        $listado = $listado->first();
        if (!is_null($listado) && !is_null($listado->newsletter)) {
            $a_news = $listado->newsletter->arrayPosts();
        }

        return view('Newsletter::front_newsletter', compact('page_title', 'listado', 'a_news'));
    }

    public function listado()
    {
        $page_title = trans("Newsletter::front_lang.listado_newsletters");

        $listado = NewsletterMailingList::getBySlug('newsletterSubscribers')
            ->campaigns()
            ->onlySent()
            ->orderBy("sent_at", "DESC")
            ->paginate(10);

        return view('Newsletter::front_listado', compact('page_title', 'listado'));
    }

    public function previewNewsletter($id)
    {
        if (file_exists(storage_path("newsletter/processed/$id.html"))) {
            $file = storage_path("newsletter/processed/$id.html");
            return file_get_contents($file);
        }
        return "";
    }
}
