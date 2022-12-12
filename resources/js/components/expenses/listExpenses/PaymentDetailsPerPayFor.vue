<template>
    <div v-if="displayPaymentInfo">
        <div id="modal">
            <div class="modal-content">
                <div class="p-2 pageHeading">
                    Expense Data<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="row">
                    <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Vendor Name,Check#, Payment Date" /></div>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    From Date: <span>{{from}}</span>
                    To Date: <span>{{to}}</span>
                    For: <span>{{paymentInfo.length > 0 ? paymentInfo[0].pay_for : ''}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <div class="col pb-2 pt-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th v-for="item in tableHeader" v-bind:key="item">{{item}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in resultQuery" v-bind:key="index" @click="openItem(item)">
                                <td>{{item.check_number}}</td>
                                <td>{{item.vendor_id}}</td>
                                <td>{{item.name}}</td>
                                <td>{{item.payment_date}}</td>
                                <td>{{item.amount_paid}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <show-payments-per-entry :form-type="formType" ref="showPaymentsPerEntry" :item-info="selectedItem" />
    </div>
</template>
<script>
import moment from 'moment'
import ShowPaymentsPerEntry from '../payVendorsWoPo/ShowPaymentsPerEntry.vue';

export default {
    name: "PaymentDetailsPerPayFor",
    props: ["paymentInfo", "from", "to", "formType"],
    components:{
        ShowPaymentsPerEntry
    },
    data(){
        return {
            displayPaymentInfo: false,
            tableHeader: ["Check #", "Vendor ID", "Vendor Name", "Pay Date", "Amount"],
            currentDate: "",
            currentTime: "",
            selectedItem: "",
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
                return this.paymentInfo.filter((item)=>{
                    return this.searchQuery.toLowerCase().split(' ').every(v => (item.name.toLowerCase().includes(v) || item.payment_date.toLowerCase().includes(v) || item.check_number == (v)))
                })
            }else{
                return this.paymentInfo;
            }
        },
    },
    methods:{
        show() {
            this.searchQuery = null;
            this.displayPaymentInfo = true;
        },
        hide() {
            this.displayPaymentInfo = false;
        },
        openItem(item) {
            this.selectedItem = item;
            this.$nextTick(()=>{
                this.$refs.showPaymentsPerEntry.show();
            });
        },
    }
}
</script>