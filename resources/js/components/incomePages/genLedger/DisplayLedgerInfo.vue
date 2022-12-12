<template>
    <div v-if="displayLedgerInfo">
        <div id="modal">
            <div class="modal-content">
                <div class="p-2 pageHeading">
                    Ledger Info<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="row">
                    <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Payment Type, Name, Invoice ID" /></div>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    From Date: <span>{{from}}</span>
                    To Date: <span>{{to}}</span>
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
                            <tr v-for="(item,index) in resultQuery" v-bind:key="index">
                                <td>{{item.payment_date}}</td>
                                <td>{{item.name}}</td>
                                <td>{{item.first_name}} {{item.last_name}}</td>
                                <td>{{item.invoice_id}}</td>
                                <td>{{item.amount_paid_per_invoice}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import moment from 'moment'
export default {
    name: "DisplayLedgerInfo",
    props: ["ledgerInfo", "from", "to"],
    data(){
        return {
            displayLedgerInfo: false,
            tableHeader: ["Payment Date", "Payment Type", "Customer Name", "Invoice No.", "Amount Paid"],
            currentDate: "",
            currentTime: "",
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
                return this.ledgerInfo.filter((item)=>{
                    return this.searchQuery.toLowerCase().split(' ').every(v => (item.first_name.toLowerCase().includes(v) || item.last_name.toLowerCase().includes(v) || item.name.toLowerCase().includes(v) || item.invoice_id == (v)))
                })
            }else{
                return this.ledgerInfo;
            }
        },
    },
    methods:{
        show() {
            this.searchQuery = null;
            this.displayLedgerInfo = true;
        },
        hide() {
            this.displayLedgerInfo = false;
        },
    }
}
</script>