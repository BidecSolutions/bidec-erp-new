<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MaterialRequest extends Model
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

    function scopeVoucherNo($query)
    {
        $prifix = 'MR';

        $maxReg = DB::selectOne("select max(convert(substr(`material_request_no`,3,length(substr(`material_request_no`,3))-4),signed integer)) reg from `material_requests` where substr(`material_request_no`,-4,2) = " . date('m') . " and substr(`material_request_no`,-2,2) = " . date('y') . "")->reg;
        $reg = $maxReg + 1;
        return $voucherNo = $prifix . $reg . date('my');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->company_id = Session::get('company_id');
            $model->location_id = Session::get('company_location_id');
            $model->status = 1;
            $model->user_id = Auth::user()->id;
            $model->created_by = Auth::user()->name;
            $model->created_date = date('Y-m-d');
        });
    }
    public function materialRequestData()
    {
        return $this->hasMany(MaterialRequestData::class, 'material_request_id', 'id');
    }
}
