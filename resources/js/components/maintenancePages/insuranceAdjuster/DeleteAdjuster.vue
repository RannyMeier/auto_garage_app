<template>
    <div v-if="deleteAdjuster">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    Delete Insurance Adjuster<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully deleted entry. Please stay and we will redirect you.</h4>
                <h4>Are you sure you want to delete this adjuster?</h4>>
                <form class="formBody">
                    <div class="row">
                        <div class="col-6">
                            <label>Adjuster Name: </label> {{insAdjInfo.adjuster_name}}<br/>
                            <label>Insurance Company: </label> {{insAdjInfo.company_name}} <br/>
                            <label>PH Number: </label>{{insAdjInfo.phone}}<br/>
                            <label>Fax: </label>{{insAdjInfo.fax}} <br/>
                            <label>Car: </label> {{insAdjInfo.car}} <br/>
                        </div>
                    </div>
                    <hr/>
                    <div class="p-3">
                        <button class="btn btn-link" @click="hide">Cancel</button>
                        <button class="btn btn-primary btn-sm" @click.prevent="delAdjuster">Delete</button>
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
    name: "DeleteAdjuster",
    props: ["insAdjInfo"],
    data() {
        return {
            deleteAdjuster: false,
            currentDate: '',
            currentTime: '',
            submitFailed: false,
            addNew: false,
        }
    },
    async mounted() {
        this.currentDate = moment().format('MMM DD, YYYY');
        this.currentTime = moment().format('HH:MM A');
    },
    methods:{
        async delAdjuster(){
            this.submitFailed = false;
            try {
                axios.post(`deleteAdjuster`, this.insAdjInfo);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        },
        show() {
            this.deleteAdjuster = true;
        },
        hide() {
            if(this.addNew) {
                setTimeout(() => {
                    this.deleteAdjuster = false;
                    this.$emit('reload:insAdj');
                }, 5000);
            } else {
                this.deleteAdjuster = false;
                this.$emit('reload:insAdj');
            }
        }
    }
}
</script>
