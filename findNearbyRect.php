<?php @session_start();

require_once('config.inc.php');

header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');


$northeast_lat = isset($_REQUEST['ne_lat']) ? $_REQUEST['ne_lat'] : null;
$northeast_lon = isset($_REQUEST['ne_lon']) ? $_REQUEST['ne_lon'] : null;
$southwest_lat = isset($_REQUEST['sw_lat']) ? $_REQUEST['sw_lat'] : null;
$southwest_lon = isset($_REQUEST['sw_lon']) ? $_REQUEST['sw_lon'] : null;

$count = isset($_GET['count']) ? $_GET['count'] : 0;

$min_results = $count ? $count / 2.5 : 15;
$max_results = 5000;

if($count > $max_results)
	$count = $max_results;

if($count && $count < $max_results)
	$max_results = $count;

if($count < $min_results)
	$count = $min_results;



//limit to 100 results
$sql = "SELECT placeID, placename,
				address, street, state, city, country, phone, url,
				placePhoto, tdsNumber, comment, lat, longi, userID
		FROM tbl_place
		WHERE MBRCONTAINS(GeomFromText(?), location)
		LIMIT $max_results";
		
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
	//figure out lat delta, correct for curvature of earth

	//create bounding box -- close enough to a radius
	$swLat = $southwest_lat;
	$swLon = $southwest_lon;
	$neLat = $northeast_lat;
	$neLon = $northeast_lon;

	$poly = "POLYGON(($swLat $swLon, $neLat $swLon, $neLat $neLon, $swLat $neLon, $swLat $swLon))";

	$stmt->bind_param('s', $poly);

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
} while(count($rows) <= $min_results && ($i < 2));


//allow jsonp
if(isset($_REQUEST['callback']))
	echo $_REQUEST['callback'] . '(' . json_encode($rows) . ')';
else
	echo json_encode($rows);
?>