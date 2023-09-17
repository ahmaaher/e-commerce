<?php

session_start();
$pageTitle = "Items";
if(isset($_SESSION["username"])) {
	include "init.php";
	
	$do = isset($_GET['do']) ? $_GET['do'] : 'main';

	if($do == "main") {				// Main page ============================

		// Getting all members into the table of the members.
		$stmt = $conn->prepare("SELECT items.*, categories.Name AS category_name, users.Username
								FROM items
								INNER JOIN categories ON categories.ID = items.Category_ID
								INNER JOIN users ON users.ID = items.Member_ID
								ORDER BY items.ID");
		$stmt->execute();
		$items_rows = $stmt->fetchAll();
		?>
			<h1 class="text-center">Manage Items</h1>
			<div class="container">
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>ID</td>
							<td>Image</td>
							<td>Name</td>
							<td>Price</td>
							<td>Country</td>
							<td>Quality</td>
							<td>Rating</td>
							<td>Insert Date</td>
							<td>Category</td>
							<td>Memeber</td>
							<td>Control</td>
						</tr>
						<?php
							foreach($items_rows as $items_row) {
								echo "<tr>";
									echo "<td>" . $items_row['ID'] . "</td>";
									echo "<td>";
										if(!empty($items_row['img'])){
											echo "<img src='uploads/item_imgs/" . $items_row['img'] . "' />";
										}else{ echo "<img src='uploads/item_imgs/default_i_img.jpg' />";}
									echo "</td>";
									echo "<td>" . $items_row['Name'] . "</td>";
									echo "<td>" . $items_row['Price'] . "</td>";
									echo "<td>" . $items_row['Country'] . "</td>";
									echo "<td>";  if($items_row['Quality'] == 1){echo 'Used';}else{echo 'New';}  echo "</td>";
									echo "<td>" . $items_row['Rating'] . "/5</td>";
									echo "<td>" . $items_row['Add_Date'] . "</td>";
									echo "<td>" . $items_row['category_name'] . "</td>";
									echo "<td>" . $items_row['Username'] . "</td>";
									echo "<td>
											<a href='items.php?do=edit&id=" . $items_row['ID'] ."' class='btn btn-success'>Edit</a>
											<a href='items.php?do=delete&id=" . $items_row['ID'] ."' class='btn btn-danger confirm'>Delete</a>";
											if($items_row['ApprStat'] == 0){
												echo "<a href='items.php?do=approve&id=" 
													 . $items_row['ID'] .
													 "'class='btn btn-info activate'><i class='fa fa-check'></i> Approve</a>";
											}
									echo "</td>";
								echo "</tr>";
							}
						?>
					</table>
				</div>
				<a href='items.php?do=add' class="btn btn-primary" style="margin-bottom: 15px"><i class="fa fa-plus"></i> Add item</a>
			</div>
		<?php
	}elseif($do == "edit") { 		// Edit page ============================

		$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

		// Check if the item with a spacific ID exists in the database
		$stmt = $conn -> prepare("SELECT * FROM items WHERE ID = ?");
		$stmt -> execute(array($id));
		$item = $stmt -> fetch();

		$checkItem = checkItem("ID", "items", $id);

		if($checkItem > 0){
			if($_SESSION['groupId'] == 1 || $_SESSION['id'] == $item['Member_ID']){
				?>
					<h1 class="text-center">Edit Item</h1>
					<div class="container">
						<form class="form-horizontal" action="?do=update" method="POST" enctype="multipart/form-data">
							<!-- Start hidden ID -->
							<input type="hidden" name="id" value="<?php echo $id; ?>" />
							<!-- End hidden ID -->
							<!-- Start Name field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Name :</label>
								<div class="col-sm-10 col-md-5">
									<input type="text" name="name" class="form-control" value="<?php echo $item['Name']; ?>" placeholder="Name of the item" required/>
								</div>
							</div>
							<!-- End Name field -->
							<!-- Start Description field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Description :</label>
								<div class="col-sm-10 col-md-5">
									<input type="text" name="description" class="form-control" value="<?php echo $item['Description']; ?>" placeholder="Describe the item" required/>
								</div>
							</div>
							<!-- End Description field -->
							<!-- Start Price field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Price :</label>
								<div class="col-sm-10 col-md-5">
									<input type="text" name="price" class="form-control" value="<?php echo $item['Price']; ?>" placeholder="The price of the item (eg. $100)" required/>
								</div>
							</div>
							<!-- End Price field -->
							<!-- Start Country field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Country :</label>
								<div class="col-sm-10 col-md-5">
									<input type="text" name="country" class="form-control" value="<?php echo $item['Country'] ?>" placeholder="The country the item made in." required/>
								</div>
							</div>
							<!-- End Country field -->
							<!-- Start Tags field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Tags</label>
								<div class="col-sm-10 col-md-5">
									<textarea name="tags" type="textarea" class="form-control" placeholder="Type your Tags separated by comma"></textarea>
								</div>
							</div>
							<!-- End Tags field -->
							<!-- Start Image field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Image</label>
								<div class="col-sm-10 col-md-5">
									<input type="file" name="item_img" />
								</div>
							</div>
							<!-- End Image field -->
							<!-- Start Quality field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Quality :</label>
								<div class="col-sm-10 col-md-5">
									<select name="quality">
										<option value="1" <?php if($item['Quality'] == 1){echo 'selected';} ?> >Used</option>
										<option value="2" <?php if($item['Quality'] == 2){echo 'selected';} ?> >New</option>

									</select>
								</div>
							</div>
							<!-- End Quality field -->
							<!-- Start Rating field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Rating :</label>
								<div class="col-sm-10 col-md-5">
									<select name="rating">
										<option value="1" <?php if($item['Rating'] == 1){echo 'selected';} ?> >1</option>
										<option value="2" <?php if($item['Rating'] == 2){echo 'selected';} ?> >2</option>
										<option value="3" <?php if($item['Rating'] == 3){echo 'selected';} ?> >3</option>
										<option value="4" <?php if($item['Rating'] == 4){echo 'selected';} ?> >4</option>
										<option value="5" <?php if($item['Rating'] == 5){echo 'selected';} ?> >5</option>
									</select>
								</div>
							</div>
							<!-- End Rating field -->
							<!-- Start Categories field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Category :</label>
								<div class="col-sm-10 col-md-5">
									<select name="category">
										<?php
											$parent_cats = records('*', 'categories', "WHERE parent = 0");
											foreach($parent_cats as $parent_cat){
												echo "<option value='" . $parent_cat['ID'] . "' ";
													if($parent_cat['ID'] == $item['Category_ID']){echo 'selected';}
													echo ">" . $parent_cat['Name'] . "</option>";
													
													$sec_lev_parent_cats = records('*', 'categories', "WHERE parent = {$parent_cat['ID']}");
													foreach($sec_lev_parent_cats as $sec_lev_parent_cat){
														echo "<option value='" . $sec_lev_parent_cat['ID'] . "' ";
															if($sec_lev_parent_cat['ID'] == $item['Category_ID']){echo 'selected';}
															echo ">- " . $sec_lev_parent_cat['Name'] . "</option>";

														$third_lev_parent_cats = records('*', 'categories', "WHERE parent = {$sec_lev_parent_cat['ID']}");
														foreach($third_lev_parent_cats as $third_lev_parent_cat){
															echo "<option value='" . $third_lev_parent_cat['ID'] . "' ";
																if($third_lev_parent_cat['ID'] == $item['Category_ID']){echo 'selected';}
																echo ">-- " . $third_lev_parent_cat['Name'] . "</option>";
														}
													}
											}
										?>

									</select>
								</div>
							</div>
							<!-- End Categories field -->
							<!-- Start Users field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">User :</label>
								<div class="col-sm-10 col-md-5">
									<select name="user">
										<?php
											if($_SESSION['groupId'] == 1){
												$stmt = $conn->prepare("SELECT ID, Username FROM users");
												$stmt->execute();
												$users = $stmt->fetchAll();
												foreach($users as $user){
													echo "<option value='" . $user['ID'] . "' ";
														if($item['Member_ID'] == $user['ID']){echo 'selected';}
														echo ">" . $user['Username'] . "</option>";
												}
											}else{
												$stmt = $conn->prepare("SELECT ID, Username FROM users WHERE ID = ?");
												$stmt->execute(array($_SESSION['id']));
												$user = $stmt->fetch();
												echo "<option value='" . $user['ID'] . "'>" . $user['Username'] . "</option>";
											}
										?>
									</select>
								</div>
							</div>
							<!-- End Users field -->
							<!-- Start Add Button field -->
							<div class="form-group form-group-lg">
								<div class="col-sm-10 col-md-5">
									<input type="submit" value="Update" class="btn btn-primary btn-lg" />
								</div>
							</div>
							<!-- End Add Button field -->
						</form>

						<?php 
							// Getting all Comments into the table of the Comments.
							$stmt = $conn->prepare("SELECT 
														comments.*, users.Username AS Member_Name 
													FROM comments
													INNER JOIN users ON users.ID = comments.UserID 
													WHERE ItemID = $id ");
							$stmt->execute();
							$rows = $stmt->fetchAll();
							if(! empty($rows)){
								?>
									<!-- Start comments related to the item we're trying to edit -->
									<h1 class="text-center">Manage [<?php echo $item['Name'] ?>] Comments</h1>
									<div class="container">
										<div class="table-responsive">
											<table class="main-table text-center table table-bordered">
												<tr>
													<td>ID</td>
													<td>Comment</td>
													<td>Date</td>
													<td>Member ID</td>
													<td>Member Name</td>
													<td>Controls</td>
												</tr>
												<?php
													foreach($rows as $row) {
														echo "<tr>";
															echo "<td>" . $row['ID'] . "</td>";
															echo "<td>" . $row['Comment'] . "</td>";
															echo "<td>" . $row['CommDate'] . "</td>";
															echo "<td>" . $row['UserID'] . "</td>";
															echo "<td>" . $row['Member_Name'] . "</td>";
															echo "<td>
																	<a href='comments.php?do=edit&id=" . $row['ID'] ."' class='btn btn-success'>Edit</a>
																	<a href='comments.php?do=delete&id=" . $row['ID'] ."' class='btn btn-danger confirm'>Delete</a>";
																	if($row['CommStat'] == 0){
																		echo "<a href='comments.php?do=approve&id=" 
																			 . $row['ID'] .
																			 "'class='btn btn-info activate'><i class='fa fa-check'></i> Approve</a>";
																	}
															echo "</td>";
														echo "</tr>";
													}
												?>
											</table>
										</div>
										<a href='comments.php?do=add' class="btn btn-primary" style="margin-bottom: 15px"><i class="fa fa-plus"></i> Add Comment</a>
									</div>
									<!-- End comments related to the item we're trying to edit -->
								<?php 
							}
						?>
					</div>

				<?php
			}else{echo "<div class='container alert alert-danger text-center'>Sorry this item is not yours. and you don't have admin permision to edit it.</div>";}
		}else{echo "<div class='container alert alert-danger text-center'>Sorry, there is no item with such ID.</div>";}
	}elseif($do == "update"){		// Update page ==========================
		echo "<div class='container'>";
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				echo "<h1 class='text-center'>Update Item</h1>";

				$i_img_name		= $_FILES['item_img']['name'];
				$i_img_tmp		= $_FILES['item_img']['tmp_name'];
				$i_img_type		= $_FILES['item_img']['type'];
				$i_img_size		= $_FILES['item_img']['size'];

				$img_allowed_ex	= array("jpeg", "jpg", "png");

				$img_type_exploded 	= explode(".", $i_img_name);
				$i_img_ex			= strtolower(end($img_type_exploded));

				$id 			= $_POST['id'];
				$name 			= $_POST['name'];
				$description 	= $_POST['description'];
				$price 			= filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
				$country 		= $_POST['country'];
				$tags 			= $_POST['tags'];
				$quality 		= $_POST['quality'];
				$rating 		= $_POST['rating'];
				$category 		= $_POST['category'];
				$user 			= $_POST['user'];

				$form_errors = array();


				if(empty($name)) {$errors[] = "Name cannot be empty.";}
				if(empty($description)) {$errors[] = "Description cannot be empty.";}
				if(empty($price)) {$errors[] = "Price cannot be empty.";}
				if(empty($country)) {$errors[] = "Country cannot be empty.";}
				if(empty($quality)) {$errors[] = "Quality cannot be empty.";}
				if(empty($rating)) {$errors[] = "Rating cannot be empty.";}
				if(empty($category)) {$errors[] = "Category cannot be empty.";}
				if(empty($user)) {$errors[] = "Rating cannot be empty.";}
				if(empty($i_img_name)){$form_errors[] = "Sorry you have to choose image for this product.";}
				if(!empty($i_img_name) && !in_array($i_img_ex, $img_allowed_ex)){$form_errors[] = "Sorry this file type is not allowed.";}

				if(empty($form_errors)){

					$item_img = rand(0, 1000) . "_" . $name . "." . $i_img_ex;
					move_uploaded_file($i_img_tmp, "uploads/item_imgs/" . $item_img);

					// Update data
					$stmt = $conn->prepare("UPDATE items SET Name=?, Description=?, Price=?, Country=?, tags=?, img=?, Quality=?, Rating=?, Category_ID=?, Member_ID=? WHERE ID=?");
					$stmt->execute(array($name, $description, $price, $country, $tags, $item_img, $quality, $rating, $category, $user, $id));

					$sucMsg = "<div class='alert alert-success'>" . $stmt->rowCount() . " records have been updated.</div>";
					homeRedirection($sucMsg, 'back', 2);
				}else{
					foreach ($form_errors as $error) {
						echo "<div class='alert alert-danger'>" . $error . "</div>";
					}
				}
			}else {
				$errMsg = "<div class='alert alert-danger'>Sorry you cannot browse this page directly.</div>";
				homeRedirection($errMsg, 'back', '');
			}
		echo "</div>";
	}elseif($do == "add") {			// Add page =============================

			?>
				<h1 class="text-center">Add new Item</h1>
				<div class="container">
					<form class="form-horizontal" action="?do=insert" method="POST" enctype="multipart/form-data">
						<!-- Start Name field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Name :</label>
							<div class="col-sm-10 col-md-5">
								<input type="text" name="name" class="form-control" autocomplete="off" placeholder="Name of the item"/>
							</div>
						</div>
						<!-- End Name field -->
						<!-- Start Description field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Description :</label>
							<div class="col-sm-10 col-md-5">
								<input type="text" name="description" class="form-control" placeholder="Describe the item"/>
							</div>
						</div>
						<!-- End Description field -->
						<!-- Start Price field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Price :</label>
							<div class="col-sm-10 col-md-5">
								<input type="text" name="price" class="form-control" placeholder="The price of the item (eg. $100)"/>
							</div>
						</div>
						<!-- End Price field -->
						<!-- Start Country field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Country :</label>
							<div class="col-sm-10 col-md-5">
								<input type="text" name="country" class="form-control" placeholder="The country the item made in."/>
							</div>
						</div>
						<!-- End Country field -->
						<!-- Start Tags field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Tags</label>
							<div class="col-sm-10 col-md-5">
								<textarea name="tags" type="textarea" class="form-control" placeholder="Type your Tags separated by comma"></textarea>
							</div>
						</div>
						<!-- End Tags field -->
						<!-- Start Item Image -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Image</label>
							<div class="col-sm-10 col-md-5">
								<input type="file" name="item_img"/>
							</div>
						</div>
						<!-- End Item Image -->
						<!-- Start Quality field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Quality :</label>
							<div class="col-sm-10 col-md-5">
								<select name="quality">
									<option value="0">...</option>
									<option value="1">Used</option>
									<option value="2">New</option>

								</select>
							</div>
						</div>
						<!-- End Quality field -->
						<!-- Start Rating field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Rating :</label>
							<div class="col-sm-10 col-md-5">
								<select name="rating">
									<option value="0">....</option>
									<option value="1">1</option>
									<option value="2">2</option>
									<option value="3">3</option>
									<option value="4">4</option>
									<option value="5">5</option>
								</select>
							</div>
						</div>
						<!-- End Rating field -->
						<!-- Start Categories field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">Category :</label>
							<div class="col-sm-10 col-md-5">
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
						<!-- Start Users field -->
						<div class="form-group form-group-lg">
							<label class="col-sm-2 control-label">User :</label>
							<div class="col-sm-10 col-md-5">
								<select name="member">
									<?php
										if($_SESSION['groupId'] == 1){
											echo "<option value='0'>....</option>";
											$stmt = $conn->prepare("SELECT ID, Username FROM users");
											$stmt->execute();
											$users = $stmt->fetchAll();
											foreach($users as $user){
												echo "<option value='" . $user['ID'] . "'>" . $user['Username'] . "</option>";
											}
										}else{
											$stmt = $conn->prepare("SELECT ID, Username FROM users WHERE ID = ?");
											$stmt->execute(array($_SESSION['id']));
											$user = $stmt->fetch();
											echo "<option value='" . $user['ID'] . "'>" . $user['Username'] . "</option>";
										}
									?>
								</select>
							</div>
						</div>
						<!-- End Users field -->
						<!-- Start Add Button field -->
						<div class="form-group form-group-lg">
							<div class="col-sm-10 col-md-5">
								<input type="submit" value="Add" class="btn btn-primary btn-lg" />
							</div>
						</div>
						<!-- End Add Button field -->
					</form>
				</div>
			<?php
	}elseif($do == "insert") {		// Insert page ==========================

		echo "<div class='container'>";
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				echo "<h1 class='text-center'>Insert Item</h1>";
				
				$i_img_name		= $_FILES['item_img']['name'];
				$i_img_tmp		= $_FILES['item_img']['tmp_name'];
				$i_img_type		= $_FILES['item_img']['type'];
				$i_img_size		= $_FILES['item_img']['size'];

				// Getting file extension
				$img_type_exploded 	= explode(".", $i_img_name);
				$i_img_ex			= strtolower(end($img_type_exploded));

				//allowed extensions to be uploaded
				$img_allowed_ex = array("jpeg", "jpg", "png");

				$name 			= $_POST['name'];
				$description 	= $_POST['description'];
				$price 			= filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_INT);
				$country 		= $_POST['country'];
				$tags 			= $_POST['tags'];
				$quality 		= $_POST['quality'];
				$rating 		= $_POST['rating'];
				$cat_id 		= $_POST['category'];
				$member_id 		= $_POST['member'];

				// Validate input fields
				$errors = array();

				if(empty($name)) {$errors[] = "Name cannot be empty.";}
				if(empty($description)) {$errors[] = "Description cannot be empty.";}
				if(empty($price)) {$errors[] = "Price cannot be empty.";}
				if(empty($country)) {$errors[] = "Country cannot be empty.";}
				if(empty($quality)) {$errors[] = "Quality cannot be empty.";}
				if(empty($rating)) {$errors[] = "Rating cannot be empty.";}
				if(empty($cat_id)) {$errors[] = "Category cannot be empty.";}
				if(empty($member_id)) {$errors[] = "Rating cannot be empty.";}
				if(empty($i_img_name)) {$errors[] = "You have to upload an image.";}
				if(!empty($i_img_name) && !in_array($i_img_ex, $img_allowed_ex)) {$errors[] = "This file cannot be uploaded, try another file type.";}
				if($i_img_size > 2097152) {$errors[] = "Image cannot be more than 2MB.";}

				if(empty($errors)) {
					$item_img = rand(1, 1000) . "_" . $name;
					move_uploaded_file($i_img_tmp, "uploads/item_imgs/" . $item_img);

					// Insert data preparation code
					$stm = $conn->prepare("INSERT INTO items(Name, Description, Price, Country, tags, img, Quality, Rating, Add_Date, Category_ID, Member_ID)
											VALUES(:name, :description, :price, :country, :tags, :img, :quality, :rating, now(), :cat_id, :member_id)");
					// Executing the insert preparation code by the data given from 'POST' request from the form.
					$stm->execute(array(
						'name' 			=> $name,
						'description' 	=> $description,
						'price'			=> $price,
						'country'		=> $country,
						'tags'			=> $tags,
						'img'			=> $item_img,
						'quality'		=> $quality,
						'rating'		=> $rating,
						'cat_id' 		=> $cat_id,
						'member_id'		=> $member_id
					));

					$sucMsg = "<div class='container alert alert-success'>" . $stm->rowCount() . " records have been inserted.</div>";
					homeRedirection($sucMsg, 'back', '');
				}else{
					foreach($errors as $error) {echo "<div class='alert alert-danger'>" . $error . "</div>";}
					homeRedirection('', 'back', 3);
				}
			}else {
				$errMsg = "<div class='container alert alert-danger'>Sorry you can't browse this page directly.</div>";
				homeRedirection($errMsg, 'back', 3);
			}
		echo "</div>";
	}elseif($do == "delete"){		// Delete page ==========================
		echo "<div class='container'>";
			$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

			// This statement is for getting Member_ID from items table.
			$stmt = $conn->prepare("SELECT Member_ID FROM items WHERE ID=?");
			$stmt->execute(array($id));
			$item = $stmt->fetch();

			// Check if an item with a spacific ID in the items table exists.
			$check = checkItem('ID', 'items',  $id);

			if($check > 0){
				if($_SESSION['groupId'] == 1 || $_SESSION['id'] == $item['Member_ID']){
					$stmt = $conn->prepare("DELETE FROM items WHERE ID=:id");
					$stmt->bindParam('id', $id);
					$stmt->execute();

					$msg = "<div class='container alert alert-danger'>" . $stmt->rowCount() . " record have been deleted.</div>";
					homeRedirection($msg, 'back', 4);

				}elseif($_SESSION['id'] == $item['Member_ID']){
					$stmt = $conn->prepare("DELETE FROM items WHERE ID=:id");
					$stmt->bindParam('id', $id);
					$stmt->execute();

				}else { echo "<div class='container alert alert-danger text-center'>This item does not belong to you to delete it.</div>";}
			}else{echo "<div class='container alert alert-danger text-center'>The Item you are trying to delete is not exists.</div>";}
		echo "</div>";
	}elseif($do == "approve"){		// Approve page =========================
		// Check if there is an 'id' = numeric number in the link [through get request]
		// This is if() abbreviated, if id is set in  the link and is numeric then get the integer value of it or else equal 0
		$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

		// Check if an item with a spacific ID exists in the database
		$check = checkItem('ID', 'items', $id);

		$stmt = $conn->prepare("SELECT Member_ID FROM items WHERE ID = ?");
		$stmt->execute(array($id));
		$member_check = $stmt->fetch();

		echo "<h1 class='text-center'>Approve Item</h1>";
		// Updating the item if the ID exists or print error message if the ID does not exist.
		if($check > 0) {
			if($_SESSION['groupId'] == 1){
				$stmt = $conn->prepare("UPDATE items SET ApprStat = 1 WHERE ID = ?");
				$stmt->execute(array($id));

				$succMsg = "<div class='container alert alert-success'>" . $stmt->rowCount() . " records has been Approved.</div>";
				homeRedirection($succMsg, 'back');
			}else{
				$errMsg = "<div class='container alert alert-danger'>Sorry you don't have admin permision to approve this item.</div>";
				homeRedirection($errMsg, 'back');
			}
		}else {
			$errMsg = "<div class='container alert alert-danger'>Sorry there is no Item with such ID.</div>";
			homeRedirection($errMsg, 'back');
		}
	}else {							// Default page =========================
		echo "<div class='container alert alert-danger'>Error, there is no such page with this name \"" . $_GET['do'] . "\"</div>";
	}

	include $temp . "footer.php";
}else {
	header("Location: index.php");
	exit();
}

?>