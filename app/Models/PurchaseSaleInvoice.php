<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PurchaseSaleInvoice extends Model
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

    public function scopeVoucherNo($query, $param)
    {
        $prefix = $param == 1 ? 'PI' : 'SI'; // 1 = Purchase, 2 = Sale

        $maxReg = DB::table('purchase_sale_invoices')
            ->selectRaw("MAX(CONVERT(SUBSTR(invoice_no, 3, LENGTH(SUBSTR(invoice_no, 3)) - 4), SIGNED INTEGER)) as reg")
            ->where('invoice_type', $param)
            ->whereRaw("SUBSTR(invoice_no, -4, 2) = ?", [date('m')])
            ->whereRaw("SUBSTR(invoice_no, -2, 2) = ?", [date('y')])
            ->value('reg');

        $reg = ($maxReg ?? 0) + 1;

        return $prefix . $reg . date('my');
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
