<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CompanyLocations extends Model
{
    public $timestamps = false;
    protected $table = 'company_locations';
    use HasFactory;
    protected $fillable = [
        'company_id',
        'location_code',
        'name',
        'phone_no',
        'email',
        'address',
        'status',
    ];

    // get active record
    function scopeStatus($query, $status)
    {
        if ($status != '') {
            return $query->where('status', $status);
        }
    }


    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::user()->name;
            $model->created_date = date('Y-m-d');
            $model->status = 1;
        });
    }
}
