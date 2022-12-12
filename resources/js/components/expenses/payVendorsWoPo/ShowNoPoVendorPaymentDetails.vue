<template>
    <div class="container">
        <div class="p-2 pageHeading">Pay Vendors (Without POs)</div>
        <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Check#, Payment Date, Pay For, Name" /></div>
            <div class="col-3"><button type="button" class="footerButton form-control p-2" @click="addPaymentInfo">Add Payment Info</button></div>
            <div class="col-2"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
        </div>
        <br/>
        <h3 v-if="tableBody.length==0" class="p-3">
            No listings found.
        </h3>
        <h3 v-else-if="loading" class="p-3">Loading...</h3>
        <div v-else class="col pb-2">
            <table class="table">
                <thead>
                    <tr>
                        <th v-for="item in tableHeader" v-bind:key="item">{{item}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item,index) in resultQuery" v-bind:key="index" @click="openItem(item)">
                        <td>{{item.check_number}}</td>
                        <td>{{item.payment_date}}</td>
                        <td>{{item.name}}</td>
                        <td>{{item.pay_for}}</td>
                        <td>{{item.amount_paid}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <show-payments-per-entry :form-type="formType" ref="showPaymentsPerEntry" :item-info="selectedItem" />
        <add-wo-po-payment ref="addWoPoPayment" @reload:data="getAllWoPo"/>
    </div>
</template>

<script>
    import axios from 'axios';
    import ShowPaymentsPerEntry from './ShowPaymentsPerEntry.vue';
    import AddWoPoPayment from './AddWoPoPayment.vue';

    export default {
	    name:"ShowNoPoVendorPaymentDetails",
        components:{
            ShowPaymentsPerEntry,
            AddWoPoPayment
        },
        props:{
            formType: String
        },
        data(){
            return {
                loadingFailed: false,
                selectedItem:{},
                tableHeader: ['Check #', 'Pay Date', 'Vendor Name', 'Pay For', 'Amount Paid'],
                tableBody: [],
                invoices: [],
                loading: false,
                searchQuery:null
            };
        },
        async mounted() {
            await this.getAllWoPo();
        },
        computed:{
        resultQuery() {
            if(this.searchQuery){
                return this.tableBody.filter((item)=>{
                    return this.searchQuery.toLowerCase().split(' ').every(v => (item.check_number.toLowerCase().includes(v) || item.payment_date.toLowerCase().includes(v) || item.name.toLowerCase().includes(v) || item.pay_for.toLowerCase().includes(v)))
                })
            }else{
                return this.tableBody;
            }
        },
    },
        methods:{
            async getAllWoPo() {
                this.loading = true;
                this.distinctVendorIds = [];
                try {
                    const response = await axios.get(`getAllWoPo`);
                    
                    this.tableBody = response.data;

                } catch (exception) {
                    console.log("exception: ", exception);
                }
                this.loading = false;
            },
            escape() {
                this.$emit("escape:form",this.formType)
            },
            openItem(item) {
                this.selectedItem = item;
                this.$nextTick(()=>{
                    this.$refs.showPaymentsPerEntry.show();
                });
            },
            addPaymentInfo() {
                this.$nextTick(()=>{
                    this.$refs.addWoPoPayment.show();
                });
            }
        }
    }
</script>