<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReturnGoodReceiptNote extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'return_good_receipt_notes';
    function scopeVoucherNo($query)
    {
        $prifix = 'RGRN';
        $maxReg = self::whereRaw("substr(`return_grn_no`,-4,2) = ? and substr(`return_grn_no`,-2,2) = ?", [date('m'), date('y')])
            ->max(DB::raw("convert(substr(`return_grn_no`,4,length(substr(`return_grn_no`,4))-4),signed integer)"));
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
