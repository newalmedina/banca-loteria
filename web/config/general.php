<?php

return [
    'app_version' => '0.2.0',
    'only_backoffice' => true,
    'multilanguage' => true,
    'home_if_authenticated' => '/',
    'media' => [
        "upload_max_file_size" => env('MEDIA_UPLOAD_MAX_FILE_SIZE', 200), // Tamaño máximo en MB de los ficheros a subir en el apartado media
    ],
    'GMAPS_URL' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2175.952415123695!2d2.0892146986910127!3d41.4683385803103!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xc21b0f08f3e2e2f1!2sESADECREAPOLIS!5e0!3m2!1ses!2ses!4v1476708658976' // URL del google maps que nos da google para localizar la dirección en el mapa

];
