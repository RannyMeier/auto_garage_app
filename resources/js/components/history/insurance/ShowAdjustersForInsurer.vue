<template>
    <div v-if="showAdjustersForInsurer">
        <div id="modal">
            <div class="modal-content">
                <div class="p-2 pageHeading">
                    Adjusters for selected Insurance<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <br/>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    Insurance Name: <span>{{insInfo.company_name}}</span>
                    Address: <span>{{insInfo.address}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <br/>
                <h3 v-if="tableBody.length==0" class="p-3">
                    No order details found.
                </h3>
                <div v-else class="col pb-2 pt-2">
                    <table class="table">
                        <thead>
                            <tr>
                                <th v-for="item in tableHeader" v-bind:key="item">{{item}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item,index) in tableBody" v-bind:key="index" @click="openItem(item)">
                                <td>{{item.id}}</td>
                                <td>{{item.adjuster_name}}</td>
                                <td>{{item.phone}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <show-orders-per-adjuster :form-type="formType" ref="showOrdersPerAdjuster" :adjuster-info="selectedItem" @reload:data="getAdjustersForGivenInsurance" />
    </div>
</template>

<script>
import moment from 'moment';
import axios from 'axios';
import ShowOrdersPerAdjuster from './ShowOrdersPerAdjuster.vue';
    
export default {
    name: "ShowAdjustersForInsurer",
    props: ["insInfo", "formType"],
    components:{
        ShowOrdersPerAdjuster
    },
    data() {
        return {
            showAdjustersForInsurer: false,
            currentDate: "",
            currentTime: "",
            tableHeader: ['Adjuster ID', 'Adjuster Name','Phone'],
            tableBody: [],
            selectedItem: {},
        }
    },
    mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    methods: {
        async getAdjustersForGivenInsurance() {
            const params = { params: { ins_id: this.insInfo.id}};
            this.distinctOrderIds = [];
            const response = await axios.get(`getAdjustersForGivenInsurance`, params);
            this.tableBody = response.data;
        },
        async show() {
            this.showAdjustersForInsurer = true;
            
            await this.getAdjustersForGivenInsurance();
        },
        hide() {
            this.showAdjustersForInsurer = false;
        },
        openItem(item) {
            this.selectedItem = item;
            this.$nextTick(()=>{
                this.$refs.showOrdersPerAdjuster.show();
            });
        }
    }
}
</script>
