<template>
    <div v-if="displayEditInsAdj">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    View/Edit Insurance Adjuster<span class="closeModal pull-right" @click="hide">&times;</span>
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
                            <label>Adjuster Name<span class="req">*</span>: </label> <input type="text" name="adjuster_name" v-model="formFields.adjuster_name"/> <br/>
                            <label>Insurance Company<span class="req">*</span>: </label>
                            <select v-model="formFields.ins_id" style="width: 55%;margin-left:2rem;">
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
    name: "DisplayEditInsAdj",
    props: ["insAdjInfo"],
    data() {
        return {
            displayEditInsAdj: false,
            currentDate: '',
            currentTime: '',
            formFields: '',
            submitFailed: false,
            company:'',
            companies:[],
            addNew: false,
        }
    },
    async mounted() {
        this.currentDate = moment().format('MMM DD, YYYY');
        this.currentTime = moment().format('HH:MM A');
        await this.getAllInsurers();
    },
    computed:{
        disableUpdateButton() {
            return Object.entries(this.insAdjInfo).toString() === Object.entries(this.formFields).toString() || this.formFields.phone == '' || this.formFields.adjuster_name == '' || this.formFields.ins_id == '';
        },
    },
    methods:{
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
        async update(){
            this.submitFailed = false;
            try {
                axios.post(`updateAdjuster`, this.formFields);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        },
        show() {
            this.formFields = JSON.parse(JSON.stringify(this.insAdjInfo));
            this.displayEditInsAdj = true;
        },
        hide() {
            if(this.addNew) {
                setTimeout(() => {
                    this.formFields = {};
                    this.displayEditInsAdj = false;
                    this.$emit('reload:insAdj');
                }, 5000);
            } else {
                this.formFields = {};
                this.displayEditInsAdj = false;
                this.$emit('reload:insAdj');
            }
        }
    }
}
</script>
