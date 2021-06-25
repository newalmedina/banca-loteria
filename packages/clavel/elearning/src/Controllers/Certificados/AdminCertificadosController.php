<?php

namespace Clavel\Elearning\Controllers\Certificados;

use Clavel\Elearning\Models\CertificadoPaginaTranslationsElements;
use Clavel\Elearning\Requests\CertificadosRequest;
use Clavel\Elearning\Models\Certificado;

use App\Http\Requests;
use App\Http\Controllers\AdminController;
use Clavel\Elearning\Models\CertificadoPagina;
use Clavel\Elearning\Models\CertificadoPaginaTranslation;
use App\Services\GetLanguage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;

class AdminCertificadosController extends AdminController
{
    protected $page_title_icon = '<i class="fa  fa-file-image-o" aria-hidden="true"></i>';

    private $numero_paginas = 3;

    public function __construct()
    {
        parent::__construct();

        //if (Auth::user()!=null && (!Auth::user()->can('admin-certificados'))) abort(404);
    }

    public function index()
    {
        if (!Auth::user()->can('admin-certificados-list')) {
            abort(404);
        }

        $page_title = trans("elearning::certificados/admin_lang.certificados");

        return view('elearning::certificados.admin_index', compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getData()
    {
        $certificados = Certificado::select(
            array(
                'id',
                'activo',
                'nombre'
            )
        );

        return Datatables::of($certificados)
            ->editColumn(
                'activo',
                '@if(Auth::user()->can("admin-certificados-update"))
                        @if($activo)
                            <button class="btn btn-success btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/certificados/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.descativa') . '"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/certificados/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.activa') . '"
                            data-placement="right" data-toggle="popover">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </button>
                        @endif
                    @else
                        @if($activo)
                            <button class="btn btn-success btn-sm disabled" data-placement="right">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm disabled" data-placement="right">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </button>
                        @endif
                    @endif'
            )
            ->addColumn('actions', '
                @if(Auth::user()->can("admin-certificados-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/certificados/\'.$id.\'/edit\') }}\';"
                    data-content="' . trans('general/admin_lang.modificar') . '"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-certificados-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/certificados/\'.$id.\'/destroy\') }}\');"
                    data-content="' . trans('general/admin_lang.borrar') . '"
                     data-placement="left" data-toggle="popover">
                     <i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->rawColumns(['activo', 'actions'])
            ->make();
    }

    public function create()
    {
        if (!Auth::user()->can('admin-certificados-create')) {
            abort(404);
        }
        $certificado = new Certificado();
        $certificado->paginas = $this->numero_paginas;
        $paginas = new $certificado->paginasCertificado();
        $form_data = array(
            'route' => array(
                'admin.certificados.store'
            ),
            'method' => 'POST', 'id' => 'formData', 'class' => 'form-horizontal'
        );
        $page_title = trans("elearning::certificados/admin_lang.nuevo_pages");

        // Idiomas
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getLangs();
        return view(
            'elearning::certificados.admin_edit',
            compact(
                'page_title',
                'certificado',
                'form_data',
                'a_trans'
            )
        );
    }

    public function store(CertificadosRequest $request)
    {
        if (!Auth::user()->can('admin-certificados-create')) {
            abort(404);
        }
        $certificado = Certificado::create($request->except("_token"));

        for ($i = 1; $i <= $certificado->paginas; $i++) {
            $pagina = new CertificadoPagina();
            $pagina->certificado_id = $certificado->id;
            $pagina->pagina = $i;
            $pagina->save();
        }

        return redirect('admin/certificados/' . $certificado->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function edit($id)
    {
        if (!Auth::user()->can('admin-certificados-update')) {
            abort(404);
        }

        $certificado = Certificado::find($id);
        $form_data = array(
            'route' => array(
                'admin.certificados.update', $certificado->id
            ),
            'method' => 'PATCH', 'id' => 'formData', 'class' => 'form-horizontal'
        );
        $page_title = trans("elearning::certificados/admin_lang.modify_page");

        // Idiomas
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getLangs();

        return view(
            'elearning::certificados.admin_edit',
            compact('page_title', 'certificado', 'form_data', 'a_trans')
        );
    }

    public function update(CertificadosRequest $request, $id)
    {
        if (!Auth::user()->can('admin-certificados-update')) {
            abort(404);
        }

        $certificado = Certificado::find($id);
        $certificado->update($request->except("_token", "pagina", "certlang", "keypag"));
        return redirect('admin/certificados/' . $certificado->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('admin-certificados-delete')) {
            abort(404);
        }

        $certificado = Certificado::find($id);
        if (is_null($certificado)) {
            abort(404);
        }
        $certificado->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Certificado eliminada',
            'id' => $certificado->id
        ));
    }

    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-certificados-update')) {
            abort(404);
        }

        $certificado = Certificado::find($id);

        if (!is_null($certificado)) {
            $certificado->activo = !$certificado->activo;
            return $certificado->save() ? 1 : 0;
        }

        return 0;
    }

    public function getElement(Request $request, $id)
    {
        if (!Auth::user()->can('admin-certificados-update')) {
            abort(404);
        }

        $element = CertificadoPaginaTranslationsElements::find($id);
        if (is_null($element)) {
            abort(404);
        }

        $page_title = trans("elearning::certificados/admin_lang.elements");

        $fontFamily = trans("elearning::certificados/font_lang.family");
        $fontSize = trans("elearning::certificados/font_lang.size");
        $fontColor = $element->fontcolor;

        $elementTranslation = $element->paginaTranslation;
        $plantilla = CertificadoPaginaTranslation::where(
            'certificado_pagina_id',
            $elementTranslation->certificado_pagina_id
        )
            ->where('locale', $elementTranslation->locale)->first();
        $page_number = CertificadoPagina::select("pagina")->find($plantilla->id)->pagina;

        return view(
            'elearning::certificados.admin_elements',
            compact(
                'page_title',
                'idioma',
                'plantilla',
                'fontFamily',
                'fontSize',
                'page_number',
                'element'
            )
        );
    }
    public function getElements($idioma, $idplantilla, $top, $left)
    {
        $page_title = trans("elearning::certificados/admin_lang.elements");

        $fontFamily = trans("elearning::certificados/font_lang.family");
        $fontSize = trans("elearning::certificados/font_lang.size");

        $element = new CertificadoPaginaTranslationsElements();
        $element->mtop = $top;
        $element->mleft = $left;
        $element->width = 225;
        $element->height = 125;
        $element->fontcolor = '#000000';
        $plantilla = CertificadoPaginaTranslation::where('certificado_pagina_id', $idplantilla)
            ->where('locale', $idioma)
            ->first();
        $page_number = CertificadoPagina::select("pagina")->find($idplantilla)->pagina;

        return view(
            'elearning::certificados.admin_elements',
            compact(
                'page_title',
                'idioma',
                'plantilla',
                'fontFamily',
                'fontSize',
                'page_number',
                'element'
            )
        );
    }

    public function postElements(Request $request)
    {
        $element_id = $request->input('id');
        $element = null;
        if (!empty($element_id)) {
            $element = CertificadoPaginaTranslationsElements::where('id', $element_id)->first();
        }

        if (!empty($element)) {
            $element->update($request->all());
        } else {
            $element = CertificadoPaginaTranslationsElements::create($request->all());
        }


        return view('elearning::certificados.admin_element_unit', compact('element'));
    }

    public function moveElement(Request $request)
    {
        $element = CertificadoPaginaTranslationsElements::find($request->input('id'));

        $mtop = $request->input("mtop");
        $mleft = $request->input("mleft");
        $width = $request->input("width");
        $height = $request->input("height");
        if (isset($mtop)) {
            $element->mtop = $mtop;
        }
        if (isset($mleft)) {
            $element->mleft = $mleft;
        }
        if (isset($width)) {
            $element->width = $width;
        }
        if (isset($height)) {
            $element->height = $height;
        }

        $element->save();
        return $element->id;
    }

    public function postPlantilla(Request $request)
    {
        $certificado_pagina_id = $request->input("certificado_pagina_id");
        $plantilla = CertificadoPaginaTranslation::where('certificado_pagina_id', $certificado_pagina_id)
            ->where('locale', $request->input("locale"))
            ->first();
        if (isset($plantilla)) {
            $plantilla->update($request->all());
        } else {
            $plantilla = CertificadoPaginaTranslation::create($request->all());
        }
        return $plantilla->id;
    }

    public function destroyElement($id)
    {
        if (!Auth::user()->can('admin-certificados-delete')) {
            abort(404);
        }

        $element = CertificadoPaginaTranslationsElements::find($id);
        if (is_null($element)) {
            abort(404);
        }
        $element->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Elemento eliminado',
            'id' => $element->id
        ));
    }

    public function pdfCertificado($idcertificado)
    {
        if (!Auth::user()->can('admin-certificados-list')) {
            abort(404);
        }
        $certificado = Certificado::find($idcertificado);

        // Idiomas
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getLangs();
        $view = view('elearning::certificados.admin_pdf', compact('certificado', 'a_trans'))->render();

        // Creamos la carpeta storage/fonts si no existe. Es necesaria para la descarga de fuentes externas como cache
        $outPath = storage_path("fonts/");
        if (!file_exists($outPath)) {
            mkdir($outPath, 0777, true);
        }
        //return $view;
        try {
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('a4', 'landscape');

            return $pdf->stream('Certificado');
        } catch (\Exception $ex) {
            die('Se ha producido un error al generar el certificado. Asegurate que la plantilla esta en formato RGB.');
        }
    }
}
