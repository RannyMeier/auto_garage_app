<template>
    <div v-if="addVendorPayment">
        <div id="modal">
            <div class="modal-content">
                <div class="p-2 pageHeading">
                    Deposit Payment Slip<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    Vendor ID: <span>{{vendorInfo.id}}</span>
                    Vendor Name: <span>{{vendorInfo.name}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully added new entry. Please stay and we will redirect you.</h4>
                <form class="formBody">
                    <div>
                        <div>
                            <label style="width: 20%;">Invoice ID: </label>
                            <select v-model="formFields.invoice_id" @click="invoiceChosen">
                                <option v-for="item in invoices" v-bind:key="item.invoice_id" :value="item.invoice_id">
                                    {{item.invoice_id}}
                                </option>
                            </select>
                            <br/>
                            <div v-if="formFields.invoice_id !== ''">
                                <label>Old Balance: </label> {{oldBalance}}
                                <br/>
                                <label>Payment Amount: </label> <input type="text" name="amount_paid" v-model="formFields.amount_paid"/>
                                <br/>
                                <label>New Balance: </label> {{newBalance}}
                                <br/>
                                <label style="width: 20%;">Pay For: </label>
                                <select v-model="payForValue" @click="chosenPayFor">
                                    <option value=""></option>
                                    <option value="add_new_pay_for">Add New PayFor</option>
                                    <option v-for="item in payFor" v-bind:key="item.id" :value="item.id">
                                        {{item.pay_for}}
                                    </option>
                                </select>
                                <br/>
                                <label>Check Number: </label><input type="text" name="card_check_number" v-model="formFields.card_check_number" />
                                <br/>
                                <label>Memo: </label><input type="text" name="memo" v-model="formFields.memo" />
                                <br/>
                            </div>
                        </div>
                    </div>
                    <div class="p-3">
                        <button class="btn btn-link" @click="hide">Cancel</button>
                        <button class="btn btn-primary btn-sm" @click.prevent="submit" :disabled="disableSubmitButton"> Submit</button>
                    </div>
                    <small v-if="submitFailed" style="color:red;">Something went wrong. Please try again. If problem persists, contact developer.</small>
                </form>
            </div>
        </div>
        <add-new-pay-for ref="addNewPayFor" @reload:data="getPayForId" />
    </div>
</template>

<script>
import moment from 'moment';
import axios from 'axios';
import AddNewPayFor from './AddNewPayFor.vue'

export default {
    name: "AddVendorPayment",
    components:{
        AddNewPayFor
    },
    props: ["vendorInfo"],
    data() {
        return {
            today: new Date().toISOString().slice(0,10),
            addVendorPayment: false,
            submitFailed: false,
            currentDate: "",
            currentTime: "",
            payForValue: '',
            invoices: [],
            paymentDetails: [],
            payFor:[],
            formFields: {
                invoice_id: '',
                amount_paid: '',
                card_check_number: '',
                pay_for_id: '',
                memo:''
            },
            newPaymentEntry: '',
            addNew: false,
        }
    },
    mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    computed:{
        disableSubmitButton() {
            const reqFields = this.formFields.amount_paid == '' || this.formFields.card_check_number == '';
            return reqFields || this.newBalance < 0 || isNaN(this.newBalance);
        },
        newBalance() {
            return parseFloat(this.oldBalance) - this.checkVals(this.formFields.amount_paid);
        },
        oldBalance() {
            return this.paymentDetails.length > 0 ? this.paymentDetails[0].balance : 0;
        }
    },
    methods: {
        async getPayForId(value) {
            if(value !== 0) {
                this.formFields.pay_for_id = value.pay_for.id;
                this.payForValue = this.formFields.pay_for_id;
                await this.getAllPayFor();
            } else {
                this.payForValue = '';
            }
        },
        async chosenPayFor() {
            if(this.payForValue == "add_new_pay_for"){
                this.$nextTick(()=>{
                    this.$refs.addNewPayFor.show();
                });
            } else {
                this.formFields.pay_for_id = this.payForValue;
                // await this.getAllPayFor();
            }    
        },
        async getAllInvoicesPerVendorNotPaid() {
            const params = {params: {vendor_id: this.vendorInfo.id}};
            try {
                const response = await axios.get(`getAllInvoicesPerVendorNotPaid`,params);
                this.invoices = response.data;
            }catch (exception) {
                console.log("exception: ", exception);
            }
        },
        async invoiceChosen() {
            const params = {params: {invoice_id: this.formFields.invoice_id}};
            
            try {
                const response = await axios.get(`getPaymentDetailsForInvoice`, params);
                this.paymentDetails = response.data;
            }catch (exception) {
                console.log("exception: ", exception);
            }
        },
        async getAllPayFor() {
            try {
                const response = await axios.get(`getAllPayFor`);
                this.payFor = response.data;
            }catch (exception) {
                console.log("exception: ", exception);
            }
        },
        checkVals(value) {
            if(typeof value != "string") {
                return value == null ? 0.00 : parseFloat(value);
            } else {
                return value.trim() == "" || value == null ? 0.00 : parseFloat(value)
            }
        },
        async show() {
            this.addVendorPayment = true;
            await this.getAllInvoicesPerVendorNotPaid();
            await this.getAllPayFor();
        },
        hide() {
            var self = this;
            Object.keys(self.formFields).forEach(function(key,index) {
                if(typeof self.formFields[key] === "string") 
                    self.formFields[key] = ''; 
                else if (typeof self.formFields[key] === "boolean") 
                    self.formFields[key] = false; 
                else {
                    self.formFields[key] = '';
                }
            });

            if(this.addNew) {
                setTimeout(() => {
                    this.addVendorPayment = false;
                    this.$emit('reload:data');
                }, 5000);
            } else {
                this.addVendorPayment = false;
                this.$emit('reload:data');
            }
        },
        async submit() {
            this.submitFailed = false;
            try {
                this.formFields.balance = this.newBalance;
                const amtPaid = parseFloat(this.paymentDetails[0].amount_paid) + parseFloat(this.formFields.amount_paid)
                this.formFields.amount_paid = amtPaid;
                await axios.post(`updateVendorPaymentDetails`, this.formFields);
                
            } catch (exception) {
                this.submitFailed = true;
                console.log("exception: ", exception)
            }

            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        }
    }
}
</script>
