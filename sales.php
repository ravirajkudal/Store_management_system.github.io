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
		$product_id = 0;

		include('db.php');

		$user_id = $_SESSION['user_id'];

		$sql = "SELECT * FROM product_master WHERE is_delete = 0 AND user_id='$user_id'";
		$product_result = mysqli_query($db,$sql);

		if($_POST)
		{
			if(isset($_POST['oper']))
			{
				
				if(isset($_POST['txt_product_id']))
				{
					$product_id = $_POST['txt_product_id'];
				}
				
				if(isset($_POST['product_details_array']))
				{
					$product_details_array = $_POST['product_details_array'];
				}

				if(isset($_POST['customer_name']))
				{
					$customer_name = $_POST['customer_name'];
				}

				if($_POST['oper']=='product_details')
				{
					$sql = "SELECT * FROM product_master WHERE product_id = '$product_id'";
					$result = mysqli_query($db,$sql);
					while ($row=mysqli_fetch_assoc($result)) {
						$data = array('product_price' => $row['product_price'],'product_quantity' => $row['product_quantity'],'product_discount' => $row['product_discount']);
						echo json_encode($data);
					}
				}
				else if($_POST['oper']=='save')
				{
					$grand_amount = 0;
					$grand_discount = 0;

					foreach ($product_details_array as $key) {
						$grand_amount = $grand_amount + $key['total_amount'];
						$grand_discount = $grand_discount + $key['product_discount'];
					}

					$sales_date = date("Y-m-d");
					$sql  = "INSERT INTO sales_master(customer_name,grand_amount,grand_discount,sales_date,user_id) VALUES('$customer_name','$grand_amount','$grand_discount','$sales_date','$user_id')";

					mysqli_query($db,$sql);
					$sales_master_id = $db->insert_id;

					foreach ($product_details_array as $key) {
						$product_id = $key['product_id'];
						$product_name = $key['product_name'];
						$product_price = $key['product_price'];
						$product_quantity = $key['quantity'];
						$product_discount = $key['product_discount'];
						$total_price = $key['total_amount'];
						$remaining_quantity = $key['remaining_quantity'];

						$sql  = "INSERT INTO sales_details(product_name,product_price,product_quantity,product_discount,total_price,sales_master_id) VALUES('$product_name','$product_price','$product_quantity','$product_discount','$total_price','$sales_master_id')";
						mysqli_query($db,$sql);

						$sql = "UPDATE product_master SET product_quantity='$remaining_quantity' WHERE product_id = $product_id";
						mysqli_query($db,$sql);
					}
					
				}
			}
		}
		else
		{

			include("header.php");
?>

	<style type="text/css">
		.danger{
			color: red;
		}
		.hand-cursor{
			cursor:pointer;
		}
		.btn-success{
			width: unset !important;
		}
	</style>

	<script lang="javascript" type="text/javascript">
		var product_array = {};

		$('document').ready(function(){
			
			$('#btn_submit').click(function(){

				$.ajax({
					url: "sales.php",
					type: "POST",
					data:{'oper':'save','product_details_array': product_array,'customer_name':$("#txt_customer_name").val()},
					success:function(response){
						swal("Message","Record Saved Successfully!","success");
						product_array = {};
						$("#txt_customer_name").val("");
						$("#table_product tr.tr-product").remove();
						$('#btn_submit').attr("disabled","disabled");
					}
				});
			});

			$('#btn_cancel').click(function(){
				var product_id = 1;
				console.log(product_array);
				clearControl();
				$("#btn_add_product").val("Add Product");
			});
			
			$("#txt_quantity").on("input", function(){
				if($("#txt_quantity").val()==0)
				{
					$("#txt_quantity").val("");
				}
			});

			$("#ddl_product_name").change(function(){
				var product_id = $("#ddl_product_name").val();
				if(product_id!=0)
				{	
					$.ajax({
						url: "sales.php",
						type: "POST",
						data:{'oper':'product_details','txt_product_id':product_id},
						success:function(response){
							var data = JSON.parse(response);
							$("#txt_price").val(data.product_price);
							$("#txt_stock").val(data.product_quantity);
							$("#txt_discount").val(data.product_discount);
							$("#txt_quantity").focus();
						}
					});
				}
				else
				{
					$("#txt_price").val("");
					$("#txt_stock").val("");
					$("#txt_discount").val("");
				}
				
			});

			$('#btn_add_product').click(function(){
				if(valCheck()==true)
				{
					var product_id = $("#ddl_product_name").val();
					var product_name = $("#ddl_product_name :selected").text();
					var product_stock = $("#txt_stock").val();
					var quantity = $("#txt_quantity").val();

					if(parseInt(quantity)>parseInt(product_stock))
					{
						$("#txt_quantity").val("");
						$("#txt_quantity").focus();
						swal("Message","Sales Quantity should not be greater than product stock","warning");
					}
					else
					{
						var product_price = $("#txt_price").val();
						var product_discount = $("#txt_discount").val();
						var total_amount = (parseInt(product_price) * parseInt(quantity)) - parseInt(product_discount);
						var remaining_quantity = parseInt(product_stock) - parseInt(quantity);
						if($('#btn_add_product').val()=="Add Product")
						{
							if(product_id in product_array)
							{
								swal("Message",product_name+" Already Exists In The List","warning");
							}
							else
							{
								var tableTr = "<tr id="+"tr"+product_id+" class='tr-product'>"+ "<td class='product_td text-center'>"+ product_id +"</td>"+ "<td class='product_td text-center'>"+ product_name +"</td>"+ "<td class='product_td text-center'>"+ product_price +"</td>"+ "<td class='product_td text-center'>"+ quantity +"</td>"+ "<td class='product_td text-center'>"+ product_discount +"</td>"+ "<td class='product_td text-center'>"+ total_amount +"</td>" + "<td class='product_td text-center'><i class='fa fa-edit hand-cursor' onclick='editProduct("+product_id+")'></i>&nbsp;<i class='fa fa-trash danger hand-cursor' onclick='removeProduct("+product_id+")'></i>" +"</td>" +"</tr>";

								$("#table_product").append(tableTr);
								

								product_array[product_id] = {'product_id': product_id, 'product_name': product_name, 'product_price': product_price, 'product_discount': product_discount, 'quantity': quantity ,'total_amount': total_amount, 'remaining_quantity': remaining_quantity};
								$("#btn_submit").removeAttr("disabled");
							}
						}
						else
						{
							var tableTr = "<td class='product_td text-center'>"+ product_id +"</td>"+ "<td class='product_td text-center'>"+ product_name +"</td>"+ "<td class='product_td text-center'>"+ product_price +"</td>"+ "<td class='product_td text-center'>"+ quantity +"</td>"+ "<td class='product_td text-center'>"+ product_discount +"</td>"+ "<td class='product_td text-center'>"+ total_amount +"</td>" + "<td class='product_td text-center'><i class='fa fa-edit hand-cursor' onclick='editProduct("+product_id+")'></i>&nbsp;<i class='fa fa-trash danger hand-cursor' onclick='removeProduct("+product_id+")'></i>" +"</td>";
							var trId = $("#txt_product_id").val();
							$("#tr"+trId).html(tableTr);
							product_array[product_id] = {'product_id': product_id, 'product_name': product_name, 'product_price': product_price, 'product_discount': product_discount, 'quantity': quantity ,'total_amount': total_amount, 'remaining_quantity': remaining_quantity};
							$("#btn_add_product").val("Add Product");
						}	
						clearControl();
					}
				}
			});
		});
	
		function removeProduct(paraProductId)
		{
			$("#tr"+paraProductId).remove();
			delete product_array[paraProductId];
			if($(".tr-product").length==0)
			{
				$("#btn_submit").attr("disabled","disabled");
			}
		}

		function editProduct(paraProductId)
		{
			console.log(product_array);
			$("#txt_product_id").val(paraProductId);
			var product_price = product_array[paraProductId].product_price;
			var quantity = product_array[paraProductId].quantity;
			var product_stock = parseInt(product_array[paraProductId].remaining_quantity) + parseInt(quantity);
			var product_discount = product_array[paraProductId].product_discount;
			$("#ddl_product_name").val(paraProductId);
			$("#txt_price").val(product_price);
			$("#txt_stock").val(product_stock);
			$("#txt_discount").val(product_discount);
			$("#txt_quantity").val(quantity);
			$('#btn_add_product').val("Update Product");
		}

		function valCheck()
		{
			var b = false;
						
			if($('#txt_customer_name').val().trim()=="")
			{
				$('#txt_customer_name').focus();
				swal("Message","Please Enter Customer Name","warning");
			}
			else if($('#ddl_product_name').val()==0)
			{
				$('#ddl_product_name').focus();
				swal("Message","Please Select Product Name","warning");
			}
			else if($('#txt_quantity').val()=="")
			{
				$('#txt_quantity').focus();
				swal("Message","Please Enter Quantity","warning");
			}
			else
			{
				b = true;
			}
			return b;
		}
		
		function clearControl()
		{	
			$('#ddl_product_name').focus();
			$('#ddl_product_name').val(0);
			$('#txt_stock').val("");
			$('#txt_price').val("");
			$('#txt_quantity').val("");
			$('#txt_discount').val("");
		}
		
		function isNumber(evt) {
	
			var iKeyCode = (evt.which) ? evt.which : evt.keyCode
			if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57) || iKeyCode == 46)
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
						<h2>Sales Product</h2>
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

										<input type="hidden" id="txt_product_id">

										<label>Customer Name</label>
										<div class="form-group">
											<div>
												<input class="form-control" placeholder="Customer Name" id="txt_customer_name" name="txt_customer_name" type="text" onkeypress="return isText(event);"autofocus>
											</div>
										</div>
										<label>Product Name</label>
										<div class="form-group">
											<div>
												<select class="form-control" id="ddl_product_name">
													<option value="0">-- Select --</option>
													<?php
														while ($row=mysqli_fetch_assoc($product_result)) {
															echo '<option value='.$row['product_id'].'>'.$row['product_name'].'</option>';
														}
													?>
												</select>
											</div>
										</div>
										<label>Product Price</label>
										<div class="form-group">
											<div>
												<input class="form-control" placeholder="Product Price" id="txt_price" name="txt_price" type="text" onkeypress="return isNumber(event);" readonly>
											</div>
										</div>
										<label>Product Stock</label>
										<div class="form-group">
											<div>
												<input class="form-control" placeholder="Product Stock" id="txt_stock" name="txt_stock" type="text" onkeypress="return isNumber(event);" readonly>
											</div>
										</div>
										<label>Product Discount (INR)</label>
										<div class="form-group">
											<div>
												<input class="form-control" placeholder="Discount (INR)" id="txt_discount" name="txt_discount" type="text" onkeypress="return isNumber(event);" readonly>
											</div>
										</div>
										<label>Quantity</label>
										<div class="form-group">
											<div>
												<input class="form-control" placeholder="Quantity" id="txt_quantity" name="txt_quantity" type="text" onkeypress="return isNumber(event);">
											</div>
										</div>
										<div class="ln_solid"></div>
										<div class="form-group">
											<center>
												<div>
													<input type="button" class="btn  btn-success" id="btn_add_product" name="btn_add_product" value="Add Product">
													&nbsp;
													<input type="button" class="btn  btn-primary" id="btn_cancel" name="btn_cancel" value="Cancel"> 
												</div>
											</center>
										</div>
									</form>
									<div class="ln_solid"></div>

									<div class="gridCont">
										<table border='1' cellpadding='10'  cellspacing='0' class='grid  table table-striped table-bordered table-hover dataTable no-footer My-fonts' id="table_product">
											<thead>
												<tr>
													<th class='text-center'>
														Product Id
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
													<th class='text-center'>
														Total Amount
													</th>
													<th class='text-center'>
														Action
													</th>
												</tr>
											</thead>
										</table>
										<div class="ln_solid"></div>
										<center>
											<button type="button" class="btn btn-success" id="btn_submit"  disabled="disabled">Save</button>
										</center>
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