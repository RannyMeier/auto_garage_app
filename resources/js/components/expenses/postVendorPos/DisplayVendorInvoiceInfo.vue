<template>
  <div v-if="displayVendorInviceInfo">
    <div id="modal">
      <div class="modal-content">
            <div class="pb-2 pageHeading">
                Display/Edit Info for Selected Invoice<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <div class="formHeadingFooter p1">
                <span>{{currentTime}}</span>
                Invoice ID: <span style="font-weight: bolder; color: #000;">{{invoiceItem.id}}</span>
                <span>{{currentDate}}</span>
            </div>
            <div v-if="!vendorInvoiceResults">
                <h3>No records found.</h3>
            </div>                
            <h4 v-if="addNew" style="color: darkgreen;">Successfully updated entry. Please stay and we will redirect you.</h4>
            <form v-else class="formBody">
                <div class="row">
                    <div class="col-6">
                        <span>Service Description: {{vendorInvoiceResults.service_name}}</span>
                        <br />
                        <hr/>
                        <label>Quantity: </label> <input type="text" name="quantity" v-model="formFields.quantity" /> 
                        <br/>
                        <hr/>
                        <label>Our Cost: </label> <input type="text" name="our_cost" v-model="formFields.our_cost" /> 
                        <br/>
                        <hr/>
                        <label>Parts #: </label> <input type="text" name="parts_num" v-model="formFields.parts_num" /> 
                        <br/>
                        <hr/>
                        <span>Total: {{totalCost}}</span>
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

    export default {
    name: "DisplayVendorInvoiceInfo",
    props: [
        "invoiceItem", "formType"
    ],
    data() {
        return {
            displayVendorInviceInfo: false,
            submitFailed:false,
            created_at_date: '',
            currentDate: "",
            currentTime: "",
            formFields: {
                quantity: '',
                our_cost: '',
                parts_num: '',
            },
            vendorInvoiceResults:[],
            addNew: false,
        };
    },
    mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    computed: {
        disableUpdateButton() {
            return false;
            // return this.formFields.quantity == '' || this.formFields.our_cost == '' || this.formFields.parts_num == '';
        },
        totalCost() {
            return (this.formFields.our_cost * this.formFields.quantity);
        },
    },
    methods: {
        async getInvoiceAndVendorInfo() {
            const params = {params:{invoice_id: this.invoiceItem.id}};
            try {
                const response = await axios.get(`getInvoiceAndVendorInfo`, params);
                this.vendorInvoiceResults = response.data[0];
                
                if(this.vendorInvoiceResults){
                    this.formFields.quantity = this.vendorInvoiceResults.quantity;
                    this.formFields.our_cost = this.vendorInvoiceResults.our_cost;
                    this.formFields.parts_num = this.vendorInvoiceResults.parts_num;
                }
            } catch (exception) {
                console.log("exception: ", exception)
            }
        },
        async show() {
            this.displayVendorInviceInfo = true;
            await this.getInvoiceAndVendorInfo();
        },
        hide() {
            this.formFields = {};
            this.submitFailed = false;

            var self = this;
            Object.keys(self.formFields).forEach(function(key,index) {
                if(typeof self.formFields[key] === "string" && arrayNotToReset.indexOf(key) == -1) 
                    self.formFields[key] = ''; 
                else if (typeof self.formFields[key] === "boolean") 
                    self.formFields[key] = false;
            });

            if(this.addNew) {
                setTimeout(() => {
                    this.displayVendorInviceInfo = false;
                    this.$emit('reload:invoicelist');
                }, 5000);
            } else {
                this.displayVendorInviceInfo = false;
                this.$emit('reload:invoicelist');
            } 
        },
        async update() {
            this.submitFailed = false;

            this.formFields.invoice_id = this.invoiceItem.id;
            this.formFields.quantity = parseFloat(this.formFields.quantity);
            this.formFields.our_cost = parseFloat(this.formFields.our_cost);
            this.formFields.parts_num = parseFloat(this.formFields.parts_num);
           
            try {
                await axios.post(`/updateInfoForVendorInvoicePostOps`,this.formFields);
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