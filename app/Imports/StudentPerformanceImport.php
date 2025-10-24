<?php
    namespace App\Imports;

    use Illuminate\Support\Collection;
    use Maatwebsite\Excel\Concerns\ToCollection;
    use Session;
    use DB;
    use Auth;
    use Carbon\Carbon;
    
    class StudentPerformanceImport implements ToCollection
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
            $getStudentList = DB::table('students')->get();
            $getParaList = DB::table('paras as p')
                ->select('p.*','pod.total_lines_in_para')
                ->join('para_other_details as pod','p.id','=','pod.para_id')
                ->where('pod.company_id',$schoolId)
                ->where('pod.company_location_id',$schoolCampusId)
                ->get();
            
            $counter = 0;
            $errorData = [];
            
            
            foreach ($rows as $row) {
                if($counter != 0){
                    $noOfLines = $row[3];
                    $paraName = $row[1];
                    $date = Carbon::createFromFormat('d-m-Y', $row[2])->format('Y-m-d');
                    $studentId = 0;
                    $paraId = 0;
                    $noOfLinesTwo = 0;
                    foreach($getStudentList as $gslRow){
                        if($gslRow->registration_no == $row[0]){
                            $studentId = $gslRow->id;
                        }
                    }
                    foreach($getParaList as $gplRow){
                        if($gplRow->para_name == $paraName){
                            $paraId = $gplRow->id;
                            $noOfLinesTwo = $gplRow->total_lines_in_para;
                        }
                    }
                    if($studentId != 0){
                        
                        $totalLines = DB::table('student_day_wise_performances')
                            ->where('student_id', $studentId)
                            ->where('para_id', $paraId)
                            ->where('company_id', $schoolId)
                            ->where('company_location_id', $schoolCampusId)
                            ->sum('no_of_lines');
                        $remainingLines = $noOfLinesTwo - $totalLines;
                        if($remainingLines <= $noOfLines){
                            $errorData[] = 'Test One '.$counter;
                        }else{
                            
                            $getDetail = DB::table('student_day_wise_performances')
                                ->where('student_id',$studentId)
                                ->where('para_id',$paraId)
                                ->where('performance_date',$date)
                                ->get();
                            if(count($getDetail) == 0){
                                $data = array(
                                    'company_id' => $schoolId,
                                    'company_location_id' => $schoolCampusId,
                                    'student_id' => $studentId,
                                    'para_id' => $paraId ?? 0,
                                    'performance_date' => $date,
                                    'no_of_lines' => $noOfLines,
                                    'status' => 1,
                                    'created_by' => Auth::user()->name,
                                    'date' => date('Y-m-d')
                                );
                                
                                // Insert the processed data into the database if needed
                                if (!empty($data)) {
                                    DB::table('student_day_wise_performances')->insert($data);
                                }
                                
                            }
                        }
                    }
                }
                $counter++;
            }
            
            return $errorData;
        }
    }
?>