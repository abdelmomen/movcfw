<?php

function copyObjectVars($from,&$to)
{
	$vars=get_object_vars ( $from );

	foreach($vars as $key => $value)
	{
		if(property_exists(get_class($to), $key))
			$to->$key=$value;
	}
}

function deleteFirstChar( $string ) {
	return substr( $string, 1 );
}

function movc_autoload($class_name) {

	if(stripos( $class_name,"bean")!==false && file_exists(APP_DIR.BEAN_DIR.$class_name.".class.php"))
		req_once(APP_DIR.BEAN_DIR.$class_name.".class.php");
	
	elseif(stripos( $class_name,"model")!==false && file_exists(APP_DIR.MODEL_DIR.$class_name.".class.php"))
		req_once(APP_DIR.MODEL_DIR.$class_name.".class.php");
		
	elseif(stripos( $class_name,"entity")!==false && file_exists(APP_DIR.ENTITY_DIR.$class_name.".class.php"))
		req_once (APP_DIR.ENTITY_DIR.$class_name.".class.php");
	
	elseif(file_exists(APP_DIR.ACTION_DIR.$class_name.".class.php"))
		req_once(APP_DIR.ACTION_DIR.$class_name.".class.php");
	else 
		throw new Exception("$class_name cannot be autoloaded");
}

function creatProbArr($arrayProbs)
{
	$returnedArr=array();
	$probs= explode(',', $arrayProbs);

	foreach ($probs as $prob)
		$returnedArr[$prob]=null;

	return $returnedArr;
}

function redirect($to=null)
{
	if($to)
		header( 'Location:'.$to );
	else
		header( 'Location:'.BASE_URL );
	die();
}


function log_n($object=0,$logString=0){

	if(is_string($object))
	{
		$logString=$object;
	}

	if($object===0&&$logString===0)
	{
		error_log("no given parameters \n",3,LOG_PATH);
		return 0;//exit for now
	}

	$class="";

	if(is_object($object))
		$class=get_class($object);

	error_log($class."==>".$logString,3,LOG_PATH);
}

function log_r($object){
	ob_start(); //Start buffering
	print_r($object); //print the result
	print "\n";
	$output = ob_get_contents(); //get the result from buffer
	ob_end_clean(); //close buffer
	error_log($output,3, LOG_PATH);
}


function str_enclose($string){
	return "'".$string."'";
}

function str_enclose_like($string){
	return "'%".$string."%'";
}

function debg($something)
{
	print "<div style='border:1px solid ;direction:ltr;font-size: 14px;padding: 10px;back;background-color:#FEE;'><pre>";
	if(is_object($something)||is_array($something))
	{
		print_r($something);
	}
	else
		print($something);
	
	print"</pre></div>";
}

function dead($something)
{
	debg($something);
	die();
}

function print_i($something,$someone=null)
{
	print  " $something --- $someone <br/>";
}

function print_x( $e )
{
	$trace=$e->getTraceAsString();
	$asHtml = str_replace("\n", "\n<br/>", $trace);
	print '<p><b>Caught exception:</b><br/>>'.$asHtml. "\n</p>";
	print '<p><b>with message:</b> <h3 style="color:red;padding-left: 40px" > '. $e->getMessage()."</h3></p>";
}

function is_valid_email($email) {
	$result = TRUE;
	//TDOO eregi is deprecated
	if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email)) {
		$result = FALSE;
	}
	return $result;
}

//TDOO remove req_once

function req_once($file)
{
	if(file_exists($file)){
		require_once($file);
	}
	else {
		$msg="File -".$file."- doesn't exist";
		throw(new Exception($msg));
	}
}

function last_node(array $arr)
{
	$rev_arr=array_reverse($arr);
	if($rev_arr[0])return $rev_arr[0];
	elseif($rev_arr[1])return $rev_arr[1];
	else throw new Exception("empty last two Nodes");
}

function selected_element(array $arr,$proberty,$select){
	
	$ret_arr=array();
	
	foreach ($arr as $k =>$one){
		
		if(is_object($one) )
		{
			if($one->$proberty == $select)
				$one->_selected="selected='selected'";
			else 
				$one->_selected="";
		}

		
		elseif(is_array($one))
		{
			if($one[$proberty] == $select)
				$new=array("_selected"=>"selected='selected'");
			else
				$new=array("_selected"=>"");
			
			$one=array_merge($one,$new);
		}

		$ret_arr[$k]=$one;
	}
	
	return $ret_arr;
}
