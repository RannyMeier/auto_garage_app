<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class DefaultValues extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['labor_rate','storage_rate','parts_markup','interest_rate','sales_tax_rate','paint_material_rate','owner_name', 'company_name', 'address', 'city', 'zip', 'state', 'phone1', 'phone2'];

    public $table = "default_values";
}
