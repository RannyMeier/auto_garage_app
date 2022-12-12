<template>
    <div class="container home-page-background">
        <div class="row justify-content-center">
            <div class="pb-2 pageHeading" style="background-color:#CD7F32;">
                West County Auto Body Repair
            </div>
            <div class="row nav-bar-row mt-2"  v-if="!showForm">
                <div class="dropdown col-lg-2" v-for="item in navValues" v-bind:key="item.key">
                    <button class="btn" type="button" data-toggle="dropdown" :class="[(paneName == item.key ? 'nav-bar-active' : '')]" @click.prevent="selectedPane(item.key)">{{item.value}}</button>
                    <ul class="dropdown-menu dropdown-box nav-bar-pages-ul">
                        <li v-for="(dm,idx) in dropDownMenus" v-bind:key="idx" class="p1">
                            <a href="javascript:void(0)" @click.prevent="selectedDropDownValue(dm.key)">
                                {{dm.value}}
                            </a>
                        </li>
                    </ul>                    
                </div>
            </div>
            <div v-else-if="showForm">
                <div v-if="selectedFormType == 'customer'">
                    <display-customer-list :form-type="selectedFormType" @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='vehicle'">
                    <display-vehicle-list :form-type="selectedFormType" @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='employee' || selectedFormType=='all_emp_file' || selectedFormType=='curr_emp_file'">
                    <display-employee-list :form-type="selectedFormType" @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='adjuster'">
                    <display-ins-adj-list :form-type="selectedFormType" @escape:form="escapeForm" />
                </div>
                 <div v-else-if="selectedFormType=='insurer'">
                    <display-insurer-list :form-type="selectedFormType" @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='vendor'">
                    <display-vendors-list :form-type="selectedFormType" @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='services'">
                    <display-service-desc-list :form-type="selectedFormType" @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='make_and_model'">
                    <display-make-model-list :form-type="selectedFormType" @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='default'">
                    <default-values-edit :form-type="selectedFormType" @escape:form="escapeForm" />
                </div>

                <div v-else-if="selectedFormType == 'work_in_update' || selectedFormType == 'work_in_new' || selectedFormType == 'work_in_aged'">
                    <new-orders :form-type="selectedFormType" @escape:form="escapeForm" />
                </div>

                <div v-else-if="selectedFormType == 'acc_rec_update' || selectedFormType == 'rec_age'">
                    <display-all-orders :form-type="selectedFormType"  @escape:form="escapeForm"/>
                </div>
                <div v-else-if="selectedFormType == 'rec_cust'">
                    <display-all-customers :form-type="selectedFormType"  @escape:form="escapeForm"/>
                </div>
                <div v-else-if="selectedFormType == 'gen_ledg_date'">
                    <gen-ledger-by-date-main :form-type="selectedFormType"  @escape:form="escapeForm"/>
                </div>
                <div v-else-if="selectedFormType=='cust_val_income'">
                    <customer-value-by-income-main :form-type="selectedFormType"  @escape:form="escapeForm"/>
                </div>

                <div v-else-if="selectedFormType=='work_done_by_date'">
                    <work-done-by-date-main :form-type="selectedFormType"  @escape:form="escapeForm" />
                </div>

                <div v-else-if="selectedFormType=='history_order'">
                    <delivered-orders-list :form-type="selectedFormType"  @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='history_customer'">
                    <show-all-customers :form-type="selectedFormType"  @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='history_vehicle'">
                    <show-vehicle-details :form-type="selectedFormType"  @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='history_insurance'">
                    <show-insurer-details :form-type="selectedFormType"  @escape:form="escapeForm" />
                </div>

                <div v-else-if="selectedFormType=='post_vendor_po'">
                    <main-page-post-vendors :form-type="selectedFormType"  @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='pay_vendors_w_po'">
                    <show-po-vendor-payment-details :form-type="selectedFormType"  @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='pay_vendors_wo_po'">
                    <show-no-po-vendor-payment-details :form-type="selectedFormType"  @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='expenses_type' || selectedFormType == 'expenses_date_type'">
                    <expense-history :form-type="selectedFormType"  @escape:form="escapeForm" />
                </div>
                <div v-else-if="selectedFormType=='vendor_history_wo_po'">
                    <main-page-post-vendors-wo-po :form-type="selectedFormType"  @escape:form="escapeForm" />
                </div>
            </div>
            <div v-else>
                <center class="logo-name">AUTO PRO</center>
            </div>
        </div>
    </div>
</template>

<script>
    import GenLedgerByDateMain from './incomePages/genLedger/GenLedgerByDateMain.vue';
    import NewOrders from './workInPages/newOrderPages/NewOrders.vue'
    import DisplayCustomerList from './maintenancePages/customer/DisplayCustomerList.vue'
    import DisplayVehicleList from './maintenancePages/vehicle/DisplayVehicleList.vue'
    import DisplayEmployeeList from './maintenancePages/employees/DisplayEmployeeList.vue'
    import DisplayInsAdjList from './maintenancePages/insuranceAdjuster/DisplayInsAdjList.vue'
    import DisplayInsurerList from './maintenancePages/insurer/DisplayInsurerList.vue'
    import DisplayVendorsList from './maintenancePages/vendors/DisplayVendorsList.vue'
    import DisplayServiceDescList from './maintenancePages/serviceDescription/DisplayServiceDescList.vue';
    import DisplayMakeModelList from './maintenancePages/makeModel/DisplayMakeModelList.vue';
    import DefaultValuesEdit from './maintenancePages/defaultValues/DefaultValuesEdit.vue';
    import DisplayAllOrders from './incomePages/accRecUpdate/DisplayAllOrders.vue'
    import DisplayAllCustomers from './incomePages/recByCustomer/DisplayAllCustomers.vue'
    import CustomerValueByIncomeMain from './incomePages/custValueByIncome/CustomerValueByIncomeMain.vue';
    import WorkDoneByDateMain from './employeePages/WorkDoneByDateMain.vue'
    import DeliveredOrdersList from './history/DeliveredOrdersList.vue';
    import ShowAllCustomers from './history/customers/ShowAllCustomers.vue';
    import ShowVehicleDetails from './history/vehicles/ShowVehicleDetails.vue';
    import ShowInsurerDetails from './history/insurance/ShowInsurerDetails.vue';
    import MainPagePostVendors from './expenses/postVendorPos/MainPagePostVendors.vue';
    import ShowPoVendorPaymentDetails from './expenses/payVendorsWithPo/ShowPoVendorPaymentDetails.vue';
    import ShowNoPoVendorPaymentDetails from './expenses/payVendorsWoPo/ShowNoPoVendorPaymentDetails.vue';
    import ExpenseHistory from './expenses/listExpenses/ExpenseHistory.vue';
    import MainPagePostVendorsWoPo from './expenses/vendorHistoryWoPo/MainPagePostVendorsWoPo.vue';

    export default {
	    name:"HomePage",
        components:{
            DisplayCustomerList,
            DisplayVehicleList,
            DisplayEmployeeList,
            DisplayInsAdjList,
            DisplayVendorsList,
            DisplayServiceDescList,
            DisplayMakeModelList,
            DefaultValuesEdit,
            NewOrders,
            DisplayAllOrders,
            DisplayAllCustomers,
            GenLedgerByDateMain,
            CustomerValueByIncomeMain,
            WorkDoneByDateMain,
            DisplayInsurerList,
            DeliveredOrdersList,
            ShowAllCustomers,
            ShowVehicleDetails,
            ShowInsurerDetails,
            MainPagePostVendors,
            ShowPoVendorPaymentDetails,
            ShowNoPoVendorPaymentDetails,
            ExpenseHistory,
            MainPagePostVendorsWoPo
        },
        data(){
            return {
                isMounted: false,
                paneName: '',
                selectedFormType: '',
                showForm: false,
                showDropDown: false,
                navValues: [
                    {key: 'workIn', value:'Work In'},
                    {key: 'income', value:'Income'},
                    {key: 'expenses', value:'Expenses'},
                    {key: 'employee', value:'Employee'},
                    {key: 'maintenance', value:'Maintenance'},
                    {key: 'history', value:'History'},
                ],
            };
        },
        mounted() {
            this.paneName = this.paneName == '' ? this.navValues[0].key : this.paneName;
            this.isMounted = true;
        },
        computed:{
            dropDownMenus() {
                let dropDownMenu = [];
                if(this.paneName == "workIn") {
                    dropDownMenu = [
                        {key: "work_in_new", value:"NEW  Estimate and Orders"},
                        {key: "work_in_update", value:"UPDATE  Estimate and Orders"},
                        {key: "work_in_aged", value:"AGED  Estimate and Orders"}
                    ];
                }

                if(this.paneName == "income") {
                    dropDownMenu = [
                        {key: "acc_rec_update", value:"Account Receivable - Update"},
                        {key: "rec_cust", value:"Receivable by Customer"},
                        {key: "rec_age", value:"Receivable by Age"},
                        {key: "gen_ledg_date", value:"General Ledger by Date"},
                        {key: "cust_val_income", value:"Customer Value by Income"},
                        // {key: "aged", value:"Print Labels -- Customers"},
                        // {key: "aged", value:"Print List -- Customers"},
                    ];
                }

                if(this.paneName == "expenses") {
                    dropDownMenu = [
                        {key: "post_vendor_po", value:"Post Vendor POs"},
                        // {key: "update_acc_payable", value:"Update Accounts Payable"},
                        {key: "pay_vendors_w_po", value:"Pay Vendors (with PO)"},
                        {key: "pay_vendors_wo_po", value:"Pay Vendors (without PO)"},
                        {key: "expenses_date_type", value:"Expenses by Date & Type"},
                        {key: "expenses_type", value:"Expenses by Type"},
                        {key: "vendor_history_wo_po", value:"Vendor History (without PO)"},
                        // {key: "gen_ledg_date", value:"General Ledger by Date"},
                        // {key: "aged", value:"Print Labels -- Customers"},
                        // {key: "aged", value:"Print List -- Customers"},
                    ];
                }

                if(this.paneName == "employee") {
                    dropDownMenu = [
                        {key: "work_done_by_date", value:"Work Done by Date"},
                        {key: "curr_emp_file", value:"Current Employees File"},
                        {key: "all_emp_file", value:"All Employees File"}
                    ];
                }

                if(this.paneName == "maintenance") {
                    dropDownMenu = [
                        {key: "customer", value:"Customer"},
                        {key: "vehicle", value:"Vehicle"},
                        {key: "employee", value:"Employee"},
                        {key: "insurer", value:"Insurer"},
                        {key: 'adjuster', value: "Adjuster"},
                        {key: "vendor", value:"Vendor"},
                        {key: "default", value:"Default"},
                        {key: "services", value:"Services"},
                        {key: "make_and_model", value:"Make & Model"},
                        // {key: "reconcilation", value:"Reconcilation"}
                    ];
                }

                if(this.paneName == "history") {
                    dropDownMenu = [
                        {key: "history_order", value:"Order"},
                        {key: "history_customer", value:"Customer"},
                        {key: "history_vehicle", value:"Vehicle"},
                        {key: "history_insurance", value:"Insurance"}
                    ];
                }

                this.selectedFormType = dropDownMenu[0].key;
                return dropDownMenu;
            }
        },
        methods:{
            selectedPane(value)
            {
                this.paneName = value;
                this.showDropDown = true;
            },
            selectedDropDownValue(name) {
                this.selectedFormType = name;
                this.showForm = true;
            },
            escapeForm(value) {               
                if(this.showForm === false) {
                    this.showDropDown = false;
                    this.selectedFormType = '';
                    this.showForm = false;
                } else {
                    this.showDropDown=true;
                    this.showForm = false;
                }
            },
        }
    }
</script>
