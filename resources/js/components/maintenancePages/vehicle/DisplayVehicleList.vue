<template>
    <div class="container">
         <div class="p-2 pageHeading">Vehicle List</div>
         <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Cust Name/phone/model" /></div>
            <div class="col-2"><button type="button" class="footerButton form-control p-2" @click="addNewVehicle">Add</button></div>
            <div class="col-3"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
        </div>
        <br/>
        <h3 v-if="tableBody.length==0" class="p-3">
            No listings found. Please click the Add button to add a new vehicle.
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
                        <td>{{item.cust_id}}</td>
                        <td>{{item.first_name}} {{item.last_name}}</td>
                        <td>{{item.phone1}}</td>
                        <td>{{item.model_name}}</td>
                        <td><button type="button" class="footerButton p-3"  @click="deleteVehicle(item)">Delete</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <add-new-vehicle ref="addNewVehicle" @reload:vehicle="getAllVehicles" />
        <display-edit-vehicle ref="displayEditVehicle" :vehicle-info="selectedItem" @reload:vehicle="getAllVehicles" />
        <delete-vehicle ref="deleteVehicle" :vehicle-info="itemToDelete" @reload:vehicle="getAllVehicles" />
    </div>
</template>

<script>
    import axios from 'axios';
    import AddNewVehicle from './AddNewVehicle.vue';
    import DisplayEditVehicle from './DisplayEditVehicle.vue'
    import DeleteVehicle from './DeleteVehicle.vue';

    export default {
        name: "DisplayVehicleList",
        components: {
            AddNewVehicle,
            DisplayEditVehicle,
            DeleteVehicle
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'Customer Name', 'Phone1', 'Make Model', ''],
                tableBody: [],
                loading: false,
                deleteItem: false,
                itemToDelete:{},
                searchQuery: null
            }
        },
        async mounted() {
            await this.getAllVehicles();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v =>( item.first_name.toLowerCase().includes(v) ||  item.last_name.toLowerCase().includes(v) ||  item.phone1.toLowerCase().includes(v) ||  item.model_name.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllVehicles() {
                this.loading = true;
                this.deleteItem = false;
                this.itemToDelete = {};

                try {
                    const response = await axios.get(`getAllVehicles`);
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
                        this.$refs.displayEditVehicle.show();
                    });
                }
            },
            addNewVehicle() {
                this.$nextTick(()=>{
                    this.$refs.addNewVehicle.show();
                });
            },
            deleteVehicle(item){
                this.deleteItem = true;
                this.itemToDelete = item;
                this.$nextTick(()=>{
                    this.$refs.deleteVehicle.show();
                });
            }
        }
    }
</script>