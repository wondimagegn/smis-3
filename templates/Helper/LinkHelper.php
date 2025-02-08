<?php 

/* /app/View/Helper/LinkHelper.php */
App::uses('AppHelper', 'View/Helper');
class LinkHelper extends AppHelper {
    
   	public function isActiveMenu($submenu=null,$controller=null,$action=null) {
		     if(!empty($submenu)) {
			    $keys = array_keys($submenu);
			    if(strcasecmp($keys[0], '0') != 0) {
				    if((strcasecmp($submenu['url']['controller'],$controller) == 0 
				    || strcasecmp(Inflector::variable($submenu['url']['controller']),$controller) == 0)
				    && strcasecmp($submenu['url']['action'],$action) == 0
				    ){
				        return true;
				    } elseif(!empty($submenu['children'])) {
				        //debug($submenu['children']);
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

   public function getChildren($children,$html){
		$str=null;
		foreach($children as $k =>$v){
		     $str.=$this->Html->link($v['url']['controller'], '/'.$v['url']['controller'].'/'.$v['url']['action']);	 
		}
   }

  public function renameControllerTitle(&$menuoptimized=null, $rename_menu_title=null) {
     if(!empty($menuoptimized)) {
            foreach($menuoptimized as $key => $sub_menu) {
                if(array_key_exists($sub_menu['id'], $rename_menu_title)) {
                    $menuoptimized[$key]['title'] = $rename_menu_title[$sub_menu['id']];
                }
            if(!empty($sub_menu['children']))
                $this->renameControllerTitle($menuoptimized[$key]['children'], $rename_menu_title);
            }
          }
}

}

?>
