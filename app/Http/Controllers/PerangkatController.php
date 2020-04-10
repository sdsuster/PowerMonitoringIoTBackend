<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Lantai;
use App\Perangkat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User;

class PerangkatController extends Controller
{
    public function getRawHeader($id)
    {
        return response(DB::select('SELECT id, P.nama AS "name" FROM perangkat P WHERE id_lantai = ' . $id));
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
            foreach ($value->children as $gedung) {
                $gedung->children = app('App\Http\Controllers\LantaiController')->getRawHeader($gedung->id)->getOriginalContent();
            }
        }
        return response(['data' => $kampus, 'header' => ['Kampus', 'Gedung', 'Lantai']], 200);
    }
    public function getTree()
    {
        $kampus = app('App\Http\Controllers\KampusController')->getHeader()->getOriginalContent();
        $perangkat = DB::select('SELECT id, P.nama AS "name", hash_id FROM perangkat P');
        foreach ($kampus as $value) {

            $value->children = app('App\Http\Controllers\GedungController')->getRawHeader($value->id)->getOriginalContent();
            $value->id .= 'K';
            foreach ($value->children as $gedung) {

                $gedung->children = app('App\Http\Controllers\LantaiController')->getRawHeader($gedung->id)->getOriginalContent();
                $gedung->id .= 'G';
                foreach ($gedung->children as $lantai) {

                    $lantai->children = $this->getRawHeader($lantai->id)->getOriginalContent();
                    $lantai->id .= 'L';
                }
            }
        }
        return response(['data' => $kampus, 'perangkat' => $perangkat], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        if ($request->id_lantai == null) {
            return response(DB::select('SELECT P.id, P.nama, U.name AS created_by, UU.name AS last_updated_by, hash_id, hash_pass,P.created_at, P.updated_at FROM perangkat P JOIN users U ON P.created_by = U.id JOIN users UU ON P.last_updated_by '));
        }
        return response(DB::select('SELECT P.id, P.nama, U.name AS created_by, UU.name AS last_updated_by, hash_id, hash_pass, P.created_at, P.updated_at FROM perangkat P JOIN users U ON P.created_by = U.id JOIN users UU ON P.last_updated_by WHERE id_lantai = ' . $request->id_lantai));
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
        if ($request->id_lantai == null || Lantai::find($request->id_lantai) == null) {
            return response([
                'message' => 'Id kampus tidak ditemukan.',
            ], 406);
        }
        if ($request->nama == null) {
            return response([
                'message' => 'Nama belum terisi.',
            ], 406);
        }
        $data = new Perangkat();
        $data->nama = $request->nama;
        $data->id_lantai = $request->id_lantai;
        $data->hash_id = $this->generateSecret('hash_id');
        $data->hash_pass = $this->generateSecret('hash_pass');
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
     * @param  \App\Perangkat  $kampus
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $gedung = Perangkat::find($id);
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
     * @param  \App\Perangkat  $kampus
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
        $perangkat = Perangkat::find($id);
        if ($perangkat == null) {
            return response([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }

        if (Lantai::find($request->id_lantai) == null) {
            return response([
                'message' => 'Lantai tidak valid.',
            ], 404);
        }
        $perangkat->nama = $request->nama;
        $perangkat->id_lantai = $request->id_lantai;
        $perangkat->last_updated_by = Auth::user()->id;
        $perangkat->save();
        $perangkat = $this->show($id)->getOriginalContent();
        return response(
            [
                'message' => 'Data telah diedit.',
                'data' => $perangkat
            ],
            200
        );
    }
    public function destroyByParrent($parentId)
    {
        Perangkat::where('id_lantai', $parentId)->delete();
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
        $perangkat = Perangkat::find($id);
        if ($perangkat == null) {
            return response([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }
        $perangkat->delete();
        return response(
            [
                'message' => 'Data telah dihapus.',
                'data' => $perangkat
            ],
            200
        );
    }
    public function generateHashPass(Request $req)
    {
        $perangkat = Perangkat::find($req->id);

        if ($perangkat == null) {
            return response([
                'message' => 'Data tidak ditemukan.',
            ], 404);
        }
        $perangkat->hash_pass = $this->generateSecret('hash_pass');
        $perangkat->last_updated_by = Auth::user()->id;
        $perangkat->save();
        $perangkat = $this->show($perangkat->id)->getOriginalContent();
        return response(
            [
                'message' => 'Hash Pass telah berubah.',
                'data' => $perangkat
            ],
            200
        );
    }
    public function generateSecret($field)
    {
        $ran = '';
        do {
            $ran =  bin2hex(random_bytes(8));
        } while (Perangkat::where($field, $ran)->first() != null);

        return $ran;
    }
}
