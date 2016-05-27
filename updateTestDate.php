<?php @session_start();

require_once('config.inc.php');

$id_place = isset($_REQUEST['placeID']) ? $_REQUEST['placeID'] : null;
$date = isset($_REQUEST['date']) ? $_REQUEST['date'] : null;

//limit to 50 results
$sql = "UPDATE tbl_place
		SET DateCreated = ?
		WHERE placeID = ?";
		
$stmt = $mysqli->prepare($sql);

if($mysqli->error || !$stmt)
{
	error_log("mysqli error while preparing: " . $mysqli->error);
	echo $mysqli->error;
	die(json_encode(null));
}

$stmt->bind_param('si', $date, $id_place);

$stmt->execute();

if($mysqli->error)
{
	error_log("mysqli error while executing: " . $mysqli->error);
	echo $mysqli->error;
	die(json_encode(null));
}

echo json_encode($stmt->affected_rows);
?>