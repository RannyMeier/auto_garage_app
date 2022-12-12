<template>
  <div v-if="showPaymentsPerVendor">
    <div id="modal">
      <div class="modal-content">
            <div class="p-2 pageHeading">
                Display All Payments for Current Vendor<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <div class="row">
                <div class="col-8"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Check#, Payment Date, Pay For, Service, Invoice" /></div>
                <div class="col-3"><button type="button" class="form-control footerButton p-2" @click="addPayment">Add New Payment</button></div>
            </div>
            <br/>
            <div class="formHeadingFooter p1">
                <span>{{currentTime}}</span>
                Vendor ID.: <span style="font-weight: bolder; color: #000;">{{vendorInfo.id}}</span>
                <span>{{currentDate}}</span>
            </div>
            <br/>
            <h3 v-if="tableBody.length==0" class="p-3">
                No listings found. Please click the Add button to add a new payment.
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
                            <td>{{item.card_check_number}}</td>
                            <td>{{item.payment_date}}</td>
                            <td>{{item.invoice_id}}</td>
                            <td>{{item.service_name}}</td>
                            <td>{{item.pay_for}}</td>
                            <td>{{item.amount_paid}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
    <add-vendor-payment ref="addVendorPayment" :vendor-info="vendorInfo" :form-type="formType" @reload:data="getAllPaymentDetailsPerVendor" />
  </div>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';
    import AddVendorPayment from './AddVendorPayment.vue';

    export default {
    name: "ShowPaymentsPerVendor",
    components:{
        AddVendorPayment
    },
    props: [
         "vendorInfo", "formType"
    ],
    data() {
        return {
            showPaymentsPerVendor: false,
            tableBody:[],
            selectedItem: {},
            tableHeader: ["Check#", "Payment Date", "Invoice ID", "Description", "Paid For", "Amount Paid"],
            currentDate: "",
            currentTime: "",
            payDets:[],
            distinctInvoiceIds: [],
            searchQuery: null,
        };
    },
    async mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    computed:{
        resultQuery() {
            if(this.searchQuery){
                return this.tableBody.filter((item)=>{
                    return this.searchQuery.toLowerCase().split(' ').every(v => (item.card_check_number.toLowerCase().includes(v) || item.payment_date.toLowerCase().includes(v) || item.service_name.toLowerCase().includes(v) || item.pay_for.toLowerCase().includes(v) || item.invoice_id == (v)))
                })
            }else{
                return this.tableBody;
            }
        },
    },
    methods: {
        async getAllPaymentDetailsPerVendor() {
            const params = {params: {vendor_id: this.vendorInfo.id}};

            try {
                const response = await axios.get(`/getAllPaymentDetailsPerVendor`, params);
                this.tableBody = response.data;
            } catch (exception) {
                console.log("exception: ", exception)
            }
        },
        async show() {
            this.searchQuery = null;
            await this.getAllPaymentDetailsPerVendor();
            this.showPaymentsPerVendor = true;
        },
        hide() {
            this.showPaymentsPerVendor = false;
        },
        addPayment() {
            this.$nextTick(() => {
                this.$refs.addVendorPayment.show();
            });
        },
    }
}
</script>