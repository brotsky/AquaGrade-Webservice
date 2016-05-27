<?php
 
 // Main file getting the responce in JSON formate

//error_reporting(0);
error_reporting(E_ALL);
ini_set("display_errors", "On"); 
	
function json_encode1($data) {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return $data;
            case 'string':
                return '"' . addslashes($data) . '"';
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode1($value);
                    $output_associative[] = json_encode1($key) . ':' . json_encode1($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            default:
                return ''; // Not supported
        }
    }

include_once('config.php');		  	 // This file include for your server connection 
include_once('dbconnection.php');	 // This file include for your database connection 


if($_REQUEST['op']=='login'){include("login.php");}
if($_REQUEST['op']=='getUserInfo'){include("getUserInfo.php");}
if($_REQUEST['op']=='register'){include("register.php");}
if($_REQUEST['op']=='postPlace'){include("postPlace.php");}
if($_REQUEST['op']=='getPlace'){include("getPlace.php");}
if($_REQUEST['op']=='getMoreRecentPlace'){include("getMoreRecentPlace.php");}
if($_REQUEST['op']=='getOnePlace'){include("getOnePlace.php");}
if($_REQUEST['op']=='getOnePlaceFourSquare'){include("getOnePlaceFourSquare.php");}
if($_REQUEST['op']=='deletePlace'){include("deletePlace.php");}
if($_REQUEST['op']=='updatePlace'){include("updatePlace.php");}
if($_REQUEST['op']=='manager'){include("manager.php");}

	global $outputjson , $is_login ; 

$op = '';


if(!isset($_REQUEST['op']))
{   
    $outputjson['error'] = "Operation missing";   
}
else
{
	$op=$_REQUEST['op'];
}
if(!isset($_REQUEST['params']))
{  
    $_REQUEST['params'] =  array();  
}

$valid=true;


@$op = $_REQUEST['op'];
unset($_REQUEST['op']);
$params = $_REQUEST;

if($valid){
	
	if(is_callable($op) ){
		 
		 $op($params);
		 
		 
	} else {
		
		$outputjson['error'] = " Operation does not exists" ;
		
	}
	
}

// Remove the null value while get the result from the database to set the JSON formate

function removeNULL($input)
{
	$return = array();

	foreach ($input as $key => $val)
	{
		if( is_array($val) )
		{
			$return[$key] = removeNULL($val);
		}
		else
		{	
			if($val == NULL)
			{
				$return[$key] = "";
			}
			else
			{
				$return[$key] = $val;
			}
			
		}
	}
	return $return;          
} 



$temp_outputjson = removeNULL($outputjson);

echo json_encode1($temp_outputjson);

?>