<?php

namespace Clavel\Recognition\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Clavel\Recognition\Services\RecognitionService;

class RecognitionTextractController extends Controller
{
    private $recognition = null;
    private $bucket = '';

    public function __construct()
    {
        $this->recognition = new RecognitionService();

        $this->bucket = config('recognition.s3.bucket', '');
    }

    public function getTextract(Request $request)
    {
        $page_title = trans("recognition::general/front_lang.titleTextract");

        $bucket = $this->bucket;
        $objects = $this->recognition->getBucketObjects($bucket);

        return view(
            'recognition::recognition.textract.front_index_textract',
            compact(
                'page_title',
                'bucket',
                'objects'
            )
        );
    }

    public function getTextractProcess(Request $request, $bucket, $file)
    {
        $page_title = trans("recognition::general/front_lang.titleTextract");

        $file = base64_decode($file);

        $textract = $this->recognition->getTextractText($this->bucket, $file);

        $textDetections = $textract['textos'];
        $dni = $textract['dni'];

        return view(
            'recognition::recognition.textract.front_index_textract_objects',
            compact(
                'page_title',
                'textDetections',
                'dni'
            )
        );
    }
}
