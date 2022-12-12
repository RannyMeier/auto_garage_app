<template>
    <div v-if="addWoPoPayment">
        <div id="modal">
            <div class="modal-content">
                <div class="p-2 pageHeading">
                    Deposit Payment Slip<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully added new entry. Please stay and we will redirect you.</h4>
                <form class="formBody">
                    <div>
                        <div>
                            <label style="width: 20%;">Vendor: </label>
                            <select v-model="formFields.vendor_id">
                                <option v-for="item in vendors" v-bind:key="item.id" :value="item.id">
                                    {{item.id}}
                                </option>
                            </select>
                            <br/>
                            <label>Payment Amount: </label> <input type="text" name="amount_paid" v-model="formFields.amount_paid"/>
                            <br/>
                            <label style="width: 20%;">Pay For: </label>
                            <select v-model="payForValue" @click="chosenPayFor">
                                <option value=""></option>
                                <option value="add_new_pay_for">Add New PayFor</option>
                                <option v-for="item in payFor" v-bind:key="item.id" :value="item.id">
                                    {{item.pay_for}}
                                </option>
                            </select>
                            <br/>
                            <label>Check Number: </label><input type="text" name="check_number" v-model="formFields.check_number" />
                            <br/>
                            <label>Memo: </label><input type="text" name="memo" v-model="formFields.memo" />
                            <br/>
                        </div>
                    </div>
                    <div class="p-3">
                        <button class="btn btn-link" @click="hide">Cancel</button>
                        <button class="btn btn-primary btn-sm" @click.prevent="submit" :disabled="disableSubmitButton"> Submit</button>
                    </div>
                    <small v-if="submitFailed" style="color:red;">Something went wrong. Please try again. If problem persists, contact developer.</small>
                </form>
            </div>
        </div>
        <add-new-pay-for ref="addNewPayFor" @reload:data="getPayForId" />
    </div>
</template>

<script>
import moment from 'moment';
import axios from 'axios';
import AddNewPayFor from '../payVendorsWithPo/AddNewPayFor.vue';

export default {
    name: "AddWoPoPayment",
    components:{
        AddNewPayFor
    },
    data() {
        return {
            today: new Date().toISOString().slice(0,10),
            addWoPoPayment: false,
            submitFailed: false,
            currentDate: "",
            currentTime: "",
            payForValue: '',
            vendors: [],
            payFor:[],
            formFields: {
                vendor_id: '',
                amount_paid: '',
                check_number: '',
                pay_for_id: '',
                memo:''
            },
            addNew: false,
        }
    },
    mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    computed:{
        disableSubmitButton() {
            const reqFields = this.formFields.amount_paid == '' || this.formFields.check_number == '' || this.formFields.vendor_id == '' || this.formFields.pay_for_id == '';
            return reqFields;
        },
    },
    methods: {
        async getPayForId(value) {
            if(value !== 0) {
                this.formFields.pay_for_id = value.pay_for.id;
                this.payForValue = this.formFields.pay_for_id;
                await this.getAllPayFor();
            } else {
                this.payForValue = '';
            }
        },
        async chosenPayFor() {
            if(this.payForValue == "add_new_pay_for"){
                this.$nextTick(()=>{
                    this.$refs.addNewPayFor.show();
                });
            } else {
                this.formFields.pay_for_id = this.payForValue;
                // await this.getAllPayFor();
            }    
        },
        async getAllVendors() {
            try {
                const response = await axios.get(`getAllVendors`);
                this.vendors = response.data;
            }catch (exception) {
                console.log("exception: ", exception);
            }
        },
        async getAllPayFor() {
            try {
                const response = await axios.get(`getAllPayFor`);
                this.payFor = response.data;
            }catch (exception) {
                console.log("exception: ", exception);
            }
        },
        async show() {
            this.addWoPoPayment = true;
            await this.getAllVendors();
            await this.getAllPayFor();
        },
        hide() {
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
            if(this.addNew) {
                setTimeout(() => {
                    this.addWoPoPayment = false;

                    this.$emit('reload:data');
                }, 5000);
            } else {
                this.addWoPoPayment = false;
                this.$emit('reload:data');
            }
        },
        async submit() {
            this.submitFailed = false;
            try {
                await axios.post(`addNewWoPo`, this.formFields);
                
            } catch (exception) {
                this.submitFailed = true;
                console.log("exception: ", exception)
            }

            if(this.submitFailed == false) {
                this.addNew = true;
               this.hide();
            }
        }
    }
}
</script>
