<template>
  <div v-if="displayInvoicesForVendorWoPo">
    <div id="modal">
      <div class="modal-content">
            <div class="p-2 pageHeading">
                Without Po Vendor Data<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <div class="row">
                <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Check#, Pay For" /></div>
            </div>
            <br/>
            <div class="formHeadingFooter p1">
                <span>{{currentTime}}</span>
                Vendor ID: <span style="font-weight: bolder; color: #000;">{{vendorInfo.vendor_id}}</span>
                <span>{{currentDate}}</span>
            </div>
            <br/>
            <h3 v-if="payDets.length==0" class="p-3">
                No listings found.
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
                            <td>{{item.check_number}}</td>
                            <td>{{item.pay_for}}</td>
                            <td>{{item.payment_date}}</td>
                            <td>{{item.amount_paid}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
  </div>
</template>

<script>
    import moment from 'moment';
    
    export default {
    name: "DisplayInvoicesForVendorWoPo",
    props: [
         "vendorInfo", "payDets"
    ],
    data() {
        return {
            displayInvoicesForVendorWoPo: false,
            tableBody:[],
            selectedItem: {},
            tableHeader: ["Check#", "Pay For", "Pay Date", "Amount"],
            currentDate: "",
            currentTime: "",
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
                return this.payDets.filter((item)=>{
                    return this.searchQuery.toLowerCase().split(' ').every(v => (item.check_number.toLowerCase().includes(v) || item.pay_for.toLowerCase().includes(v)))
                })
            }else{
                return this.payDets;
            }
        },
    },
    methods: {
        show() {
            this.searchQuery = null;
            this.displayInvoicesForVendorWoPo = true;
        },
        hide() {
            this.displayInvoicesForVendorWoPo = false;
        },
    }
}
</script>