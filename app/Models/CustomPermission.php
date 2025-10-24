<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Auth;
use DB;

class CustomPermission extends Model
{
    protected $table = "permissions";
    use HasFactory;
    //use LogsActivity;
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id','sub_menu_id','name','guard_name'
    ];

    public static function getpermissionGroups()
    {
        $permission_groups = DB::table('permissions')
            ->join('menus', 'permissions.group_id', '=', 'menus.id')
            ->groupBy('permissions.group_id')
            ->groupBy('menus.menu_name')
            ->select('menus.menu_name','permissions.group_id')
            ->get();
        return $permission_groups;
    }

    public static function getpermissionsByGroupName($group_id)
    {
        $permissions = DB::table('permissions')
            ->select('name', 'id')
            ->where('group_id', $group_id)
            ->get();
        return $permissions;
    }
    

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //     ->logOnly(['*']);
    // }
}
