<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Hash;
use Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use DB;

class ParentAuthController extends Controller
{
    public function index()
    {
        return view('parent_module.login');
    }

    public function customLogin(Request $request)
    {
        $request->validate([
            'cnic' => 'required|numeric|digits:13', // Adjust validation rules as needed
        ]);
        $student = DB::table('student_parent_and_guardian_informations')->where('cnic_no',$request->cnic)->first();

        if ($student) {
            Session::put('login_cnic',$student->cnic_no);
            return redirect()->route('parents.dashboard')->with('success', 'Login Successfully'); // Adjust as needed
        }

        return redirect()->back()->withErrors(['cnic' => 'Invalid CNIC or student not found.']);
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return redirect()->route('parentLogin');
    }
}
