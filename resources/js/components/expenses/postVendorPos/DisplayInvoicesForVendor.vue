<template>
  <div v-if="displayInvoicesForVendor">
    <div id="modal">
      <div class="modal-content">
            <div class="p-2 pageHeading">
                Display All Invoices for Current Vendor<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <div class="row">
                <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Service, Order#, Invoice#, Balance" /></div>
            </div>
            <br/>
            <div class="formHeadingFooter p1">
                <span>{{currentTime}}</span>
                Order No.: <span style="font-weight: bolder; color: #000;">{{vendorInfo.id}}</span>
                <span>{{currentDate}}</span>
            </div>
            <br/>
            <h3 v-if="tableBody.length==0" class="p-3">
                No listings found. Please click the Add button to add a new invoice.
            </h3>
            <div v-else class="col pb-2 pt-2">
                <table class="table">
                    <thead>
                        <tr>
                            <th v-for="item in tableHeader" v-bind:key="item">{{item}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item,index) in resultQuery" v-bind:key="index" @click="openInvoiceItem(item)">
                            <td>{{item.service_name}}</td>
                            <td>{{item.id}}</td>
                            <td>{{item.order_id}}</td>
                            <td>{{item.date_created}}</td>
                            <td>{{item.inv_vendor_balance}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
    <display-vendor-invoice-info ref="displayVendorInviceInfo" :invoice-item="selectedItem" :form-type="formType" @reload:invoicelist="getAllInvoicesForGivenVendor" />
  </div>
</template>

<script>
    import moment from 'moment';
    import DisplayVendorInvoiceInfo from './DisplayVendorInvoiceInfo.vue';
    import axios from 'axios';

    export default {
    name: "DisplayInvoicesForVendor",
    components:{
        DisplayVendorInvoiceInfo
    },
    props: [
         "vendorInfo", "formType"
    ],
    data() {
        return {
            displayInvoicesForVendor: false,
            tableBody:[],
            selectedItem: {},
            tableHeader: ["Description", "Invoice ID", "Order ID", "Date of Invoice", "Balance"],
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
                    return this.searchQuery.toLowerCase().split(' ').every(v => (item.service_name.toLowerCase().includes(v) || item.id == (v) || item.order_id == (v) || item.inv_vendor_balance == v))
                })
            }else{
                return this.tableBody;
            }
        },
    },
    methods: {
        async getAllInvoicesForGivenVendor() {
            const params = {params: {vendor_id: this.vendorInfo.id}};

            try {
                const response = await axios.get(`/getAllInvoicesForGivenVendor`, params);
                this.tableBody = response.data['invoices'];
                this.payDets = response.data['payDets'];

                this.tableBody.forEach((item) => {
                    item.inv_vendor_total = 0;
                    item.inv_vendor_balance = 0;
                    item.inv_vendor_amount_paid = 0;
                    this.distinctInvoiceIds.push(item.id);
                });
                this.calculateTotal();
                this.calculateAmountPaid();
                this.calculateBalance();
            } catch (exception) {
                console.log("exception: ", exception)
            }
        },
        calculateTotal() {
            this.distinctInvoiceIds.forEach((invoiceId) => {
                let payDetArrayPerInvoice = this.payDets[invoiceId];

                if(payDetArrayPerInvoice && payDetArrayPerInvoice.length > 0) {
                    this.subTotal = payDetArrayPerInvoice.reduce(function (result, item) {
                        
                        result = result + item.total_bill;
                        return result;
                    }, 0);
                    
                    this.tableBody = this.tableBody.map((element) => {
                        if(element.id == invoiceId) {
                            return {...element, inv_vendor_total: this.subTotal};
                        }
                        return element;
                    });
                }
            });
        },
        calculateAmountPaid(){
            this.distinctInvoiceIds.forEach((invoiceId) => {
                let payDetArrayPerInvoice = this.payDets[invoiceId];

                if(payDetArrayPerInvoice && payDetArrayPerInvoice.length > 0) {
                    this.totalAmountPaid = payDetArrayPerInvoice.reduce(function (result, item) {
                        result = result + item.amount_paid;
                        return result;
                    }, 0);
                    
                    this.tableBody = this.tableBody.map((element) => {
                        if(element.id == invoiceId) {
                            return {...element, inv_vendor_amount_paid: this.totalAmountPaid};
                        }
                        return element;
                    });
                }
                
            });
        },
        calculateBalance() {
            this.distinctInvoiceIds.forEach((invoiceId) => {
                this.tableBody = this.tableBody.map((element) => {
                    if(element.id == invoiceId) {
                        let newBalance = element.inv_vendor_total - element.inv_vendor_amount_paid;
                        
                        return {...element, inv_vendor_balance: newBalance};
                    }
                    return element;
                });
            });
        },
        async show() {
            this.searchQuery = null;
            await this.getAllInvoicesForGivenVendor();
            this.displayInvoicesForVendor = true;
        },
        hide() {
            this.displayInvoicesForVendor = false;
        },
        viewTotal() {
            this.$nextTick(() => {
                this.$refs.displayTotal.show();
            });
        },
        addNewInvoice() {
            this.$nextTick(() => {
                this.$refs.addNewInvoice.show();
            });
        },
        openInvoiceItem(item) {
            this.selectedItem = item;
            this.$nextTick(()=>{
                this.$refs.displayVendorInviceInfo.show();
            });
        }
    }
}
</script>