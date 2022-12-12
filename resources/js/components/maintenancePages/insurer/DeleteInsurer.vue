<template>
    <div v-if="deleteInsurer">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    Delete Insurer<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully deleted entry. Please stay and we will redirect you.</h4>
                <h4>Are you sure you want to delete this insurer?</h4>
                <form class="formBody">
                    <div class="row">
                        <div class="col-6">
                            <label>Company Name: </label>{{insInfo.company_name}}<br/>
                            <label>Fax: </label> {{insInfo.fax}} <br/>
                            <label>Address: </label>{{insInfo.address}}  <br/>
                            <label>City: </label>{{insInfo.city}}  <br/>
                            <label>State: </label> {{insInfo.state}}  <br/>
                            <label>Zip: </label>{{insInfo.zip}}  <br/>
                        </div> 
                    </div>
                    <hr/>
                    <div class="p-3">
                        <button class="btn btn-link" @click="hide">Cancel</button>
                        <button class="btn btn-primary btn-sm" @click.prevent="delInsurer">Delete</button>
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
    name: "DeleteInsurer",
    props: ["insInfo"],
    data() {
        return {
            deleteInsurer: false,
            currentDate: '',
            currentTime: '',
            submitFailed: false,
            addNew: false,
        }
    },
    mounted() {
        this.currentDate = moment().format('MMM DD, YYYY');
        this.currentTime = moment().format('HH:MM A');
    },
    methods:{
        async delInsurer(){
            this.submitFailed = false;
            try {
                axios.post(`deleteInsurer`, this.insInfo);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        },
        show() {
            this.deleteInsurer = true;
        },
        hide() {
            if(this.addNew) {
                 setTimeout(() => {
                    this.deleteInsurer = false;
                    this.$emit('reload:insurer');
                }, 5000);
            } else {
                this.deleteInsurer = false;
                this.$emit('reload:insurer');
            }
        }
    }
}
</script>
