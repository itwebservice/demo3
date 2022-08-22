


<div class="app_panel_content Filter-panel">
		<div class="row">
			<div class="col-md-3 col-sm-4 mg_bt_10_sm_xs">
			    <input type="text" id="from_date" onchange="get_to_date(this.id,'to_date');" name="from_date" class="form-control" placeholder="*From Date" title="From Date">
			</div>
			<div class="col-md-3 col-sm-4 mg_bt_10_sm_xs">
			    <input type="text" id="to_date" name="to_date" onchange="validate_validDate('from_date','to_date');" class="form-control" placeholder="*To Date" title="To Date">
			</div>	
			<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10">
				<button class="btn btn-sm btn-info ico_right" onclick="report_reflect(true)">Proceed&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>
			</div>
			<div class="col-md-3 col-sm-6 col-xs-12 mg_bt_10" style="float:right; text-align:end;">
			<button id="exportexcel" class="btn btn-sm btn-primary ico_right" onclick="exportToExcel('gpd_tour_report')">Export&nbsp;&nbsp;<i class="fa fa-arrow-right"></i></button>

		</div>
		</div>
	</div>
	

<div id="div_list" class="main_block mg_tp_20">
<div class="row mg_tp_20"> <div class="col-md-12 no-pad"> <div class="table-responsive">
<table id="gpd_tour_report" class="table table-hover" style="margin: 20px 0 !important;">         
</table>
</div></div></div>
</div>
<div id="other_agent_display">

</div>
<script>
$( "#from_date, #to_date" ).datetimepicker({ timepicker:false, format:'d-m-Y' });        

	
	function report_reflect(data){
		
		var id = 1;
		if(data != true)
		{
		var fromdate = null;
			var todate = null;
			
	}
		else
		{
		var fromdate = $('#from_date').val();
		var todate = $('#to_date').val();
				
		}
		var column = [
	{ title : "SR_NO."},
	{ title : "Branch_Name"},
	{ title : "Total Enquiries"},
	{ title : "Active"},
	{ title : "In-Followup"},
	{ title : "Dropped"},
	{ title : "Converted"},
	{ title : "Total Budget"},
	{ title : "Strong"},
	{ title : "Hot"},
	{ title : "Cold"},
	{ title : "Actions"},
];
		$.post('report_reflect/agent_wise_query_sale_report/get_report.php', {id : id, fromdate : fromdate, todate : todate}, function(data){
		// $('#div_list').html(data);
		pagination_load(data, column, true, true, 20, 'gpd_tour_report');
	});
	}
	
	report_reflect(false);


	function view_agent_qry_sale_modal(branch_id)
{
	var base_url = $('#base_url').val();
	$.post(base_url+'view/reports/analysis_reports/report_reflect/agent_wise_query_sale_report/view_agent_qry_sale_modal.php', { branch_id : branch_id}, function(data){
		$('#other_agent_display').html(data);
	});
}
</script>