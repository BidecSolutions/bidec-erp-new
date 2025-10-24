<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Session;
use DB;

class TransferNote extends Model
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

    function scopeVoucherNo($query){
        $prifix = 'TN';
        
        $maxReg = DB::selectOne("select max(convert(substr(`transfer_note_no`,3,length(substr(`transfer_note_no`,3))-4),signed integer)) reg from `transfer_notes` where substr(`transfer_note_no`,-4,2) = ".date('m')." and substr(`transfer_note_no`,-2,2) = ".date('y')."")->reg;
        $reg = $maxReg+1;
        return $voucherNo = $prifix.$reg.date('my');
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
