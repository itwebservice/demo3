<?php $sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$sq_req[city_id]'")); ?>
<div class="row">
	<div class="col-md-12">
		<div class="profile_box main_block">
        	 	<h3 class="editor_title">Quotation Details</h3>
    			<div class="panel panel-default panel-body app_panel_style">
    				<div class="row mg_bt_10">
						<div class="col-md-4">
							<i class="fa fa-angle-double-right"></i>&nbsp;&nbsp;<label>Quotation For <em>:</em></label> <?= $sq_req['quotation_for'] ?>
						</div>
						<div class="col-md-4">
							<i class="fa fa-angle-double-right"></i>&nbsp;&nbsp;<label>City Name <em>:</em></label><?php
							for($i=0;$i<sizeof($city_id_arr);$i++){ 
								$sq_city = mysqli_fetch_assoc(mysqlQuery("select * from city_master where city_id='$city_id_arr[$i]'"));
								$sep = ($i<sizeof($city_id_arr)-1) ? ',' : '';
								echo $sq_city['city_name'].$sep; } ?>
						</div>
						<div class="col-md-4">
							<i class="fa fa-angle-double-right"></i>&nbsp;&nbsp;<label>Tour Type <em>:</em></label> <?= $sq_req['tour_type'] ?>
						</div>
					</div>
					<div class="row mg_bt_10">
						<div class="col-md-4">
							<i class="fa fa-angle-double-right"></i>&nbsp;&nbsp;<label>Quotation Date <em>:</em></label> <?= get_date_user($sq_req['quotation_date']) ?>
						</div>
						<div class="col-md-4">
							<i class="fa fa-angle-double-right"></i>&nbsp;&nbsp;<label>Airport Pickup <em>:</em></label> <?= $sq_req['airport_pickup'] ?>
						</div>
						<div class="col-md-4">
							<i class="fa fa-angle-double-right"></i>&nbsp;&nbsp;<label>Cab Type <em>:</em></label> <?= $sq_req['cab_type'] ?>
						</div>
					</div>
					<div class="row mg_bt_10">
						<div class="col-md-4">
						<i class="fa fa-angle-double-right"></i>&nbsp;&nbsp;<label>Transfer Type <em>:</em></label> <?= $sq_req['transfer_type'] ?>
						</div>
					</div>
					<hr/>
					<div class="row mg_bt_10">
						<?php 
						$dynamic_fields = $sq_req['dynamic_fields'];
						$dynamic_fields_arr = json_decode($dynamic_fields, true);
						foreach($dynamic_fields_arr as $dynamic_fields){
							$name = explode('_', $dynamic_fields['name']);
							$name = ucfirst(implode(' ', $name));
							if($name=="Total members"){
								$name="Total Passenger";
							}
							?>
							<div class="col-md-4 mg_bt_10">
								<i class="fa fa-angle-double-right"></i><label>&nbsp;&nbsp;<?= $name ?> <em>:</em></label> <?= $dynamic_fields['value'] ?>
							</div>
							<?php
						}
						?>
					</div>
					<div class="row mg_bt_10">
						<div class="col-md-12">
							<i class="fa fa-angle-double-right"></i>&nbsp;&nbsp;<label>Enquiry Details <em>:</em></label> <?= $sq_req['enquiry_specification'] ?>
						</div>
					</div>
					<div class="row mg_bt_10">
						<div class="col-md-12">
							<i class="fa fa-angle-double-right"></i>&nbsp;&nbsp;<label>Activity <em>:</em></label> <?= $sq_req['excursion_specification'] ?>
						</div>
					</div>
    			</div>
		   </div>
	</div>
</div>