<?php @session_start();

require_once('config.inc.php');

header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');


//$center_lat = isset($_REQUEST['lat']) ? $_REQUEST['lat'] : null;
//$center_lon = isset($_REQUEST['lon']) ? $_REQUEST['lon'] : null;

/*

$radius_levels = array(0.5, 1, 2, 3, 5, 10, 20, 50);

$count = isset($_GET['count']) ? $_GET['count'] : 0;

$min_results = $count ? $count / 2.5 : 15;
$max_results = 20;

if($count > $max_results)
	$count = $max_results;

if($count && $count < $max_results)
	$max_results = $count;

if($count < $min_results)
	$count = $min_results;

*/

//limit to 100 results
/*
$sql = "SELECT placeID, placename,
				address, street, state, city, country, phone, url,
				placePhoto, tdsNumber, comment, lat, longi, userID
		FROM tbl_place
		WHERE MBRCONTAINS(GeomFromText(?), location)
		LIMIT $max_results";
*/
		
$sql="SELECT placeID, placename,
				address, street, state, city, country, phone, url,
				placePhoto, tdsNumber, comment, lat, longi, userID
                from `tbl_place` order by `placeID` DESC limit 1";
		
$stmt = $mysqli->prepare($sql);

if($mysqli->error || !$stmt)
{
	error_log("mysqli error while preparing: " . $mysqli->error);
	die(json_encode(null));
}

$rows = null;

$i = 0;
do
{

	$stmt->bind_result($placeID, $placename,
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

	$stmt->free_result();

	++$i;
} while(count($rows) <= $min_results && ($i < count($radius_levels)));


//allow jsonp
if(isset($_REQUEST['callback']))
	echo $_REQUEST['callback'] . '(' . json_encode($rows) . ')';
else
	echo json_encode($rows);
?>