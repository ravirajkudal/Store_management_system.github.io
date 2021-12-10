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

	$sql="SELECT * FROM product_master WHERE is_delete=0 AND user_id = '$user_id' ";
	$product_result = mysqli_query($db,$sql);
	$total_product_count = $product_result->num_rows;

	$sql="SELECT * FROM sales_master WHERE user_id = '$user_id'";
	$sales_result = mysqli_query($db,$sql);
	$total_bill_count = $sales_result->num_rows;

	include('header.php');

?>

	<div class="main_container">

	<!-- page content -->
	<div class="right_col" role="main" style="min-height: 1381px;">
	  <div class="">
	    <div class="row top_tiles">
	      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
	        <a href="add_product.php"><div class="tile-stats">
	          <div class="icon"><i class="fa fa-sort-amount-desc"></i></div>
	          <div class="count"><?php echo $total_product_count;?></div>
	          <h3>Total Product</h3>
	          <p></p>
	        </div>
	      </div></a>
	      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
	        <a href="bill_list.php"><div class="tile-stats">
	          <div class="icon"><i class="fa fa-check-square-o"></i></div>
	          <div class="count"><?php echo $total_bill_count;?></div>
	          <h3>Total Bill</h3>
	          <p></p>
	        </div></a>
	      </div>
	    </div>

	    <div class="row">
	      <div class="col-md-12">
	        <div class="x_panel">
	          <div class="x_title">
	            <h2>Product Details</h2>
	            <div class="filter">
                    <a href="add_product.php" type="button" id="btn_back" class="btn btn-primary pull-right">Add Product</a>
                </div>
	            <div class="clearfix"></div>
	          </div>
	          <div class="x_content">
	          	<table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0">
					<thead>
						<tr>
							<th>Product Id</th>
							<th>Product Name</th>
							<th>Product Price</th>
							<th>Quantiy</th>
							<th>Discount</th>
						</tr>
					</thead>
					<?php
						while ($row=mysqli_fetch_assoc($product_result)) {
							$grid.="<tr>";
							
							$grid.="<td>".$row['product_id']."</td>";		
							$grid.="<td>".$row['product_name']."</td>";		
							$grid.="<td>".$row['product_price']."</td>";		
							$grid.="<td>".$row['product_quantity']."</td>";		
							$grid.="<td>".$row['product_discount']."</td>";	
							$grid.="</tr>";
						}

						echo $grid;
					?>
				</table>
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
?>