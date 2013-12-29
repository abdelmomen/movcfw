<?php


if(!session_id())session_start();

require_once 'developer.php';
	
if(isset($_REQUEST['table']))
{
	$table_name=$_REQUEST['table'];
	
	// set the $status
	if(
	isset($_REQUEST['write_entity'])||isset($_REQUEST['write_bean'])
	||isset($_REQUEST['write_controller'])||isset($_REQUEST['write_action'])
	)
	$_SESSION['table:'.$_REQUEST['table']]="yes";
	print "<h3>$table_name</h3>";
	
	$status=$_SESSION['table:'.$table_name.''];
	
	//print "<a href='?developer=yes&write=1&table=$table_name'>Writen Data Files </a> :  $status <br/><hr/>";
	?>
	
	Writen Data Files : <?=$status?>  
<p>
	<a href="<?=BASE_URL?>developer" style="font-size: 120%;"> << Back</a>
</p>

	<hr/>
	
	<form action="" method="post">
	
<input type="checkbox" name="write_entity" value="" checked="checked"/> write entity || id field name = 
<input type="text" name="field" value="id" /><br />
<input type="checkbox" name="write_bean" value=""  checked="checked"/> write form bean <br />
<input type="checkbox" name="write_controller" value="" checked="checked"/> write controller <br />
<input type="checkbox" name="write_action" value="" checked="checked"/> write action <br/>
<!--  <input type="checkbox" name="write_xml" value="" /> write XML <br/>  -->
<input type="submit" value=".: Write :." />  <br/>

	</form>
	
	<?php 
	generateDataObjects();
}
else
{
	if(defined('SQLITE_PATH'))
	{
		$db=new Database();
		$tables=$db->query("SELECT name FROM sqlite_master WHERE type = 'table'");
		
		foreach ($tables as $table)
		{
			if(!isset($_SESSION['table:'.$table['name']]))
			$_SESSION['table:'.$table['name']]="no";
		}
	}
	else
	{

		$name=DB_NAME;
	
		mysql_connect(DB_HOST, DB_USER, DB_PASS);
		
		mysql_query("use $name ");
	
		// Intialize  
		$tables=mysql_query("SHOW TABLES FROM $name");
		
		while ($row = mysql_fetch_row($tables))
		{
			if(!isset($_SESSION['table:'.$row[0]]))
			$_SESSION['table:'.$row[0]]="no";
		}
	}
	
	drawTables();
}//end else 



function drawTables(){
	

	if(defined('SQLITE_PATH'))
	{

		$db=new Database();
		$tables=$db->query("SELECT name FROM sqlite_master WHERE type = 'table'");
		
		foreach ($tables as $table)
		{
			$name=$table['name'];
			$status=$_SESSION['table:'.$name];
			print "<a href='?developer=yes&table=$name'> $name </a> ======> Writen Data Files : $status <br/><hr/>";
		}
	}
	else
	{
		$name=DB_NAME;
		
		mysql_connect(DB_HOST, DB_USER, DB_PASS);
		
		mysql_query("use $name ");
	
		$tables=mysql_query("SHOW TABLES FROM $name");
		while ($row = mysql_fetch_row($tables)) {
			$status=$_SESSION['table:'.$row[0]];
			print "<a href='?developer=yes&table=$row[0]'> $row[0] </a> ======> Writen Data Files : $status <br/><hr/>";
		}
	}
}

function generateDataObjects()
{
	$columns=array();
		
	if(defined('SQLITE_PATH'))
	{
		$table_is=$_REQUEST['table'];
		$db=new Database();
		
		$results = $db->query("PRAGMA table_info(" . $table_is . ")");
		
		foreach ($results as $row)
		{
			array_push($columns, $row['name']);
		}
	}
	else
	{
		$table_is=$_REQUEST['table'];
		
		$results = mysql_query("SHOW COLUMNS FROM $table_is");
		
		while ($col = mysql_fetch_assoc($results))
		{
		    $columns[]=$col['Field'];
		}
	}

	print "<textarea cols='150' rows='25'>";
	
	//print generateDTOXML($table_is);
	//print generateXML($table_is,isset($_REQUEST['write_xml']));

	print generateSimpleForm($table_is,$columns);
	
	print generateTable($table_is,$columns);
	
	print "</textarea><hr/>";
	
	print "<textarea cols='100' rows='40'>";
	
	if(isset($_REQUEST['write_entity'])&&isset($_REQUEST['field']))
		$id_field=$_REQUEST['field'];
	else
		$id_field="";
		
	print generateEntity($table_is,$columns,isset($_REQUEST['write_entity']),$id_field);
	
	print generateBean($table_is,$columns,isset($_REQUEST['write_bean']));
	
	print generateController($table_is,isset($_REQUEST['write_controller']));
	
	print generateAction($table_is,isset($_REQUEST['write_action']));
	
	print "</textarea>";
	
}
