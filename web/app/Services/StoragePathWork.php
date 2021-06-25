<?php
/**
 * Created by PhpStorm.
 * User: toni
 * Date: 28/10/2015
 * Time: 10:03
 */

namespace App\Services;

use VideoStream;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class StoragePathWork
{
    protected $pathWork = "";
    protected $pathRoot = "";
    public $pathConnection = "local";

    public function __construct($pathWorking = "", $pathRoot = '')
    {
        $this->pathWork = $pathWorking;
        $this->pathRoot = ($pathRoot!='') ? $pathRoot : "app";

        if (!Storage::makeDirectory("/".$this->pathWork)) {
            Storage::createDir("/".$this->pathWork);
        }
    }

    public function readStorePath()
    {
        $folders=[];

        foreach (array_unique(Storage::allDirectories($this->pathWork)) as $subfolder) {
            $folders[]=preg_replace("/^{$this->pathWork}/", '', $subfolder);
        }

        return $folders;
    }

    public function createDir($directory)
    {
        if (Storage::makeDirectory("/".$this->pathWork.$directory)) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteDir($directory)
    {
        if (Storage::makeDirectory("/".$this->pathWork.$directory)) {
            Storage::deleteDirectory("/".$this->pathWork.$directory);
            return true;
        } else {
            return false;
        }
    }

    public function saveFile($file, $directory)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Uuid::uuid4() . '.' . $extension;

        Storage::disk('local')->put(
            "/".$this->pathWork . $directory ."/". $filename,
            File::get($file)
        );

        return $filename;
    }

    public function showFileVideo($filename, $directory)
    {
        $directory = preg_replace("/^\/{$this->pathWork}/", '', $directory);

        $video_path = "/" . $this->pathWork . $directory . "/" . $filename;
        if (Storage::disk('local')->exists($video_path)) {
            /*
            $file = Storage::disk('local')->get("/" . $this->pathWork . $directory . "/" . $filename);

            $mimetype = Storage::disk('local')->mimeType("/" . $this->pathWork . $directory . "/" . $filename);
            $size = Storage::disk('local')->size("/" . $this->pathWork . $directory . "/" . $filename);

            return (new \Illuminate\Http\Response($file, 200))
                  ->header('Content-Type', $mimetype)
                  ->header('Content-Length', $size)
                  ->header('Accept-Ranges', 'bytes');
            */

            $stream = new VideoStreamService($video_path);
            $stream->start();
        } else {
            return 0;
        }
    }

    public function showFile($filename, $directory)
    {
        $directory = preg_replace("/^\/{$this->pathWork}/", '', $directory);

        if (Storage::disk('local')->exists("/".$this->pathWork . $directory ."/". $filename)) {
            $file = Storage::disk('local')->get("/" . $this->pathWork . $directory . "/" . $filename);
            $mimetype = Storage::disk('local')->mimeType("/" . $this->pathWork . $directory . "/" . $filename);

            return (new Response($file, 200))
                ->header('Content-Type', $mimetype);
        } else {
            return 0;
        }
    }


    public function copyFile($file, $directory_original, $directory_to_copy)
    {
        if (Storage::disk($this->pathConnection)->exists("/".$this->pathWork . $directory_original ."/". $file)) {
            Storage::disk($this->pathConnection)
                ->copy(
                    "/" . $this->pathWork . $directory_original . "/" . $file,
                    "/" . $this->pathWork . $directory_to_copy . "/" . $file
                );
        }
    }

    public function showFileJson($filename, $directory)
    {
        if ($directory!='') {
            $directory = preg_replace("/^\/{$this->pathWork}/", '', $directory);
        }

        if (Storage::disk('local')->exists("/".$this->pathWork . $directory ."/". $filename)) {
            $file = Storage::disk('local')->get("/" . $this->pathWork . $directory . "/" . $filename);

            return json_decode($file);
        } else {
            return false;
        }
    }

    public function saveFileJson($filename, $directory, $array_save)
    {
        if ($directory!='') {
            $directory = preg_replace("/^\/{$this->pathWork}/", '', $directory);
        }

        Storage::disk('local')->put(
            "/".$this->pathWork . $directory ."/". $filename,
            json_encode($array_save)
        );
    }

    public function deleteFile($filename, $directory)
    {
        if ($directory!='') {
            $directory = preg_replace("/^\/{$this->pathWork}/", '', $directory);
        }

        if ($filename!='') {
            if (Storage::disk('local')->exists("/".$this->pathWork . $directory ."/". $filename)) {
                Storage::delete("/".$this->pathWork . $directory ."/". $filename);
            }
        }
    }

    public function getFile($filename, $directory)
    {
        $directory = preg_replace("/^\/{$this->pathWork}/", '', $directory);

        if (Storage::disk('local')->exists("/".$this->pathWork . $directory ."/". $filename)) {
            return storage_path("app")."/".$this->pathWork . $directory."/".$filename;
        } else {
            return false;
        }
    }
}
