<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Events\UserLoggedIn;


class AuthController extends Controller
{
    //

    public function login(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
                'device_token' => 'nullable', // Add validation for device token
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                    'data' => null,
                ], 422);
            }

            $credentials = $request->only('username', 'password');
            Log::info("Login Data: " . $request->username);

            $user = User::where('username', $request->username)->where('status', 1)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Username does not exist or your account is inactive',
                    'data' => null,
                ], 404);
            }

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Invalid credentials',
                    'data' => null,
                ], 401);
            }

            // // Save the device token
            // $deviceToken = $request->input('device_token');

            // if (!empty($user->device_tokens)) {
            //     $existingTokens = json_decode($user->device_tokens, true);
            // } else {
            //     $existingTokens = [];
            // }

            // if (!in_array($deviceToken, $existingTokens)) {
            //     $existingTokens[] = $deviceToken;
            //     $user->device_tokens = json_encode($existingTokens);
            //     $user->save();
            // }

            // Generate access token
            $accessToken = $user->createToken('MyApp')->plainTextToken;

            // event(new UserLoggedIn($user));

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'User logged in successfully',
                'data' => [
                    'user' => $user,
                    'access_token' => $accessToken,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function lmsParentLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                    'data' => null,
                ], 422);
            }

            $credentials = $request->only('email', 'password');
            Log::info("Login Data: " . $request->email);
            $user = User::where('email', $request->email)->where('status', 1)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Email does not exist or your account is inactive',
                    'data' => null,
                ], 404);
            }

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Invalid credentials',
                    'data' => null,
                ], 401);
            }

            // Generate access token
            $accessToken = $user->createToken('MyApp')->plainTextToken;
            Log::info("Userids Array: " . json_encode($user->student_ids_array_for_parents));


            $modifiedUser = [
                'id' => $user->id,
                'acc_type' => $user->acc_type,
                'company_id' => $user->company_id,
                'company_location_id' => $user->company_location_id,
                'username' => $user->username,
                'mobile_no' => $user->mobile_no,
                'cnic_no' => $user->cnic_no,
                'name' => $user->name,
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Parent logged in successfully',
                'data' => [
                    'user' => $modifiedUser,
                    'access_token' => $accessToken,
                    // 'students' => $studentDetails,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        try {
            // Ensure the user is authenticated
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No authenticated user found',
                    'data' => null
                ], 401);
            }

            // Revoke the token
            $user->currentAccessToken()->delete();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while logging out',
                'data' => $e->getMessage()
            ], 500);
        }
    }
    public function sendToken(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'device_token' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'warning',
                    'message' => $validator->errors()->first(),
                    'data' => null,
                ], 422);
            }

            // Ensure the user is authenticated
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No authenticated user found',
                    'data' => null
                ], 401);
            }

            // Save the device token for the authenticated user
            $deviceToken = $request->input('device_token');

            // Assuming you have a `device_tokens` field in the `users` table as JSON or a separate model/table
            if (!empty($user->device_tokens)) {
                $existingTokens = json_decode($user->device_tokens, true);
            } else {
                $existingTokens = [];
            }

            if (!in_array($deviceToken, $existingTokens)) {
                $existingTokens[] = $deviceToken;
                $user->device_tokens = json_encode($existingTokens);
                $user->save();
            }

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Device Token Added Successfully',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while saving Device Token',
                'data' => $e->getMessage()
            ], 500);
        }
    }


    public function studentList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'warning',
                'message' => $validator->errors()->first(),
                'data' => null,
            ], 422);
        }
        $user = Auth::user();
        if ($user->acc_type != 'parent') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'data' => null,
            ]);
        }
        $studentIdsArray = json_decode($user->student_ids_array_for_parents, true) ?? [];
        $studentIds = array_column($studentIdsArray, 'student_id');

        $students = Student::whereIn('id', $studentIds)->where('company_id', $request->company_id)->get();

        // Format student data
        $studentDetails = $students->map(function ($student) {
            $Section = Section::with('classes')->find($student->section_id);
            return [
                'id' => $student->id,
                'name' => $student->student_name,
                'roll_no' => $student->registration_no,
                'section_name' => $Section->classes->class_no . '-' . $Section->section_name,
            ];
        });
        return response()->json([
            'status' => 'success',
            'message' => 'Student List retrieved Successfully successfully',
            'data' => $studentDetails
        ], 200);
    }



}
