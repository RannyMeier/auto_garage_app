<template>
  <div v-if="showInvoices">
    <div id="modal">
      <div class="modal-content">
            <div class="p-2 pageHeading">
                Display All Invoices for Current Order<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <div class="row">
                <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Service" /></div>
                <div class="col-3"><button type="button" class="form-control footerButton p-3" @click="viewTotal">View Total</button></div>
                <div class="col-3"><button type="button" class="form-control footerButton p-3" @click="addNewInvoice" v-if=!hideAddButton>Add New Invoice</button></div>
            </div>
            <br/>
            <div class="formHeadingFooter p1">
                <span>{{currentTime}}</span>
                Order No.: <span style="font-weight: bolder; color: #000;">{{orderInfo.id}}</span>
                <span>{{currentDate}}</span>
            </div>
            <br/>
            <div class="formHeadingFooter p-1">
                <span>In: {{orderInfo.date_created}}</span>
                <span>Out: {{orderInfo.delivery_date}}</span>
            </div>
            <h3 v-if="invoiceList.length==0" class="p-3">
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
                            <td>{{item.is_sublet}}</td>
                            <td>{{item.quantity}}</td>
                            <td>{{item.est_cost}}</td>
                            <td>{{item.cust_cost}}</td>
                            <td>{{item.estimated_labor}}</td>
                            <td>{{item.labor_cost}}</td>
                            <td>{{item.sub_total}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> 
    <display-total ref="displayTotal" :order-info="orderInfo" :invoices="invoiceList"></display-total>
    <add-new-invoice ref="addNewInvoice" :order-info="orderInfo" @reload:invoicelist="getAllInvoices"></add-new-invoice>
    <display-invoice-item ref="showInvoiceDetails" :order-info="orderInfo" :invoice-item="selectedItem" :form-type="formType" @reload:invoicelist="getAllInvoices" />
  </div>
</template>

<script>
    import moment from 'moment';
    import AddNewInvoice from './AddNewInvoice.vue';
    import DisplayInvoiceItem from './DisplayInvoiceItem.vue';
    import DisplayTotal from './DisplayTotal.vue';
    import axios from 'axios';

    const NOT_SHOW_ADD_INVOICE_BUTTON = ['cust_val_income', 'work_done_by_date', 'history_order', 'history_customer','history_vehicle','history_insurance'];

    export default {
    name: "DisplayInvoicesForOrder",
    components:{
        DisplayInvoiceItem,
        AddNewInvoice,
        DisplayTotal
    },
    props: [
         "orderInfo", "formType"
    ],
    data() {
        return {
            showInvoices: false,
            invoiceList:[],
            selectedItem: {},
            tableHeader: ["Description", "Sublet", "Qt", "Est Parts", "Parts Cost", "Est Labor", "Labor Cost", "SubTotal"],
            currentDate: "",
            currentTime: "",
            searchQuery: null,
        };
    },
    async mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    computed:{
        hideAddButton() {
            return NOT_SHOW_ADD_INVOICE_BUTTON.includes(this.formType);
        },
        resultQuery() {
            if(this.searchQuery){
                return this.invoiceList.filter((item)=>{
                    return this.searchQuery.toLowerCase().split(' ').every(v => (item.service_name.toLowerCase().includes(v)))
                })
            }else{
                return this.invoiceList;
            }
        }
    },
    methods: {
        async getAllInvoices() {
            const params = {params: {order_id: this.orderInfo.id}};

            try {
                const response = await axios.get(`/getAllInvoicesForGivenOrder`, params);
                this.invoiceList = response.data;
            } catch (exception) {
                console.log("exception: ", exception)
            }

        },
        async show() {
            await this.getAllInvoices();
            this.showInvoices = true;
        },
        hide() {
            this.showInvoices = false;
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
                this.$refs.showInvoiceDetails.show();
            });
        }
    }
}
</script>