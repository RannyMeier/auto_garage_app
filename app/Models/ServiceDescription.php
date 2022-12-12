<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceDescription extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['service_name'];

    public $table = "service_description";
}
