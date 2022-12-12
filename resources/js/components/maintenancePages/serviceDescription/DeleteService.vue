<template>
    <div v-if="deleteService">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    Delete Service Description<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully deleted entry. Please stay and we will redirect you.</h4>
                <h4>Are you sure you want to delete this service?</h4>
                <form class="formBody">
                    <div class="row">
                        <div class="col-6">
                            <label>Service Description: </label>{{serviceDescInfo.service_name}}<br/>
                            <br/>
                        </div>
                    </div>
                    <hr/>
                    <div class="p-3">
                        <button class="btn btn-link" @click="hide">Cancel</button>
                        <button class="btn btn-primary btn-sm" @click.prevent="delService">Delete</button>
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
    name: "DeleteService",
    props: ["serviceDescInfo"],
    data() {
        return {
            deleteService: false,
            currentDate: '',
            currentTime: '',
            submitFailed: false,
            addNew: false,
        }
    },
     async mounted(){
        this.currentDate = moment().format('MMM DD, YYYY');
        this.currentTime = moment().format('HH:MM A');
    },
    methods:{
        async delService(){
            this.submitFailed = false;
            try {
                axios.post(`deleteService`, this.serviceDescInfo);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        },
        show() {
            this.deleteService = true;
        },
        hide() {
            if(this.addNew) {
                setTimeout(() => {
                    this.deleteService = false;
                    this.$emit('reload:serviceDesc');
                }, 5000);
            } else {
                this.deleteService = false;
                this.$emit('reload:serviceDesc');
            }
            
        }
    }
}
</script>
