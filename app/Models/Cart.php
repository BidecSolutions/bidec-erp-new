<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Cart extends Model
{
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

    // default save username
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->company_id = Session::get('company_id');
            $model->company_location_id = Session::get('company_location_id');
            $model->status = 1;
            $model->created_by = Auth::user()->name;
            $model->created_date = date('Y-m-d');
        });

        // static::updating(function ($model) {
        //     $model->updated_by = Auth::user()->name;
        //     $model->updated_date = date('Y-m-d');
        //     $model->updated_time = date("H:i:s");
        // });
    }

    function scopeVoucherNo($query){
        $prifix = 'ORD';
        $maxReg = Cart::whereRaw("substr(`order_no`,-4,2) = ? and substr(`order_no`,-2,2) = ?", [date('m'), date('y')])
            ->max(DB::raw("convert(substr(`order_no`,4,length(substr(`order_no`,4))-4),signed integer)"));
        $reg = $maxReg+1;
        return $voucherNo = $prifix.$reg.date('my');
    }
}
