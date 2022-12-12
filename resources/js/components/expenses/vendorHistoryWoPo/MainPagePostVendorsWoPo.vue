<template>
    <div class="container">
        <div class="p-2 pageHeading">Without PO Vendor History</div>
        <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Vendor#, Name" /></div>
            <div class="col-2"></div>
            <div class="col-3"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
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
                        <td>{{item.vendor_id}}</td>
                        <td>{{item.name}}</td>
                        <td>{{item.amount_paid}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <display-invoices-for-vendor-wo-po :form-type="formType" ref="displayInvoicesForVendorWoPo" :vendor-info="selectedItem" :pay-dets="selectedItemPayDets" />
    </div>
</template>

<script>
    import axios from 'axios';
    import DisplayInvoicesForVendorWoPo from './DisplayInvoicesForVendorWoPo.vue';

    export default {
	    name:"MainPagePostVendorsWoPo",
        components:{
           DisplayInvoicesForVendorWoPo
        },
        props:{
            formType: String
        },
        data(){
            return {
                loadingFailed: false,
                selectedItem:{},
                selectedItemPayDets:'',
                tableHeader: ['Vendor ID', 'Vendor Name', 'Amount Paid Till Date'],
                tableBody: [],
                invoices: [],
                loading: false,
                totalAmountPaid: 0,
                payDets: [],
                distinctVendorIds: [],
                searchQuery: null,
            };
        },
        async mounted() {
            await this.getAllVendorsWoPoDetails();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v => (item.vendor_id == (v) || item.name.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllVendorsWoPoDetails() {
                this.loading = true;
                this.distinctVendorIds = [];
                try {
                    const response = await axios.get(`getAllVendorsWoPoDetails`);
                    
                    this.tableBody = response.data['vendors'];
                    this.payDets = response.data['vendorPayDetails'];
                    
                    this.tableBody.forEach((item) => {
                        item.amount_paid = 0;
                        this.distinctVendorIds.push(item.vendor_id);
                    });
                    this.calculateAmountPaid();

                } catch (exception) {
                    console.log("exception: ", exception);
                }
                this.loading = false;
            },
            calculateAmountPaid(){
                this.distinctVendorIds.forEach((vendorId) => {
                    let payDetArrayPerVendor = this.payDets[vendorId];

                    if(payDetArrayPerVendor && payDetArrayPerVendor.length > 0) {
                        this.totalAmountPaid = payDetArrayPerVendor.reduce(function (result, item) {
                            
                            result = result + item.amount_paid;
                            return result;
                        }, 0);
                        
                        this.tableBody = this.tableBody.map((element) => {
                            if(element.vendor_id == vendorId) {
                                return {...element, amount_paid: this.totalAmountPaid};
                            }
                            return element;
                        });
                    }
                    
                });
            },
            escape() {
                this.$emit("escape:form",this.formType)
            },
            openItem(item) {
                this.selectedItem = item;
                this.selectedItemPayDets = this.payDets[this.selectedItem.vendor_id];
                this.$nextTick(()=>{
                    this.$refs.displayInvoicesForVendorWoPo.show();
                });
            },
        }
    }
</script>