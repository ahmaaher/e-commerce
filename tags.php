<?php 
session_start();
$pageTitle = "Categories";
include "init.php"; 

if(isset($_GET['tn'])){
	$tag_n = $_GET['tn'];
	?>
		<div class="container">
			<?php 
				$tag_items = records("*", "items", "WHERE tags LIKE '%$tag_n%'");
				if(!empty($tag_items)){ 
			?>
					<h1 class='text-center'> <?php echo $tag_n ?> tag</h1>
					<div row="row">
						<?php
							foreach ($tag_items as $tag_item) {
								echo "<div class='col-sm-6 col-md-3 cat_items'>";
									echo "<div class='thumbnail item_box'>";
										echo "<img class='img-responsive' src='img.jpg' alt='' />";
										echo "<div class='caption of_hidden'>";
											echo "<h4><a href='item.php?i_id=" . $tag_item['ID'] ."'>" . $tag_item['Name'] . "</a></h4>";
											echo "<h4 class='item_price'>$" . $tag_item['Price'] . "</h4>";
											echo "<p>" . $tag_item['Description'] . "</p>";
											echo "<span class='i_date'>" . $tag_item['Add_Date'] . "</span>";
										echo "</div>";
									echo "</div>";
								echo "</div>";
							}
						?>
					</div>
			<?php 
				}else{
					echo 'Sorry, there\'s no products to show in this TAG.';
				}
			?>
		</div>
	<?php
}else{

}

?>

<?php include $temp . "footer.php"; ?>
