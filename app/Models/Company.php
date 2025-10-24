<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
class Company extends Model
{
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

    public function nazim()
    {
        return $this->hasOne(Employee::class, 'id', 'nazim_id');
    }

    public function naibnazim()
    {
        return $this->hasOne(Employee::class, 'id', 'naib_nazim_id');
    }

    public function moavin()
    {
        return $this->hasOne(Employee::class, 'id', 'moavin_id');
    }
}
