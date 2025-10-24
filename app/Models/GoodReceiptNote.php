<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GoodReceiptNote extends Model
{
    protected $table = 'good_receipt_notes';
    public $timestamps = false;
    use HasFactory;
    protected $fillable = ['supplier_id'];

    // get active record
    function scopeStatus($query, $status)
    {
        if ($status != '') {
            return $query->where('status', $status);
        }
    }

    function scopeVoucherNo($query)
    {
        $prifix = 'GRN';
        $maxReg = SELF::whereRaw("substr(`grn_no`,-4,2) = ? and substr(`grn_no`,-2,2) = ?", [date('m'), date('y')])
            ->max(DB::raw("convert(substr(`grn_no`,4,length(substr(`grn_no`,4))-4),signed integer)"));
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
    public function goodReceiptNote()
    {
        return $this->belongsTo(GoodReceiptNote::class, 'good_receipt_note_id');
    }
    public function goodReceiptNoteData()
    {
        return $this->hasMany(GoodReceiptNoteData::class, 'good_receipt_note_id', 'id');
    }

    // PurchaseOrder.php
    public function purchaseOrderData()
    {
        return $this->hasMany(PurchaseOrderData::class);
    }
    public function items()
{
    return $this->hasMany(GoodReceiptNoteData::class);
}
}
