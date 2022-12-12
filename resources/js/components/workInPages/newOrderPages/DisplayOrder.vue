<template>
  <div v-if="displayOrder">
    <div id="modal">
      <div class="modal-content">
            <div class="pb-2 pageHeading">
                View/Edit Order Details<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <div class="formHeadingFooter p-1">
                <span>{{currentTime}}</span>
                Order ID:<span style="font-weight: bolder; color: #000;"> {{formFields.id}}</span>
                <span>{{currentDate}}</span>
            </div>
            <br/>
            <div class="formHeadingFooter p-1">
                <span>In: {{formFields.date_created}}</span>
                <span>Out: {{formFields.delivery_date}}</span>
            </div>
            <h4 v-if="addNew" style="color: darkgreen;">Successfully updated entry. Please stay and we will redirect you.</h4>
            <strong class="p-2" v-if="formType =='new'">Fields marked with red stars are required fields.</strong>
            <strong class="p-2" v-else>Only Vehicle Information can be edited. All other fields are view only. <br/>You can click "Display Invoices" button at the bottom to view/edit/add new invoices.</strong>
            <strong class="p-2" v-if="orderInfo.delivery_date != null">Cannot update this invoice as vehicle has been delivered already.</strong>
            <form class="formBody">
                <div class="row">
                    <label>Customer: </label>
                    <select v-model="formFields.cust_id" style="width: 45%;margin-left:2rem;" disabled>
                        <option v-for="item in allCustomersList" v-bind:key="item.id" :value="item.id">
                            {{item.first_name}} {{item.last_name}} - {{item.phone1}}
                        </option>
                    </select>
                    <br/>
                    <hr/>
                    <label>Select a Insurance Adjuster: </label>
                    <select v-model="formFields.ins_adj_id" style="width: 45%;margin-left:2rem;" :disabled="disableOrderDetails">
                        <option v-for="item in insAdjList" v-bind:key="item.id" :value="item.id">
                            {{item.adjuster_name}} - {{item.company_name}}
                        </option>
                    </select>
                    <br/>
                    <hr/>
                    <label>Select a vehicle<span class="req">*</span>: </label>
                    <select v-model="formFields.vehicle_id" style="width: 45%;margin-left:2rem;" :disabled="disableOrderDetails">
                        <option v-for="item in carsOfCustomer" v-bind:key="item.id" :value="item.id">
                            {{item.model_name}}
                        </option>
                    </select>
                    <br/>
                    <hr/>
                </div>
                <div class="row">
                    <hr/>
                    <div class="col-6">
                        Other Charges:
                        <br/>
                        <hr/>
                        <label>Waste: </label> <input type="text" name="waste" v-model="formFields.waste" placeholder="0.00" :disabled="disableOrderDetails"/> <br/>
                        <label>Towing: </label> <input type="text" name="towing" v-model="formFields.towing" :disabled="disableOrderDetails"/> <br/>
                        <label>Storage Days: </label> <input type="text" name="storage_days" v-model="formFields.storage_days" :disabled="disableOrderDetails"/> <br/>
                        <label>Storage Rate: </label> <input type="text" name="storage_rate" v-model="formFields.storage_rate" :disabled="disableOrderDetails"/> <br/>
                        Storage Cost = {{storageCost}}
                    </div>
                    <div class="col-6">
                        Update Default Rates for this customer?
                        <br/>
                        <hr/>
                        <label>Labor Rate:</label> <input type="text" name="labor_rate" v-model="formFields.labor_rate" placeholder="0.00" :disabled="disableOrderDetails"/> <br/>
                        <label>Discount:</label> <input type="text" name="discount" v-model="formFields.discount" :disabled="disableOrderDetails"/> <br/>
                        <label>Tax Rate:</label> <input type="text" name="tax_rate" v-model="formFields.tax_rate" :disabled="disableOrderDetails"/> <br/>
                        <label>Customer Class (Base Markup):</label> <input type="text" name="parts_markup" v-model="formFields.parts_markup" :disabled="disableOrderDetails"/><br/>
                    </div>
                <hr/>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label>Delivery Date:</label><input type="date" id="delivery_date" name="delivery_date" v-model="deliveryDate" class="ml-1" :disabled="disableDeliveryDateField">
                        <br/>
                    </div>
                </div>
                <div class="p-3">
                    <button class="btn btn-link" @click="hide">Cancel</button>
                    <button class="btn btn-primary btn-sm" @click.prevent="update" :disabled="disableUpdateButton"> Update</button>
                </div>
                <small v-if="submitFailed" style="color:red;">Something went wrong. Please try again. If problem persists, contact developer.</small>
            </form>
            <div class="formFooter">
                <button type="button" class="footerButton p-3" @click="displayInvoices">Display Invoices</button>
            </div>
        </div>
    </div> 
    <display-invoices-for-order ref="showInvoices" :order-info="orderInfo" :form-type="formType" />
  </div>
</template>

<script>
    import moment from 'moment';
    import DisplayInvoicesForOrder from './DisplayInvoicesForOrder.vue';
    import axios from 'axios';

    const DISABLE_INPUT_FOR_FORM_TYPES = ['aged', 'history_order', 'work_done_by_date', 'cust_val_income', 'history_customer','history_vehicle', 'history_insurance'];

    export default {
    name: "DisplayOrder",
    components: { DisplayInvoicesForOrder },
    props: [
        "orderInfo", "formType"
    ],
    data() {
        return {
            displayOrder: false,
            submitFailed: false,
            currentDate: "",
            currentTime: "",
            formFields: {},
            allCustomersList: [],
            carsOfCustomer: [],
            insAdjList: [],
            deliveryDate: '',
            addNew: false,
        };
    },
    async mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
        await this.getAllCustomers();
        await this.getAllInsAdj();
        
    },
    computed: {
        disableDeliveryDateField(){
            return this.orderInfo.delivery_date != null || DISABLE_INPUT_FOR_FORM_TYPES.includes(this.formType);
        },
        disableOrderDetails(){
            return this.formType !== 'new';
        },
        disableUpdateButton() {
            return (Object.entries(this.orderInfo).toString() === Object.entries(this.formFields).toString() || this.orderInfo.delivery_date != null) && new Date(this.formFields.delivery_date).toISOString().slice(0,10) == new Date(this.deliveryDate).toISOString().slice(0,10);
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
    methods: {
        async getAllVehiclesForGivenCustomer() {
            const params = {params:{cust_id: this.orderInfo.cust_id}};
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
        async getAllInsAdj() {
            try {
                const response = await axios.get(`/getAllInsAdj`);
                this.insAdjList = response.data;
            } catch (exception) {
                console.log("exception: ", exception)
            }
        },
        async show() {
            this.formFields = JSON.parse(JSON.stringify(this.orderInfo));
            this.deliveryDate = this.formFields.delivery_date !== null ? new Date(this.formFields.delivery_date).toISOString().slice(0,10) : null;
            await this.getAllVehiclesForGivenCustomer();
            this.displayOrder = true;
        },
        hide() {
            if(this.addNew) {
                setTimeout(() => {
                    this.formFields = {};

                    this.displayOrder = false;
                    this.$emit('reload:data');
                }, 5000);
            } else {
                this.formFields = {};

                this.displayOrder = false;
                this.$emit('reload:data');
            }
        },
        displayInvoices() {
            this.$nextTick(() => {
                this.$refs.showInvoices.show();
            });
        },
        async update() {
            this.submitFailed = false;
            this.formFields.storage_cost = this.storageCost;
            this.formFields.delivery_date = this.deliveryDate;

            try {
                await axios.post(`/updateOrder`,this.formFields);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        }
    }
}
</script>