<template>
    <div class="container">
         <div class="p-2 pageHeading">Vendor List</div>
         <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Phone/Name" /></div>
            <div class="col-2"><button type="button" class="footerButton form-control p-2" @click="addNewVendors">Add</button></div>
            <div class="col-3"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
        </div>
        <br/>
        <h3 v-if="tableBody.length==0" class="p-3">
            No listings found. Please click the Add button to add a new vendor.
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
                        <td>{{item.name}}</td>
                        <td>{{item.phone}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <add-new-vendors ref="addNewVendors" @reload:vendors="getAllVendors" />
        <display-edit-vendors ref="displayEditVendors" :vendor-info="selectedItem" @reload:vendors="getAllVendors" />
    </div>
</template>

<script>
    import axios from 'axios';
    import AddNewVendors from './AddNewVendors.vue';
    import DisplayEditVendors from './DisplayEditVendors.vue'

    export default {
        name: "DisplayVendorsList",
        components: {
            AddNewVendors,
            DisplayEditVendors
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'Vendor Name', 'Phone'],
                tableBody: [],
                loading: false,
                searchQuery: null
            }
        },
        async mounted() {
            await this.getAllVendors();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v =>( item.name.toLowerCase().includes(v) ||  item.phone.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllVendors() {
                this.loading = true;

                try {
                    const response = await axios.get(`getAllVendors`);
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
                    this.$refs.displayEditVendors.show();
                });
            },
            addNewVendors() {
                this.$nextTick(()=>{
                    this.$refs.addNewVendors.show();
                });
            }
        }
    }
</script>