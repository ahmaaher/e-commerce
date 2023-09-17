<?php
// Output Buffering (Ya3ny Takhzeen .. Fa Haykhzen El Data El Awl except 'headers') Start. For not sending outputs before headers.
// It's preferable to set the 'ob_start()' before the 'session()' function.
ob_start('ob_gzhandler'); // gz is a technique to handle the outputs and compress it to speed up the preformance
session_start();
$pageTitle = "New Ad";
include "init.php";

if(isset($_SESSION['user_front'])){

	$secc_added_item = '';

	if($_SERVER['REQUEST_METHOD'] == 'POST'){

		$i_img_name		= $_FILES['item_img']['name'];
		$i_img_tmp		= $_FILES['item_img']['tmp_name'];
		$i_img_type		= $_FILES['item_img']['type'];
		$i_img_size		= $_FILES['item_img']['size'];

		$img_allowed_ex	= array("jpeg", "jpg", "png");

		$img_type_exploded 	= explode(".", $i_img_name);
		$i_img_ex			= strtolower(end($img_type_exploded));

		$item_name 		  = htmlspecialchars($_POST['name'], ENT_QUOTES);
		$item_price 	  = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
		$item_description = htmlspecialchars($_POST['description'], ENT_QUOTES);
		$item_country 	  = htmlspecialchars($_POST['country'], ENT_QUOTES);
		$item_tags	  	  = htmlspecialchars($_POST['tags'], ENT_QUOTES);
		$item_quality 	  = filter_var($_POST['quality'], FILTER_SANITIZE_NUMBER_INT);
		$item_rating 	  = filter_var($_POST['rating'], FILTER_SANITIZE_NUMBER_INT);
		$item_category 	  = filter_var($_POST['category'], FILTER_SANITIZE_NUMBER_INT);

		// ---------- start appending errors to $form_errors array ----------
		$form_errors = array();
		if(empty($item_name)||empty($item_price)||empty($item_description)||empty($item_country)||empty($item_quality)||empty($item_rating)||empty($item_rating)){
			$form_errors[] = 'You have to fill all fields';
		}
		if(strlen($item_name) < 3){ $form_errors[] = 'Username can\'t be less than 3 characters'; }
		if(strlen($item_description) > 200){ $form_errors[] = 'Description can\'t be more than 200 characters'; }
		if(empty($i_img_name)) {$form_errors[] = "You have to upload an image.";}
		if(!empty($i_img_name) && !in_array($i_img_ex, $img_allowed_ex)) {$form_errors[] = "This file cannot be uploaded, try another file type.";}
		if($i_img_size > 2097152) {$form_errors[] = "Image cannot be more than 2MB.";}
		// ---------- end appending errors to $form_errors array ----------

		if(empty($form_errors)){

			$item_img = rand(1, 1000) . "_" . $item_name . $i_img_ex;
			move_uploaded_file($i_img_tmp, "admin/uploads/item_imgs/" . $item_img);

			$item_stmt = $conn->prepare('INSERT INTO items(Name, Price, Description, Add_Date, Country, tags, img, Quality, Rating, ApprStat, Category_ID, Member_ID)
										 VALUES(:iname, :iprice, :idescription, now(), :icountry, :itags, :iimg, :iquality, :irating, 0, :icatID, :imID)'
			);
			$item_stmt->execute(array(
				'iname' 		=> $item_name,
				'iprice' 		=> $item_price,
				'idescription' 	=> $item_description,
				'icountry' 		=> $item_country,
				'itags' 		=> $item_tags,
				'iimg' 			=> $item_img,
				'iquality' 		=> $item_quality,
				'irating' 		=> $item_rating,
				'icatID' 		=> $item_category,
				'imID' 			=> $_SESSION['user_front_id'],
			));
			if($item_stmt){
				$secc_added_item = '<div class="alert alert-success">Item added successfuly.</div>';
				// here we are making refresh for the page to prevent submitting the same data again on refresh
				header("refresh:1;url=" . $_SERVER["PHP_SELF"]);
			}
		}
	}

	?>
		<div class="cr-ads">   <!-- creating New Ad -->
			<div class="container">
				<div class="panel panel-primary">
					<div class="panel-heading">My Ads</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12 col-md-8">
								<form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
									<!-- Start Name field -->
									<div class="form-group form-group-lg">
										<label class="col-sm-2 control-label">Item Name :</label>
										<div class="col-sm-10 col-md-8">
											<input 	pattern=".{3,}" title="Must contain at least 3 char" 
													data-anything=".live-name" type="text" name="name" class="form-control live" 
													autocomplete="off" placeholder="Name of the item" required/>
										</div>
									</div>
									<!-- Start Price field -->
									<div class="form-group form-group-lg">
										<label class="col-sm-2 control-label">Price :</label>
										<div class="col-sm-10 col-md-8">
											<input data-anything=".live-price" type="text" name="price" class="form-control live" placeholder="The price of the item (eg. $100)" required/>
										</div>
									</div>
									<!-- Start Description field -->
									<div class="form-group form-group-lg">
										<label class="col-sm-2 control-label">Description :</label>
										<div class="col-sm-10 col-md-8">
											<input 	pattern=".{10,}" title="Must contain at least 10 char"
													data-anything=".live-des" type="text" name="description" 
													class="form-control live" placeholder="Describe the item" required/>
										</div>
									</div>
									<!-- Start Country field -->
									<div class="form-group form-group-lg">
										<label class="col-sm-2 control-label">Country :</label>
										<div class="col-sm-10 col-md-8">
											<input type="text" name="country" class="form-control" placeholder="The country the item made in." required/>
										</div>
									</div>
									<!-- Start Tags field -->
									<div class="form-group form-group-lg">
										<label class="col-sm-2 control-label">Tags :</label>
										<div class="col-sm-10 col-md-8">
											<textarea name="tags" type="textarea" class="form-control" placeholder="Type your Tags separated by comma"></textarea>
										</div>
									</div>
									<!-- Start Image field -->
									<div class="form-group form-group-lg">
										<label class="col-sm-2 control-label">Item Image :</label>
										<div class="col-sm-10 col-md-8">
											<input type="file" name="item_img" />
										</div>
									</div>
									<!-- Start Quality field -->
									<div class="form-group form-group-lg">
										<label class="col-sm-2 control-label">Quality :</label>
										<div class="col-sm-10 col-md-8">
											<select name="quality" required>
												<option value="">...</option>
												<option value="1">Used</option>
												<option value="2">New</option>

											</select>
										</div>
									</div>
									<!-- Start Rating field -->
									<div class="form-group form-group-lg">
										<label class="col-sm-2 control-label">Rating :</label>
										<div class="col-sm-10 col-md-8">
											<select name="rating" required>
												<option value="">....</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
											</select>
										</div>
									</div>
									<!-- Start Categories field -->
									<div class="form-group form-group-lg">
										<label class="col-sm-2 control-label">Category :</label>
										<div class="col-sm-10 col-md-8">
											<select name="category">
												<option value="0">....</option>
												<?php
													$parent_cats = records('*', 'categories', "WHERE parent = 0");
													foreach($parent_cats as $parent_cat){
														echo "<option value='" . $parent_cat['ID'] . "' ";
															echo ">" . $parent_cat['Name'] . "</option>";
															
															$sec_lev_parent_cats = records('*', 'categories', "WHERE parent = {$parent_cat['ID']}");
															foreach($sec_lev_parent_cats as $sec_lev_parent_cat){
																echo "<option value='" . $sec_lev_parent_cat['ID'] . "' ";
																	echo ">- " . $sec_lev_parent_cat['Name'] . "</option>";

																$third_lev_parent_cats = records('*', 'categories', "WHERE parent = {$sec_lev_parent_cat['ID']}");
																foreach($third_lev_parent_cats as $third_lev_parent_cat){
																	echo "<option value='" . $third_lev_parent_cat['ID'] . "' ";
																		echo ">-- " . $third_lev_parent_cat['Name'] . "</option>";
																}
															}
													}
												?>
											</select>
										</div>
									</div>
									<!-- End Categories field -->
									<!-- Start Add Button field -->
									<div class="form-group form-group-lg">
										<div class="col-sm-offset-2 col-sm-8">
											<input type="submit" value="Add" class="btn btn-primary btn-lg" />
										</div>
									</div>
									<!-- End Add Button field -->
								</form>
							</div>
							<div class="col-sm-12 col-md-4">
								<div class='thumbnail item_box'>
									<img class='img-responsive' src='admin/uploads/item_imgs/default_i_img.jpg' alt='' />
									<div class='caption'>
										<h4 class='live-name'>Item name</h4>
										<h4>$<span class='live-price'>Price</span></h4>
										<p class='live-des'>Description</p>
									</div>
								</div>
							</div>
						</div>
						<?php
							if(!empty($form_errors)){
								foreach($form_errors as $item_error){
									echo '<div class="alert alert-danger">' . $item_error . '</div>';
								}
							}else{
								echo $secc_added_item;
							}
						?>
					</div>
				</div>
			</div>
		</div>
	<?php

}else{header('location: login.php'); exit();}
include $temp . "footer.php";
ob_end_flush(); // Send the output buffer and turn off output buffering.
?>
