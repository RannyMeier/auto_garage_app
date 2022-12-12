<template>
    <div class="container">
        <div class="p-2 pageHeading">Post Vendor POs</div>
        <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Name,Amount Paid, Balance" /></div>
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
                        <td>{{item.id}}</td>
                        <td>{{item.name}}</td>
                        <td>{{item.amount_paid}}</td>
                        <td>{{item.balance}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <display-invoices-for-vendor :form-type="formType" ref="displayInvoicesForVendor" :vendor-info="selectedItem" @reload:data="getAllVendorPayDetails" />
    </div>
</template>

<script>
    import DisplayInvoicesForVendor from './DisplayInvoicesForVendor.vue';
    import axios from 'axios';

    export default {
	    name:"MainPagePostVendors",
        components:{
            DisplayInvoicesForVendor
        },
        props:{
            formType: String
        },
        data(){
            return {
                loadingFailed: false,
                selectedItem:{},
                tableHeader: ['Vendor ID', 'Vendor Name', 'Amount Paid Till Date', 'Balance'],
                tableBody: [],
                invoices: [],
                loading: false,
                subTotal: 0,
                payDets: [],
                distinctVendorIds: [],
                searchQuery: null,
            };
        },
        async mounted() {
            await this.getAllVendorPayDetails();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v => (item.name.toLowerCase().includes(v) || item.amount_paid == (v) || item.balance == (v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllVendorPayDetails() {
                this.loading = true;
                this.distinctVendorIds = [];
                try {
                    const response = await axios.get(`getAllVendorPayDetails`);
                    
                    this.tableBody = response.data['vendors'];
                    this.payDets = response.data['vendorPayDetails'];

                    this.tableBody.forEach((item) => {
                        item.total_bill = 0;
                        item.balance = 0;
                        item.amount_paid = 0;
                        this.distinctVendorIds.push(item.id);
                    });
                    this.calculateTotal();
                    this.calculateAmountPaid();
                    this.calculateBalance();

                } catch (exception) {
                    console.log("exception: ", exception);
                }
                this.loading = false;
            },
            calculateTotal() {
               this.distinctVendorIds.forEach((vendorId) => {
                    let payDetArrayPerVendor = this.payDets[vendorId];

                    if(payDetArrayPerVendor && payDetArrayPerVendor.length > 0) {
                        this.subTotal = payDetArrayPerVendor.reduce(function (result, item) {
                            result = result + item.total_bill;
                            return result;
                        }, 0);
                        
                        this.tableBody = this.tableBody.map((element) => {
                            if(element.id == vendorId) {
                                return {...element, total_bill: this.subTotal};
                            }
                            return element;
                        });
                    }
                });
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
                            if(element.id == vendorId) {
                                return {...element, amount_paid: this.totalAmountPaid};
                            }
                            return element;
                        });
                    }
                    
                });
            },
            calculateBalance() {
                this.distinctVendorIds.forEach((vendorId) => {
                    this.tableBody = this.tableBody.map((element) => {
                        if(element.id == vendorId) {
                            let newBalance = element.total_bill - element.amount_paid;
                            
                            return {...element, balance: newBalance};
                        }
                        return element;
                    });
                });
            },
            escape() {
                this.$emit("escape:form",this.formType)
            },
            openItem(item) {
                this.selectedItem = item;
                this.$nextTick(()=>{
                    this.$refs.displayInvoicesForVendor.show();
                });
            },
        }
    }
</script>