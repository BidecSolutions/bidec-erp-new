<?php
    namespace App\Http\Controllers\Users;
    use App\Http\Controllers\Controller;

    use Request;
    use Input;
    use DB;
	use Session;
	use Hash;
	use Redirect;
	use App\Models\Menu;
	use App\Models\CustomPermission;
	use App\Models\SubMenu;
    class UsersAddDetailController extends Controller
	{
		/**
		 * Create a new controller instance.
		 *
		 * @return void
		 */

         public function addMainMenuTitleDetail(Request $request){

			$menu_type = $request::get('menu_type');
			$menu_icon = $request::get('menu_icon');
			$title = $request::get('title_name');
			$title_id = preg_replace('/\s+/', '', $title);

			$data1['menu_type'] =	$menu_type;
			$data1['menu_icon'] = $menu_icon;
			$data1['menu_name'] = $title;

			Menu::create($data1);
			//Artisan::call('cache:clear');
		}

        public function addSubMenuDetail(Request $request){

			$menu_id = $request::get('menu_id');
			$sub_menu_icon = $request::get('sub_menu_icon');
			$sub_menu_name = $request::get('sub_menu_name');
			$url = $request::get('url');
			$sub_menu_type = $request::get('sub_menu_type');


			$data1['menu_id'] =	$menu_id;
			$data1['sub_menu_icon'] = $sub_menu_icon;
			$data1['sub_menu_name'] = $sub_menu_name;
			$data1['url']     		  = $url;
			$data1['sub_menu_type'] = $sub_menu_type;

			$subMenu = SubMenu::create($data1);

			$data['group_id'] = $menu_id;
            $data['sub_menu_id'] = $subMenu->id;
            $data['name'] = $url;
            $data['guard_name'] = 'web';
            CustomPermission::create($data);

			//Cache::forget('masterSubMenus_'.$mainNavigationName.'');
			//Cache::forget('SubQuerySubMenus_'.$mainNavigationName.'');

		}

		function addUsersLoginTimePeriodAndPermissionDetail(Request $request){
			$m = Session::get('company_id');
			$company_id_detail = $request::input('company_id_detail');
			$companyId = implode("<*>",$company_id_detail);
			$emp_email = $request::input('username');
			$employee_password = $request::input('password');
			$emp_name = $request::input('name');
			$account_type = $request::input('account_type');
			$company_id_detail = $request::input('company_id_detail');
			$mobile_no = $request::input('mobile_no');
			$cnic_no = $request::input('cnic_no');

			$dataCredentials['name'] = $emp_name;
			$dataCredentials['username'] = $emp_email;
			$dataCredentials['mobile_no'] = $mobile_no;
			$dataCredentials['cnic_no'] = $cnic_no;
			$dataCredentials['email'] = $emp_email;
			$dataCredentials['password'] = Hash::make($employee_password);
			$dataCredentials['acc_type'] = $account_type;
			$dataCredentials['sgpe'] = $emp_name.'<*>'.$employee_password.'<*>'.$emp_email;
			$dataCredentials['company_id'] = $companyId;
			$dataCredentials['updated_at'] = date("Y-m-d");
			$dataCredentials['created_at'] = date("Y-m-d");
			$dataCredentials['status'] = 1;

			$userId = DB::table('users')->insertGetId($dataCredentials);

			return Redirect::to('users/viewUsersLoginTimePeriodList');
		}
    }
?>
