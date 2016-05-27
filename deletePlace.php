<?php

function deletePlace($params=array())
{
	global $outputjson;
	$jsonArray = array();
	$jsonval = array();
	$conn = new DBConnection();
	$mysql = $conn->connect();
	
	$placeID=$params['placeId'];
	
	//$image='/webservice/upload_image/thumb/';

	$sql3="Delete From `tbl_place` where placeID='".$placeID."' ";
	
	//echo($sql3);
	$result3 = mysqli_query($mysql,$sql3);
	$affected=mysqli_affected_rows($mysql);
	
	if($affected > 0)
	{
	      $sql5="Select * From `tbl_place` where placeID='".$placeID."' ";
	      $result4 = mysql_query($mysql,$sql5);
	     $row=mysql_fetch_array($result4);
             //unlink('http://aquagrade.com/webservice/upload_image/thumb/'.$row['placePhoto']);
             //unlink('http://aquagrade.com/webservice/upload_image/'.$row['placePhoto']);
               $outputjson['Message']="History place deleted successfully";
	}
	else
	{
		$outputjson['Messsage'] ="Delete unsuccessful";
	}
}

?>