<template>
    <div v-if="deleteVehicle">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    Delete Vehicle<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully deleted entry. Please stay and we will redirect you.</h4>
                <h4>Are you sure you want to delete this vehicle?</h4>
                <form class="formBody">
                    <div class="row">
                        <div class="col-6">
                            <label>Customer: </label> {{vehicleInfo.first_name}} {{vehicleInfo.last_name}}<br/>
                            <label>License: </label> {{vehicleInfo.license}} <br/>
                            <label>State: </label> {{vehicleInfo.state}} <br/>
                            <label>Model: </label> {{vehicleInfo.model_name}}<br/>
                            <label>Vehicle ID Number: </label> {{vehicleInfo.vehicle_num}} <br/>
                        </div>
                        <div class="col-6">
                            <label>Year: </label> {{vehicleInfo.year}} <br/>
                            <label>Paint Color: </label> {{vehicleInfo.paint_color}} <br/>
                            <label>Paint Numbers: </label> {{vehicleInfo.paint_number}} <br/>
                            <label>Manufacture Date: </label> {{vehicleInfo.manufacture_date}}  <br/>
                            <label>Miles: </label> {{vehicleInfo.miles}}
                        </div> 
                    </div>
                    <hr/>
                    <div class="p-3">
                        <button class="btn btn-link" @click="hide">Cancel</button>
                        <button class="btn btn-primary btn-sm" @click.prevent="delVehicle">Delete</button>
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
    name: "DeleteVehicle",
    props: ["vehicleInfo"],
    data() {
        return {
            deleteVehicle: false,
            currentDate: '',
            currentTime: '',
            submitFailed: false,
            addNew: false
        }
    },
     async mounted(){
        this.currentDate = moment().format('MMM DD, YYYY');
        this.currentTime = moment().format('HH:MM A');
    },
    methods:{
        async delVehicle(){
            this.submitFailed = false;
            try {
                axios.post(`deleteVehicle`, this.vehicleInfo);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        },
        show() {
            this.deleteVehicle = true;
        },
        hide() {
            if(this.addNew) {
                setTimeout(() => {
                    this.deleteVehicle = false;
                    this.$emit('reload:vehicle');
                }, 5000);
            } else {
                this.deleteVehicle = false;
                this.$emit('reload:vehicle');
            }
        }
    }
}
</script>
