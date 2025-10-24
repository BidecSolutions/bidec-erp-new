<?php
    namespace App\Imports;

    use Illuminate\Support\Collection;
    use Maatwebsite\Excel\Concerns\ToCollection;
    use Session;
    use DB;
    use Auth;
    use Carbon\Carbon;
    
    class EmployeeAttendanceImport implements ToCollection
    {
        function fractionToTime($fraction) {
            //return $fraction;
            $hours = floor($fraction * 24);
            $minutes = round(($fraction * 24 - $hours) * 60);
            return sprintf("%02d:%02d", $hours, $minutes);
        }

        public function collection(Collection $rows)
        {
            $schoolId = Session::get('company_id');
            $schoolCampusId = Session::get('company_location_id');
            $getEmployeeList = DB::table('employees')->where('company_id',$schoolId)->get();
            $counter = 0;
            
            foreach ($rows as $row) {
                if($counter != 0){
                    $clockIn = $this->fractionToTime($row[2]);
                    $clockOut = $this->fractionToTime($row[3]);
                    $date = Carbon::createFromFormat('d-m-Y', $row[1])->format('Y-m-d');
                    $employeeId = 0;
                    foreach($getEmployeeList as $gelRow){
                        if($gelRow->emp_no == $row[0]){
                            $employeeId = $gelRow->id;
                        }
                    }
                    if($employeeId != 0){
                        $data[] = [
                            'company_id' => $schoolId,
                            'company_location_id' => $schoolCampusId,
                            'type' => 1,
                            'emp_id' => $employeeId,
                            'student_id' => 0,
                            'date' => $date,
                            'clock_in' => $clockIn,
                            'clock_out' => $clockOut,
                            'status' => 1,
                            'created_by' => Auth::user()->name,
                            'created_date' => date('Y-m-d')
                        ];
                    }
                }
                $counter++;
            }
            
            // Insert the processed data into the database if needed
            if (!empty($data)) {
                DB::table('attendances')->insert($data);
            }
        }
    }
?>