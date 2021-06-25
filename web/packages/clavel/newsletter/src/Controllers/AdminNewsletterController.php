<?php

namespace App\Modules\Newsletter\Controllers;

use Carbon\Carbon;
use App\Models\Idioma;
use Pelago\Emogrifier;
use Illuminate\Http\Request;
use Clavel\Posts\Models\Post;
use App\Services\LanguageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

use App\Http\Controllers\AdminController;
use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\NewsletterRow;
use App\Modules\Newsletter\Models\NewsletterField;

use App\Modules\Newsletter\Models\NewsletterTemplates;
use App\Modules\Newsletter\Models\NewsletterTranslation;
use App\Modules\Newsletter\Requests\AdminNewsletterRequest;
use App\Modules\Newsletter\Models\NewsletterFieldTranslation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminNewsletterController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-fa-paper-plane" aria-hidden="true"></i>';


    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-newsletter';
    }

    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-newsletter-list')) {
            app()->abort(403);
        }

        $page_title = trans("Newsletter::admin_lang.newsletter");
        $idiomas = Idioma::active()->get();

        return view("Newsletter::admin_index", compact('page_title', 'idiomas'));
        //->with('page_title_icon', $this->page_title_icon);
    }

    public function create()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-newsletter-create')) {
            app()->abort(403);
        }

        $newsletter = new Newsletter();
        $a_news = $newsletter->arrayPosts();
        $form_data = array(
            'route' => array(
                'newsletter.store'
            ), 'method' => 'POST', 'id' => 'formData',
            'class' => 'form-horizontal'
        );
        $page_title = trans("Newsletter::admin_lang.nuevo_newsletter");
        $newsletter->generated = '0';

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($newsletter);

        $active_templates = NewsletterTemplates::where('active', 1)->pluck('nombre', 'id');

        return view('Newsletter::admin_edit', compact(
            'page_title',
            'newsletter',
            'a_news',
            'form_data',
            'page_description',
            'active_templates',
            'a_trans'
        ));
    }

    public function store(AdminNewsletterRequest $request)
    {
        if (!auth()->user()->can('admin-newsletter-create')) {
            app()->abort(403);
        }

        $newsletter = Newsletter::create($request->except(
            "_token",
            "export-textarea",
            "export-textarea-editable",
            "userlang"
        ));

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = NewsletterTranslation::findOrNew($value["id"]);
            $itemTrans->newsletter_id = $newsletter->id;
            $itemTrans->locale = $key;
            $itemTrans->subject = $value["subject"];
            $itemTrans->save();
        }

        return redirect()->to('admin/newsletter/' . $newsletter->id . "/edit")
            ->with('success', trans('Newsletter::admin_lang.save_ok'));
    }

    public function edit($id)
    {
        if (!auth()->user()->can('admin-newsletter-update')) {
            app()->abort(403);
        }

        $newsletter = Newsletter::findOrFail($id);
        $design = null;
        if (!empty($newsletter->template_id)) {
            $design = $newsletter->template->where("id", $newsletter->template_id)->first();
        }
        $a_news = $newsletter->arrayPostsDesigner();
        $form_data = array(
            'route' => array(
                'newsletter.update', $newsletter->id
            ),
            'method' => 'PATCH', 'id' => 'formData', 'class' => 'form-horizontal'
        );
        $page_title = trans("Newsletter::admin_lang.modify_newsletter");

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($newsletter);

        $active_templates = NewsletterTemplates::where('active', 1)->pluck('nombre', 'id');

        return view('Newsletter::admin_edit', compact(
            'page_title',
            'newsletter',
            'a_news',
            'form_data',
            'page_description',
            'design',
            'active_templates',
            'a_trans'
        ));
    }

    public function update(AdminNewsletterRequest $request, $id)
    {
        if (!auth()->user()->can('admin-newsletter-update')) {
            app()->abort(403);
        }

        $newsletter = Newsletter::findOrFail($id);
        $change_template = false;
        if ($newsletter->template_id != $request->input("template_id")) {
            $change_template = true;
        }

        $newsletter->update($request->except("_token", "export-textarea", "export-textarea-editable", 'userlang'));

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = NewsletterTranslation::findOrNew($value["id"]);
            $itemTrans->newsletter_id = $newsletter->id;
            $itemTrans->locale = $key;
            $itemTrans->subject = $value["subject"];
            $itemTrans->save();
        }

        if ($change_template) {
            $newsletter->custom_header = null;
            $newsletter->custom_footer = null;
            $newsletter->generated = false;
            $newsletter->save();
        } else {
            $this->generateTemplates(
                $newsletter,
                $request->input('export-textarea'),
                $request->input('export-textarea-editable')
            );
        }


        return redirect()->to('admin/newsletter/' . $newsletter->id . "/edit")
            ->with('success', trans('Newsletter::admin_lang.save_ok'));
    }

    public function destroy($id)
    {
        // Si no tiene permisos para modificar lo echamos
        if (!auth()->user()->can('admin-newsletter-delete')) {
            app()->abort(403);
        }

        $newsletter = Newsletter::findOrFail($id);

        if (file_exists(storage_path("newsletter/editable/" . $newsletter->id . ".html"))) {
            unlink(storage_path("newsletter/editable/" . $newsletter->id . ".html"));
        }
        if (file_exists(storage_path("newsletter/processed/" . $newsletter->id . ".html"))) {
            unlink(storage_path("newsletter/processed/" . $newsletter->id . ".html"));
        }
        if (file_exists(storage_path("newsletter/log/" . $newsletter->id . ".txt"))) {
            unlink(storage_path("newsletter/log/" . $newsletter->id . ".txt"));
        }
        $newsletter->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Newsletter eliminada',
            'id' => $newsletter->id
        ));
    }

    public function getData()
    {
        $locale = app()->getLocale();

        $newsletter = DB::table('newsletters as n')
            ->leftJoin('newsletter_translations as nt', function ($join) use ($locale) {
                $join->on('nt.newsletter_id', '=', 'n.id');
                $join->on('nt.locale', '=', DB::raw("'" . $locale . "'"));
            })
            ->select(
                array(
                    'n.id',
                    'n.name',
                    'nt.subject',
                    'n.generated'
                )
            );

        return Datatables::of($newsletter)
            ->editColumn('name', function ($row) {
                return $row->name;
            })
            ->addColumn('actions', '
                @if(Auth::user()->can("admin-newsletter-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/newsletter/\'.$id.\'/edit\') }}\';"
                    data-content="' . trans('general/admin_lang.modificar') . '" data-placement="right"
                    data-toggle="popover"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-newsletter-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/newsletter/\'.$id.\'\') }}\');"
                    data-content="' . trans('general/admin_lang.borrar') . '" data-placement="left"
                    data-toggle="popover"><i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                @if($generated)
                    <button class="btn bg-purple btn-sm"
                    onclick="javascript:showPreview(\'{{ $id }}\');"
                    data-content="' . trans('general/admin_lang.ver') . '" data-placement="right"
                    data-toggle="popover"><i class="fa fa-search" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->removeColumn('generated')
            ->rawColumns(['name', 'actions'])
            ->make();
    }

    private function generateTemplates($newsletter, $export_textarea, $export_textarea_editable)
    {
        if (isset($export_textarea)) {
            ob_clean();
            $this->createStoragePaths();

            $idiomas = Idioma::active()->get();
            foreach ($idiomas as $idioma) {
                $html = ' <table id="sim-wrapper" width="100%" aria-hidden="true">' .
                    '<tr><td id="sim-wrapper-newsletter" ALIGN="CENTER">' .
                    $export_textarea[$idioma->code] . '</td></tr>' .
                    '</table>';
                $css = file_get_contents(asset('assets/admin/css/newsletter_builder/newsletter.css'));
                $css .= 'body {padding: 0px; margin: 0px; background: ' .
                    $newsletter->template->background_content .
                    '; font-family: "Source Sans Pro","Helvetica Neue",Helvetica,Arial,sans-serif; }';

                $emogrifier = new Emogrifier();
                $emogrifier->setHtml($html);
                $emogrifier->setCss($css);
                $content_newsletter = $emogrifier->emogrify();
                file_put_contents(
                    storage_path("/newsletter/processed/" .
                        $idioma->code . "/" . $newsletter->id . ".html"),
                    $content_newsletter
                );
            }
        }

        if (isset($export_textarea_editable)) {
            file_put_contents(storage_path("/newsletter/editable/" .
                $newsletter->id . ".html"), $export_textarea);
        }
    }

    public function setRow(Request $request)
    {
        try {
            $newsletter_id = $request->input("id");
            $cols = $request->input("col");

            $row = new NewsletterRow();
            $row->newsletter_id = $newsletter_id;
            $row->cols = $cols;
            $row->position = 999;
            $row->save();

            echo $row->id;
        } catch (NotFoundHttpException $e) {
            echo "NOK";
        }
    }

    public function reorder(Request $request)
    {
        $a_ids = explode(",", $request->input("idspos"));

        $nX = 1;
        foreach ($a_ids as $id) {
            $row = NewsletterRow::find($id);
            $row->position = $nX;
            $row->save();
            $nX++;
        }

        return "OK";
    }

    public function deleteRow(Request $request)
    {
        $row = NewsletterRow::findOrFail($request->input("idrow"));
        $row->delete();
        return "OK";
    }

    public function formData($template_id, $row_id, $position, $id = "")
    {
        $design = NewsletterTemplates::findOrFail($template_id);
        if ($id != '') {
            $field = NewsletterField::findOrFail($id);
        } else {
            $field = new NewsletterField();
            $field->newsletter_row_id = $row_id;
            $field->position = $position;
            $field->image_position = "t";
            $field->type = "post";
            $field->in_box = 0;
        }

        $longitud_defecto = (int) (500 / $field->row->cols);

        if ($id == '' || ($field->text_length == '' && $field->text_length != '0')) {
            $field->text_length = $longitud_defecto;
        }
        if ($field->title_color == '') {
            $field->title_color = $design->title_font_color;
        }
        if ($field->text_color == '') {
            $field->text_color = $design->font_color;
        }

        $posts = Post::where('active', '=', '1')
            ->whereRaw(
                "((date_activation < ? OR COALESCE(date_activation,'') = '')
                    AND (date_deactivation > ? OR COALESCE(date_deactivation,'') = ''))",
                [
                    Carbon::today(),
                    Carbon::today()
                ]
            )
            ->get();

        // Idioma
        $serviceTranslation = new LanguageService(app()->getLocale());

        $a_trans = $serviceTranslation->getTranslations($field);

        $form_data = array(
            'route' => array('newsletter.savefield'),
            'method' => 'POST',
            'id' => 'formField',
            'class' => 'form-horizontal'
        );

        return view('Newsletter::admin_form', compact('form_data', 'field', 'posts', 'longitud_defecto', 'a_trans'));
    }


    public function savefield(Request $request)
    {
        if ($request->input("id") != '') {
            $newsletter_field = NewsletterField::findOrFail($request->input("id"));
            $newsletter_field->update($request->except(
                "_token",
                'image_position_info',
                'userlang'
            ));
        } else {
            $newsletter_field = NewsletterField::create(
                $request->except(
                    "_token",
                    'image_position_info',
                    'userlang'
                )
            );
        }

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = NewsletterFieldTranslation::findOrNew($value["id"]);

            $itemTrans->newsletter_row_field_id = $newsletter_field->id;
            $itemTrans->locale = $key;
            $itemTrans->body = $value["body"];
            $itemTrans->save();
        }

        if ($request->input("type") == 'post') {
            if ($newsletter_field->complete_post == '1') {
                $newsletter_field->text_length = null;
            }
            if ($newsletter_field->image_position != 'n') {
                $newsletter_field->image_position = $request->input("image_position_info");
            }
            $newsletter_field->save();
            return Response::json(array(NewsletterField::find($newsletter_field->id)->getPostMultilang()));
        } else {
            $newsletter_field->post_id = null;
            $newsletter_field->image_position = 't';
            $newsletter_field->title_color = "#53545e";
            $newsletter_field->text_color = "#8e8e90";
            $newsletter_field->text_length = null;
            $newsletter_field->complete_post = false;
            $newsletter_field->save();
            return Response::json(array(NewsletterField::find($newsletter_field->id)->getContentMultilang()));
        }
    }

    public function deletePost(Request $request)
    {
        $id = $request->input("id");

        $content = NewsletterField::FindOrFail($id);
        $content->delete();

        echo "OK";
    }

    public function customTemplate(Request $request)
    {
        $newsletter = Newsletter::findOrFail($request->input("newsletter_id"));
        $newsletter->custom_header = $request->input("custom_header");
        $newsletter->custom_footer = $request->input("custom_footer");
        $newsletter->save();
    }

    public function deshacer(Request $request)
    {
        $newsletter = Newsletter::findOrFail($request->input("newsletter_id"));
        $newsletter->custom_header = null;
        $newsletter->custom_footer = null;
        $newsletter->save();

        $a_response = array("header" => $newsletter->template->header, "footer" => $newsletter->template->footer);
        return Response::json($a_response);
    }

    private function createStoragePaths()
    {
        if (!file_exists(storage_path() . "/newsletter")) {
            mkdir(storage_path() . "/newsletter");
        }

        $idiomas = Idioma::active()->get();
        foreach ($idiomas as $idioma) {
            if (!file_exists(storage_path() . "/newsletter/processed/" . $idioma->code)) {
                mkdir(storage_path() . "/newsletter/processed/" . $idioma->code);
            }
        }

        if (!file_exists(storage_path() . "/newsletter/editable/")) {
            mkdir(storage_path() . "/newsletter/editable");
        }
    }

    public function previewNewsletter($id, $locale = "")
    {
        if ($locale == '') {
            $locale = config('app.locale');
        }
        $file = storage_path("newsletter/processed/$locale/$id.html");
        $html_return = file_get_contents($file);
        $html_return = str_replace("##NOMBRE##", "Jhon", $html_return);
        $html_return = str_replace("##APELLIDOS##", "Doe", $html_return);
        $html_return = str_replace("##FECHA##", Carbon::now()->format("d/m/Y"), $html_return);
        $html_return = str_replace("##NEWSLETTER_NAME##", "My newsletter", $html_return);
        $html_return = str_replace("##CAMPAIGN_NAME##", "My campaign", $html_return);
        return $html_return;
    }

    public function hfContenidos($row_id, $col_id)
    {
        $newsletter = new Newsletter();
        $serviceTranslation = new LanguageService(app()->getLocale());
        $a_trans = $serviceTranslation->getTranslations($newsletter);
        return view('Newsletter::admin_form_header_footer', compact('row_id', 'col_id', 'a_trans'));
    }
}
