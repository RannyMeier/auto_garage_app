<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Invoices;

class VendorPayDetails extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['vendor_id','invoice_id', 'pay_for_id', 'total_bill','amount_paid','balance','payment_type','card_check_number','payment_date', 'memo'];

    public $table = "vendor_pay_details";

    public function Invoices(){
        return $this->belongsTo(Invoices::class , 'invoice_id' , 'id');
    }
}
