<template>
    <div v-if="displayEditVehicle">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    View/Edit Vehicle<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully updated entry. Please stay and we will redirect you.</h4>
                <strong class="p-2">Fields marked with red stars are required fields.</strong>
                <form class="formBody">
                    <div class="row">
                        <div class="col-6">
                            <label style="width: 45%;">Select customer<span class="req">*</span>: </label>
                            <select v-model="formFields.cust_id">
                                <option v-for="item in allCustomerList" v-bind:key="item.id" :value="item.id">
                                    {{item.first_name}} {{item.last_name}}
                                </option>
                            </select>
                            <br/>
                            <label>License<span class="req">*</span>: </label> <input type="text" name="license" v-model="formFields.license"/> <br/>
                            <br/>
                            <label>State<span class="req">*</span>: </label> <input type="text" name="state" v-model="formFields.state"/> <br/>
                            <br/>
                            <label style="width: 45%;">Select Model<span class="req">*</span>: </label>
                            <select v-model="formFields.make_model_id">
                                <option v-for="item in allModelList" v-bind:key="item.id" :value="item.id">
                                    {{item.model_name}}
                                </option>
                            </select>                            
                            <br/>
                            <label>Vehicle ID Number<span class="req">*</span>: </label> <input type="text" name="vehicle_num" v-model="formFields.vehicle_num"/>  <br/>
                        </div>
                        <div class="col-6">
                            <label>Year: </label> <input type="text" name="year" v-model="formFields.year"/> <br/>
                            <label>Paint Color: </label> <input type="text" name="paint_color" v-model="formFields.paint_color"/>  <br/>
                            <label>Paint Numbers: </label> <input type="text" name="paint_number" v-model="formFields.paint_number"/> <br/>
                            <label>Manufacture Date: </label> <input type="number" name="manufacture_date" v-model="formFields.manufacture_date"/>  <br/>
                            <label>Miles: </label> <input type="text" name="miles" v-model="formFields.miles"/>
                        </div> 
                    </div>
                    <hr/>
                    <div class="p-3">
                        <button class="btn btn-link" @click="hide">Cancel</button>
                        <button class="btn btn-primary btn-sm" @click.prevent="update" :disabled="disableUpdateButton">Update</button>
                    </div>
                    <small v-if="submitFailed" style="color:red;">Something went wrong. Please try again. If problem persists, contact developer.</small>
                </form>    
            </div>
        </div> 
    </div>
</template>

<script>
import moment from 'moment';
import axios from 'axios';

export default {
    name: "DisplayEditVehicle",
    props: ["vehicleInfo"],
    data() {
        return {
            displayEditVehicle: false,
            currentDate: '',
            currentTime: '',
            formFields: '',
            submitFailed: false,
            allCustomerList: [],
            allModelList: [],
            addNew: false,
        }
    },
     async mounted(){
        this.currentDate = moment().format('MMM DD, YYYY');
        this.currentTime = moment().format('HH:MM A');
        await this.getAllCustomers();
        await this.getAllModelNames();
    },
    computed:{
        disableUpdateButton() {
            return Object.entries(this.vehicleInfo).toString() === Object.entries(this.formFields).toString() || this.formFields.cust_id == '' || this.formFields.license == '' || this.formFields.state == '' || this.formFields.make_model_id == '' || this.formFields.vehicle_num == '';
        },
    },
    methods:{
        async getAllModelNames() {
            try {
                const response = await axios.get(`getAllModelNames`);
                this.allModelList = response.data;
            } catch(exception) {
                console.log("exception: ", exception)
            }
        },
        async getAllCustomers() {
            try {
                const response = await axios.get(`getAllCustomers`);
                this.allCustomerList = response.data;
            } catch(exception) {
                console.log("exception: ", exception)
            }
        },
        async update(){
            this.submitFailed = false;
            try {
                axios.post(`updateGivenVehicle`, this.formFields);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        },
        show() {
            this.formFields = JSON.parse(JSON.stringify(this.vehicleInfo));
            this.displayEditVehicle = true;
        },
        hide() {
            if(this.addNew) {
                setTimeout(() => {
                    this.formFields = {};
                    this.displayEditVehicle = false;
                    this.$emit('reload:vehicle');
                }, 5000);
            } else {
                this.formFields = {};
                this.displayEditVehicle = false;
                this.$emit('reload:vehicle');
            }
        }
    }
}
</script>
