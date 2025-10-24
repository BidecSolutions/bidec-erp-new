<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignTest;
use App\Models\StudentAssignTestStatus;
use DB;
use Illuminate\Http\Request;
use Validator;

class ProgessController extends Controller
{
    //


    public function subjectWiseStudentProgress(Request $request)
    {
        try {
            // Validate request parameters
            $validator = Validator::make($request->all(), [
                'student_id' => 'required|exists:students,id',
                'company_id' => 'required',
                'company_location_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Extract request parameters
            $studentId = $request->input('student_id');
            $schoolId = $request->input('company_id');
            $schoolCampusId = $request->input('company_location_id');

            // Calculate the current month's start and end dates
            $currentMonthStart = now()->startOfMonth()->format('Y-m-d');
            $currentMonthEnd = now()->endOfMonth()->format('Y-m-d');

            // Fetch student information
            $student = DB::table('students')
                ->where('id', $studentId)
                ->where('company_id', $schoolId)
                ->where('company_location_id', $schoolCampusId)
                ->select('id as student_id', 'student_name', 'registration_no')
                ->first();

            if (!$student) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Student not found.',
                    'data' => [],
                ], 200);
            }

            // Fetch the attendance records for the current month
            $attendanceRecords = DB::table('student_attendances')
                ->where('student_id', $studentId)
                ->whereBetween('attendence_date', [$currentMonthStart, $currentMonthEnd])
                ->where('company_id', $schoolId)
                ->where('company_location_id', $schoolCampusId)
                ->select('attendence_type', 'attendence_date')
                ->get();

            // Total days and present days
            $totalDays = $attendanceRecords->count();
            $presentDays = $attendanceRecords->where('attendence_type', '1')->count();

            // Calculate attendance percentage
            $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

            // Fetch all tests the student has participated in
            $tests = StudentAssignTestStatus::where('student_id', $studentId)->get();

            // Group test results by subject
            $subjectWiseData = [];

            foreach ($tests as $test) {
                $assignTest = AssignTest::find($test->assign_test_id);

                if ($assignTest) {
                    $subjectId = $assignTest->subject_id;

                    if (!isset($subjectWiseData[$subjectId])) {
                        $subjectWiseData[$subjectId] = [
                            'subject_id' => $subjectId,
                            'subject_name' => $assignTest->subject->subject_name ?? 'Unknown', // Assuming subject relationship exists
                            'total_marks_obtained' => 0,
                            'total_marks_possible' => 0,
                        ];
                    }

                    $subjectWiseData[$subjectId]['total_marks_obtained'] += $test->no_of_marks_recieved;
                    $subjectWiseData[$subjectId]['total_marks_possible'] += $assignTest->no_of_marks;
                }
            }

            // Calculate percentage for each subject
            foreach ($subjectWiseData as &$data) {
                $data['test_percentage'] = $data['total_marks_possible'] > 0
                    ? round(($data['total_marks_obtained'] / $data['total_marks_possible']) * 100, 2)
                    : 0;
            }

            // Prepare the final response
            $response = [
                'student_id' => $student->student_id,
                'student_name' => $student->student_name,
                'registration_no' => $student->registration_no,
                'attendance_percentage' => $attendancePercentage,
                'subjects' => array_values($subjectWiseData), // Reindex for clean JSON response
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Subject-wise data retrieved successfully.',
                'data' => $response,
            ], 200);
        } catch (\Exception $e) {
            // Log and handle the error
            \Log::error('Error fetching subject-wise data: ' . $e->getMessage() . $e->getLine());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching subject-wise data. Please try again later.',
            ], 500);
        }
    }


    public function studentProgress(Request $request)
    {
        try {
            // Validate request parameters
            $validator = Validator::make($request->all(), [
                'section_id' => 'required|exists:sections,id',
                'company_id' => 'required',
                'company_location_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Extract request parameters
            $sectionId = $request->input('section_id');
            $schoolId = $request->input('company_id');
            $schoolCampusId = $request->input('company_location_id');

            // Calculate the current month's start and end dates
            $currentMonthStart = now()->startOfMonth()->format('Y-m-d');
            $currentMonthEnd = now()->endOfMonth()->format('Y-m-d');

            // Fetch students in the specified section
            $students = DB::table('students')
                ->where('section_id', $sectionId)
                ->where('company_id', $schoolId)
                ->where('company_location_id', $schoolCampusId)
                ->select('id as student_id', 'student_name', 'registration_no')
                ->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'No students found in the specified section.',
                    'data' => [],
                ], 200);
            }

            // Initialize the response data
            $attendanceData = [];

            foreach ($students as $student) {
                // Fetch the attendance records for the current month
                $tests = StudentAssignTestStatus::where('student_id', $student->student_id)->get();

                // Calculate total marks and percentage
                $totalMarksObtained = $tests->sum('no_of_marks_recieved');
                $totalMarksPossible = $tests->reduce(function ($carry, $test) {
                    $assignTest = AssignTest::find($test->assign_test_id);
                    return $carry + ($assignTest ? $assignTest->no_of_marks : 0);
                }, 0);

                $percentage = $totalMarksPossible > 0
                    ? round(($totalMarksObtained / $totalMarksPossible) * 100, 2)
                    : 0;

                $attendanceRecords = DB::table('student_attendances')
                    ->where('student_id', $student->student_id)
                    ->whereBetween('attendence_date', [$currentMonthStart, $currentMonthEnd])
                    ->where('company_id', $schoolId)
                    ->where('company_location_id', $schoolCampusId)
                    ->select('attendence_type', 'attendence_date')
                    ->get();

                // Total days and present days
                $totalDays = $attendanceRecords->count();
                $presentDays = $attendanceRecords->where('attendence_type', '1')->count();

                // Calculate attendance percentage
                $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

                // Add the student's attendance data to the response
                $attendanceData[] = [
                    'student_id' => $student->student_id,
                    'student_name' => $student->student_name,
                    'registration_no' => $student->registration_no,
                    'attendance_percentage' => $attendancePercentage,
                    'total_marks_obtained' => $totalMarksObtained,
                    'total_marks_possible' => $totalMarksPossible,
                    'test_percentage' => $percentage, // Test percentage added here
                ];
            }

            // Return the response
            return response()->json([
                'status' => 'success',
                'message' => 'Attendance data retrieved successfully.',
                'data' => $attendanceData,
            ], 200);
        } catch (\Exception $e) {
            // Log and handle the error
            \Log::error('Error fetching attendance data: ' . $e->getMessage() . $e->getLine());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching attendance data. Please try again later.',
            ], 500);
        }
    }

}
