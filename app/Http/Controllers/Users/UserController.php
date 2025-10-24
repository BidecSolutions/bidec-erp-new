<?php

namespace App\Http\Controllers\users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainMenuTitle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    // public $companyId = Session::get('company_id');
    // public function __construct(){
    //     $this->companyId = Session::get('company_id');
    // }
    public function createMainMenuTitleForm()
    {
        $menuType = array(
            '1' => 'User',
            '2' => 'Purchase',
            '3' => 'Sales',
            '4' => 'Store',
            '5' => 'Finance',
            '6' => 'Setting',
            '7' => 'Reports',
            '8' => 'Dashboard',
            '9' => 'HR'
        );
        return view('Users.createMainMenuTitleForm', compact('menuType'));
    }

    public function createSubMenuForm()
    {
        $MainMenuTitles = new MainMenuTitle;
        $menus = DB::table('menus')->where('status', '=', '1')->get();
        return view('Users.createSubMenuForm', compact('menus'));
    }

    public function viewUsersLoginTimePeriodList()
    {
        return view('Users.viewUsersLoginTimePeriodList');
    }

    public function addUsersLoginTimePeriod()
    {

        $userList = DB::Connection('mysql')->table('users')->where('status', '=', '1')->where('company_id', '=', Session::get('company_id'))->get();
        return view('Users.addUsersLoginTimePeriod', compact('userList'));
    }
}
