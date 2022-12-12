<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceAdj extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['company_name', 'fax', 'address', 'city', 'state','zip'];

    public $table = "insurance_adjuster";
}
