<template>
    <div v-if="displayAllOrdersPerCustomer">
        <div id="modal">
            <div class="modal-content">
                <div class="p-2 pageHeading">
                    Order Details For Customer<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="row">
                    <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Vendor Name,Check#, Payment Date" /></div>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    Customer Name: <span>{{customerInfo.first_name}} {{customerInfo.last_name}}</span>
                    Customer ID: <span>{{customerInfo.id}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <br/>
                <h3 v-if="tableBody.length==0" class="p-3">
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
                            <tr v-for="(item,index) in resultQuery" v-bind:key="index" @click="openItem(item)">
                                <td>{{item.id}}</td>
                                <td>{{item.date_created}}</td>
                                <td>{{item.delivery_date !== null ? 'yes' : 'no'}}</td>
                                <td>{{item.total_bill}}</td>
                                <td>{{item.last_paid}}</td>
                                <td>{{item.amount_paid}}</td>
                                <td>{{item.balance}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <show-payment-details ref="showPaymentDetails" :order-info="selectedItem" @reload:orders="getAllOrdersForGivenCustomer" />
    </div>
</template>

<script>
import moment from 'moment';
import axios from 'axios';
import ShowPaymentDetails from '../accRecUpdate/ShowPaymentDetails.vue';
    
export default {
    name: "DisplayAllOrdersPerCustomer",
    props: ["customerInfo"],
    components:{
        ShowPaymentDetails
    },
    data() {
        return {
            displayAllOrdersPerCustomer: false,
            currentDate: "",
            currentTime: "",
            tableHeader: ['Order ID', 'Order Date', 'Delivered?', 'Total', 'Last Paid', 'Amount Paid', 'Balance'],
            tableBody: [],
            distinctOrderIds: [],
            invoices: [],
            payDets: [],
            selectedItem: {},
            searchQuery: null,
        }
    },
    mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    computed:{
        resultQuery() {
            if(this.searchQuery){
                return this.tableBody.filter((item)=>{
                    return this.searchQuery.split(' ').every(v => (item.id == (v) || item.total_bill == (v) || item.amount_paid == (v) || item.balance == (v)))
                })
            }else{
                return this.tableBody;
            }
        },
    },
    methods: {
        async getAllOrdersForGivenCustomer() {
            const params = { params: { cust_id: this.customerInfo.id}};
            this.distinctOrderIds = [];
            const response = await axios.get(`getAllOrdersForGivenCustomer`, params);
            this.tableBody = response.data['orders'];
            this.invoices = response.data['invoiceList'];
            this.payDets = response.data['allPayDets'];

            this.tableBody.forEach((item) => {
                item.total_bill = 0;
                item.balance = 0;
                item.amount_paid = 0;
                item.last_paid = '';
                this.distinctOrderIds.push(item.id);
            });
            this.calculateTotal();
            this.calculateAmountPaid();
            this.calculateBalance();
            this.calculateLastPayDate();
        },
        calculateLastPayDate() {
            this.distinctOrderIds.forEach((orderId) => {
                let payDetArrayPerOrder = this.payDets[orderId];

                if(payDetArrayPerOrder && payDetArrayPerOrder.length > 0) {
                    payDetArrayPerOrder.forEach((dets) => {
                        this.tableBody = this.tableBody.map((element) => {
                            if(element.id == orderId) {
                                let newDate = element.last_paid > dets.payment_date ? element.last_paid : dets.payment_date;
                                return {...element, last_paid: newDate};
                            }
                            return element;
                        });
                    });
                } 
            });
        },
        calculateTotal() {
            this.distinctOrderIds.forEach((orderId) => {
                let invoiceArrayPerOrder = this.invoices[orderId];

                if(invoiceArrayPerOrder && invoiceArrayPerOrder.length > 0) {
                    this.subTotal = invoiceArrayPerOrder.reduce(function (result, item) {
                        result = result + item.sub_total_with_tax + item.total_labor_cost;
                        return result;
                    }, 0);
                    
                    this.tableBody = this.tableBody.map((element) => {
                        if(element.id == orderId) {
                            let newTotal = element.total_bill + this.subTotal + element.towing + element.waste + (element.storage_cost);
                            
                            return {...element, total_bill: newTotal};
                        }
                        return element;
                    });
                }
                
            });
        },
        calculateAmountPaid(){
            this.distinctOrderIds.forEach((orderId) => {
                let payDetArrayPerOrder = this.payDets[orderId];

                if(payDetArrayPerOrder && payDetArrayPerOrder.length > 0) {
                    this.totalAmountPaid = payDetArrayPerOrder.reduce(function (result, item) {
                        result = result + item.amount_paid;
                        return result;
                    }, 0);
                    
                    this.tableBody = this.tableBody.map((element) => {
                        if(element.id == orderId) {
                            return {...element, amount_paid: this.totalAmountPaid};
                        }
                        return element;
                    });
                }
                
            });
        },
        calculateBalance() {
            this.distinctOrderIds.forEach((orderId) => {
                this.tableBody = this.tableBody.map((element) => {
                    if(element.id == orderId) {
                        let newBalance = element.total_bill - element.amount_paid;
                        
                        return {...element, balance: newBalance};
                    }
                    return element;
                });
            });
        },
        async show() {
            this.displayAllOrdersPerCustomer = true;
            this.searchQuery = null;
            
            await this.getAllOrdersForGivenCustomer();
        },
        hide() {
            this.displayAllOrdersPerCustomer = false;
        },
        openItem(item) {
            this.selectedItem = item;
            this.$nextTick(()=>{
                this.$refs.showPaymentDetails.show();
            });
        }
    }
}
</script>
