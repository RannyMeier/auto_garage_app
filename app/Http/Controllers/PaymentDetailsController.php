<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Orders;
use App\Models\Customers;
use App\Models\CustChargeInfo;
use App\Models\Vehicles;
use App\Models\Invoices;
use App\Models\Vendors;
use App\Models\JobDescriptions;
use App\Models\Employees;
use App\Models\PaymentDetails;
use App\Models\PaymentTypes;

class PaymentDetailsController extends Controller
{
    public function getPaymentDetailsForLedgerByDate(Request $request) {
        $from = $request->from. "00:00:00";
        $to = $request->to. "12:59:59";

        return PaymentDetails::query()
        ->select(['payment_details.*', 'customer_details.first_name', 'customer_details.last_name', 'payment_types.name', 'orders.date_created'])
        ->selectRaw('customer_details.id as cust_id')
        ->leftJoin('orders', 'payment_details.order_id', '=', 'orders.id')
        ->leftJoin("payment_types", "payment_details.payment_type", "=", "payment_types.id")
        ->leftJoin('customer_details', 'orders.cust_id', '=', 'customer_details.id')
        ->where("payment_date", ">=", $from)
        ->where("payment_date", "<=", $to)
        ->get()->toArray();
    }

    public function addNewPaymentDetail(Request $request) {
        try {
            DB::beginTransaction();

            $paymentEntry = PaymentDetails::create([
                'order_id' => $request->order_id,
                'total_bill' => $request->total_bill,
                'amount_paid' => $request->amount_paid,
                'balance' => $request->balance,
                'payment_type' => $request->payment_type,
                'credit_card_number' => $request->credit_card_number,
                'payment_date' => Carbon::now(),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return ['paymentEntry' => $paymentEntry];
    }

    public function getAllPaymentDetailsForGivenOrder(Request $request) {
        return PaymentDetails::query()->select(['payment_details.*', 'customer_details.first_name', 'customer_details.last_name', 'payment_types.name'])
        ->leftJoin('orders', 'payment_details.order_id', '=', 'orders.id')
        ->leftJoin('customer_details', 'orders.cust_id', '=', 'customer_details.id')
        ->leftJoin('payment_types', 'payment_details.payment_type', 'payment_types.id')
        ->where('payment_details.order_id', '=', $request->order_id)
        ->get()->toArray();
    }

    public function getAllPaymentTypes() {
        return PaymentTypes::all()->toArray();
    }
}