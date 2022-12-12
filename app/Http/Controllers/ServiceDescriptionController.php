<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\ServiceDescription;

class ServiceDescriptionController extends Controller
{
    public function addNewServiceDesc(Request $request) 
    {
        try {
            DB::beginTransaction();

            $serviceDesc = ServiceDescription::create([
                'service_name'=>$request->service_name,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return ['serviceDesc' => $serviceDesc];
    }

    public function updateGivenServiceDesc(Request $request) {
        try {
            DB::beginTransaction();

            ServiceDescription::query()->where('id', '=', $request->id)->update([
                'service_name'=>$request->service_name,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated service description information.']);
    }

    public function getAllServiceDesc() {
        return ServiceDescription::all()->toArray();
    }

    public function deleteService(Request $request) {
        try {
            DB::beginTransaction();

            ServiceDescription::query()->where('id', '=', $request->id)->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully deleted service.']);
    }

}