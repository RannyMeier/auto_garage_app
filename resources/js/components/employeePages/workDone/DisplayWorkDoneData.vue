<template>
    <div v-if="displayWorkDoneData">
        <div id="modal">
            <div class="modal-content">
                 <div class="p-2 pageHeading">
                    Display Work Done<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="row">
                    <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Service Name,Date created" /></div>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    Employee ID: <span>{{empId}}</span>
                    Employee Name: <span>{{invoiceInfo.length > 0 ? invoiceInfo[0].first_name : ''}} {{invoiceInfo.length > 0 ? invoiceInfo[0].last_name : ''}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    Total Hours: <span>{{totalHours}}</span>
                    Total Cost: <span>{{totalCost}}</span>
                </div>
                <h3 v-if="invoiceInfo.length==0" class="p-3">
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
                                <td>{{item.date_created}}</td>
                                <td>{{item.service_name}}</td>
                                <td>{{item.labor_hours}}</td>
                                <td>{{item.labor_rate}}</td>
                                <td>{{item.total_labor_cost}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <display-invoice-data ref="displayInvoiceData" :invoice-info="selectedItem" @reload:data="getEmpPayDetailsForWorkDone" />
    </div>
</template>

<script>
    import DisplayInvoiceData from './DisplayInvoiceData.vue';
    import moment from 'moment'
    import axios from 'axios';

    export default {
	    name:"DisplayWorkDoneData",
        components:{
            DisplayInvoiceData
        },
        props:["to", "from", "empId"],
        data(){
            return {
                currentDate: '',
                currentTime: '',
                displayWorkDoneData: false,
                selectedItem:{},
                tableHeader: ["Invoice Date","Description", "Labor Hours", "Labor Rate", "Labor Cost"],
                invoiceInfo: [],
                searchQuery: null,
            };
        },
        mounted() {
            this.currentDate = moment().format("MMM DD, YYYY");
            this.currentTime = moment().format("HH:MM A");
        },
        computed: {
            resultQuery() {
                if(this.searchQuery){
                    return this.invoiceInfo.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v => (item.date_created.toLowerCase().includes(v) || item.service_name.toLowerCase().includes(v)))
                    })
                }else{
                    return this.invoiceInfo;
                }
            },
            totalHours() {
                if(this.invoiceInfo) {
                    let totalHourValue = 0;
                    this.invoiceInfo.forEach((item)=>{
                        totalHourValue = parseFloat(totalHourValue) + parseFloat(item.labor_hours);
                    });

                    return totalHourValue;
                }
            },
            totalCost() {
                if(this.invoiceInfo) {
                    let totalCostValue = 0;
                    this.invoiceInfo.forEach((item)=>{
                        totalCostValue = parseFloat(totalCostValue) + parseFloat(item.total_labor_cost);
                    });

                    return totalCostValue;
                }
            }
        },
        methods:{
            openItem(item) {
                this.selectedItem = item;
                this.$nextTick(()=>{
                    this.$refs.displayInvoiceData.show();
                });
            },
            async show() {
                this.searchQuery = null;
                await this.getEmpPayDetailsForWorkDone();
                this.displayWorkDoneData = true;
            },
            hide() {
                this.displayWorkDoneData = false;
            },
            async getEmpPayDetailsForWorkDone() {
                let fromDate = this.from != "" ? this.from : new Date("01-01-2000").toISOString().slice(0,10);
                let toDate = this.to != "" ? this.to : new Date().toISOString().slice(0,10);

                const params = {params :{
                        from: fromDate,
                        to: toDate,
                        emp_id: this.empId,
                    }
                }

                try {
                    const response = await axios.get(`getEmpPayDetailsForWorkDone`, params);
                    this.invoiceInfo = response.data;
                } catch (exception) {
                    console.log("exception: ", exception)
                }
            },
        }
    }
</script>