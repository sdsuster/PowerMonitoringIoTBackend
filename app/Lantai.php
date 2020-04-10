<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lantai extends Model
{
    protected $table = 'Lantai';
    //
    protected $fillable = [
        'nama', 'id_gedung', 'created_by', 'last_updated_by',
    ];
}
