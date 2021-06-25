<?php


namespace Clavel\FileTransfer\Controllers;

// https://bmorton.com/posts/laravel-media-dropzone
// https://laraveldaily.com/multiple-file-upload-with-dropzone-js-and-laravel-medialibrary-package/
// https://styde.net/subir-archivos-en-laravel-con-dropzone/
// https://codepen.io/fuxy22/pen/pyYByO


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Clavel\FileTransfer\Models\Bundle;
use Clavel\FileTransfer\Models\BundleFile;
use Clavel\FileTransfer\Requests\SendRequest;
use Clavel\FileTransfer\Services\Upload;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FrontFileTransferUploadController extends Controller
{
    // The upload form
    public function create(Request $request)
    {
        // Generating unique ID for multiple uploads bundle
        $bundle_id  = substr(sha1(uniqid(null, true)), 0, rand(10, 20));

        $page_title = trans("file-transfer::front_lang.upload-files-title");

        return view(
            'file-transfer::file-transfer.front_upload',
            compact(
                'page_title',
                'bundle_id'
            )
        );
    }

    public function store(Request $request)
    {
        try {
            // Bundle ID must the sent among the request
            if (empty($request->header('X-Upload-Bundle'))) {
                throw new Exception('Invalid request');
            }
            if (! $request->hasFile('file')) {
                throw new Exception('No file was attached to the request');
            }
            if (! $request->file('file')->isValid()) {
                throw new Exception('Uploaded file is not valid');
            }

            // Validating file upload
            $this->validate($request, [
                'file'  => 'required|file|max:'.(Upload::fileMaxSize() / 1000)
            ]);

            $bundle = $request->header('X-Upload-Bundle');


            // Generating the file name
            $original   = $request->file->getClientOriginalName();
            $filename   = substr(sha1($original.time()), 0, rand(10, 20));
            // Getting the storage path
            $path = Upload::generateFilePath($filename);

            // Upload path
            //$destinationPath = storage_path('uploads'.DIRECTORY_SEPARATOR.$bundle.DIRECTORY_SEPARATOR.$path);

            /*
            // Create directory if not exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            */
            // Uploading file to given path
            $fullpath = $request->file('file')
                ->storeAs($path, $filename);

            // Generating file metadata
            $file = [
                'original'              => $original,
                'filesize'              => Storage::disk('local')->size($fullpath),
                'fullpath'              => $fullpath,
                'filename'              => $filename,
                'created_at'            => time()
            ];
            $request->session()->push($request->header('X-Upload-Bundle').'.files', $file);

            return response()->json([
                'result'    => true
            ]);
        } catch (Exception $e) {
            return response()->json([
                'result'    => false,
                'error'     => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        $filename =  $request->get('filename');

        // Upload path
        $destinationPath = storage_path('uploads');

        // Create directory if not exists
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $path=$destinationPath."/".$filename;
        if (file_exists($path)) {
            unlink($path);
        }

        return response()->json(['success'=>$filename]);
    }

    public function complete(Request $request)
    {
        // Bundle ID must be sent among headers
        abort_if(empty($request->header('X-Upload-Bundle')), 401);

        $bundleId = $request->header('X-Upload-Bundle');
        // Getting files from session
        if (! $bundle = $request->session()->get($bundleId)) {
            $bundle = [];
        }
        // Aborting if no file was uploaded
        abort_if(empty($bundle['files']) || count($bundle['files']) == 0, 500);
        // And clearing content from the session
        $request->session()->forget($bundleId);
        // Getting an existing metadata file if applicable
        if (! $metadata = Upload::getMetadata($bundleId)) {
            $metadata = [
                'created_at'    => time(),
                'expires_at'    => time()+60*60*24*15, # TODO : make this editable in the FRONT
                'bundle_id'     => $bundleId,
                'view-auth'     => substr(sha1(uniqid('', true)), 0, rand(6, 10)),
                'delete-auth'   => substr(sha1(uniqid('', true)), 0, rand(6, 10)),
                'fullsize'      => 0,
                'files'         => $bundle['files']
            ];
        } else {
            // The metadata file already exists
            // Adding bundle files to metadata
            $metadata['files'] = array_merge($metadata['files'], $bundle['files']);
        }
        // Processing size
        if (! empty($metadata['files'])) {
            $size = 0;
            foreach ($metadata['files'] as $f) {
                $size += $f['filesize'];
            }
            $metadata['fullsize'] = $size;
        }
        // Saving metadata

        // Creamos el bundle si no existe
        $bundle = Bundle::where('bundle_key', $bundleId)->first();
        if (empty($bundle)) {
            $bundle = new Bundle();
            $bundle->bundle_key = $bundleId;
        }
        $bundle->view_auth = $metadata['view-auth'];
        $bundle->delete_auth = $metadata['delete-auth'];
        $bundle->fullsize = $metadata['fullsize'];
        $bundle->expires_at = Carbon::now()->addDays(15);
        $bundle->save();

        // Borramos los anteriores ficheros
        BundleFile::where('bundle_id', $bundle->id)->delete();
        foreach ($metadata['files'] as $f) {
            $bundleFile = new BundleFile();
            $bundleFile->bundle_id = $bundle->id;
            $bundleFile->original = $f['original'];
            $bundleFile->filename = $f['filename'];
            $bundleFile->fullpath = $f['fullpath'];
            $bundleFile->filesize = $f['filesize'];
            $bundleFile->save();
        }

        try {
            Storage::disk('local')->put('bundles/'.$request->header('X-Upload-Bundle').'.json', json_encode($metadata));
            return response()->json([
                'result'            => true,
                'bundle_url'        => route('file-transfer.bundle.preview', [
                    'bundle'        => $request->header('X-Upload-Bundle'),
                    'auth'          => $metadata['view-auth']
                ]),
                'delete_url'        => route('file-transfer.bundle.delete', [
                    'bundle'        => $request->header('X-Upload-Bundle'),
                    'auth'          => $metadata['delete-auth']
                ]),
                'download_url'      => route('file-transfer.bundle.download', [
                    'bundle'        => $request->header('X-Upload-Bundle'),
                    'auth'          => $metadata['view-auth']
                ])
            ]);
        } catch (Exception $e) {
            return response()->json([
                'result'        => false,
                'error'         => $e->getMessage()
            ], 500);
        }
    }

    public function send(SendRequest $request)
    {
        $email_destino = $request->input('email_destino');
        $email = $request->input('email');
        $mensaje = $request->input('message');
        $bundle_key = $request->input('bundle_id');
        $bundle = Bundle::with('files')->where('bundle_key', $bundle_key)->first();

        if (empty($bundle)) {
            return redirect()->route('file-transfer.upload.create')
                ->with('error', __("file-transfer::front_lang.koform"));
        }
        // Enviamos el email al destinatario
        Mail::send(
            'file-transfer::file-transfer.email',
            compact('email_destino', 'email', 'mensaje', 'bundle'),
            function ($message) use ($email_destino, $email) {
                $message
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->to($email_destino, $email_destino)
                    ->subject(trans('file-transfer::front_lang.ficheros_enviados', ['email' => $email_destino]));
            }
        );


        // Enviamos una notificacion al propietario
        Mail::send(
            'file-transfer::file-transfer.email_notice',
            compact('email_destino', 'email', 'mensaje', 'bundle'),
            function ($message) use ($email_destino, $email) {
                $message
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->to($email, $email)
                    ->subject(trans('file-transfer::front_lang.ficheros_enviados', ['email' => $email_destino]));
            }
        );


        return redirect()->route('file-transfer.upload.create')
            ->with('success', __("file-transfer::front_lang.okform"));
    }
}
