<?php

namespace App\Helpers;

use App\Models\Section;
use App\Models\Student;
use App\Models\Department;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Classes;
use App\Models\Country;
use App\Models\States;
use App\Models\City;
use App\Models\Paras;
use App\Models\Employee;
use App\Models\ChartOfAccount;
use App\Models\ParaOtherDetail;
use App\Models\LevelOfPerformance;
use App\Models\StudentDayWisePerformance;
use App\Models\ManzilPerformanceData;
use App\Models\Head;
use GoogleTranslate;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CommonHelper
{
    public function __construct()
    {
        //$this->middleware('MultiDB');
    }

    public static function showFinanceVoucherStatus($status, $voucherStatus)
    {
        $data = '<button class="btn btn-primary btn-sm">Pending</button>';
        if ($voucherStatus == 1) {
            if ($status == 2) {
                $data = '<button class="btn btn-danger btn-sm">Inactive</button>';
            }
        } else if ($voucherStatus == 2) {
            $data = '<button class="btn btn-success btn-sm">Approved</button>';
        } else if ($voucherStatus == 3) {
            $data = '<button class="btn btn-warning btn-sm">Reject</button>';
        }
        echo $data;
    }

    public static function getButtonsforDirectSaleInvoiceVouchers($data)
    {
        $type = $data['type'];
        $id = $data['id'];
        $status = $data['status'];
        $voucherTypeStatus = $data['voucher_type_status'];
        $data = '';
        if ($status == 1 && $voucherTypeStatus == 1) {
            $data .= '<button class="btn btn-xs btn-warning" onclick="inactiveDirectSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">In-Active</button>&nbsp;';
        } else if ($voucherTypeStatus == 1) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="activeDirectSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Active</button>&nbsp;';
        }
        if ($voucherTypeStatus == 1 && $status == 1) {
            $data .= '<button class="btn btn-xs btn-success" onclick="approveDirectSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Approve</button>&nbsp;';
            $data .= '<button class="btn btn-xs btn-danger" onclick="rejectDirectSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Reject</button>&nbsp;';
        } else if ($voucherTypeStatus == 3) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="repostDirectSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Repost</button>&nbsp;';
        }
        echo $data;
    }

    public static function getButtonsforPurchaseOrdersAndGoodReceiptNoteVouchers($data)
    {
        $type = $data['type'];
        $id = $data['id'];
        $status = $data['status'];
        $voucherTypeStatus = $data['voucher_type_status'];
        $data = '';
        if ($status == 1 && $voucherTypeStatus == 1) {
            $data .= '<button class="btn btn-xs btn-warning" onclick="inactivePurchaseVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">In-Active</button>&nbsp;';
        } else if ($voucherTypeStatus == 1) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="activePurchaseVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Active</button>&nbsp;';
        }
        if ($voucherTypeStatus == 1 && $status == 1) {
            $data .= '<button class="btn btn-xs btn-success" onclick="approvePurchaseVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Approve</button>&nbsp;';
            $data .= '<button class="btn btn-xs btn-danger" onclick="rejectPurchaseVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Reject</button>&nbsp;';
        } else if ($voucherTypeStatus == 3) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="repostPurchaseVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Repost</button>&nbsp;';
        }
        echo $data;
    }
    //material request and store challan
    public static function getButtonsforMaterialRequestAndStoreChallanVouchers($data)
    {
        $type = $data['type'];
        $id = $data['id'];
        $status = $data['status'];
        $voucherTypeStatus = $data['voucher_type_status'];
        $data = '';
        if ($status == 1 && $voucherTypeStatus == 1) {
            $data .= '<button class="btn btn-xs btn-warning" onclick="inactiveMaterialVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">In-Active</button>&nbsp;';
        } else if ($voucherTypeStatus == 1) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="activeMaterialVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Active</button>&nbsp;';
        }
        if ($voucherTypeStatus == 1 && $status == 1) {
            $data .= '<button class="btn btn-xs btn-success" onclick="approveMaterialVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Approve</button>&nbsp;';
            $data .= '<button class="btn btn-xs btn-danger" onclick="rejectMaterialVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Reject</button>&nbsp;';
        } else if ($voucherTypeStatus == 3) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="repostMaterialVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Repost</button>&nbsp;';
        }
        echo $data;
    }
    public static function getButtonsforReturnGoodReceiptNoteVouchers($data)
    {
        $type = $data['type'];
        $id = $data['id'];
        $status = $data['status'];
        $voucherTypeStatus = $data['voucher_type_status'];
        $output = '';

        // When status is Active and voucher is pending, show the In-Active button
        if ($status == 1 && $voucherTypeStatus == 1) {
            $output .= '<button class="btn btn-xs btn-warning" onclick="inactiveReturnVoucher(' . $id . ')">In-Active</button>&nbsp;';
        } else if ($voucherTypeStatus == 1) {
            // If voucher is pending but status is not active, show the Active button
            $output .= '<button class="btn btn-xs btn-danger" onclick="activeReturnVoucher(' . $id . ')">Active</button>&nbsp;';
        }

        // When voucher is pending and status is active, allow Approve/Reject
        if ($voucherTypeStatus == 1 && $status == 1) {
            $output .= '<button class="btn btn-xs btn-success" onclick="approveReturnVoucher(' . $id . ')">Approve</button>&nbsp;';
            $output .= '<button class="btn btn-xs btn-danger" onclick="rejectReturnVoucher(' . $id . ')">Reject</button>&nbsp;';
        } else if ($voucherTypeStatus == 3) {
            // When voucher is rejected, provide an option to repost
            $output .= '<button class="btn btn-xs btn-danger" onclick="repostReturnVoucher(' . $id . ')">Repost</button>&nbsp;';
        }

        echo $output;
    }
    public static function getButtonsforReturnSale($data)
    {
        $type = $data['type'];
        $id = $data['id'];
        $status = $data['status'];
        $voucherTypeStatus = $data['voucher_type_status'];
        $output = '';

        // When status is Active and voucher is pending, show the In-Active button
        if ($status == 1 && $voucherTypeStatus == 1) {
            $output .= '<button class="btn btn-xs btn-warning" onclick="inactiveReturnSale(' . $id . ')">In-Active</button>&nbsp;';
        } else if ($voucherTypeStatus == 1) {
            // If voucher is pending but status is not active, show the Active button
            $output .= '<button class="btn btn-xs btn-danger" onclick="activeReturnSale(' . $id . ')">Active</button>&nbsp;';
        }

        // When voucher is pending and status is active, allow Approve/Reject
        if ($voucherTypeStatus == 1 && $status == 1) {
            $output .= '<button class="btn btn-xs btn-success" onclick="approveReturnSale(' . $id . ')">Approve</button>&nbsp;';
            $output .= '<button class="btn btn-xs btn-danger" onclick="rejectReturnSale(' . $id . ')">Reject</button>&nbsp;';
        } else if ($voucherTypeStatus == 3) {
            // When voucher is rejected, provide an option to repost
            $output .= '<button class="btn btn-xs btn-danger" onclick="repostReturnSale(' . $id . ')">Repost</button>&nbsp;';
        }

        echo $output;
    }


    public static function getButtonsforStoreVouchers($data)
    {
        $type = $data['type'];
        $id = $data['id'];
        $status = $data['status'];
        $voucherTypeStatus = $data['voucher_type_status'];
        $data = '';
        if ($status == 1 && $voucherTypeStatus == 1) {
            $data .= '<button class="btn btn-xs btn-warning" onclick="inactiveStoreVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">In-Active</button>&nbsp;';
        } else if ($voucherTypeStatus == 1) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="activeStoreVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Active</button>&nbsp;';
        }
        if ($voucherTypeStatus == 1 && $status == 1) {
            $data .= '<button class="btn btn-xs btn-success" onclick="approveStoreVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Approve</button>&nbsp;';
            $data .= '<button class="btn btn-xs btn-danger" onclick="rejectStoreVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Reject</button>&nbsp;';
        } else if ($voucherTypeStatus == 3) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="repostStoreVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Repost</button>&nbsp;';
        }
        echo $data;
    }

    public static function getButtonsforPurchaseAndSaleInvoiceVouchers($data)
    {
        $type = $data['type'];
        $id = $data['id'];
        $status = $data['status'];
        $voucherTypeStatus = $data['voucher_status'];
        $data = '';
        // if ($status == 1 && $voucherTypeStatus == 1) {
        //     $data .= '<button class="btn btn-xs btn-warning" onclick="inactivePurchaseSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">In-Active</button>&nbsp;';
        // } else if ($voucherTypeStatus == 1) {
        //     $data .= '<button class="btn btn-xs btn-danger" onclick="activePurchaseSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Active</button>&nbsp;';
        // }
        if ($voucherTypeStatus == 1 && $status == 1) {
            $data .= '<button class="btn btn-xs btn-success" onclick="approvePurchaseSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Approve</button>&nbsp;';
            //$data .= '<button class="btn btn-xs btn-danger" onclick="rejectPurchaseSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Reject</button>&nbsp;';
        } else if($voucherTypeStatus == 2 && $status == 1){
            $data .= '<button class="btn btn-xs btn-danger" onclick="reversePurchaseSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Reverse</button>&nbsp;';      
        } else if ($voucherTypeStatus == 3) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="repostPurchaseSaleInvoiceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Repost</button>&nbsp;';
        }
        echo $data;
    }

    public static function getButtonsforPaymentAndReceiptAndJournalVouchers($data)
    {
        $type = $data['type'];
        $id = $data['id'];
        $status = $data['status'];
        $voucherTypeStatus = $data['voucher_type_status'];
        $data = '';
        if ($status == 1 && $voucherTypeStatus == 1) {
            $data .= '<button class="btn btn-xs btn-warning" onclick="inactiveFinanceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">In-Active</button>&nbsp;';
        } else if ($voucherTypeStatus == 1) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="activeFinanceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Active</button>&nbsp;';
        }
        if ($voucherTypeStatus == 1 && $status == 1) {
            $data .= '<button class="btn btn-xs btn-success" onclick="approveFinanceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Approve</button>&nbsp;';
            $data .= '<button class="btn btn-xs btn-danger" onclick="rejectFinanceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Reject</button>&nbsp;';
        } else if ($voucherTypeStatus == 3) {
            $data .= '<button class="btn btn-xs btn-danger" onclick="repostFinanceVoucher(' . $type . ', ' . $id . ', ' . $status . ', ' . $voucherTypeStatus . ')">Repost</button>&nbsp;';
        }
        echo $data;
    }

    public static function settingDetail()
    {
        $companyId = Session::get('company_id');
        $companyLocationId = Session::get('company_location_id');
        $settingDetail = DB::table('settings')->where('company_id', $schoolId)->where('company_location_id', $companyLocationId)->where('status', 1)->first();
        return $settingDetail;
    }

    public static function getMonthTotalFeePayment($schoolCampusId, $schoolId, $monthYear)
    {
        $explodeMonthYear = explode('-', $monthYear);
        $year = $explodeMonthYear[0];
        $month = $explodeMonthYear[1];

        return $getDetail = DB::table('generate_fee_voucher_datas as gfvd')
            ->join('generate_fee_vouchers as gfv', 'gfvd.generate_fee_voucher_id', '=', 'gfv.id')
            ->whereMonth('gfv.month_year', $month)
            ->whereYear('gfv.month_year', $year)
            ->where('gfv.company_id', $schoolId)
            ->where('gfv.company_location_id', $schoolCampusId)
            ->sum('gfvd.amount');
    }

    public static function getMonthTotalFeeReceipt($schoolCampusId, $schoolId, $monthYear)
    {
        $explodeMonthYear = explode('-', $monthYear);
        $year = $explodeMonthYear[0];
        $month = $explodeMonthYear[1];
        return $getDetail = DB::table('fees')
            ->whereMonth('month_year', $month)
            ->whereYear('month_year', $year)
            ->where('company_id', $schoolId)
            ->where('company_location_id', $schoolCampusId)
            ->sum('amount');
    }

    public static function getNoOfStudents($schoolCampusId, $schoolId)
    {
        return $getNoOfStudents = DB::table('students')
            ->where('company_id', $schoolId)
            ->where('company_location_id', $schoolCampusId)
            ->count();
    }

    public static function getMonthSalaryExpense($schoolCampusId, $schoolId, $monthYear)
    {
        return $getDetail = DB::table('employee_payroll_detail as epd')
            ->join('employee_payroll_data_detail as epdd', 'epd.id', '=', 'epdd.epd_id')
            ->where('epd.company_id', $schoolId)
            ->where('epd.company_location_id', $schoolCampusId)
            ->where('epd.month_year', $monthYear)
            ->sum('epdd.net_salary');
    }

    public static function getEmployeePayrollDetail($empId, $eadId)
    {
        $normalAllowance = DB::table('allowance_type as at')
            ->leftJoin('employee_allowance_detail as ead', function ($join) use ($empId) {
                $join->on('at.id', '=', 'ead.allowance_id')
                    ->where('ead.employee_id', $empId)
                    ->where('ead.status', 1);
            })
            ->select('ead.*', 'at.id as naId') // Adjust if you need specific columns
            ->where('at.type', 1)
            ->get();
        $additionalAllowance = DB::table('allowance_type as at')
            ->leftJoin('employee_allowance_detail as ead', function ($join) use ($empId) {
                $join->on('at.id', '=', 'ead.allowance_id')
                    ->where('ead.employee_id', $empId)
                    ->where('ead.status', 1);
            })
            ->select('ead.*', 'at.id as aaId') // Adjust if you need specific columns
            ->where('at.type', 2)
            ->get();
        $data = '';
        $totalAllowance = 0;
        $totalAdditionalAllowance = 0;
        foreach ($normalAllowance as $naRow) {
            $totalAllowance += $naRow->amount;
            $data .= '<td class="text-center" style="width:200px !important;"><input type="hidden" name="emp_normal_allowance_' . $empId . '[]" id="emp_normal_allowance_' . $empId . '" value="' . $naRow->naId . '" />
                        <input type="number" onchange="calculateAmounts(1,\'normalAllowanceClass\',\'' . $empId . '\')" name="normal_allowance_' . $empId . '_' . $naRow->naId . '" id="normal_allowance_' . $naRow->naId . '" value="' . $naRow->amount . '" class="normalAllowanceClass_{{$elRow->id}}" /></td>';
        }
        $data .= '<td class="text-center" style="width:200px !important;"><input type="number" readonly name="emp_total_allowance_' . $empId . '" id="emp_total_allowance_' . $empId . '" value="' . $totalAllowance . '" /></td>';
        foreach ($additionalAllowance as $aaRow) {
            $totalAdditionalAllowance += $aaRow->amount;
            $data .= '<td class="text-center" style="width:200px !important;"><input type="hidden" name="emp_additional_allowance_' . $empId . '[]" id="emp_additional_allowance_' . $empId . '" value="' . $aaRow->aaId . '" />
                        <input type="number" onchange="calculateAmounts(2,\'additionalAllowanceClass\',\'' . $empId . '\')" name="additional_allowance_' . $empId . '_' . $aaRow->aaId . '" id="additional_allowance_' . $aaRow->aaId . '" value="' . $aaRow->amount . '" class="additionalAllowanceClass_{{$elRow->id}}" /></td>';
        }
        $data .= '<td class="text-center" style="width:200px !important;"><input type="number" readonly name="emp_total_additional_allowance_' . $empId . '" id="emp_total_additional_allowance_' . $empId . '" value="' . $totalAdditionalAllowance . '"/></td>';
        $data .= '<td class="text-center" style="width:200px !important;"><input type="number" readonly name="emp_gross_salary_' . $empId . '" id="emp_gross_salary_' . $empId . '" value="' . $totalAdditionalAllowance + $totalAllowance . '" /></td>';
        echo $data;
    }

    public static function calculateAge($date)
    {
        // Calculate the age based on the date of birth
        $today = new DateTime(); // Today's date
        $diff = $today->diff(new DateTime($date)); // Difference between dates

        // Extract the age from the difference object
        return $age = $diff->y; // y gives the number of full years
    }

    public static function displayPDFTableHeader($colspan, $title)
    {
        $schoolDetail = DB::table('companies')->where('id', Session::get('company_id'))->first();
        if (empty($schoolDetail)) {
            $imageLogo = '<img src="' . url('assets/img/no_image.png') . '" alt="Lights" style="width:200px; height: 150px">';
        } else {
            if (file_exists($schoolDetail->school_logo)) {
                $imageLogo = '<img src="' . url($schoolDetail->school_logo) . '" alt="Lights" style="width:200px; height: 150px">';
            } else {
                $imageLogo = '<img src="' . url('assets/img/no_image.png') . '" alt="Lights" style="width:200px; height: 150px">';
            }
        }
        $data = '<tr class="pdfClass hidden hideExportTwo"><th colspan="' . $colspan . '"><br /><br /><br /><div class="row"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6"><div class="mh m1-h "><h5 class="pageHeadingTitle">' . $title . '</h5></div></div><div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">' . $imageLogo . '</div></div></th></tr>';
        echo $data;
    }
    public static function getCompanyLocations($schoolId)
    {
        return DB::table('company_locations')
            ->where('company_id', $schoolId) // Fetch locations for the given school ID
            ->pluck('name', 'id') // Fetch only name and id
            ->toArray(); // Convert to an array
    }
    public static function displaySchoolLogo()
    {
        $schoolDetail = DB::table('companies')->where('id', Session::get('company_id'))->first();
        if (empty($schoolDetail)) {
            $imageLogo = url('assets/img/no_image.png');
        } else {
            if (file_exists($schoolDetail->school_logo)) {
                $imageLogo = url($schoolDetail->school_logo);
            } else {
                $imageLogo = url('assets/img/no_image.png');
            }
        }
        echo $imageLogo;
    }

    public static function studentWeeklyPerformanceDetail($data)
    {
        $studentId = $data['student_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        return $performances = StudentDayWisePerformance::select('paras.id', 'paras.para_name')
            ->join('paras', 'student_day_wise_performances.para_id', '=', 'paras.id')
            ->whereBetween('performance_date', [$start_date, $end_date])
            ->where('student_id', $studentId)
            ->groupBy(['paras.id', 'paras.para_name'])
            ->selectRaw('SUM(student_day_wise_performances.no_of_lines) as completedLines')
            ->get();
    }

    public static function getWeeksInMonth($year, $month)
    {
        $weeks = array();

        // Get the first day of the month
        $firstDayOfMonth = new \DateTime("$year-$month-01");

        // Get the number of days in the month
        $daysInMonth = $firstDayOfMonth->format('t');

        // Loop through each day of the month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            // Create a DateTime object for the current day
            $currentDay = new \DateTime("$year-$month-$day");

            // Get the week number of the current day
            $weekNum = $currentDay->format('W');

            // Add the current day to the corresponding week
            $weeks[$weekNum][] = $currentDay->format('Y-m-d');
        }

        // Get the start and end dates of each week
        $weekDates = array();
        foreach ($weeks as $weekNum => $days) {
            $startDate = min($days);
            $endDate = max($days);
            $weekDates[$weekNum] = array(
                'start_date' => $startDate,
                'end_date' => $endDate
            );
        }

        return $weekDates;
    }

    public static function changeDateformat($date)
    {
        $date = date('d-m-Y', strtotime($date));
        return $date;
    }

    public static function calculateTotalHours($clock_in, $clock_out)
    {
        if ($clock_in == '00:00' || $clock_out == '00:00') {
            return '-';
        } else {
            // Convert time strings to Unix timestamps
            $startTimeStamp = strtotime($clock_in);
            $endTimeStamp = strtotime($clock_out);

            // Calculate the difference in seconds
            $difference = $endTimeStamp - $startTimeStamp;

            // Convert the difference to hours and minutes
            $totalHours = floor($difference / 3600); // 3600 seconds in an hour
            $totalMinutes = floor(($difference % 3600) / 60);

            return $totalHours . ':' . $totalMinutes;
        }
    }
    public static function checkPermissionInnerOptions($routeUrl)
    {
        return $routeUrl;
        $userCanPermission = Auth::user()->can($routeUrl);
        $result = Auth::user()->email == 'ushahfaisalranta@gmail.com'
            ? $routeUrl : ($userCanPermission ? $routeUrl : null);
        return $result;
    }
    public static function displayViewPageTitle($paramOne)
    {
?>
        <div class=" mh m1-h ">
            <h5><?php echo $paramOne; ?></h5>
        </div>
    <?php
    }

    public static function get_all_student_wise_remaining_paras($studentId)
    {

        return $results = DB::table('para_other_details')
            ->join('paras', 'para_other_details.para_id', '=', 'paras.id')
            ->select('paras.id', 'paras.para_name')
            ->where('para_other_details.company_id', Session::get('company_id'))
            ->where('para_other_details.company_location_id', Session::get('company_location_id'))
            ->whereNotIn('paras.id', function ($query) use ($studentId) {
                $query->select('para_id')
                    ->from('student_current_paras')
                    ->where('student_id', $studentId);
            })
            ->get();
    }

    public static function get_all_chart_of_account($status = '')
    {
        $companyId = Session::get('company_id');

        return DB::select('CALL GetAllChartOfAccounts(?, ?)', [$status, $companyId]);
    }

    // public static function get_all_chart_of_account($status = '')
    // {
    //     return ChartOfAccount::status($status)
    //         ->select('id', 'code', 'name')
    //         ->where('company_id', Session::get('company_id'))
    //         ->orderBy('level1', 'ASC')
    //         ->orderBy('level2', 'ASC')
    //         ->orderBy('level3', 'ASC')
    //         ->orderBy('level4', 'ASC')
    //         ->orderBy('level5', 'ASC')
    //         ->orderBy('level6', 'ASC')
    //         ->orderBy('level7', 'ASC')
    //         ->get();
    // }
    public static function getManzilPerformanceData($id)
    {
        return DB::table('manzil_performance_datas as mpd')
            ->join('paras as p', 'mpd.para_id', '=', 'p.id')
            ->join('level_of_performances as lop', 'mpd.level_of_performance_id', '=', 'lop.id')
            ->select('mpd.*', 'p.*', 'lop.*')
            ->where('mpd.manzil_performance_id', $id)
            ->get();
    }

    public static function getAdditionalActivityData($id)
    {
        return DB::table('additional_activity_datas as aad')
            ->join('heads as h', 'aad.head_id', '=', 'h.id')
            ->join('level_of_performances as lop', 'aad.level_of_performance_id', '=', 'lop.id')
            ->select('aad.*', 'h.*', 'lop.*')
            ->where('aad.additional_activity_id', $id)
            ->get();
    }

    public static function get_all_heads($status = '')
    {
        return Head::status($status)->select('id', 'head_name')->where('company_id', Session::get('company_id'))->where('company_location_id', Session::get('company_location_id'))->get();
    }
    public static function get_all_level_of_performance($status = '')
    {
        return LevelOfPerformance::status($status)->select('id', 'performance_name')->where('company_id', Session::get('company_id'))->where('company_location_id', Session::get('company_location_id'))->get();
    }
    public static function get_all_employees($emp_type = '', $status = '')
    {
        return Employee::status($status)
            ->when($emp_type != '', function ($q) use ($emp_type) {
                return $q->where('emp_type', '=', $emp_type);
            })
            ->select('id', 'emp_name')->where('company_id', Session::get('company_id'))->where('company_location_id', Session::get('company_location_id'))->get();
    }

    public static function get_all_employees_two($emp_type = '', $status = '', $company_id = '')
    {
        return Employee::status($status)
            ->when($emp_type != '', function ($q) use ($emp_type) {
                return $q->where('emp_type', '=', $emp_type);
            })
            ->when($company_id != '', function ($q) use ($company_id) {
                return $q->where('company_id', '=', $company_id);
            })
            ->select('id', 'emp_name')->get();
    }

    public static function get_all_sections($status = '')
    {
        return Section::status($status)->with('classes')
            ->where('company_id', Session::get('company_id'))
            ->where('company_location_id', Session::get('company_location_id'))
            ->get();
    }

    public static function get_all_departments($status = '')
    {
        return Department::status($status)->select('id', 'department_name')->where('company_id', Session::get('company_id'))->where('company_location_id', Session::get('company_location_id'))->get();
    }

    public static function get_all_teachers($status = '')
    {
        return Employee::status($status)->select('id', 'emp_name')->where('company_id', Session::get('company_id'))->where('company_location_id', Session::get('company_location_id'))->get();
    }
    public static function get_all_subjects($status = '')
    {
        return Subject::status($status)->select('id', 'subject_name')->where('company_id', Session::get('company_id'))->where('company_location_id', Session::get('company_location_id'))->get();
    }

    public static function get_all_classes($status = '')
    {
        return Classes::status($status)->select('id', 'class_name')->where('company_id', Session::get('company_id'))->where('company_location_id', Session::get('company_location_id'))->get();
    }

    public static function displayPageTitle($title)
    {
    ?>
        <div class="mh m1-h ">
            <h5><?php echo $title; ?></h5>
        </div>
        <?php
    }

    public static function get_all_countries($status = '')
    {
        return Country::status($status)->select('id', 'country_name')->get();
    }

    public static function get_all_paras($status = '')
    {
        return Paras::status($status)->select('id', 'para_name')->get();
    }

    public static function get_all_students($status = '')
    {
        $loginCnic = Session::get('login_cnic');
        if (empty($loginCnic)) {
            $studentList = Student::status($status)->select('id', 'student_name', 'registration_no')->where('company_id', Session::get('company_id'))->where('company_location_id', Session::get('company_location_id'))->get();
        } else {
            $studentList = DB::table('students as s')
                ->join('student_parent_and_guardian_informations as spagi', 's.id', '=', 'spagi.student_id')
                ->select('s.id', 's.student_name', 's.registration_no', 'spagi.cnic_no')
                ->where('spagi.cnic_no', $loginCnic)
                ->get();
        }
        return $studentList;
    }

    public static function get_all_states($status = '')
    {
        return States::status($status)->select('id', 'state_name')->get();
    }

    public static function get_all_cities($status = '')
    {
        return City::status($status)->select('id', 'city_name')->get();
    }

    public static function display_document($document)
    {
        $data = '';
        if ($document == '-') {
            echo $document;
        } else {
            $ext = pathinfo($document, PATHINFO_EXTENSION);
            if ($ext == 'pdf') {
                $data = '<a target="_blank" href="' . url($document) . '">Download File</a>';
            } else {
                $data = '<img src="' . url($document) . '" alt="Lights" style="width:250px; height: 200px">';
            }
            echo $data;
        }
    }

    public static function display_document_two($document)
    {
        $data = '';
        if (empty($document)) {
            $data = '<img src="' . url('assets/img/no_image.png') . '" alt="Lights" style="width: 60px; height: 60px; border-radius: 50px;">';
        } else {
            $ext = pathinfo($document, PATHINFO_EXTENSION);
            if ($ext == 'pdf') {
                $data = '-';
            } else {
                $data = '<img src="' . url($document) . '" alt="Lights" style="width: 60px; height: 60px; border-radius: 50px;">';
            }
        }
        echo $data;
    }

    public static function getCompanyNameTwo($param1)
    {
        return $companyName = DB::selectOne('select `name` from `companies` where `id` = ' . $param1 . '')->name;
    }

    public static function displayPrintButtonInBlade($param1, $param2, $param3)
    {
        if ($param3 == 1) {
        ?>
            <button class="btn btn-sm btn-info"
                onclick="printView('<?php echo $param1 ?>','<?php echo $param2 ?>','<?php echo $param3 ?>')"
                style="<?php echo $param2; ?>">
                <span class="glyphicon glyphicon-print"></span> Print
            </button>
        <?php } else { ?>
            <button class="btn btn-sm btn-info"
                onclick="printViewTwo('<?php echo $param1 ?>','<?php echo $param2 ?>','<?php echo $param3 ?>')"
                style="<?php echo $param2; ?>">
                <span class="glyphicon glyphicon-print"></span> Print
            </button>
        <?php }
    }

    public static function checkUserPermissionForSingleOption($param1,$param2,$param3,$param4,$param5,$param6,$param7){
        if ($param7 == 'user') {
            if($param4 == 'add'){
                $columnName = 'right_add';
            }else if($param4 == 'viewlist'){
                $columnName = 'right_viewlist';
            }else if($param4 == 'edit'){
                $columnName = 'right_edit';
            }else if($param4 == 'viewsingle'){
                $columnName = 'right_viewsingle';
            }
			else if($param4 == 'delete'){
                $columnName = 'right_delete';
            }
			else if($param4 == 'repost'){
                $columnName = 'right_repost';
            }
            $menuDetail = DB::Connection('mysql')->table('role_detail')
                ->where('company_id', '=', $param1)
                ->where('user_id', '=', $param2)
                ->where('menu_id', '=', $param5)
                ->first();
            if(empty($menuDetail)){
                return $menuDetail;
            }else {
                return $menuDetail->$columnName;
            }
        }else{
            return '1';
        }
    }

    public static function displayExportButton($param1, $param2, $param3)
    {
        ?>
        <?php
        //if(singlePermission(getSessionCompanyId(), Auth::user()->id, Input::get('parentCode'), 'right_export', Auth::user()->acc_type)){
        ?>
        <button class="btn btn-sm btn-warning"
            onclick="exportView('<?php echo $param1 ?>','<?php echo $param2 ?>','<?php echo $param3 ?>')"
            style="<?php echo $param2; ?>">
            <span class="glyphicon glyphicon-print"></span> Export to CSV
        </button>
        <?php //} else{
        ?>
        <button disabled class="hide btn btn-sm btn-warning">
            <span class="glyphicon glyphicon-print"></span> Export to CSV
        </button>
        <?php //} 
        ?>

    <?php
    }

public static function displayCompanyInfo()
{
    try {
        $company = DB::table('companies as c')
            ->leftJoin('company_locations as cl', 'cl.company_id', '=', 'c.id')
            ->select(
                'c.name',
                'c.address',
                'c.contact_no',
                'c.school_logo',
                'cl.name as location'
            )
            ->where('c.status', 1)
            ->first();

        if (!$company) {
            return '<p class="text-center text-danger">No active company found.</p>';
        }

        $logoPath = $company->school_logo;
        if ($logoPath && !preg_match('/^http(s)?:\/\//', $logoPath)) {
            $logoPath = asset($logoPath);
        }

        return '
            <div class="text-center" style="margin-bottom: 30px;">
                '.($logoPath ? '
                    <div style="margin-bottom: 1px;">
                        <img src="'.$logoPath.'" alt="Company Logo"
                             style="width:100px;height:100px;object-fit:cover;
                             border-radius:50%;border:2px solid #ddd;margin:0 auto 10px;">
                    </div>' : '').'
                <h3 style="margin:0;font-weight:700;">Company Name: '.strtoupper($company->name).'</h3>
                <p style="margin:0;">Address: '.($company->address ?? '-').'</p>
                <p style="margin:0;">Location: '.($company->location ?? '-').'</p>
                <p style="margin:0;">Contact: '.($company->contact_no ?? '-').'</p>
            </div>';
    } catch (\Exception $e) {
        return '<p class="text-danger text-center">Error loading company info: '.$e->getMessage().'</p>';
    }
}


    public static function createFormLinkForList($param1, $param2, $param3, $param4)
    {
        return '<a href="' . url('' . $param4 . '?pageType=' . $param2 . '&&parentCode=' . $param3 . '#SFR') . '" class="btn btn-sm btn-success">Create Form</a>';
    }

    public static function getPerformanceDetailStudentAndParaWise($student_id, $para_id, $total_lines_in_para, $no_of_dayss, $no_of_liness)
    {
        $no_of_lines = $no_of_liness;
        $no_of_days = $no_of_dayss;
        $remainingLines = $total_lines_in_para - $no_of_lines;
        $getDetail = DB::table('para_other_details')
            ->where('para_id', $para_id)
            ->where('company_id', Session::get('company_id'))
            ->where('status', 1)
            ->first();
        $getDetailTwo = DB::table('student_day_wise_performances')
            ->where('student_id', $student_id)
            ->where('company_id', Session::get('company_id'))
            ->where('para_id', $para_id)
            ->get();
        $a = 0;
        $b = 0;
        $counter = 0;
        $tdClass = '';
        $averageLinesPerDay = round($no_of_lines / $no_of_days);
        foreach ($getDetailTwo as $gdtRow) {
            if ($getDetail->excelent > $counter) {
                $tdClass = 'success';
            } else if ($getDetail->good > $counter) {
                $tdClass = 'warning';
            } else if ($getDetail->average > $counter) {
                $tdClass = 'danger';
            } else {
                $tdClass = '';
            }

            echo '<td class="' . $tdClass . ' text-center">' . round($gdtRow->no_of_lines, 0) . '</td>';
            $counter++;
        }
        $remainingLinesTwo = 0;
        for ($i = $no_of_days + 1; $i <= 100; $i++) {
            if ($getDetail->excelent >= $i) {
                $tdClass = 'success';
            } else if ($getDetail->good >= $i) {
                $tdClass = 'warning';
            } else if ($getDetail->average >= $i) {
                $tdClass = 'danger';
            } else {
                $tdClass = '';
            }
            if ($remainingLinesTwo <= $remainingLines) {
                $remainingLinesTwo += $averageLinesPerDay;
                if ($remainingLinesTwo > $remainingLines) {
                    echo '<td class="' . $tdClass . ' text-center">' . $remainingLinesTwo - $remainingLines . '</td>';
                } else {
                    echo '<td class="' . $tdClass . ' text-center">' . $averageLinesPerDay . '</td>';
                }
            } else {
                echo '<td class="' . $tdClass . ' text-center">-</td>';
            }
        }
        // while (strtotime($from_date) <= strtotime($end_date)) {
        //     $getDetail = DB::table('student_day_wise_performances')
        //         ->where('student_id',$student_id)
        //         ->where('para_id',$para_id)
        //         ->get();
        //     $averageLinesPerDay = $getParaDetail->no_of_lines / count($getDetailTwo);

        //     if(count($getDetail) == 0){
        //         if($a <= $remainingLines){
        //             if($from_date < date('Y-m-d')){
        //                 echo '<td>-</td>';
        //             }else{
        //                 echo '<th>'.$averageLinesPerDay.'</th>';
        //                 $a = $a + $averageLinesPerDay;
        //             }
        //             $b++;

        //         }else{
        //             echo '<td>-</td>';
        //         }
        //     }else{
        //         foreach($getDetail as $gdRow){
        //             echo '<td>'.$gdRow->no_of_lines.'</td>';
        //             if($gdRow->no_of_lines != 0){
        //                 $b++;
        //             }
        //         }
        //     }

        //     $from_date = date ("Y-m-d", strtotime("+1 days", strtotime($from_date)));
        // }
        echo '<input type="hidden" id="remaining_lines_' . $student_id . '_' . $para_id . '" value="' . $remainingLines . '" />';
        echo '<input type="hidden" id="completion_day_' . $student_id . '_' . $para_id . '" value="' . $b . '" />';
    }

    public static function getParaOtherDetail($para_id, $student_id)
    {
        $getDetail = DB::table('para_other_details')
            ->where('para_id', $para_id)
            ->where('company_id', Session::get('company_id'))
            ->where('status', 1)->first();
    ?>
        <td class="text-center"><?php echo $getDetail->total_lines_in_para ?></td>
        <td class="text-center "><?php echo $getDetail->excelent ?><input type="hidden"
                id="excelent_day_<?php echo $student_id; ?>_<?php echo $para_id; ?>"
                value="<?php echo $getDetail->excelent ?>" /></td>
        <td class="text-center"><?php echo $getDetail->good ?><input type="hidden"
                id="good_day_<?php echo $student_id; ?>_<?php echo $para_id; ?>" value="<?php echo $getDetail->good ?>" /></td>
        <td class="text-center"><?php echo $getDetail->average ?><input type="hidden"
                id="average_day_<?php echo $student_id; ?>_<?php echo $para_id; ?>" value="<?php echo $getDetail->average ?>" />
        </td>
        <td class="text-center"><?php echo $getDetail->estimated_completion_days ?></td>
<?php
    }
}
?>