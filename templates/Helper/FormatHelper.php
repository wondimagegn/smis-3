<?php 
App::uses('AppHelper', 'View/Helper');
class FormatHelper extends AppHelper {

	 public $helpers = array('Session');
	
	function humanize_date ($date=null) {
		return date("F d, Y h:i:s A", mktime (substr($date,11 ,2), 
		substr($date,14 ,2), 
		substr($date,17 ,2), 
		substr($date,5 ,2), 
		substr($date,8 ,2), 
		substr($date,0 ,4)));

	}

	function humanize_date_short2($date=null) {
		return date("M d, y h:i:s A", mktime (substr($date,11 ,2), 
		substr($date,14 ,2), 
		substr($date,17 ,2), 
		substr($date,5 ,2), 
		substr($date,8 ,2), 
		substr($date,0 ,4)));

	}

	function humanize_date_short($date=null) {
		return date("M j, y", mktime (substr($date,11 ,2), 
		substr($date,14 ,2), 
		substr($date,17 ,2), 
		substr($date,5 ,2), 
		substr($date,8 ,2), 
		substr($date,0 ,4)));

	}

	function humanize_date_short_extended($date=null) {
		
		return date("M d, Y", mktime (substr($date,11 ,2), 
		substr($date,14 ,2), 
		substr($date,17 ,2), 
		substr($date,5 ,2), 
		substr($date,8 ,2), 
		substr($date,0 ,4)));


	}

      function humanize_date_short_extended_testDate($date=null) {
		
   		return date("M, Y", mktime (substr($date,11 ,2), 
		substr($date,14 ,2), 
		substr($date,17 ,2), 
		substr($date,5 ,2), 
		substr($date,8 ,2), 
		substr($date,0 ,4)));

	}

	function humanize_date_short_extended_all($date=null) {
		return date("F d, Y", mktime (substr($date,11 ,2), 
		substr($date,14 ,2), 
		substr($date,17 ,2), 
		substr($date,5 ,2), 
		substr($date,8 ,2), 
		substr($date,0 ,4)));

	}
	function humanize_hour ($time=null) {
	    $return_time = strtotime($time);
	    return date("h:i A",$return_time);
	}


	function short_date ($date=null) {
	    $date = new DateTime($date);
	    return $date->format("M d, Y");
	}

	public function isActiveMenu($submenu=null,$controller=null,$action=null) {
	   // return false;
	     if(!empty($submenu)) {
		    $keys = array_keys($submenu);
		    if(strcasecmp($keys[0], '0') != 0) {
		            if((strcasecmp($submenu['url']['controller'],$controller) == 0 
		            || strcasecmp(Inflector::variable($submenu['url']['controller']),$controller) == 0)
		            && strcasecmp($submenu['url']['action'],$action) == 0
		            ){
		                return true;
		            } elseif(!empty($submenu['children'])) {
		                if ($this->isActiveMenu($submenu['children'],$controller,$action)) {
		                    return true;
		                }
		            }
		            else {
		                return false;
		            }
		    } else {
		        foreach($submenu as $sk=>$sv) {
		           if ($this->isActiveMenu($sv,$controller,$action)) {
		                    return true;
		            }
		        }
		    }
	     }
	     return false;       
	}

	public function renameControllerTitle(&$menuoptimized=null,$rename_menu_title=null) {
	if(!empty($menuoptimized)) {
            	foreach($menuoptimized as $key => $sub_menu) 
		{
	                if(array_key_exists($sub_menu['id'], $rename_menu_title)) {
                    $menuoptimized[$key]['title'] = $rename_menu_title[$sub_menu['id']];
                }
               if(!empty($sub_menu['children']))
                	$this->renameControllerTitle($menuoptimized[$key]['children'], $rename_menu_title);
                }
       }
      }
      function getChildren($children,$html){
		$str=null;
		foreach($children as $k =>$v){
		     $str.=$this->Html->link($v['url']['controller'], '/'.$v['url']['controller'].'/'.$v['url']['action']);	 
		}
      }

      function humanTiming($date){
      	$convertedTime=strtotime($date);
      	$time=time() -$convertedTime;
      	$time=($time<1) ? 1 : $time;
      	$tokens =array(
      		60*60*24*365=>'year',
      		60*60*24*30=>'month',
            60*60*24*30=>'month',
      		60*60*24*7=>'week',
      		60*60*24=>'day',
      		60*60 => 'hour',
      		60*1=>'minute',
      		1=>'second',
      	);
      	foreach ($tokens as $unit => $text) {
      		if($time < $unit) continue;
      		$numberOfUnits=floor($time/$unit);
      		return $numberOfUnits.' '.$text;
      	}
      }

       public function checkIfHasPermission($controllerActionUrl){
			    
				$permissionLists= $this->Session->read('permissionLists');
			
				if (isset($permissionLists) && !empty($permissionLists)) {
				    if (in_array($controllerActionUrl,$permissionLists) ) {
				       
				        return true;
				    }
				}
				return false;
	  }

}
?>
