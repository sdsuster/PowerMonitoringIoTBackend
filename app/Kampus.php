<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kampus extends Model
{
    //
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'kampus';
    protected $fillable = [
        'nama', 'alamat', 'created_by', 'last_updated_by',
    ];
}
