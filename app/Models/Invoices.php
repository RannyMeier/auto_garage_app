<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Orders;
use App\Models\VendorPayDetails;

class Invoices extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['order_id','vendor_id','service_desc_id','employee_id','is_sublet','sublet_amount','parts_num','quantity','our_cost','cust_cost','est_cost','is_paint_material','total_paint_material_cost','labor_hours','estimated_labor', 'total_labor_cost','sub_total','sub_total_with_tax','date_created'];

    public $table = "invoices";

    public function Orders(){
        return $this->belongsTo(Orders::class , 'cust_id' , 'id');
    }

    public function VendorPayDetails(){
        return $this->belongsTo(VendorPayDetails::class , 'id' , 'invoice_id');
    }
}
