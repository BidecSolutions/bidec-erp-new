<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\{
    AuthController,
    LocationController,
    TeacherController,
    StudentController,
    AcademicDetailController,
    ProgessController
};

use App\Http\Controllers\{
    AssignTaskController,
    AssignTestController,
    StudentAttendenceController,
    AnnouncementController,
    FeeController,
    NotificationController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('lmslogin', 'login')->name('api.lmslogin');
    Route::post('lms-parent-login', 'lmsParentLogin')->name('api.lmslogin');
});
Route::middleware('auth:sanctum')->group(function () {

    Route::controller(LocationController::class)->group(function () {
        Route::get('campus/detail', 'detail')->name('api.campus.detail');
    });

    Route::controller(AuthController::class)->group(function () {
        Route::post('lmslogout', 'logout')->name('api.logout');
        Route::post('send-token', 'sendToken')->name('api.sendToken');
    });


    Route::prefix('teacher')->group(function () {
        Route::middleware('throttle:api')->group(function () {
            Route::controller(AssignTaskController::class)->group(function () {
                Route::prefix('assign-tasks')->group(function () {
                    Route::post('/', 'index')->name('assign-tasks.index');
                    Route::post('/studentList', 'studentList')->name('assign-tasks.studentList');
                    Route::post('/subjectList', 'subjectList')->name('assign-tasks.subjectList');
                    Route::post('/store', 'store')->name('assign-tasks.store');
                    Route::post('/update', 'update')->name('assign-tasks.update');
                    Route::post('/change-status', 'changeStatus')->name('assign-tasks.change-status');
                    Route::post('/update-assign-task-status', 'updateAssignTaskStatus')->name('assign-tasks.update-assign-task-status');
                    Route::get('/detail/{id}', 'show')->name('assign-tasks.detail');
                    Route::post('/update-multiple-student-task-status', 'updateMultipleStudentTaskStatus')->name('assign-tasks.update-multiple-student-task-status');
                });
            });

            Route::controller(AnnouncementController::class)->group(function () {
                Route::prefix('announcements')->group(function () {
                    Route::post('/', 'index')->name('announcements.index');
                    Route::post('/store', 'store')->name('announcements.store');
                    Route::get('/detail/{id}', 'show')->name('announcements.detail');
                    Route::post('/today-latest-annoucement', 'todayLatestAnnoucement')->name('announcements.today-latest-annoucement');
                });
            });

            Route::controller(FeeController::class)->group(function () {
                Route::prefix('fees')->group(function () {
                    Route::post('/student-wise-generated-fee-voucher-list', 'student_wise_generated_fee_voucher_list')->name('fees.student-wise-generated-fee-voucher-list');
                });
            });

            Route::controller(AssignTestController::class)->group(function () {
                Route::prefix('assign-tests')->group(function () {
                    Route::post('/', 'index')->name('assign-tests.index');
                    Route::post('/store', 'store')->name('assign-tests.store');
                    Route::post('/studentList', 'studentList')->name('assign-tests.studentList');
                    Route::post('/change-status', 'changeStatus')->name('assign-tests.change-status');
                    Route::post('/update-assign-test-status', 'updateAssignTestStatus')->name('assign-tests.update-assign-test-status');
                    Route::get('/detail/{id}', 'show')->name('assign-tests.detail');
                    Route::post('/update', 'update')->name('assign-tests.update');
                    Route::post('/update-multiple-student-test-status', 'updateMultipleStudentTestStatus')->name('assign-tests.update-multiple-student-test-status');

                });
            });

            Route::controller(StudentAttendenceController::class)->group(function () {
                Route::prefix('student-attendance')->group(function () {
                    Route::post('/', 'index')->name('student-attendance.index');
                    Route::post('/store', 'store')->name('student-attendance.store');
                    Route::post('/storeMassAttendance', 'storeMassAttendance')->name('student-attendance.storeMassAttendance');

                });
            });
            Route::controller(ProgessController::class)->group(function () {
                Route::prefix('progress')->group(function () {
                    Route::post('/studentProgress', 'studentProgress')->name('progress.studentProgress');
                    Route::post('/subjectWiseStudentProgress', 'subjectWiseStudentProgress')->name('progress.subjectWiseStudentProgress');

                });
            });

            Route::controller(TeacherController::class)->group(function () {
                Route::get('detail', 'teacherDetail')->name('api.teacherDetail');
                Route::get('/{empId}/classesToday', 'classesToday')->name('api.teachers.classesToday');
                Route::get('/classTeacher', 'classTeacher')->name('api.teachers.classTeacher');
                Route::get('/classesList', 'classesList')->name('api.teachers.classesList');
                Route::get('/studentList', 'studentList')->name('api.teachers.studentList');
                Route::get('/subjectList', 'subjectList')->name('api.teachers.subjectList');
            });

            Route::controller(NotificationController::class)->group(function () {
                Route::prefix('notifications')->group(function () {
                    Route::post('/student-notifications', 'student_notifications')->name('notifications.student-notifications');
                });
            });
            Route::controller(AcademicDetailController::class)->group(function () {
                Route::prefix('academic-detail')->group(function () {
                    Route::post('/', 'index')->name('academic-detail.index');
                    Route::post('/academic-status', 'academicstatus')->name('academic-detail.academicstatus');
                    Route::post('/listAcademicDetailsByMonth', 'listAcademicDetailsByMonth')->name('listAcademicDetailsByMonth.academicstatus');
                });
            });
        });


    });
    Route::prefix('student')->group(function () {
        Route::middleware('throttle:api')->group(function () {

            Route::controller(StudentController::class)->group(function () {
                Route::post('detail', 'studentDetail')->name('api.studentDetail');
                Route::post('/studentOff', 'studentOffRegistrationNumber')->name('api.student.studentOffRegistrationNumber');
                Route::get('/subjectList', 'subjectList')->name('api.student.subjectList');
                Route::get('/allPeriodsForSection', 'allPeriodsForSection')->name('api.student.allPeriodsForSection');
                Route::post('/taskList', 'taskList')->name('api.student.taskList');
                Route::post('/testList', 'testList')->name('api.student.testList');
                Route::post('/getYearWiseAttendance', 'getYearWiseAttendance')->name('api.student.getYearWiseAttendance');
                Route::post('/getMonthWiseAttendance', 'getMonthWiseAttendance')->name('api.student.getMonthWiseAttendance');
                Route::post('/getAllDatesAttendance', 'getAllDatesAttendance')->name('api.student.getAllDatesAttendance');
            });
        });
    });
    Route::prefix('parent')->group(function () {
        Route::middleware('throttle:api')->group(function () {

            Route::controller(AuthController::class)->group(function () {
                Route::post('studentList', 'studentList')->name('api.parent.studentList');
            });
        });
    });
});
