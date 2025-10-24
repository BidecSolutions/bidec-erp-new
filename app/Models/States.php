<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\City;
use DB;
use Session;

use Auth;

class States extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = ['country_id','state_name','status','created_by','created_date'];


    // get active record
    function scopeStatus($query,$status)
    {
        if($status != ''){
            return $query->where('status',$status);
        }
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->hasMany(City::class);
    }

    // default save username
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->company_id = Session::get('company_id');
            $model->created_by = Auth::user()->name;
            $model->created_date = date('Y-m-d');
        });
    }
}
