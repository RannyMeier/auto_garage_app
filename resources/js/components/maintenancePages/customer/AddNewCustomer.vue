<template>
    <div v-if="addNewCustomer">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    Add New Customer<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully added new entry. Please stay and we will redirect you.</h4>
                <strong class="p-2">Fields marked with red stars are required fields.</strong>
                <form class="formBody">
                    <div class="row">
                        <div class="col-6">
                            <label>First Name<span class="req">*</span>: </label> <input type="text" name="first_name" v-model="formFields.first_name"/> <br/>
                            <label>Last Name<span class="req">*</span>: </label> <input type="text" name="last_name" v-model="formFields.last_name"/> <br/>
                            <label>PH Number<span class="req">*</span>: </label><input type="text" name="phone1" v-model="formFields.phone1" placeholder="816123456" @input="acceptNumber('phone1')"/><br/>
                            <label>Contact: </label> <input type="text" name="contact" v-model="formFields.contact"/>  <br/>
                            <label>PH-2 Number: </label> <input type="text" name="phone2" v-model="formFields.phone2" placeholder="816123456" @input="acceptNumber('phone2')"/> <br/>
                        </div>
                        <div class="col-6">
                            <label>Address<span class="req">*</span>: </label> <input type="text" name="address" v-model="formFields.address"/> <br/>
                            <label>City<span class="req">*</span>: </label> <input type="text" name="city" v-model="formFields.city"/>  <br/>
                            <label>State<span class="req">*</span>: </label> <input type="text" name="state" v-model="formFields.state"/> <br/>
                            <label>Zip Code<span class="req">*</span>: </label> <input type="number" name="zip" v-model="formFields.zip"/>  <br/>
                            <label>Comments: </label> <textarea name="comments" v-model="formFields.comments" rows="5"/> <br/>
                        </div> 
                    </div>
                    <hr/>
                    <div class="p-3">
                        <button class="btn btn-link" @click="hide">Cancel</button>
                        <button class="btn btn-primary btn-sm" @click.prevent="submit" :disabled="disableSubmitButton"> Submit</button>
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
        name: "AddNewCustomer",
        props:{
            sendBackId :{
                required: false
            }
        },
        data() {
            return {
                addNewCustomer: false,
                currentDate: '',
                currentTime: '',
                submitFailed: false,
                custId: 0,
                formFields: {
                    phone1: '',
                    phone2: '',
                    first_name: '',
                    last_name: '',
                    address: '',
                    state: '',
                    city: '',
                    zip: '',
                    contact: '',
                    comments: '',
                },
                addNew: false,
            }
        },
        mounted(){
            this.currentDate = moment().format('MMM DD, YYYY');
            this.currentTime = moment().format('HH:MM A');
        },
        computed:{
            disableSubmitButton() {
                return this.formFields.phone1 == '' || this.formFields.first_name == '' || this.formFields.last_name == '';
            }
        },
        methods: {
            acceptNumber(value) {
                if(value === 'phone1') {
                    var x = this.formFields.phone1.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    this.formFields.phone1 = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
                } else if(value === 'phone2') {
                    var x = this.formFields.phone2.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    this.formFields.phone2 = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
                }
            },
            async submit() {
                this.submitFailed = false;
                try {
                    const response = await axios.post(`/addNewCustomer`,this.formFields);
                    this.custId = response.data;
                } catch (exception) {
                    this.submitFailed = true;
                }
                
                if(this.submitFailed == false) {
                    var self = this;
                    Object.keys(self.formFields).forEach(function(key,index) {
                        if(typeof self.formFields[key] === "string") 
                            self.formFields[key] = ''; 
                        else if (typeof self.formFields[key] === "boolean") 
                            self.formFields[key] = false;
                        else {
                            self.formFields[key] = '';
                        } 
                    });

                    this.addNew = true;
                    this.hide();
                }
            },
            show() {
                this.addNewCustomer = true;
            },
            hide() {
                if(this.addNew) {
                    setTimeout(() => {
                        this.addNewCustomer = false;

                        if(this.sendBackId == true || this.sendBackId == "true") {
                            this.$emit('cust:id', this.custId);
                        }
                        this.$emit('reload:customers');
                    }, 5000);
                } else {
                    this.addNewCustomer = false;

                    if(this.sendBackId == true || this.sendBackId == "true") {
                        this.$emit('cust:id', this.custId);
                    }
                    this.$emit('reload:customers');
                }
                
            }
        }
    }
</script>