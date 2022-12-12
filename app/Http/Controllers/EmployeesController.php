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
use App\Models\Employees;
use App\Models\VehicleDetails;

class EmployeesController extends Controller
{
    public function addNewEmployee(Request $request) 
    {
        try {
            DB::beginTransaction();

            Employees::create([
                'first_name'=>$request->first_name,
                'last_name'=>$request->last_name,
                'address'=>$request->address,
                'city'=>$request->city,
                'state'=>$request->state,
                'zip'=>$request->zip,
                'phone'=>$request->phone,
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully created employee information.']);
    }

    public function updateGivenEmployee(Request $request) {
        try {
            DB::beginTransaction();

            Employees::query()->where('id', '=', $request->id)->update([
                'first_name'=>$request->first_name,
                'last_name'=>$request->last_name,
                'address'=>$request->address,
                'city'=>$request->city,
                'state'=>$request->state,
                'zip'=>$request->zip,
                'phone'=>$request->phone,
                'start_date'=>$request->start_date,
                'end_date'=>$request->end_date,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated employee information.']);
    }

    public function getAllEmployees() {
        return Employees::query()->select('*')->whereNull('end_date')->get()->toArray();
    }

    public function getCurrentAndOldEmployees() {
        return Employees::all()->toArray();
    }
}