<?php
function manager($params=array())
{
    
    
		
/*

	$image='http://trevorblanding.com/webservice/upload_image/thumb/';
	while($stmt->fetch())
	{
		if(!isset($added[$placeID]))
		{
			$row = new stdClass;
			
			$row->FoursquareID = $fsqid;
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
			$row->DateCreated = $DateCreated;
			
			$rows[] = $row;
			$added[$placeID] = true;
		}
	}

echo json_encode($rows);
    
    */
    
    
	global $outputjson;
	$jsonArray = array();
	$jsonval = array();
	$conn = new DBConnection();
	$mysql = $conn->connect();
	

	$sql = "SELECT placeID, placename,
				address, street, state, city, country, phone, url,
				placePhoto, tdsNumber, comment, lat, longi, userID, FsqId, DateCreated
		FROM tbl_place
		ORDER BY placeID DESC
		LIMIT 20";
	

	//echo($sql);
	$result = mysqli_query($mysql,$sql);
	$affected_rows=mysqli_affected_rows($mysql);
	
		
	if($affected_rows > 0)
	{
		while($row=mysqli_fetch_array($result))
		{
			$jsonArray['placeID'] =$row['placeID'];
		//	$jsonArray['UserID'] =$row['userID'];
		//	$jsonArray['Username'] =$row['username'];
		//	$jsonArray['Password'] =$row['password'];
		//	$jsonArray['Email'] =$row['email'];
  
			$jsonval[]=$jsonArray;
		}
		$outputjson= $jsonval;
	}
	else
	{
		$outputjson['Message']='No record found'; 		
	}
}