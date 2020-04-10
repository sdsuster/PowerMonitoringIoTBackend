<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    //
    protected $table = 'gedung';
    protected $fillable = [
        'nama', 'id_kampus', 'created_by', 'last_updated_by',
    ];
}
