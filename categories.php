<?php 
session_start();
$pageTitle = "Categories";
include "init.php"; 

$cat_stmt = $conn -> prepare("SELECT items.*, categories.Name AS cat_name, categories.parent AS cat_parent  FROM items
						  	INNER JOIN categories ON items.Category_ID = categories.ID
						  	WHERE items.Category_ID = ? AND ApprStat = 1 ORDER BY items.ID DESC");

if(isset($_GET['cat_id'])){

	$cat_stmt -> execute(array($_GET['cat_id']));
	$cat_items = $cat_stmt->fetchAll();
	
	?>
		<div class="container">
			<?php 
				if(!empty($cat_items)){ 
			?>
					<h2 class="text-center"><?php echo $cat_items[0]['cat_name']; ?></h2>
					<div row="row">
						<?php
							foreach ($cat_items as $cat_item) {
								echo "<div class='col-sm-6 col-md-3 cat_items'>";
									echo "<div class='thumbnail item_box'>";
										echo "<img class='img-responsive img_special_scale' src='admin/uploads/item_imgs/";
											if(!empty($cat_item['img'])){echo $cat_item['img'];}else{echo "default_i_img.jpg";}
										echo "' alt='' />";
										echo "<div class='caption of_hidden'>";
											echo "<h4><a href='item.php?i_id=" . $cat_item['ID'] ."'>" . $cat_item['Name'] . "</a></h4>";
											echo "<h4 class='item_price'>$" . $cat_item['Price'] . "</h4>";
											echo "<p>" . $cat_item['Description'] . "</p>";
											echo "<span class='i_date'>" . $cat_item['Add_Date'] . "</span>";
										echo "</div>";
									echo "</div>";
								echo "</div>";
							}
						?>
					</div>
			<?php 
				}else{
					echo 'Sorry, there\'s no products to show in this category.';
				}
			?>
		</div>
	<?php
}else{
	$all_cats = records('*', 'categories');
	?>
		<div class="main_cat">
			<div class="container">
				<h2 class="text-center">Categories</h2>
				<?php
					foreach($all_cats as $cat){
						echo '<a class="main_c_links" href="?cat_id=' . $cat['ID'] . '">' . $cat['Name'] . '</a>';
					}
				?>
			</div>
		</div>
	<?php
}

?>

<?php include $temp . "footer.php"; ?>
