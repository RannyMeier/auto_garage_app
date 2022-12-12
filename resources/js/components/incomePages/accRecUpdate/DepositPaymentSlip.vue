<template>
    <div v-if="depositPaymentSlip">
        <div id="modal">
            <div class="modal-content">
                <div class="p-2 pageHeading">
                    Deposit Payment Slip<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    Order ID: <span>{{orderInfo.id}}</span>
                    Customer Name: <span>{{orderInfo.first_name}} {{orderInfo.last_name}}</span>
                    <span>{{currentDate}}</span>
                </div>                
                <h4 v-if="addNew" style="color: darkgreen;">Successfully added new entry. Please stay and we will redirect you.</h4>
                <form class="formBody">
                    <div>
                        <div>
                            <label>Old Balance: </label> {{orderInfo.balance}}
                            <br/>
                            <label>Payment Amount: </label> <input type="text" name="amount_paid" v-model="formFields.amount_paid"/>
                            <br/>
                            <label>New Balance: </label> {{newBalance}}
                            <br/>
                            <label style="width: 20%;">Payment Type: </label>
                            <select v-model="formFields.payment_type">
                                <option v-for="item in paymentTypes" v-bind:key="item.id" :value="item.id">
                                    {{item.name}}
                                </option>
                            </select>
                            <br/>
                            <div v-if="enterCreditCardNumber">
                                <label>Credit Card Number: </label><input type="text" name="credit_card_number" v-model="formFields.credit_card_number" />
                            </div>
                            <br/>
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
    </div>
</template>

<script>
import moment from 'moment';
import axios from 'axios';

export default {
    name: "DepositPaymentSlip",
    props: ["orderInfo", "paymentDetailsList"],
    data() {
        return {
            today: new Date().toISOString().slice(0,10),
            depositPaymentSlip: false,
            submitFailed: false,
            currentDate: "",
            currentTime: "",
            oldBalance: "",
            paymentTypes: [],
            formFields: {
                order_id: '',
                total_bill: '',
                amount_paid: '',
                balance: '',
                payment_type: '',
                credit_card_number: '',
            },
            newPaymentEntry: '',
            addNew: false
        }
    },
    async mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
        await this.getPaymentTypes();
    },
    computed:{
        disableSubmitButton() {
            const reqFields = this.formFields.amount_paid == '' || this.formFields.payment_type == '';
            const otherFields = this.enterCreditCardNumber && this.formFields.credit_card_number == '';
            return reqFields || otherFields || this.newBalance < 0 || isNaN(this.newBalance);
        },
        newBalance() {
            return parseFloat(this.orderInfo.balance) - this.checkVals(this.formFields.amount_paid);
        },
        enterCreditCardNumber() {
            return this.formFields.payment_type == 3 || this.formFields.payment_type == 4 || this.formFields.payment_type == 5;
        },
    },
    methods: {
        async getPaymentTypes() {
            try {
                const response = await axios.get(`getAllPaymentTypes`);
                this.paymentTypes = response.data;
            } catch (exception) {
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
        show() {
            this.formFields.order_id = this.orderInfo.id;
            this.formFields.total_bill = this.orderInfo.total_bill;
            this.depositPaymentSlip = true;
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
                    this.depositPaymentSlip = false;

                    if(this.newPaymentEntry != '') {
                        this.$emit('reload:data',this.newPaymentEntry);
                    } else {
                        this.$emit('reload:data');
                    }
                }, 5000);
            } else {
                 this.depositPaymentSlip = false;

                if(this.newPaymentEntry != '') {
                    this.$emit('reload:data',this.newPaymentEntry);
                } else {
                    this.$emit('reload:data');
                }
            }
        },
        async submit() {
            this.submitFailed = false;
            try {
                this.formFields.balance = this.newBalance;
                
                const response = await axios.post(`addNewPaymentDetail`, this.formFields);
                this.newPaymentEntry = response.data;
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
