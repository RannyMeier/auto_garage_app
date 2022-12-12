<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Invoices;
use App\Models\Orders;
use App\Models\DefaultValues;
use App\Models\PaymentDetails;
use App\Models\VendorPayDetails;
use App\Models\PaymentTypes;

class OrdersController extends Controller
{
    public function addNewOrder(Request $request) {
        try {
            $defaultValues = DefaultValues::all()->toArray()[0];

            $taxRate = $request->tax_rate == "" ? $defaultValues['sales_tax_rate'] : $request->tax_rate;
            $laborRate = $request->labor_rate == "" ? $defaultValues['labor_rate'] : $request->labor_rate;
            $storageRate = $request->storage_rate == "" ? $defaultValues['storage_rate'] : $request->storage_rate;
            $partsMarkup = $request->parts_markup == "" ? $defaultValues['parts_markup'] : $request->parts_markup;
            $storageCost = $request->storage_rate == "" ? ($defaultValues['storage_rate'] * $request->storage_days) : $request->storage_cost;

            $dateCreated = $request->date_created == "" ? Carbon::now() : $request->date_created;
            
            DB::beginTransaction();
            
            $order = Orders::create([
                'cust_id' => $request->cust_id,
                'vehicle_id' => $request->vehicle_id,
                'ins_adj_id' => $request->ins_adj_id,
                'tax_rate' => $taxRate,
                'labor_rate' => $laborRate,
                'towing' => $request->towing,
                'waste' => $request->waste,
                'storage_days' => $request->storage_days,
                'storage_rate' => $storageRate,
                'storage_cost' => $storageCost,
                'parts_markup' => $partsMarkup,
                'discount' => $request->discount,
                'delivery_date' => $request->delivery_date,
                'date_created' => $request->date_created,
            ]);

            $totalPaintMaterialCost = $request->is_paint_material ? $request->quantity * $defaultValues['paint_material_rate'] : 0;
            $totalLaborCost = $request->labor_hours * $laborRate;
            $subTotal = $request->sub_total + $totalPaintMaterialCost;
            $subTotalWithTax = ($subTotal - $totalLaborCost) + (($subTotal - $totalLaborCost)*$taxRate);
            $custCost = ($request->our_cost * $request->quantity) + ($request->our_cost * $request->quantity * $partsMarkup);

            $invoices = Invoices::create([
                'order_id' => $order->id,
                'vendor_id' => $request->vendor_id,
                'employee_id' => $request->employee_id,
                'service_desc_id' => $request->service_desc_id,
                'is_sublet' => $request->is_sublet,
                'sublet_amount' => $request->sublet_amount,
                'parts_num' => $request->parts_num,
                'quantity' => $request->quantity,
                'our_cost' => $request->our_cost,
                'cust_cost' => $custCost,
                'est_cost' => $request->est_cost,
                'is_paint_material' => $request->is_paint_material,
                'total_paint_material_cost' => $totalPaintMaterialCost,
                'labor_hours' => $request->labor_hours,
                'total_labor_cost' => $totalLaborCost,
                'estimated_labor' => $request->estimated_labor,
                'sub_total' => $subTotal,
                'sub_total_with_tax' => $subTotalWithTax,
                'date_created' => $request->date_created,
            ]);

            $totalBill = $invoices->our_cost * $invoices->quantity;

            VendorPayDetails::create([
                'vendor_id' => $request->vendor_id,
                'invoice_id' => $invoices->id,
                'payment_type' => null,
                'total_bill' => $totalBill,
                'amount_paid' => 0,
                'balance' => $totalBill,
                'payment_date' => null,
                'card_check_number' => null,
                'memo' => null,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully created an order.']);
    }

    public function updateOrder(Request $request) {
        try {
            $defaultValues = DefaultValues::all()->toArray()[0];

            $taxRate = $request->tax_rate == "" ? $defaultValues['sales_tax_rate'] : $request->tax_rate;
            $laborRate = $request->labor_rate == "" ? $defaultValues['labor_rate'] : $request->labor_rate;
            $storageRate = $request->storage_rate == "" ? $defaultValues['storage_rate'] : $request->storage_rate;
            $partsMarkup = $request->parts_markup == "" ? $defaultValues['parts_markup'] : $request->parts_markup;
            $storageCost = $request->storage_rate == "" ? ($defaultValues['storage_rate'] * $request->storage_days) : $request->storage_cost;

            DB::beginTransaction();
            
            $order = Orders::query()->where('id', '=', $request->id)->update([
                'cust_id' => $request->cust_id,
                'vehicle_id' => $request->vehicle_id,
                'ins_adj_id' => $request->ins_adj_id,
                'tax_rate' => $taxRate,
                'labor_rate' => $laborRate,
                'towing' => $request->towing,
                'waste' => $request->waste,
                'storage_days' => $request->storage_days,
                'storage_rate' => $storageRate,
                'storage_cost' => $storageCost,
                'parts_markup' => $partsMarkup,
                'discount' => $request->discount,
                'delivery_date' => $request->delivery_date,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated an order.']);
    }

    public function updateInvoice(Request $request) {
        $defaultValues = DefaultValues::all()->toArray()[0];
        $order = Orders::query()->select('*')->where('id', '=', $request->order_id)->get()->toArray()[0];
       
        try {
            DB::beginTransaction();
            
            $totalPaintMaterialCost =$request->is_paint_material ? $request->quantity * $defaultValues['paint_material_rate'] : 0;
            $totalLaborCost = $request->labor_hours * $order['labor_rate'];
            $subTotal = $request->sub_total + $totalPaintMaterialCost;
            $subTotalWithTax = ($subTotal - $totalLaborCost) + (($subTotal - $totalLaborCost) * $order['tax_rate']);

            $invoice = Invoices::query()->where('id', '=', $request->id)->update([
                'vendor_id' => $request->vendor_id,
                'employee_id' => $request->employee_id,
                'service_desc_id' => $request->service_desc_id,
                'is_sublet' => $request->is_sublet,
                'sublet_amount' => $request->sublet_amount,
                'parts_num' => $request->parts_num,
                'quantity' => $request->quantity,
                'our_cost' => $request->our_cost,
                'cust_cost' => $request->cust_cost,
                'est_cost' => $request->est_cost,
                'is_paint_material' => $request->is_paint_material,
                'total_paint_material_cost' => $totalPaintMaterialCost,
                'labor_hours' => $request->labor_hours,
                'total_labor_cost' => $totalLaborCost,
                'estimated_labor' => $request->estimated_labor,
                'sub_total' => $subTotal,
                'sub_total_with_tax' => $subTotalWithTax,
                'date_created' => $request->date_created,
            ]);

            $vendorDetails = VendorPayDetails::query()->select('*')->where('invoice_id', '=', $request->id)->get()->toArray()[0];

            $totalBill = $invoice->our_cost * $invoice->quantity;
            $balance = $totalBill - $vendorDetails['amount_paid'];

            VendorPayDetails::query()->where('invoice_id', '=', $request->id)->update([
                'vendor_id' => $invoice->vendor_id,
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

    public function addNewInvoice(Request $request) {
        $defaultValues = DefaultValues::all()->toArray()[0];
        $order = Orders::query()->select('*')->where('id', '=', $request->order_id)->get()->toArray()[0];
       
        try {
            DB::beginTransaction();
            
            $totalPaintMaterialCost = $request->is_paint_material ? $request->quantity * $defaultValues['paint_material_rate'] : 0;
            $totalLaborCost = $request->labor_hours * $order['labor_rate'];
            $subTotal = $request->sub_total + $totalPaintMaterialCost;
            $subTotalWithTax = ($subTotal - $totalLaborCost) + (($subTotal - $totalLaborCost) * $order['tax_rate']);

            $invoices = Invoices::create([
                'order_id' => $request->order_id,
                'vendor_id' => $request->vendor_id,
                'employee_id' => $request->employee_id,
                'service_desc_id' => $request->service_desc_id,
                'is_sublet' => $request->is_sublet,
                'sublet_amount' => $request->sublet_amount,
                'parts_num' => $request->parts_num,
                'quantity' => $request->quantity,
                'our_cost' => $request->our_cost,
                'cust_cost' => $request->cust_cost,
                'est_cost' => $request->est_cost,
                'is_paint_material' => $request->is_paint_material,
                'total_paint_material_cost' => $totalPaintMaterialCost,
                'labor_hours' => $request->labor_hours,
                'total_labor_cost' => $totalLaborCost,
                'estimated_labor' => $request->estimated_labor,
                'sub_total' => $subTotal,
                'sub_total_with_tax' => $subTotalWithTax,
                'date_created' => $request->date_created,
            ]);

            $totalBill = $invoices->our_cost * $invoices->quantity;

            VendorPayDetails::create([
                'vendor_id' => $request->vendor_id,
                'invoice_id' => $invoices->id,
                'payment_type' => null,
                'total_bill' => $totalBill,
                'amount_paid' => 0,
                'balance' => $totalBill,
                'payment_date' => null,
                'card_check_number' => null,
                'memo' => null,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully added an invoice.']);
    }

    public function getAllOrders() {
        $orders = Orders::query()->select(['orders.*', 'customer_details.first_name', 'customer_details.last_name', 'make_model.model_name'])
        ->selectRaw('customer_details.id as cust_id')
        ->selectRaw('DATEDIFF(NOW(),orders.created_at) as order_age')
        ->leftJoin('customer_details', 'orders.cust_id', '=', 'customer_details.id')
        ->leftJoin('vehicle_details', 'orders.vehicle_id', '=', 'vehicle_details.id')
        ->leftJoin('make_model', 'vehicle_details.make_model_id', '=', 'make_model.id')
        ->get()->toArray();

        $invoiceList = Invoices::With(['Orders'])->select("*")->get()->groupBy('order_id');

        $allPayDets = PaymentDetails::With(['Orders'])->select("*")->get()->groupBy('order_id');

        return ['orders' => $orders, 'invoiceList' => $invoiceList,'allPayDets' => $allPayDets];
    }

    public function getAllDeliveredOrders() {
        $orders = Orders::query()->select(['orders.*', 'customer_details.first_name', 'customer_details.last_name', 'make_model.model_name'])
        ->selectRaw('customer_details.id as cust_id')
        ->selectRaw('DATEDIFF(NOW(),orders.created_at) as order_age')
        ->leftJoin('customer_details', 'orders.cust_id', '=', 'customer_details.id')
        ->leftJoin('vehicle_details', 'orders.vehicle_id', '=', 'vehicle_details.id')
        ->leftJoin('make_model', 'vehicle_details.make_model_id', '=', 'make_model.id')
        ->whereNotNull('delivery_date')
        ->get()->toArray();

        $invoiceList = Invoices::With(['Orders'])->select("*")->get()->groupBy('order_id');

        $allPayDets = PaymentDetails::With(['Orders'])->select("*")->get()->groupBy('order_id');

        return ['orders' => $orders, 'invoiceList' => $invoiceList,'allPayDets' => $allPayDets];
    }

    public function getAllInvoicesForGivenOrder(Request $request) {
        return Invoices::query()->select(['invoices.*', 'service_description.service_name'])
        ->leftJoin('service_description', 'invoices.service_desc_id', '=', 'service_description.id')
        ->where('invoices.order_id', '=', $request->order_id)
        ->get()->toArray();
    }

    public function getAllOrdersForGivenCustomer(Request $request) {
        $orders = Orders::query()->select(['orders.*', 'vehicle_details.license', 'make_model.model_name'])
        ->leftJoin('vehicle_details', 'orders.vehicle_id', '=', 'vehicle_details.id')
        ->leftJoin('make_model', 'vehicle_details.make_model_id', '=', 'make_model.id')
        ->where('orders.cust_id', '=', $request->cust_id)
        ->get()->toArray();

        $invoiceList = Invoices::With(['Orders'])->select("*")->get()->groupBy('order_id');

        $allPayDets = PaymentDetails::With(['Orders'])->select("*")->get()->groupBy('order_id');

        return ['orders' => $orders, 'invoiceList' => $invoiceList,'allPayDets' => $allPayDets];
    }

    public function getAllOrdersForGivenVehicle(Request $request) {
        $orders = Orders::query()->select(['orders.*', 'customer_details.first_name', 'customer_details.last_name'])
        ->leftJoin('customer_details', 'orders.cust_id', '=', 'customer_details.id')
        ->where('orders.vehicle_id', '=', $request->vehicle_id)
        ->get()->toArray();

        $invoiceList = Invoices::With(['Orders'])->select("*")->get()->groupBy('order_id');

        $allPayDets = PaymentDetails::With(['Orders'])->select("*")->get()->groupBy('order_id');

        return ['orders' => $orders, 'invoiceList' => $invoiceList,'allPayDets' => $allPayDets];
    }

    public function getAllOrdersForGivenAdjuster(Request $request) {
        $orders = Orders::query()->select(['orders.*', 'customer_details.first_name', 'customer_details.last_name', 'make_model.model_name'])
        ->leftJoin('customer_details', 'orders.cust_id', '=', 'customer_details.id')
        ->leftJoin('vehicle_details',  'orders.vehicle_id', '=', 'vehicle_details.id')
        ->leftJoin('make_model', 'make_model.id', '=', 'vehicle_details.id')
        ->where('orders.ins_adj_id', '=', $request->adj_id)
        ->get()->toArray();

        $invoiceList = Invoices::With(['Orders'])->select("*")->get()->groupBy('order_id');

        $allPayDets = PaymentDetails::With(['Orders'])->select("*")->get()->groupBy('order_id');

        return ['orders' => $orders, 'invoiceList' => $invoiceList,'allPayDets' => $allPayDets];
    }
}
