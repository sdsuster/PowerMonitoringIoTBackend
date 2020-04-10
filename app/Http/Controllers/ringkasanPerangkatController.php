<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RingkasanPerangkat;
use App\HourFormatting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Perangkat;
use Exception;

class ringkasanPerangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDayPower($id)
    {
        //
        // return DB::select('SELECT SUM(RERATA) FROM RINGKASANPERANGKAT WHERE JAM>=0 GROUP BY ID_PERANGKAT');
        $perangkat = Perangkat::findOrFail($id);
        $arr = RingkasanPerangkat::select('rerata')->where([['jam', '>=', HourFormatting::getFormatedDayHour(Carbon::now())], ['id_perangkat', '=', $id]])->orderBy('jam')->pluck('rerata')->toArray();
        
        $response = (object) [];
        $response->graphData = array_map(function ($val) {
            return $val * .24;
        }, $arr);
        $response->demandLimit = $perangkat->demand_limit;
        $response->summary = [];

        try{
            $buf = (object) [];
            $buf->title = 'Ringkasan Hari Ini';
            $buf->data = json_decode($this->getDaySummary($id));
            $response->summary[0] = $buf;

            
        }catch(Exception $e){
        }
        try{
            $buf = (object) [];
            $buf->title = 'Ringkasan Seminggu Terakhir';
            $buf->data = json_decode($this->getWeekSummary($id));
            $response->summary[1] = $buf;
        }catch(Exception $e){}

        try{
            $buf = (object) [];
            $buf->title = 'Ringkasan Sebulan Terakhir';
            $buf->data = json_decode($this->getMonthSummary($id));
            $response->summary[2] = $buf;
        }catch(Exception $e){}
        $response->name = $perangkat->nama;
        return response(json_encode($response));
    }

    public function getDaySummary($id)
    {
        $perangkat = Perangkat::findOrFail($id);
        $overDemand =  DB::select('SELECT ROUND(SUM(IF(rerata_R* .22 > ' . $perangkat->demand_limit . ', rerata_R, 0)) * .22, 3)  AS "R",
        ROUND(SUM(IF(rerata_S* .22 > ' . $perangkat->demand_limit . ', rerata_S, 0))* .22, 3) AS "S",
        ROUND(SUM(IF(rerata_T* .22 > ' . $perangkat->demand_limit . ', rerata_T, 0))* .22, 3) AS "T",
        ROUND((SUM(IF(rerata_R* .22 > ' . $perangkat->demand_limit . ', rerata_R, 0)) +
        SUM(IF(rerata_S* .22 > ' . $perangkat->demand_limit . ', rerata_S, 0)) +
        SUM(IF(rerata_T* .22 > ' . $perangkat->demand_limit . ', rerata_T, 0))) * .22, 3) AS "Total"
        FROM ringkasanperangkat WHERE jam >=' . HourFormatting::getFormatedDayHour(Carbon::now()) . ' AND id_perangkat = '.$id.' GROUP BY id_perangkat')[0];
        
        
        $underDemand =  DB::select('SELECT ROUND(SUM(IF(rerata_R* .22 > ' . $perangkat->demand_limit . ', rerata_R, 0)) * .22, 3)  AS "R",
        ROUND(SUM(IF(rerata_S* .22 <= ' . $perangkat->demand_limit . ', rerata_S, 0))* .22, 3) AS "S",
        ROUND(SUM(IF(rerata_T* .22 <= ' . $perangkat->demand_limit . ', rerata_T, 0))* .22, 3) AS "T",
        ROUND((SUM(IF(rerata_R* .22 <= ' . $perangkat->demand_limit . ', rerata_R, 0)) +
        SUM(IF(rerata_S* .22 <= ' . $perangkat->demand_limit . ', rerata_S, 0)) +
        SUM(IF(rerata_T* .22 <= ' . $perangkat->demand_limit . ', rerata_T, 0))) * .22, 3) AS "Total"
        FROM ringkasanperangkat WHERE jam >=' . HourFormatting::getFormatedDayHour(Carbon::now()) . ' AND id_perangkat = '.$id.' GROUP BY id_perangkat')[0];
        $efisiensi = (object) array();
        $efisiensi->R = round($underDemand->R - $overDemand->R, 3);
        $efisiensi->S = round($underDemand->S - $overDemand->S, 3);
        $efisiensi->T = round($underDemand->T - $overDemand->T, 3);
        $efisiensi->Total = round($underDemand->Total - $overDemand->Total, 3);
        $biaya = (object) array();
        $biaya->R = round(($underDemand->R + $overDemand->R)/0.95*1200, 0)/1000;
        $biaya->S = round(($underDemand->S + $overDemand->S)/0.95*1200, 0)/1000;
        $biaya->T = round(($underDemand->T + $overDemand->T)/0.95*1200, 0)/1000;
        $biaya->Total = round($biaya->R + $biaya->S + $biaya->T, 0);
        $daySummary = [];
        $daySummary[0] = ['title' => 'Over Demand Limit', 'data' => $overDemand];
        $daySummary[1] = ['title' => 'Under Demand Limit', 'data' => $underDemand];
        $daySummary[2] = ['title' => 'Efisiensi', 'data' => $efisiensi];
        $daySummary[3] = ['title' => 'Perkiraan Biaya (Rb)', 'data' => $biaya];
        return json_encode($daySummary);
        // return response(DB::raw('SELECT * '))
    }
    public function getWeekSummary($id)
    {
        $perangkat = Perangkat::findOrFail($id);
        $overDemand =  DB::select('SELECT ROUND(SUM(IF(rerata_R* .22 > ' . $perangkat->demand_limit . ', rerata_R, 0)) * .22, 3)  AS "R",
        ROUND(SUM(IF(rerata_S* .22 > ' . $perangkat->demand_limit . ', rerata_S, 0))* .22, 3) AS "S",
        ROUND(SUM(IF(rerata_T* .22 > ' . $perangkat->demand_limit . ', rerata_T, 0))* .22, 3) AS "T",
        ROUND((SUM(IF(rerata_R* .22 > ' . $perangkat->demand_limit . ', rerata_R, 0)) +
        SUM(IF(rerata_S* .22 > ' . $perangkat->demand_limit . ', rerata_S, 0)) +
        SUM(IF(rerata_T* .22 > ' . $perangkat->demand_limit . ', rerata_T, 0))) * .22, 3) AS "Total"
        FROM ringkasanperangkat WHERE jam >=' . HourFormatting::getFormatedWeekHour(Carbon::now()) . ' AND id_perangkat = '.$id.' GROUP BY id_perangkat')[0];
        
        $underDemand =  DB::select('SELECT ROUND(SUM(IF(rerata_R* .22 > ' . $perangkat->demand_limit . ', rerata_R, 0)) * .22, 3)  AS "R",
        ROUND(SUM(IF(rerata_S* .22 <= ' . $perangkat->demand_limit . ', rerata_S, 0))* .22, 3) AS "S",
        ROUND(SUM(IF(rerata_T* .22 <= ' . $perangkat->demand_limit . ', rerata_T, 0))* .22, 3) AS "T",
        ROUND((SUM(IF(rerata_R* .22 <= ' . $perangkat->demand_limit . ', rerata_R, 0)) +
        SUM(IF(rerata_S* .22 <= ' . $perangkat->demand_limit . ', rerata_S, 0)) +
        SUM(IF(rerata_T* .22 <= ' . $perangkat->demand_limit . ', rerata_T, 0))) * .22, 3) AS "Total"
        FROM ringkasanperangkat WHERE jam >=' . HourFormatting::getFormatedWeekHour(Carbon::now()) . ' AND id_perangkat = '.$id.' GROUP BY id_perangkat')[0];
        $efisiensi = (object) array();
        $efisiensi->R = round($underDemand->R - $overDemand->R, 3);
        $efisiensi->S = round($underDemand->S - $overDemand->S, 3);
        $efisiensi->T = round($underDemand->T - $overDemand->T, 3);
        $efisiensi->Total = round($underDemand->Total - $overDemand->Total, 3);
        $biaya = (object) array();
        $biaya->R = round(($underDemand->R + $overDemand->R)/0.95*1200, 0)/1000;
        $biaya->S = round(($underDemand->S + $overDemand->S)/0.95*1200, 0)/1000;
        $biaya->T = round(($underDemand->T + $overDemand->T)/0.95*1200, 0)/1000;
        $biaya->Total = round($biaya->R + $biaya->S + $biaya->T, 0);
        $daySummary = [];
        $daySummary[0] = ['title' => 'Over Demand Limit', 'data' => $overDemand];
        $daySummary[1] = ['title' => 'Under Demand Limit', 'data' => $underDemand];
        $daySummary[2] = ['title' => 'Efisiensi', 'data' => $efisiensi];
        $daySummary[3] = ['title' => 'Perkiraan Biaya (Rb)', 'data' => $biaya];
        return json_encode($daySummary);
        // return response(DB::raw('SELECT * '))
    }
    public function getMonthSummary($id)
    {
        $perangkat = Perangkat::findOrFail($id);
        $overDemand =  DB::select('SELECT ROUND(SUM(IF(rerata_R* .22 > ' . $perangkat->demand_limit . ', rerata_R, 0)) * .22, 3)  AS "R",
        ROUND(SUM(IF(rerata_S* .22 > ' . $perangkat->demand_limit . ', rerata_S, 0))* .22, 3) AS "S",
        ROUND(SUM(IF(rerata_T* .22 > ' . $perangkat->demand_limit . ', rerata_T, 0))* .22, 3) AS "T",
        ROUND((SUM(IF(rerata_R* .22 > ' . $perangkat->demand_limit . ', rerata_R, 0)) +
        SUM(IF(rerata_S* .22 > ' . $perangkat->demand_limit . ', rerata_S, 0)) +
        SUM(IF(rerata_T* .22 > ' . $perangkat->demand_limit . ', rerata_T, 0))) * .22, 3) AS "Total"
        FROM ringkasanperangkat WHERE jam >=' . HourFormatting::getFormatedMonthHour(Carbon::now()) . ' AND id_perangkat = '.$id.' GROUP BY id_perangkat')[0];
        $underDemand =  DB::select('SELECT ROUND(SUM(IF(rerata_R* .22 > ' . $perangkat->demand_limit . ', rerata_R, 0)) * .22, 3)  AS "R",
        ROUND(SUM(IF(rerata_S* .22 <= ' . $perangkat->demand_limit . ', rerata_S, 0))* .22, 3) AS "S",
        ROUND(SUM(IF(rerata_T* .22 <= ' . $perangkat->demand_limit . ', rerata_T, 0))* .22, 3) AS "T",
        ROUND((SUM(IF(rerata_R* .22 <= ' . $perangkat->demand_limit . ', rerata_R, 0)) +
        SUM(IF(rerata_S* .22 <= ' . $perangkat->demand_limit . ', rerata_S, 0)) +
        SUM(IF(rerata_T* .22 <= ' . $perangkat->demand_limit . ', rerata_T, 0))) * .22, 3) AS "Total"
        FROM ringkasanperangkat WHERE jam >=' . HourFormatting::getFormatedMonthHour(Carbon::now()) . ' AND id_perangkat = '.$id.' GROUP BY id_perangkat')[0];
        $efisiensi = (object) array();
        $efisiensi->R = round($underDemand->R - $overDemand->R, 3);
        $efisiensi->S = round($underDemand->S - $overDemand->S, 3);
        $efisiensi->T = round($underDemand->T - $overDemand->T, 3);
        $efisiensi->Total = round($underDemand->Total - $overDemand->Total, 3);
        $biaya = (object) array();
        $biaya->R = round(($underDemand->R + $overDemand->R)/0.95*1200, 0)/1000;
        $biaya->S = round(($underDemand->S + $overDemand->S)/0.95*1200, 0)/1000;
        $biaya->T = round(($underDemand->T + $overDemand->T)/0.95*1200, 0)/1000;
        $biaya->Total = round($biaya->R + $biaya->S + $biaya->T, 0);
        $daySummary = [];
        $daySummary[0] = ['title' => 'Over Demand Limit', 'data' => $overDemand];
        $daySummary[1] = ['title' => 'Under Demand Limit', 'data' => $underDemand];
        $daySummary[2] = ['title' => 'Efisiensi', 'data' => $efisiensi];
        $daySummary[3] = ['title' => 'Perkiraan Biaya (Rb)', 'data' => $biaya];
        return json_encode($daySummary);
        // return response(DB::raw('SELECT * '))
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
