<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReceiptData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ChartOfAccount extends Model
{
    protected $table = "chart_of_accounts";
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

    public function parent()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_code', 'code')
                ->where('company_id', Session::get('company_id'))
                ->where('company_location_id', Session::get('company_location_id'));
    }

    public function receipt_data()
    {
        return $this->hasMany(ReceiptData::class);
    }

    function scopeGenerateAccountCode($query,$param)
    {
        $maxId = DB::table($this->table)->when($param != '0', function ($q) use ($param){
            return $q->where('parent_code','like',$param);
        }, function ($q) {
            return $q->where('parent_code', 0);
        })->where('company_id',Session::get('company_id'))->where('company_location_id',Session::get('company_location_id'))->max('id');
        $code = '';
        if($param == 0){
            $code = $maxId + 1;
        }else{
            if($maxId == ''){
                $code = $param.'-1';
            }else{
                $maxCodeDetail = DB::table($this->table)->where('id',$maxId)->where('company_id',Session::get('company_id'))->where('company_location_id',Session::get('company_location_id'))->first();
                $maxCode = $maxCodeDetail->code;
                $max = explode('-',$maxCode);
                $code = $param.'-'.(end($max)+1);
            }
        }
        return $code;
    }
}
