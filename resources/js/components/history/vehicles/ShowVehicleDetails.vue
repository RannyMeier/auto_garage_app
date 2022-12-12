<template>
    <div class="container">
         <div class="p-2 pageHeading">Vehicle List</div>
        <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Name, Model,License" /></div>
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
                        <td>{{item.first_name}} {{item.last_name}}</td>
                        <td>{{item.license}}</td>
                        <td>{{item.model_name}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <show-orders-per-vehicle ref="showOrdersPerVehicle" :form-type="formType" :vehicle-info="selectedItem" @reload:vehicle="getAllVehicles" />
    </div>
</template>

<script>
    import axios from 'axios';
    import ShowOrdersPerVehicle from './ShowOrdersPerVehicle.vue'

    export default {
        name: "ShowVehicleDetails",
        components: {
            ShowOrdersPerVehicle,
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'Customer Name', 'License#', 'Make Model'],
                tableBody: [],
                loading: false,
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
                        return this.searchQuery.toLowerCase().split(' ').every(v => (item.license.toLowerCase().includes(v) || item.first_name.toLowerCase().includes(v) || item.model_name.toLowerCase().includes(v) || item.last_name.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllVehicles() {
                this.loading = true;

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
                this.selectedItem = item;
                this.$nextTick(()=>{
                    this.$refs.showOrdersPerVehicle.show();
                });
            },
        }
    }
</script>