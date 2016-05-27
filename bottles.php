<?php @session_start();

require_once('config.inc.php');

?>

<html>
<head>
<style>
img
{
	float: left;
	height: 64px;
	vertical-align: middle;
	margin-right: 10px;
}
</style>

<link rel="stylesheet" type="text/css" media="all" href="css/jquery.dataTables.css"/>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
<script type="text/javascript">if(typeof jQuery=='undefined'){document.write(unescape("%3Cscript type='text/javascript' src='jquery-1.10.2.min.js' %3E%3C/script%3E"));}</script>
<script type="text/javascript" src="jquery.dataTables.min.js"></script>

</head>

<body>
<table id="bottles">
<thead>
	<tr>
		<th>Bottle</th>
		<th width="20%">TDS</th>
		<th width="25%">Notes</th>
	</tr>
</thead>

<tbody>
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
	
	
	echo '<tr data-id_bottle="' . $id_bottle . '">'
			. '<td><img src="' . $url_photo . '" title="' . $name . '" height="100"> ' . $name . '<br/>' . $phone_number . '<br/>' . $website . '</td>'
			. '<td align="center">' . $tds . '</td>'
			. '<td>' . $notes . '</td>'
		. '</tr>';
			
	/*
			. 'TDS: ' . $tds . '<br/>'
			. 'Phone #: ' . $phone_number . '<br/>'
			. 'Website: ' . $website . '<br/>'
			. 'Notes: ' . $notes . '<br/>';
			*/
}
?>
</tbody>
</table>

<script>
$(document).ready(function()
{
    $('#bottles').dataTable();
});

</script>

</body>
</html>