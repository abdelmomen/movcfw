<?php

class Action extends SmartyBC implements ArrayAccess {

	private  $errors = array();
	protected $model;
	protected $has_model=true;
	protected $has_bean=true;
	var $bean;
	
	// implements ArrayAccess Methods
   public function offsetExists($name){
      return (isset($this->_tpl_vars[$name]));
   }

   public function offsetGet($name){
      return ($this->_tpl_vars[$name]);
   }

   public function offsetSet($name, $value){
      $this->assign($name, $value);
   }

   public function offsetUnset($name){
      if (isset($this->_tpl_vars[$name])) {
         unset($this->_tpl_vars[$name]);
      }
      return (true);
   }
   
    public function prepare_time(){
    	
    	// Nice 2 digit formatting
		$prepend = array('00','01','02','03','04','05','06','07','08','09'); 
		$hours     = array_merge($prepend,range(10, 23)); 
		$minutes     = array_merge($prepend,range(10, 59));
		
		// make array keys also 2 digits 
		$hours=array_combine($hours,$hours);
		$minutes=array_combine($minutes,$minutes);
		
		$this['t_hours']		= $hours;
		$this['t_minutes']		= $minutes;
    }

    public function __construct(){
    	
    	$registry=Registry::singleton();
    	
    	$this["registry"]=$registry;
    	$this["lang"]=Registry::singleton()->getStuff('lang');
    	// Cast Array to object to ease the access of it using {$ini->
    	$this["ini"]=(object)Registry::singleton()->getStuff("ini");
    	
    	parent::__construct();
    	$my_name=get_class($this);
    	if($this->has_bean)
		{
			//$this->bean=Registry::getNewBean(get_class($this));
			$bean_name= $my_name."Bean";
			$this->bean=new $bean_name();
		}
		else
		{
			$this->bean=new FormBean();
		}
		
		if($this->has_model)
		{
			$model_name= $my_name."Model";
			$this->model=new $model_name();
		}
			
		
		
    }
    
    
	public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
	{
		
		$p_title=$this->get_template_vars('p_title');

		if(empty($p_title))
		{
			$ini=(object)Registry::singleton()->getStuff("ini");
			$class_small_name=lcfirst(get_class($this));
			
			if(!empty($ini->labels[$class_small_name]))
				$this->assign('p_title',$ini->labels[$class_small_name]);
			else 
				$this->assign('p_title',$class_small_name);
		}

		//$this->debugging=true;
		$this->force_cache=true;// force compile every time
		$this->template_dir=TMPL_PATH;
		$this->php_handling=Smarty::PHP_ALLOW;
		$this->assignErrors();
		$this->assign("registry", Registry::singleton());
		parent::display($template,$cache_id,$compile_id,$parent);
	}

	public function getActionName(){
		return (string)$this->xml["name"];
	}
	
	public function execute(FormBean $bean){
    	print "<h1> no execute( )implementation for Action ".$this->getActionName()." </h2>";
	}

	public function addError($error){
		
		$this->errors[]=$error;
	}
	
	public function assignErrors(){
		
		if(!empty($this->errors)){
			
			$value="<ul class='act_errors'> \n";
			
			foreach($this->errors as $error)
				$value.= "<li>".$error."</li>";
				
			$value.= "</ul> \n ";
			
			$this->errors=array();// remove the errors
			$this->assign("act_errors", $value);
		}// end if $this->erorrs
		else 
			$this->assign("act_errors", "");
	}
}	
