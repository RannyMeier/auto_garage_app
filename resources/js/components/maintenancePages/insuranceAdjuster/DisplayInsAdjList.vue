<template>
    <div class="container">
        <div class="p-2 pageHeading">Insurance Adjuster List</div>
        <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Adjuster Name/Phone" /></div>
            <div class="col-2"><button type="button" class="footerButton form-control p-2" @click="addNewInsAdj">Add</button></div>
            <div class="col-3"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
        </div>
        <br/>
        <h3 v-if="tableBody.length==0" class="p-3">
            No listings found. Please click the Add button to add a new Insurance Adjuster.
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
                        <td>{{item.adjuster_name}}</td>
                        <td>{{item.phone}}</td>
                        <td>{{item.company_name}}</td>
                        <td><button type="button" class="footerButton p-3"  @click="deleteAdj(item)">Delete</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <add-new-ins-adj ref="addNewInsAdj" @reload:insAdj="getAllInsAdj" />
        <display-edit-ins-adj ref="displayEditInsAdj" :ins-adj-info="selectedItem" @reload:insAdj="getAllInsAdj" />
        <delete-adjuster ref="deleteAdjuster" :ins-adj-info="itemToDelete" @reload:insAdj="getAllInsAdj" />
    </div>
</template>

<script>
    import axios from 'axios';
    import AddNewInsAdj from './AddNewInsAdj.vue';
    import DisplayEditInsAdj from './DisplayEditInsAdj.vue'
    import DeleteAdjuster from './DeleteAdjuster.vue'

    export default {
        name: "DisplayInsAdjList",
        components: {
            AddNewInsAdj,
            DisplayEditInsAdj,
            DeleteAdjuster
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'Adjuster Name', 'Phone', 'Insurance Company Name', ''],
                tableBody: [],
                loading: false,
                deleteItem: false,
                itemToDelete:{},
                searchQuery: null,
            }
        },
        async mounted() {
            await this.getAllInsAdj();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v =>(item.adjuster_name.toLowerCase().includes(v) ||  item.phone.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllInsAdj() {
                this.loading = true;
                this.deleteItem = false;
                this.itemToDelete = {};

                try {
                    const response = await axios.get(`getAllInsAdj`);
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
                        this.$refs.displayEditInsAdj.show();
                    });
                }
            },
            addNewInsAdj() {
                this.$nextTick(()=>{
                    this.$refs.addNewInsAdj.show();
                });
            },
            deleteAdj(item) {
                this.deleteItem = true;
                this.itemToDelete = item;
                this.$nextTick(()=>{
                    this.$refs.deleteAdjuster.show();
                });
            }
        }
    }
</script>