<?php

namespace Clavel\Recognition\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Clavel\Recognition\Services\RecognitionService;

class RecognitionRekognitionController extends Controller
{
    private $recognition = null;
    private $bucket = '';

    public function __construct()
    {
        $this->recognition = new RecognitionService();

        $this->bucket = config('recognition.s3.bucket', '');
    }

    public function getRekognition(Request $request)
    {
        $page_title = trans("recognition::general/front_lang.titleRekognition");

        $bucket = $this->bucket;
        $objects = $this->recognition->getBucketObjects($bucket);


        return view(
            'recognition::recognition.rekognition.front_index_rekognition',
            compact(
                'page_title',
                'bucket',
                'objects'
            )
        );
    }

    public function getRekognitionProcess(Request $request, $bucket, $file)
    {
        $page_title = trans("recognition::general/front_lang.titleRekognition");

        $file = base64_decode($file);

        $textract = $this->recognition->getRekognitionText($this->bucket, $file);

        $textDetections = $textract['textos'];
        $dni = $textract['dni'];


        $textDetections = $textract['textos'];
        $dni = $textract['dni'];

        return view(
            'recognition::recognition.rekognition.front_index_rekognition_objects',
            compact(
                'page_title',
                'textDetections',
                'dni'
            )
        );
    }

    public function getRekognitionProcessLabel(Request $request, $bucket, $file)
    {
        $page_title = trans("recognition::general/front_lang.titleRekognition");

        $file = base64_decode($file);

        $textLabels = $this->recognition->getRekognitionProcessLabel($this->bucket, $file);

        $textDetections = $textLabels['textDetections'];

        return view(
            'recognition::recognition.rekognition.front_index_rekognition_objects_label',
            compact(
                'page_title',
                'textDetections'
            )
        );
    }
}
