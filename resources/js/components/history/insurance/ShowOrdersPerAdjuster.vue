<template>
    <div v-if="showOrdersPerAdjuster">
        <div id="modal">
            <div class="modal-content">
                <div class="p-2 pageHeading">
                    Order Details For Adjuster<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    Adjuster Name: <span>{{adjusterInfo.adjuster_name}}</span>
                    Adjuster ID: <span>{{adjusterInfo.id}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    Total Customer Value: <span>{{overallTotal}}</span>
                </div>
                <br/>
                <h3 v-if="tableBody.length==0" class="p-3">
                    No order details found.
                </h3>
                <div v-else class="col pb-2 pt-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th v-for="item in tableHeader" v-bind:key="item">{{item}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableBody" v-bind:key="index" @click="openItem(item)">
                                <td>{{item.id}}</td>
                                <td>{{item.date_created}}</td>
                                <td>{{item.first_name}} {{item.last_name}}</td>
                                <td>{{item.total_bill}}</td>
                                <td>{{item.model_name}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <display-order :form-type="formType" ref="displayOrder" :order-info="selectedItem" @reload:data="getAllOrdersForGivenAdjuster" />
    </div>
</template>

<script>
import moment from 'moment';
import axios from 'axios';
import DisplayOrder from '../../workInPages/newOrderPages/DisplayOrder.vue';
    
export default {
    name: "ShowOrdersPerAdjuster",
    props: ["adjusterInfo", "formType"],
    components:{
        DisplayOrder
    },
    data() {
        return {
            showOrdersPerAdjuster: false,
            currentDate: "",
            currentTime: "",
            tableHeader: ['Order ID', 'Order Date', 'Customer', 'Total Bill', 'Balance'],
            tableBody: [],
            distinctOrderIds: [],
            invoices: [],
            payDets: [],
            selectedItem: {},
            sum: 0
        }
    },
    mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    computed:{
        overallTotal() {
            this.sum = 0;
            if(this.tableBody.length > 0) {
                this.sum = this.tableBody.reduce(function (result, item) {
                    result = result + item.total_bill;
                    return result;
                }, 0);
            }
            return this.sum;
        }
    },
    methods: {
        async getAllOrdersForGivenAdjuster() {
            const params = { params: { adj_id: this.adjusterInfo.id}};
            this.distinctOrderIds = [];
            const response = await axios.get(`getAllOrdersForGivenAdjuster`, params);
            this.tableBody = response.data['orders'];
            this.invoices = response.data['invoiceList'];
            this.payDets = response.data['allPayDets'];

            this.tableBody.forEach((item) => {
                item.total_bill = 0;
                this.distinctOrderIds.push(item.id);
            });
            this.calculateTotal();
        },
        calculateTotal() {
            this.distinctOrderIds.forEach((orderId) => {
                let invoiceArrayPerOrder = this.invoices[orderId];

                if(invoiceArrayPerOrder && invoiceArrayPerOrder.length > 0) {
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
                }
            });
        },
        async show() {
            this.showOrdersPerAdjuster = true;
            
            await this.getAllOrdersForGivenAdjuster();
        },
        hide() {
            this.showOrdersPerAdjuster = false;
        },
        openItem(item) {
            this.selectedItem = item;
            this.$nextTick(()=>{
                this.$refs.displayOrder.show();
            });
        }
    }
}
</script>
