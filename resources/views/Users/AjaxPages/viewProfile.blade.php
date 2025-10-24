<?php

$id = $_GET['id'];
$m = CommonFacades::getSessionCompanyId();
 $userDetail = DB::Connection('mysql')->table('users')->where('id','=',$id)->first();
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
        <?php echo CommonFacades::displayPrintButtonInBlade('PrintTentativeBookingForm','','1');?>
    </div>
</div>
<div class="lineHeight">&nbsp;</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="well" id="PrintTentativeBookingForm">
            <?php echo CommonFacades::headerPrintSectionInPrintView($m);?>
            <div class="lineHeight">&nbsp;</div>         
            <div class="lineHeight">&nbsp;</div>
            <div class="panel">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
                            <div class="table-responsive">
                                <table class="table table-striped table-responsive table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Users Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th>Username:</th>
                                            <td><?php echo $userDetail->username;?></td>
										</tr>
										<tr>
											<th>Account Type</th>
                                            <td><?php echo $userDetail->acc_type; ?></td>
										</tr>
										
										<tr>
											<th>Email</th>
                                            <td><?php echo $userDetail->email;?></td>
										</tr>
                                            
                                        <tr>
											<th>CNIC No:</th>
                                            <td colspan="5"><?php echo $userDetail->cnic_no; ?></td>
										</tr>    
									</tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-5">
                            <div class="table-responsive">
                                <table class="table table-striped table-responsive table-condensed">
                                    <thead>
										<tr>
											<td>Customer Contact Detail</td>
										</tr>
                                    </thead>
                                    <tbody>
										<tr>
											<th>Mobile No's</th>
										</tr>
										<tr>
											<td><?php echo $userDetail->mobile_no; ?></td>
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
</div>
<?php

?>