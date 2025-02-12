<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faltante extends Model
{
    protected $table = 'faltantes';
    protected $primaryKey = 'idFaltante';
    public $timestamps = false;
}
