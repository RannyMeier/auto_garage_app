<template>
    <div class="container">
         <div class="p-2 pageHeading">Insurers List</div>
        <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Company/Zip" /></div>
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
                        <td>{{item.company_name}}</td>
                        <td>{{item.address}}</td>
                        <td>{{item.city}}</td>
                        <td>{{item.state}}</td>
                        <td>{{item.zip}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <show-adjusters-for-insurer ref="showAdjustersForInsurer" :form-type="formType" :ins-info="selectedItem" @reload:insurer="getAllInsurers" />
    </div>
</template>

<script>
    import axios from 'axios';
    import ShowAdjustersForInsurer from './ShowAdjustersForInsurer.vue'

    export default {
        name: "ShowInsurerDetails",
        components: {
            ShowAdjustersForInsurer
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'Company Name', 'Address', 'City', 'State', 'Zip'],
                tableBody: [],
                loading: false,
                searchQuery: null,
            }
        },
        async mounted() {
            await this.getAllInsurers();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v => (item.company_name.toLowerCase().includes(v) || item.zip.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllInsurers() {
                this.loading = true;

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
                this.selectedItem = item;
                this.$nextTick(()=>{
                    this.$refs.showAdjustersForInsurer.show();
                });
            },
        }
    }
</script>