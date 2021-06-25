<?php

namespace Clavel\Recognition\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Clavel\Recognition\Services\RecognitionService;

class RecognitionS3Controller extends Controller
{
    private $recognition = null;

    public function __construct()
    {
        $this->recognition = new RecognitionService();
    }

    public function getS3(Request $request)
    {
        $page_title = trans("recognition::general/front_lang.titleS3");

        $buckets = $this->recognition->getBuckets();

        return view(
            'recognition::recognition.s3.front_index_s3',
            compact(
                'page_title',
                'buckets'
            )
        );
    }

    public function getS3BucketContent(Request $request, $bucket)
    {
        $page_title = trans("recognition::general/front_lang.titleS3Objects");

        $objects = $this->recognition->getBucketObjects($bucket);

        return view(
            'recognition::recognition.s3.front_index_s3_objects',
            compact(
                'page_title',
                'bucket',
                'objects'
            )
        );
    }

    public function getS3BucketUpload(Request $request, $bucket)
    {
        $page_title = trans("recognition::general/front_lang.titleS3ObjectsUpload");

        return view(
            'recognition::recognition.s3.front_index_s3_upload',
            compact(
                'page_title',
                'bucket'
            )
        );
    }

    public function getS3BucketUploadFile(Request $request)
    {
        $bucket = $request->input('bucket', '');
        $files = $request->file();
        if (!is_null($files)) {
            foreach ($files as $file) {
                $this->recognition->upload($bucket, $file, $file->getClientOriginalName());
            }
        }

        return redirect('/recognition/s3/'.$bucket);
    }


    public function getS3BucketDownload(Request $request, $bucket, $file)
    {
        $file = base64_decode($file);

        $this->recognition->download($bucket, $file);
    }

    public function getS3View(Request $request, $bucket, $file)
    {
        try {
            $file = base64_decode($file);

            $presignedUrl = $this->recognition->url($bucket, $file);


            return view(
                'recognition::recognition.s3.front_image',
                compact(
                    'presignedUrl'
                )
            );
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    public function getS3Delete(Request $request, $bucket, $file)
    {
        $file = base64_decode($file);

        $this->recognition->delete($bucket, $file);

        return redirect('/recognition/s3/'.$bucket);
    }


    public function getS3Process(Request $request, $bucket, $file)
    {
        $page_title = trans("recognition::general/front_lang.titleS3Objects");

        $file = base64_decode($file);

        $presignedUrl = $this->recognition->url($bucket, $file);



        $data = $this->recognition->isDni($bucket, $file);

        $labels = $data['labels'];
        $textos1 = $data['rekognition'];
        $textos2 = $data['texttract'];
        $dni = $data['dni'];

        return view(
            'recognition::recognition.s3.front_index_s3_process',
            compact(
                'page_title',
                'bucket',
                'file',
                'labels',
                'dni',
                'textos1',
                'textos2',
                'presignedUrl'
            )
        );
    }
}
