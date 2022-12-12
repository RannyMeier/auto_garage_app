<template>
    <div class="container">
        <div class="p-2 pageHeading">General Ledger By Date</div>
        <div class="formFooter">
            <button type="button" class="footerButton p-3" @click="escape">Back</button>
        </div>
        <br/>
        <div class="col pb-2">
            <form class="formBody">
                <label for="from">Date From:</label>
                <input type="date" id="from" name="from" v-model="formFields.from">
                <br/>
                <label for="to">Date To:</label>
                <input type="date" id="to" name="to" v-model="formFields.to" :min="formFields.from" :max="currentDate">
                <br/>
                <hr/>
                <input type="radio" id="work_done" name="work_done" value="work_done" v-model="work_type" style="width: 3%;">
                <label for="work_done">Work Done</label><br>
                <hr/>
                <input type="radio" id="work_delivered" name="work_delivered" value="work_delivered" v-model="work_type" style="width: 3%;">
                <label for="work_delivered">Work Delivered</label><br>
                <br />
                <hr/>
                <label>Select an employee<span class="req">*</span>: </label>
                <select v-model="formFields.emp_id" style="width: 45%;margin-left:2rem;">
                    <option v-for="item in allEmpValues" v-bind:key="item.id" :value="item.id">
                        {{item.first_name}} {{item.last_name}}
                    </option>
                </select>
                <br/>
                <br/>
                <hr/>
                <br/>
                <div class="p-3">
                    <button class="btn btn-primary btn-sm" @click.prevent="submit"> Submit</button>
                </div>
            </form>
        </div>
        <display-work-done-data ref="displayWorkDoneData" :to="formFields.to" :from="formFields.from" :emp-id="formFields.emp_id"/>
        <display-work-delivered-data ref="displayWorkDeliveredData" :form-type="formType" :to="formFields.to" :from="formFields.from" :emp-id="formFields.emp_id"/>
    </div>
</template>
<script>
import axios from 'axios';
import DisplayWorkDoneData from './workDone/DisplayWorkDoneData.vue'
import DisplayWorkDeliveredData from './workDelivered/DisplayWorkDeliveredData.vue'

export default {
    name: "WorkDoneByDateMain.vue",
    props:["formType"],
    components:{
        DisplayWorkDoneData,
        DisplayWorkDeliveredData
    },
    data() {
        return {
            currentDate: new Date().toISOString().slice(0,10),
            work_type:'',
            formFields:{
                from: '',
                to: new Date().toISOString().slice(0,10),
                emp_id: 0
            },
            allEmpValues: [],
        }
    },
    async mounted() {
        await this.getAllEmployees();
    },
    methods:{
        escape() {
            this.$emit("escape:form",this.formType)
        },
        async submit(){
            if(this.work_type == 'work_done') {
                this.$nextTick(()=>{
                    this.$refs.displayWorkDoneData.show();
                });
            } else {
                this.$nextTick(() => {
                    this.$refs.displayWorkDeliveredData.show();
                })
            }
        },
        async getAllEmployees() {
            try {
                const response = await axios.get(`/getCurrentAndOldEmployees`);
                this.allEmpValues = response.data;
            } catch (exception) {
                console.log("exception: ", exception)
            }
        },
    }
}
</script>
