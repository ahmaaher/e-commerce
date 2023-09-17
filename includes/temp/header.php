<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title><?php getTitle(); ?></title>
		<link rel="stylesheet" href="<?php echo $cssLips; ?>bootstrap.min.css"/>
		<link rel="stylesheet" href="<?php echo $cssLips; ?>fontawesome-all.min.css" />
		<link rel="stylesheet" href="<?php echo $cssAdm; ?>front.css" />
	</head>
	<body>
		<div class="upper_bar">
			<div class="container">
				<?php 
					if(isset($_SESSION['user_front'])){
				?>

					    <div class="pull-right btn-group upper_bar_drdn">
				        	<img class="img-circle upper_bar_img" src="user.png" />
				            <span class="dropdown-toggle upper_bar_btn btn btn-default" data-toggle="dropdown">
				            	<?php echo $_SESSION['user_front']; ?>
				            	<span class="caret upper_bar_caret"></span>
				            </span>
				            <ul class="dropdown-menu">
					            <li><a href="profile.php">Profile</a></li>
					            <li><a href="newad.php">New item</a></li>
					            <li><a href="categories.php">Categories</a></li>
					            <li><a href="logout.php">Logout</a></li>
				            </ul>
					    </div>

				<?php
						$chechUserStat = chechUserStat($_SESSION['user_front']);
						if($chechUserStat == 0){
							// this user need to be active
						}
					}else{
						echo '<a href="login.php"> <span class="pull-right">Login | Signup</span> </a>';
					}
				?>
			</div>
		</div>
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<div class="container">
				<a class="navbar-brand" href="index.php">BRANDY</a>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav mr-auto pull-right">
						<?php
							$cat_records = records('*', 'categories', 'WHERE parent = 0');
							foreach($cat_records as $cat_record){
								echo "<li class='nav-item'><a class='nav-link' href='categories.php?cat_id=" . $cat_record['ID'] . "'>" . $cat_record['Name'] . "</a></li>";
							}
						?>
					</ul>
				</div>
			</div>
		</nav>
