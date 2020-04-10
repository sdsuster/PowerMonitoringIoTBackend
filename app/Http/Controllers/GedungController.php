<?php

namespace App\Http\Controllers;

use App\Gedung;

use Illuminate\Http\Request;
use Auth;
use App\Kampus;
use Illuminate\Support\Facades\DB;
use App\User;

class GedungController extends Controller
{

    public function getRawHeader($id)
    {
        return response(DB::select('SELECT id, G.nama AS "name" FROM gedung G WHERE id_kampus = ' . $id));
    }
    /**
     * Display a listing of the resource header.
     *
     * @return \Illuminate\Http\Response
     */
    public function getHeader()
    {
        $kampus = app('App\Http\Controllers\KampusController')->getHeader()->getOriginalContent();
        foreach ($kampus as $value) {
            $value->data = $this->getRawHeader($value->id)->getOriginalContent();
        }
        // return response(['data' => $kampus, 'header' => ['Kampus', 'Gedung']], 200);
        return response(['data' => app('App\Http\Controllers\KampusController')->getHeader()->getOriginalContent(), 'header' => ['Kampus']], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if ($request->id_kampus == null) {
            return response(DB::select('SELECT G.id, G.nama, U.name AS created_by, UU.name AS last_updated_by, G.created_at, G.updated_at FROM gedung G JOIN users U ON G.created_by = U.id JOIN users UU ON G.last_updated_by '));
        }
        return response(DB::select('SELECT G.id, G.nama, U.name AS created_by, UU.name AS last_updated_by, G.created_at, G.updated_at FROM gedung G JOIN users U ON G.created_by = U.id JOIN users UU ON G.last_updated_by WHERE id_kampus = ' . $request->id_kampus));
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
        if ($request->id_kampus == null || Kampus::find($request->id_kampus) == null) {
            return response([
                'message' => 'Id kampus tidak ditemukan.',
            ], 406);
        }
        if ($request->nama == null) {
            return response([
                'message' => 'Nama belum terisi.',
            ], 406);
        }
        $data = new Gedung();
        $data->nama = $request->nama;
        $data->id_kampus = $request->id_kampus;
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
     * @param  \App\Gedung  $kampus
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $gedung = Gedung::find($id);
        if ($gedung == null) {
            return response([
                'message' => 'Data tidak ditemukan.' . $id,
            ], 404);
        }
        $user = User::find($gedung->created_by);
        $user2 = User::find($gedung->last_updated_by);
        $gedung->created_by = $user->name;
        $gedung->last_updated_by = $user2->name;
        return response($gedung, 200);
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
        $gedung = Gedung::find($id);
        if ($gedung == null) {
            return response([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }

        if (Kampus::find($request->id_kampus) == null) {
            return response([
                'message' => 'Kampus tidak valid.',
            ], 404);
        }
        $gedung->nama = $request->nama;
        $gedung->id_kampus = $request->id_kampus;
        $gedung->last_updated_by = Auth::user()->id;
        $gedung->save();
        $gedung = $this->show($id)->getOriginalContent();
        return response(
            [
                'message' => 'Data telah diedit.',
                'data' => $gedung
            ],
            200
        );
    }
    public function destroyByParrent($parentId)
    {
        $data = Gedung::where('id_kampus', $parentId)->get();
        foreach ($data as $gedung) {
            app('App\Http\Controllers\LantaiController')->destroyByParrent($gedung->id);
        }
        Gedung::where('id_kampus', $parentId)->delete();
        return response(200);
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
        $gedung = Gedung::find($id);
        if ($gedung == null) {
            return response([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }
        app('App\Http\Controllers\LantaiController')->destroyByParrent($id);
        $gedung->delete();
        return response(
            [
                'message' => 'Data telah dihapus.',
                'data' => $gedung
            ],
            200
        );
    }
}
