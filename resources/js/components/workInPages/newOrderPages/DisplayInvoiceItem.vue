<template>
  <div v-if="showInvoiceDetails">
    <div id="modal">
      <div class="modal-content">
            <div class="pb-2 pageHeading">
                Display/Edit Info for Selected Invoice<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <div class="formHeadingFooter p1">
                <span>{{currentTime}}</span>
                Invoice ID: <span style="font-weight: bolder; color: #000;">{{invoiceItem.id}}</span>
                Order ID: <span style="font-weight: bolder; color: #000;">{{orderInfo.id}}</span>
                <span>{{currentDate}}</span>
            </div>
            <h4 v-if="addNew" style="color: darkgreen;">Successfully updated entry. Please stay and we will redirect you.</h4>
            <strong class="p-2">Fields marked with red stars are required fields.</strong>
            <strong class="p-2" v-if="vehicleAlreadyDelivered">Cannot update this invoice as order has been delivered already.</strong>
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
                        <label for="date_created">Date:</label><input type="date" id="date_created" name="date_created" v-model="created_at_date" class="ml-1" :disabled="disableInputFields">
                        <br/>
                        <hr/>
                        <label>Vendor: </label>
                        <select v-model="formFields.vendor_id" style="width: 45%;margin-left:2rem;" :disabled="disableInputFields">
                            <option v-for="item in vendorsList" v-bind:key="item.id" :value="item.id">
                                {{item.name}}
                            </option>
                        </select>
                        <br/>
                        <hr/>
                        <label>Quantity: </label> <input type="text" name="quantity" v-model="formFields.quantity" :disabled="disableInputFields"/> 
                        <br/>
                        <hr/>
                        <label>Parts#: </label> <input type="text" name="parts_num" v-model="formFields.parts_num" :disabled="disableInputFields"/> 
                        <br/>
                        <hr/>
                         Sublet or Not?: <input type="checkbox" id="is_sublet" name="is_sublet" v-model="formFields.is_sublet" :disabled="disableInputFields">
                        <br />
                        <div v-if="formFields.is_sublet">
                            <label>Sublet Amount: </label> <input type="text" name="sublet_amount" v-model="formFields.sublet_amount" placeholder="0.00" :disabled="disableInputFields"/> 
                            <br/>
                        </div>
                        <div v-else>
                            <label>Our Cost: </label> <input type="text" name="our_cost" v-model="formFields.our_cost" :disabled="disableInputFields"/> 
                            <br/>
                            Customer Cost after calculation is: {{custCost}}
                            <br/>
                        </div>
                        <br/>
                        <hr/>
                        <label>Description<span class="req">*</span>: </label>
                        <select v-model="formFields.service_desc_id" style="width: 45%;margin-left:2rem;" :disabled="disableInputFields">
                            <option v-for="item in serviceDescList" v-bind:key="item.id" :value="item.id">
                                {{item.service_name}}
                            </option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Estimated Cost: </label> <input type="text" name="est_cost" v-model="formFields.est_cost" :disabled="disableInputFields"/> 
                        <br/>
                        <hr/>
                        Use Paint/Material?: <input type="checkbox" id="is_paint_material" name="is_paint_material" v-model="formFields.is_paint_material" :disabled="disableInputFields">
                        <br/>
                        <span>Paint Material Cost: {{paintMaterialCost}}</span>
                        <br />
                        <hr/>
                        <label>Select an employee<span class="req">*</span>: </label>
                        <select v-model="formFields.employee_id" style="width: 45%;margin-left:2rem;" :disabled="disableInputFields">
                            <option v-for="item in allEmpValues" v-bind:key="item.id" :value="item.id">
                                {{item.first_name}} {{item.last_name}}
                            </option>
                        </select>
                        <br/>
                        <hr/>
                        <b>Labor Hours Rate (@ {{orderInfo.labor_rate}}/hour): </b> 
                        <br/>
                        <input type="text" name="labor_hours" v-model="formFields.labor_hours" :disabled="disableInputFields"/> 
                        <br/>
                        Labor Cost = {{laborCost}} 
                        <br/>
                        <hr/>
                        <label>Estimated Labor: </label> <input type="text" name="estimated_labor" v-model="formFields.estimated_labor" :disabled="disableInputFields"/> 
                        <br/>
                    </div>
                    <hr/>
                </div>
                <div class="p-3">
                    <button class="btn btn-link" @click="hide">Cancel</button>
                    <button class="btn btn-primary btn-sm" @click.prevent="update" :disabled="disableUpdateButton"> Update</button>
                </div>
                <small v-if="submitFailed" style="color:red;">Something went wrong. Please try again. If problem persists, contact developer.</small>
            </form>
        </div>
    </div> 
  </div>
</template>

<script>
    import moment from 'moment';

    const DISABLE_INPUT_FOR_FORM_TYPES = ['history_order', 'work_done_by_date', 'cust_val_income', 'history_customer','history_vehicle','history_insurance'];
    
    export default {
    name: "DisplayInvoiceItem",
    props: [
        "invoiceItem", "orderInfo", "formType"
    ],
    data() {
        return {
            showInvoiceDetails: false,
            submitFailed:false,
            created_at_date: '',
            currentDate: "",
            currentTime: "",
            formFields: {},
            insAdjList: [],
            vendorsList: [],
            serviceDescList: [],
            allEmpValues: [],
            defaultValues: [],
            carsOfCustomer:[],
            addNew: false,
        };
    },
    async mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
        await this.getAllInsAdj();
        await this.getAllVendors();
        await this.getAllServiceDesc();
        await this.getAllEmployees();
        await this.getDefaultValues();
        await this.getAllVehiclesForGivenCustomer();
    },
    computed: {
        vehicleAlreadyDelivered(){
            return this.orderInfo.delivery_date != null || DISABLE_INPUT_FOR_FORM_TYPES.includes(this.formType);
        },
        disableInputFields(){
            return DISABLE_INPUT_FOR_FORM_TYPES.includes(this.formType);
        },
        disableUpdateButton() {
            const noFieldsUpdated = Object.entries(this.formFields).toString() === Object.entries(this.invoiceItem).toString();
            const requiredFieldsEmpty = this.formFields.employee_id == '' || this.formFields.service_desc_id == '';
            return (noFieldsUpdated || requiredFieldsEmpty || this.vehicleAlreadyDelivered) && new Date(this.formFields.date_created).toISOString().slice(0,10) == new Date(this.created_at_date).toISOString().slice(0,10);
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
                const response = await axios.get(`/getCurrentAndOldEmployees`);
                this.allEmpValues = response.data;
            } catch (exception) {
                console.log("exception: ", exception)
            }
        },
        show() {
            this.formFields = JSON.parse(JSON.stringify(this.invoiceItem));
            this.created_at_date = new Date(this.formFields.date_created).toISOString().slice(0,10);
            this.showInvoiceDetails = true;
        },
        hide() {
            if(this.addNew) {
                setTimeout(() => {
                    this.formFields = {};
                    this.submitFailed = false;

                    const arrayNotToReset = ["storage_rate", "labor_rate", "tax_rate", "parts_markup"];
                    var self = this;
                    Object.keys(self.formFields).forEach(function(key,index) {
                        if(typeof self.formFields[key] === "string" && arrayNotToReset.indexOf(key) == -1) 
                            self.formFields[key] = ''; 
                        else if (typeof self.formFields[key] === "boolean") 
                            self.formFields[key] = false;
                    });

                    this.showInvoiceDetails = false;
                    this.$emit('reload:invoicelist');
                }, 5000);
            } else {
                this.formFields = {};
                this.submitFailed = false;

                const arrayNotToReset = ["storage_rate", "labor_rate", "tax_rate", "parts_markup"];
                var self = this;
                Object.keys(self.formFields).forEach(function(key,index) {
                    if(typeof self.formFields[key] === "string" && arrayNotToReset.indexOf(key) == -1) 
                        self.formFields[key] = ''; 
                    else if (typeof self.formFields[key] === "boolean") 
                        self.formFields[key] = false;
                });

                this.showInvoiceDetails = false;
                this.$emit('reload:invoicelist');
            }
        },
        async update() {
            this.submitFailed = false;

            this.formFields.storage_cost = this.storageCost;
            this.formFields.sub_total = this.custCost + this.laborCost;
            this.formFields.date_created = this.created_at_date;

            try {
                await axios.post(`/updateInvoice`,this.formFields);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        },
    },
}
</script>