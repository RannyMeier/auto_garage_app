<template>
    <div v-if="addNewInsAdj">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    Add New Insurance Adjuster<span class="closeModal pull-right" @click="hide">&times;</span>
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
                            <label>Adjuster Name<span class="req">*</span>: </label> <input type="text" name="adjuster_name" v-model="formFields.adjuster_name"/> <br/>
                            <label>Insurance Company<span class="req">*</span>: </label>
                            <select v-model="company" @click="chosenCompany" style="width: 55%;margin-left:2rem;">
                                <option value=""></option>
                                <option value="add_new_insurer">Add New Insurer</option>
                                <option v-for="item in companies" v-bind:key="item.id" :value="item.id">
                                    {{item.company_name}} {{item.address}} {{item.zip}}
                                </option>
                            </select>
                            <label>PH Number<span class="req">*</span>: </label><input type="text" name="phone" v-model="formFields.phone" placeholder="816123456" @input="acceptNumber('phone')"/><br/>
                            <label>Fax<span class="req">*</span>: </label> <input type="text" name="fax" v-model="formFields.fax"/> <br/>
                            <label>Car<span class="req">*</span>: </label> <input type="text" name="car" v-model="formFields.car"/>  <br/>
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
        <add-new-insurer ref="addNewInsurer" :send-back-id="true" @ins:id="getInsId" />
    </div>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';
    import AddNewInsurer from '../insurer/AddNewInsurer.vue';

    export default {
        name: "AddNewInsAdj",
        components:{
            AddNewInsurer
        },
        props:{
            sendBackId :{
                required: false
            }
        },
        data() {
            return {
                addNewInsAdj: false,
                currentDate: '',
                currentTime: '',
                submitFailed: false,
                insAdjId: 0,
                companies: [],
                company: '',
                formFields: {
                    phone: '',
                    adjuster_name: '',
                    ins_id: '',
                    fax: '',
                    car: '',
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
                return this.formFields.phone == '' || this.formFields.adjuster_name == '' || this.formFields.ins_id == '';
            }
        },
        methods: {
            async getInsId(value) {
                if(value !== 0) {
                    this.formFields.ins_id = value.insAdj.id;
                    this.company = this.formFields.ins_id;
                    await this.getAllInsurers();
                } else {
                    this.company = '';
                }
            },
            async getAllInsurers() {
                try {
                    const response = await axios.get(`getAllInsurers`);
                    this.companies = response.data;
                } catch(exception) {
                    console.log("exception: ", exception);
                }
            },
            acceptNumber(value) {
                if(value === 'phone') {
                    var x = this.formFields.phone.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    this.formFields.phone = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
                }
            },
            async chosenCompany() {
                if(this.company == "add_new_insurer"){
                    this.$nextTick(()=>{
                        this.$refs.addNewInsurer.show();
                    });
                } else {
                    this.formFields.ins_id = this.company;
                    await this.getAllInsurers();
                }    
            },
            async submit() {
                this.submitFailed = false;
                try {
                    const response = await axios.post(`/addNewAdjuster`,this.formFields);
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
                this.addNewInsAdj = true;
            },
            hide() {
                if(this.addNew) {
                    setTimeout(() => {
                        this.addNewInsAdj = false;
                        if(this.sendBackId == true || this.sendBackId == "true") {
                            this.$emit('insAdj:id', this.insAdjId);
                        }
                        this.$emit('reload:insAdj');
                    }, 5000);
                } else {
                    this.addNewInsAdj = false;
                    if(this.sendBackId == true || this.sendBackId == "true") {
                        this.$emit('insAdj:id', this.insAdjId);
                    }
                    this.$emit('reload:insAdj');
                }
            }
        }
    }
</script>