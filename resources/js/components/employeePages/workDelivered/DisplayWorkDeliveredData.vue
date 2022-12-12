<template>
    <div v-if="displayWorkDeliveredData">
        <div id="modal">
            <div class="modal-content">
                 <div class="p-2 pageHeading">
                    Display Work Done<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="row">
                    <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Model Name, License" /></div>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    Employee ID: <span>{{empId}}</span>
                    Employee Name: <span>{{orderInfo.length>0?orderInfo[0].first_name:''}} {{orderInfo.length>0?orderInfo[0].last_name:''}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <br/>
                <h3 v-if="orderInfo.length==0" class="p-3">
                    No invoices found.
                </h3>
                <div v-else class="col pb-2 pt-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th v-for="item in tableHeader" v-bind:key="item">{{item}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in resultQuery" v-bind:key="index" @click="openItem(item)">
                                <td>{{item.id}}</td>
                                <td>{{item.license}}</td>
                                <td>{{item.model_name}}</td>
                                <td>{{item.total_bill}}</td>
                                <td>{{item.balance}}</td>
                                <td>{{item.labor_cost}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <display-order :form-type="formType" ref="displayOrder" :order-info="selectedItem" />
    </div>
</template>

<script>
    import DisplayOrder from '../../workInPages/newOrderPages/DisplayOrder.vue'    
    import moment from 'moment'
    import axios from 'axios';

    export default {
	    name:"DisplayWorkDeliveredData",
        components:{
            DisplayOrder
        },
        props:["to", "from", "empId", "formType"],
        data(){
            return {
                currentDate: '',
                currentTime: '',
                displayWorkDeliveredData: false,
                selectedItem:{},
                tableHeader: ["Order No","License", "Vehicle", "Total Bill", "Balance", "Total Labor Cost"],
                orderInfo: [],
                distinctOrderIds: [],
                invoices: [],
                payDets: [],
                totalLaborCostValue: 0,
                searchQuery: null,
            };
        },
        mounted() {
            this.currentDate = moment().format("MMM DD, YYYY");
            this.currentTime = moment().format("HH:MM A");
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.orderInfo.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v => (item.license.toLowerCase().includes(v) || item.model_name.toLowerCase().includes(v)))
                    })
                }else{
                    return this.orderInfo;
                }
            },
        },
        methods:{
            openItem(item) {
                this.selectedItem = item;
                this.$nextTick(()=>{
                    this.$refs.displayOrder.show();
                });
            },
            async show() {
                this.searchQuery = null;
                await this.getEmpPayDetailsForWorkDelivered();
                this.displayWorkDeliveredData = true;
            },
            hide() {
                this.displayWorkDeliveredData = false;
            },
            async getEmpPayDetailsForWorkDelivered() {
                let fromDate = this.from != "" ? this.from : new Date("01-01-2000").toISOString().slice(0,10);
                let toDate = this.to != "" ? this.to : new Date().toISOString().slice(0,10);

                const params = {params :{
                        from: fromDate,
                        to: toDate,
                        emp_id: this.empId,
                    }
                }

                try {
                    const response = await axios.get(`getEmpPayDetailsForWorkDelivered`, params);
                    this.orderInfo = response.data['orders'];
                    this.invoices = response.data['invoiceList'];
                    this.payDets = response.data['allPayDets'];

                    this.orderInfo.forEach((item) => {
                        item.total_bill = 0;
                        item.balance = 0;
                        item.labor_cost = 0;
                        item.amount_paid = 0;
                        this.distinctOrderIds.push(item.id);
                    });
                    this.calculateTotal();
                    this.calculateAmountPaid();
                    this.calculateLaborCost();
                    this.calculateBalance();
                } catch (exception) {
                    console.log("exception: ", exception)
                }
            },
            calculateLaborCost() {
                this.distinctOrderIds.forEach((orderId) => {
                    let invoiceArrayPerOrder = this.invoices[orderId];

                    if(invoiceArrayPerOrder && invoiceArrayPerOrder.length > 0) {
                        this.totalLaborCostValue = invoiceArrayPerOrder.reduce(function (result, item) {
                            result = result + item.total_labor_cost;
                            return result;
                        }, 0);
                        
                        this.orderInfo = this.orderInfo.map((element) => {
                            if(element.id == orderId) {
                                let newTotal = element.labor_cost + this.totalLaborCostValue;
                                
                                return {...element, labor_cost: newTotal};
                            }
                            return element;
                        });
                    }
                    
                });
            },
             calculateTotal() {
                this.distinctOrderIds.forEach((orderId) => {
                    let invoiceArrayPerOrder = this.invoices[orderId];

                    if(invoiceArrayPerOrder && invoiceArrayPerOrder.length > 0) {
                        this.subTotal = invoiceArrayPerOrder.reduce(function (result, item) {
                            result = result + item.sub_total_with_tax + item.total_labor_cost;
                            return result;
                        }, 0);
                        
                        this.orderInfo = this.orderInfo.map((element) => {
                            if(element.id == orderId) {
                                let newTotal = element.total_bill + this.subTotal + element.towing + element.waste + (element.storage_cost);
                                
                                return {...element, total_bill: newTotal};
                            }
                            return element;
                        });
                    }
                    
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
                    
                    this.orderInfo = this.orderInfo.map((element) => {
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
                this.orderInfo = this.orderInfo.map((element) => {
                    if(element.id == orderId) {
                        let newBalance = element.total_bill - element.amount_paid;
                        
                        return {...element, balance: newBalance};
                    }
                    return element;
                });
            });
        },
        }
    }
</script>