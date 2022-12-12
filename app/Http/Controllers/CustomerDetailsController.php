<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\CustomerDetails;

class CustomerDetailsController extends Controller
{
    public function addNewCustomer(Request $request) 
    {
        try {
            DB::beginTransaction();

            $customerInfo = CustomerDetails::create([
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'state' => $request->state,
                'city' => $request->city,
                'zip' => $request->zip,
                'contact' => $request->contact,
                'comments' => $request->comments,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return ['customer' => $customerInfo];
    }

    public function updateGivenCustomer(Request $request) {
        try {
            DB::beginTransaction();

            $customerInfo = CustomerDetails::query()->where('id', '=', $request->id)->update([
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'state' => $request->state,
                'city' => $request->city,
                'zip' => $request->zip,
                'contact' => $request->contact,
                'comments' => $request->comments,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully updated customer information.']);
    }

    public function getAllCustomers() {
        return CustomerDetails::query()->select('*')->get()->toArray();
    }

    public function deleteCustomer(Request $request) {
        try {
            DB::beginTransaction();

            $customerInfo = CustomerDetails::query()->where('id', '=', $request->id)->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw Exception($e);
        }
        
        return response()->json(['success' => 'Successfully deleted customer.']);
    }
}