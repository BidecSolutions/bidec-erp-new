<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReceiptData;
use DB;
use Session;

class Receipt extends Model
{
    protected $table = "receipts";
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
            $prifix = 'CRV';
        }else if($param == 2){
            $prifix = 'BRV';
        }

        $maxReg = Receipt::whereRaw("substr(`rv_no`,-4,2) = ? and substr(`rv_no`,-2,2) = ?", [date('m'), date('y')])
            ->max(\DB::raw("convert(substr(`rv_no`,4,length(substr(`rv_no`,4))-4),signed integer)"));
        $reg = $maxReg+1;
        return $voucherNo = $prifix.$reg.date('my');
    }

    public function receipt_data()
    {
        return $this->hasMany(ReceiptData::class,'receipt_id','id');
    }

    public function direct_sale_invoice()
    {
        return $this->hasOne(DirectSaleInvoice::class,'id','dsi_id');
    }

    public function sale_invoice()
    {
        return $this->hasOne(PurchaseSaleInvoice::class,'id','si_id');
    }
}
