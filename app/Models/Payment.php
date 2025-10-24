<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PaymentData;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    protected $table = "payments";
    public $timestamps = false;
    use HasFactory;
    protected $fillable = [];

    // get active record
    function scopeStatus($query,$status)
    {
        if($status != ''){
            return $query->where('status',$status);
        }
    }

    function scopeVoucherNo($query,$param){
        if($param == 1){
            $prifix = 'CPV';
        }else if($param == 2){
            $prifix = 'BPV';
        }

        $maxReg = Payment::whereRaw("substr(`pv_no`,-4,2) = ? and substr(`pv_no`,-2,2) = ?", [date('m'), date('y')])
            ->max(DB::raw("convert(substr(`pv_no`,4,length(substr(`pv_no`,4))-4),signed integer)"));
        $reg = $maxReg+1;
        return $voucherNo = $prifix.$reg.date('my');
    }

    public function payment_data()
    {
        return $this->hasMany(PaymentData::class,'payment_id','id');
    }

    public function purchase_order()
    {
        return $this->hasOne(PurchaseOrder::class,'id','po_id');
    }

    public function purchase_invoice()
    {
        return $this->hasOne(PurchaseSaleInvoice::class,'id','pi_id');
    }
}
