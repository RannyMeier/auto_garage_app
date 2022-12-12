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
use App\Models\EmployeePayDetails;
use App\Models\Invoices;
use App\Models\Orders;
use App\Models\PaymentDetails;

class EmployeePayDetailsController extends Controller
{
    public function addNewEmpPayment(Request $request) 
    {
        try {
            DB::beginTransaction();

            EmployeePayDetails::create([
                'emp_id'=>$request->emp_id,
                'invoice_id'=>$request->invoice_id,
                'pay_date'=>$request->pay_date,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully created employee pay information.']);
    }

    public function updateEmpPayment(Request $request) {
        try {
            DB::beginTransaction();

            Employees::query()->where('id', '=', $request->id)->update([
                'pay_date'=>$request->pay_date,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated employee pay information.']);
    }

    /**
     * Show all invoices the given employee worked on
     */
    public function getEmpPayDetailsForWorkDone(Request $request) {
        $from = $request->from;//. "00:00:00";
        $to = $request->to;//. "12:59:59";

        return Invoices::query()->select(['employee_pay_details.pay_date','employees.first_name','employees.last_name', 'orders.labor_rate', 'invoices.labor_hours', 'invoices.*', 'service_description.service_name'])
        ->leftJoin('employee_pay_details', 'invoices.id', '=', 'employee_pay_details.invoice_id')
        ->leftJoin('orders', 'invoices.order_id', '=', 'orders.id')
        ->leftJoin('employees', 'invoices.employee_id', '=', 'employees.id')
        ->leftJoin('service_description', 'invoices.service_desc_id', '=', 'service_description.id')
        ->where('invoices.employee_id', '=', $request->emp_id)
        ->where("invoices.date_created", ">=", $from)
        ->where("invoices.date_created", "<=", $to)
        ->get()->toArray();
    }

    public function getEmpPayDetailsForWorkDelivered(Request $request) {
        $from = $request->from;//. "00:00:00";
        $to = $request->to;//. "12:59:59";

        $orders = Orders::query()->select(['employees.first_name','employees.last_name', 'orders.*', 'vehicle_details.license', 'make_model.model_name'])
        ->leftJoin('invoices', 'orders.id', '=', 'invoices.order_id')
        ->leftJoin('employees', 'invoices.employee_id', '=', 'employees.id')
        ->leftJoin('vehicle_details', 'orders.vehicle_id', '=', 'vehicle_details.id')
        ->leftJoin('make_model', 'vehicle_details.make_model_id', '=', 'make_model.id')
        ->where('invoices.employee_id', '=', $request->emp_id)
        ->where("orders.date_created", ">=", $from)
        ->where("orders.date_created", "<=", $to)
        ->whereNotNull('delivery_date')
        ->distinct()->get()->toArray();

        $invoiceList = Invoices::With(['Orders'])->select("*")->where('employee_id', '=',$request->emp_id)->get()->groupBy('order_id');

        $allPayDets = PaymentDetails::With(['Orders'])->select("*")->get()->groupBy('order_id');

        return ['orders' => $orders, 'invoiceList' => $invoiceList,'allPayDets' => $allPayDets];
    }
}