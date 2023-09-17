<?php

// Function to get page title. v1.0
function getTitle() {
	global $pageTitle;
	if(isset($pageTitle)) {
		echo $pageTitle;
	}else {
		echo "Default";
	}
}

// Redirection function  v1.1
// $msg = echo a message [error | success | warning .. etc]
// $url = the url that the fuction will redirect to after the specified seconds [$sec]
// $sec = seconds before redirecting
function homeRedirection($msg, $url= null, $sec= 3) {

	// Showing error message.
	echo $msg;

	if($url === null) {
		$url = "index.php";
		$urlName = "Homepage";
	}elseif($url == "back") {
		if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '') {
			$url = $_SERVER['HTTP_REFERER'];
			$urlName = "Previous page";
		}else {$url = "index.php"; $urlName = "Homepage";}
	}else {$url = "index.php"; $urlName = "Homepage";}

	// If there is a number of second placed, then redirect the page after that number of seconds.
	if($sec > 0) {
		echo "<div class='container alert alert-info'>You will be redirected to $urlName in $sec seconds</div>";
		header("refresh:$sec;url=$url");
		exit();
	}
}

// Dynamic check item function v1.0
// $column = the name of the column u want to select from specific table in the database
// $tabel = the name of the table from the database
// $value = the value of the column u want to select
function checkItem($column, $table, $value){
	global $conn;
	$stmt = $conn->prepare("SELECT $column FROM $table WHERE $column = ?");
	$stmt->execute(array($value));
	$count = $stmt->rowCount();
	return $count;
}

// Count Items function v1.1
// Counting a specific item in table
// $item = the item you want to count
// $table = the table from which you fetch the items
function countItems($item, $table, $value = null){
	// Adding additional query to the prepared statement if needed
	$addQuery = "";
	if($value === null || $value === ""){ // ($value == "") sometimes understood as 0, So make sure to write (=== not ==)
		$addQuery = "";
	}else{$addQuery = "WHERE $item = $value";}

	// Start the preparation and execution
	global $conn;
	$stmt = $conn->prepare("SELECT COUNT($item) FROM $table $addQuery");
	$stmt->execute();
	return $stmt->fetchColumn();
}
// Get latest items function v1.0, v1.1
// Getting the latest items from a specific column from a table [users, items, comments ....].
// $item = the item to select [User, Category, ID ...]
// $table = the tablt to select items from
// $order = the column we will order the results DESC with respect to.
// $limit = the limited data outputs
// v1.1 $addQuery for all members except ADMIN
function getLatest($item, $table, $order, $limit = 10){
	global $conn;
	$addQuery = "";
	if($table == 'users'){ $addQuery = 'WHERE GroupID = 0';}
	$stmt = $conn->prepare("SELECT $item FROM $table $addQuery ORDER BY $order DESC LIMIT $limit");
	$stmt->execute();
	$rows = $stmt->fetchAll();
	$count = $stmt->rowCount();
	return $rows;
}

// Dynamic function for getting any records from any table v1.0, v1.1, v1.2
// $item = the item to select [*, id, name, ....]
// $table = the tablt to select from
// $where = for adding additional condition like "WHERE sth = sth"
// $and   = for adding additional condition like "AND sth = sth"
function records($item, $table, $where = NULL, $and = NULL, $order = 'ID', $ordering = 'ASC'){
	global $conn;

	$stmt = $conn->prepare("SELECT $item FROM $table $where $and ORDER BY $order $ordering");
	$stmt->execute();
	$records = $stmt->fetchAll();
	return $records;
}

?>