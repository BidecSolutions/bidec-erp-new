<?php
    namespace App\Helpers;

use App\Models\Location;
use DB;
    use Config;
    use Auth;
    use Session;
    use Cache;
    class SelectListHelper{
        public function __construct()
        {
            //$this->middleware('MultiDB');
        }

        public static function getChildChartOfAccountList($m,$status,$firstValue,$id){
            $childChartOfAccountList = Cache::rememberForever('cacheChartOfAccountChildAccount_'.$m.'',function() use ($m){
                return DB::table("accounts")->select('*')
                    ->whereNotIn('code',function($query){
                        $query->select('parent_code')->from('accounts')->where('status','=','1');
                    })
                    ->where('status','=','1')->orderBy('level1', 'ASC')
                    ->orderBy('level2', 'ASC')
                    ->orderBy('level3', 'ASC')
                    ->orderBy('level4', 'ASC')
                    ->orderBy('level5', 'ASC')
                    ->orderBy('level6', 'ASC')
                    ->orderBy('level7', 'ASC')
                    ->get();
            });
            $data = '<option value="'.$firstValue.'">Select Child Chart Of Account</option>';
            //$data = '';
            foreach($childChartOfAccountList as $ccoalRow){
                $disabled = '';
                
                if($status != 0){
                    if($ccoalRow->status != 1){
                        $disabled = 'disabled';
                    } 
                }
                $selected = '';
                if($ccoalRow->id == $id){
                    $selected = 'selected';
                }
                $data .= '<option value="'.$ccoalRow->id.'"'.$disabled.''.$selected.'>'.$ccoalRow->code.' --- '.$ccoalRow->name.'</option>';
            }
            return $data;
        }

        public static function getChartOfAccountList($m,$status,$firstValue,$id){
            $chartOfAccountList = Cache::rememberForever('cacheChartOfAccount_'.$m.'',function() use ($m){
                return DB::table('accounts')
                ->where('company_id','=',$m)
                ->orderBy('level1', 'ASC')
		        ->orderBy('level2', 'ASC')
		        ->orderBy('level3', 'ASC')
		        ->orderBy('level4', 'ASC')
		        ->orderBy('level5', 'ASC')
		        ->orderBy('level6', 'ASC')
		        ->orderBy('level7', 'ASC')
                ->get();
            });
            

            $data = '<option value="'.$firstValue.'">Select Chart Of Account</option>';
            foreach($chartOfAccountList as $coalRow){
                $disabled = '';
                
                if($status != 0){
                    if($coalRow->status != 1){
                        $disabled = 'disabled';
                    } 
                }
                $selected = '';
                if($coalRow->id == $id){
                    $selected = 'selected';
                }
                $data .= '<option value="'.$coalRow->id.'"'.$disabled.''.$selected.'>'.$coalRow->code.' --- '.$coalRow->name.'</option>';
            }
            return $data;
        }

        public static function getCustomerList($m,$status,$id){
            $supplierList = DB::connection('tenant')->select("select * from customers");
            

            $data = '<option value="">Select Customers</option>';
            foreach($supplierList as $slRow){
                $disabled = '';
                if($status != 0){
                    if($slRow->status != 1){
                        $disabled = 'disabled';
                    } 
                }
                $selected = '';
                if($slRow->id == $id){
                    $selected = 'selected';
                }
                $data .= '<option value="'.$slRow->id.'"'.$disabled.''.$selected.'>'.str_replace("'", "", $slRow->buyer_name).'</option>';
            }
            return $data;
        }

        public static function getSupplierList($m,$status,$id){
            $supplierList = Cache::rememberForever('cacheSupplier_'.$m.'',function() use ($m){
                return DB::connection('tenant')->select("select * from supplier where company_id = ".$m."");
            });
            

            $data = '<option value="">Select Supplier</option>';
            foreach($supplierList as $slRow){
                $disabled = '';
                if($status != 0){
                    if($slRow->status != 1){
                        $disabled = 'disabled';
                    } 
                }
                $selected = '';
                if($slRow->id == $id){
                    $selected = 'selected';
                }
                $data .= '<option value="'.$slRow->id.'"'.$disabled.''.$selected.'>'.str_replace("'", "", $slRow->name).'</option>';
            }
            return $data;
        }

        public static function getSubDepartmentList($m,$status,$id){
            if($status == 0){
                $statusCondition = '';
            }else {
                $statusCondition = ' and status = '.$status.'';
            }
            $departmentAndSubDepartmentList = DB::connection('tenant')->select("select 
                sub_department.id,
                sub_department.sub_department_name,
                sub_department.department_id,
                department.department_name
                from sub_department INNER JOIN department ON sub_department.department_id = department.id where sub_department.company_id = ".$m."".$statusCondition."");
            $data = '<option value="">Select Department / Sub Department</option>';
            foreach($departmentAndSubDepartmentList as $dasdlRow){
                $selected = '';
                if($dasdlRow->id == $id){
                    $selected = 'selected';
                }
                $data .= '<option value="'.$dasdlRow->id.'"'.$selected.'>'.$dasdlRow->department_name.' - '.$dasdlRow->sub_department_name.'</option>';
            }
            return $data;
        }

        public static function getDepartmentList($m,$status,$id){
            $departmentList = Cache::rememberForever('cacheDepartment_'.$m.'',function() use ($m){
                return DB::select("select * from department where company_id = ".$m."");
            });
            $data = '<option value="">Select Department</option>';
            foreach($departmentList as $dlRow){
                $disabled = '';
                if($status != 0){
                    if($dlRow->status != 1){
                        $disabled = 'disabled';
                    } 
                }
                $selected = '';
                if($dlRow->id == $id){
                    $selected = 'selected';
                }
                $data .= '<option value="'.$dlRow->id.'"'.$disabled.''.$selected.'>'.$dlRow->department_name.'</option>';
            }
            return $data;
        }

        public static function getAllLocation($m){
            $locationList = Cache::rememberForever('cacheLocation_'.$m.'',function() use ($m){
                return DB::connection('tenant')->select("select * from location where company_id = ".$m." and status = 1");
            });            
            $data = '<option value="">Select Location</option>';
            foreach($locationList as $llRow){
                    $data .= '<option value="'.$llRow->id.'"'.'>'.$llRow->location_name.'</option>';
            }
            return $data;
        }
        public static function getLocationList($m,$status,$id){
            $m = getSessionCompanyId();
            $locationList = Location::where('company_id', $m)->get();
            $allowed_warehouses = CommonFacades::user_allowed_warehouses();
            $accType = Auth::user()->acc_type;
            $allowed_usertype = ['user', 'superuser', 'superadmin'];
            $data = '<option value="">Select Location</option>';
            foreach($locationList as $llRow){
                if (in_array($llRow->id, $allowed_warehouses) && in_array(auth()->user()->acc_type,$allowed_usertype)) {
                    $disabled = '';
                    if($status != 0){
                        if($llRow->status != 1){
                            $disabled = 'disabled';
                        } 
                    }
                    $selected = '';
                    if($llRow->id == $id){
                        $selected = 'selected';
                    }
                    $data .= '<option value="'.$llRow->id.'"'.$disabled.''.$selected.'>'.$llRow->location_name.'</option>';
                }elseif(auth()->user()->acc_type == 'client'){
                    $disabled = '';
                    if($status != 0){
                        if($llRow->status != 1){
                            $disabled = 'disabled';
                        } 
                    }
                    $selected = '';
                    if($llRow->id == $id){
                        $selected = 'selected';
                    }
                    $data .= '<option value="'.$llRow->id.'"'.$disabled.''.$selected.'>'.$llRow->location_name.'</option>';
                }
            }
            return $data;
        }
        public static function getLocationOriginList($m,$status,$id){
            if($id == 0){
                $locationList = DB::connection('tenant')->select("select * from location_origins");
            }else{
                $locationList = DB::connection('tenant')->select("select * from location_origins where id = ".$id."");
            }
                        
            
            $data = '<option value="">Select Location</option>';
            foreach($locationList as $llRow){
                $disabled = '';
                // if($status != 0){
                //     if($llRow->status != 1){
                //         $disabled = 'disabled';
                //     } 
                // }
                $selected = '';
                if($llRow->id == $id){
                    $selected = 'selected';
                }
                $data .= '<option value="'.$llRow->id.'"'.$disabled.''.$selected.'>'.$llRow->name.'</option>';
            }
            return $data;
        }

        public static function getProjectList($m,$status,$id){
            $projectList = Cache::rememberForever('cacheProject_'.$m.'',function() use ($m){
                return DB::connection('tenant')->select("select * from project where company_id = ".$m."");
            });
            $data = '<option value="">Select Project</option>';
            foreach($projectList as $plRow){
                $disabled = '';
                if($status != 0){
                    if($plRow->status != 1){
                        $disabled = 'disabled';
                    } 
                }
                $selected = '';
                if($plRow->id == $id){
                    $selected = 'selected';
                }
                $data .= '<option value="'.$plRow->id.'"'.$disabled.''.$selected.'>'.$plRow->project_name.'</option>';
            }
            return $data;
        }

        public static function getAccountYearList($m,$status,$id){
            $accountYearList = Cache::rememberForever('cacheAccountYear_'.$m.'',function() use ($m){
                return DB::Connection('mysql')->select("select * from accountyear where company_id = ".$m."");
            });
            $data = '<option value="">Select Account Year</option>';
            foreach($accountYearList as $aylRow){
                $disabled = '';
                if($status != 0){
                    if($aylRow->status != 1){
                        $disabled = 'disabled';
                    } 
                }
                $selected = '';
                if($aylRow->AccountYearId == $id){
                    $selected = 'selected';
                }
                $data .= '<option value="'.$aylRow->AccountYearId.'"'.$disabled.''.$selected.'>'.$aylRow->AccountYearName.'</option>';
            }
            return $data;
        }

        public static function getUserDetailList($m,$status,$id){
            $userDetailList = Cache::rememberForever('cacheUserDetail_'.$m.'',function() use ($m){
                return DB::Connection('mysql')->select("select * from users where company_id = ".$m."");
            });
            $data = '<option value="">Select User</option>';
            foreach($userDetailList as $udlRow){
                $disabled = '';
                if($status != 0){
                    if($udlRow->status != 1){
                        $disabled = 'disabled';
                    } 
                }
                $selected = '';
                if($udlRow->id == $id){
                    $selected = 'selected';
                }
                $data .= '<option value="'.$udlRow->id.'"'.$disabled.''.$selected.'>'.$udlRow->name.'</option>';
            }
            return $data;
        }

        public static function getAccountYearListTwo(){
            $values = Cache::rememberForever('cacheAccountYearTwo', function () {
                return DB::Connection('mysql')->table('accountyear')->orderBy('AccountYearId')->get();
            });
            $data_array = [];
            foreach($values as $key => $val):
                $data_array[$val->AccountYearId] = $val;
            endforeach;
            return $data_array;
        }
    }
?>