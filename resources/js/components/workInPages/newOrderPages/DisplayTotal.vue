<template>
  <div v-if="displayTotal">
    <div id="modal">
      <div class="small-modal-content">
            <div class="pb-2 pageHeading">
                View Total Cost and Estimates<span class="closeModal pull-right" @click="hide">&times;</span>
            </div>
            <div class="formHeadingFooter p1">
                <span>{{currentTime}}</span>
                Order ID: <span style="font-weight: bolder; color: #000;"> {{orderInfo.id}}</span>
                <span>{{currentDate}}</span>
            </div>
            <br/>
            <div class="formHeadingFooter p1">
                <span>IN: {{orderInfo.date_created}}</span>
                <span>OUT: {{orderInfo.delivery_date}}</span>
            </div>
            <br/>
            <div class="row">
                <div class="col-6">
                    <div class="small-box">
                        <h4 class="bg-color-orange p-1">Estimates</h4>
                        <span style="color:yellow">Parts: </span>$ {{estimatedParts}} <br/>
                        <span style="color:yellow">Labor: </span>{{estimatedLabor}}
                        <hr/>
                        <span style="color:yellow">Total: </span>$ {{estimatesTotal}}
                    </div>
                </div>
                <div class="col-6">
                    <div class="small-box">
                        <h4 class="bg-color-orange p-1">Cost</h4>
                        <span style="color:yellow">Parts: </span>$ {{partsCost}} <br/>
                        <span style="color:yellow">Less Discount:</span> {{totalDiscount}} <br/>
                        <hr/>
                        <span style="color:yellow">Total Parts: </span>{{totalPartsCostDiscount}}<br/>
                        <hr/>
                        <span style="color:yellow">Sublet: </span>{{totalSubletCost}} <br/>
                        <span style="color:yellow">Paint/Material:</span> {{paintMaterialTotal}}<br/>
                        <hr/>
                        <span style="color:yellow">Sub Total: </span>{{totalPartsSubletPaint}}<br/>
                        <hr/>
                        <span style="color:yellow">Tax: </span>{{totalTax}} <br/>
                        <span style="color:yellow">Labor: </span> {{totalLaborCostValue}} <br/>
                        <span style="color:yellow">Hazard Waste: </span> {{totalWaste}} <br/>
                        <span style="color:yellow">Storage: </span> {{totalStorage}} <br/>
                        <span style="color:yellow">Towing: </span> {{totalTowing}}<br/>
                        <hr/>
                        <span style="color:yellow">Total Parts: </span>$ {{finalTotalCost}} <br/>
                    </div>
                </div>
            </div>
        </div>
    </div> 
  </div>
</template>

<script>
    import moment from 'moment';
    export default {
    name: "DisplayTotal",
    props: ["invoices", "orderInfo"],
    data() {
        return {
            displayTotal: false,
            currentDate: "",
            currentTime: "",
            totalEstimatedParts: '',
            totalEstimatedLabor: '',
            totalPartsCost: '',
            totalSubletAmount: '',
            totalPaintMaterialCost: '',
            totalLaborCost:'',
        };
    },
    mounted() {
        this.currentDate = moment().format("MMM DD, YYYY");
        this.currentTime = moment().format("HH:MM A");
    },
    computed:{
        estimatedParts() {
            this.totalEstimatedParts =  this.invoices.reduce(function (result, item) {
                return result+parseFloat(item.est_cost);
            }, 0.00);
            return this.totalEstimatedParts;
        },
        estimatedLabor() {
            this.totalEstimatedLabor =  this.invoices.reduce(function (result, item) {
                return result+parseFloat(item.estimated_labor)
            }, 0.00);
            return this.totalEstimatedLabor
        },
        estimatesTotal() {
            return this.totalEstimatedParts + this.totalEstimatedLabor;
        },
        partsCost() {
            this.totalPartsCost = this.invoices.reduce(function (result, item) {
                return result+parseFloat(item.cust_cost)
            }, 0.00);
            return this.totalPartsCost
        },
        totalDiscount() {
            return parseFloat(this.orderInfo.discount);
        },
        totalPartsCostDiscount() {
            return this.totalDiscount + this.totalPartsCost;
        },
        totalSubletCost() {
            this.totalSubletAmount = this.invoices.reduce(function (result, item) {
                return result+ parseFloat(item.sublet_amount)
            }, 0.00);
            return this.totalSubletAmount
        },
        paintMaterialTotal() {
            this.totalPaintMaterialCost = this.invoices.reduce(function (result, item) {
                return result+parseFloat(item.total_paint_material_cost)
            }, 0.00);
            return this.totalPaintMaterialCost
        },
        totalPartsSubletPaint() {
            return this.totalPaintMaterialCost + this.totalSubletAmount + this.totalDiscount + this.totalPartsCost;
        },
        totalTax() {
            return this.totalPartsSubletPaint * this.orderInfo.tax_rate;
        },
        totalLaborCostValue() {
            this.totalLaborCost = this.invoices.reduce(function (result, item) {
                return result+parseFloat(item.total_labor_cost)
            }, 0.00);
            return this.totalLaborCost
        },
        totalWaste() {
            return parseFloat(this.orderInfo.waste);
        },
        totalTowing() {
            return parseFloat(this.orderInfo.towing);
        },
        totalStorage() {
            return parseFloat(this.orderInfo.storage_cost);
        },
        finalTotalCost() {
            return this.totalPartsSubletPaint + this.totalTax + this.totalLaborCostValue + this.totalWaste + this.totalTowing + this.totalStorage;
        }
    },
    methods: {
        show() {
            this.displayTotal = true;
        },
        hide() {
            this.displayTotal = false;
        },
    },
}
</script>