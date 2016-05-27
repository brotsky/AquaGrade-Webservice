<?php

// Set the formate of webservice for getting JSON responce

global $ops_withoutlogin , $operation_list ;

$ops_withoutlogin = array();

//'questionid' =>array('1,2');

$operation_list = array(

							   
						);	   
// function for retrieving the data
function oplist($params = array()){ 

	global $outputjson , $operation_list;


	echo "<pre>"; print_r($operation_list);


	// $outputjson = $operation_list;  

}



?>