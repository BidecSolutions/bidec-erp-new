<?php

namespace App\Http\Controllers\Users;
use App\Http\Controllers\Controller;
use App\Helpers\CommonHelper;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\User;
use Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UsersDataCallController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware(['auth','MultiDB']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

     public function filterUsersLoginTimePeriodAndRolePermissionList()
    {
        // Retrieve session and authenticated user data
        $companyId = Session::get('company_id');
        $schoolCampusId = Session::get('company_location_id');
        $authUser = Auth::user();

        // Define the authority users based on account type using a match expression
        $authorityUsers = match ($authUser->acc_type) {
            'client' => ['superadmin', 'user', 'superuser', 'owner'],
            'owner' => ['superadmin', 'user', 'superuser'],
            'superadmin', 'superuser' => ['user', 'superuser'],
            default => [],
        };

        // Parse company IDs from the authenticated user's data
        $companyIdsString = $authUser->company_id;
        $companyIds = explode('<*>', $companyIdsString);

        // Build the base query to filter users
        $query = User::whereIn('acc_type', $authorityUsers);
            if($authUser->acc_type == 'client'){

            }else{
                $query->whereIn('company_id', $companyIds);
            }
            

        // // Apply campus filtering based on the employee type
        // if ($authUser->emp_type_multiple_campus == 1) {
        //     $query->where('company_location_id', $schoolCampusId);
        // } else {
        //     // Decode the JSON string to an array and extract campus IDs
        //     $campusArray = json_decode($authUser->company_location_ids_array, true); // Assuming JSON-encoded string
        //     $campusIds = array_column($campusArray, 'company_location_id');
        //     $query->whereIn('company_location_id', $campusIds);
        // }

        // Execute the query to get user details
        $userDetails = $query->get();

        // Return the view with the retrieved user data
        return view('Users.AjaxPages.filterUsersLoginTimePeriodAndRolePermissionList', compact('userDetails'));
    }

    public function loadSchoolCampusDetailDependSchoolDetailId(Request $request)
    {
        $schoolId = $request->input('schoolId');
        $userId = Auth::id(); // Simplified user ID retrieval

        // Retrieve the campus details with a more readable query
        $assignUserCampusesDetail = DB::table('assign_user_campuses as auc')
            ->join('company_locations as sc', 'auc.company_location_id', '=', 'sc.id')
            ->where('auc.user_id', $userId)
            ->where('auc.company_id', $schoolId)
            ->select('sc.id', 'sc.name')
            ->get();


        // Build the options string using array_map for cleaner code
        $options = $assignUserCampusesDetail->map(function ($campus) {
            return "<option value=\"{$campus->id}\">{$campus->name}</option>";
        })->implode('');

        Log::info($options);

        return $options;
    }

    public function makeFormAssignSchoolAndSchoolCampusSection(Request $request){
        $schoolId = $request->input('m');
        $id = $request->input('id');
        $accType = $request->input('accType');
        ?>
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <input type="hidden" name="assign_school_and_school_campus_section_array[]" id="assign_school_and_school_campus_section_array" value="<?php echo $id?>" />
                <label class="sf-label">Select School</label>
                <select id="school_detail_<?php echo $id?>" class="multiselect-ui form-control" name="company_id_detail_<?php echo $id?>" onchange="loadSchoolCampusDetailDependSchoolDetailId('<?php echo $id?>')" required="required">
                    <option value="">Select School</option>
                    <?php
                        if($accType == 'client'){
                            $companiesList = DB::Connection('mysql')->table('companies')->select(['name','id','dbName'])->where('status','=','1')->get();
                        }else if($accType == 'superadmin'){
                            $checkCompanyId = Auth::user()->company_id;
                            $a = explode("<*>",$checkCompanyId);
                            $companiesList = DB::Connection('mysql')->table('companies')->select(['name','id','dbName'])->where('status','=','1')->whereIn('id', $a)->get();
                        }else if($accType == 'superuser'){
                            $checkCompanyId = Auth::user()->company_id;
                            $a = explode("<*>",$checkCompanyId);
                            $companiesList = DB::Connection('mysql')->table('companies')->select(['name','id','dbName'])->where('status','=','1')->whereIn('id', $a)->get();
                        }
                        foreach($companiesList as $cRow1){
                    ?>
                            <option value="<?php echo $cRow1->id;?>" class="testing" ><?php echo $cRow1->name;?></option>
                    <?php
                        }
                    ?>
                </select>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <label class="sf-label">Select School Campus</label>
                <select id="company_location_id_detail_<?php echo $id?>" class="multiselect-ui form-control" name="company_location_id_detail_<?php echo $id?>[]" multiple="multiple" required="required">
                </select>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <input type="button" class="btn btn-xs btn-danger" onclick="removeAssignSchoolAndSchoolCampusSectionRow('<?php echo $id?>')" value="Remove" />
            </div>
        <?php
    }
}
