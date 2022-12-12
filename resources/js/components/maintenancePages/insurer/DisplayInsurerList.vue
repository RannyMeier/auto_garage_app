<template>
    <div class="container">
        <div class="p-2 pageHeading">Insurers List</div>
        <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Company Name/Zip" /></div>
            <div class="col-2"><button type="button" class="footerButton form-control p-2" @click="addNewInsurer">Add</button></div>
            <div class="col-3"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
        </div>
        <br/>
        <h3 v-if="tableBody.length==0" class="p-3">
            No listings found. Please click the Add button to add a new Insurer.
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
                        <td>{{item.company_name}}</td>
                        <td>{{item.address}}</td>
                        <td>{{item.city}}</td>
                        <td>{{item.state}}</td>
                        <td>{{item.zip}}</td>
                        <td><button type="button" class="footerButton p-3"  @click="deleteInsurer(item)">Delete</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <add-new-insurer ref="addNewInsurer" @reload:insurer="getAllInsurers" />
        <display-edit-insurer ref="displayEditInsurer" :ins-info="selectedItem" @reload:insurer="getAllInsurers" />
        <delete-insurer ref="deleteInsurer" :ins-info="itemToDelete" @reload:insurer="getAllInsurers" />
    </div>
</template>

<script>
    import axios from 'axios';
    import AddNewInsurer from './AddNewInsurer.vue';
    import DisplayEditInsurer from './DisplayEditInsurer.vue'
    import DeleteInsurer from './DeleteInsurer.vue';

    export default {
        name: "DisplayInsurerList",
        components: {
            AddNewInsurer,
            DisplayEditInsurer,
            DeleteInsurer
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'Company Name', 'Address', 'City', 'State', 'Zip', ''],
                tableBody: [],
                loading: false,
                deleteItem: false,
                itemToDelete:{},
                searchQuery:null,
            }
        },
        async mounted() {
            await this.getAllInsurers();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v =>( item.company_name.toLowerCase().includes(v) ||  item.zip.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllInsurers() {
                this.loading = true;
                this.deleteItem = false;
                this.itemToDelete = {};

                try {
                    const response = await axios.get(`getAllInsurers`);
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
                        this.$refs.deleteInsurer.show();
                    });
                }
            },
            addNewInsurer() {
                this.$nextTick(()=>{
                    this.$refs.addNewInsurer.show();
                });
            },
            deleteInsurer(item){
                this.deleteItem = true;
                this.itemToDelete = item;
                this.$nextTick(()=>{
                    this.$refs.deleteInsurer.show();
                });
            }
        }
    }
</script>