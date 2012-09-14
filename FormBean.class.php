<?php 

class FormBean{
	
	var $helper="";
	var $where="";
	var $order="";
	
	var $meta=array();
	
	CONST TYPE="type";
	CONST REQUIRED="required";
	CONST PATH="path";
	CONST TYPES_FILE="file";
	CONST TYPES_DATE="date";
	
	CONST DATE_TIME_HOURS="time_h";
	CONST DATE_TIME_MINUTES="time_m";
	
	CONST MAX_KB_SIZE="max_kb_size";
	
	function reset()
	{
		$vars = get_object_vars($this);
		
      	foreach($vars as $key => $val)
      	{ 
         	$this->$key = null; 
      	}
	}
	
	public function fromArray($request=null)
	{
		//throw new Exception("no fromArray Implementation for Bean".get_class($this));
		$vars = get_object_vars($this);
		
		if(!is_array($request) && empty($request))
			$request=$_REQUEST;
			
		
	    foreach($vars as $key => $val) 
	    {
	    	if(isset($request[$key])&&$request[$key]!=="")
	    	$this->$key = $request[$key];
	    }
	}
	
	public static function fromEntity(Entity $entity,$called_class=null)
	{
		if(!$called_class)
			$called_class=get_called_class();


		if(version_compare(PHP_VERSION, '5.3.0', '<')&&!$called_class)
			throw new Exception("You Must Define Called Class");
		
		$instance = new $called_class();
		
		foreach(get_object_vars($entity) as $prop => $value)
		{
			if(property_exists($instance,$prop))
				$instance->$prop=$value;
		}
		return $instance;
	}
	
	
	function validate(){
		// Validation code goes here
	}

	public function fromArrayMeta(){
		    	//if(isset($this->meta[$key])&&$this->meta[$key]['required']==TRUE)echo "$key req";
		    	$this->fromArray();
		    	foreach($this->meta as $key=>$val)
		    	{

		    		if(isset($val[FormBean::TYPE])&&$val[FormBean::TYPE]==FormBean::TYPES_FILE)
		    		{
		    			// TDOO upload work
		    			$path_parts = pathinfo($_FILES[$key]['name']);
		    			
		    			if(!isset($val[FormBean::PATH]))
		    				throw new Exception(" The FormBean proberty ($key) type ".FormBean::TYPES_FILE
		    									." Must have a FormBean::PATH on meta Array() ");
		    			
						require_once 'lib/Upload.php';
						require_once 'lib/ImageAPI.class.php';
						
						$upload = new Upload();

		    			$upload->SetFileName($_FILES[$key]['name']);
			
						$upload->SetTempName($_FILES[$key]['tmp_name']);
						
						$upload->SetUploadDirectory($val[FormBean::PATH]);
						
						//Maximum file size in bytes, if this is not set, the value in your php.ini file will be the maximum value
						if(isset($val[FormBean::MAX_KB_SIZE]))
							$upload->SetMaximumFileSize($val[FormBean::MAX_KB_SIZE]*1000);

						$fileIsOK=$upload->UploadFile();
						
						$this->$key=$val[FormBean::PATH].$upload->GetFileName();
						
						// TDOO Also Create Thumb
		    		}
		    		else if(isset($val[FormBean::TYPE])&&$val[FormBean::TYPE]==FormBean::TYPES_DATE)
		    		{

		    			$time_h=FormBean::DATE_TIME_HOURS;
		    			if(isset($val[FormBean::DATE_TIME_HOURS]))
		    				$hours=$this->$time_h;
		    				
		    			$time_m=FormBean::DATE_TIME_MINUTES;
		    			if(isset($val[FormBean::DATE_TIME_MINUTES]))
		    				$mins=$this->$time_m;
		    			
		    			$time=strtotime($this->$key ." $hours:$mins");
		    			
		    			$this->$key=$time;
		    		}

		    		// TODO move required to validate and call validate from fromArray()
		    		//if(isset($val["required"])&&$val["required"]==TRUE)
		    	}

	}
}
