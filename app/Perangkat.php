<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perangkat extends Model
{
    //
    protected $table = 'perangkat';
    protected $fillable = [
        'nama', 'id_lantai', 'created_by', 'last_updated_by', 'hash_id', 'hash_pass'
    ];
}
