@php
    use App\Helpers\CommonHelper;
@endphp
<div class="well">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                school Information
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="floatLeft">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <tbody>
                                            <tr>
                                                <th>Company Code</th>
                                                <td>{{$company->company_code}}</td>
                                            </tr>
                                            <tr>
                                                <th>Company Name</th>
                                                <td>{{$company->name}}</td>
                                            </tr>
                                            <tr>
                                                <th>Address</th>
                                                <td>{{$company->address}}</td>
                                            </tr>
                                            <tr>
                                                <th>Contact No</th>
                                                <td>{{$company->contact_no}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="floatRight">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <tbody>
                                            <tr>
                                                <th>Current Nazim</th>
                                                <td>{{!empty($company->nazim) ? $company->nazim->emp_name : '-'}}</td>
                                            </tr>
                                            <tr>
                                                <th>Current Naib Nazim</th>
                                                <td>{{!empty($company->naibnazim) ? $company->naibnazim->emp_name : '-'}}</td>
                                            </tr>
                                            <tr>
                                                <th>Current Moavin</th>
                                                <td>{{!empty($company->moavin) ? $company->moavin->emp_name : '-'}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <form method="POST" action="{{ route('companies.addSchoolAdditionalDetail') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <input type="hidden" name="company_id" id="company_id" value="{{$company->id}}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <label>Nazim e Talimat</label>
                        <select class="form-control select2" required name="nazim_id" id="nazim_id">
                            <option value="0">Select Option</option>
                            @foreach (CommonHelper::get_all_employees_two(3,1,$company->id) as $row)
                                <option value="{{ $row->id }}">{{ $row->emp_name }}</option>
                            @endforeach
                        </select>
                        @error('nazim_id')
                            <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <label>Naib Nazim</label>
                        <select class="form-control select2" required name="naib_nazim_id" id="naib_nazim_id">
                            <option value="0">Select Option</option>
                            @foreach (CommonHelper::get_all_employees_two(4,1,$company->id) as $row)
                                <option value="{{ $row->id }}">{{ $row->emp_name }}</option>
                            @endforeach
                        </select>
                        @error('naib_nazim_id')
                            <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <label>Muavin Nazim</label>
                        <select class="form-control select2" required name="moavin_id" id="moavin_id">
                            <option value="0">Select Option</option>
                            @foreach (CommonHelper::get_all_employees_two(5,1,$company->id) as $row)
                                <option value="{{ $row->id }}">{{ $row->emp_name }}</option>
                            @endforeach
                        </select>
                        @error('moavin_id')
                            <div class="text-sm text-danger text-red-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="lineHeight">&nbsp;</div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                        <button type="submit" class="btn btn-sm btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
