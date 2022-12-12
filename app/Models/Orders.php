<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Customers;

class Orders extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['cust_id','vehicle_id','ins_adj_id','tax_rate','labor_rate','towing','waste','storage_days','storage_rate','storage_cost','parts_markup','discount','delivery_date', 'date_created'];

    public $table = "orders";

    public function Customers(){
        return $this->belongsTo(Customers::class , 'cust_id' , 'id');
    }
}
