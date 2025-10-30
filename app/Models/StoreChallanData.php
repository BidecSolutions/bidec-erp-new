<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StoreChallanData extends Model
{
    public $table = 'store_challan_datas';
    public $timestamps = false;
    use HasFactory;
     protected $fillable = [];
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
            $model->company_id = Session::get('company_id');
            $model->location_id = Session::get('company_location_id');
            $model->status = 1;
            $model->created_by = Auth::user()->name;
            $model->created_date = date('Y-m-d');
        });
    }
    public function storeChallan()
    {
        return $this->belongsTo(StoreChallan::class, 'store_challan_id');
    }
    public function storeChallanData()
    {
        return $this->belongsTo(StoreChallan::class, 'companyId');
    }
}
