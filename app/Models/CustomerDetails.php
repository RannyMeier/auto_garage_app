<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDetails extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['phone1','phone2','first_name','last_name','address','city', 'state', 'zip','contact','comments'];
    protected $guarded = [];

    protected $table = "customer_details";
}
