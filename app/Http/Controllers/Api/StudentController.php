<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignTask;
use App\Models\AssignTest;
use App\Models\Roster;
use App\Models\RosterDetail;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendence;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    //



    public function studentDetail(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'message' => $validator->errors()->first(),
                'data' => null,
            ], 422);
        }
        $data = Student::findOrFail($request->student_id);
        return response()->json([
            'status' => 'success',
            'message' => 'Student Data Retrieved Successfully',
            'data' => $data
        ]);
    }

    public function studentOffRegistrationNumber(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'message' => $validator->errors()->first(),
                'data' => null,
            ], 422);
        }

        $student = Student::findOrFail($request->student_id);
        $section = Section::with('classes')->find($student->section_id);
        $data = [
            'student_id' => $student->id,
            'registration_no' => $student->registration_no,
            'section' => [
                'section_id' => $section->id,
                'class_name' => $section->classes->class_no,
                'section_name' => $section->section_name
            ]
        ];
        return response()->json([
            'status' => 'success',
            'message' => 'Student Off & Registration Number retrieved Successfully',
            'data' => $data
        ]);
    }

    public function subjectList(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'section_id' => 'required|exists:sections,id', // Validate section_id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'message' => $validator->errors()->first(),
                'data' => null,
            ], 422);
        }

        // Get today's day of the week
        $today = Carbon::now()->format('l');

        // Retrieve the roster for the section for today
        $roster = Roster::where('section_id', $request->query('section_id'))
            ->where('day_of_week', $today)
            ->first();

        if (!$roster) {
            return response()->json([
                'status' => 'error',
                'message' => 'No schedule found for this section today',
                'data' => null
            ], 404);
        }

        // Get roster details for today with the required relationships
        $rosterDetails = RosterDetail::where('roster_id', $roster->id)
            ->with(['timeSlot', 'subjectTeacherAssignment.subject', 'subjectTeacherAssignment.teacher'])
            ->get();

        if ($rosterDetails->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No subjects scheduled for this section today',
                'data' => null
            ], 404);
        }

        // Format the response data
        $responseData = $rosterDetails->map(function ($detail) {
            return [
                'subject_id' => $detail->subjectTeacherAssignment->subject->id,
                'subject_name' => $detail->subjectTeacherAssignment->subject->subject_name,
                'teacher_name' => $detail->subjectTeacherAssignment->teacher->emp_name ?? 'Not Assigned',
                'time_slot' => $detail->timeSlot ? [
                    'start_time' => Carbon::parse($detail->timeSlot->start_time)->format('h:i A'), // Convert to 12-hour format
                    'end_time' => Carbon::parse($detail->timeSlot->end_time)->format('h:i A'),   // Convert to 12-hour format
                ] : null, // Handle cases where timeSlot is null
            ];
        });

        // Return the formatted response
        return response()->json([
            'status' => 'success',
            'message' => 'Subject list for today retrieved successfully',
            'data' => $responseData,
        ]);
    }


    public function allPeriodsForSection(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'section_id' => 'required|exists:sections,id', // Validate section_id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'message' => $validator->errors()->first(),
                'data' => null,
            ], 422);
        }

        // Retrieve all rosters for the section
        $rosters = Roster::where('section_id', $request->query('section_id'))
            ->with(['rosterDetails.subjectTeacherAssignment.subject', 'rosterDetails.subjectTeacherAssignment.teacher', 'rosterDetails.timeSlot'])
            ->get();

        if ($rosters->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No schedules found for this section',
                'data' => null
            ], 404);
        }

        // Format the response data
        $responseData = $rosters->map(function ($roster) {
            return [
                'day_of_week' => $roster->day_of_week,
                'periods' => $roster->rosterDetails->map(function ($detail) {
                    return [
                        'subject_id' => $detail->subjectTeacherAssignment->subject->id ?? null,
                        'subject_name' => $detail->subjectTeacherAssignment->subject->subject_name ?? 'Unknown Subject',
                        'teacher_name' => $detail->subjectTeacherAssignment->teacher->emp_name ?? 'Not Assigned',
                        'time_slot' => $detail->timeSlot ? [
                            'start_time' => Carbon::parse($detail->timeSlot->start_time)->format('h:i A'), // Convert to 12-hour format
                            'end_time' => Carbon::parse($detail->timeSlot->end_time)->format('h:i A'),   // Convert to 12-hour format
                        ] : null,
                    ];
                })
            ];
        });

        // Return the formatted response
        return response()->json([
            'status' => 'success',
            'message' => 'All periods retrieved successfully',
            'data' => $responseData,
        ]);
    }



    public function taskList(Request $request)
    {
        // Validate input data
        $validatedData = $request->validate([
            'student_id' => 'required|integer|exists:students,id', // Valid student ID
            'subject_id' => 'required|integer|exists:subjects,id', // Valid subject ID
            'company_location_id' => 'required|integer'
        ]);

        try {
            // Fetch tasks for the student and subject
            $tasks = AssignTask::with([
                'studentAssignTaskStatus' => function ($query) use ($validatedData) {
                    $query->where('student_id', $validatedData['student_id']);
                }
            ])
                ->where('subject_id', $validatedData['subject_id'])
                ->where('company_location_id', $validatedData['company_location_id'])
                ->where('assign_task_status', 1)
                ->get();

            // Calculate counts
            $totalTasks = $tasks->count();
            $completedCount = $tasks->filter(function ($task) use ($validatedData) {
                $studentStatus = $task->studentAssignTaskStatus->firstWhere('student_id', $validatedData['student_id']);
                return $studentStatus && $studentStatus->assign_task_status == 2 && $studentStatus->submission_date !== null;
            })->count();
            $uncompletedCount = $tasks->filter(function ($task) use ($validatedData) {
                $studentStatus = $task->studentAssignTaskStatus->firstWhere('student_id', $validatedData['student_id']);
                return $studentStatus && $studentStatus->assign_task_status == 1 && $studentStatus->submission_date === null;
            })->count();

            // Format the response
            $responseData = $tasks->map(function ($task) use ($validatedData) {
                $studentStatus = $task->studentAssignTaskStatus->firstWhere('student_id', $validatedData['student_id']);
                return [
                    'task_title' => $task->title,
                    'start_date' => $task->start_date,
                    'end_date' => $task->end_date,
                    'task_status' => $task->assign_task_status, // Task status from the task itself
                    'submission_date' => $studentStatus ? $studentStatus->submission_date : null, // Status specific to the student
                    'assign_task_status' => $studentStatus ? $studentStatus->assign_task_status : null, // Status specific to the student
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Tasks Retrieved Successfully',
                'data' => $responseData,
                'total_task_count' => $totalTasks,
                'completed_count' => $completedCount,
                'uncompleted_count' => $uncompletedCount,
            ], 200);

        } catch (\Exception $e) {
            // Handle errors
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving tasks.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function testList(Request $request)
    {
        // Validate input data
        $validatedData = $request->validate([
            'student_id' => 'required|integer|exists:students,id', // Valid student ID
            'subject_id' => 'required|integer|exists:subjects,id', // Valid subject ID
            'company_location_id' => 'required|integer'
        ]);

        try {
            // Fetch tests for the student and subject
            $tests = AssignTest::with([
                'studentAssignTestStatus' => function ($query) use ($validatedData) {
                    $query->where('student_id', $validatedData['student_id']);
                }
            ])
                ->where('subject_id', $validatedData['subject_id'])
                ->where('assign_test_status', 1)
                ->where('company_location_id', $validatedData['company_location_id'])
                ->get();

            // Format the response
            $responseData = $tests->map(function ($test) use ($validatedData) {
                $studentStatus = $test->studentAssignTestStatus->firstWhere('student_id', $validatedData['student_id']);
                return [
                    'test_name' => $test->title,
                    'test_status' => $test->assign_test_status, // Status of the test itself
                    'student_test_status' => $studentStatus ? $studentStatus->assign_test_status : null, // Status specific to the student
                    'total_marks' => $test->no_of_marks, // Total marks for the test
                    'marks_received' => $studentStatus ? $studentStatus->no_of_marks_recieved : null, // Marks received by the student, null if not given
                ];
            });

            // Calculate counts
            $totalTests = $responseData->count();
            $completedTests = $responseData->where('student_test_status', 2)->count();
            $pendingTests = $totalTests - $completedTests;

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Tests Retrieved Successfully',
                'data' => $responseData,
                'counts' => [
                    'total_tests' => $totalTests,
                    'completed_tests' => $completedTests,
                    'pending_tests' => $pendingTests,
                ],
            ], 200);

        } catch (\Exception $e) {
            // Handle errors
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving tests.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function getYearWiseAttendance(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'company_id' => 'required',
            'company_location_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $studentId = $request->input('student_id');
        $schoolId = $request->input('company_id');
        $campusId = $request->input('company_location_id');

        // Year-wise counts and percentages
        $yearWiseAttendance = DB::table('student_attendances')
            ->selectRaw("
                YEAR(attendence_date) as year,
                SUM(attendence_type = 1) as present,
                SUM(attendence_type = 2) as absent,
                SUM(attendence_type = 3) as late,
                SUM(attendence_type = 4) as `leave`,
                COUNT(*) as total_days,
                ROUND((SUM(attendence_type = 1) / COUNT(*)) * 100, 2) as attendance_percentage
            ")
            ->where('student_id', $studentId)
            ->where('company_id', $schoolId)
            ->where('company_location_id', $campusId)
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Year-wise attendance retrieved successfully.',
            'data' => $yearWiseAttendance,
        ]);
    }

    public function getMonthWiseAttendance(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'company_id' => 'required',
            'company_location_id' => 'required',
            'month' => 'required|date_format:Y-m' // Format: YYYY-MM
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $studentId = $request->input('student_id');
        $schoolId = $request->input('company_id');
        $campusId = $request->input('company_location_id');
        $month = $request->input('month');

        // Month-wise summary
        $monthSummary = DB::table('student_attendances')
            ->selectRaw("
                SUM(attendence_type = 1) as present,
                SUM(attendence_type = 2) as absent,
                SUM(attendence_type = 3) as late,
                SUM(attendence_type = 4) as `leave`,
                COUNT(*) as total_days,
                ROUND((SUM(attendence_type = 1) / COUNT(*)) * 100, 2) as attendance_percentage
            ")
            ->where('student_id', $studentId)
            ->where('company_id', $schoolId)
            ->where('company_location_id', $campusId)
            ->whereRaw("DATE_FORMAT(attendence_date, '%Y-%m') = ?", [$month])
            ->first();

        // Day-wise details
        $dayWiseAttendance = StudentAttendence::where('student_id', $studentId)
            ->where('company_id', $schoolId)
            ->where('company_location_id', $campusId)
            ->whereRaw("DATE_FORMAT(attendence_date, '%Y-%m') = ?", [$month])
            ->selectRaw("DAY(attendence_date) as date, attendence_type")
            ->orderBy('attendence_date', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Month details attendance retrieved successfully.',
            'data' => [
                'month_summary' => $monthSummary,
                'day_wise_attendance' => $dayWiseAttendance,
            ],
        ]);
    }



}
