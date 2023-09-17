<?php
// Output Buffering (Ya3ny Takhzeen .. Fa Haykhzen El Data El Awl except 'headers') Start. For not sending outputs before headers.
// It's preferable to set the 'ob_start()' before the 'session()' function.
ob_start('ob_gzhandler'); // gz is a technique to handle the outputs and compress it to speed up the preformance
session_start();
$pageTitle = "Show item";
include "init.php";

$i_id = isset($_GET['i_id']) && is_numeric($_GET['i_id']) ? intval($_GET['i_id']) : 0;

$stmt = $conn -> prepare("SELECT items.*, categories.Name AS cat_name, categories.ID AS c_id, users.username FROM items
						  INNER JOIN categories ON items.Category_ID = categories.ID
						  INNER JOIN users ON items.Member_ID = users.ID
						  WHERE items.ID = ?");
$stmt -> execute(array($i_id));

if($stmt->rowCount() > 0){

	$item = $stmt -> fetch();

	if($item['ApprStat'] == 1){ 
		?>

			<h1 class='text-center'><?php echo $item['Name']; ?></h1>
			<dive class='show-item'>
				<div class='container'>
					<div class='row'>
						<div class='col-md-4 col-sm-12'>
							<img class='img-responsive ' src='img.jpg' alt='' />
						</div>
						<div class='col-md-8 col-sm-12'>
							<div class='caption'>
								<ul class='list-unstyled'>
									<li><h2><?php echo $item['Name']; ?></h2></li>
									<li class='item_price'><h3>$<?php echo $item['Price']; ?></h3></li>
									<li><span>Description</span> : <?php echo $item['Description']; ?></li>
									<li><span>Date</span> : <?php echo $item['Add_Date']; ?></li>
									<li><span>Made in</span> : <?php echo $item['Country']; ?></li>
									<li><span>Category</span> : <a href='categories.php?cat_id=<?php echo $item['c_id']; ?>'><?php echo $item['cat_name']; ?></a></li>
									<li><span>Seller</span> : <a href='profile.php'><?php echo $item['username']; ?></a></li>
									<li class='tags_li'>
										<span>Tags</span> : 
										<?php
											$i_tags = explode(',', $item['tags']);
											if(!empty($i_tags[0])){
												foreach($i_tags as $i_tag){
													$i_tag = str_replace(" ", "", $i_tag);
													echo "<a href='tags.php?tn=" . $i_tag . "'>" . strtoupper($i_tag) . "</a> <span class='tags_separator'>|</span> ";
												}
											}else{echo "No TAGS";}
										?>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<hr class='custom-hr'>

					<!-- Start adding commet on the item section -->
					<?php 	if(isset($_SESSION['user_front'])){ ?>
								<div class='row'>
									<div class='col-md-offset-4'>
										<div class='item-add-comment'>
											<h3>Add Comment</h3>
											<form action='<?php echo $_SERVER["PHP_SELF"] . "?i_id=" . $item['ID'] ?>' method='POST'>
												<textarea name='comment'></textarea>
												<input class='btn btn-primary' type='submit' value='Add Comment'>
											</form>
											<?php
												if($_SERVER['REQUEST_METHOD'] == 'POST'){
													$add_comment = htmlspecialchars($_POST['comment'], ENT_QUOTES);
													$i_id		 = $item['ID'];
													$u_id		 = $_SESSION['user_front_id'];
													if(! empty($add_comment)){
														$insert_stmt = $conn->prepare('INSERT INTO comments(Comment, CommStat, CommDate, ItemID, UserID)
																						VALUES(:zcomment, 0, now(), :zi_id, :zu_id)'
														);

														$insert_stmt->execute(array(
															'zcomment'	=> $add_comment,
															'zi_id'		=> $i_id,
															'zu_id'		=> $u_id,
														));

														if($insert_stmt){
															echo '<div class="alert alert-success">Comment added successfuly</div>';
															// here we are making refresh for the page to prevent submitting the same data again on refresh
															header("refresh:1;url=" . $_SERVER["PHP_SELF"] . "?i_id=" . $item['ID']);
														}
													}
												}
											?>
										</div>
									</div>
								</div>
					<?php 
							}else{echo 'YOU have to <a href="login.php">login or register</a> to add comment';}
					?>
					<!-- End adding comments on the item section -->

					<!-- Start showing comments on the item section -->
					<hr class='custom-hr'>
					<?php
						// Getting all Comments of a specific item.
						$get_c_stmt = $conn->prepare("SELECT comments.*, users.Username FROM comments
														INNER JOIN users ON users.ID = comments.UserID
														WHERE comments.ItemID = ? AND CommStat = 1"
						);
						$get_c_stmt->execute(array($item['ID']));
						$item_comments = $get_c_stmt->fetchAll();

						foreach($item_comments as $item_comments){
								echo '<div class="comment_box">';
									echo '<div class="item_n">';
										echo '<img class="img-responsive img-circle" src="user.png" alt="" />';
										echo '<span>' . $item_comments['Username'] . '</span>';
									echo '</div>';
									echo '<span class="item_c">' . $item_comments['Comment'] . '</span>';
								echo "</div>";
						}
					?>
					<!-- End showing comments on the item section -->
				</div>
			</dive>
		<?php   
	}else{
		echo '<div class="container"><h3>Sorry, this item is wainting admin approval. Try again later.</h3></div>';
	}
	
}else{
	echo '<div class="container"><h3>ERROR, there\'s no item with such id</h3></div>';
}


include $temp . "footer.php";
ob_end_flush(); // Send the output buffer and turn off output buffering.
?>

