<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\JournalVoucherData;
use Illuminate\Support\Facades\DB;

class JournalVoucher extends Model
{
    protected $table = "journal_vouchers";
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

    function scopeVoucherNo($query)
    {
        return $voucherNo = DB::selectOne("SELECT generate_journal_voucher_no() AS voucher_no")->voucher_no;
    }

    public function journal_voucher_data()
    {
        return $this->hasMany(JournalVoucherData::class, 'journal_voucher_id', 'id');
    }
}
