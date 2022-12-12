<template>
    <div class="container">
        <div class="p-2 pageHeading">Delivered Orders</div>
        <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Name,date created" /></div>
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
                        <td>{{item.date_created}}</td>
                        <td>{{item.first_name}} {{item.last_name}}</td>
                        <td>{{item.total_bill}}</td>
                        <td>{{item.balance}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <display-order :form-type="formType" ref="displayOrder" :order-info="selectedItem" @reload:data="getAllDeliveredOrders" />
    </div>
</template>

<script>
    import axios from 'axios';
    import DisplayOrder from '../workInPages/newOrderPages/DisplayOrder.vue';

    export default {
	    name:"NewOrders",
        components:{
            DisplayOrder
        },
        props:{
            formType: String
        },
        data(){
            return {
                loadingFailed: false,
                selectedItem:{},
                tableHeader: ["Order No.", "Order Date", "Customer", "Total", "Balance"],
                tableBody: [],
                invoices: [],
                loading: false,
                subTotal: 0,
                payDets: [],
                distinctOrderIds: [],
                searchQuery: null
            };
        },
        async mounted() {
            await this.getAllDeliveredOrders();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v => (item.date_created.toLowerCase().includes(v) || item.first_name.toLowerCase().includes(v) || item.last_name.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllDeliveredOrders() {
                this.loading = true;
                this.distinctOrderIds = [];
                try {
                    const response = await axios.get(`getAllDeliveredOrders`);
                    this.tableBody = response.data['orders'];
                    this.invoices = response.data['invoiceList'];
                    this.payDets = response.data['allPayDets'];

                    this.tableBody.forEach((item) => {
                        item.total_bill = 0;
                        item.balance = 0;
                        item.amount_paid = 0;
                        this.distinctOrderIds.push(item.id);
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
               this.distinctOrderIds.forEach((orderId) => {
                    let invoiceArrayPerOrder = this.invoices[orderId];

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
            escape() {
                this.$emit("escape:form",this.formType)
            },
            openItem(item) {
                this.selectedItem = item;
                this.$nextTick(()=>{
                    this.$refs.displayOrder.show();
                });
            },
        }
    }
</script>