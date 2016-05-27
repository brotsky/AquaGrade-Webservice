<?php @session_start();

require_once('config.inc.php');

$count = isset($_GET['count']) ? $_GET['count'] : 20;

//limit to 50 results
$sql = "SELECT DateCreated, placeID, placename,
				address, street, state, city, country, phone, url,
				placePhoto, tdsNumber, comment, lat, longi, userID
		FROM tbl_place
		WHERE DateCreated = '0000-00-00 00:00:00'
		LIMIT ?";
		
$stmt = $mysqli->prepare($sql);

if($mysqli->error || !$stmt)
{
	error_log("mysqli error while preparing: " . $mysqli->error);
	die(json_encode(null));
}


$listings = array();
$stmt->bind_param('i', $count);
$stmt->bind_result($DateCreated, $placeID, $placename,
					$address, $street, $state, $city, $country, $phone, $url,
					$placePhoto, $tdsNumber, $comment, $lat, $lon, $userID);
$stmt->execute();

if($mysqli->error)
{
	error_log("mysqli error while executing: " . $mysqli->error);
	die(json_encode(null));
}

	$image='http://trevorblanding.com/webservice/upload_image/thumb/';
	while($stmt->fetch())
	{
		if(!isset($added[$placeID]))
		{
			$row = new stdClass;
			
			$row->DateCreated = $DateCreated;
			$row->PlaceID = $placeID;
			$row->Placename = $placename;
			
			$row->Address = $address;
			$row->Street = $street;
			$row->State = $state;
			$row->City = $city;
			$row->Country = $country;
			$row->Phone = $phone;
			$row->URL = $url;
			
			$row->PlacePhoto = $image.$placePhoto;
			$row->TdsNumber = $tdsNumber;
			$row->Comment = $comment;

			$row->Lat = $lat;
			$row->Longi = $lon;
			
			$row->UserID = $userID;
			
			$rows[] = $row;
			$added[$placeID] = true;
		}
	}

echo json_encode($rows);
?>