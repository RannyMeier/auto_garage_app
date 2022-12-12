<template>
    <div class="container">
         <div class="p-2 pageHeading">Make Model List</div>
         <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Model Name" /></div>
            <div class="col-2"></div>
            <div class="col-3"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
        </div>
        <br/>
        <h3 v-if="tableBody.length==0" class="p-3">
            No listings found. Please click the Add button to add a new model.
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
                        <td>{{item.model_name}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <add-new-make-model ref="addNewMakeModel" @reload:makeModel="getAllMakeModel" />
        <display-edit-make-model ref="displayEditMakeModel" :make-model-info="selectedItem" @reload:makeModel="getAllMakeModel" />
    </div>
</template>

<script>
    import axios from 'axios';
    import AddNewMakeModel from './AddNewMakeModel.vue'
    import DisplayEditMakeModel from './DisplayEditMakeModel.vue';

    export default {
        name: "DisplayMakeModelList",
        components: {
            AddNewMakeModel,
            DisplayEditMakeModel
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'Model Name'],
                tableBody: [],
                loading: false,
                searchQuery: null,
            }
        },
        async mounted() {
            await this.getAllMakeModel();
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v => item.model_name.toLowerCase().includes(v))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllMakeModel() {
                this.searchQuery = null;
                this.loading = true;

                try {
                    const response = await axios.get(`getAllMakeModel`);
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
                    this.$refs.displayEditMakeModel.show();
                });
            },
            addNewMakeModel() {
                this.$nextTick(()=>{
                    this.$refs.addNewMakeModel.show();
                });
            }
        }
    }
</script>