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
use App\Models\CustomerDetails;
use App\Models\VehicleDetails;

class VehicleDetailsController extends Controller
{
    public function addNewVehicle(Request $request) 
    {
        try {
            DB::beginTransaction();

            $vehicleInfo = VehicleDetails::create([
                'cust_id'=>$request->cust_id,
                'license'=>$request->license,
                'state'=>$request->state,
                'make_model_id'=>$request->make_model_id,
                'year'=>$request->year,
                'paint_color'=>$request->paint_color,
                'paint_number'=>$request->paint_number,
                'vehicle_num'=>$request->vehicle_num,
                'manufacture_date'=>$request->manufacture_date,
                'miles'=>$request->miles
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return ['vehicle' => $vehicleInfo];
    }

    public function updateGivenVehicle(Request $request) {
        try {
            DB::beginTransaction();

            VehicleDetails::query()->where('id', '=', $request->id)->update([
                'cust_id'=>$request->cust_id,
                'license'=>$request->license,
                'state'=>$request->state,
                'make_model_id'=>$request->make_model_id,
                'year'=>$request->year,
                'paint_color'=>$request->paint_color,
                'paint_number'=>$request->paint_number,
                'vehicle_num'=>$request->vehicle_num,
                'manufacture_date'=>$request->manufacture_date,
                'miles'=>$request->miles
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated vehicle information.']);
    }

    public function getAllVehicles() {
        return VehicleDetails::query()->select(['vehicle_details.*', 'customer_details.first_name', 'customer_details.last_name', 'customer_details.phone1', 'make_model.model_name'])
        ->leftJoin("customer_details", "vehicle_details.cust_id", '=','customer_details.id')
        ->leftJoin('make_model', 'vehicle_details.make_model_id', '=', 'make_model.id')
        ->get()->toArray();
    }

    public function getAllVehiclesForGivenCustomer(Request $request) {
        return VehicleDetails::query()->select(['vehicle_details.*', 'make_model.model_name'])
        ->leftJoin('make_model', 'vehicle_details.make_model_id', '=', 'make_model.id')
        ->where("vehicle_details.cust_id", '=', $request->cust_id)
        ->get()->toArray();
    }

    public function getAllModelNames() {
        return MakeModel::all()->toArray();
    }

    public function deleteVehicle(Request $request) {
        try {
            DB::beginTransaction();

            VehicleDetails::query()->where('id', '=', $request->id)->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully deleted vehicle.']);
    }
}