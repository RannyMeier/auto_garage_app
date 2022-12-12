<template>
    <div v-if="deleteCustomer">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    Delete Customer<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully deleted entry. Please stay and we will redirect you.</h4>
                <h4>Are you sure you want to delete this customer?</h4>
                <form class="formBody">
                    <div class="row">
                        <div class="col-6">
                            <label>First Name: </label> {{customerInfo.first_name}} <br/>
                            <label>Last Name: </label> {{customerInfo.last_name}} <br/>
                            <label>PH Number: </label>{{customerInfo.phone1}}<br/>
                            <label>Contact: </label>{{customerInfo.contact}}  <br/>
                            <label>PH-2 Number: </label>{{customerInfo.phone2}}<br/>
                        </div>
                        <div class="col-6">
                            <label>Address: </label>{{customerInfo.address}} <br/>
                            <label>City: </label>{{customerInfo.city}}  <br/>
                            <label>State: </label>{{customerInfo.state}} <br/>
                            <label>Zip Code: </label>{{customerInfo.zip}}  <br/>
                            <label>Comments: </label> {{customerInfo.comments}} <br/>
                        </div> 
                    </div>
                    <hr/>
                    <div class="p-3">
                        <button class="btn btn-link" @click="hide">Cancel</button>
                        <button class="btn btn-primary btn-sm" @click.prevent="deleteCust">Delete</button>
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
    name: "DeleteCustomer",
    props: ["customerInfo"],
    data() {
        return {
            deleteCustomer: false,
            currentDate: '',
            currentTime: '',
            submitFailed: false,
            addNew: false,
        }
    },
    mounted() {
        this.currentDate = moment().format('MMM DD, YYYY');
        this.currentTime = moment().format('HH:MM A');
    },
    methods:{
        async deleteCust(){
            this.submitFailed = false;
            try {
                const formFields = {id: this.customerInfo.id};
                axios.post(`deleteCustomer`, formFields);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        },
        show() {
            this.deleteCustomer = true;
        },
        hide() {
            if(this.addNew) {
                setTimeout(() => {
                    this.deleteCustomer = false;
                    this.$emit('reload:customers');
                }, 5000);
            } else {
                this.deleteCustomer = false;
                this.$emit('reload:customers');
            }
        }
    }
}
</script>
