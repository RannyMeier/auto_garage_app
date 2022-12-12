<template>
    <div v-if="showPaymentDetails">
        <div id="modal">
            <div class="modal-content">
                <div class="p-2 pageHeading">
                    Payment Details for Order Number: {{orderInfo.id}}<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="row">
                    <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Payment Type" /></div>
                    <div class="col-3"><button type="button" class="form-control footerButton p-2" @click="depositSlip">Insert Payment</button></div>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    Customer Name: <span>{{orderInfo.first_name}} {{orderInfo.last_name}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    Total Billed: <span>{{orderInfo.total_bill}}</span>
                    Total Paid: <span>{{headingAmountPaid}}</span>
                    Balance: <span>{{headingBalance}}</span>
                </div>
                <br/>
                <h3 v-if="paymentDetailsList.length==0" class="p-3">
                    No payment details found.
                </h3>
                <div v-else class="col pb-2 pt-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th v-for="item in tableHeader" v-bind:key="item">{{item}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in resultQuery" v-bind:key="index">
                                <td>{{item.amount_paid}}</td>
                                <td>{{item.balance}}</td>
                                <td>{{item.name}}</td>
                                <td>{{item.payment_date}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <deposit-payment-slip ref="depositPaymentSlip" :order-info="orderInfo" :payment-details-list="paymentDetailsList" @reload:data="reloadData"></deposit-payment-slip>
    </div>
</template>
<script>
import axios from 'axios';
import moment from 'moment';
import DepositPaymentSlip from './DepositPaymentSlip.vue';

export default {
    name: "showPaymentDetails",
    props : ["orderInfo"],
    components:{
        DepositPaymentSlip
    },
    data() {
        return {
            showPaymentDetails: false,
            currentDate: "",
            currentTime: "",
            tableHeader: ["Amount Paid", "Balance", "Payment Type", "Payment Date"],
            paymentDetailsList: [],
            headingAmountPaid: 0,
            headingBalance: 0,
            searchQuery: null
        }
    },
    mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    computed:{
        resultQuery() {
            if(this.searchQuery){
                return this.paymentDetailsList.filter((item)=>{
                    return this.searchQuery.toLowerCase().split(' ').every(v => (item.name.toLowerCase().includes(v)))
                })
            }else{
                return this.paymentDetailsList;
            }
        },
    },
    methods: {
        async reloadData(value) {
            if(value) {
                const newPaymentEntered = value.paymentEntry;
                this.headingAmountPaid = this.headingAmountPaid + parseFloat(newPaymentEntered['amount_paid']);
                this.headingBalance = parseFloat(newPaymentEntered['balance']);
                await this.getAllPaymentDetailsForGivenOrder();
            }
        },
        async getAllPaymentDetailsForGivenOrder() {
            const params = { params: { order_id: this.orderInfo.id } };

            try {
                const response = await axios.get(`getAllPaymentDetailsForGivenOrder`, params);
                this.paymentDetailsList = response.data;
            } catch (exception) {
                console.log("exception: ", exception)
            }
        },
        depositSlip() {
            this.$nextTick(()=>{
                this.$refs.depositPaymentSlip.show();
            });
        },
        async show() {
            this.showPaymentDetails = true;
            this.headingAmountPaid = parseFloat(this.orderInfo.amount_paid);
            this.headingBalance = parseFloat(this.orderInfo.balance);
            this.searchQuery = null;
            await this.getAllPaymentDetailsForGivenOrder();
        },
        hide() {
            this.showPaymentDetails = false;
            this.$emit('reload:orders');
        },   
    }
}
</script>