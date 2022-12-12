<template>
  <div v-if="showPaymentsPerEntry">
    <div id="modal">
      <div class="modal-content">
            <div class="p-2 pageHeading">
                Display Payment Info for selected entry<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <br/>
            <div class="formHeadingFooter p1">
                <span>{{currentTime}}</span>
                <span>{{currentDate}}</span>
            </div>
            <br/>
            <div class="col pb-2 pt-2">
                <label>Vendor Name: </label> {{itemInfo.name}} <br/>
                <label>Pay For:</label> {{itemInfo.pay_for}} <br/>
                <label>Check#: </label> {{itemInfo.check_number}} <br/>
                <label>Amount Paid: </label> {{itemInfo.amount_paid}} <br/>
                <label>Memo: </label> {{itemInfo.memo}} <br/>
                <label>Payment Date: </label> {{itemInfo.payment_date}}
            </div>
        </div>
    </div> 
  </div>
</template>

<script>
    import moment from 'moment';
    
    export default {
    name: "ShowPaymentsPerEntry",
    props: [
         "itemInfo", "formType"
    ],
    data() {
        return {
            showPaymentsPerEntry: false,
            currentDate: "",
            currentTime: "",
        };
    },
    async mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    methods: {
        show() {
            this.showPaymentsPerEntry = true;
        },
        hide() {
            this.showPaymentsPerEntry = false;
        },
    }
}
</script>