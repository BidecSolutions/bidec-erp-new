<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\Menu;
use Auth;

class SubMenu extends Model
{
    public $timestamps = false;
    use HasFactory;
    //use LogsActivity;
    protected $fillable = ['menu_id','sub_menu_icon','sub_menu_name','url','sub_menu_type'];


    // get active record
    function scopeStatus($query,$status)
    {
        if($status != ''){
            return $query->where('status',$status);
        }
    }

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //     ->logOnly(['*']);
    // }

    public function menu()
    {
        return $this->belongsTo(Menu::class,'menu_id','id');
    }

    // default save username
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::user()->name;
            $model->created_date = date('Y-m-d');
        });
    }
}
