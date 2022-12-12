import HomePage from './components/HomePage.vue'

import GenLedgerByDateMain from './components/incomePages/genLedger/GenLedgerByDateMain.vue'
import DisplayLedgerInfo from './components/incomePages/genLedger/DisplayLedgerInfo.vue'

import DisplayCustomerList from './components/maintenancePages/customer/DisplayCustomerList.vue'
import AddNewCustomer from './components/maintenancePages/customer/AddNewCustomer.vue'
import DisplayEditCustomer from './components/maintenancePages/customer/DisplayEditCustomer.vue'

import DisplayVehicleList from './components/maintenancePages/vehicle/DisplayVehicleList.vue'
import AddNewVehicle from './components/maintenancePages/vehicle/AddNewVehicle.vue'
import DisplayEditVehicle from './components/maintenancePages/vehicle/DisplayEditVehicle.vue'

import DisplayEmployeeList from './components/maintenancePages/employees/DisplayEmployeeList.vue'
import AddNewEmployee from './components/maintenancePages/employees/AddNewEmployee.vue'
import DisplayEditEmployee from './components/maintenancePages/employees/DisplayEditEmployee.vue'

import DisplayInsAdjList from './components/maintenancePages/insuranceAdjuster/DisplayInsAdjList.vue'
import AddNewInsAdj from './components/maintenancePages/insuranceAdjuster/AddNewInsAdj.vue'
import DisplayEditInsAdj from './components/maintenancePages/insuranceAdjuster/DisplayEditInsAdj.vue'

import DisplayInsurerList from './components/maintenancePages/insurer/DisplayInsurerList.vue'
import AddNewInsurer from './components/maintenancePages/insurer/AddNewInsurer.vue'
import DisplayEditInsurer from './components/maintenancePages/insurer/DisplayEditInsurer.vue'

import DisplayVendorsList from './components/maintenancePages/vendors/DisplayVendorsList.vue'
import AddNewVendors from './components/maintenancePages/vendors/AddNewVendors.vue'
import DisplayEditVendors from './components/maintenancePages/vendors/DisplayEditVendors.vue'

import DisplayServiceDescList from './components/maintenancePages/serviceDescription/DisplayServiceDescList.vue'
import AddNewServiceDesc from './components/maintenancePages/serviceDescription/AddNewServiceDesc.vue'
import DisplayEditServiceDesc from './components/maintenancePages/serviceDescription/DisplayEditServiceDesc.vue'

import DisplayMakeModelList from './components/maintenancePages/makeModel/DisplayMakeModelList.vue'
import AddNewMakeModel from './components/maintenancePages/makeModel/AddNewMakeModel.vue'
import DisplayEditMakeModel from './components/maintenancePages/makeModel/DisplayEditMakeModel.vue'

import DefaultValuesEdit from './components/maintenancePages/defaultValues/DefaultValuesEdit.vue'

import NewOrders from './components/workInPages/newOrderPages/NewOrders.vue'
import AddNewOrder from './components/workInPages/newOrderPages/AddNewOrder.vue'
import DisplayOrder from './components/workInPages/newOrderPages/DisplayOrder.vue'
import DisplayInvoicesForOrder from './components/workInPages/newOrderPages/DisplayInvoicesForOrder.vue'
import DisplayInvoiceItem from './components/workInPages/newOrderPages/DisplayInvoiceItem.vue'
import AddNewInvoice from './components/workInPages/newOrderPages/AddNewInvoice.vue'
import DisplayTotal from './components/workInPages/newOrderPages/DisplayTotal.vue'

import DisplayAllOrders from './components/incomePages/accRecUpdate/DisplayAllOrders.vue'
import ShowPaymentDetails from './components/incomePages/accRecUpdate/ShowPaymentDetails.vue'
import DepositPaymentSlip from './components/incomePages/accRecUpdate/DepositPaymentSlip.vue'

import DisplayAllCustomers from './components/incomePages/recByCustomer/DisplayAllCustomers.vue'
import DisplayAllOrdersPerCustomer from './components/incomePages/recByCustomer/DisplayAllOrdersPerCustomer.vue'

import CustomerValueByIncomeMain from './components/incomePages/custValueByIncome/CustomerValueByIncomeMain.vue'
import DisplayOrderDetailsCustValue from './components/incomePages/custValueByIncome/DisplayOrderDetailsCustValue.vue'

import WorkDoneByDateMain from './components/employeePages/WorkDoneByDateMain.vue'
import DisplayWorkDoneData from './components/employeePages/workDone/DisplayWorkDoneData.vue'
import DisplayInvoiceData from './components/employeePages/workDone/DisplayInvoiceData.vue'

import DeliveredOrdersList from './components/history/DeliveredOrdersList.vue'
import ShowAllCustomers from './components/history/customers/ShowAllCustomers.vue'
import ShowOrdersPerCustomer from './components/history/customers/ShowOrdersPerCustomer.vue'
import ShowVehicleDetails from './components/history/vehicles/ShowVehicleDetails.vue'
import ShowOrdersPerVehicle from './components/history/vehicles/ShowOrdersPerVehicle.vue'
import ShowInsurerDetails from './components/history/insurance/ShowInsurerDetails.vue'
import ShowAdjustersForInsurer from './components/history/insurance/ShowAdjustersForInsurer'
import ShowOrdersPerAdjuster from './components/history/insurance/ShowOrdersPerAdjuster'

import MainPagePostVendors from './components/expenses/postVendorPos/MainPagePostVendors.vue'
import DisplayInvoicesForVendor from './components/expenses/postVendorPos/DisplayInvoicesForVendor.vue'
import DisplayVendorInvoiceInfo from './components/expenses/postVendorPos/DisplayVendorInvoiceInfo.vue'

import ShowPoVendorPaymentDetails from './components/expenses/payVendorsWithPo/ShowPoVendorPaymentDetails.vue'
import ShowPaymentsPerVendor from './components/expenses/payVendorsWithPo/ShowPaymentsPerVendor.vue'
import AddVendorPayment from './components/expenses/payVendorsWithPo/AddVendorPayment.vue'
import AddNewPayFor from './components/expenses/payVendorsWithPo/AddNewPayFor.vue'

import ShowNoPoVendorPaymentDetails from './components/expenses/payVendorsWoPo/ShowNoPoVendorPaymentDetails.vue'
import AddWoPoPayment from './components/expenses/payVendorsWoPo/AddWoPoPayment.vue'
import ShowPaymentsPerEntry from './components/expenses/payVendorsWoPo/ShowPaymentsPerEntry.vue'
import PaymentDetailsPerPayFor from './components/expenses/listExpenses/PaymentDetailsPerPayFor.vue'
import ExpenseHistory from './components/expenses/listExpenses/ExpenseHistory.vue'
import DisplayInvoicesForVendorWoPo from './components/expenses/vendorHistoryWoPo/DisplayInvoicesForVendorWoPo.vue'
import MainPagePostVendorsWoPo from './components/expenses/vendorHistoryWoPo/MainPagePostVendorsWoPo.vue'

import DeleteCustomer from './components/maintenancePages/customer/DeleteCustomer.vue'
import DeleteVehicle from './components/maintenancePages/vehicle/DeleteVehicle.vue'
import DeleteAdjuster from './components/maintenancePages/insuranceAdjuster/DeleteAdjuster.vue'
import DeleteInsurer from './components/maintenancePages/insurer/DeleteInsurer.vue'
import DeleteService from './components/maintenancePages/serviceDescription/DeleteService.vue'

require('./bootstrap');
require('./consts');

window.Vue = require('vue').default;
 
const app = new Vue({
    el: '#app',
	components: {
        "home-page": HomePage,
        "gen-ledger-by-date-main": GenLedgerByDateMain,
        "display-ledger-info": DisplayLedgerInfo,
        "display-customer-list": DisplayCustomerList,
        "add-new-customer": AddNewCustomer,
        "display-edit-customer": DisplayEditCustomer,
        "display-vehicle-list": DisplayVehicleList,
        "add-new-vehicle": AddNewVehicle,
        "display-edit-vehicle": DisplayEditVehicle,
        "display-employee-list": DisplayEmployeeList,
        "add-new-employee": AddNewEmployee,
        "display-edit-employee": DisplayEditEmployee,
        "display-ins-adj-list": DisplayInsAdjList,
        "add-new-ins-adj": AddNewInsAdj,
        "display-edit-ins-adj": DisplayEditInsAdj,
        "display-vendors-list": DisplayVendorsList,
        "add-new-vendors": AddNewVendors,
        "display-edit-vendors": DisplayEditVendors,
        "display-service-desc-list": DisplayServiceDescList,
        "add-new-service-desc": AddNewServiceDesc,
        "display-edit-service-desc": DisplayEditServiceDesc,
        "display-make-model-list": DisplayMakeModelList,
        "add-new-make-model": AddNewMakeModel,
        "display-edit-make-model": DisplayEditMakeModel,
        "default-values-edit": DefaultValuesEdit,
        "new-orders": NewOrders,
        "add-new-order": AddNewOrder,
        "display-order": DisplayOrder,
        "display-invoices-for-order": DisplayInvoicesForOrder,
        "display-invoice-item": DisplayInvoiceItem,
        "add-new-invoice": AddNewInvoice,
        "display-total": DisplayTotal,
        "display-all-orders": DisplayAllOrders,
        "show-payment-details": ShowPaymentDetails,
        "deposit-payment-slip": DepositPaymentSlip,
        "display-all-customers": DisplayAllCustomers,
        "display-all-orders-per-customer": DisplayAllOrdersPerCustomer,
        "customer-value-by-income-main": CustomerValueByIncomeMain,
        "display-order-details-cust-value":DisplayOrderDetailsCustValue,
        "work-done-by-date-main": WorkDoneByDateMain,
        "display-work-done-data": DisplayWorkDoneData,
        "display-invoice-data": DisplayInvoiceData,
        "display-insurer": DisplayInsurerList,
        "add-new-insurer": AddNewInsurer,
        "display-edit-insurer": DisplayEditInsurer,
        "delivered-orders-list": DeliveredOrdersList,
        "show-all-customers": ShowAllCustomers,
        "show-orders-per-customer": ShowOrdersPerCustomer,
        "show-vehicle-details": ShowVehicleDetails,
        "show-orders-per-vehicle": ShowOrdersPerVehicle,
        "show-insurer-details": ShowInsurerDetails,
        "show-adjusters-for-insurer": ShowAdjustersForInsurer,
        "show-orders-per-adjuster": ShowOrdersPerAdjuster,
        "main-page-post-vendors": MainPagePostVendors,
        "display-invoices-for-vendor": DisplayInvoicesForVendor,
        "display-vendor-invoice-info": DisplayVendorInvoiceInfo,
        "show-po-vendor-payment-details":ShowPoVendorPaymentDetails,
        "show-payments-per-vendor": ShowPaymentsPerVendor,
        "add-vendor-payment": AddVendorPayment,
        "add-new-pay-for": AddNewPayFor,
        "show-no-po-vendor-payment-details": ShowNoPoVendorPaymentDetails,
        "add-wo-po-payment": AddWoPoPayment,
        "show-payments-per-entry": ShowPaymentsPerEntry,
        "payment-details-per-pay-for": PaymentDetailsPerPayFor,
        "expense-history": ExpenseHistory,
        "main-page-post-vendors-wo-po": MainPagePostVendorsWoPo,
        "display-invoices-for-vendor-wo-po": DisplayInvoicesForVendorWoPo,
        "delete-customer": DeleteCustomer,
        "delete-vehicle": DeleteVehicle,
        "delete-adjuster": DeleteAdjuster,
        "delete-insurer": DeleteInsurer,
        "delete-service": DeleteService
    }
});
