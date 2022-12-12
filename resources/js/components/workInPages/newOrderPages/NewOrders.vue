<template>
    <div class="container">
        <div class="p-2 pageHeading" v-if="formTypeValue =='new'">New Estimate and Orders</div>
        <div class="p-2 pageHeading" v-else-if="formTypeValue =='update'">Update Estimate and Orders</div>
        <div class="p-2 pageHeading" v-else>Aged Estimate and Orders</div>
        <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Name, Model Name" /></div>
            <div class="col-3"><button v-if="formTypeValue =='new'" type="button" class="form-control footerButton p-3" @click="addNewOrder">Add Order</button></div>
            <div class="col-2"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
        </div>
        <br/>
        <h3 v-if="resultQuery.length==0" class="p-3">
            No listings found. <span v-if="formTypeValue == 'new'">Please click the Add button to add a new order.</span>
        </h3>
        <h3 v-else-if="loading" class="p-3">Loading...</h3>
        <div v-else class="col pb-2">
            <table class="table">
                <thead>
                    <tr>
                        <th v-for="item in tableHeader" v-bind:key="item">{{item}}</th>
                    </tr>
                </thead>
                <tbody v-if="formTypeValue == 'new' || formTypeValue == 'update'">
                    <tr v-for="(item,index) in resultQuery" v-bind:key="index" @click="openItem(item)">
                        <td>{{item.id}}</td>
                        <td>{{item.first_name}} {{item.last_name}}</td>
                        <td>{{item.model_name}}</td>
                        <td>{{item.total_bill}}</td>
                        <td>{{item.balance}}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr v-for="(item,index) in resultQuery" v-bind:key="index" @click="openItem(item)">
                        <td>{{item.date_created}}</td>
                        <td>{{item.order_age}}</td>
                        <td>{{item.id}}</td>
                        <td>{{item.first_name}} {{item.last_name}}</td>
                        <td>{{item.model_name}}</td>
                        <td>{{item.balance}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <add-new-order ref="addNewOrder" @reload:data="getAllOrders" />
        <display-order :form-type="formTypeValue" ref="displayOrder" :order-info="selectedItem" @reload:data="getAllOrders" />
    </div>
</template>

<script>
    import AddNewOrder from './AddNewOrder.vue';
    import DisplayOrder from './DisplayOrder.vue';
    import axios from 'axios';

    export default {
	    name:"NewOrders",
        components:{
            AddNewOrder,
            DisplayOrder
        },
        props:{
            formType: String
        },
        data(){
            return {
                loadingFailed: false,
                selectedItem:{},
                tableHeader: '',
                tableHeaderNewOrUpdate:["Order No.", "Customer", "Car's Make and Model", "Total", "Balance"],
                tableBody: [],
                tableHeaderAged:["Date In", "Aged", "Order No.", "Customer", "Car's Make and Model", "Balance"],
                invoices: [],
                loading: false,
                subTotal: 0,
                payDets: [],
                distinctOrderIds: [],
                searchQuery: null
            };
        },
        async mounted() {
            this.tableHeader = this.formType == "work_in_new" || this.formType == "work_in_update" ? this.tableHeaderNewOrUpdate : this.tableHeaderAged;
            await this.getAllOrders();
        },
        computed: {
            formTypeValue() {
                if (this.formType == "work_in_new") {
                    return 'new';
                } else if (this.formType == "work_in_update") {
                    return 'update';
                } else {
                    return 'aged';
                }
            },
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v => (item.first_name.toLowerCase().includes(v) || item.last_name.toLowerCase().includes(v) || item.model_name.toLowerCase().includes(v) || item.order_age == (v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllOrders() {
                this.loading = true;
                this.distinctOrderIds = [];
                try {
                    const response = await axios.get(`getAllOrders`);
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
            addNewOrder() {
                this.$refs.addNewOrder.show();
            }
        }
    }
</script>