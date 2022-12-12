<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerDetailsController;
use App\Http\Controllers\VehicleDetailsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentDetailsController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\InsuranceAdjusterController;
use App\Http\Controllers\VendorsController;
use App\Http\Controllers\ServiceDescriptionController;
use App\Http\Controllers\MakeModelController;
use App\Http\Controllers\DefaultValuesController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\EmployeePayDetailsController;
use App\Http\Controllers\PayForController;
use App\Http\Controllers\VendorPayWoPoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Default Values Controller
 */
Route::get('/getDefaultValues', [DefaultValuesController::class, 'getDefaultValues']);
Route::post('/updateDefaultValues', [DefaultValuesController::class, 'updateDefaultValues']);

/**
 * CustomerDetailsController
 */
Route::get('/getAllCustomers', [CustomerDetailsController::class, 'getAllCustomers']);

Route::post('/addNewCustomer', [CustomerDetailsController::class, 'addNewCustomer']);
Route::post('/updateGivenCustomer', [CustomerDetailsController::class, 'updateGivenCustomer']);
Route::post('/deleteCustomer', [CustomerDetailsController::class, 'deleteCustomer']);

/**
 * VehicleDetailsController
 */

Route::get('/getAllVehicles', [VehicleDetailsController::class, 'getAllVehicles']);
Route::get('/getAllModelNames', [VehicleDetailsController::class, 'getAllModelNames']);
Route::get('/getAllVehiclesForGivenCustomer', [VehicleDetailsController::class, 'getAllVehiclesForGivenCustomer']);

Route::post('/addNewVehicle', [VehicleDetailsController::class, 'addNewVehicle']);
Route::post('/updateGivenVehicle', [VehicleDetailsController::class, 'updateGivenVehicle']);
Route::post('/deleteVehicle', [VehicleDetailsController::class, 'deleteVehicle']);

/**
 * VendorPayWoPoController
 */

Route::get('/getAllWoPo', [VendorPayWoPoController::class, 'getAllWoPo']);
Route::get('/getWoPoForGivenId', [VendorPayWoPoController::class, 'getWoPoForGivenId']);
Route::get('/getWoPoForGivenVendor', [VendorPayWoPoController::class, 'getWoPoForGivenVendor']);
Route::get('/getPaymentDetailsWoPoByDateAndType', [VendorPayWoPoController::class, 'getPaymentDetailsWoPoByDateAndType']);
Route::get('/getPaymentDetailsWoPoByType', [VendorPayWoPoController::class, 'getPaymentDetailsWoPoByType']);
Route::get('/getAllVendorsWoPoDetails', [VendorPayWoPoController::class, 'getAllVendorsWoPoDetails']);

Route::post('/addNewWoPo', [VendorPayWoPoController::class, 'addNewWoPo']);

/**
 * EmployeesController
 */

Route::get('/getAllEmployees', [EmployeesController::class, 'getAllEmployees']);
Route::get('/getCurrentAndOldEmployees', [EmployeesController::class, 'getCurrentAndOldEmployees']);

Route::post('/addNewEmployee', [EmployeesController::class, 'addNewEmployee']);
Route::post('/updateGivenEmployee', [EmployeesController::class, 'updateGivenEmployee']);

/**
 * InsuranceAdjusterController
 */

Route::get('/getAllInsAdj', [InsuranceAdjusterController::class, 'getAllInsAdj']);
Route::get('/getAllInsurers', [InsuranceAdjusterController::class, 'getAllInsurers']);
Route::get('/getAdjustersForGivenInsurance', [InsuranceAdjusterController::class, 'getAdjustersForGivenInsurance']);

Route::post('/addNewInsurance', [InsuranceAdjusterController::class, 'addNewInsurance']);
Route::post('/addNewAdjuster', [InsuranceAdjusterController::class, 'addNewAdjuster']);

Route::post('/updateGivenInsurance', [InsuranceAdjusterController::class, 'updateGivenInsurance']);
Route::post('/updateAdjuster', [InsuranceAdjusterController::class, 'updateAdjuster']);

Route::post('/deleteInsurer', [InsuranceAdjusterController::class, 'deleteInsurer']);
Route::post('/deleteAdjuster', [InsuranceAdjusterController::class, 'deleteAdjuster']);

/**
 * VendorsController
 */

Route::get('/getAllVendors', [VendorsController::class, 'getAllVendors']);
Route::get('/getAllPaymentDetailsPerVendor', [VendorsController::class, 'getAllPaymentDetailsPerVendor']);
Route::get('/getAllInvoicesPerVendorNotPaid', [VendorsController::class, 'getAllInvoicesPerVendorNotPaid']);
Route::get('/getPaymentDetailsForInvoice', [VendorsController::class, 'getPaymentDetailsForInvoice']);

Route::post('/addNewVendors', [VendorsController::class, 'addNewVendors']);
Route::post('/updateGivenVendors', [VendorsController::class, 'updateGivenVendors']);
Route::post('/updateVendorPaymentDetails', [VendorsController::class, 'updateVendorPaymentDetails']);

/**
 * PayForController
 */
Route::post('/addNewPayFor', [PayForController::class, 'addNewPayFor']);
Route::get('/getAllPayFor', [PayForController::class, 'getAllPayFor']);


/**
 * InvoiceController
 */
Route::get('/getAllVendorPayDetails', [InvoiceController::class, 'getAllVendorPayDetails']);
Route::get('/getAllInvoicesForGivenVendor', [InvoiceController::class, 'getAllInvoicesForGivenVendor']);
Route::get('/getInvoiceAndVendorInfo', [InvoiceController::class, 'getInvoiceAndVendorInfo']);

Route::post('/updateInfoForVendorInvoicePostOps', [InvoiceController::class, 'updateInfoForVendorInvoicePostOps']);
// Route::post('/updateGivenCustomer', [InvoiceController::class, 'updateGivenCustomer']);


/**
 * ServiceDescriptionController
 */

Route::get('/getAllServiceDesc', [ServiceDescriptionController::class, 'getAllServiceDesc']);

Route::post('/addNewServiceDesc', [ServiceDescriptionController::class, 'addNewServiceDesc']);
Route::post('/updateGivenServiceDesc', [ServiceDescriptionController::class, 'updateGivenServiceDesc']);
Route::post('/deleteService', [ServiceDescriptionController::class, 'deleteService']);


/**
 * MakeModelController
 */

Route::get('/getAllMakeModel', [MakeModelController::class, 'getAllMakeModel']);

Route::post('/addNewMakeModel', [MakeModelController::class, 'addNewMakeModel']);
Route::post('/updateGivenMakeModel', [MakeModelController::class, 'updateGivenMakeModel']);

/**
 * OrdersController
 */

Route::get('/getAllOrders', [OrdersController::class, 'getAllOrders']);
Route::get('/getAllInvoicesForGivenOrder', [OrdersController::class, 'getAllInvoicesForGivenOrder']);
Route::get('/getAllOrdersForGivenCustomer', [OrdersController::class, 'getAllOrdersForGivenCustomer']);
Route::get('/getAllDeliveredOrders', [OrdersController::class, 'getAllDeliveredOrders']);
Route::get('/getAllOrdersForGivenVehicle', [OrdersController::class, 'getAllOrdersForGivenVehicle']);
Route::get('/getAllOrdersForGivenAdjuster', [OrdersController::class, 'getAllOrdersForGivenAdjuster']);

Route::post('/addNewOrder', [OrdersController::class, 'addNewOrder']);
Route::post('/updateOrder', [OrdersController::class, 'updateOrder']);
Route::post('/updateInvoice', [OrdersController::class, 'updateInvoice']);
Route::post('/addNewInvoice', [OrdersController::class, 'addNewInvoice']);


/**
 * PaymentDetailsController
 */

Route::get('/getAllPaymentDetailsForGivenOrder', [PaymentDetailsController::class, 'getAllPaymentDetailsForGivenOrder']);
Route::get('/getAllPaymentTypes', [PaymentDetailsController::class, 'getAllPaymentTypes']);
Route::get('/getPaymentDetailsForLedgerByDate', [PaymentDetailsController::class, 'getPaymentDetailsForLedgerByDate']);

Route::post('/addNewPaymentDetail', [PaymentDetailsController::class, 'addNewPaymentDetail']);
Route::post('/updateGivenMakeModel', [PaymentDetailsController::class, 'updateGivenMakeModel']);

/**
 * EmployeePayDetailsController
 */

Route::get('/getEmpPayDetailsForWorkDone', [EmployeePayDetailsController::class, 'getEmpPayDetailsForWorkDone']);
Route::get('/getEmpPayDetailsForWorkDelivered',[EmployeePayDetailsController::class, 'getEmpPayDetailsForWorkDelivered']);

Route::post('/addNewEmpPayment', [EmployeePayDetailsController::class, 'addNewEmpPayment']);
Route::post('/updateEmpPayment', [EmployeePayDetailsController::class, 'updateEmpPayment']);


/**
 * 
 */

Route::get('/', function () {
    return view('welcome');
});
