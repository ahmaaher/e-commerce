<?php
// Output Buffering (Ya3ny Takhzeen .. Fa Haykhzen El Data El Awl except 'headers') Start. For not sending outputs before headers.
// It's preferable to set the 'ob_start()' before the 'session()' function.
ob_start('ob_gzhandler'); // gz is a technique to handle the outputs and compress it to speed up the preformance
session_start();
$pageTitle = "Profile";
include "init.php";

if(isset($_SESSION['user_front'])){

	$userStmt = $conn->prepare("SELECT * FROM users WHERE Username=?");
	$userStmt->execute(array($_SESSION['user_front']));
	$userInfo = $userStmt->fetch();
	?>
		<div class="pro-info">   <!-- Profile Information -->
			<div class="container">
				<div class="panel panel-primary">
					<div class="panel-heading">My Information</div>
					<div class="panel-body">
						<ul class="list-unstyled">
							<li><span class="span_info">ID</span>: <?php echo $userInfo['ID']; ?></li>
							<li><span class="span_info">Username</span>: <?php echo $userInfo['Username']; ?></li>
							<li><span class="span_info">Email</span>: <?php echo $userInfo['Email']; ?></li>
							<li><span class="span_info">Registration date</span>: <?php echo $userInfo['Insert_Date']; ?></li>
							<li><span class="span_info">Permission</span>: <?php if($userInfo['GroupID'] == 1){ echo 'Admin Permission'; }else{ echo 'Member Permission'; } ?></li>
							<li><span class="span_info">Status</span>: <?php if($userInfo['RegStatus'] == 1){ echo 'Active account'; }else{ echo 'non-active account'; } ?></li>
						</ul>
						<a href='#' class='btn btn-default'>Edit info</a>
					</div>
				</div>
			</div>
		</div>
		<div class="pro-ads">   <!-- Profile Ads -->
			<div class="container">
				<div class="panel panel-primary">
					<div class="panel-heading">My Ads</div>
					<div class="panel-body">
						<div class="row">
							<?php
								$addQuery = 'WHERE Member_ID = ' . $userInfo['ID'];
								$my_items = records("*", "items", "WHERE Member_ID = {$userInfo['ID']}", "", "ID", "DESC");
								if(!empty($my_items)){
									foreach ($my_items as $my_item) {
										echo "<div class='col-sm-6 col-md-3 cat_items'>";
											echo "<div class='thumbnail item_box'>";
												if($my_item['ApprStat'] == 0){echo '<span class="waiting-appr">waiting approval..</span>';}
												echo "<img class='img-responsive img_special_scale' src='admin/uploads/item_imgs/";
													if(!empty($my_item['img'])){echo $my_item['img'];}else{echo "default_i_img.jpg";}
												echo "' alt='' />";
												echo "<div class='caption of_hidden'>";
													echo "<h3><a href='item.php?i_id=" . $my_item['ID'] ."'>" . $my_item['Name'] . "</a></h3>";
													echo "<h4 class='item_price'>$" . $my_item['Price'] . "</h4>";
													echo "<p>" . $my_item['Description'] . "</p>";
													echo "<span class='i_date'>" . $my_item['Add_Date'] . "</span>";
												echo "</div>";
											echo "</div>";
										echo "</div>";
									}
									echo '<div class="col-md-12 col-sm-12 text-center"><span class="btn-primary addnew-btn"><a href="newad.php">New Ad</a></span></div>';
								}else{echo 'NO Ads!<span class="pull-right btn-primary addnew-btn"><a href="newad.php">New Ad</a></span>';}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="pro-comments">  <!-- Profile comments -->
			<div class="container">
				<div class="panel panel-primary">
					<div class="panel-heading">My Comments</div>
					<div class="panel-body">
						<div class="row">
							<?php
								$stmt = $conn->prepare('SELECT comments.*, items.Name AS item_name FROM comments
														INNER JOIN items ON comments.ItemID = items.ID
														INNER JOIN users ON comments.UserID = users.ID
														WHERE users.ID = ?');
								$stmt->execute(array($userInfo['ID']));
								$my_records = $stmt->fetchAll();
								if(!empty($my_records)){
									foreach ($my_records as $my_record){
										echo '<div class="comment_box">';
											echo '<span class="item_n">' . $my_record['item_name'] . '</span>';
											echo '<span class="item_c">' . $my_record['Comment'] . '</span>';
										echo "</div>";
									}
								}else{echo 'NO COMMENTS!';}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php
}else{header('location: login.php'); exit();}
include $temp . "footer.php";
ob_end_flush(); // Send the output buffer and turn off output buffering.
?>

