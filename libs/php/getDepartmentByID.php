<?php

$executionStartTime = microtime(true);

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

$query = $conn->prepare('SELECT id, name, locationID FROM department WHERE id = ?');

$query->bind_param("i", $_REQUEST['param1']);

$query->execute();

if (false === $query) {

	$output['status']['code'] = "400";
	$output['status']['name'] = "executed";
	$output['status']['description'] = "query failed";
	$output['data'] = [];

	echo json_encode($output);

	mysqli_close($conn);
	exit;
}

$result = $query->bind_result($id, $name, $locationID);
class departmentResult
{
};
$data = new departmentResult;

while ($query->fetch()) {
	$data->id = $id;
	$data->name = $name;
	$data->locationID = $locationID;
}

$output['status']['code'] = "200";
$output['status']['name'] = "ok";
$output['status']['description'] = "success";
$output['status']['returnedIn'] = (microtime(true) - $executionStartTime) / 1000 . " ms";
$output['data'] = $data;

header('Content-Type: application/json; charset=UTF-8');

echo json_encode($output);

mysqli_close($conn);
