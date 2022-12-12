<template>
    <div class="container">
        <div class="p-2 pageHeading">Receivable By Customer</div>
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
        
        <display-all-orders-per-customer ref="displayAllOrdersPerCustomer" :customer-info="selectedItem" />
    </div>
</template>

<script>
    import axios from 'axios';
    import DisplayAllOrdersPerCustomer from './DisplayAllOrdersPerCustomer.vue'

    export default {
        name: "DisplayAllCustomers",
        components: {
            DisplayAllOrdersPerCustomer
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
                        return this.searchQuery.toLowerCase().split(' ').every(v => (item.first_name.toLowerCase().includes(v) || item.last_name.toLowerCase().includes(v) || item.phone1.toLowerCase().includes(v)))
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
                    this.$refs.displayAllOrdersPerCustomer.show();
                });
            },
        }
    }
</script>