<template>
    <div v-if="displayEditMakeModel">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    View/Edit Model Description<span class="closeModal pull-right" @click="hide">&times;</span>
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
                            <label>Model Name<span class="req">*</span>: </label> <input type="text" name="model_name" v-model="formFields.model_name"/> <br/>
                            <br/>
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
    name: "DisplayEditMakeModel",
    props: ["makeModelInfo"],
    data() {
        return {
            displayEditMakeModel: false,
            currentDate: '',
            currentTime: '',
            formFields: '',
            submitFailed: false,
            addNew: false,
        }
    },
     async mounted(){
        this.currentDate = moment().format('MMM DD, YYYY');
        this.currentTime = moment().format('HH:MM A');
    },
    computed:{
        disableUpdateButton() {
            return Object.entries(this.makeModelInfo).toString() === Object.entries(this.formFields).toString() || this.formFields.model_name == '';
        },
    },
    methods:{
        async update(){
            this.submitFailed = false;
            try {
                axios.post(`updateGivenMakeModel`, this.formFields);
            } catch (exception) {
                this.submitFailed = true;
            }
            if(this.submitFailed == false) {
                this.addNew = true;
                this.hide();
            }
        },
        show() {
            this.formFields = JSON.parse(JSON.stringify(this.makeModelInfo));
            this.displayEditMakeModel = true;
        },
        hide() {
            if(this.addNew) {
                setTimeout(() => {
                    this.formFields = {};
                    this.displayEditMakeModel = false;
                    this.$emit('reload:makeModel');
                }, 5000);
            } else {
                this.formFields = {};
                this.displayEditMakeModel = false;
                this.$emit('reload:makeModel');
            }
            
        }
    }
}
</script>
