<?php

namespace App\Http\Controllers\Siska;

use App\Http\Controllers\Controller;
use App\Models\Admin\AksesUser;
use App\Models\Siska\AnalisisFormulir;
use App\Models\Siska\Formulir;
use App\Models\Siska\FormulirData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use function PHPUnit\Framework\isNull;

class FormulirController extends Controller
{
    /* base */
    private function basecolumn() {
        return $basecolumn=[
            'description',
            'required',
        ];
    }

    private function validation($data) {
        $rules = [
            'description' => 'required',
            'required' => 'required',
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

        $data = new Formulir();
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
        $query = Formulir::eloquentQuery($sortBy, $orderBy, $searchValue, [
            'formulirdata'
        ]);
        $data = $query->paginate($length);
        return new DataTableCollectionResource($data);
    }

    public function show($id)
    {
//        $data = Formulir::find($id);
        $query = Formulir::eloquentQuery('id', 'asc', '', [
            'formulirdata'
        ]);
        $data = $query->where('nxt_siska_formulir.id', '=', $id)->first();
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

        $data = Formulir::find($id);
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

        $data = Formulir::find($id);
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
    public function filterdata(Request $request) {

        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
//        return $request;
        $formulirid = json_decode($request->input('formulirid'), true);
        $data = [];
        foreach ($formulirid as $fid) {
            $query = Formulir::eloquentQuery($sortBy, $orderBy, $searchValue, [
                'formulirdata'
            ]);
            array_push($data, $query->where('nxt_siska_formulir.id', '=', $fid)->first());
//            $obj = (object)
//            $dataFd = FormulirData::eloquentQuery($sortBy, $orderBy, $searchValue)
//                ->where('formulirid', '=', $fid)
//                ->get();
//            $dataF = Formulir::where('id', '=', $fid)->first();
//            array_push($data, $dataFd);
        }
        return Response::json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
