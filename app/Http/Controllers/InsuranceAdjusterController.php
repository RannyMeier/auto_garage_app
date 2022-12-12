<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\InsuranceAdj;
use App\Models\Adjuster;

class InsuranceAdjusterController extends Controller
{
    public function addNewInsurance(Request $request) 
    {
        try {
            DB::beginTransaction();

            $insAdj = InsuranceAdj::create([
                'company_name'=>$request->company_name,
                'fax'=>$request->fax,
                'address'=>$request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
    
        return ['insAdj' => $insAdj];
    }

    public function addNewAdjuster(Request $request) {
        try {
            DB::beginTransaction();

           $adjDets = Adjuster::create([
                'ins_id' => $request->ins_id,
                'adjuster_name'=>$request->adjuster_name,
                'phone'=>$request->phone,
                'fax' => $request->fax,
                'car' => $request->car
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }

        $newDetails = Adjuster::query()->select('adjuster.*', 'insurance_adjuster.company_name')
        ->leftJoin('insurance_adjuster', 'insurance_adjuster.id', '=', 'adjuster.ins_id')
        ->where('adjuster.id', '=', $adjDets->id)
        ->get()->toArray();
    
        return ['adjuster' => $newDetails];
    }
    public function updateGivenInsurance(Request $request) {
        try {
            DB::beginTransaction();

            InsuranceAdj::query()->where('id', '=', $request->id)->update([
                'company_name'=>$request->company_name,
                'fax'=>$request->fax,
                'address'=>$request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated insurance information.']);
    }

    public function updateAdjuster(Request $request) {
        try {
            DB::beginTransaction();

            $adjDets = Adjuster::query()->where('id', '=', $request->id)->update([
                'ins_id' => $request->ins_id,
                'adjuster_name'=>$request->adjuster_name,
                'phone'=>$request->phone,
                'fax' => $request->fax,
                'car' => $request->car
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated adjuster information.']);
    }

    public function getAllInsAdj() {
        return Adjuster::query()->select('adjuster.*', 'insurance_adjuster.company_name')
        ->leftJoin('insurance_adjuster', 'insurance_adjuster.id', '=', 'adjuster.ins_id')
        ->get()->toArray();
    }

    public function getAllInsurers() {
        return InsuranceAdj::all()->toArray();
    }

    public function getAdjustersForGivenInsurance(Request $request) {
        return Adjuster::query()->select('adjuster.*')
        ->where('ins_id', '=', $request->ins_id)
        ->get()->toArray();
    }

    public function deleteInsurer(Request $request) {
        try {
            DB::beginTransaction();

            InsuranceAdj::query()->where('id', '=', $request->id)->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully deleted insurer.']);
    }

    public function deleteAdjuster(Request $request) {
        try {
            DB::beginTransaction();

            Adjuster::query()->where('id', '=', $request->id)->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully deleted Adjuster.']);
    }
}