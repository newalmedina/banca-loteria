<?php namespace Clavel\Elearning\Traits;

trait RealTranslatableTrait
{
    public function getTranslatedNombre()
    {
        if (empty($this->nombre)) {
            foreach ($this->getTranslationsArray() as $idioma) {
                if (!empty($idioma["nombre"])) {
                    return $idioma["nombre"];
                }
            }
        }
        return $this->nombre;
    }

    public function getTranslatedTitulo()
    {
        if (empty($this->titulo)) {
            foreach ($this->getTranslationsArray() as $idioma) {
                if (!empty($idioma["titulo"])) {
                    return $idioma["titulo"];
                }
            }
        }
        return $this->titulo;
    }

    public function getTranslatedURL()
    {
        if (empty($this->url_amigable)) {
            foreach ($this->getTranslationsArray() as $idioma) {
                if (!empty($idioma["url_amigable"])) {
                    return $idioma["url_amigable"];
                }
            }
        }
        return $this->url_amigable;
    }

    public function getTraduccionesReales()
    {
        $traducidos = [];
        foreach ($this->getTranslationsArray() as $langKey => $idioma) {
            if (!empty($idioma["titulo"]) || !empty($idioma["nombre"])) {
                $traducidos[] = $langKey;
            }
        }
        return \App\Models\Idioma::active()->whereIn("code", $traducidos)->get();
    }
}
