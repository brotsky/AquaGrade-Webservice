<?php @session_start();

require_once('config.inc.php');


$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : null;
$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : null;
$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : null;
$userID = isset($_REQUEST['userID']) ? $_REQUEST['userID'] : null;

$sql = "UPDATE users
		SET username = ?, password = ?, email = ?
		WHERE userID = ?";

$stmt = $mysqli->prepare($sql);

if($mysqli->error || !$stmt)
{
	error_log("mysqli error while preparing: " . $mysqli->error);
	die('error');
}

$total = 0;

$stmt->bind_param('sssi',
	$username, $password, $email, $userID);

if($stmt->execute())
{
	$user = new stdClass;
	$user->userID = $userID;
	$user->username = $username;
	$user->email = $email;
	
	return json_encode($user);
}


if($mysqli->error)
{
	error_log("mysqli error while executing: " . $mysqli->error);
}

die(json_encode(0));
?>