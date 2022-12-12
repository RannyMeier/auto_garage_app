<template>
    <div class="container">
         <div class="p-2 pageHeading">Customer Value By Income</div>
        <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Name, Phone" /></div>
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
                        <td>{{item.first_name}}</td>
                        <td>{{item.last_name}}</td>
                        <td>{{item.phone1}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <display-order-details-cust-value ref="displayOrderDetailsCustValue" :customer-info="selectedItem" :form-type="formType" />
    </div>
</template>

<script>
    import axios from 'axios';
    import DisplayOrderDetailsCustValue from './DisplayOrderDetailsCustValue.vue'

    export default {
        name: "CustomerValueByIncomeMain",
        components: {
            DisplayOrderDetailsCustValue
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'First Name', 'Last Name', 'Phone1'],
                tableBody: [],
                loading: false,
                searchQuery: null,
            }
        },
        async mounted() {
            await this.getAllCustomers();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v => (item.phone1.toLowerCase().includes(v) || item.first_name.toLowerCase().includes(v) || item.last_name.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllCustomers() {
                this.loading = true;

                try {
                    const response = await axios.get(`getAllCustomers`);
                    this.tableBody = response.data;
                } catch(exception) {
                    console.log("exception: ", exception)
                }
                
                this.loading = false;
            },
            escape() {
                this.$emit("escape:form",this.formType)
            },
            openItem(item) {
                this.selectedItem = item;
                this.$nextTick(()=>{
                    this.$refs.displayOrderDetailsCustValue.show();
                });
            },
        }
    }
</script>