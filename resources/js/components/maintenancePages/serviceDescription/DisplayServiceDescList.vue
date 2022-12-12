<template>
    <div class="container">
         <div class="p-2 pageHeading">Service Description List</div>
         <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Service Name" /></div>
            <div class="col-2"><button type="button" class="footerButton form-control p-2" @click="addNewServiceDesc">Add</button></div>
            <div class="col-3"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
        </div>
        <br/>
        <h3 v-if="tableBody.length==0" class="p-3">
            No listings found. Please click the Add button to add a new service description.
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
                        <td>{{item.service_name}}</td>
                        <td><button type="button" class="footerButton p-3"  @click="deleteService(item)">Delete</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <add-new-service-desc ref="addNewServiceDesc" @reload:serviceDesc="getAllServiceDesc" />
        <display-edit-service-desc ref="displayEditServiceDesc" :service-desc-info="selectedItem" @reload:serviceDesc="getAllServiceDesc" />
        <delete-service ref="deleteService" :service-desc-info="itemToDelete" @reload:serviceDesc="getAllServiceDesc" />
    </div>
</template>

<script>
    import axios from 'axios';
    import AddNewServiceDesc from './AddNewServiceDesc.vue';
    import DisplayEditServiceDesc from './DisplayEditServiceDesc.vue'
    import DeleteService from './DeleteService.vue';

    export default {
        name: "DisplayServiceDescList",
        components: {
            AddNewServiceDesc,
            DisplayEditServiceDesc,
            DeleteService
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'Service Description',''],
                tableBody: [],
                loading: false,
                deleteItem: false,
                itemToDelete:{},
                searchQuery: null,
            }
        },
        async mounted() {
            await this.getAllServiceDesc();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v =>(item.service_name.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllServiceDesc() {
                this.loading = true;
                this.deleteItem = false;
                this.itemToDelete = {};

                try {
                    const response = await axios.get(`getAllServiceDesc`);
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
                        this.$refs.displayEditServiceDesc.show();
                    });
                }
            },
            addNewServiceDesc() {
                this.$nextTick(()=>{
                    this.$refs.addNewServiceDesc.show();
                });
            },
            deleteService(item){
                this.deleteItem = true;
                this.itemToDelete = item;
                this.$nextTick(()=>{
                    this.$refs.deleteService.show();
                });
            }
        }
    }
</script>