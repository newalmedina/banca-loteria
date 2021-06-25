<?php


namespace App\Services;

class MailSpamService
{
    public static function hasForbiddenWords($message)
    {
        if (empty($message)) {
            return false;
        }

        // Deberiamos ver como mejoras la lista con ficheros json
        $lista_palabras = ["coronavirus", "http://meet-free.club"];

        // Si hacemos el explode nos pueden entrar por todas partes pero es una primera aproximacion
        $palabras = explode(' ', strtolower($message));
        $intersec = array_intersect($palabras, $lista_palabras);
        return (!empty($intersec));
    }
}
