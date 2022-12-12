<template>
  <div v-if="addNewInvoice">
    <div id="modal">
      <div class="small-modal-content">
            <div class="pb-2 pageHeading">
                Add New Invoice<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <div class="formHeadingFooter p1">
                <span>{{currentTime}}</span>
                Order ID:<span style="font-weight: bolder; color: #000;">{{orderInfo.id}}</span>
                <span>{{currentDate}}</span>
            </div>
            <h4 v-if="addNew" style="color: darkgreen;">Successfully added new entry. Please stay and we will redirect you.</h4>
            <strong class="p-2">Fields marked with red stars are required fields.</strong>
            <strong class="p-2" v-if="vehicleAlreadyDelivered">Cannot submit this invoice as order has been delivered already.</strong>
            <form class="formBody">
                <div class="row">
                    <label>Customer<span class="req">*</span>: </label> <span>{{orderInfo.first_name}} {{orderInfo.last_name}} - {{orderInfo.cust_id}}</span>
                    <br/>
                    <hr/>
                    <label>Select a Insurance Adjuster: </label>
                    <select v-model="orderInfo.ins_adj_id" style="width: 45%;margin-left:2rem;" disabled>
                        <option v-for="item in insAdjList" v-bind:key="item.id" :value="item.id">
                            {{item.adjuster_name}} - {{item.company_name}}
                        </option>
                    </select>
                    <br/>
                    <hr/>
                    <div v-if="carsOfCustomer.length > 0">
                        <label>Select a vehicle<span class="req">*</span>: </label>
                        <select v-model="orderInfo.vehicle_id" style="width: 45%;margin-left:2rem;" disabled>
                            <option v-for="item in carsOfCustomer" v-bind:key="item.id" :value="item.id">
                                {{item.model_name}}
                            </option>
                        </select>
                        <br/>
                    </div>
                    <hr/>
                </div>
                <div class="row">
                    <hr/>
                    <div class="col-6">
                        Other Charges:
                        <br/>
                        <hr/>
                        <label>Waste: </label> <input type="text" name="waste" v-model="orderInfo.waste" placeholder="0.00" disabled/> <br/>
                        <label>Towing: </label> <input type="text" name="towing" v-model="orderInfo.towing" disabled/> <br/>
                        <label>Storage Days: </label> <input type="text" name="storage_days" v-model="orderInfo.storage_days" disabled/> <br/>
                        <label>Storage Rate: </label> <input type="text" name="storage_rate" v-model="orderInfo.storage_rate" disabled/> <br/>
                        Storage Cost = {{storageCost}}
                    </div>
                    <div class="col-6">
                        Update Default Rates for this customer?
                        <br/>
                        <hr/>
                        <label>Labor Rate:</label> <input type="text" name="labor_rate" v-model="orderInfo.labor_rate" placeholder="0.00" disabled/> <br/>
                        <label>Discount:</label> <input type="text" name="discount" v-model="orderInfo.discount" disabled/> <br/>
                        <label>Tax Rate:</label> <input type="text" name="tax_rate" v-model="orderInfo.tax_rate" disabled/> <br/>
                        <label>Customer Class (Base Markup):</label> <input type="text" name="parts_markup" v-model="orderInfo.parts_markup" disabled/><br/>
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
                        <select v-model="formFields.service_desc_id" style="width: 45%;margin-left:2rem;">
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
                        <b>Labor Hours Rate (@ {{orderInfo.labor_rate}}/hour): </b> 
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
    <add-new-vendors ref="addNewVendors" :send-back-id="true" @vendor:id="getVendorId" />
  </div>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';
    import AddNewVendors from '../../maintenancePages/vendors/AddNewVendors.vue';

    export default {
    name: "AddNewInvoice",
    components:{
        AddNewVendors
    },
    props:["orderInfo"],
    data() {
        return {
            submitFailed:false,
            addNewInvoice: false,
            currentDate: "",
            currentTime: "",
            vendorValue: '',
            insAdjList: [],
            vendorsList: [],
            serviceDescList: [],
            allEmpValues: [],
            defaultValues: [],
            carsOfCustomer:[],
            formFields: {
                date_created: '',
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
    mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    computed:{
        vehicleAlreadyDelivered(){
            return this.orderInfo.delivery_date != null;
        },
        disableSubmitButton(){
            return (this.formFields.employee_id == '' || this.formFields.service_desc_id == '' || this.vehicleAlreadyDelivered);
        },
        storageCost() {
            return this.orderInfo.storage_rate * this.orderInfo.storage_days;
        },
        custCost() {
            return (this.formFields.our_cost * this.formFields.quantity) + (this.formFields.our_cost * this.formFields.quantity * this.orderInfo.parts_markup);
        },
        paintMaterialCost() {
            return this.formFields.is_paint_material == true ? this.formFields.quantity * this.defaultValues['paint_material_rate'] : 0;
        },
        laborCost() {
            return this.formFields.labor_hours * this.orderInfo.labor_rate;
        }
    },
    methods: {
        async getVendorId(value) {
            if(value !== 0) {
                this.formFields.vendor_id = value.vendor.id;
                this.vendorValue = this.formFields.vendor_id;
                await this.getAllVendors();
            } else {
                this.vendorValue = '';
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
        async getAllVehiclesForGivenCustomer() {
            const params = {params:{cust_id: this.formFields.cust_id}};
            try {
                const response = await axios.get(`getAllVehiclesForGivenCustomer`, params);
                this.carsOfCustomer = response.data;
            } catch (exception) {
                console.log("exception: ", exception)
            }
        },
        async getDefaultValues(){
            try {
                const response = await axios.get(`/getDefaultValues`);
                this.defaultValues = response.data[0];
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
        async show() {
            await this.getAllVehiclesForGivenCustomer();
            await this.getAllEmployees();
            await this.getAllInsAdj();
            await this.getAllServiceDesc();
            await this.getAllVendors();
            await this.getDefaultValues();
            this.addNewInvoice = true;
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

                    this.addNewInvoice = false;
                    this.$emit('reload:invoicelist');
                }, 5000);
            } else {
                var self = this;
                Object.keys(self.formFields).forEach(function(key,index) {
                    if(typeof self.formFields[key] === "string" && arrayNotToReset.indexOf(key) == -1) 
                        self.formFields[key] = ''; 
                    else if (typeof self.formFields[key] === "boolean") 
                        self.formFields[key] = false;
                });

                this.addNewInvoice = false;
                this.$emit('reload:invoicelist');
            }
        },
        async submit() {
            this.submitFailed = false;
            this.formFields.sub_total = this.custCost + this.laborCost;
            this.formFields.order_id = this.orderInfo.id;
            this.formFields.cust_cost= this.custCost;

            try {
                await axios.post(`/addNewInvoice`, this.formFields);
            } catch(exception) {
                this.submitFailed = true;
            }

            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        }
    },
}
</script>