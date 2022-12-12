<template>
    <div class="container">
         <div class="p-2 pageHeading">Customer List</div>
         <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By First, Last Name or Phone" /></div>
            <div class="col-2"><button type="button" class="footerButton form-control p-2" @click="addNewCustomer">Add</button></div>
            <div class="col-3"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
        </div>
        <br/>
        <h3 v-if="tableBody.length==0" class="p-3">
            No listings found. Please click the Add button to add a new customer.
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
                        <td><button type="button" class="footerButton p-3"  @click="deleteCustomer(item)">Delete</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <add-new-customer ref="addNewCustomer" @reload:customers="getAllCustomers" />
        <display-edit-customer ref="displayEditCustomer" :customer-info="selectedItem" @reload:customers="getAllCustomers" />
        <delete-customer ref="deleteCustomer" :customer-info="itemToDelete" @reload:customers="getAllCustomers" />
    </div>
</template>

<script>
    import axios from 'axios';
    import AddNewCustomer from './AddNewCustomer.vue';
    import DisplayEditCustomer from './DisplayEditCustomer.vue'
    import DeleteCustomer from './DeleteCustomer.vue'

    export default {
        name: "DisplayCustomerList",
        components: {
            AddNewCustomer,
            DisplayEditCustomer,
            DeleteCustomer
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'First Name', 'Last Name', 'Phone1', ''],
                tableBody: [],
                loading: false,
                deleteItem: false,
                itemToDelete:{},
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
                this.searchQuery = null;
                this.loading = true;
                this.deleteItem = false;
                this.itemToDelete = {};

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
                if(!this.deleteItem) {
                    this.selectedItem = item;
                    this.$nextTick(()=>{
                        this.$refs.displayEditCustomer.show();
                    });
                }
            },
            addNewCustomer() {
                this.$nextTick(()=>{
                    this.$refs.addNewCustomer.show();
                });
            },
            deleteCustomer(item){
                this.deleteItem = true;
                this.itemToDelete = item;
                this.$nextTick(()=>{
                    this.$refs.deleteCustomer.show();
                });
            }
        }
    }
</script>