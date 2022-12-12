<template>
    <div class="container">
        <div class="p-2 pageHeading" v-if="formType == 'expenses_date_type'" >Expenses By Date and Type</div>
        <div class="p-2 pageHeading" v-else >Expenses By Type</div>
        <div class="formFooter">
            <button type="button" class="footerButton p-3" @click="escape">Back</button>
        </div>
        <br/>
        <div class="col pb-2">
            <form class="formBody">
                <label v-if="formType == 'expenses_date_type'" for="from">Date From:</label>
                <input v-if="formType == 'expenses_date_type'" type="date" id="from" name="from" v-model="formFields.from" />
                <br/>
                <label v-if="formType == 'expenses_date_type'" for="to">Date To:</label>
                <input v-if="formType == 'expenses_date_type'" type="date" id="to" name="to" v-model="formFields.to" :min="formFields.from" :max="currentDate">
                <br/>
                <label style="width: 20%;">Pay For: </label>
                <select v-model="formFields.pay_for_id">
                    <option v-for="item in payFor" v-bind:key="item.id" :value="item.id">
                        {{item.pay_for}}
                    </option>
                </select>
                <br/>
                <div class="p-3">
                    <button class="btn btn-primary btn-sm" :disabled="disableSubmit" @click.prevent="submit"> Submit</button>
                </div>
            </form>
        </div>
        <payment-details-per-pay-for ref="paymentInfo" :form-type="formType" :payment-info="paymentInfo" :from="formFields.from" :to="formFields.to" />
    </div>
</template>
<script>
import axios from 'axios';
import PaymentDetailsPerPayFor from './PaymentDetailsPerPayFor.vue';

export default {
    name: "ExpenseHistory",
    props:["formType"],
    components:{
        PaymentDetailsPerPayFor
    },
    data() {
        return {
            currentDate: new Date().toISOString().slice(0,10),
            formFields:{
                from: '',
                to: new Date().toISOString().slice(0,10),
                pay_for_id: '',
            },
            payFor: [],
            paymentInfo: '',
            payForValue:''
        }
    },
    async mounted(){
        await this.getAllPayFor(); 
    },
    computed:{
        disableSubmit() {
            return this.formFields.pay_for_id == '';
        }
    },
    methods:{
        escape() {
            this.$emit("escape:form",this.formType)
        },
        async getAllPayFor() {
            try {
                const response = await axios.get(`getAllPayFor`);
                this.payFor = response.data;
            } catch(exception) {
                console.log('exception:', exception);
            }
        },
        async submit(){
            this.formFields.from = this.formFields.from != "" ? this.formFields.from : new Date("01-01-2000").toISOString().slice(0,10);
            this.formFields.to = this.formFields.to != "" ? this.formFields.to : this.currentDate;

            const params = {
                params :{
                    from: this.formFields.from,
                    to: this.formFields.to,
                    pay_for_id: this.formFields.pay_for_id
                }
            }
            
            try {
                if(this.formType == 'expenses_date_type') {
                    const response = await axios.get(`getPaymentDetailsWoPoByDateAndType`, params);
                    this.paymentInfo = response.data;
                } else {
                    const response = await axios.get(`getPaymentDetailsWoPoByType`, params);
                    this.paymentInfo = response.data;
                }
                
                this.$nextTick(()=>{
                    this.$refs.paymentInfo.show();
                });
                } catch (exception) {
                    console.log("exception: ", exception)
                }
        }
    }
}
</script>
