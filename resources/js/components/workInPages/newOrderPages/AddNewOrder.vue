<template>
  <div v-if="addNewOrder">
    <div id="modal">
      <div class="modal-content">
            <div class="pageHeading">
                Add New Order<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <div class="formHeadingFooter p1">
                <span>{{currentTime}}</span>
                <span>{{currentDate}}</span>
            </div>
            <h4 v-if="addNew" style="color: darkgreen;">Successfully added new entry. Please stay and we will redirect you.</h4>
            <strong class="p-2">Fields marked with red stars are required fields.</strong>
            <form class="formBody" ref="addNewCustForm">
                <div class="row">
                    <label>Select a Customer<span class="req">*</span>: </label>
                    <select v-model="customerValue" @click="chosenCustomer" style="width: 45%;margin-left:2rem;">
                        <option value=""></option>
                        <option value="add_new_customer">Add New Customer</option>
                        <option v-for="item in allCustomersList" v-bind:key="item.id" :value="item.id">
                            {{item.first_name}} {{item.last_name}} - {{item.phone1}}
                        </option>
                    </select>
                    <br/>
                    <hr/>
                    <label>Select a Insurance Adjuster: </label>
                    <select v-model="insAdjValue" @click="chosenInsAdj" style="width: 45%;margin-left:2rem;">
                        <option value=""></option>
                        <option value="add_new_ins_adj">Add New Insurance Adjuster</option>
                        <option v-for="item in insAdjList" v-bind:key="item.id" :value="item.id">
                            {{item.adjuster_name}} - {{item.company_name}}
                        </option>
                    </select>
                    <br/>
                    <hr/>
                    <div v-if="formFields.cust_id !== ''">
                        <label>Select a vehicle<span class="req">*</span>: </label>
                        <select v-model="vehicleValue" @click="chosenVehicle" style="width: 45%;margin-left:2rem;">
                            <option value=""></option>
                            <option value="add_new_vehicle">Add New Vehicle</option>
                            <option v-for="item in carsOfCustomer" v-bind:key="item.id" :value="item.id">
                                {{item.model_name}}
                            </option>
                        </select>
                    </div>
                    <br/>
                    <hr/>
                </div>
                <div class="row">
                    <hr/>
                    <div class="col-6">
                        Other Charges:
                        <br/>
                        <hr/>
                        <label>Waste: </label> <input type="text" name="waste" v-model="formFields.waste" placeholder="0.00"/> <br/>
                        <label>Towing: </label> <input type="text" name="towing" v-model="formFields.towing"/> <br/>
                        <label>Storage Days: </label> <input type="text" name="storage_days" v-model="formFields.storage_days"/> <br/>
                        <label>Storage Rate: </label> <input type="text" name="storage_rate" v-model="formFields.storage_rate"/> <br/>
                        Storage Cost = {{storageCost}}
                    </div>
                    <div class="col-6">
                        Update Default Rates for this customer?
                        <br/>
                        <hr/>
                        <label>Labor Rate:</label> <input type="text" name="labor_rate" v-model="formFields.labor_rate" placeholder="0.00"/> <br/>
                        <label>Discount:</label> <input type="text" name="discount" v-model="formFields.discount"/> <br/>
                        <label>Tax Rate:</label> <input type="text" name="tax_rate" v-model="formFields.tax_rate"/> <br/>
                        <label>Customer Class (Base Markup):</label> <input type="text" name="parts_markup" v-model="formFields.parts_markup"/><br/>
                    </div>
                <hr/>
                </div>
                <div class="row">
                    <h3>Add Invoice Details</h3><br/>
                    <hr/>
                    <div class="col-6">
                        <label for="date_created">Date:</label><input type="date" id="date_created" name="date_created" v-model="formFields.date_created" class="ml-1">
                        <br/>
                        <hr/>
                        <label>Vendor: </label>
                        <select v-model="vendorValue" @click="chosenVendor" style="width: 45%;margin-left:2rem;">
                            <option value=""></option>
                            <option value="add_new_vendor">Add New Vendor</option>
                            <option v-for="item in vendorsList" v-bind:key="item.id" :value="item.id">
                                {{item.name}}
                            </option>
                        </select>
                        <br/>
                        <hr/>
                        <label>Quantity: </label> <input type="text" name="quantity" v-model="formFields.quantity"/> 
                        <br/>
                        <hr/>
                        <label>Parts#: </label> <input type="text" name="parts_num" v-model="formFields.parts_num"/> 
                        <br/>
                        <hr/>
                         Sublet or Not?: <input type="checkbox" id="is_sublet" name="is_sublet" v-model="formFields.is_sublet">
                        <br />
                        <div v-if="formFields.is_sublet">
                            <label>Sublet Amount: </label> <input type="text" name="sublet_amount" v-model="formFields.sublet_amount" placeholder="0.00"/> 
                            <br/>
                        </div>
                        <div v-else>
                            <label>Our Cost: </label> <input type="text" name="our_cost" v-model="formFields.our_cost"/> 
                            <br/>
                            Customer Cost after calculation is: {{custCost}}
                            <br/>
                        </div>
                        <br/>
                        <hr/>
                        <label>Description<span class="req">*</span>: </label>
                        <select v-model="serviceDescValue" @click="chosenService" style="width: 45%;margin-left:2rem;">
                            <option value=""></option>
                            <option value="add_new_service">Add New Service</option>
                            <option v-for="item in serviceDescList" v-bind:key="item.id" :value="item.id">
                                {{item.service_name}}
                            </option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Estimated Cost: </label> <input type="text" name="est_cost" v-model="formFields.est_cost"/> 
                        <br/>
                        <hr/>
                        Use Paint/Material?: <input type="checkbox" id="is_paint_material" name="is_paint_material" v-model="formFields.is_paint_material">
                        <br/>
                        <span>Paint Material Cost: {{paintMaterialCost}}</span>
                        <br />
                        <hr/>
                        <label>Select an employee<span class="req">*</span>: </label>
                        <select v-model="formFields.employee_id" style="width: 45%;margin-left:2rem;">
                            <option v-for="item in allEmpValues" v-bind:key="item.id" :value="item.id">
                                {{item.first_name}} {{item.last_name}}
                            </option>
                        </select>
                        <br/>
                        <hr/>
                        <b>Labor Hours Rate (@ {{formFields.labor_rate}}/hour): </b> 
                        <br/>
                        <input type="text" name="labor_hours" v-model="formFields.labor_hours"/> 
                        <br/>
                        Labor Cost = {{laborCost}} 
                        <br/>
                        <hr/>
                        <label>Estimated Labor: </label> <input type="text" name="estimated_labor" v-model="formFields.estimated_labor"/> 
                        <br/>
                    </div>
                    <hr/>
                </div>
                <div class="p-3">
                    <button class="btn btn-link" @click="hide">Cancel</button>
                    <button class="btn btn-primary btn-sm" @click.prevent="submit" :disabled="disableSubmitButton"> Submit</button>
                </div>
                <small v-if="submitFailed" style="color:red;">Something went wrong. Please try again. If problem persists, contact developer.</small>
            </form>
        </div>
    </div>
    <add-new-customer ref="addNewCustomer" :send-back-id="true" @cust:id="getCustId" />
    <add-new-vehicle ref="addNewVehicle" :send-back-id="true" :customer-id="formFields.cust_id"  @vehicle:id="getVehicleId" />
    <add-new-ins-adj ref="addNewInsAdj" :send-back-id="true" @insAdj:id="getInsAdjId" />
    <add-new-service-desc ref="addNewServiceDesc" :send-back-id="true" @desc:id="getServiceDescId" />
    <add-new-vendors ref="addNewVendors" :send-back-id="true" @vendor:id="getVendorId" />
  </div>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';
    import AddNewCustomer from '../../maintenancePages/customer/AddNewCustomer.vue';
    import AddNewVehicle from '../../maintenancePages/vehicle/AddNewVehicle.vue';
    import AddNewInsAdj from '../../maintenancePages/insuranceAdjuster/AddNewInsAdj.vue';
    import AddNewServiceDesc from '../../maintenancePages/serviceDescription/AddNewServiceDesc.vue';
    import AddNewVendors from '../../maintenancePages/vendors/AddNewVendors.vue';

    export default {
	    name:"AddNewOrder",
        props:{
            formType: String
        },
        components:{
            AddNewCustomer, AddNewVehicle, AddNewInsAdj, AddNewServiceDesc, AddNewVendors
        },
        data(){
            return {
                addNewOrder: false,
                submitFailed: false,
                customerValue:'',
                vehicleValue: '',
                insAdjValue: '',
                serviceDescValue: '',
                vendorValue: '',
                selectedModelName: '',
                currentDate: '',
                currentTime: '',
                allCustomersList: [],
                carsOfCustomer: [],
                insAdjList: [],
                defaultValues: [],
                vendorsList: [],
                serviceDescList: [],
                allEmpValues: [],
                todaysDate: new Date().toISOString().slice(0,10),
                formFields:{ 
                    cust_id: '',
                    ins_adj_id: '',
                    vehicle_id: '',
                    waste: 0,
                    towing: 0,
                    storage_days: 0,
                    storage_rate: 0,
                    storage_cost: '',
                    labor_rate: 0,
                    tax_rate: 0,
                    discount: 0,
                    parts_markup: 0,
                    date_created: this.todaysDate,
                    vendor_id: '',
                    is_sublet: false,
                    sublet_amount: 0,
                    our_cost: 0,
                    quantity: 0,
                    parts_num: 0,
                    service_desc_id: '',
                    est_cost: 0,
                    is_paint_material: false,
                    employee_id: '',
                    labor_hours: 0,
                    estimated_labor: 0,
                },
                addNew: false,
            };
        },
        async mounted() {
            this.currentDate = moment().format('MMM DD, YYYY');
            this.currentTime = moment().format('HH:MM A');
            await this.getAllCustomers();
            await this.getAllInsAdj();
            await this.getDefaultValues();
            await this.getAllVendors();
            await this.getAllServiceDesc();
            await this.getAllEmployees();
        },
        computed:{
            disableSubmitButton() {
                return this.formFields.cust_id == '' || this.formFields.vehicle_id == '' || this.formFields.employee_id == '' || this.formFields.service_desc_id == '';
            },
            storageCost() {
                return this.formFields.storage_rate * this.formFields.storage_days;
            },
            custCost() {
                return (this.formFields.our_cost * this.formFields.quantity) + (this.formFields.our_cost * this.formFields.quantity * this.formFields.parts_markup);
            },
            paintMaterialCost() {
                return this.formFields.is_paint_material == true ? this.formFields.quantity * this.defaultValues['paint_material_rate'] : 0;
            },
            laborCost() {
                return this.formFields.labor_hours * this.formFields.labor_rate;
            }
        },
        methods:{
            async getVendorId(value) {
                if(value !== 0) {
                    this.formFields.vendor_id = value.vendor.id;
                    this.vendorValue = this.formFields.vendor_id;
                    await this.getAllVendors();
                } else {
                    this.vendorValue = '';
                }
            },
            async getServiceDescId(value) {
                if(value !== 0) {
                    this.formFields.service_desc_id = value.serviceDesc.id;
                    this.serviceDescValue = this.formFields.service_desc_id;
                    await this.getAllServiceDesc();
                } else {
                    this.serviceDescValue = '';
                }
            },
            async getCustId(value) {
                if(value !== 0) {
                    this.formFields.cust_id = value.customer.id;
                    this.customerValue = this.formFields.cust_id;
                    await this.getAllCustomers();
                } else {
                    this.customerValue = '';
                }
            },
            async getVehicleId(value) {
                if(value !== 0) {
                    this.formFields.vehicle_id = value.vehicle.id;
                    this.vehicleValue = this.formFields.vehicle_id;
                    await this.getAllVehiclesForGivenCustomer();
                } else {
                    this.vehicleValue = '';
                }
            },
            async getInsAdjId(value) {
                if(value !== 0) {
                    this.formFields.ins_adj_id = value.adjuster[0].id;
                    this.insAdjValue = this.formFields.ins_adj_id;
                    await this.getAllInsAdj();
                } else {
                    this.insAdjValue = '';
                }
            },
            async chosenCustomer() {
                if(this.customerValue == "add_new_customer"){
                    this.$nextTick(()=>{
                        this.$refs.addNewCustomer.show();
                    });
                } else {
                    this.formFields.cust_id = this.customerValue;
                    await this.getAllVehiclesForGivenCustomer();
                }    
            },
            async chosenService() {
                if(this.serviceDescValue == "add_new_service"){
                    this.$nextTick(()=>{
                        this.$refs.addNewServiceDesc.show();
                    });
                } else {
                    this.formFields.service_desc_id = this.serviceDescValue;
                    await this.getAllServiceDesc();
                } 
            },
            async chosenVendor() {
                if(this.vendorValue == "add_new_vendor"){
                    this.$nextTick(()=>{
                        this.$refs.addNewVendors.show();
                    });
                } else {
                    this.formFields.vendor_id = this.vendorValue;
                    await this.getAllVendors();
                } 
            },
            async chosenVehicle() {
                if(this.vehicleValue == "add_new_vehicle") {
                    this.$nextTick(()=>{
                        this.$refs.addNewVehicle.show();
                    });
                } else {
                    this.formFields.vehicle_id = this.vehicleValue;
                }  
            },
            async chosenInsAdj() {
                if(this.insAdjValue == "add_new_ins_adj") {
                    this.$nextTick(()=>{
                        this.$refs.addNewInsAdj.show();
                    });
                } else {
                    this.formFields.ins_adj_id = this.insAdjValue;
                }  
            },
            async getAllVehiclesForGivenCustomer() {
                const params = {params:{cust_id: this.formFields.cust_id}};
                try {
                    const response = await axios.get(`getAllVehiclesForGivenCustomer`, params);
                    this.carsOfCustomer = response.data;
                } catch (exception) {
                    console.log("exception: ", exception)
                }
            },
            async getAllCustomers() {
                try {
                    const response = await axios.get(`/getAllCustomers`);
                    this.allCustomersList = response.data;
                } catch (exception) {
                    console.log("exception: ", exception)
                }
            },
            async getDefaultValues(){
                try {
                    const response = await axios.get(`/getDefaultValues`);
                    this.defaultValues = response.data[0];
                    this.formFields.storage_rate = this.defaultValues['storage_rate'];
                    this.formFields.labor_rate = this.defaultValues['labor_rate'];
                    this.formFields.tax_rate = this.defaultValues['sales_tax_rate'];
                    this.formFields.parts_markup = this.defaultValues['parts_markup'];
                } catch (exception) {
                    console.log("exception: ", exception)
                }
            },
            async getAllInsAdj() {
                try {
                    const response = await axios.get(`/getAllInsAdj`);
                    this.insAdjList = response.data;
                } catch (exception) {
                    console.log("exception: ", exception)
                }
            },
            async getAllVendors() {
                try {
                    const response = await axios.get(`/getAllVendors`);
                    this.vendorsList = response.data;
                } catch (exception) {
                    console.log("exception: ", exception)
                }
            },
            async getAllServiceDesc() {
                try {
                    const response = await axios.get(`/getAllServiceDesc`);
                    this.serviceDescList = response.data;
                } catch (exception) {
                    console.log("exception: ", exception)
                }
            },
            async getAllEmployees() {
                try {
                    const response = await axios.get(`/getAllEmployees`);
                    this.allEmpValues = response.data;
                } catch (exception) {
                    console.log("exception: ", exception)
                }
            },
            show() {
                this.addNewOrder = true
            },
            hide() {
                const arrayNotToReset = ["storage_rate", "labor_rate", "tax_rate", "parts_markup"];

                if(this.addNew) {
                    setTimeout(() => {
                        var self = this;
                        Object.keys(self.formFields).forEach(function(key,index) {
                            if(typeof self.formFields[key] === "string" && arrayNotToReset.indexOf(key) == -1) 
                                self.formFields[key] = ''; 
                            else if (typeof self.formFields[key] === "boolean") 
                                self.formFields[key] = false;
                        });

                        this.addNewOrder = false;
                        this.$emit('reload:data');
                    }, 5000);
                } else {
                    var self = this;
                    Object.keys(self.formFields).forEach(function(key,index) {
                        if(typeof self.formFields[key] === "string" && arrayNotToReset.indexOf(key) == -1) 
                            self.formFields[key] = ''; 
                        else if (typeof self.formFields[key] === "boolean") 
                            self.formFields[key] = false;
                    });

                    this.addNewOrder = false;
                    this.$emit('reload:data');
                }
            },
            async submit() {
                this.submitFailed = false;

                this.formFields.storage_cost = this.storageCost;
                this.formFields.sub_total = this.custCost + this.laborCost;

                try {
                    await axios.post(`/addNewOrder`,this.formFields);
                } catch (exception) {
                    this.submitFailed = true;
                }
                
                if(this.submitFailed == false) {
                    this.addNew = true;
                    this.hide();
                }
            },
        }
    }
</script>