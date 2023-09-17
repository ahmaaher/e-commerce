<?php
session_start();
$pageTitle = "Home";
include "init.php";

echo '<div class="container">';
	echo '<div class="row home_p_items">';
		$all_items = records('*', 'items', 'WHERE ApprStat = 1', '', 'ID', 'DESC');
		foreach($all_items as $item){
				echo "<div class='col-sm-6 col-md-3 cat_items'>";
					echo "<div class='thumbnail item_box'>";
						echo "<img class='img-responsive img_special_scale' src='admin/uploads/item_imgs/";
							if(!empty($item['img'])){echo $item['img'];}else{echo "default_i_img.jpg";}
						echo "' alt='' />";
						echo "<div class='caption of_hidden'>";
							echo "<h3><a href='item.php?i_id=" . $item['ID'] ."'>" . $item['Name'] . "</a></h3>";
							echo "<h4 class='item_price'>$" . $item['Price'] . "</h4>";
							echo "<p>" . $item['Description'] . "</p>";
							echo "<span class='i_date'>" . $item['Add_Date'] . "</span>";
						echo "</div>";
					echo "</div>";
				echo "</div>";
		}
	echo '</div>';
echo '</div>';

include $temp . "footer.php";
?>


