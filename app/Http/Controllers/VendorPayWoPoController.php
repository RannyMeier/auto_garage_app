<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\VendorPayWoPo;
use App\Models\Vendors;

class VendorPayWoPoController extends Controller
{
    public function addNewWoPo(Request $request) 
    {
        try {
            DB::beginTransaction();

            VendorPayWoPo::create([
                'vendor_id'=>$request->vendor_id,
                'pay_for_id'=>$request->pay_for_id,
                'amount_paid'=>$request->amount_paid,
                'payment_date'=>Carbon::now(),
                'check_number'=>$request->check_number,
                'memo' => $request->memo,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully added Payment W/O PO information.']);
    }

    public function getAllWoPo() {
        return VendorPayWoPo::query()->select(['vendor_pay_wo_po.*', 'vendors.name', 'vendor_pay_for.pay_for'])
        ->leftJoin('vendors', 'vendors.id','=','vendor_pay_wo_po.vendor_id')
        ->leftJoin('vendor_pay_for', 'vendor_pay_for.id','=','vendor_pay_wo_po.pay_for_id')
        ->get()->toArray();
    }

    public function getWoPoForGivenId(Request $request) {
        return VendorPayWoPo::query()->select(['vendor_pay_wo_po.*', 'vendors.name', 'vendor_pay_for.pay_for'])
        ->leftJoin('vendors', 'vendors.id','=','vendor_pay_wo_po.vendor_id')
        ->leftJoin('vendor_pay_for', 'vendor_pay_for.id','=','vendor_pay_wo_po.pay_for_id')
        ->where('vendor_pay_wo_po.id', '=', $request->id)
        ->get()->toArray();
    }

    public function getWoPoForGivenVendor(Request $request) {
        return VendorPayWoPo::query()->select(['vendor_pay_wo_po.*', 'vendors.name', 'vendor_pay_for.pay_for'])
        ->leftJoin('vendors', 'vendors.id','=','vendor_pay_wo_po.vendor_id')
        ->leftJoin('vendor_pay_for', 'vendor_pay_for.id','=','vendor_pay_wo_po.pay_for_id')
        ->where('vendor_pay_wo_po.vendor_id', '=', $request->vendor_id)
        ->get()->toArray();
    }

    public function getPaymentDetailsWoPoByDateAndType(Request $request) {
        $from = $request->from. "00:00:00";
        $to = $request->to. "12:59:59";

        return VendorPayWoPo::query()
        ->select(['vendor_pay_wo_po.*', 'vendors.name', 'vendor_pay_for.pay_for'])
        ->leftJoin('vendors', 'vendors.id','=','vendor_pay_wo_po.vendor_id')
        ->leftJoin('vendor_pay_for', 'vendor_pay_for.id','=','vendor_pay_wo_po.pay_for_id')
        ->where("payment_date", ">=", $from)
        ->where("payment_date", "<=", $to)
        ->where("vendor_pay_wo_po.pay_for_id", '=', $request->pay_for_id)
        ->get()->toArray();
    }

    public function getPaymentDetailsWoPoByType(Request $request) {
        return VendorPayWoPo::query()
        ->select(['vendor_pay_wo_po.*', 'vendors.name', 'vendor_pay_for.pay_for'])
        ->leftJoin('vendors', 'vendors.id','=','vendor_pay_wo_po.vendor_id')
        ->leftJoin('vendor_pay_for', 'vendor_pay_for.id','=','vendor_pay_wo_po.pay_for_id')
        ->where("vendor_pay_wo_po.pay_for_id", '=', $request->pay_for_id)
        ->get()->toArray();
    }

    public function getAllVendorsWoPoDetails() {
        $vendorPayDetails = VendorPayWoPo::query()->select(['vendor_pay_wo_po.*', 'vendor_pay_for.pay_for'])
        ->leftJoin('vendor_pay_for', 'vendor_pay_for.id','=','vendor_pay_wo_po.pay_for_id')
        ->get()->groupBy('vendor_id');

        $vendors = VendorPayWoPo::query()->select(['vendor_pay_wo_po.vendor_id', 'vendors.name','vendors.phone'])
        ->leftJoin('vendors', 'vendors.id','=','vendor_pay_wo_po.vendor_id')
        ->distinct('vendor_pay_wo_po.vendor_id')->get();
        
        return ['vendors'=>$vendors,'vendorPayDetails' => $vendorPayDetails];
    }
}