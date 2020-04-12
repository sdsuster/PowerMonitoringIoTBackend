<?php

namespace App\Http\Controllers;

use App\Lantai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Gedung;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User;

class LantaiController extends Controller
{
    public function getRawHeader($id)
    {
        return response(DB::select('SELECT id, L.nama AS "name" FROM lantai L WHERE id_gedung = ' . $id));
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
            $value->children = app('App\Http\Controllers\GedungController')->getRawHeader($value->id)->getOriginalContent();
        }
        return response(['data' => $kampus, 'header' => ['Kampus', 'Gedung']], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if ($request->id_gedung == null) {
            return response(DB::select('SELECT L.id, L.nama, U.name AS created_by, UU.name AS last_updated_by, L.created_at, L.updated_at FROM lantai L JOIN users U ON L.created_by = U.id JOIN users UU ON L.last_updated_by = UU.id'));
        }
        return response(DB::select('SELECT L.id, L.nama, U.name AS created_by, UU.name AS last_updated_by, L.created_at, L.updated_at FROM lantai L JOIN users U ON L.created_by = U.id JOIN users UU ON L.last_updated_by  = UU.id WHERE id_gedung = ' . $request->id_gedung));
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
        if ($request->id_gedung == null || Gedung::find($request->id_gedung) == null) {
            return response([
                'message' => 'Id kampus tidak ditemukan.',
            ], 406);
        }
        if ($request->nama == null) {
            return response([
                'message' => 'Nama belum terisi.',
            ], 406);
        }
        $data = new Lantai();
        $data->nama = $request->nama;
        $data->id_gedung = $request->id_gedung;
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
        $gedung = Lantai::find($id);
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
        $gedung = Lantai::find($id);
        if ($gedung == null) {
            return response([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }

        if (Gedung::find($request->id_gedung) == null) {
            return response([
                'message' => 'Gedung tidak valid.',
            ], 404);
        }
        $gedung->nama = $request->nama;
        $gedung->id_gedung = $request->id_gedung;
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
        $data = Lantai::where('id_gedung', $parentId)->get();
        foreach ($data as $lantai) {
            app('App\Http\Controllers\PerangkatController')->destroyByParrent($lantai->id);
        }
        Lantai::where('id_gedung', $parentId)->delete();
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
        $lantai = Lantai::find($id);
        if ($lantai == null) {
            return response([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }
        app('App\Http\Controllers\PerangkatController')->destroyByParrent($id);
        $lantai->delete();
        return response(
            [
                'message' => 'Data telah dihapus.',
                'data' => $lantai
            ],
            200
        );
    }
}
