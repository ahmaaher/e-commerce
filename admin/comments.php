<?php

session_start();
$pageTitle = "Comments";
if(isset($_SESSION["username"])) {
	include "init.php";

	$do = isset($_GET['do']) ? $_GET['do'] : 'main'; // if condition shortened >> codition ? True : false;
													 // which means if $_GET['do'] exists, then $do=$_GET['do'], if not exist then, $do='main';
	if($do == "main") {				// Main page ============================
		
		// Adding additional query to the statement preparation to get the pending Comments only
		// This code is just for getting the pending Comments in the table of the main page Comments,
		// when we click on 'pending Comments' in the Home page'Dashboard'
		$addQuery = "";
		if(isset($_GET['page']) && $_GET['page'] == 'pending'){
			$addQuery = "AND CommStat = 0";
		}

		// Getting all Comments into the table of the Comments.
		$stmt = $conn->prepare("SELECT 
									comments.*, items.Name AS Item_Name, users.Username AS Member_Name 
								FROM comments
								INNER JOIN items ON items.ID = comments.ItemID
								INNER JOIN users ON users.ID = comments.UserID
								$addQuery");
		$stmt->execute();
		$rows = $stmt->fetchAll();
		?>
			<h1 class="text-center">Manage Comments</h1>
			<div class="container">
				<div class="table-responsive">
					<table class="main-table text-center table table-bordered">
						<tr>
							<td>ID</td>
							<td>Comment</td>
							<td>Date</td>
							<td>Item ID</td>
							<td>Item Name</td>
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
									echo "<td>" . $row['ItemID'] . "</td>";
									echo "<td>" . $row['Item_Name'] . "</td>";
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
		<?php
	}elseif($do == "edit") { 		// Edit page ============================
		$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

		// Check if a comment with a spacific ID exists in the database
		$stmt = $conn -> prepare("SELECT * FROM comments WHERE ID = ?");
		$stmt -> execute(array($id));
		$row = $stmt -> fetch();
		$count = $stmt -> rowCount();

		// Checks if the logged in user is an admin or the same user who's trying to edit the comment that has logged in.
		if($_SESSION['groupId'] == 1 || $_SESSION['id'] == $row['UserID']) {
			// Showing the form of editing
			if($count > 0) {
				?>
					<h1 class="text-center">Edit</h1>
					<div class="container">
						<form class="form-horizontal" action="?do=update" method="POST">
							<input type="hidden" name="cId" value="<?php echo $id ?>"/>
							<!-- Start Comment field -->
							<div class="form-group form-group-lg">
								<label class="col-sm-2 control-label">Comment</label>
								<div class="col-sm-10 col-md-5">
									<textarea class="form-control" name="comment" style="height: 300px"><?php echo $row['Comment'] ?></textarea>
								</div>
							</div>
							<!-- End Comment field -->
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
														if($row['UserID'] == $user['ID']){echo 'selected';}
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
							<!-- Start submit button -->
							<div class="form-group form-group-lg">
								<div class="col-sm-10 col-md-5">
									<input type="submit" value="Save" class="btn btn-primary btn-lg" />
								</div>
							</div>
							<!-- End submit button -->
						</form>
					</div>
				<?php
			}else {
				// if the spacified ID doesn't exist in the database, print an Error massege
				$errMsg = "<div class='container alert alert-danger'>Sorry, there's no such ID</div>";
				homeRedirection($errMsg);
			}
		}else {
			$errMsg = "<div class='container alert alert-danger'>Sorry you are not an admin to modify this comment.</div>";
			homeRedirection($errMsg, 'back', 3);
		}
	}elseif($do == "update"){		// Update page ==========================
		echo "<h1 class='text-center'>Update Comment</h1>";
		echo "<div class='container'>";
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				echo "<h1 class='text-center'>Update</h1>";
				
				$cId 		= $_POST['cId'];
				$comment 	= $_POST['comment'];
				$memId 		= $_POST['user'];

				// Update data
				$stm = $conn->prepare("UPDATE comments SET Comment=?, UserID=? WHERE ID=?");
				$stm->execute(array($comment, $memId, $cId));

				$sucMsg = "<div class='alert alert-success'>" . $stm->rowCount() . " records has been updated.</div>";
				homeRedirection($sucMsg, 'back', 3);

			}else {
				$errMsg = "<div class='alert alert-danger'>Sorry you cannot browse this page directly.</div>";
				homeRedirection($errMsg, 'back');
			}
		echo "</div>";
	}elseif($do == "add") {			// Add page =============================
		$stmt = $conn -> prepare("SELECT * FROM comments");
		$stmt -> execute();
		$row = $stmt -> fetch();
		?>
			<h1 class="text-center">Add new comment</h1>
			<div class="container">
				<form class="form-horizontal" action="?do=insert" method="POST">
					<!-- Start Comment field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Comment</label>
						<div class="col-sm-10 col-md-5">
							<textarea class="form-control" name="comment" style="height: 300px"></textarea>
						</div>
					</div>
					<!-- End Comment field -->
					<!-- Start Item field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">Item :</label>
						<div class="col-sm-10 col-md-5">
							<select name="item">
								<option value="0">....</option>
								<?php
									$stmt = $conn->prepare("SELECT ID, Name FROM items");
									$stmt->execute();
									$items = $stmt->fetchAll();
									foreach($items as $item){
										echo "<option value='" . $item['ID'] . "' >" . $item['Name'] . "</option>";
									}
								?>
							</select>
						</div>
					</div>
					<!-- End Item field -->
					<!-- Start Users field -->
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label">User :</label>
						<div class="col-sm-10 col-md-5">
							<select name="user">
								<option value="0">....</option>
								<?php
									if($_SESSION['groupId'] == 1) {
										$stmt = $conn->prepare("SELECT ID, Username FROM users");
										$stmt->execute();
										$users = $stmt->fetchAll();
										foreach($users as $user){
											echo "<option value='" . $user['ID'] . "' >" . $user['Username'] . "</option>";
										}
									}else{
										$stmt = $conn->prepare("SELECT ID, Username FROM users WHERE ID = ?");
										$stmt->execute(array($_SESSION['id']));
										$users = $stmt->fetchAll();
										foreach($users as $user){
											echo "<option value='" . $user['ID'] . "' selected>" . $user['Username'] . "</option>";
										}
									}
								?>
							</select>
						</div>
					</div>
					<!-- End Users field -->
					<!-- Start submit field -->
					<div class="form-group form-group-lg">
						<div class="col-sm-10 col-md-5">
							<input type="submit" value="Add" class="btn btn-primary btn-lg" />
						</div>
					</div>
					<!-- End submit field -->
				</form>
			</div>
		<?php
	}elseif($do == "insert") {		// Insert page ==========================
		
		echo "<div class='container'>";
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				echo "<h1 class='text-center'>Insert Comment</h1>";
				
				$comment 	= $_POST['comment'];
				$itemId 	= $_POST['item'];
				$userId 	= $_POST['user'];

				// Insert data preparation code
				$stm = $conn->prepare("INSERT INTO comments(Comment, ItemID, UserID)
										VALUES(:comment, :itemId, :userId) ");
				// Executing the insert preparation code by the data given from 'POST' request from the form.
				$stm->execute(array(
					'comment' 		=> $comment,
					'itemId' 		=> $itemId,
					'userId'		=> $userId
				));

				$sucMsg = "<div class='container alert alert-success'>" . $stm->rowCount() . " records have been inserted.</div>";
				homeRedirection($sucMsg, 'back');
			}else {
				$errMsg = "<div class='container alert alert-danger'>Sorry you can't browse this page directly.</div>";
				homeRedirection($errMsg, 'back');  // Default home redirection is 3 seconds.
			}
		echo "</div>";
	}elseif($do == "delete"){		// Delete page ==========================
		
		$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
		// Check if a user with a spacific ID exists in the database
		$check = checkItem('ID', 'comments', $id);

		// for selecting the id of the user that started the session
		$stmt_userId = $conn->prepare("SELECT UserID FROM comments WHERE ID = :id");
		$stmt_userId->bindParam("id", $id);
		$stmt_userId->execute();
		$c_userId = $stmt_userId->fetch();

		// Checks if the logged in user is an admin or the same user who's trying to delete the comment.
		if($_SESSION['id'] == $c_userId['UserID'] || $_SESSION['groupId'] == 1) {
			echo "<h1 class='text-center'>Delete Comment</h1>";
			// Deleting the Comment if the ID is exists or print error message if the ID is not exist.
			if($check > 0) {
				
				$stmt = $conn->prepare("DELETE FROM comments WHERE ID = :id");
				$stmt->bindParam("id", $id);
				$stmt->execute();

				$sucMsg = "<div class='container alert alert-success'>" . $stmt->rowCount() . " records has been deleted.</div>";
				homeRedirection($sucMsg, 'back');

			}else {
				$errMsg = "<div class='container alert alert-danger'>Sorry there is no such comment with this ID.</div> ";
				homeRedirection($errMsg, 'back');
			}
		}else {
				$errMsg = "<div class='container alert alert-danger'>Sorry you are not an Admin to delete this comment.</div> ";
				homeRedirection($errMsg, 'back', '');
		}
	}elseif($do == "approve"){		// Approve page ========================

		// Check if there is an 'id' = numeric number in the link [through get request]
		$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

		// Check if a comment with a spacific ID exists in the database
		$check = checkItem('ID', 'comments', $id);

		// Checks if the logged in user is an admin.
		if($_SESSION['groupId'] == 1) {
			echo "<h1 class='text-center'>Approve Comment</h1>";
			// Updating the comment if the ID is exists or print error message if the ID is not exist.
			if($check > 0) {
				$stmt = $conn->prepare("UPDATE comments SET commStat = 1 WHERE ID = ?");
				$stmt->execute(array($id));

				$succMsg = "<div class='container alert alert-success'>" . $stmt->rowCount() . " records has been Approved.</div>";
				homeRedirection($succMsg, 'back');

			}else {
				$errMsg = "<div class='container alert alert-danger'>Sorry there is no comment with such ID.</div>";
				homeRedirection($errMsg, 'back');
			}
		}else {
				$errMsg = "<div class='container alert alert-danger'>Sorry you are not an Admin to Approve this comment.</div>";
				homeRedirection($errMsg, 'back', '');
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