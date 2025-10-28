<?php
$accType = Auth::user()->acc_type;
$m;
$current_date = date('Y-m-d');
$currentMonthStartDate = isset($_GET['fromDate']) ? $_GET['fromDate'] : date('Y-m-01');
$currentMonthEndDate =  isset($_GET['toDate']) ? $_GET['toDate'] :date('Y-m-t');
?>

@extends('layouts.default')

@section('content')
<script src="{{ URL::asset('assets/custom/js/customMainFunction.js') }}"></script>
    <div class="well_N">
        <div class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
                        <?php echo CommonFacades::displayPrintButtonInBlade('printPurchaseOrderVoucherList', '', '1'); ?>
                        <?php echo CommonFacades::displayExportButton('purchaseOrderVoucherList', '', '1'); ?>
                    </div>
                    <div class="lineHeight">&nbsp;</div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="well">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <span class="subHeadingLabelClass">Petty Cash Report</span>
                                </div>
                            </div>
                            <div class="lineHeight">&nbsp;</div>
                            <form action="{{ url('store/report/pettycash') }}" method="get">
                                
                                <input type="hidden" name="tbodyId" id="tbodyId" value="filterPurchaseOrderVoucherList"
                                    readonly="readonly" class="form-control" />
                                <input type="hidden" name="m" id="m" value="<?php echo $m; ?>"
                                    readonly="readonly" class="form-control" />
                                <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo url('/'); ?>"
                                    readonly="readonly" class="form-control" />
                                <input type="hidden" name="pageType" id="pageType" value="0" readonly="readonly"
                                class="form-control" />
                                <input type="hidden" name="filterType" id="filterType" value="2" readonly="readonly"
                                    class="form-control" />

                                <div class="row">
                                    <!-- <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <label>Supplier</label>
                                        <select name="filterSupplierId" id="filterSupplierId" class="form-control">
                                            <?php echo SelectListFacades::getSupplierList($m,0, isset($_GET['filterSupplierId']) ? $_GET['filterSupplierId'] : 0);?>
                                        </select>
                                    </div> -->
                                    <!-- <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <label>Invoice No.</label>
                                        <input class="form-control" type="text" name="invoiceNumber" value="{{ isset($_GET['invoiceNumber']) ? $_GET['invoiceNumber'] : old('invoiceNumber')}}">
                                    </div> -->
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <label>From Date</label>
                                        <input type="Date" name="fromDate" id="fromDate" value="<?php echo $currentMonthStartDate; ?>"
                                            class="form-control" />
                                    </div>

                                    
                                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <label>To Date</label>
                                        <input type="Date" name="toDate" id="toDate" value="<?php echo $currentMonthEndDate; ?>"
                                            class="form-control" />
                                    </div>
                                    <!-- <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                        <label>Select Voucher Status</label>
                                        <select name="selectVoucherStatus" id="selectVoucherStatus" class="form-control">
                                            <?php echo CommonFacades::voucherStatusSelectList(); ?>
                                        </select>
                                    </div> -->

                                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 text-right">
                                        <input type="submit" value="View Filter Data" class="btn btn-sm btn-danger"
                                            style="margin-top: 32px;" />
                                    </div>
                                </div>
                            </form>
                            
                            <div class="lineHeight">&nbsp;</div>
                            <div id="printPurchaseOrderVoucherList">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="panel">
                                            <div class="panel-body">
                                                <?php echo CommonFacades::headerPrintSectionInPrintView($m); ?>
                                                <div class="row">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" id="purchaseOrderVoucherList">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">S.No</th>
                                                                        <th class="text-center">PR #</th>
                                                                        <th class="text-center">Invoice #</th>
                                                                        <th class="text-center">Date.</th>
                                                                        
                                                                        <th class="text-center">Description</th>
                                                                        <th class="text-center">Unit</th>
                                                                        <!-- <th class="text-center">INVOICE Qty</th> -->
                                                                        <th class="text-center">Qty</th>
                                                                        <th class="text-center">Rate</th>
                                                                        <th class="text-center">Amount</th>
                                                                        <th class="text-center">Department</th>                                                                                                                                                                                                           
                                                                        <th class="text-center">Project</th>
                                                                        <th class="text-center">Supplier</th>
                                                                        <th class="text-center">PO #</th>    
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tbody>   
                                                                 @php
                                                                 $i =1 ;
                                                                @endphp
                                                                    @foreach($stocks as $stock)
                                                                    <tr>
                                                                        <td>{{$i++}}</td>
                                                                        <td>{{$stock->purchase_request_no}}</td>
                                                                        <td>-</td>
                                                                        <td>{{$stock->purchase_order_date}}</td>
                                                                        @php 
                                                                        $subitem=App\Models\Subitem::where('id',$stock->sub_item_id)->first();
                                                                        if(!empty($subitem)){
                                                                            $item=$subitem->sub_ic;
                                                                            $uom=App\Models\UOM::select('uom_name')->where('id',$subitem->uom)->value('uom_name');
                                                                        }else{
                                                                            $item='-';
                                                                            $uom='-';
                                                                        }
                                                                        @endphp
                                                                        <td>{{ $item }}</td>
                                                                        
                                                                        <td>{{ $uom }}</td>
                                                                        <td>{{$stock->purchase_order_qty}}</td>
                                                                        <td>{{$stock->unit_price}}</td>
                                                                        <td>{{$stock->sub_total}}</td>
                                                                        <td>{{$stock->sub_department_name}}</td>
                                                                        <td>{{$stock->project_name}}</td>
                                                                        <td>{{$stock->name}}</td>
                                                                        <td>{{$stock->purchase_order_no}}</td>
                                                                        
                                                                    </tr>
                                                                   
                                                                    @endforeach
                                                                    
                                                                    @foreach($vouchers as $stock)
                                                                    <tr>
                                                                        <td>{{$i++}}</td>
                                                                        <td>-</td>
                                                                        <td>-</td>
                                                                        <td>{{$stock->expense_date}}</td>
                                                                        
                                                                        <td>{{ $stock->description }}</td>
                                                                        
                                                                        <td>-</td>
                                                                        <td>{{$stock->qty}}</td>
                                                                        <td>{{$stock->expense_amount}}</td>
                                                                        <td>{{($stock->expense_amount/$stock->qty)}}</td>
                                                                        <td>{{$stock->sub_department_name}}</td>
                                                                        <td>{{$stock->project_name}}</td>
                                                                        <td>{{$stock->name}}</td>
                                                                        <td>{{$stock->ev_no}}</td>
                                                                        
                                                                    </tr>
                                                                   
                                                                    @endforeach
                                                                                                                                        
                                                                </tbody>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- <script src="{{ URL::asset('assets/custom/js/customStoreFunction.js') }}"></script> --}}
@endsection
