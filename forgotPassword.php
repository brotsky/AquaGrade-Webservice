<?php @session_start();

require_once('config.inc.php');


$_username = isset($_REQUEST['username']) ? strtolower($_REQUEST['username']) : null;
$_email = isset($_REQUEST['email']) ? strtolower($_REQUEST['email']) : null;

if(!$_username && !$_email)
	die(json_encode(0));



$sql = "SELECT username, email, password
		FROM users
		WHERE LOWER(username) = ? OR LOWER(email) = ?
		LIMIT 1";

$stmt = $mysqli->prepare($sql);

if($mysqli->error || !$stmt)
{
	error_log("mysqli error while preparing: " . $mysqli->error);
	die(json_encode(-1));
}

$stmt->bind_param('ss', $_username, $_email);
$stmt->bind_result($username, $email, $password);

$stmt->execute();
$stmt->store_result();
$stmt->fetch();


if($stmt->num_rows > 0)
{
	mail(
		$email,
		
		'Aquagrade login information',
		
		"Your Aquagrade login information is below.\n\nUsername: $username\nPassword: $password\n\nThank you,\nAquagrade",
		
		'From: webmaster@aquagrade.com' . "\r\n" .
		'Reply-To: no-reply@aquagrade.com'
	);
	
	die(json_encode(1));
}


if($mysqli->error)
{
	error_log("mysqli error while executing: " . $mysqli->error);
	die(json_encode(-1));
}

die(json_encode(0));
?>