<?php
if(!isset($_SESSION)){
  session_start();
} 
if(!(isset($_SESSION['user_id']))){
  echo "You have not logged in...";
  header('Refresh: 2; URL = index.php');
}
else{
	$sql = '';
	$grid = '';
	$rs = '';
	$msg = '';

	include('db.php');

	$user_id = $_SESSION['user_id'];

	$sql="SELECT * FROM sales_master WHERE user_id= '$user_id'";

	$bili_list=mysqli_query($db,$sql);

	if($_POST)
		{
			if(isset($_POST['oper']))
			{
				if($_POST['oper']=='search')
				{
					$sales_master_id = $_POST['sales_master_id'];

					$sql="SELECT sales_master.sales_master_id,sales_master.customer_name, sales_master.grand_amount, sales_master.grand_discount,sales_master.sales_date, sales_details.product_name, sales_details.product_price,sales_details.product_quantity,sales_details.product_discount,sales_details.total_price FROM sales_master INNER JOIN sales_details ON sales_master.sales_master_id = sales_details.sales_master_id WHERE sales_master.sales_master_id= '$sales_master_id'";
					
					$rs=mysqli_query($db,$sql);
					
					$today_date = date("Y-m-d");
					$row=mysqli_fetch_assoc($rs);

					$grid.= '<table width="100%">
						<tr>
							<td width="12%"><b>Customer Name:</b></td>
							<td width="23%" id="td_customer_name">'.$row['customer_name'].'</td>
							<td width="20%"><div align="right"><b>Purchase Date:</b> &nbsp;</div></td>
							<td width="20%"> '.$row['sales_date'].'</td>
							<td width="10%"><div align="right"><b>Bill Date:</b> &nbsp;</div></td>
							<td width="10%">'.$today_date.'</td>
						</tr>
					</table> <hr>
					<table width="100%">
						<tr class="line-bottom">
							<th class="table-space">Sr No.</th>
							<th class="table-space">Product Name</th>
							<th class="table-space">Price</th>
							<th class="table-space">Quantity</th>
							<th class="table-space">Discount</th>
							<th class="table-space">Total Amount</th>
						</tr>';

					$sr_no = 1;
					$result=mysqli_query($db,$sql);

					while ($salesRow=mysqli_fetch_assoc($result)) {
						$grid.='<tr>
							<td width="12%" class="table-space">'.$sr_no.'</td>
							<td width="23%" class="table-space">'.$salesRow['product_name'].'</td>
							<td width="20%" class="table-space">'.$salesRow['product_price'].'</td>
							<td width="20%" class="table-space">'.$salesRow['product_quantity'].'</td>
							<td width="10%" class="table-space">'.$salesRow['product_discount'].'</td>
							<td width="10%" class="table-space">'.$salesRow['total_price'].'</td>
						</tr>';

						$sr_no = $sr_no+1;
					}
						
					$grid.='<tr>
							<td width="12%" class="table-space"></td>
							<td width="23%" class="table-space"></td>
							<td width="20%" class="table-space"></td>
							<td width="20%" class="table-space"></td>
							<td width="10%" class="table-space"></td>
							<td width="10%" class="table-space line-top">'.$row['grand_amount'].'</td>
						</tr>';

					$grid.='</table>';

					echo $grid;
				}
			}
				
		}
		else
		{
			include('header.php');

?>
	<style>	
		.myRec{
			
			cursor:pointer;
			color:#49BF4C;
		}
		.table-space{
			padding: 7px !important;
		}
		.line-bottom{
			border-bottom: 1px solid;
		}
		.line-top{
			border-top: 1px solid;
		}
	</style>

	<script src="https://kendo.cdn.telerik.com/2020.1.219/js/kendo.all.min.js"></script>

	<script lang="javascript" type="text/javascript">
		
		$('document').ready(function(){

			//fillGrid();

			$("#btn_print").click(function(){
				var customerName = $("#td_customer_name").html();
				customerName = customerName.replace(" ", "_");
				kendo.drawing.drawDOM($("#pdfPage"))
				.then(function(group) {
					// Render the result as a PDF file
					return kendo.drawing.exportPDF(group, {
					paperSize: "auto",
						margin: { left: "1cm", top: "1cm", right: "1cm", bottom: "1cm" }
					});
				})
				.done(function(data) {
					// Save the PDF file
					kendo.saveAs({
						dataURI: data,
						fileName: customerName+".pdf"
					});
				});
			});

			$("#btn_back").click(function(){
				$("#bill_page").hide();
				$("#gridCont").show();
				$("#pageTitle").html("Bill List");
			});
		});

		function searchRecord(id)
		{
				
			var recid = $('#'+id).attr('recid');
			$.ajax({
				url:"bill_list.php",
				type:"post",
				data:{'oper':'search','sales_master_id':recid},
				success: function (response) {	
					console.log(response);
					$("#gridCont").hide();
					$("#bill_page").show();
					$('#bill').html(response);
					$("#pageTitle").html("Bill Print");
				},
				error: function(jqXHR, textStatus, errorThrown) {
					swal("Message","Record Not Found","error");
				}	
			});
		}
	
	</script>

	<div class="main_container">

	<!-- page content -->
	<div class="right_col" role="main" style="min-height: 1381px;">
	  <div class="">
	   

	    <div class="row">
	      <div class="col-md-12">
	        <div class="x_panel">
	          <div class="x_title">
	            <h2 id="pageTitle">Bill List</h2>
	            <div class="filter">
	            
	            </div>
	            <div class="clearfix"></div>
	          </div>
	          <div class="x_content">
	          	<div id="gridCont">
	          			<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0">
						<thead>
							<tr>
								<th>Customer Name</th>
								<th>Purchase Date</th>
								<th>Grand Amount</th>
							</tr>
						</thead>
						<?php
							while ($row=mysqli_fetch_assoc($bili_list)) {
								$grid.="<td ><div id='id_".$row['sales_master_id']."'  class='myRec' recid='".$row['sales_master_id']."'  onClick='searchRecord(this.id);'>".$row['customer_name']."</div></td>";
								$grid.="<td class=''>".$row['sales_date']."</td>";		
								$grid.="<td class=''>".$row['grand_amount']."</td>";
								$grid.="</tr>";		
							}

							echo $grid;
						?>
					</table>
	          	</div>
		          

				<div id="bill_page" style="display: none;">
					<div>
						<button type="button" id="btn_back" class="btn btn-primary pull-left">Back</button>
						<button type="button" id="btn_print" class="btn btn-success pull-right">Print</button>
					</div>
					<br><br>
					<div id="pdfPage">
						<center><h3>Bill</h3></center>
						<br>
						<div id="bill">
						</div>
					</div>
						
				</div>

	          </div>
	        </div>
	      </div>
	    </div>

	  </div>
	</div>
	<!-- /page content -->
	</div>
    

<?php
	include("footer.php");
	}
}
?>