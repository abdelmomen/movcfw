<?php
//TDOO get folders from config
function generateEntity($tableName,$all_vars,$is_write=null,$id=null)
{
	
$class_name		=getCamelCase($tableName);

$enitiyName		=$class_name."Entity";
$beanName		=$class_name."Bean";

$fo="<?php\n\nclass $class_name"."Entity extends Entity\n{ \n";

	foreach ($all_vars as $small){
		$fo.= "\n		var \$$small;";
	}
	
if(!$id)$id='id';

$fo.="\n";
	
$fo.=<<<HERE
		
    public function __construct() 
    {
		parent::__construct('$tableName','$id');
    }
	
}//end $class_name Entity
\n
HERE;

	if($is_write==1)
	{
		chdir(APP_PATH."app/entity");
		
		$myFile = $class_name."Entity.class.php";
		
		$fh = fopen($myFile, 'w') or die("can't open file");
		
		fwrite($fh, $fo);
		
		fclose($fh);
	}

return $fo;
}


function generateBean($tableName,$all_vars,$is_write=null){
	
$class_name		=getCamelCase($tableName);
$enitiyName		=$class_name."Entity";

///
$fo = "<?php\nclass $class_name"."Bean extends FormBean\n{ \n";

	foreach ($all_vars as $small){
		$fo.="\n	var \$$small;";
	}
	
$fo.="\n";

$fo.=<<<HERE

}//end bean\n
HERE;

	if($is_write==1)
	{
		chdir(APP_PATH."app/beans");
		
		$myFile = $class_name."Bean.class.php";
		
		$fh = fopen($myFile, 'w') or die("can't open file");
		
		fwrite($fh, $fo);
		
		fclose($fh);
	}

return $fo;
}


function generateXML($tableName,$is_write=null){
	
	$class_name		=getCamelCase($tableName);
	$beanName		=$class_name."Bean";
	$entityName		=$class_name."Entity";
	$actionClass	=$class_name."Action";
	$ctrlName		=$class_name."Ctrl";

$rest=<<<HERE

<!-- XML under entities Node -->
	
	<entity name="$entityName" path="app/models/$entityName.class.php" />

<!-- XML under controllers Node -->
	
	<controller name="$ctrlName" path="app/controllers/$ctrlName.class.php" />
	
<!-- XML under beans Node -->
		
	<bean name="$beanName" path="app/beans/$beanName.class.php"/>

<!-- XML under actions Node -->
	
	<action name="$actionClass" bean="$beanName" controller="$ctrlName" path="app/actions/$actionClass.class.php" />

<!-- XML under operations Node -->
	
	<operation url="$tableName" do="$actionClass::"  />
\n\n
HERE;

	if($is_write)
	{
		
		$dom = new DOMDocument('1.0');
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		$dom->load("app/app.xml");
		
		/* @var $tag DOMElement */
		
		// Entites Work
		$entities = $dom->getElementsByTagName('entities')->item(0);

		$tag=$dom->createElement("entity");
		$tag->setAttribute("name",$entityName);
		$tag->setAttribute("path","app/models/$entityName.class.php");
	
		$entities->appendChild($tag);
		
		// Controllers Work
		$controllers = $dom->getElementsByTagName('controllers')->item(0);
		
		$tag=$dom->createElement("controller");
		$tag->setAttribute("name",$ctrlName);
		$tag->setAttribute("path","app/controllers/$ctrlName.class.php");
		
		$controllers->appendChild($tag);
		
		// beans Work
		
		$beans = $dom->getElementsByTagName('beans')->item(0);
		
		$tag=$dom->createElement("bean");
		$tag->setAttribute("name",$beanName);
		$tag->setAttribute("path","app/beans/$beanName.class.php");
		
		$beans->appendChild($tag);
		
		// actions Work

		$actions = $dom->getElementsByTagName('actions')->item(0);
		
		$tag=$dom->createElement("action");
		$tag->setAttribute("name",$actionClass);
		$tag->setAttribute("bean",$beanName);
		$tag->setAttribute("controller",$ctrlName);
		$tag->setAttribute("path","app/actions/$actionClass.class.php");
		
		$actions->appendChild($tag);
		
		// operations Work
		
		$operations = $dom->getElementsByTagName('operations')->item(0);
		
		$tag=$dom->createElement("operation");
		$tag->setAttribute("url","$tableName");
		$tag->setAttribute("do","$actionClass::");		
		$operations->appendChild($tag);
		
		$tag=$dom->createElement("operation");
		$tag->setAttribute("url","$tableName-save");
		$tag->setAttribute("do","$actionClass::save");		
		$operations->appendChild($tag);
		
		$tag=$dom->createElement("operation");
		$tag->setAttribute("url","$tableName-data");
		$tag->setAttribute("do","$actionClass::data");		
		$operations->appendChild($tag);
		
		$tag=$dom->createElement("operation");
		$tag->setAttribute("url","$tableName-delete");
		$tag->setAttribute("do","$actionClass::delete");		
		$operations->appendChild($tag);
			
		$dom->save("app/app.xml");
	}
	return $rest;
}



function generateSimpleForm($table_is,$all_vars)
{
	$fo="<form action='{\$smarty.const.BASE_URL}$table_is/save' method='post' class='' enctype='multipart/form-data'>\n";
	$fo.="<input type='hidden' name='id' value='{\$bean->id}' id='id'/>";
	$add_labels="";
	
		foreach ($all_vars as $small)
		{
			$capital=ucfirst($small);
			if($small!='id'){
				$add_labels.="\n$small			= $capital";
				$fo.= "\n<p>\n<label for='$small' >{\$ini->labels['$small']}:</label>  \n	<input type='text' name='$small' value='{\$bean->$small}' id='$small'/> \n</p>\n";
			}
		}
		
	$fo.="\n <input type=\"submit\"/> \n</form>\n\n*=*=*=*=*=* Added labels is \n".$add_labels;

	return $fo;
}

function generateController($tableName,$is_write=null){


$class_name		=getCamelCase($tableName);
$ctrlName		=$class_name."Model";
$beanName		=$class_name."Bean";
$entityName		=$class_name."Entity";
///
$fo = "<?php";


$fo.=<<<HERE

/* @var \$entity $entityName */ 
/* @var \$bean $beanName */ 

class $ctrlName\n{

	function getAll($beanName  \$bean=null,\$where="")
	{
		\$entity=new $entityName();
		\$all=\$entity->Find(\$where);
		\$beansArr=array();
		
	      foreach(\$all as \$entity)
	      {
	      	\$beansArr[]=$beanName::fromEntity(\$entity,"$beanName");
	      }
	      
		return \$beansArr;
	}
	
	function delete($beanName \$bean)
	{
		\$entity=new $entityName();
		\$entity->fromBean(\$bean);
		\$entity->Load(" id=".\$bean->id);
		return \$entity->Delete();
	}
	
	function save($beanName \$bean)
	{
		\$entity=new $entityName();
		\$entity->fromBean(\$bean);
		if(\$bean->id)
		{
			\$entity->Load(" id=".\$bean->id);
			\$entity->fromBean(\$bean);
		}

		\$entity->Save();
		return $beanName::fromEntity(\$entity,"$beanName");
	}
	
	function getById($beanName \$bean)
	{
		\$entity=new $entityName();
		\$entity->Load(" id=".\$bean->id);
		return $beanName::fromEntity(\$entity,"$beanName");
	}
}
\n\n
HERE;

	if($is_write==1)
	{

		chdir(APP_PATH."app/model");
		
		$myFile = $class_name."Model.class.php";
		
		$fh = fopen($myFile, 'w') or die("can't open file");
		
		fwrite($fh, $fo);
		
		fclose($fh);
	}

return $fo;
}

function generateAction($tableName,$is_write=null)
{

$capitalTableName=getCamelCase($tableName);

$actionName=$capitalTableName;
$modelName=$capitalTableName."Model";
$beanName=$capitalTableName."Bean";

///
$fo = "<?php";


$fo.=<<<HERE

/* @var \$model $modelName */
/* @var \$bean $beanName */

class $actionName extends Action\n{

	function execute(FormBean \$bean)
	{

	}

	function save(FormBean \$bean)
	{
		\$bean->fromArray();
		// model work
		\$model=\$this->model;
		\$model->save(\$bean);
		//return redirect(BASE_URL."$tableName/all");
	}
	
	function delete(FormBean \$bean)
	{
		\$bean->fromArray();
		// model work
		\$model=\$this->model;
		\$model->delete(\$bean);
		//return redirect(BASE_URL."$tableName/all");
	}
	
	function data(FormBean \$bean)
	{
		\$bean->fromArray();
		// model work
		if(\$bean->id)
		{
			\$model=\$this->model;
			\$bean=\$model->getById(\$bean);
		}
		\$this['bean']=\$bean;
		//\$this->display("{$tableName}_data.tpl");
	}
	
	function all(FormBean \$bean)
	{
		\$model=\$this->model;
		\$all=\$model->getAll();
		\$this['all_$tableName']=\$all;
		\$this['bean']=\$bean;
		//\$this->display("{$tableName}_all.tpl");
	}
}

HERE;

	if($is_write==1)
	{
		chdir(APP_PATH."app/actions");
		
		$myFile = $actionName.".class.php";
		
		$fh = fopen($myFile, 'w') or die("can't open file");
		
		fwrite($fh, $fo);
		
		fclose($fh);
	}

return $fo;
	
}

function getCamelCase($tableName){
	
  $words = explode('_', strtolower($tableName));

  $return = '';
  foreach ($words as $word) {
    $return .= ucfirst(trim($word));
  }

  return $return;
}

