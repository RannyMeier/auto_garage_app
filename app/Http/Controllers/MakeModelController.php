<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\MakeModel;

class MakeModelController extends Controller
{
    public function addNewMakeModel(Request $request) 
    {
        try {
            DB::beginTransaction();

            MakeModel::create([
                'model_name'=>$request->model_name,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully created model information.']);
    }

    public function updateGivenMakeModel(Request $request) {
        try {
            DB::beginTransaction();

            MakeModel::query()->where('id', '=', $request->id)->update([
                'model_name'=>$request->model_name,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated model information.']);
    }

    public function getAllMakeModel() {
        return MakeModel::all()->toArray();
    }
}