<?php

namespace App\Http\Controllers;

use App\DetailPerangkat;
use Illuminate\Http\Request;
use App\Events\InjectPerangkat;
use App\RingkasanPerangkat;
use Carbon\Carbon;
use App\HourFormatting;
use App\Perangkat;

class DetailPerangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $carbon = Carbon::now();
        $detailPerangkat = new DetailPerangkat();
        $detailPerangkat->r_val = $request->r_val;
        $detailPerangkat->s_val = $request->s_val;
        $detailPerangkat->t_val = $request->t_val;

        $detailPerangkat->id_perangkat = $request->perangkat->id;
        
        $ringkasan = RingkasanPerangkat::findOrNew(HourFormatting::getFormatedHour($carbon), $request->perangkat->id);
        // return response($ringkasan);
        $ringkasan->addData($detailPerangkat);
        $ringkasan->save();
        $detailPerangkat->save();


        event(new InjectPerangkat($detailPerangkat, $request->hash_id));
        return response(Perangkat::all());
        // broadcast(new InjectPerangkat($detailPerangkat, 'fd90b4212c86dacf'))->toOthers();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DetailPerangkat  $detailPerangkat
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // if($request->jam)
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DetailPerangkat  $detailPerangkat
     * @return \Illuminate\Http\Response
     */
    public function edit(DetailPerangkat $detailPerangkat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DetailPerangkat  $detailPerangkat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DetailPerangkat $detailPerangkat)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DetailPerangkat  $detailPerangkat
     * @return \Illuminate\Http\Response
     */
    public function destroy(DetailPerangkat $detailPerangkat)
    {
        //
    }
}
