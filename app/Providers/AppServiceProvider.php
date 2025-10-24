<?php

namespace App\Providers;

use App\Repositories\AcademicDetailRepository;
use App\Repositories\Interfaces\AcademicDetailRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use App\Repositories\DepartmentRepository;

use App\Repositories\Interfaces\StudentRepositoryInterface;
use App\Repositories\StudentRepository;

use App\Repositories\Interfaces\TeacherRepositoryInterface;
use App\Repositories\TeacherRepository;

use App\Repositories\Interfaces\SubjectRepositoryInterface;
use App\Repositories\SubjectRepository;

use App\Repositories\Interfaces\SectionRepositoryInterface;
use App\Repositories\SectionRepository;

use App\Repositories\Interfaces\ClassRepositoryInterface;
use App\Repositories\ClassRepository;


use App\Repositories\Interfaces\AcademicStatusRepositoryInterface;
use App\Repositories\AcademicStatusRepository;

use App\Repositories\Interfaces\ClassTimingRepositoryInterface;
use App\Repositories\ClassTimingRepository;

use App\Repositories\Interfaces\CountryRepositoryInterface;
use App\Repositories\CountryRepository;


use App\Repositories\Interfaces\StateRepositoryInterface;
use App\Repositories\StateRepository;


use App\Repositories\Interfaces\CityRepositoryInterface;
use App\Repositories\CityRepository;

use App\Repositories\Interfaces\ParaRepositoryInterface;
use App\Repositories\ParaRepository;

use App\Repositories\Interfaces\StudentPerformanceRepositoryInterface;
use App\Repositories\StudentPerformanceRepository;

use App\Repositories\Interfaces\CompanyRepositoryInterface;
use App\Repositories\CompanyRepository;


use App\Repositories\Interfaces\LocationRepositoryInterface;
use App\Repositories\LocationRepository;

use App\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Repositories\EmployeeRepository;


use App\Repositories\Interfaces\ChartOfAccountRepositoryInterface;
use App\Repositories\ChartOfAccountRepository;

use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\PaymentRepository;

use App\Repositories\Interfaces\ReceiptRepositoryInterface;
use App\Repositories\ReceiptRepository;

use App\Repositories\Interfaces\JournalVoucherRepositoryInterface;
use App\Repositories\JournalVoucherRepository;

use App\Repositories\Interfaces\HeadRepositoryInterface;
use App\Repositories\HeadRepository;

use App\Repositories\Interfaces\LevelOfPerformanceRepositoryInterface;
use App\Repositories\LevelOfPerformanceRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DepartmentRepositoryInterface::class, DepartmentRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(TeacherRepositoryInterface::class, TeacherRepository::class);
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
        $this->app->bind(SectionRepositoryInterface::class, SectionRepository::class);
        $this->app->bind(ClassRepositoryInterface::class, ClassRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(StateRepositoryInterface::class, StateRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(ClassTimingRepositoryInterface::class, ClassTimingRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(ParaRepositoryInterface::class, ParaRepository::class);
        $this->app->bind(StudentPerformanceRepositoryInterface::class, StudentPerformanceRepository::class);
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(ChartOfAccountRepositoryInterface::class, ChartOfAccountRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(ReceiptRepositoryInterface::class, ReceiptRepository::class);
        $this->app->bind(JournalVoucherRepositoryInterface::class, JournalVoucherRepository::class);
        $this->app->bind(HeadRepositoryInterface::class, HeadRepository::class);
        $this->app->bind(LevelOfPerformanceRepositoryInterface::class, LevelOfPerformanceRepository::class);
        $this->app->bind(AcademicStatusRepositoryInterface::class, AcademicStatusRepository::class);
        $this->app->bind(AcademicDetailRepositoryInterface::class, AcademicDetailRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        require_once app_path('Helpers/CommonFunctions.php');
    }
}
