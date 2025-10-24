<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ChartOfAccount;

class JournalVoucherData extends Model
{
    use HasFactory;

    public function account()
    {
        return $this->hasOne(ChartOfAccount::class,'id','acc_id');
    }
}
