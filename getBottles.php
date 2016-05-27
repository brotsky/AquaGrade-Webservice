<?php @session_start();

require_once('config.inc.php');




//limit to 50 results
$sql = "SELECT name, tds, phone_number, website, url_photo, date_entered
		FROM bottle_tds
		ORDER BY LOWER(name)
		LIMIT 5000";
		
$stmt = $mysqli->prepare($sql);

if($mysqli->error || !$stmt)
{
	error_log("mysqli error while preparing: " . $mysqli->error);
	die(json_encode(0));
}


$listings = array();

$stmt->bind_result($name, $tds, $phone_number, $website, $url_photo, $date_entered);

$stmt->execute();

if($mysqli->error)
{
	error_log("mysqli error while executing: " . $mysqli->error);
	die(json_encode(0));
}

while($stmt->fetch())
{
	$listing = new stdClass;
	
	$listing->name = $name;
	$listing->tds = $tds;
	$listing->phone_number = $phone_number;
	$listing->website = $website;
	$listing->url_photo = $url_photo;
	$listing->date_entered = $date_entered;
	
	$listings[] = $listing;
}

echo json_encode($listings);
?>