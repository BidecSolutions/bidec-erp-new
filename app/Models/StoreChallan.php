<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StoreChallan extends Model
{
    protected $table = 'store_challans';
    public $timestamps = false;
    use HasFactory;
    protected $fillable = ['department_id'];

    // get active record
    function scopeStatus($query, $status)
    {
        if ($status != '') {
            return $query->where('status', $status);
        }
    }

    function scopeVoucherNo($query)
    {
        $prifix = 'SC';

        $maxReg = DB::selectOne("select max(convert(substr(`store_challan_no`,3,length(substr(`store_challan_no`,3))-4),signed integer)) reg from `store_challans` where substr(`store_challan_no`,-4,2) = " . date('m') . " and substr(`store_challan_no`,-2,2) = " . date('y') . "")->reg;
        $reg = $maxReg + 1;
        return $voucherNo = $prifix . $reg . date('my');
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
        return $this->hasMany(StoreChallanData::class, 'store_challan_id', 'id');
    }

    // PurchaseOrder.php
    public function materialRequestData()
    {
        return $this->hasMany(MaterialRequestData::class);
    }
    public function items()
{
    return $this->hasMany(StoreChallanData::class);
}
}
