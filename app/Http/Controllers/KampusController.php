<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kampus;
use Illuminate\Support\Facades\Response;
use Auth;
use App\User;
use DB;

class KampusController extends Controller
{
    //

    public function getHeader()
    {
        return response(DB::select('SELECT id, K.nama AS "name" FROM kampus K'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response(DB::select('SELECT K.id, K.nama, K.alamat, U.name AS created_by, UU.name AS last_updated_by, K.created_at, K.updated_at FROM kampus K JOIN users U ON K.created_by = U.id JOIN users UU ON K.last_updated_by = UU.id '));
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
        if ($request->nama == null || $request->alamat == null) {
            return response([
                'message' => 'Nama atau Alamat belum terisi.',
            ], 406);
        }
        $data = new Kampus();
        $data->nama = $request->nama;
        $data->alamat = $request->alamat;
        $data->created_by = Auth::user()->id;
        $data->last_updated_by = Auth::user()->id;
        $data->save();
        $data->created_by = Auth::user()->name;
        $data->last_updated_by = Auth::user()->name;
        return response(
            [
                'message' => 'Sukses menambah data.',
                'data' => $data
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kampus  $kampus
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $kampus = Kampus::find($id);
        if ($kampus == null) {
            return response([
                'message' => 'Data tidak ditemukan.' . $id,
            ], 404);
        }
        $user = User::find($kampus->created_by);
        $user2 = User::find($kampus->last_updated_by);
        $kampus->created_by = $user->name;
        $kampus->last_updated_by = $user2->name;
        return response($kampus, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kampus  $kampus
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
     * @param  \App\Kampus  $kampus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $kampus = Kampus::find($id);
        if ($kampus == null) {
            return response([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }
        $kampus->nama = $request->nama;
        $kampus->alamat = $request->alamat;
        $kampus->last_updated_by = Auth::user()->id;
        $kampus->save();
        $kampus = $this->show($id)->getOriginalContent();
        return response(
            [
                'message' => 'Data telah diedit.',
                'data' => $kampus
            ],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kampus  $kampus
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $kampus = Kampus::find($id);
        if ($kampus == null) {
            return response([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }
        app('App\Http\Controllers\GedungController')->destroyByParrent($id);
        $kampus->delete();
        return response(
            [
                'message' => 'Data telah dihapus.',
                'data' => $kampus
            ],
            200
        );
    }
}
