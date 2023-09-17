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

// Dynamic function for getting any records from any table v1.0, v1.1, v1.2
// $item = the item to select [*, id, name, ....]
// $table = the tablt to select from
// $where = for adding additional condition like "WHERE sth = sth"
// $and   = for adding additional condition like "AND sth = sth"
function records($item, $table, $where = NULL, $and = NULL, $order = 'ID', $ordering = 'ASC'){
	global $conn;

	//if condition abbreviated
	// if $addQuery = Null (which is no thing) then $addQuery = Null  ==> and  $query = NULL also
	// if $addQuery has value then $addQuery = this value  ==> $query = same value as well
	// this piece of code where in v1.0
	// $query = $addQuery == NULL ? '' : $addQuery;

	$stmt = $conn->prepare("SELECT $item FROM $table $where $and ORDER BY $order $ordering");
	$stmt->execute();
	$records = $stmt->fetchAll();
	return $records;
}

// Checking if the user is admin or just member
function chechUserStat($user){
	global $conn;
	$checkStmt = $conn->prepare("SELECT Username FROM users WHERE Username=? AND RegStatus=0");
	$checkStmt->execute(array($user));
	$checkRow = $checkStmt->rowCount();
	return $checkRow;
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

//Dynamic function for getting any records from any table
function getAllRecords($field, $table, $where = NULL, $and = NULL){
	global $conn;
	$stmt = $conn->prepare("SELECT $field FROM $table $where $and");
	$stmt->execute();
	$allRecords = $stmt->fetchAll();
	return $allRecords;
}

?>