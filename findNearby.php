<?php @session_start();

require_once('config.inc.php');

header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');


$center_lat = isset($_REQUEST['lat']) ? $_REQUEST['lat'] : null;
$center_lon = isset($_REQUEST['lon']) ? $_REQUEST['lon'] : null;



$radius_levels = array(0.5, 1, 2, 3, 5, 10, 20, 50);

$count = isset($_GET['count']) ? $_GET['count'] : 0;

$min_results = $count ? $count / 2.5 : 15;
$max_results = 2000;

if($count > $max_results)
	$count = $max_results;

if($count && $count < $max_results)
	$max_results = $count;

if($count < $min_results)
	$count = $min_results;


if(isset($_REQUEST['userID']) && $_REQUEST['userID'] != 0) {
    $userID = intval( $_REQUEST['userID'] );
    $sql = "SELECT placeID, placename,
				address, street, state, city, country, phone, url,
				placePhoto, tdsNumber, comment, lat, longi, userID, FsqId
		FROM tbl_place
		WHERE MBRCONTAINS(GeomFromText(?), location)
		    AND userID = $userID
		LIMIT $max_results";
} else {
//limit to 100 results
    $sql = "SELECT placeID, placename,
				address, street, state, city, country, phone, url,
				placePhoto, tdsNumber, comment, lat, longi, userID, FsqId
		FROM tbl_place
		WHERE MBRCONTAINS(GeomFromText(?), location)
		LIMIT $max_results";
}
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
	$radius = $radius_levels[$i];

	//figure out lat delta, correct for curvature of earth
	$lat_delta = $radius / 69.1;
	$lon_delta = $radius / (69.1 * cos($center_lat / 57.3));

	//create bounding box -- close enough to a radius
	$swLat = $center_lat - $lat_delta;
	$swLon = $center_lon - $lon_delta;
	$neLat = $center_lat + $lat_delta;
	$neLon = $center_lon + $lon_delta;

	$poly = "POLYGON(($swLat $swLon, $neLat $swLon, $neLat $neLon, $swLat $neLon, $swLat $swLon))";

	$stmt->bind_param('s', $poly);

	$stmt->bind_result($placeID, $placename,
					$address, $street, $state, $city, $country, $phone, $url,
					$placePhoto, $tdsNumber, $comment, $lat, $lon, $userID, $venueID);

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
			$row->FsqId = $venueID;
			
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

die();
?>