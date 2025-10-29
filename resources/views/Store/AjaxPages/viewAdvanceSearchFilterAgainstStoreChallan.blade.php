<?php
$counter = 1;
$currentDateTime = date('Y-m-d H:i:s');
$startRecordNo = Input::get('startRecordNo');
$endRecordNo = Input::get('endRecordNo');
$m;
$data = '';
?>
<input type="hidden" name="functionName" id="functionName" value="stdc/filterStoreChallanVoucherList" readonly="readonly"
    class="form-control" />
<input type="hidden" name="tbodyId" id="tbodyId" value="filterStoreChallanVoucherList" readonly="readonly"
    class="form-control" />
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-bordered sf-table-list" id="storeChallanVoucherList">
                <thead>
                    <tr>
                        <th class="text-center">S.No</th>
                        <th class="text-center">Location</th>
                        <th class="text-center">Department / Sub Department</th>
                        <th class="text">Project</th>
                        <th class="text-center">M.R.No.</th>
                        <th class="text-center">M.R.Date</th>
                        <th class="text-center">S.C.No.</th>
                        <th class="text-center">S.C.Date</th>
                        <th class="text-center">Remarks</th>
                        <th class="text-center">Created Detail</th>
                        <th class="text-center">S.C.Status</th>
                        <th class="text-center">Approve Detail</th>
                        <th class="text-center">Receive Detail</th>
                        <th class="text-center hidden-print">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($filterStoreChallanVoucherList as $row) {
                        $paramOne = 'stdc/viewStoreChallanVoucherDetail';
                        $paramTwo = $row->store_challan_no;
                        $paramThree = 'View Store Challan Voucher Detail';
                        $paramFour = 'store/editStoreChallanVoucherForm';
                    
                        $materialRequestCreatedDetail = '<span class="btn btn-xs btn-success">' . $row->username . '<br />' . CommonFacades::changeDateFormat($row->date) . '<br />' . $row->time . ' - ';
                        $createdDateTime = $row->date . ' ' . $row->time;
                        $materialRequestCreatedDetail .= CommonFacades::displayDateTimeDifference($createdDateTime, $currentDateTime, 'd') . '</span>';
                    
                        if ($row->store_challan_status == 2 || ($row->store_challan_status == 4 && $row->status == 1)) {
                            if ($row->approve_date == '0000-00-00') {
                                $materialRequestApprovedDetail = '<span class="btn btn-xs btn-success">' . $row->username . '<br />' . CommonFacades::changeDateFormat($row->date) . '<br />' . $row->time . ' - ';
                                $approvedDateTime = $row->date . ' ' . $row->time;
                                $materialRequestApprovedDetail .= CommonFacades::displayDateTimeDifference($approvedDateTime, $currentDateTime, 'd') . '</span>';
                            } else {
                                $materialRequestApprovedDetail = '<span class="btn btn-xs btn-success">' . $row->approve_username . '<br />' . CommonFacades::changeDateFormat($row->approve_date) . '<br />' . $row->approve_time . ' - ';
                                $approvedDateTime = $row->approve_date . ' ' . $row->approve_time;
                                $materialRequestApprovedDetail .= CommonFacades::displayDateTimeDifference($approvedDateTime, $currentDateTime, 'd') . '</span>';
                            }
                        } else {
                            $materialRequestApprovedDetail = '<span class="btn btn-xs btn-danger">-</span>';
                        }
                        if ($row->store_challan_status == 4 && $row->status == 1) {
                            $materialRequestRecievedDetail = '<span class="btn btn-xs btn-reciver" style="background-color: orange !important;">' . $row->receiver_username . '<br />' . CommonFacades::changeDateFormat($row->receiver_date) . '<br />' . $row->receiver_time . ' - ';
                            $RecivedDateTime = $row->receiver_date . ' ' . $row->receiver_time;
                            $materialRequestRecievedDetail .= CommonFacades::displayDateTimeDifference($RecivedDateTime, $currentDateTime, 'd') . '</span>';
                            // if($row->receiver_date == '0000-00-00'){
                            // 	$materialRequestRecievedDetail = '<span class="btn btn-xs btn-danger">'.$row->receiver_username.'<br />'.CommonFacades::changeDateFormat($row->date).'<br />'.$row->time.' - ';
                            // 	$approvedDateTime = $row->date.' '.$row->time;
                            // 	$materialRequestRecievedDetail .= CommonFacades::displayDateTimeDifference($approvedDateTime,$currentDateTime,'d').'</span>';
                            // }else{
                            // 	$materialRequestRecievedDetail = '<span class="btn btn-xs btn-danger">'.$row->receiver_username.'<br />'.CommonFacades::changeDateFormat($row->receiver_date).'<br />'.$row->receiver_time.' - ';
                            // 	$approvedDateTime = $row->receiver_date.' '.$row->receiver_time;
                            // 	$materialRequestRecievedDetail .= CommonFacades::displayDateTimeDifference($approvedDateTime,$currentDateTime,'d').'</span>';
                            // }
                        } else {
                            $materialRequestRecievedDetail = '<span class="btn btn-xs btn-danger">-</span>';
                        }
                    
                        $data .= '<tr><td class="text-center">' . $counter++ . '</td>';
                        $data .= '<td>' . CommonFacades::displayValueByIdUsingCache('cacheLocation', 'location', $row->location_id, 'location_name') . '</td>';
                        $data .= '<td>' . CommonFacades::displayValueByIdUsingCache('cacheDepartment', 'department', $row->department_id, 'department_name') . ' / ' . CommonFacades::displayValueByIdUsingCache('cacheSubDepartment', 'sub_department', $row->sub_department_id, 'sub_department_name') . '</td>';
                        $data .= '<td>' . CommonFacades::displayValueByIdUsingCache('cacheProject', 'project', $row->project_id, 'project_name') . '</td>';
                        $data .= '<td class="text-center">' . $row->material_request_no . '</td>';
                        $data .= '<td class="text-center">' . CommonFacades::changeDateFormat($row->material_request_date) . '</td>';
                        $data .= '<td class="text-center">' . $row->store_challan_no .'</td>';
                        $data .= '<td class="text-center">' . CommonFacades::changeDateFormat($row->store_challan_date) . '</td>';
                        $data .= '<td>' . $row->description . '</td>';
                        $data .= '<td class="text-center">' . $materialRequestCreatedDetail . '</td>';
                        $data .= '<td class="text-center">' . PurchaseFacades::checkVoucherStatus($row->store_challan_status, $row->status) . '</td>';
                        $data .= '<td class="text-center">' . $materialRequestApprovedDetail . '</td>';
                        $data .= '<td class="text-center">' . $materialRequestRecievedDetail . '</td>';
                        $data .= '<td class="text-center hidden-print"><div class="dropdown"><button class="btn btn-xs dropdown-toggle theme-btn" type="button" data-toggle="dropdown">Action  <span class="caret"></span></button><ul class="dropdown-menu">';
                    
                        $data .= '<li><a onclick="showDetailModelOneParamerter(\'' . $paramOne . '\',\'' . $paramTwo . '\',\'' . $paramThree . '\')"><span class="glyphicon glyphicon-eye-open"></span> View</a></li>';
                        //$data.=StoreFacades::displayMaterialRequestVoucherListButton($m,$row->material_request_status,$row->status,$row->material_request_no,'material_request_no','material_request_status','status','material_request','material_request_data',$paramFour,'Material Request Voucher Edit Detail Form', '', '');
                        $data .= '</ul></div></td></tr>';
                    }
                    ?>

                    <?php
                    echo $data;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(function() {
        $("select").select2();
    });

    function updateRecordLimitStoreChallanList(paramOne, paramTwo) {
        $('#startRecordNo').val(paramOne);
        $('#endRecordNo').val(paramTwo);
        viewRangeWiseDataFilter();
    }
</script>
<script src="{{ URL::asset('assets/custom/js/customStoreFunction.js') }}"></script>
