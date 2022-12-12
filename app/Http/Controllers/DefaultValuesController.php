<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\DefaultValues;

class DefaultValuesController extends Controller
{
    public function updateDefaultValues(Request $request) {
        try {
            DB::beginTransaction();

            DefaultValues::query()->where('id', '=', $request->id)->update([
                'labor_rate'=>$request->labor_rate,
                'storage_rate'=>$request->storage_rate,
                'parts_markup'=>$request->parts_markup,
                'interest_rate'=>$request->interest_rate,
                'sales_tax_rate'=>$request->sales_tax_rate,
                'paint_material_rate'=>$request->paint_material_rate,
                'owner_name'=>$request->owner_name,
                'company_name'=>$request->company_name,
                'address'=>$request->address,
                'city'=>$request->city,
                'state'=>$request->state,
                'zip'=>$request->zip,
                'phone1'=>$request->phone1,
                'phone2'=>$request->phone2,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated vendor information.']);
    }

    public function getDefaultValues() {
        return DefaultValues::all()->toArray();
    }
}