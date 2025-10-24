@php
    use App\Helpers\CommonHelper;
    $loop = 2;
    $disableSelect = '';
    $hiddenOption = '';
    $allChartOfAccounts = CommonHelper::get_all_chart_of_account(1);
@endphp

@extends('layouts.layouts')

@section('content')
<div class="well_N">
    <div class="boking-wrp dp_sdw">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                {{ CommonHelper::displayPageTitle('Add New Journal Voucher') }}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right">
                <a href="{{ route('journalvouchers.index') }}" class="btn btn-success btn-xs">View List</a>
            </div>
        </div>

        <form method="POST" action="{{ route('journalvouchers.store') }}">
            @csrf

            {{-- Header Fields --}}
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <label>Voucher Type</label>
                    <select name="voucher_type" id="voucher_type" class="form-control @error('voucher_type') border border-danger @enderror select2">
                        <option value="1" {{ old('voucher_type') == 1 ? 'selected' : '' }}>Normal</option>
                    </select>
                    @error('voucher_type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <label>Journal Voucher Date <span class="text-danger">*</span></label>
                    <input type="date" name="jv_date" class="form-control @error('jv_date') border border-danger @enderror"
                        id="jv_date" value="{{ old('jv_date', date('Y-m-d')) }}" />
                    @error('jv_date')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                    <label>Slip No <span class="text-danger">*</span></label>
                    <input type="text" name="slip_no" class="form-control @error('slip_no') border border-danger @enderror"
                        id="slip_no" value="{{ old('slip_no') }}" />
                    @error('slip_no')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Description --}}
            <div class="row">
                <div class="col-lg-12">
                    <label>Description <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control @error('description') border border-danger @enderror"
                        value="{{ old('description') ?? '-' }}" />
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- JV Table --}}
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table id="buildyourform" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:200px;">Account Head <span class="text-danger">*</span></th>
                                    <th class="text-center" style="width:350px;">Description <span class="text-danger">*</span></th>
                                    <th class="text-center" style="width:125px;">Debit <span class="text-danger">*</span></th>
                                    <th class="text-center" style="width:125px;">Credit <span class="text-danger">*</span></th>
                                    <th class="text-center" style="width:100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="addMoreJvsDetailRows_1">
                                @for ($j = 1; $j <= $loop; $j++)
                                    <tr>
                                         <input type="hidden" name="jvsDataSection_1[]" class="form-control" id="jvsDataSection_1" value="<?php echo $j?>" />
                                        <td>
                                            <select class="form-control select2 account-select requiredField" required name="account_id_1_{{ $j }}" id="account_id_1_{{ $j }}" onchange="checkDuplicateAccounts()">
                                                <option value="">-- Select --</option>
                                                <?php foreach($allChartOfAccounts as $acoaRow){ ?>
                                                    <option value="{{$acoaRow->id}}">{{$acoaRow->code}} ---- {{$acoaRow->name}}</option>
                                                <?php }?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="description_1_{{ $j }}" id="description_1_{{ $j }}"
                                                class="form-control requiredField" placeholder="Description"
                                                 required value="-" />
                                        </td>
                                        <td>
                                            <input type="number" name="d_amount_1_{{ $j }}" id="d_amount_1_{{ $j }}"
                                                class="form-control d_amount_1 requiredField" min="0" step="0.01"
                                                placeholder="Debit" onfocus="mainDisable('c_amount_1_{{ $j }}','d_amount_1_{{ $j }}');"
                                                onkeyup="sum('1')" required />
                                        </td>
                                        <td>
                                            <input type="number" name="c_amount_1_{{ $j }}" id="c_amount_1_{{ $j }}"
                                                class="form-control c_amount_1 requiredField" min="0" step="0.01"
                                                placeholder="Credit" onfocus="mainDisable('d_amount_1_{{ $j }}','c_amount_1_{{ $j }}');"
                                                onkeyup="sum('1')" required />
                                        </td>
                                        <td class="text-center">---</td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>

                        {{-- Total Row --}}
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td colspan="2">Total Amount</td>
                                    <td style="width:150px;">
                                        <input type="number" readonly id="d_t_amount_1" name="d_t_amount_1"
                                            class="form-control requiredField text-right" />
                                    </td>
                                    <td style="width:150px;">
                                        <input type="number" readonly id="c_t_amount_1" name="c_t_amount_1"
                                            class="form-control requiredField text-right" />
                                    </td>
                                    <td class="text-center">---</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Add More Button --}}
                    <div class="text-right">
                        <input type="button" class="btn btn-sm btn-primary {{ $hiddenOption }}"
                            onclick="addMoreJvsDetailRows('1')" value="Add More JV's Rows" />
                    </div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="lineHeight">&nbsp;</div>
            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="reset" id="reset" class="btn btn-primary">Clear Form</button>
                    <button type="submit" class="btn btn-sm btn-success">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    let rowCount = {{ $loop + 1 }};
    const chartOfAccounts = @json($allChartOfAccounts);

    function addMoreJvsDetailRows(id) {
        const tbody = $('#addMoreJvsDetailRows_' + id);
        const rowId = rowCount++;

        let optionsHtml = '<option value="">-- Select --</option>';
        chartOfAccounts.forEach(function(acoaRow) {
            optionsHtml += `<option value="${acoaRow.id}">${acoaRow.code} ---- ${acoaRow.name}</option>`;
        });

        const row = `
        <tr id="removeJvsRows_${id}_${rowId}">
            <td>
                 <input type="hidden" name="jvsDataSection_1[]" class="form-control" id="jvsDataSection_1" value="${rowId}" />
                <select class="form-control select2 requiredField account-select" name="account_id_${id}_${rowId}" id="account_id_${id}_${rowId}" required onchange="checkDuplicateAccounts()">
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <input type="text" name="description_${id}_${rowId}" value="-" class="form-control requiredField" placeholder="Description" required />
            </td>
            <td>
                <input type="number" name="d_amount_${id}_${rowId}" id="d_amount_${id}_${rowId}" class="form-control d_amount_${id} requiredField" min="0" step="0.01"
                    placeholder="Debit" onfocus="mainDisable('c_amount_${id}_${rowId}','d_amount_${id}_${rowId}');"
                    onkeyup="sum('${id}')" required />
            </td>
            <td>
                <input type="number" name="c_amount_${id}_${rowId}" id="c_amount_${id}_${rowId}" class="form-control c_amount_${id} requiredField" min="0" step="0.01"
                    placeholder="Credit" onfocus="mainDisable('d_amount_${id}_${rowId}','c_amount_${id}_${rowId}');"
                    onkeyup="sum('${id}')" required />
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-xs" onclick="removeJvsRows('${id}', '${rowId}')">Remove</button>
            </td>
        </tr>
        `;
        tbody.append(row);
        $('.select2').select2();
    }

    function removeJvsRows(id, counter) {
        $('#removeJvsRows_' + id + '_' + counter).remove();
        sum(id);
    }

    function checkDuplicateAccounts() {
        const selectedAccounts = [];
        let hasDuplicate = false;

        $('.account-select').each(function () {
            const selected = $(this).val();
            if (selected) {
                // if (selectedAccounts.includes(selected)) {
                //     $('.select2').select2('destroy');
                //     alert('Duplicate Account Head selected. Please choose a different one.');
                //     $(this).val('');
                //     hasDuplicate = true;
                //     $('.select2').select2();
                // } else {
                //     selectedAccounts.push(selected);
                // }
            }
        });
    }

    $('form').on('submit', function (e) {
        const rowsCount = $('.account-select').length;
        if (rowsCount < 2) {
            e.preventDefault();
            alert('At least two JV rows are required.');
        }
    });

</script>
@endsection
