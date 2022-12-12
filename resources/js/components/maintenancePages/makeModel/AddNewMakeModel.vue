<template>
    <div v-if="addNewMakeModel">
        <div id="modal">
            <div class="modal-content">
                <div class="pageHeading">
                    Add New Model Description<span class="closeModal pull-right" @click="hide">&times;</span>
                </div>
                <div class="formHeadingFooter p1">
                    <span>{{currentTime}}</span>
                    <span>{{currentDate}}</span>
                </div>
                <h4 v-if="addNew" style="color: darkgreen;">Successfully added new entry. Please stay and we will redirect you.</h4>
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
                        <button class="btn btn-primary btn-sm" @click.prevent="submit" :disabled="disableSubmitButton"> Submit</button>
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
        name: "AddNewMakeModel",
        data() {
            return {
                addNewMakeModel: false,
                currentDate: '',
                currentTime: '',
                submitFailed: false,
                formFields: {
                    model_name: '',
                },
                addNew: false,
            }
        },
        async mounted(){
            this.currentDate = moment().format('MMM DD, YYYY');
            this.currentTime = moment().format('HH:MM A');
        },
        computed:{
            disableSubmitButton() {
                return this.formFields.model_name == '';
            }
        },
        methods: {
            async submit() {
                this.submitFailed = false;
                try {
                    await axios.post(`/addNewMakeModel`,this.formFields);
                    this.addNew = true;
                } catch (exception) {
                    this.submitFailed = true;
                }
                
                if(this.submitFailed == false) {
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
                    this.hide();
                }
            },
            show() {
                this.addNewMakeModel = true;
            },
            hide() {
                if(this.addNew) {
                    setTimeout(() => {
                        this.addNewMakeModel = false;
                    
                        this.$emit('reload:makeModel')
                    }, 5000);
                } else {
                    this.addNewMakeModel = false;
                    this.$emit('reload:makeModel')
                }
            }
        }
    }
</script>