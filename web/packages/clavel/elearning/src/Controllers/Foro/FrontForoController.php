<?php

namespace Clavel\Elearning\Controllers\Foro;

use App\Http\Controllers\FrontController;
use Clavel\Elearning\Models\ForoEntrada;
use Clavel\Elearning\Requests\ForoMsgRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class FrontForoController extends FrontController
{
    public $selected_foro;

    protected $foro_slug = "paciente";
    protected $page_title = "foro/front_lang.Foro_paciente";

    public function __construct()
    {
        parent::__construct();

        if (auth()->user() != null && !auth()->user()->can('frontend-foro')) {
            abort(404);
        }

        $this->selected_foro = ForoEntrada::activos()->first();
    }

    public function index($asignatura_id = 0, $modulo_id = 0, $contenido_id = 0)
    {
        $temas = ForoEntrada::hiloPadre()->activos();
        if (!empty($asignatura_id)) {
            $temas->where("asignatura_id", $asignatura_id);
        }
        if (!empty($modulo_id)) {
            $temas->where("modulo_id", $modulo_id);
        }
        if (!empty($contenido_id)) {
            $temas->where("contenido_id", $contenido_id);
        }
        $temas = $temas->paginate(2);

        return view("elearning::foro.front_inicio_partial", compact("temas"));
    }

    public function create(Request $request)
    {
        if (auth()->user() != null && !auth()->user()->can('frontend-foro-create')) {
            abort(404);
        }

        $mensaje = new ForoEntrada();
        $mensaje->asignatura_id = $request->input("asignatura_id", null);
        $mensaje->modulo_id = $request->input("modulo_id", null);
        $mensaje->contenido_id = $request->input("contenido_id", null);

        if (!empty($request->input("parent_id", null))) {
            $mensaje->parent_id = $request->input("parent_id");
            $mensaje_padre = ForoEntrada::find($mensaje->parent_id);
            $mensaje->titulo = "RE: " . $mensaje_padre->titulo;
        }

        $form_data = array('route' => array('foro.store'), 'method' => 'POST', 'id' => 'frmSendHilo');

        return view("elearning::foro.front_formulario", compact('mensaje', 'form_data'));
    }

    public function store(ForoMsgRequest $request)
    {
        if (!auth()->user()->can("frontend-foro-create")) {
            abort(404);
        }

        $parent_id = (!is_null($request->get("parent_id"))
            && $request->get("parent_id") != '') ? $request->get("parent_id") : null;

        try {
            DB::beginTransaction();

            $foroEntrada = new ForoEntrada();
            $foroEntrada->user_id = $request->user_id;
            $foroEntrada->parent_id = $parent_id;
            $foroEntrada->asignatura_id = $request->asignatura_id;
            $foroEntrada->modulo_id = ($request->modulo_id === null ? 0 : $request->modulo_id);
            $foroEntrada->contenido_id = ($request->contenido_id === null ? 0 : $request->contenido_id);
            $foroEntrada->visible = 1;
            $foroEntrada->titulo = $request->titulo;
            $foroEntrada->mensaje = $request->mensaje;
            $foroEntrada->save();

            DB::commit();

            //Envio email creación nuevo foro
            $user = auth()->user()->username;
            $email = auth()->user()->email;
            $msn = $request->mensaje;
            Mail::send('elearning::foro.email_foro', compact('user', 'email', 'msn'), function ($message) {
                $message
                    ->to(env('MAIL_FROM_ADDRESS_CONTACT_US'))
                    ->subject(trans('elearning::foro/front_lang.email_confirmation') . env("APP_NAME"));
            });

            return response()->json(["success" => true, "parent_id" => $parent_id]);
        } catch (\PDOException $e) {
            DB::rollBack();
            echo "NOK";
        }
    }

    public function show(Request $request)
    {
        $id = $request->id;
        if (auth()->user() != null && !auth()->user()->can('frontend-foro-list')) {
            abort(404);
        }

        $mensajes = ForoEntrada::where("parent_id", "=", $id)
            ->orWhere("id", "=", $id)
            ->activos()
            ->orderby("created_at", "ASC")
            ->paginate(8);

        if (is_null($mensajes) || empty($mensajes)) {
            abort(404);
        }

        $page_list = $request->input("page_list", "1");

        return view("elearning::foro.front_detalle", compact("mensajes", 'page_list'));
    }

    public function edit($id)
    {
        if (auth()->user() != null && !auth()->user()->can('frontend-foro-update')) {
            abort(404);
        }

        $mensaje = ForoEntrada::findOrFail($id);

        $form_data = array(
            'route' => array(
                'foro.update', $mensaje->id
            ),
            'method' => 'PATCH',
            'id' => 'frmSendHilo'
        );

        return view("elearning::foro.front_formulario", compact('mensaje', 'form_data'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
        if (!auth()->user()->can('frontend-foro-update')) {
            abort(404);
        }

        $parent_id = (!is_null($request->input("parent_id"))
            && $request->input("parent_id") != '') ? $request->input("parent_id") : null;

        $request->merge(array(
            'parent_id' => (!is_null($request->input("parent_id"))
                && $request->input("parent_id") != '') ? $request->input("parent_id") : null
        ));

        try {
            DB::beginTransaction();
            $mensaje = ForoEntrada::findOrFail($id);
            $mensaje->update($request->except('_token'));
            DB::commit();

            //Envio email modificación foro
            $user = auth()->user()->username;
            $email = auth()->user()->email;
            $msn = $mensaje->mensaje;

            Mail::send('elearning::foro.email_edit_foro', compact('user', 'email', 'msn'), function ($message) {
                $message
                    ->to(env('MAIL_FROM_ADDRESS_CONTACT_US'))
                    ->subject(trans('elearning::foro/front_lang.email_confirmation') . env("APP_NAME"));
            });

            return response()->json(["success" => true, "parent_id" => $parent_id]);
        } catch (\PDOException $e) {
            DB::rollBack();
            echo "NOK";
        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('frontend-foro-delete') &&
            !auth()->user()->can("frontend-foro-delete-self")
        ) {
            abort(404);
        }

        $mensaje = ForoEntrada::findOrFail($id);
        $parent_id = $mensaje->parent_id;

        if (auth()->user()->id != $mensaje->user_id && !auth()->user()->can("frontend-foro-delete")) {
            abort(404);
        }

        try {
            return Response::json(array('success' => $mensaje->delete(), "parent_id" => $parent_id));
        } catch (\Exception $e) {
            return "NOK";
        }
    }
}
