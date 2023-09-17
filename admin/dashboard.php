<?php

// Output Buffering (Ya3ny Takhzeen .. Fa Haykhzen El Data El Awl except 'headers') Start. For not sending outputs before headers.
// It's preferable to set the 'ob_start()' before the 'session()' function.
ob_start('ob_gzhandler'); // gz is a technique to handle the outputs and compress it to speed up the preformance
session_start();
if(isset($_SESSION["username"])) {
	$pageTitle = "Dashboard";
	include "init.php";

	$numLM = '';
	$latestMembers = getLatest('*', 'users', 'ID');
	$numLI = 5;
	$latestItems = getLatest('*', 'items', 'ID');

	?>
		<div class="container text-center home-stat"> <!-- Total Dashboard -->
			<h1>Dashboard</h1>
			<div class="row home-stat">
				<div class="col-md-3">
					<div class="stat st-members">
						<i class="fa fa-users"></i>
						Total Members
						<a href="members.php"><span><?php echo countItems('ID', 'users') ?></span></a>
					</div>
				</div>
				<div class="col-md-3">
					<div class="stat st-pending">
						<i class="fa fa-user-plus"></i>
						Pending Members
						<a href="members.php?do=main&page=pending"><span><?php echo countItems('RegStatus', 'users', 0) ?></span></a>
					</div>
				</div>
				<div class="col-md-3">
					<div class="stat st-items">
						<i class="fa fa-tag"></i>
						Total Items
						<a href="items.php"><span><?php echo countItems('ID', 'items') ?></span></a>
					</div>
				</div>
				<div class="col-md-3">
					<div class="stat st-comments">
						<i class="fa fa-comments"></i>
						Total comments
						<a href="comments.php"><span><?php echo countItems('ID', 'comments') ?></span></a>
					</div>
				</div>
			</div>
		</div>
		
		<div class="container latest"> <!-- Latest Dashboard -->
			<!-- Row for latest users & latest items -->
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<div class="panel-heading">
							<i class="fa fa-users"></i>Latest <?php echo count($latestMembers); ?> users:
							<span class="pull-right toggle-list selected"><i class="fa fa-minus"></i></span>
						</div>
						<div class="panel-body">
							<ul class='list-unstyled latest_members'>
								<?php 
									foreach ($latestMembers as $member){
										echo "<li>";
											echo $member['Username'];
											echo "<a href='members.php?do=edit&id=" .  $member['ID'] . "' class='btn btn-success pull-right'><span><i class='fa fa-edit'></i>Edit</span></a>";
											if($member['RegStatus'] == 0){
												echo "<a href='members.php?do=activate&id=" . $member['ID'] ."' class='btn btn-info activate pull-right'>Activate</a>";
											}
										echo "</li>";
									}
								?>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-default pull-right">
						<div class="panel-heading">
							<i class="fa fa-tag"></i>Latest <?php echo count($latestItems); ?> items:
							<span class="pull-right toggle-list selected"><i class="fa fa-minus"></i></span>
						</div>
						<div class="panel-body">
							<ul class='list-unstyled latest_members'>
								<?php 
									foreach ($latestItems as $item){
										echo "<li>";
											echo $item['Name'];
											echo "<a href='items.php?do=edit&id=" .  $item['ID'] . "' class='btn btn-success pull-right'><span><i class='fa fa-edit'></i>Edit</span></a>";
											if($item['ApprStat'] == 0){
												echo "<a href='items.php?do=approve&id=" . $item['ID'] ."' class='btn btn-info activate pull-right'>Approve</a>";
											}
										echo "</li>";
									}
								?>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<!-- New row for latest comments. -->
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-default">
						<?php
							$stmt = $conn->prepare("SELECT comments.*, Username, items.Name AS i_name FROM comments
													INNER JOIN users ON users.ID = comments.UserID
													INNER JOIN items ON items.ID = comments.ItemID 
													ORDER BY comments.ID DESC"
							);
							$stmt->execute();
							$latest_cs = $stmt->fetchAll();
						?>
						<div class="panel-heading">
							<i class="fa fa-comment"></i>Latest <?php echo count($latest_cs); ?> comments:
							<span class="pull-right toggle-list selected"><i class="fa fa-minus"></i></span>
						</div>
						<div class="panel-body">
							<?php
								foreach ($latest_cs as $latest_c){
									echo '<div class="comment_box">';
										echo '<span class="member_n">' . $latest_c['Username'] . '</span>';
										echo '<span class="member_c">' . $latest_c['Comment'] . '</span>';
									echo "</div>";
								}
							?>
						</div>
					</div>
				</div>
			</div>

		</div>
	<?php
	include $temp . "footer.php";
}else {
	header("Location: index.php");
	exit();
}
ob_end_flush(); // Send the output buffer and turn off output buffering.

?>