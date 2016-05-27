<?php

function updatePlace($params=array())
{
	global $outputjson;
	$jsonArray = array();
	$jsonval = array();
	$conn = new DBConnection();
	$mysql = $conn->connect();
	
	$placeID=$params['placeId'];
	$tds=trim($params['tds']);
	$comment=urldecode(replace(trim($params['comment'])));
	
	//$image='http://www.dignizant.com/aqua/webservice/upload_image/thumb/';

	$sql3="UPDATE `tbl_place` set comment='".$comment."',tdsNumber='".$tds."' where placeID='".$placeID."' ";
	
	//echo($sql3);
	$result3 = mysql_query($sql3);
	$affected=mysql_affected_rows();
	
	if($result3 ==1)
	{
		$outputjson['Message']="Place updated successfully";
	}
	else
	{
		$outputjson['Message'] ="Update unsuccessfull";
	}
}
function replace($val)
{
	$str=str_replace("'","^",$val);
	return $str;
}

?>