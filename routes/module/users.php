<?php
    use App\Http\Controllers\Users\UserController;
    use App\Http\Controllers\Users\UsersAddDetailController;
    use App\Http\Controllers\Users\UsersDataCallController;
use Illuminate\Support\Facades\Route;

    Route::group(['prefix' => 'users','before' => 'csrf'], function () {
        Route::get('/createMainMenuTitleForm',[UserController::class, 'createMainMenuTitleForm'])->name('users.createMainMenuTitleForm');
        Route::get('/createSubMenuForm',[UserController::class, 'createSubMenuForm'])->name('users.createSubMenuForm');
        Route::get('/viewUsersLoginTimePeriodList',[UserController::class, 'viewUsersLoginTimePeriodList'])->name('users.viewUsersLoginTimePeriodList');
        Route::get('/addUsersLoginTimePeriod',[UserController::class, 'addUsersLoginTimePeriod'])->name('users.addUsersLoginTimePeriod');
        Route::get('/viewUsersLoginTimePeriodList',[UserController::class, 'viewUsersLoginTimePeriodList'])->name('users.viewUsersLoginTimePeriodList');
    });
    Route::group(['prefix' => 'uad','before' => 'csrf'], function () {
        Route::post('/addMainMenuTitleDetail',[UsersAddDetailController::class,'addMainMenuTitleDetail'])->name('users.addMainMenuTitleDetail');
        Route::post('/addSubMenuDetail',[UsersAddDetailController::class,'addSubMenuDetail'])->name('users.addSubMenuDetail');
        Route::post('/addUsersLoginTimePeriodAndPermissionDetail',[UsersAddDetailController::class,'addUsersLoginTimePeriodAndPermissionDetail'])->name('users.addUsersLoginTimePeriodAndPermissionDetail');
    });

    Route::group(['prefix' => 'udc','before' => 'csrf'], function (){
        Route::get('/filterUsersLoginTimePeriodAndRolePermissionList',[UsersDataCallController::class,'filterUsersLoginTimePeriodAndRolePermissionList'])->name('users.filterUsersLoginTimePeriodAndRolePermissionList');
        Route::get('/loadSchoolCampusDetailDependSchoolDetailId',[UsersDataCallController::class,'loadSchoolCampusDetailDependSchoolDetailId'])->name('users.loadSchoolCampusDetailDependSchoolDetailId');
        Route::get('/makeFormAssignSchoolAndSchoolCampusSection',[UsersDataCallController::class,'makeFormAssignSchoolAndSchoolCampusSection'])->name('users.makeFormAssignSchoolAndSchoolCampusSection');
        
    });
?>