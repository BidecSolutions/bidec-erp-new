@php
    use App\Helpers\CommonHelper;
    $schoolId = Session::get('company_id');
    $aCounter = 1;
    $totalNoOfStudents = 0;
@endphp
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="card-header"><strong>Dashboard {{$monthYear}}</strong></div>
        <div class="lineHeight">&nbsp;</div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th colspan="7" class="text-center">Teachers Details</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">S.No</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Department</th>
                                            <th class="text-center">Class</th>
                                            <th class="text-center">class #</th>
                                            <th class="text-center">No. of Student</th>
                                            <th class="text-center">Monthly Test</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($getTeacherDetail as $gtdRow)
                                            @php
                                                $totalNoOfStudents += $gtdRow->no_of_students;
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{$aCounter++}}</td>
                                                <td>{{$gtdRow->emp_name}}</td>
                                                <td>{{$gtdRow->department_name}}</td>
                                                <td>{{$gtdRow->class_name}}</td>
                                                <td class="text-center">{{$gtdRow->class_no}}</td>
                                                <td class="text-center">{{$gtdRow->no_of_students}}</td>
                                                <td class="text-center"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center" colspan="5">Total Students</th>
                                            <th class="text-center">{{$totalNoOfStudents}}</th>
                                            <th class="text-center">---</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div  class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="text-center">Salary Break up</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">Heads</th>
                                                    <th class="text-center">Amounts</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                            <tfoot>
                                                
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-condensed">
                                            <thead>
                                                <tr>
                                                    <th colspan="3" class="text-center">Expense Details</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-center">S.No</th>
                                                    <th class="text-center">Heads</th>
                                                    <th class="text-center">Amounts</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                            <tfoot>
                                                
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th colspan="4" class="text-center">Summary of Hifz Students' Performance</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Category</th>
                                            <th class="text-center">Previous</th>
                                            <th class="text-center">Current</th>
                                            <th class="text-center">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                    <tfoot>
                                        
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="text-center">Management Details</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">S.No</th>
                                            <th class="text-center">Name</th>
                                            <th class="text-center">Designation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                    <tfoot>
                                        
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th colspan="3" class="text-center">Monthly Income Detail</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">S.No</th>
                                            <th class="text-center">Heads</th>
                                            <th class="text-center">Amounts</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                    <tfoot>
                                        
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th colspan="2" class="text-center">Event Expense</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Heads</th>
                                            <th class="text-center">Amounts</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                    <tfoot>
                                        
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th colspan="7" class="text-center">Facts About Monthly Fee</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Self Finance</th>
                                    <th class="text-center">Tahfeez Fee</th>
                                    <th class="text-center">Maktab Fee</th>
                                    <th class="text-center">Avg. Tahfeez Fee</th>
                                    <th class="text-center">Avg. Maktab Fee</th>
                                    <th class="text-center">Tahfeez Per Std. Exp</th>
                                    <th class="text-center">Maktab Per Std. Exp</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                                
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>