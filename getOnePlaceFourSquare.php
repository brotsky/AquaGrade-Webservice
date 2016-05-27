<?php

function getOnePlaceFourSquare($params=array())
{
	global $outputjson;
	$jsonArray = array();
	$jsonval = array();
	$conn = new DBConnection();
	$mysql = $conn->connect();
	
	
    $fsqid = $params['fsqid'];
    
    $fsqid = explode(",", $fsqid);
    $fsqid_formatted = "";
    for($i = 0 ; $i < sizeof($fsqid) ; $i++) {
        $fsqid[$i] = "'" . $fsqid[$i] ."'";
        if($i > 0)
            $fsqid[$i] = "," .$fsqid[$i];
            
        $fsqid_formatted .= $fsqid[$i];
    }
	
	//$image='http://aquagrade.com/webservice/upload_image/thumb/';
	$image='http://trevorblanding.com/webservice/upload_image/thumb/';
	

	$sql3="Select * from `tbl_place` where FsqId='".$fsqid[0]."'";
	
	
	
	$sql3 = "select * from tbl_place where fsqid in (".$fsqid_formatted.")";
		
	$result3 = mysqli_query($mysql,$sql3);
	$affected=mysqli_affected_rows($mysql);
	
	if($affected > 0)
	{
		while($row=mysqli_fetch_array($result3))
		{
			$jsonArray['DateCreated'] =$row['DateCreated'];
			$jsonArray['FsqId'] =$row['FsqId'];
			$jsonArray['PlaceID'] =$row['placeID'];
			$jsonArray['UserID'] =$row['userID'];
			$jsonArray['Placename'] =replace($row['placename']);
			$jsonArray['Street'] =replace($row['street']);
			$jsonArray['State'] =replace($row['state']);
			$jsonArray['Country'] =replace($row['country']);
			$jsonArray['City'] =replace($row['city']);
			$jsonArray['Address'] =replace($row['address']);
			$jsonArray['FullAddress'] =replace($row['fulladdress']);
			$jsonArray['TdsNumber'] =$row['tdsNumber'];
			$jsonArray['Lat'] =$row['lat'];
			$jsonArray['Longi'] =$row['longi'];
			$jsonArray['Phone'] =$row['phone'];
			$jsonArray['Url'] =$row['url'];
			
			if($row['placePhoto']=="")
			{
				$jsonArray['PlacePhoto'] ="";
			}
			else
			{
				$jsonArray['PlacePhoto'] =$image.$row['placePhoto'];	
			}
			
			$jsonArray['TdsNumber'] =$row['tdsNumber'];
			
			
			$jsonArray['Comment'] =	urlencode(replace(trim($row['comment'])));
			$jsonval[]=$jsonArray;
		}
		$outputjson['Response']= $jsonval;
	}
	else
	{
		$outputjson['Messsage'] ="No more history found";
	}
}

function replace($val)
{
	return $val;
	//$str=str_replace("%0A","%0A",$val);
	$str=str_replace("^","'",$val);
	return $str;
}

?>
