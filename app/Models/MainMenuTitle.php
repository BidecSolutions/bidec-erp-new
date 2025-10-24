<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainMenuTitle extends Model{
    protected $table = 'menus';
    protected $fillable = [];
    protected $primaryKey = 'id';
    public $timestamps = false;
}