<template>
    <div v-if="addNewEmployee">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    Add New Employee<span class="closeModal pull-right" @click="hide">&times;</span>
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
                            <label>PH Number<span class="req">*</span>: </label><input type="text" name="phone" v-model="formFields.phone" placeholder="816123456" @input="acceptNumber('phone')"/><br/>
                            <label for="start_date">Start Date<span class="req">*</span>:</label>: <input type="date" id="start_date" name="start_date" v-model="formFields.start_date"> <br/>
                            <label for="end_date">End Date:</label>: <input type="date" id="end_date" name="end_date" v-model="formFields.end_date">
                        </div>
                        <div class="col-6">
                            <label>Address<span class="req">*</span>: </label> <input type="text" name="address" v-model="formFields.address"/> <br/>
                            <label>City<span class="req">*</span>: </label> <input type="text" name="city" v-model="formFields.city"/>  <br/>
                            <label>State<span class="req">*</span>: </label> <input type="text" name="state" v-model="formFields.state"/> <br/>
                            <label>Zip Code<span class="req">*</span>: </label> <input type="number" name="zip" v-model="formFields.zip"/>  <br/>
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
        name: "AddNewEmployee",
        data() {
            return {
                addNewEmployee: false,
                currentDate: '',
                currentTime: '',
                submitFailed: false,
                formFields: {
                    phone: '',
                    first_name: '',
                    last_name: '',
                    address: '',
                    state: '',
                    city: '',
                    zip: '',
                    start_date: '',
                    end_date: '',
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
                return this.formFields.phone == '' || this.formFields.first_name == '' || this.formFields.last_name == '' || this.formFields.address == '' || this.formFields.state == '' || this.formFields.city == '' || this.formFields.zip == '' || this.formFields.start_date == '';
            }
        },
        methods: {
            acceptNumber(value) {
                if(value === 'phone') {
                    var x = this.formFields.phone.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    this.formFields.phone = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
                }
            },
            async submit() {
                this.submitFailed = false;
                try {
                    await axios.post(`/addNewEmployee`,this.formFields);
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
                this.addNewEmployee = true;
            },
            hide() {
                if(this.addNew) {
                    setTimeout(() => {
                        this.addNewEmployee = false;
                        this.$emit('reload:employee');
                    }, 5000);
                } else {
                    this.addNewEmployee = false;
                    this.$emit('reload:employee');
                }
            }
        }
    }
</script>