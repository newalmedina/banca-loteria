<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    protected $table = "certificados";
    protected $fillable = [];
    protected $guarded = [];

    public function paginasCertificado()
    {
        return $this->hasMany('Clavel\Elearning\Models\CertificadoPagina');
    }
}
