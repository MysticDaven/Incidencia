<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'USUARIOS';
    protected $primaryKey = 'IDUSUARIO';
    public $timestamps = false;
}
