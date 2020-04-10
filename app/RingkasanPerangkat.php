<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use stdClass;

class RingkasanPerangkat extends Model
{
    //
    protected $table = 'RingkasanPerangkat';
    protected $fillable = ['jam', 'id_perangkat', 'n', 'rerata', 'rerata_r', 'rerata_s', 'rerata_t'];
    public $timestamps = false;
    function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->n = 0;
        $this->rerata = 0;
    }
    public static function findOrNew($waktu, $idPerangkat)
    {
        $data = parent::where([['jam', '=', $waktu], ['id_perangkat', '=',  $idPerangkat]])->first();
        if ($data == null) {
            $res = DB::select('SELECT MAX(jam) AS jam FROM ringkasanPerangkat WHERE id_Perangkat =' . $idPerangkat . ' GROUP BY id_perangkat');
            $latest = new stdClass();
            if(count($res) == 0){
                $latest->jam = 0;
            }else{
                $latest = $res[0];
            }
            
            for (; $latest->jam < $waktu; $latest->jam++) {
                (new RingkasanPerangkat(['jam' => $latest->jam, 'id_perangkat' => $idPerangkat]))->save();
            }
            return new RingkasanPerangkat(['jam' => $waktu, 'id_perangkat' => $idPerangkat]);
        } else {
            return $data;
        }
    }

    public  function addData($data)
    {
        $rerataRbaru = ($this->rerata_r * $this->n + $data->r_val) / ($this->n + 1);
        $rerataSbaru = ($this->rerata_s * $this->n + $data->s_val) / ($this->n + 1);
        $rerataTbaru = ($this->rerata_t * $this->n + $data->t_val) / ($this->n + 1);
        $rerataTotal = ($rerataRbaru + $rerataTbaru + $rerataSbaru);
        $this->n++;
        $this->rerata_r = $rerataRbaru;
        $this->rerata_s = $rerataSbaru;
        $this->rerata_t = $rerataTbaru;
        $this->rerata = $rerataTotal;
    }
}
