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
use App\Models\VendorPayDetails;
use App\Models\Vendors;

class VendorsController extends Controller
{
    public function addNewVendors(Request $request) 
    {
        try {
            DB::beginTransaction();

            $vendor = Vendors::create([
                'name'=>$request->name,
                'phone'=>$request->phone,
                'address'=>$request->address,
                'city'=>$request->city,
                'state'=>$request->state,
                'zip'=>$request->zip,
                'fax'=>$request->fax,
                'ss_fed'=>$request->ss_fed,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return ['vendor' => $vendor];
    }

    public function updateGivenVendors(Request $request) {
        try {
            DB::beginTransaction();

            Vendors::query()->where('id', '=', $request->id)->update([
                'name'=>$request->name,
                'phone'=>$request->phone,
                'address'=>$request->address,
                'city'=>$request->city,
                'state'=>$request->state,
                'zip'=>$request->zip,
                'fax'=>$request->fax,
                'ss_fed'=>$request->ss_fed,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated vendor information.']);
    }

    public function getAllVendors() {
        return Vendors::all()->toArray();
    }

    public function getAllPaymentDetailsPerVendor(Request $request) {
        return VendorPayDetails::query()->select(['vendor_pay_details.*', 'service_description.service_name', 'vendor_pay_for.pay_for'])
        ->leftJoin('invoices', 'vendor_pay_details.invoice_id', '=', 'invoices.id')
        ->leftJoin('service_description', 'invoices.service_desc_id', '=', 'service_description.id')
        ->leftJoin('vendor_pay_for', 'vendor_pay_details.pay_for_id','=','vendor_pay_for.id')
        ->where('vendor_pay_details.vendor_id', '=', $request->vendor_id)
        ->whereNotNull('card_check_number')
        ->whereNotNull('payment_date')
        ->get()->toArray();
    }

    public function getAllInvoicesPerVendorNotPaid(Request $request) {
        return VendorPayDetails::query()->select(['vendor_pay_details.invoice_id'])
        ->where('vendor_pay_details.vendor_id', '=', $request->vendor_id)
        ->where('balance', '>', 0)
        // ->whereNull('card_check_number')
        // ->whereNull('payment_date')
        ->get()->toArray();
    }

    public function getPaymentDetailsForInvoice(Request $request) {
        return VendorPayDetails::query()->select(['vendor_pay_details.*'])
        ->where('vendor_pay_details.invoice_id', '=', $request->invoice_id)
        ->get()->toArray();
    }

    public function updateVendorPaymentDetails(Request $request) {
        try {
            DB::beginTransaction();
            
            VendorPayDetails::query()->where('invoice_id', '=', $request->invoice_id)->update([
                'card_check_number' => $request->card_check_number,
                'amount_paid' => $request->amount_paid,
                'balance' => $request->balance,
                'pay_for_id' => $request->pay_for_id,
                'memo' => $request->memo,
                'payment_date'=>Carbon::now()
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated an payment.']);
    }
}