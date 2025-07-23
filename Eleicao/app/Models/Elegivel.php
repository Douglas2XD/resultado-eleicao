<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Elegivel extends Model
{
    protected $table = 'elegiveis';
    protected $fillable = [
        'nome',
        'idade',
        'eleicao_id'
    ];
}
