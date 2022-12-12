<template>
    <div class="container">
         <div class="p-2 pageHeading">Employee List</div>
         <div class="row">
            <div class="col-6"><input class="form-control" type="text" v-model="searchQuery" placeholder="Search By Employee Name" /></div>
            <div class="col-2"><button type="button" class="footerButton form-control p-2" @click="addNewEmployee">Add</button></div>
            <div class="col-3"><button type="button" class="footerButton form-control p-2" @click="escape">Back</button></div>
        </div>
        <br/>
        <h3 v-if="tableBody.length==0" class="p-3">
            No listings found. Please click the Add button to add a new Employee.
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
                        <td>{{item.start_date}}</td>
                        <td>{{item.end_date}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <add-new-employee ref="addNewEmployee" @reload:employee="getAllEmployees" />
        <display-edit-employee ref="displayEditEmployee" :employee-info="selectedItem" @reload:employee="getAllEmployees" />
    </div>
</template>

<script>
    import axios from 'axios';
    import AddNewEmployee from './AddNewEmployee.vue';
    import DisplayEditEmployee from './DisplayEditEmployee.vue'

    export default {
        name: "DisplayEmployeeList",
        components: {
            AddNewEmployee,
            DisplayEditEmployee
        },
        props: ["formType"],
        data() {
            return {
                selectedItem:{},
                tableHeader: ['ID', 'Name', 'Start Date', 'End Date'],
                tableBody: [],
                loading: false,
                searchQuery: null,
            }
        },
        async mounted() {
            if(this.formType == 'curr_emp_file') {
                await this.getAllEmployees();
            } else {
                await this.getCurrentAndOldEmployees();
            }
        },
        computed:{
            resultQuery() {
                if(this.searchQuery){
                    return this.tableBody.filter((item)=>{
                        return this.searchQuery.toLowerCase().split(' ').every(v =>( item.first_name.toLowerCase().includes(v) ||  item.last_name.toLowerCase().includes(v)))
                    })
                }else{
                    return this.tableBody;
                }
            }
        },
        methods:{
            async getAllEmployees() {
                this.loading = true;

                try {
                    const response = await axios.get(`getAllEmployees`);
                    this.tableBody = response.data;
                } catch(exception) {
                    console.log("exception: ", exception)
                }
                
                this.loading = false;
            },
            async getCurrentAndOldEmployees() {
                this.loading = true;

                try {
                    const response = await axios.get(`getCurrentAndOldEmployees`);
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
                    this.$refs.displayEditEmployee.show();
                });
            },
            addNewEmployee() {
                this.$nextTick(()=>{
                    this.$refs.addNewEmployee.show();
                });
            }
        }
    }
</script>