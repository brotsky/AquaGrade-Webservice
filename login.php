<?php

function login($params=array())
{
	global $outputjson;
	$jsonArray = array();
	$jsonval = array();
	$conn = new DBConnection();
	$mysql = $conn->connect();
	
	$password=$params['password'];
	$username=$params['username'];

	$sql="SELECT * FROM `users` WHERE username='".$username."' and password='".$password."'";
	

	//echo($sql);
	$result = mysqli_query($mysql,$sql);
	$affected_rows=mysqli_affected_rows($mysql);
	if($affected_rows > 0)
	{
		while($row=mysqli_fetch_array($result))
		{
			$jsonArray['Message'] ="Login successfully";
			$jsonArray['UserID'] =$row['userID'];
			$jsonArray['Username'] =$row['username'];
			$jsonArray['Password'] =$row['password'];
			$jsonArray['Email'] =$row['email'];
  
			$jsonval=$jsonArray;
		}
		$outputjson= $jsonval;
	}
	else
	{
		$outputjson['Message']='No record found'; 		
	}
}

?>