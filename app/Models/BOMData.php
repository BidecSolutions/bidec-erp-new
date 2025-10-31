<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class BOMData extends Model
{
    public $table = 'bom_datas';
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
            $model->user_id = Auth::user()->id;
            $model->created_by = Auth::user()->name;
            $model->created_date = date('Y-m-d');
        });
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function size()
    {
        return $this->belongsTo(Product::class, 'size_id', 'id');
    }
}
