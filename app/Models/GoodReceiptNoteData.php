<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class GoodReceiptNoteData extends Model
{
    public $table = 'grn_datas';
    public $timestamps = false;
    use HasFactory;
    // In app/Models/GoodReceiptNoteData.php
    protected $fillable = [
        'good_receipt_note_id', // Add this
        'po_data_id',
        'po_id',
        'quotation_no',
        'expiry_date',
        'receive_qty',
        // Other required fields...
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
        return $this->belongsTo(GoodReceiptNote::class, 'companyId');
    }
}
