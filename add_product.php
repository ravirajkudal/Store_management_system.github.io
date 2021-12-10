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

		if($_POST)
		{
			if(isset($_POST['oper']))
			{	
				$user_id = $_SESSION['user_id'];

				if(isset($_POST['txt_product_id']))
				{
					$product_id = $_POST['txt_product_id'];
				}
				if(isset($_POST['txt_product_name']))
				{
					$product_name = $_POST['txt_product_name'];
				}
				if(isset($_POST['txt_price']))
				{
					$product_price = $_POST['txt_price'];
				}
				if(isset($_POST['txt_quantity']))
				{
					$product_quantity = $_POST['txt_quantity'];
				}
				if(isset($_POST['txt_discount']))
				{
					$product_discount = $_POST['txt_discount'];
				}

				if($_POST['oper']=='grid')
				{
					$grid='';
					$sql="SELECT * from product_master where is_delete=0 AND user_id = '$user_id'";
					$rs=mysqli_query($db,$sql);
					if(!$rs){					
						echo $grid="No Records...";
					}
					else{
						$grid.="<table id='datatable-responsive' class='table table-striped table-bordered dt-responsive nowrap' cellspacing='0'>
								<thead>
									<tr>
										<th class='text-center'>
											Search
										</th>
										<th class='text-center'>
											Product Name
										</th>
										<th class='text-center'>
											Product Price
										</th>
										<th class='text-center'>
											Quantity
										</th>
										<th class='text-center'>
											Discount
										</th>
										
									</tr>
								</thead>";
						while($row=mysqli_fetch_assoc($rs)){	
						
							$grid.="<tr>";
							$grid.="<p></p>";
							$grid.="<td ><div id='id_".$row['product_id']."'  class='myRec  text-center' recid='".$row['product_id']."'  onClick='searchRecord(this.id);'>Select</div></td>";
							$grid.="<td class=' text-center'>".$row['product_name']."</td>";		
							$grid.="<td class=' text-center'>".$row['product_price']."</td>";		
							$grid.="<td class=' text-center'>".$row['product_quantity']."</td>";		
							$grid.="<td class=' text-center'>".$row['product_discount']."</td>";	
							$grid.="</tr>";									
						}
						echo $grid.="</table>";
					}
				}
				else if($_POST['oper']=='insert')
				{
					$sql="SELECT product_name FROM product_master WHERE product_name='$product_name' AND user_id='$user_id' AND is_delete=0";
					$rs=mysqli_query($db,$sql);
					if($rs->num_rows)
					{					
						echo "1";
					}
					else
					{
						$sql = "INSERT INTO product_master (product_name,product_price,product_quantity,product_discount,user_id) VALUES ('$product_name','$product_price','$product_quantity','$product_discount','$user_id')";
						mysqli_query($db,$sql);
					}
				}
				else if($_POST['oper']=='update')
				{
					$sql="SELECT product_name FROM product_master WHERE product_name='$product_name' AND user_id='$user_id' AND product_id!=$product_id AND is_delete=0";
					$rs=mysqli_query($db,$sql);
					if($rs->num_rows)
					{					
						echo "1";
					}
					else
					{
						$sql = "UPDATE product_master SET product_name='$product_name',product_price='$product_price',product_quantity='$product_quantity',product_discount='$product_discount' WHERE product_id = $product_id";
						mysqli_query($db,$sql);
					}
					
				}
				else if($_POST['oper']=='delete')
				{
					$sql = "UPDATE product_master SET is_delete=1 WHERE product_id = $product_id";
					mysqli_query($db,$sql);
				}
				else if($_POST['oper']=='search')
				{
					$sql="SELECT * FROM product_master WHERE is_delete=0 AND product_id=".$product_id;
					$rs=mysqli_query($db,$sql);
					if(!$rs){					
						echo '0';
					}
					else{
						
						if($row=mysqli_fetch_assoc($rs)){		
							$product_id = $row['product_id'];
							$product_name = $row['product_name'];
							$product_price = $row['product_price'];
							$product_quantity = $row['product_quantity'];
							$product_discount = $row['product_discount'];
							
							$data = array('product_id' => $row['product_id'], 'product_name' => $row['product_name'],'product_price' => $row['product_price'],'product_quantity' => $row['product_quantity'],'product_discount' => $row['product_discount']);
							echo json_encode($data);				
						}
					}
				}
			}
		}
		else
		{
			include("header.php");
?>

	<style>	
		.myRec{
			
			cursor:pointer;
			color:#49BF4C;
		}
	</style>
	
	<script lang="javascript" type="text/javascript">

		function searchRecord(id)
		{
				
			var recid = $('#'+id).attr('recid');
			$.ajax({
				url:"add_product.php",
				type:"post",
				data:{'oper':'search','txt_product_id':recid},
				success: function (response) {	
					if(response!='0')
					{
						var data = JSON.parse(response);
						$('#txt_product_id').val(data.product_id);
						$('#txt_product_name').val(data.product_name);
						$('#txt_price').val(data.product_price);
						$('#txt_quantity').val(data.product_quantity);
						$('#txt_discount').val(data.product_discount);
						$('#btn_save').val('Update');
						$('#btn_delete').removeAttr('disabled');
					}
					else
					{
						swal("Message","Record Not Found","error");
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					swal("Message","Record Not Found","error");
				}	
			});
		}
		
		$('document').ready(function(){
			fillGrid();
			
			$('#btn_save').click(function(){

				if(valCheck()==true)
				{					
					if($('#btn_save').val()=="Save")
					{
						/* loginPassword=Math.random().toString().substr(2,7);
						loginPassword= "Sh" + loginPassword; */
							$.ajax({
							url:"add_product.php",
							type:"post",
							data:{'oper':'insert','txt_product_name':$('#txt_product_name').val(),'txt_price':$('#txt_price').val(),'txt_quantity':$('#txt_quantity').val(),'txt_discount':$('#txt_discount').val()},
							success: function (response) {	
								if(response==1)
								{
									$('#txt_product_name').val("");
									$('#txt_product_name').focus();
									swal("Message","Record Already Exist","warning");
								}		
								else
								{
									swal("Message","Record Saved Successfully","success");
									fillGrid();
									clearControl();
								}
							},
							error: function(jqXHR, textStatus, errorThrown) {
							   swal("Message","Record Not Saved","error");
							}
						});
						
					}
					else
					{
						$.ajax({
							url:"add_product.php",
							type:"post",
							data:{'oper':'update','txt_product_id':$('#txt_product_id').val(),'txt_product_name':$('#txt_product_name').val(),'txt_price':$('#txt_price').val(),'txt_quantity':$('#txt_quantity').val(),'txt_discount':$('#txt_discount').val()},
							success: function (response) {	
								if(response==1)
								{
									$('#txt_product_name').val("");
									$('#txt_product_name').focus();
									swal("Message","Record Already Exist","warning");
								}
								else
								{
									swal("Message","Record Updated Successfully","success");
									clearControl();
									fillGrid();
								}
							},
							error: function(jqXHR, textStatus, errorThrown) {
							   swal("Message","Record Not Updated","error");
							}
						});
					}
				}
			});
			
			$('#btn_delete').click(function(){
				if($('#txt_product_id').val()!="")
				{
					if(confirm("Do you want to Delete?"))
					{
						$.ajax({
								url:"add_product.php",
								type:"post",
								data:{'oper':'delete','txt_product_id':$('#txt_product_id').val()},
								success: function (response) {							   
									fillGrid();
									swal("Message","Record Deleted Successfully","success");
								},
								error: function(jqXHR, textStatus, errorThrown) {
								   swal("Message","Record Not Deleted","error");
								}
							});
					}
					clearControl();	
				}
				else
				{
					swal("Message","Please Select a Record","warning");
				}	
							
			});
			
			$('#btn_cancel').click(function(){
				clearControl();
			});
			
			$("#txt_price").on("input", function(){
				if($("#txt_price").val()==0)
				{
					$("#txt_price").val("");
				}
			});

			/*$("#txt_discount").on("input", function(){
				if($("#txt_discount").val()==0)
				{
					$("#txt_discount").val("");
				}
			});*/

			$("#txt_discount").focusout(function(){
				if($("#txt_price").val().trim()!="")
				{
					if(parseInt($("#txt_discount").val().trim()) >= parseInt($("#txt_price").val().trim()))
					{
						$("#txt_discount").val("");
						$("#txt_discount").focus();
						swal("Message","The discount amount should not be equal or greater than the product price","warning");
					}
				}
			});
		});
		
		function fillGrid()
		{
			$.ajax({
				url:"add_product.php",
				type:"post",
				data:{'oper':'grid'},
				success: function (response) {		
					console.log(response);	           
					$('.gridCont').html(response);
		        },
		        error: function(jqXHR, textStatus, errorThrown) {
		           swal("Message",'Error Occured',"error");     
		        }
			});				
		}
		
		function valCheck()
		{
			var b = false;
						
			if($('#txt_product_name').val()=="")
			{
				$('#txt_product_name').focus();
				swal("Message","Please Enter Product Name","warning");
			}
			else if($('#txt_price').val()=="")
			{
				$('#txt_price').focus();
				swal("Message","Please Enter Product Price","warning");
			}
			else if($('#txt_quantity').val()=="")
			{
				$('#txt_quantity').focus();
				swal("Message","Please Enter Quantity","warning");
			}
			else if($('#txt_discount').val()=="")
			{
				$('#txt_discount').focus();
				swal("Message","Please Enter Discount","warning");
			}
			else
			{
				b = true;
			}
			return b;
		}
		
		function clearControl()
		{
			$('#txt_product_name').focus();
			$('#txt_product_id').val("");
			$('#txt_product_name').val("");
			$('#txt_price').val("");
			$('#txt_quantity').val("");
			$('#txt_discount').val("");
			$('#btn_save').val('Save');
			$("#btn_delete").attr("disabled","disabled")
		}
		
		function isNumber(evt) {
	
			var iKeyCode = (evt.which) ? evt.which : evt.keyCode
			if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57) || iKeyCode == 46 || ($('#txt_mobile_no').val().length>=10 && iKeyCode != 8 && iKeyCode != 9))
			{
				return false;
			}
			return true;
		}

		function isText(evt){
			var iKeyCode = (evt.which) ? evt.which : evt.KeyCode
			if((iKeyCode >= 65 && iKeyCode <=90) || (iKeyCode >= 97 && iKeyCode <= 122) || iKeyCode == 8 || iKeyCode == 32 || iKeyCode ==9)
			{
				return true;
			}
			return false;
		}
	</script>

<div class="right_col" role="main" style="min-height: 2951px;">
	<div class="">
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12" >
			
				<!-- form design -->
				<div class="x_panel" style="">
					<div class="x_title">
						<h2>Product </h2>
						<ul class="nav navbar-right panel_toolbox">
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
							</li>
						</ul>
						<div class="clearfix"></div>
					</div>
					<div class="x_content">
						<div class="container">
							<div class="row">
								<div class="x_content">
									<br/>
									<form id="frm_color_insert" data-parsley-validate class="form-horizontal form-label-left" style="width:50%;margin-left:25%;" method="post">

										<input class="form-control"  placeholder="" id="txt_product_id" name="txt_courses_id" type="hidden">

										<label>Product Name</label>
										<div class="form-group">
											<div>
												<input class="form-control" placeholder="Product Name" id="txt_product_name" name="txt_product_name" type="text" autofocus>
											</div>
										</div>
										<label>Product Price</label>
										<div class="form-group">
											<div>
												<input class="form-control" placeholder="Product Price" id="txt_price" name="txt_price" type="text" onkeypress="return isNumber(event);">
											</div>
										</div>
										<label>Quantity</label>
										<div class="form-group">
											<div>
												<input class="form-control" placeholder="Quantity" id="txt_quantity" name="txt_quantity" type="text" onkeypress="return isNumber(event);">
											</div>
										</div>
										<label>Discount (INR)</label>
										<div class="form-group">
											<div>
												<input class="form-control" placeholder="Discount (INR)" id="txt_discount" name="txt_discount" type="text" onkeypress="return isNumber(event);">
											</div>
										</div>
										<div class="ln_solid"></div>
										<div class="form-group">
											<center>
												<div>
													<input type="button" class="btn btn-success" id="btn_save" name="btn_save" value="Save">
													<input type="button" class="btn  btn-danger" id="btn_delete" name="btn_delete" value="Delete" style="margin-left:10px;" disabled>
													<input type="button" class="btn  btn-primary" id="btn_cancel" name="btn_cancel" value="Cancel" style="margin-left:10px;">
												</div>
											</center>
										</div>
									</form>
									<div class="ln_solid"></div>

									<div class="gridCont">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /form design -->
			</div>
		</div>
	</div>
</div>

<?php
	include("footer.php");
	}
}
?>