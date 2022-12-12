<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Vendors;

class VendorPayWoPo extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['vendor_id','pay_for_id','amount_paid','check_number','payment_date', 'memo'];

    public $table = "vendor_pay_wo_po";

    public function Vendors(){
        return $this->belongsTo(Vendors::class , 'vendor_id' , 'id');
    }
}
