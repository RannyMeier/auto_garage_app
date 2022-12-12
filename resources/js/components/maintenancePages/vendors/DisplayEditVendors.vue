<template>
    <div v-if="displayEditVendors">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    View/Edit Vendors<span class="closeModal pull-right" @click="hide">&times;</span>
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
                            <label>Name<span class="req">*</span>: </label> <input type="text" name="name" v-model="formFields.name"/> <br/>
                            <br/>
                            <label>PH Number<span class="req">*</span>: </label><input type="text" name="phone" v-model="formFields.phone" placeholder="816123456" @input="acceptNumber('phone')"/><br/>
                            <br/>
                            <label>Address<span class="req">*</span>: </label> <input type="text" name="address" v-model="formFields.address"/> <br/>
                            <br/>
                            <label>City<span class="req">*</span>: </label> <input type="text" name="city" v-model="formFields.city"/>  <br/>
                            <br/>
                            <label>State<span class="req">*</span>: </label> <input type="text" name="state" v-model="formFields.state"/> <br/>
                            <br/>
                            <label>Zip Code<span class="req">*</span>: </label> <input type="number" name="zip" v-model="formFields.zip"/>  <br/>                           
                        </div>
                        <div class="col-6">
                            <label>Fax: </label> <input type="text" name="fax" v-model="formFields.fax"/> 
                            <br/>
                            <label>SS Or Fed ID: </label> <input type="text" name="ss_fed" v-model="formFields.ss_fed"/>
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
    name: "DisplayEditVendors",
    props: ["vendorInfo"],
    data() {
        return {
            displayEditVendors: false,
            currentDate: '',
            currentTime: '',
            formFields: '',
            submitFailed: false,
            addNew: false,
        }
    },
     async mounted(){
        this.currentDate = moment().format('MMM DD, YYYY');
        this.currentTime = moment().format('HH:MM A');
    },
    computed:{
        disableUpdateButton() {
            return Object.entries(this.vendorInfo).toString() === Object.entries(this.formFields).toString() || this.formFields.name == '' || this.formFields.phone == '' || this.formFields.state == '' || this.formFields.address == '' || this.formFields.city == '' || this.formFields.zip == '';
        },
    },
    methods:{
        acceptNumber(value) {
            if(value === 'phone') {
                var x = this.formFields.phone.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                this.formFields.phone = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
            }
        },
        async update(){
            this.submitFailed = false;
            try {
                axios.post(`updateGivenVendors`, this.formFields);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        },
        show() {
            this.formFields = JSON.parse(JSON.stringify(this.vendorInfo));
            this.displayEditVendors = true;
        },
        hide() {
            if(this.addNew) {
                setTimeout(() => {
                    this.formFields = {};
                    this.displayEditVendors = false;
                    this.$emit('reload:vendors');
                }, 5000);
            } else {
                this.formFields = {};
                this.displayEditVendors = false;
                this.$emit('reload:vendors');
            }
        }
    }
}
</script>
