<template>
    <div class="container">
        <div class="p-2 pageHeading">General Ledger By Date</div>
        <div class="formFooter">
            <button type="button" class="footerButton p-3" @click="escape">Back</button>
        </div>
        <br/>
        <div class="col pb-2">
            <form class="formBody">
                <label for="from">Date From:</label>
                <input type="date" id="from" name="from" v-model="formFields.from">
                <br/>
                <label for="to">Date To:</label>
                <input type="date" id="to" name="to" v-model="formFields.to" :min="formFields.from" :max="currentDate">
                <br/>
                <div class="p-3">
                    <button class="btn btn-primary btn-sm" @click.prevent="submit"> Submit</button>
                </div>
            </form>
        </div>
        <display-ledger-info ref="displayLedgerInfo" :ledger-info="ledgerByDateData" :from="formFields.from" :to="formFields.to"/>
    </div>
</template>
<script>
import DisplayLedgerInfo from './DisplayLedgerInfo.vue'
import axios from 'axios';

export default {
    name: "GenLedgerByDateMain.vue",
    props:["formType"],
    components:{
        DisplayLedgerInfo
    },
    data() {
        return {
            currentDate: new Date().toISOString().slice(0,10),
            formFields:{
                from: '',
                to: new Date().toISOString().slice(0,10)
            },
            ledgerByDateData: ''
        }
    },
    methods:{
        escape() {
            this.$emit("escape:form",this.formType)
        },
        async submit(){
            this.formFields.from = this.formFields.from != "" ? this.formFields.from : new Date("01-01-2000").toISOString().slice(0,10);
            this.formFields.to = this.formFields.to != "" ? this.formFields.to : this.currentDate;

            const params = {
                params :{
                    from: this.formFields.from,
                    to: this.formFields.to
                }
            }

            try {
                const response = await axios.get(`getPaymentDetailsForLedgerByDate`, params);
                this.ledgerByDateData = response.data;

                this.$nextTick(()=>{
                this.$refs.displayLedgerInfo.show();
            });
            } catch (exception) {
                console.log("exception: ", exception)
            }
        }
    }
}
</script>
