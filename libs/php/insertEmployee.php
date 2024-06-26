<?php

// remove next two lines for production

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$executionStartTime = microtime(true);
//this includes the login details
include("db.php");

header('Content-Type: application/json; charset=UTF-8');

$conn = new mysqli($cd_host, $cd_user, $cd_password, $cd_dbname, $cd_port, $cd_socket);

if (mysqli_connect_errno()) {

	$output['status']['code'] = "300";
	$output['status']['name'] = "failure";
	$output['status']['description'] = "database unavailable";
	$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
	$output['data'] = [];

	mysqli_close($conn);

	echo json_encode($output);

	exit;
}

// SQL statement accepts parameters and so is prepared to avoid SQL injection.
// $_REQUEST used for development / debugging. Remember to change to $_POST for production

$firstName = trim($_REQUEST['param1']);
$lastName = trim($_REQUEST['param2']);
$email = trim($_REQUEST['param3']);
$jobTitle = "";

$query = $conn->prepare('INSERT INTO personnel (firstName, lastName, email, departmentID, jobTitle) VALUES(?,?,?,?,?)');
$query->bind_param("sssis", $firstName, $lastName, $email, $_REQUEST['param4'], $jobTitle);

$query->execute();

if (false === $query) {

	$output['status']['code'] = "400";
	$output['status']['name'] = "executed";
	$output['status']['description'] = "query failed";
	$output['data'] = [];

	mysqli_close($conn);

	echo json_encode($output);

	exit;
}

$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = [$_REQUEST['param1'] . " " . $_REQUEST['param2'] . " has successfully been added to the Company Directory."];

mysqli_close($conn);

echo json_encode($output);
