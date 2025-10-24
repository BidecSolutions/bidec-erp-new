<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Session;
use Auth;
use DB;

class DirectSaleInvoice extends Model
{
    public $timestamps = false;
    use HasFactory;

    // get active record
    function scopeStatus($query, $status)
    {
        if ($status != '') {
            return $query->where('status', $status);
        }
    }

    function scopeVoucherNo($query)
    {
        $prifix = 'DSI';
        $maxReg = SELF::whereRaw("substr(`dsi_no`,-4,2) = ? and substr(`dsi_no`,-2,2) = ?", [date('m'), date('y')])
            ->max(DB::raw("convert(substr(`dsi_no`,4,length(substr(`dsi_no`,4))-4),signed integer)"));
        $reg = $maxReg + 1;
        return $voucherNo = $prifix . $reg . date('my');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->company_id = Session::get('company_id');
            $model->company_location_id = Session::get('company_location_id');
            $model->status = 1;
            $model->created_by = Auth::user()->name;
            $model->created_date = date('Y-m-d');
        });
    }
}
