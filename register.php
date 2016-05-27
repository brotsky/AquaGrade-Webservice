<?php

function register($params=array())
{
	global $outputjson;
	$jsonArray = array();
	$jsonval = array();
	$conn = new DBConnection();
	$mysql = $conn->connect();
	
	$username=$params['username'];
	$password=$params['password'];
	$email=$params['email'];

	$sql="SELECT * FROM `users` WHERE email='".$email."'";

	$result = mysqli_query($mysql,$sql);
	$affected_rows=mysqli_affected_rows($mysql);
	if($affected_rows > 0)
	{
		$outputjson['Message']='You are already registered';
	}
	else
	{
		$sql1="SELECT * FROM `users` WHERE username='".$username."'";
		$result1 = mysqli_query($mysql,$sql1);
		$affected_rows1=mysqli_affected_rows($mysql);
		if($affected_rows1 > 0)
		{
			$outputjson['Message']='This username already used by another person';
		}
		else
		{
			$sql2="Insert into `users`(`username`,`password`,`email`) values ('".$username."','".$password."','".$email."')";
		
			$result2 = mysqli_query($mysql,$sql2);
			$affected_rows2=mysqli_affected_rows($mysql);
			if($affected_rows2 > 0)
			{
				$sql3="SELECT * FROM `users` WHERE email='".$email."' and password='".$password."'";
		
				$result3 = mysqli_query($mysql,$sql3);
				$affected_rows3=mysqli_affected_rows($mysql);
				if($affected_rows3 > 0)
				{
					while($row=mysqli_fetch_array($result3))
					{
						$outputjson['Message'] ="Register successfully";
					}
				}
				else
				{
					$outputjson['Message']='You are not registered'; 
				}
			}
			else
			{
				$outputjson['Message']='You are not registered'; 		
			}
		}
	}
		
}

?>