<?php

namespace App\Modules\Events\Controllers;

use App\Http\Controllers\FrontController;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Models\EventFavourite;
use App\Modules\Events\Models\EventTag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class FrontEventsController extends FrontController
{
    public function index($date_search = '')
    {
        $page_title = trans("Events::front_lang.events_list");

        if ($date_search!='') {
            $date_info = new Carbon($date_search);
        } else {
            if (Session::get('date_search')!=null) {
                $date_info = new Carbon(Session::get('date_search'));
            } else {
                $date_info = Carbon::today();
            }
        }

        if (auth()->user()!=null) {
            $events_fav = EventFavourite::hasFavoriteInOrder()->get();
        } else {
            $events_fav = null;
        }

        return view('Events::front_index', compact('page_title', 'events_fav', 'date_info'));
    }

    public function loadMonth()
    {
        $month = $_REQUEST["mes"];
        $year = $_REQUEST["ano"];

        $dateIni = new Carbon($year."-".$month."-01");
        $dateEnd = new Carbon($year."-".$month."-31");

        /*
         *  WHERE (date_start<='2015-12-01'
            AND date_end>='2015-12-31')
            OR (date_end>='2015-12-01' AND date_end<='2015-12-31')
            OR (date_start<='2015-12-31' AND date_end>='2015-12-01')
        */
        $events = Event::activeEvents()
            ->whereRaw("(date_start<='".$dateIni."' AND date_end>='".$dateEnd."')
            OR (date_end>='".$dateIni."' AND date_end<='".$dateEnd."')
            OR (date_start<='".$dateEnd."' AND date_end>='".$dateIni."')")->get();

        $a_return = [];

        $nX = 0;
        foreach ($events as $event) {
            if ($event->permission == 0 ||
                ($event->permission == 1 && auth()->user() != null && auth()->user()->can($event->permission_name))) {
                $a_return[$nX]["fecha"] = $event->date_start;
                $a_return[$nX]["titulo"] = $event->title;
                if ($event->tags()->count() > 0) {
                    $a_return[$nX]["color"] = $event->tags[0]->color;
                }
                if ($event->date_start <= $event->date_end) {
                    $date_info = new Carbon($event->date_start);
                    $date_end = new Carbon($event->date_end);
                    while ($date_info != $date_end) {
                        $date_info->addDay(1);
                        $nX++;
                        $a_return[$nX]["fecha"] = $date_info->format("Y-m-d");
                        $a_return[$nX]["titulo"] = $event->title;
                        if ($event->tags()->count() > 0) {
                            $a_return[$nX]["color"] = $event->tags[0]->color;
                        }
                    }
                }
                $nX++;
            }
        }

        return Response::json($a_return);
    }

    public function list($date_search)
    {
        Session::put('date_search', $date_search);
        $date_info = new Carbon($date_search);
        $events = Event::activeEvents()
            ->where("date_start", '<=', $date_info)
            ->where("date_end", ">=", $date_info)
            ->paginate(10);
        $tags = EventTag::Actives()->limit(30)->offset(30)->get();

        return view('Events::front_events_list', compact('events', 'date_info', 'tags'));
    }

    public function eventDetail(Request $request)
    {
        if (!empty($request->date_start)) {
            $order = ($request->slug=='siguiente') ? "ASC" : "DESC";
            $rawcompare = ($request->slug=='siguiente') ? ">=" : "<=";

            $event = Event::where('date_start', $rawcompare, $request->date_start)
                ->where("id", "<>", $request->id)
                ->orderBy("date_start", $order)
                ->activeEvents()
                ->first();
        } else {
            $event = Event::whereTranslation('url_seo', $request->slug)
                ->activeEvents()
                ->first();
        }

        if (empty($event)) {
            abort(404);
        }

        // Si la página tiene permisos
        if ($event->permission=='1') {
            $this->middleware("auth");

            if (auth()->user()==null) {
                return redirect()->guest('login');
            }

            if (!auth()->user()->can($event->permission_name)) {
                abort(401);
            }
        }

        $page_title = trans("Events::front_lang.event");

        return view('Events::front_event_detail', compact('page_title', 'event'));
    }

    public function setFavourite()
    {
        $set_fav = $_REQUEST['set_fav'];
        $event_id = $_REQUEST["id"];

        if ($set_fav=='1') {
            $favorito = EventFavourite::where("event_id", $event_id)
                ->where("user_id", auth()->user()->id)
                ->first();
            $favorito->delete();
        } else {
            $favorito = new EventFavourite();
            $favorito->event_id = $event_id;
            $favorito->user_id = auth()->user()->id;
            $favorito->save();
        }
    }
}
