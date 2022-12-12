<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Orders;

class PaymentDetails extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['order_id','total_bill','amount_paid','balance','payment_type','credit_card_number','payment_date'];

    public $table = "payment_details";

    public function Orders(){
        return $this->belongsTo(Orders::class , 'order_id' , 'id');
    }
}
