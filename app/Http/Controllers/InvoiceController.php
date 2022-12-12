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
use App\Models\Orders;
use App\Models\CustChargeInfo;
use App\Models\Vehicles;
use App\Models\Invoices;
use App\Models\Vendors;
use App\Models\JobDescriptions;
use App\Models\Employees;
use App\Models\VendorPayDetails;
use App\Models\PaymentTypes;

class InvoiceController extends Controller
{

    public function getAllVendorPayDetails() {
        $vendors = Vendors::all()->toArray();
        $vendorPayDetails = VendorPayDetails::query()->select("*")->get()->groupBy('vendor_id');

        return ['vendors'=>$vendors, 'vendorPayDetails' => $vendorPayDetails];
    }

    public function getAllInvoicesForGivenVendor(Request $request) {
        $invoices = Invoices::query()->select(["invoices.*", "service_description.service_name"])
        ->leftJoin("service_description", "invoices.service_desc_id", "=", "service_description.id")
        ->where('vendor_id', '=', $request->vendor_id)
        ->get()->toArray();

        $payDets = VendorPayDetails::query()->select('*')->get()->groupBy('invoice_id');
        
        return ['invoices'=>$invoices, 'payDets'=>$payDets];
    }

    public function getInvoiceAndVendorInfo(Request $request) {
        return Invoices::query()->select(['invoices.*', 'vendors.name', 'vendors.address', 'vendors.city', 'vendors.state', 'vendors.zip', 'vendors.fax', 'vendors.ss_fed','service_description.service_name'])
        ->leftJoin('vendors', 'invoices.vendor_id','=','vendors.id')
        ->leftJoin('service_description', 'invoices.service_desc_id','=','service_description.id')
        ->where('invoices.id', '=', $request->invoice_id)
        ->get()->toArray();
    }

    public function updateInfoForVendorInvoicePostOps(Request $request) {
        try {
            DB::beginTransaction();
            
            $invoiceData = Invoices::query()->select('*')->where('id','=',$request->invoice_id)->get()->toArray()[0];
            
            $defaultValues = DefaultValues::all()->toArray()[0];
            $order = Orders::query()->select('*')->where('id', '=', $invoiceData['order_id'])->get()->toArray()[0];
       
            $totalPaintMaterialCost = $invoiceData['is_paint_material'] ? $request->quantity * $defaultValues['paint_material_rate'] : 0;
            $subTotal = $invoiceData['sub_total'] + $totalPaintMaterialCost;
            $subTotalWithTax = ($subTotal - $invoiceData['total_labor_cost']) + (($subTotal - $invoiceData['total_labor_cost']) * $order['tax_rate']);

            $invoice = Invoices::query()->where('id', '=', $request->invoice_id)->update([
                'parts_num' => $request->parts_num,
                'quantity' => $request->quantity,
                'our_cost' => $request->our_cost,
                'total_paint_material_cost' => $totalPaintMaterialCost,
                'sub_total' => $subTotal,
                'sub_total_with_tax' => $subTotalWithTax,
            ]);

            $vendorDetails = VendorPayDetails::query()->select('*')->where('invoice_id', '=', $request->invoice_id)->get()->toArray()[0];

            $totalBill = $request->our_cost * $request->quantity;
            $balance = $totalBill - $vendorDetails['amount_paid'];

            VendorPayDetails::query()->where('invoice_id', '=', $request->invoice_id)->update([
                'total_bill' => $totalBill,
                'amount_paid' => $vendorDetails['amount_paid'],
                'balance' => $balance,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated an invoice.']);
    }
}
