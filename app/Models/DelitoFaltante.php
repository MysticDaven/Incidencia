<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DelitoFaltante extends Model
{
    protected $table = 'delitosFaltantes';
    protected $primaryKey = 'idDelito';
    public $timestamps = false;
}
