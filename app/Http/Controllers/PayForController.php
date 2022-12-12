<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\VendorPayFor;

class PayForController extends Controller
{
    public function addNewPayFor(Request $request) 
    {
        try {
            DB::beginTransaction();

            $pay_for = VendorPayFor::create([
                'pay_for'=>$request->pay_for,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return ['pay_for' => $pay_for];
    }

    public function getAllPayFor() {
        return VendorPayFor::all()->toArray();
    }

}