<?php

namespace Clavel\NotificationBroker\Controllers\Plantillas;

use App\Http\Controllers\AdminController;
use Clavel\NotificationBroker\Requests\AdminPlantillaRequest;
use Clavel\NotificationBroker\Models\Plantilla;
use Clavel\NotificationBroker\Services\GeneradorPlantillas;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Response;

class AdminPlantillasController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-plantillas';
    }

    public function index()
    {
        if (!auth()->user()->can('admin-plantillas-list')) {
            abort(403);
        }
        $page_title = trans("notificationbroker::plantillas/admin_lang.plantillas_notificaciones");
        return view("notificationbroker::plantillas.admin_index", compact('page_title'));
    }

    public function getData()
    {
        $plantillas = Plantilla::select(array('id', 'titulo', 'tipo', 'archivo', 'subject', 'is_generated'));

        return Datatables::of($plantillas)
            ->editColumn('active', function ($data) {
                if (is_null($data->is_generated)) {
                    $return = '';
                } elseif ($data->is_generated) {
                    $return = '<i class="fa fa-check-circle fa-2x" aria-hidden="true" style="color:#00a65a;"></i>';
                } else {
                    $return = '<i class="fa fa-exclamation-triangle fa-2x"
                        aria-hidden="true" style="color:#dd4b39;"></i>';
                }
                return $return;
            })
            ->addColumn('actions', '
                    @if(auth()->user()->can("admin-plantillas-update"))
                        <button class="btn btn-primary btn-sm"
                        onclick="javascript:window.location=\'{{ url(\'admin/plantillas/\'.$id.\'/edit\') }}\';"
                        data-content="' . trans('general/admin_lang.modificar') . '" data-placement="left"
                        data-toggle="popover"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                    @endif
                    @if(auth()->user()->can("admin-plantillas-delete"))
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:deleteElement(\'{{ url(\'admin/plantillas/\'.$id) }}\');"
                        data-content="' . trans('general/admin_lang.borrar') . '" data-placement="left"
                        data-toggle="popover"><i class="fa fa-trash" aria-hidden="true"></i></button>
                    @endif
                ')
            ->removeColumn('id')
            ->rawColumns(['actions', 'active'])
            ->make();
    }

    public function create()
    {
        if (!auth()->user()->can('admin-plantillas-create')) {
            abort(403);
        }
        $plantilla = new Plantilla();
        $form_data = array(
            'route' => array(
                'admin.plantillas.store'
            ),
            'method' => 'POST', 'id' => 'formTemplate',
            'class' => 'form-horizontal'
        );
        $page_title = trans("notificationbroker::plantillas/admin_lang.nueva_plantilla");
        return view('notificationbroker::plantillas.admin_edit', compact('page_title', 'plantilla', 'form_data'));
    }

    public function store(AdminPlantillaRequest $request)
    {
        if (!auth()->user()->can('admin-plantillas-create')) {
            abort(403);
        }
        try {
            $request->merge(array(
                'mensaje' => $request->input("mensaje_" . $request->input("tipo")),
                'slug' => str_slug($request->input("titulo"))
            ));
            $plantilla = Plantilla::create($request->except("token", "mensaje_sms", "mensaje_email", "generar"));

            if ($request->input("generar") == '1') {
                $this->createFileTemplate($plantilla->id);
            }

            return redirect("admin/plantillas/" . $plantilla->id . "/edit")
                ->with(array("success" => "Guardado correctamente"));
        } catch (\Exception $e) {
            return redirect("admin/plantillas/create")
                ->withErrors($e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!auth()->user()->can('admin-plantillas-update')) {
            abort(403);
        }

        $plantilla = Plantilla::findOrFail($id);
        $form_data = array(
            'route' => array(
                'admin.plantillas.update',
                $plantilla->id
            ),
            'method' => 'PATCH',
            'id' => 'formTemplate',
            'class' => 'form-horizontal'
        );
        $page_title = trans("notificationbroker::plantillas/admin_lang.modificar_plantilla");

        $iguales = $this->templateSaved($plantilla);

        return view(
            'notificationbroker::plantillas.admin_edit',
            compact('page_title', 'plantilla', 'form_data', 'iguales')
        );
    }

    private function templateSaved(Plantilla $plantilla)
    {
        $iguales = false;
        $contenido = '';
        if (!empty($plantilla->archivo)) {
            $tipoNotifiacion = array(
                "sms" => array("path" => "sms"),
                "email" => array("path" => "emails")
            );

            if (Storage::disk('templates')->exists("/" . $tipoNotifiacion[$plantilla->tipo]["path"] .
                "/" . $plantilla->archivo)) {
                $contenido = Storage::disk('templates')->get("/" . $tipoNotifiacion[$plantilla->tipo]["path"] .
                    "/" . $plantilla->archivo);
            }

            if (!empty($contenido)) {
                $contenido = str_replace("{{ @\$payload['", "{##", $contenido);
                $contenido = str_replace("'] }}", "##}", $contenido);

                $pos = strpos($contenido, $plantilla->mensaje);
                if ($pos === false) {
                    $iguales = false;
                } else {
                    $iguales = true;
                }
            }
        }

        return $iguales;
    }

    public function update(AdminPlantillaRequest $request, $id)
    {
        if (!auth()->user()->can('admin-plantillas-update')) {
            abort(403);
        }
        $plantilla = Plantilla::findOrFail($id);
        try {
            $plantilla->titulo = $request->input("titulo");
            $plantilla->tipo = $request->input("tipo");
            $plantilla->mensaje = $request->input("mensaje_" . $request->input("tipo"));
            if ($request->input("tipo") == 'email') {
                $plantilla->subject = $request->input("subject");
            }


            $plantilla->is_generated = $this->templateSaved($plantilla);
            $plantilla->save();

            if ($request->input("generar") == '1') {
                $this->createFileTemplate($plantilla->id);
            }

            return redirect("admin/plantillas/" . $plantilla->id . "/edit")
                ->with(array("success" => "Modificado correctamente"));
        } catch (\Exception $e) {
            return redirect("admin/plantillas/" . $plantilla->id . "/edit")
                ->withErrors($e->getMessage());
        }
    }

    public function destroy($id)
    {
        // Si no tiene permisos para borrar lo echamos
        if (!auth()->user()->can('admin-plantillas-delete')) {
            abort(403);
        }
        $plantilla = Plantilla::findOrFail($id);
        $genTemplate = new GeneradorPlantillas($plantilla);
        if ($genTemplate->deleteIfExistsFile()) {
            $plantilla->delete();
        }
        return Response::json(array(
            'success' => true,
            'msg' => 'Plantilla eliminada',
            'id' => $plantilla->id
        ));
    }

    public function createFileTemplate($template_id)
    {
        $plantilla = Plantilla::findOrFail($template_id);

        $genTemplate = new GeneradorPlantillas($plantilla);
        $generated = $genTemplate->createTemplate();

        $plantilla->is_generated = $this->templateSaved($plantilla);
        $plantilla->save();

        return $generated;
    }
}
