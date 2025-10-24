<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;
use Session;

class ProductVariant extends Model
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

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->status = 1;
            $model->created_by = Auth::user()->name;
            $model->created_date = date('Y-m-d');
        });

        // static::updating(function ($model) {
        //     $model->updated_by = Auth::user()->name;
        //     $model->updated_date = date('Y-m-d');
        //     $model->updated_time = date("H:i:s");
        // });
    }
}
