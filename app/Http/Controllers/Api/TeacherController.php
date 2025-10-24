<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassTeacherAssignments;
use App\Models\Employee;
use App\Models\Roster;
use App\Models\RosterDetail;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectTeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    //

    public function teacherDetail(Request $request)
    {
        $user = Auth::user();
        if ($user->acc_type == 'superadmin') {
            $data = Employee::find($user->emp_id);


            return response()->json([
                'status' => 'success',
                'message' => 'Teacher Data Retrieved Successfully',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'unauthenticated',
                'data' => null
            ], 403);
        }
    }

    public function classesList(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'employee_id' => 'required',
            'company_location_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'message' => $validator->errors()->first(),
                'data' => null,
            ], 422);
        }

        $employee = Employee::find($request->query('employee_id'));
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Teacher Not Found',
                'data' => null
            ], 404);
        }

        // Get the assignments for the teacher
        $assignments = SubjectTeacherAssignment::where('teacher_id', $request->query('employee_id'))
            ->where('company_location_id', $request->query('company_location_id'))
            ->get();

        // Fetch unique section IDs from assignments
        $sectionIds = $assignments->pluck('section_id')->unique();

        // Retrieve sections with their class and name details
        $sections = Section::with('classes')
            ->whereIn('id', $sectionIds)
            ->get();

        // Format the response data with period count
        $responseData = $sections->map(function ($section) use ($request) {
            // Get today's day of the week
            $today = 'Monday'; // e.g., 'Monday', 'Tuesday'

            // Get the roster for the section
            $roster = Roster::where('section_id', $section->id)
                ->where('day_of_week', $today)
                ->first();

            // Count periods assigned to this section today
            $periodsToday = $roster
                ? RosterDetail::where('roster_id', $roster->id)
                    ->whereHas('subjectTeacherAssignment', function ($query) use ($request) {
                        $query->where('teacher_id', $request->query('employee_id'));
                    })
                    ->count()
                : 0;

            return [
                'section_id' => $section->id,
                'class_name' => $section->classes->class_no,
                'section_name' => $section->section_name,
                'periods_today' => $periodsToday, // Add period count
            ];
        });

        // Return the formatted response
        return response()->json([
            'status' => 'success',
            'message' => 'Classes Assigned to the Teacher',
            'data' => $responseData,
        ]);
    }



    public function classTeacher(Request $request)
    {

        $validator = Validator::make($request->query(), [
            'employee_id' => 'required',
            'company_location_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'message' => $validator->errors()->first(),
                'data' => null,
            ], 422);
        }
        $employee = Employee::find($request->query('employee_id'));
        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Teacher Not Found',
                'data' => null
            ], 404);
        }

        $assignment = ClassTeacherAssignments::where('teacher_id', $request->query('employee_id'))->where('company_location_id', $request->query('company_location_id'))->first();
        if (!$assignment) {
            return response()->json([
                "status" => "error",
                "message" => "You are not assigned as a class teacher for any class. Action not permitted.",
                "data" => null
            ], 404);
        }
        $section = Section::with('classes')->find($assignment->section_id);

        $data = [
            'section_id' => $section->id,
            'class_name' => $section->classes->class_no,
            'section_name' => $section->section_name
        ];
        // Step 6: Return the response
        return response()->json([
            'status' => 'success',
            'message' => 'Is the Class Teacher Off',
            'data' => $data
        ]);

    }


    public function subjectList(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'employee_id' => 'required',
            'company_location_id' => 'required|exists:company_locations,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'message' => $validator->errors()->first(),
                'data' => null,
            ], 422);
        }

        // Validate the teacher and their assignments
        $assignments = SubjectTeacherAssignment::where('teacher_id', $request->query('employee_id'))
            ->where('company_location_id', $request->query('company_location_id'))
            ->where('section_id', $request->query('section_id'))
            ->get();

        if ($assignments->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You don\'t have access to see the subject list for this section',
                'data' => null
            ], 403);
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
            ->whereHas('subjectTeacherAssignment', function ($query) use ($request) {
                $query->where('teacher_id', $request->query('employee_id'));
            })
            ->with(['timeSlot', 'subjectTeacherAssignment.subject'])
            ->get();

        // Format the response to include the subject list and their timings
        $responseData = $rosterDetails->map(function ($detail) {
            return [
                'subject_id' => $detail->subjectTeacherAssignment->subject->id,
                'subject_name' => $detail->subjectTeacherAssignment->subject->subject_name,
                'time_slot' => $detail->timeSlot ? [
                    'start_time' => Carbon::parse($detail->timeSlot->start_time)->format('h:i A'), // Convert to 12-hour format
                    'end_time' => Carbon::parse($detail->timeSlot->end_time)->format('h:i A'),   // Convert to 12-hour format
                ] : null, // Handle cases where timeSlot is null
            ];
        });

        // Return the formatted response
        return response()->json([
            'status' => 'success',
            'message' => 'Subject list with periods for today retrieved successfully',
            'data' => $responseData,
        ]);
    }



    public function studentList(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'employee_id' => 'required',
            'company_location_id' => 'required|exists:company_locations,id',
            'section_id' => 'required|exists:sections,id',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'message' => $validator->errors()->first(),
                'data' => null,
            ], 422);
        }


        $assignments = ClassTeacherAssignments::where('teacher_id', $request->query('employee_id'))
            ->where('section_id', $request->query('section_id'))
            ->first();

        if (!$assignments) {
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have access to see student List For this section',
                'data' => null
            ]);

        }




        $responseData = Student::where('section_id', $request->query('section_id'))->select('id', 'student_name', 'registration_no')->get();

        // Return the formatted response
        return response()->json([
            'status' => 'success',
            'message' => 'Students List Assigned to the Section',
            'data' => $responseData,
        ]);

    }




}
