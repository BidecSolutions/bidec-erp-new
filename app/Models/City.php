<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\States;
use Auth;
use Session;

class City extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $fillable = ['state_id','city_name','status','created_by','created_date'];


    // get active record
    function scopeStatus($query,$status)
    {
        if($status != ''){
            return $query->where('status',$status);
        }
    }

    public function state()
    {
        return $this->belongsTo(States::class);
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
