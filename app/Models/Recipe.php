<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
class Recipe extends Model
{
    protected $table = "recipes";
    protected $fillable = [];


    public function recipeDatas()
    {
       return $this->hasMany(RecipeData::class,'recipe_id')->where('status', 1);
    }
   
    public function subItem()
    {
        return $this->belongsTo(Subitem::class,'item_id');
    }
    
    function scopeRecipeNo($query)
    {
        $id = DB::table($this->table)->max('recipe_code')+1;
        return  $number = 'Rec/'.date('Y').'/'.sprintf('%03d',$id);
    }
}

