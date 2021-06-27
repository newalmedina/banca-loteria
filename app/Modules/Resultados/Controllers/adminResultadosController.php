<?php

namespace App\Modules\Resultados\Controllers;

use App\Http\Controllers\AdminController;
use App\Models\Permission;
use App\Modules\pruebas\Models\prueba;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class adminResultadosController extends AdminController
{
    protected $page_title_icon = '';

    public function __construct()
    {
        parent::__construct();
        //$this->access_permission = 'admin-pruebas';
    }

    
    public function index()
    {
        $crawler = Goutte::request('GET', 'https://duckduckgo.com/html/?q=Laravel');
        $crawler->filter('.result__title .result__a')->each(function ($node) {
          dump($node->text());
        });
        return view('welcome');
    }

 

    

}
