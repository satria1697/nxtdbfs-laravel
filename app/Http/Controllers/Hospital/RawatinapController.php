<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Hospital\Rawatinap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class RawatinapController extends Controller
{
    /* base */
    private function basecolumn() {
        return $basecolumn=[
            'idpasien',
            'norm',
            'tglmasuk',
            'tglkeluar',
            'idkelas',
            'idbangsal',
            'idkamar',
            'iddokter',
            'jeniskasus',
            'tindakan',
            'caramasuk',
            'ketpulang',
            'carabayar',
        ];
    }

    private function validation($data) {
        $rules = [
            'idpasien' => 'required|numeric',
            'norm' => 'required',
            'tglmasuk' => 'required',
            'idkelas' => 'required|numeric',
            'idbangsal' => 'required|numeric',
            'idkamar' => 'required|numeric',
            'iddokter' => 'required|numeric',
            'caramasuk' => 'required|numeric',
            'ketpulang' => 'required|numeric',
            'carabayar' => 'required|numeric',
        ];
        $v = Validator::make($data, $rules);
        if ($v->fails()) {
            return [false, $v];
        }
        return [true, $v];
    }

    private function can() {
        $levelid = auth()->payload()->get('levelid');
        if ($levelid > 3) {
            return false;
        }
        return true;
    }

    /* create */
    public function store(Request $request)
    {
//        return $request;
        if (! $this->can()) {
            return Response::json([
                'error' => 'Tidak memiliki otorisasi',
            ], 403);
        }

        $value = $this->validation($request->all());
        $status = $value[0];
        $v = $value[1];
        if (! $status) {
            return Response::json([
                'status' => 'error',
                'error' => $v->errors(),
            ], 422);
        };

        $data = new Rawatinap();
        $basecolumn = $this->basecolumn();
        foreach ($basecolumn as $base) {
            $data->{$base} = $request->input($base);
        }

        try {
            $data->save();
        } catch (\Throwable $tr) {
            return Response::json([
                'status' => 'error',
                'errors' => $tr,
            ], 422);
        }

        return Response::json([
            'status' => 'success'
        ],200);
    }

    /* read */
    public function index(Request $request) {
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $query = Rawatinap::eloquentQuery($sortBy, $orderBy, $searchValue, [
            'kelas',
            'bangsal',
            'kamarranap',
            'dokter',
            'pasien',
        ]);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function show($id)
    {
        $query = Rawatinap::eloquentQuery('id', 'asc', '', [
            'kelas',
            'bangsal',
            'kamarranap',
            'dokter',
            'pasien',
        ]);
        $data = $query->where('nxt_hospital_rawatinap.id', '=', $id)->first();
        if (is_null($data)) {
            return Response::json([
                'error' => 'Data tidak ditemukan'
            ], 403);
        }
        return Response::json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }

    /* update */
    public function update(Request $request, $id)
    {
        if (! $this->can()) {
            return Response::json([
                'error' => 'Tidak memiliki otorisasi',
            ], 403);
        }

        if (! $this->validation($request->all())) {
            return Response::json([
                'status' => 'error',
            ], 422);
        }

        $data = Rawatinap::find($id);
        if (is_null($data)) {
            return Response::json([
                'error' => 'Data tidak ditemukan'
            ], 403);
        }

        $basecolumn = $this->basecolumn();
        try {
            foreach ($basecolumn as $base) {
                $data->update([
                    $base => $request->input($base)
                ]);
            }
            return Response::json([
                'status' => 'success'
            ], 200);
        } catch(\Throwable $tr) {
            return Response::json([
                'error' => 'error_update',
            ],304);
        }
    }

    /* delete */
    public function delete($id) {
        if (! $this->can()) {
            return Response::json([
                'error' => 'Tidak memiliki otorisasi',
            ], 403);
        }

        $data = Rawatinap::find($id);
        if (is_null($data)) {
            return Response::json([
                'error' => 'Data tidak ditemukan'
            ], 403);
        }

        try {
            $data->delete();
            return Response::json([
                'status' => 'success',
                'data' => 'Entry berhasil dihapus'
            ], 204);
        } catch (\Throwable $tr) {
            return Response::json([
                'error' => 'Entry gagal dihapus',
                'data' => $tr
            ], 304);
        }
    }

    /* custom */
}
