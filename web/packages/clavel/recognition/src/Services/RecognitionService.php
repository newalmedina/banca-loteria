<?php
namespace Clavel\Recognition\Services;

use Aws\S3\S3Client;
use Aws\Textract\TextractClient;
use Aws\Rekognition\RekognitionClient;

class RecognitionService
{
    private $AWS_KEY = '';
    private $AWS_SECRET_KEY = '';
    private $HOST = '';
    private $REGION = '';

    private $s3Client = null;

    public function __construct()
    {
        $method = "aws";

        $method = config('recognition.default', 'aws');
        if ($method == 'aws') {
            $this->AWS_KEY = config('recognition.s3.key', '');
            $this->AWS_SECRET_KEY = config('recognition.s3.secret', '');
            $this->HOST = config('recognition.s3.url', '');
            $this->REGION = config('recognition.s3.region', '');

            $this->s3Client = new S3Client([
                'version'     => 'latest',
                'region'      => $this->REGION,
                'endpoint'    => $this->HOST,
                    'credentials' => [
                    'key'      => $this->AWS_KEY,
                    'secret'   => $this->AWS_SECRET_KEY,
                ]
            ]);
        } else {
        }
    }

    public function getBuckets()
    {
        //Listing all S3 Bucket

        $buckets = [];
        try {
            $listResponse = $this->s3Client->listBuckets();
            $buckets = $listResponse['Buckets'];
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }

        return $buckets;
    }

    public function getBucketObjects($bucket)
    {
        $objects = [];
        try {
            $objectsListResponse = $this->s3Client->listObjects(['Bucket' => $bucket]);
            $objects = $objectsListResponse['Contents'] ?? [];
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }

        return  $objects;
    }

    public function upload($bucket, $file, $fileName)
    {
        $result = $this->s3Client->putObject([
            'Bucket' => $bucket,
            'Key'    =>  $fileName,
            'SourceFile' => $file
        ]);
    }

    public function delete($bucket, $file)
    {
        $result = $this->s3Client->deleteObject([
            'Bucket' => $bucket,
            'Key'    => $file

        ]);
    }

    public function download($bucket, $file)
    {
        $object = $this->s3Client->getObject(array(
            'Bucket' => $bucket,
            'Key'    => $file
         ));

        header('Content-Description: File Transfer');
        //this assumes content type is set when uploading the file.
        header('Content-Type: ' . $object['ContentType']);
        header('Content-Disposition: attachment; filename=' . $file);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        //send file to browser for download.
        echo $object["Body"];
    }

    public function url($bucket, $file)
    {
        $object = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $file,
            'ResponseContentDisposition' => 'attachment; filename="'.$file.'"'
        ]);


        $signedUrl = $this->s3Client->createPresignedRequest($object, "+6 days");

        // Create a signed URL from the command object that will last for
        // 6 days from the current time
        return (string)$signedUrl->getUri();
    }


    public function isDni($bucket, $file)
    {
        $labels = $this->getRekognitionProcessLabel($bucket, $file);

        $rekognition = $this->getRekognitionText($bucket, $file);

        $texttract = $this->getTextractText($bucket, $file);

        $dni = '';
        if ($rekognition['dni'] ==  $texttract['dni']) {
            $dni = $rekognition['dni'];
        } elseif (!empty($rekognition['dni'])) {
            $dni = $rekognition['dni'];
        } else {
            $dni = $texttract['dni'];
        }

        return [
            'dni' => $dni,
            'labels' => $labels,
            'rekognition' => $rekognition,
            'texttract' => $texttract,
        ];
    }


    public function getRekognitionProcessLabel($bucket, $file)
    {
        $client = new RekognitionClient([
            'version'     => 'latest',
            'region'      => $this->REGION,
                'credentials' => [
                'key'      => $this->AWS_KEY,
                'secret'   => $this->AWS_SECRET_KEY,
            ]
        ]);

        try {
            $result = $client->detectLabels([
                'Image' => [
                    'S3Object' => [
                        'Bucket' => $bucket,
                        'Name' => $file
                    ],
                ],
            ]);


            $textDetections = $result['Labels'];

            $confidence = 0;
            $confidenceCount = 0;
            foreach ($textDetections as $phrase) {
                if ($phrase['Name'] === "Driving License" ||
                $phrase['Name'] === "License" ||
                $phrase['Name'] === "Passport" ||
                $phrase['Name'] === "Id Cards"
                ) {
                    $confidence +=  $phrase['Confidence'];
                    $confidenceCount++;
                }
            }

            if ($confidenceCount > 0) {
                $confidence = ($confidence/$confidenceCount);
            }

            return [
                'textDetections' => $textDetections,
                'confidence' => $confidence,
                'confidenceCount' => $confidenceCount,
            ];
        } catch (\Exception $ex) {
            return [
                'textDetections' => [],
                'confidence' => 0,
                'confidenceCount' => 0
            ];
        }
    }


    public function getRekognitionText($bucket, $file)
    {
        try {
            $client = new RekognitionClient([
                'version'     => 'latest',
                'region'      => $this->REGION,
                    'credentials' => [
                    'key'      => $this->AWS_KEY,
                    'secret'   => $this->AWS_SECRET_KEY,
                ]
            ]);

            $result = $client->detectText([
                'Image' => [
                    'S3Object' => [
                        'Bucket' => $bucket,
                        'Name' => $file
                    ],
                ],
            ]);

            $textDetections = $result['TextDetections'];

            $dni = "";
            $textos = [];
            foreach ($textDetections as $phrase) {
                if ($phrase['Type'] === 'WORD') {
                    $textos[] = $phrase;
                    $v = $this->comprobarDocumentoIdentificacion($phrase['DetectedText']);
                    if (!empty($v)) {
                        $dni .= $v;
                    } else {
                        $v = $this->comprobarDocumentoIdentificacionParcial($phrase['DetectedText']);
                        if (!empty($v)) {
                            $dni .= $v;
                        }
                    }
                }
            }

            return [
                'dni' => $dni,
                'textos' => $textos
            ];
        } catch (\Exception $ex) {
            return [
                'dni' => '',
                'textos' => []
            ];
        }
    }


    public function getTextractText($bucket, $file)
    {
        try {
            $client = new TextractClient([
                'version'     => 'latest',
                'region'      => $this->REGION,
                    'credentials' => [
                    'key'      => $this->AWS_KEY,
                    'secret'   => $this->AWS_SECRET_KEY,
                ]
            ]);



            $result = $client->detectDocumentText([
                'Document' => [
                    'S3Object' => [
                        'Bucket' => $bucket,
                        'Name' => $file
                    ],
                ],
            ]);

            $textDetections = $result['Blocks'];

            $dni = "";
            $textos = [];
            foreach ($textDetections as $phrase) {
                if ($phrase['BlockType'] == 'WORD' && $phrase['Confidence'] >= 90) {
                    $textos[] = $phrase;
                    $v = $this->comprobarDocumentoIdentificacion($phrase['Text']);
                    if (!empty($v)) {
                        $dni .= $v;
                    } else {
                        $v = $this->comprobarDocumentoIdentificacionParcial($phrase['Text']);
                        if (!empty($v)) {
                            $dni .= $v;
                        }
                    }
                }
            }

            return [
                'dni' => $dni,
                'textos' => $textos
            ];
        } catch (\Exception $ex) {
            return [
                'dni' => '',
                'textos' => []
            ];
        }
    }

    public function comprobarDocumentoIdentificacion($dni)
    {
        if (strlen($dni)<9) {
            return '';
            //return "DNI demasiado corto.";
        }

        try {
            $dni = strtoupper($dni);

            $letra = substr($dni, -1, 1);
            $numero = substr($dni, 0, 8);

            // Si es un NIE hay que cambiar la primera letra por 0, 1 ó 2 dependiendo de si es X, Y o Z.
            $numero = str_replace(array('X', 'Y', 'Z'), array(0, 1, 2), $numero);

            $modulo = $numero % 23;
            $letras_validas = "TRWAGMYFPDXBNJZSQVHLCKE";
            $letra_correcta = substr($letras_validas, $modulo, 1);

            if ($letra_correcta!=$letra) {
                return '';
                //return "Letra incorrecta, la letra deber&iacute;a ser la $letra_correcta.";
            }

            return $dni;
            //return "OK";
        } catch (\Exception $ex) {
            return '';
        }
    }



    public function comprobarDocumentoIdentificacionParcial($dni)
    {
        if (strlen($dni<8)) {
            return '';
            //return "DNI demasiado corto.";
        }

        try {
            $dni = strtoupper($dni);

            $numero = substr($dni, 0, 8);

            // Si es un NIE hay que cambiar la primera letra por 0, 1 ó 2 dependiendo de si es X, Y o Z.
            $numero = str_replace(array('X', 'Y', 'Z'), array(0, 1, 2), $numero);

            $modulo = $numero % 23;
            $letras_validas = "TRWAGMYFPDXBNJZSQVHLCKE";
            $letra_correcta = substr($letras_validas, $modulo, 1);

            $nuevoDni = $numero.$letra_correcta;
            if (strlen($nuevoDni) != 9) {
                return '';
                //return "Letra incorrecta, la letra deber&iacute;a ser la $letra_correcta.";
            }

            return $nuevoDni;
            //return "OK";
        } catch (\Exception $ex) {
            return '';
        }
    }
}
