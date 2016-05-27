<?php @session_start();

require_once('config.inc.php');



$id_bottle = isset($_REQUEST['id_bottle']) && strlen($_REQUEST['id_bottle']) > 0 ? $_REQUEST['id_bottle'] : null;
$delete = isset($_REQUEST['delete']) ? 1 : 0;

$name = isset($_REQUEST['name']) && strlen($_REQUEST['name']) > 0 ? $_REQUEST['name'] : null;
$tds = isset($_REQUEST['tds']) && strlen($_REQUEST['tds']) > 0 ? $_REQUEST['tds'] : null;
$phone_number = isset($_REQUEST['phone_number']) && strlen($_REQUEST['phone_number']) > 0 ? $_REQUEST['phone_number'] : null;
$website = isset($_REQUEST['website']) && strlen($_REQUEST['website']) > 0 ? $_REQUEST['website'] : null;
$url_photo = isset($_REQUEST['url_photo']) ? $_REQUEST['url_photo'] : null;
$notes = isset($_REQUEST['notes']) && strlen($_REQUEST['notes']) > 0 ? $_REQUEST['notes'] : null;

if($delete && $id_bottle)
{
	$sql = "DELETE FROM bottle_tds
			WHERE id_bottle = ?";
	
	$stmt = $mysqli->prepare($sql);
	$stmt->bind_param('i', $id_bottle);
	$stmt->execute();
	$stmt->store_result();
	
	if($stmt->affected_rows > 0)
	{
		header('Location: bottleManager.php');
		die;
	}
	else
	{
		echo "ERROR while deleting: $mysqli->error<br/>";
	}
	
	$stmt->close();
}
else if($name && $tds)
{
	//limit to 50 results
	$sql = "INSERT INTO bottle_tds
			(name, tds, phone_number, website, notes, url_photo)
			VALUES
			(?, ?, ?, ?, ?, ?)";
			
	$stmt = $mysqli->prepare($sql);

	if($mysqli->error || !$stmt)
	{
		error_log("mysqli error while preparing: " . $mysqli->error);
		echo $mysqli->error;
	}
	
	

	if($_FILES)
	{
		$dir_local = "bottles";
		
		foreach($_FILES as $file)
		{
			//TODO: check for file quota exceeded or file too large

			$filename = $file['name'];
			$filename_full = "$dir_local/$filename";

			//TODO: check if file exists, but do what in that case?
			if(file_exists($filename_full))
			{
				echo 'FYI, that image was already uploaded.';
			}
			else
			{
				@move_uploaded_file($file['tmp_name'], $filename_full);
				@chmod($filename_full, 0777);
				
				include('SimpleImage.php');
				$image = new SimpleImage();
				$image->load($filename_full);
				$image->resizeToHeight(800);
				$image->save($filename_full);
			}
			
			$url_photo = $filename_full;
			
			break; //only do 1
		}
	}


	$stmt->bind_param('sissss', $name, $tds, $phone_number, $website, $notes, $url_photo);
	$stmt->execute();

	if($mysqli->error)
	{
		error_log("mysqli error while executing: " . $mysqli->error);
		echo $mysqli->error;
	}
	
	$stmt->store_result();
	
	if($stmt->affected_rows > 0)
	{
		echo "<b>Added $name ($tds)!</b>";
		
		$name = null;
		$tds = null;
		$website = null;
		$phone_number = null;
		$url_photo = null;
		$notes = null;
	}
	else
	{
		echo '<b>ERROR</b>';
	}
}
else
{
	echo '<b>Please enter name and TDS</b>';
}

?>


<style>
div
{
	display: inline-block;
	margin: 6px;
	padding: 4px;
	
	border: 1px solid black;
}

img
{
	display: block;
}
</style>



<br/>
<form method="post" enctype="multipart/form-data">
	<input type="text" name="name" placeholder="Bottle Name" value="<?php echo $name ?>"/>*<br/>
	<input type="text" name="tds" placeholder="TDS Reading" value="<?php echo $tds ?>"/>*<br/>
	<input type="text" name="website" placeholder="Website" value="<?php echo $website ?>"/><br/>
	<input type="text" name="phone_number" placeholder="Phone Number" value="<?php echo $phone_number ?>"/><br/>
	<input type="text" name="url_photo" placeholder="URL to photo" value="<?php echo $url_photo ?>"/> OR upload one: <input type="file" name="photo" id="photo" accept="image/*"/><br/>
	
	<textarea name="notes" placeholder="Notes" rows="5" cols="20"><?php echo $notes; ?></textarea><br/>
	
	<br/>
	<input type="submit" value="Add to Bottle Database"/>
</form>
<br/>
<br/>

<?php
//limit to 50 results
$sql = "SELECT id_bottle, name, tds, phone_number, website, notes, url_photo, date_entered
		FROM bottle_tds
		ORDER BY LOWER(name)";
		
$stmt = $mysqli->prepare($sql);

if($mysqli->error || !$stmt)
{
	error_log("mysqli error while preparing: " . $mysqli->error);
	die(json_encode(0));
}


$listings = array();

$stmt->bind_result($id_bottle, $name, $tds, $phone_number, $website, $notes, $url_photo, $date_entered);

$stmt->execute();

if($mysqli->error)
{
	error_log("mysqli error while executing: " . $mysqli->error);
	die(json_encode(0));
}

while($stmt->fetch())
{
	$listing = new stdClass;
	
	$listing->id_bottle = $id_bottle;
	$listing->name = $name;
	$listing->tds = $tds;
	$listing->phone_number = $phone_number;
	$listing->website = $website;
	$listing->notes = $notes;
	$listing->url_photo = $url_photo;
	$listing->date_entered = $date_entered;
	
	
	echo '<div><form method="post">'
			. '<input type="hidden" name="id_bottle" value="' . $id_bottle . '"/>'
			. '<img src="' . $url_photo . '" title="' . $name . '" height="100">'
			. 'Name: ' . $name . '<br/>'
			. 'TDS: ' . $tds . '<br/>'
			. 'Phone #: ' . $phone_number . '<br/>'
			. 'Website: ' . $website . '<br/>'
			. 'Notes: ' . $notes . '<br/>'
			. '<button name="delete">Delete</button>'
		 .'</form></div>';
}
?>