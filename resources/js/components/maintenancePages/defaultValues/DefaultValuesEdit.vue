<template>
    <div class="container">
         <div class="p-2 pageHeading">Default Values</div>
        <div class="formFooter">
            <button type="button" class="footerButton p-3" @click="escape">Back</button>
        </div>
        <br/>
        <div class="col pb-2">
            <h4 v-if="addNew" style="color: darkgreen;">Successfully updated entry. Please stay and we will redirect you.</h4>
            <form class="formBody">
                <div class="row">
                    <div class="col-6">
                        <label>Labor Rate<span class="req">*</span>: </label> <input type="text" name="labor_rate" v-model="formFields.labor_rate"/> <br/>
                        <br/>
                        <label>Storage Rate<span class="req">*</span>: </label> <input type="text" name="storage_rate" v-model="formFields.storage_rate"/> <br/>
                        <br/>
                        <label>Parts Markup<span class="req">*</span>: </label> <input type="text" name="parts_markup" v-model="formFields.parts_markup"/>  <br/>
                    </div>
                    <div class="col-6">
                        <label>Interest Rate<span class="req">*</span>: </label> <input type="text" name="interest_rate" v-model="formFields.interest_rate"/> <br/>
                        <label>Sales Tax Rate<span class="req">*</span>: </label> <input type="text" name="sales_tax_rate" v-model="formFields.sales_tax_rate"/>  <br/>
                        <label>Paint Material Rate<span class="req">*</span>: </label> <input type="text" name="paint_material_rate" v-model="formFields.paint_material_rate"/> <br/>
                        <label>Owner Name<span class="req">*</span>: </label> <input type="text" name="owner_name" v-model="formFields.owner_name"/>  <br/>
                        <label>Company Name<span class="req">*</span>: </label> <input type="text" name="company_name" v-model="formFields.company_name"/> <br/>
                        <label>Address<span class="req">*</span>: </label> <input type="text" name="address" v-model="formFields.address"/> <br/>
                        <label>City<span class="req">*</span>: </label> <input type="text" name="city" v-model="formFields.city"/> <br/>
                        <label>State<span class="req">*</span>: </label> <input type="text" name="state" v-model="formFields.state"/> <br/>
                        <label>Zip<span class="req">*</span>: </label> <input type="text" name="zip" v-model="formFields.zip"/> <br/>
                        <label>PH Number 1<span class="req">*</span>: </label><input type="text" name="phone1" v-model="formFields.phone1" placeholder="816123456" @input="acceptNumber('phone1')"/><br/>
                        <label>PH Number 2<span class="req">*</span>: </label><input type="text" name="phone2" v-model="formFields.phone2" placeholder="816123456" @input="acceptNumber('phone2')"/><br/>
                    </div> 
                </div>
                <hr/>
                <div class="p-3">
                    <button class="btn btn-link" @click="escape">Cancel</button>
                    <button class="btn btn-primary btn-sm" @click.prevent="update" :disabled="disableSubmitButton"> Update</button>
                </div>
                <small v-if="submitFailed" style="color:red;">Something went wrong. Please try again. If problem persists, contact developer.</small>
            </form>    
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    
    export default {
        name: "DefaultValuesEdit",
        props: ["formType"],
        data() {
            return {
                submitFailed: false,
                formFields:{},
                addNew: false,
            }
        },
        async mounted() {
            await this.getDefaultValues();
        },
        computed:{
            disableSubmitButton() {
                return this.formFields.labor_rate == '' || this.formFields.storage_rate == '' || this.formFields.parts_markup == '' || this.formFields.interest_rate == '' || this.formFields.sales_tax_rate == '' || this.formFields.paint_material_rate == '' || this.formFields.owner_name == '' || this.formFields.company_name == '' || this.formFields.address == '' || this.formFields.city == '' || this.formFields.state == '' || this.formFields.zip == '' ||this.formFields.phone1 == '' || this.formFields.phone2 == '';
            },
        },
        methods:{
            checkVals(value) {
                if(typeof value != "string") {
                    return value == null ? 0.00 : parseFloat(value);
                } else {
                    return value.trim() == "" || value == null ? 0.00 : parseFloat(value)
                }
            },
            async getDefaultValues() {
                this.loading = true;

                try {
                    const response = await axios.get(`getDefaultValues`);
                    
                    this.formFields = response.data[0];
                } catch(exception) {
                    console.log("exception: ", exception)
                }
                
                this.loading = false;
            },
            escape() {
                if(this.addNew) {
                    setTimeout(() => {
                        this.$emit("escape:form",this.formType)
                    }, 5000);
                } else {
                    this.$emit("escape:form",this.formType)
                }
            },
            acceptNumber(value) {
                if(value === 'phone1') {
                    var x = this.formFields.phone1.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    this.formFields.phone1 = !x[2] ? x[1] : '(' + x[1] + ')' + x[2] + (x[3] ? '-' + x[3] : '');
                } else if(value === 'phone2') {
                    var x = this.formFields.phone2.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                    this.formFields.phone2 = !x[2] ? x[1] : '(' + x[1] + ')' + x[2] + (x[3] ? '-' + x[3] : '');
                }
            },
            update() {
                this.submitFailed = false;
                try {
                    axios.post(`updateDefaultValues`, this.formFields);
                } catch (exception) {
                    this.submitFailed = true;
                }
                if(this.submitFailed == false) {
                    this.addNew = true;
                    this.escape();
                }
            }
        }
    }
</script>