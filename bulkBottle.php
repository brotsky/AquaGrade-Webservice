<pre>
<?php

require_once('config.inc.php');
include('SimpleImage.php');

set_time_limit(0);


$i = 0;


$f = fopen('aq_bottles.csv', 'r');

while($row = fgetcsv($f))
{
	if($i++ == 0)
		continue;
	
	$name = $row[1];
	$tds = $row[2];
	
	$phone_number = $row[4] == "NULL" ? null : $row[4];
	$website = $row[5] == "NULL" ? null : $row[5];
	$notes = $row[6] == "NULL" ? null : $row[6];
	
	$url_photo = str_replace(' ', '_', $name) . '_1.jpg';
	
	if(file_exists("bottles_import/$url_photo"))
	{
//		echo "-\t$url_photo<br/>";
		addBottle($name, $tds, $phone_number, $notes, $website, $url_photo);
	}
	else
	{
		$url_photo = 'bottles_import/' . str_replace(' ', '_', $name) . '.jpg';
		
		if(file_exists("bottles_import/$url_photo"))
		{
//			echo "-\t$url_photo<br/>";
			addBottle($name, $tds, $phone_number, $notes, $website, $url_photo);
		}
		else
		{
			echo "X\t$name<br/>";
		}
	}
}

fclose($f);

die;



function addBottle($name, $tds, $phone_number, $notes, $website, $url_photo, $resize_image = true)
{
	global $mysqli;
	
	//limit to 50 results
	$sql = "INSERT INTO bottle_tds
			(name, tds, phone_number, website, notes, url_photo)
			VALUES
			(?, ?, ?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE
			tds = ?, phone_number = ?, website = ?, notes = ?, url_photo = ?";
			
	$stmt = $mysqli->prepare($sql);

	if($mysqli->error || !$stmt)
	{
		error_log("mysqli error while preparing: " . $mysqli->error);
		echo $mysqli->error;
	}
	

	if($resize_image && !file_exists("bottles/$url_photo"))
	{
		$image = new SimpleImage();
		$image->load("bottles_import/$url_photo");
		$image->resizeToHeight(800);
		$image->save("bottles/$url_photo");
	}

	
	$url_photo = "bottles/$url_photo";
	

	$stmt->bind_param('sissss' . 'issss', $name, $tds, $phone_number, $website, $notes, $url_photo,
								$tds, $phone_number, $website, $notes, $url_photo);
	$stmt->execute();

	if($mysqli->error)
	{
		error_log("mysqli error while executing: " . $mysqli->error);
		echo $mysqli->error;
	}
	
	$stmt->store_result();
	
	if($stmt->affected_rows > 0)
	{
		echo "<b>Added $name ($tds)!</b><br/>";
		
		$name = null;
		$tds = null;
		$website = null;
		$phone_number = null;
		$url_photo = null;
		$notes = null;
	}
	else
	{
//		echo '<b>ERROR</b><br/>';
	}
}
?>
</pre>