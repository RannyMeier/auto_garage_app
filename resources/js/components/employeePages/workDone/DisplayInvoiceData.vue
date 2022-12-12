<template>
    <div v-if="displayInvoiceData">
        <div id="modal">
            <div class="modal-content">
                 <div class="p-2 pageHeading">
                    Display Invoice data<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    Invoice ID: <span>{{invoiceInfo.id}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <br/>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully updated entry. Please stay and we will redirect you.</h4>
                <div class="col pb-2 pt-2">
                    <form class="formBody">
                        <label>Description: {{invoiceInfo.service_name}}</label>
                        <label>Labor Hours: {{invoiceInfo.labor_hours}} @ {{invoiceInfo.labor_rate}} = {{invoiceInfo.total_labor_cost}}</label>
                        <label>Date Done: {{invoiceInfo.date_created}}</label>
                        <label>Estimated Labor: {{invoiceInfo.estimated_labor}}</label>
                        <br/>
                        <hr/>
                        <label for="pay_date">Date Paid:</label>
                        <input type="date" id="pay_date" name="pay_date" v-model="formFields.pay_date">
                        <br/>
                        <br/>
                        <hr/>
                        <br/>
                        <div class="p-3">
                            <button class="btn btn-link" @click="hide">Cancel</button>
                            <button class="btn btn-primary btn-sm" @click.prevent="submit"> Submit</button>
                        </div>
                        <small v-if="submitFailed" style="color:red;">Something went wrong. Please try again. If problem persists, contact developer.</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import moment from 'moment'

    export default {
	    name:"DisplayInvoiceData",
        props:["invoiceInfo"],
        data(){
            return {
                currentDate: '',
                currentTime: '',
                displayInvoiceData: false,
                loadingFailed: false,
                selectedItem:{},
                loading: false,
                submitFailed: false,
                formFields:{
                    pay_date: new Date().toISOString().slice(0,10)
                },
                addNew: false,
            };
        },
        mounted() {
            this.currentDate = moment().format("MMM DD, YYYY");
            this.currentTime = moment().format("HH:MM A");
        },
        methods:{
            async show() {
                this.displayInvoiceData = true;
            },
            hide() {
                if(this.addNew) {
                    setTimeout(() => {
                        this.displayInvoiceData = false;
                        this.$emit('reload:data');
                    }, 5000);
                } else {
                    this.displayInvoiceData = false;
                    this.$emit('reload:data');
                }
            },
            async submit() {
                this.submitFailed = false;

                this.formFields.emp_id = this.invoiceInfo.employee_id;
                this.formFields.invoice_id = this.invoiceInfo.id;
                try {
                    await axios.post(`addNewEmpPayment`, this.formFields);
                    this.addNew = true;
                } catch(exception) {
                    this.submitFailed = true;
                    console.log("exception: ", exception)
                }
                if(!this.submitFailed) {
                    this.hide();
                }
            }
        }
    }
</script>