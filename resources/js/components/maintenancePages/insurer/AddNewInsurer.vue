<template>
    <div v-if="addNewInsurer">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    Add New Insurer<span class="closeModal pull-right" @click="hide">&times;</span>
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
                            <label>Company Name<span class="req">*</span>: </label> <input type="text" name="company_name" v-model="formFields.company_name"/> <br/>
                            <label>Fax<span class="req">*</span>: </label> <input type="text" name="fax" v-model="formFields.fax"/> <br/>
                            <label>Address<span class="req">*</span>: </label> <input type="text" name="address" v-model="formFields.address"/>  <br/>
                            <label>City<span class="req">*</span>: </label> <input type="text" name="city" v-model="formFields.city"/>  <br/>
                            <label>State<span class="req">*</span>: </label> <input type="text" name="state" v-model="formFields.state"/>  <br/>
                            <label>Zip<span class="req">*</span>: </label> <input type="text" name="zip" v-model="formFields.zip"/>  <br/>
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
        name: "AddNewInsurer",
        props:{
            sendBackId :{
                required: false
            }
        },
        data() {
            return {
                addNewInsurer: false,
                currentDate: '',
                currentTime: '',
                submitFailed: false,
                insAdjId: 0,
                formFields: {
                    company_name: '',
                    fax: '',
                    address: '',
                    city: '',
                    state: '',
                    zip: '',
                },
                addNew: false
            }
        },
        mounted(){
            this.currentDate = moment().format('MMM DD, YYYY');
            this.currentTime = moment().format('HH:MM A');
        },
        computed:{
            disableSubmitButton() {
                return this.formFields.fax == '' || this.formFields.address == '' || this.formFields.company_name == '' || this.formFields.city == '' || this.formFields.state == '' || this.formFields.zip == '';
            }
        },
        methods: {
            async submit() {
                this.submitFailed = false;
                try {
                    const response = await axios.post(`/addNewInsurance`,this.formFields);
                    this.insAdjId = response.data;
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
                this.addNewInsurer = true;
            },
            hide() {
                if(this.addNew) {
                    setTimeout(() => {
                        this.addNewInsurer = false;
                        if(this.sendBackId == true || this.sendBackId == "true") {
                            this.$emit('ins:id', this.insAdjId);
                        }
                        this.$emit('reload:insurer');
                    }, 5000);
                } else {
                    this.addNewInsurer = false;
                    if(this.sendBackId == true || this.sendBackId == "true") {
                        this.$emit('ins:id', this.insAdjId);
                    }
                    this.$emit('reload:insurer');
                } 
            }
        }
    }
</script>