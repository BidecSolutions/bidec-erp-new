@php
    use App\Helpers\CommonHelper;
    $counter = 1;
    $data = [
        'type' => 1,
        'id' => $materialRequest->id,
        'status' => $materialRequest->status,
        'voucher_type_status' => $materialRequest->material_request_status,
    ];
@endphp
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonHelper::displayPrintButtonInBlade('PrintMaterialRequestDetail', '', '1'); ?>
        <?php echo CommonHelper::getButtonsforMaterialRequestAndStoreChallanVouchers($data);?>
    </div>
</div>

<div class="lineHeight">&nbsp;</div>
<div class="well">
    <div class="row" id="PrintmaterialRequestDetail">
        <style>
            .floatLeft{
                width: 45%;
                float: left;
            }
            .floatRight{
                width: 45%;
                float: right;
            }
        </style>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="floatLeft">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>MR No.</th>
                                <td>{{ $materialRequest->material_request_no }}</td>
                            </tr>
                            <tr>
                                <th>MR Date</th>
                                <td>{{ $materialRequest->material_request_date }}</td>
                            </tr>
                            <tr>
                                <th>Department</th>
                                <td>{{ $materialRequest->department ?? 'No Department specified' }}</td>
                            </tr>
                        
                        </tbody>
                    </table>
                </div>
            </div>
           
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>Description</label>
            <p>{{ $materialRequest->main_description }}</p>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <th class="text-center">Product Name</th>
                            <th class="text-center">Quantity</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQty = 0;
                        @endphp
                        @foreach ($materialRequestDetails as $key => $row)
                            @php
                                $totalQty += $row->qty ?? 0;
                            @endphp
                            <tr>
                                <td>
                                    {{ $row->product_name ?? 'N/A' }} -
                                    {{ isset($row->product_variant_amount) ? number_format($row->product_variant_amount, 2) : '0.00' }}
                                </td>
                                <td class="text-center">{{ $row->qty ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                             <th colspan="3">Total</th>
                             <th class="text-right">
                                {{ $totalQty }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
