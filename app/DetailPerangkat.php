<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPerangkat extends Model
{
    //
    protected $table = 'detail_perangkat';
    protected $fillable = ['r_val, s_val, t_val, id_perangkat'];
}
