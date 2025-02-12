<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delito extends Model
{
    protected $table = 'DELITOS';
    protected $primaryKey = 'IDDELITO';
    public $timestamps = false;
}
