<?php

/**
 * The RegPointer object
 * a pointer to stuffed objects , arrays 
 *
 * @version 0.1
 * @author Abdelmomen
 */
class RegPointer{

	var $name;
	var $type;
	var $scope='session';
	var $pre_req;
	
	const TYPE_ARRAY='array';
	const TYPE_OBJECT='object';
	const TYPE_OBJECTS_ARRAY='objs_array';
	
	const SCOPE_SESS='session';
	const SCOPE_REQ='request';
	
	const SETTING='setting';
	
	const UNSET_VALUE='unset';
	
}

/**
 * The Registry object
 * Implements the Registry and Singleton design patterns
 *
 * @version 0.1
 * @author Abdelmomen
 */
class Registry {
	

	/*@var $__regist Registry */
	/*@var $pointer RegPointer */
	
	public $pointers=array();
	
	public $stuff=array('setting' =>array());
	
	private function add_pointers(RegPointer $pointer) {
		$this->pointers[$pointer->name]=$pointer;
	}
	
	public function setStuff($name , $value , $scope=null , $not_empty=false){
		
		if($not_empty && empty($value))
			throw new Exception("Empty value , to unset value use RegPointer::UNSET_VALUE as \$value parameter");
		
		if(is_object($value))
		{
			$pointer=new RegPointer();
			$pointer->name=$name;
			
			if(!empty($scope))
				$pointer->scope=$scope;
			
			$pointer->pre_req=get_class($value);
			$pointer->type=RegPointer::TYPE_OBJECT;
			$this->add_pointers($pointer);
			// Add Value to Stuff 
			$this->stuff[$name]=$value;
		}
		elseif (is_array($value))
		{
			$pointer=new RegPointer();
			$pointer->name=$name;
			
			if(!empty($scope))
				$pointer->scope=$scope;
			
			$last_element=end($value);
			
			if(is_object($last_element))
			{
				$pointer->pre_req=get_class($last_element);
				$pointer->type=RegPointer::TYPE_OBJECTS_ARRAY;
			}
			else 
				$pointer->type=RegPointer::TYPE_ARRAY;
			
			$this->add_pointers($pointer);
			// Add Value to Stuff
			$this->stuff[$name]=$value;
		}
		else
		{
			// No Pointer
			$this->stuff[RegPointer::SETTING][$name]=$value;

		}

		return true;
	}
	
	public function getStuff($name,$obj_or_array=false){
		
		if(isset($this->pointers[$name]))
		{
			$pointer=$this->pointers[$name];
		}
		
		
		$from='';
		
		if(empty($pointer))
		{
			if($obj_or_array )
				throw new Exception("Empty pointer , while method got that its obj_or_array = true ");

			else 
				$from=RegPointer::SETTING;
		}
		else
		{
			if( ($pointer->type==RegPointer::TYPE_OBJECT || $pointer->type==RegPointer::TYPE_OBJECTS_ARRAY) 
					&& !class_exists($pointer->pre_req))
				throw new Exception("Not Defined class : ". $pointer->pre_req ." for $name stuff");
		}


		// Return the value
		if(!empty($from) && isset($this->stuff[$from][$name]))
			return $this->stuff[$from][$name];
		
		elseif( isset($this->stuff[$name]))
			return $this->stuff[$name];
		
		//elseif(empty($from) && !$obj_or_array && isset($this->stuff[RegPointer::SETTING][$name]))
			//return $this->stuff[][$name];
		
		else 
			return false;
	}
	
	/**
	 * The instance of the registry
	 * @access private
	 */
	private static $instance;
		
	/**
	 * singleton method used to access the object
	 * @access public
	 * @return Registry
	 */
	public static function singleton() 
	{
		if( ! self::$instance  )
		{
			$obj = __CLASS__;
			self::$instance = new $obj;
			// initialization
			$__regist=self::$instance;
			
			$settingPointer=new RegPointer();
			$settingPointer->name=RegPointer::SETTING;
			$settingPointer->type=RegPointer::TYPE_ARRAY;
			
			$__regist->add_pointers($settingPointer);
		}

		return self::$instance;
	}
	
	function loadPointers(){
		
		foreach($this->pointers as $pointer)
		{
			if($pointer->scope==RegPointer::SCOPE_SESS && isset($_SESSION[APP_NAME.'-'. $pointer->name]))
				$this->stuff[$pointer->name]=unserialize($_SESSION[APP_NAME.'-'.$pointer->name]);

		}
	}
	
	public function setInstance($o) {
		self::$instance = $o;
	}
       
	/**
	 * prevent cloning of the object: issues an E_USER_ERROR if this is attempted
	 */
	public function __clone()
	{
		trigger_error( 'Cloning the registry is not permitted', E_USER_ERROR );
	}
	

	/**
	 * checkSetting check if the stored setting and given value is equal
	 * @param String $key the key of stored setting 
	 * @param String $value the value you want to check
	 * @return boolean 
	 */
	public function checkSetting( $key ,$value=null)
	{
		// NOW load from Session

		if( $value !=null && isset($this->stuff['setting'][ $key ]) && $this->stuff['setting'][ $key ]==$value)
		{
			return true;
		}
		else if ( $value ==null && isset($this->stuff['setting'][ $key ]))
		{
			return true;
		}
		else 
			return false;
		
	}

	
	public static function storeSession()
	{
		$__regist=self::$instance;
		
		try
		{
			foreach($__regist->pointers as $pointer)
			{
				if($pointer->scope==RegPointer::SCOPE_SESS )
				{
					$_SESSION[APP_NAME.'-'.$pointer->name] = serialize($__regist->stuff[$pointer->name]);
				}
					
			}
			$__regist->stuff= array();
			$_SESSION[APP_NAME.'-registry'] = serialize(self::$instance);
		}
    	catch(Exception $e){
    		print $e->getTraceAsString();
    	}

  	}

	
	/**
	 * operate the required action function 
	 * @param $url from $_GET
	 * @return void
	 */
	
	public static function operate($operation)
	{
		global $route;
		
		if(strpos( $operation , '/' )===0)
			$operation=deleteFirstChar($operation);
		
		if($operation)
			Registry::singleton()->setStuff("operation", $operation);
		

		$url=array();

		// Convert Url operation to Action::method operation
		if($operation=="developer")
		{
			return include ('developer/index.php');
		}
		else if($operation=='info')
		{
			return phpinfo();
		}
		else if($operation != "")
		{
			$url=preg_split('/\//', $operation);
			
			if(isset($route[$url[0]]))
			{
				empty($url[1])?$url[1]="":"";
				$operation=$route[$url[0]]."/".$url[1];
				$url=preg_split('/\//', $operation);
			}

		}

			//$do=preg_split('/::/', $operation);		
			
			if(!empty($url[0]))
				$action_name=ucfirst($url[0]);
			else
				$action_name="Main";
				
			if(!empty($url[1]))
				$methodName=$url[1];
			else 
				$methodName="execute";
	    
		try
		{
	  		//$action=self::getAction($action_name);
			$action=new $action_name;
		}
		catch (Exception $e)
		{
			$msg=" No action with name $action_name ";
			$action=new Main();
			$methodName=$action_name;
			if(!method_exists($action, $methodName)){
				$new=new Exception($msg."<br/> And No $methodName method in MainAction");
				print_x($new);
				print_x($e);
				die();
			}
		}

	  	$bean=$action->bean;

	  	$bean->helper=$url;
		try
		{
			return $action->$methodName($bean);
		}
		catch (Exception $e)
		{
			print_x($e);
		}		
	}

}

?>