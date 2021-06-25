<?php

return [
    'basicos' => [
        "TITULO" => env("PROJECT_NAME"),
        "TIENE_COOKIES" => true,
        "TIENE_HOME" => true,
        'foro' => true
    ],
    'autentificacion' => [
        "TIPO_REGISTRO" => "1", // 1 => REGISTRO LIBRE, 2 => REQUIERE CÓDIGO
        "EMAIL_CONFIRMACION" => true // Se envía al usuario un email para que confirme su registro
    ],
    'cursos' => [
        'mostrar_asignaturas' => true, // Verifica si solo hay una asignatura y entra directa a ella. Si no muestra siempre el listado de asignaturas aunque tenga 1
        "ENTRAR_ASIGNATURA" => false, // SI SE ENTRA DIRECTAMENTE A CURSAR LA ASIGNATURA (EN EL CASO QUE ESTE ABIERTA), SI NO, PASA POR LA PANTALLA DE DESCRIPCIÓN (EN CASO CERRADA O ESTA VARIABLE A FALSE)
        "VALIDAR_DATOS_PROFILE" => true // SE ACTIVA LA OBLIGATORIEDAD DE REGISTRAR DETERMINADOS DEL USUARIO A LA HORA DE ACCEDER AL CURSO
    ],
    'contacto' => [
        "MOSTRAR_TELEFONO" => true, // Si la web muestra el teléfono de contracto
        "MOSTRAR_EMAIL" => true, // Si la web muestra el MAIL de contracto
        "TIENE_GMPAS" => true, // Si aparece el google maps en el formulario de contacto
        'GMAPS_URL' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2175.952415123695!2d2.0892146986910127!3d41.4683385803103!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xc21b0f08f3e2e2f1!2sESADECREAPOLIS!5e0!3m2!1ses!2ses!4v1476708658976' // URL del google maps que nos da google para localizar la dirección en el mapa
    ],
    'media' => [
        "upload_max_file_size" => env('MEDIA_UPLOAD_MAX_FILE_SIZE', 20), // Tamaño máximo en MB de los ficheros a subir en el apartado media
    ]

];
