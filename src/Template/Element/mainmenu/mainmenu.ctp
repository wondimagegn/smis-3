<?php

use Cake\Core\Configure;
use Cake\Utility\Inflector;

?>
<div class="grid_16">
<?php
$rename_menu_title = Configure::read('Menu.title_rename');
renameControllerTitle($menu, $rename_menu_title);
//debug($menu);
//debug($rename_menu_title);
$links = array();
$url=$this->params['url'];
$urlexplode=$url['url'];
//debug($url);
$urlexplode=explode('/',$urlexplode);
$controller=str_replace('_','',$urlexplode[0]);
$action=!empty($urlexplode[1])?$urlexplode[1]:'index';

echo "<ul id='navtabs'>";
//debug($menu);
$parent = 0;
$parent_exists = false;
//debug($menu);
foreach($menu as $key =>$value){
    if(isActiveMenu($value,$controller,$action)){
        //debug($value.','.$controller.','.$action);
        $parent_exists = true;
        break;
        }
}

foreach($menu as $key =>$value) {


				if (($parent_exists == false && $key == 0) || isActiveMenu($value,$controller,$action)) {
				        echo '<li class="cat-item current-cat">';
				        //debug($value.','.$controller.','.$action);
				        $parent=$key;
				        //$this->Session->write('active_menu_parent',$key);


                } else {
				   echo '<li class="cat-item">';

				}
				$ctrlCamel = Inflector::variable($value['url']['controller']);
				if(!array_key_exists($value['id'], $rename_menu_title))
			        $ctrlHuman = Inflector::humanize(Inflector::underscore($ctrlCamel));
				else
				    {
				    $ctrlHuman = $rename_menu_title[$value['id']];
				    }
    echo $this->Html->link($ctrlHuman, '/' . $value['url']['controller'] . '/' . $value['url']['action']);
}
echo "</ul>";

function isActiveMenu($submenu=null,$controller=null,$action=null) {
     if(!empty($submenu)) {
            $keys = array_keys($submenu);
            if(strcasecmp($keys[0], '0') != 0) {
                if ((strcasecmp($submenu['url']['controller'], $controller) == 0
                    || strcasecmp(Inflector::variable($submenu['url']['controller']),$controller) == 0)
                    && strcasecmp($submenu['url']['action'],$action) == 0
                    ){
                        return true;
                    } elseif(!empty($submenu['children'])) {
                        //debug($submenu['children']);
                        if (isActiveMenu($submenu['children'],$controller,$action)) {
                            return true;
                        }
                    }
                    else {
                        return false;
                    }
            } else {
                foreach($submenu as $sk=>$sv) {
                   if (isActiveMenu($sv,$controller,$action)) {
                            return true;
                    }
                }
            }
     }

    return false;
}


function getChildren($children,$html){
        $str=null;
        foreach($children as $k =>$v){
            $str .= $html->link($v['url']['controller'], '/' . $v['url']['controller'] . '/' . $v['url']['action']);
        }
}

/*
$urlexplode=$url['url'];
$urlexplode=strtok($urlexplode,'/');
//debug($urlexplode);
$parent=null;
foreach($menu as $mk=>$mv){

        foreach ($mv['children'] as $k => $v) {

               if($v['id'] == $urlexplode){
                    $parent=$v['parent'];
                    break 2;
               }

        }
}
*/
?>
</div>
<?php
//debug($menu);
//     debug($parent);

?>
<div class="grid_16">
<div id="submenu">
    <?php
//debug($menu);
  //   debug($parent);
        foreach($menu[$parent]['children'] as $m =>$f){
                    echo "<ul class='dropdown'>";
					echo '<li>';

            /*echo $this->Js->link($f['title'], '/'.$f['url']['controller'].'/'.$f['url']['action'],
            array('update'=>'#ajax_div','evalScripts'=>true,'before'=>
            $this->Js->get('#busy-indicator')->effect('fadeOut',array('buffer'=>false)),
            'complete'=>$this->Js->get('#busy-indicator')->effect('fadeOut',
            array('buffer'=>false))
            ));*/


            echo $this->Html->link(__($f['title'], true), array('controller'=>$f['url']['controller'],'action' =>$f['url']['action']));

            if(sizeof($f['children'])!=0){
							    //echo grandchildrenmenu($children);
							    //echo grandchildrenmenu($f['children']);
							    $children=$f['children'];
							    // $string='<div class="sidebarmenu">';
							$string = null;
		                    $string.='<ul>';
							    foreach($children as $ch=>$vch) {
                                    $string.="<li>";

                                    $string .=$this->Html->link($vch['title'], '/'.$vch['url']['controller'].'/'.$vch['url']['action']);
											/* $string .=$this->Js->link($vch['title'], '/'.$vch['url']['controller'].'/'.$vch['url']['action'],array('update'=>'#ajax_div','evalScripts'=>true,'before'=>
					$this->Js->get('#busy-indicator')->effect('fadeOut',array('buffer'=>false)),
					'complete'=>$this->Js->get('#busy-indicator')->effect('fadeOut',
					array('buffer'=>false))
					)); */
					if(sizeof($vch['children'])!=0) {
					    $string .='<ul>';
					    foreach($vch['children'] as $c => $v) {
					       $string .='<li>';
					       /*$string.=$this->Js->link($v['title'], '/'.$v['url']['controller'].'/'.$v['url']['action'],array('update'=>'#ajax_div','evalScripts'=>true,'before'=>
$this->Js->get('#busy-indicator')->effect('fadeOut',array('buffer'=>false)),
'complete'=>$this->Js->get('#busy-indicator')->effect('fadeOut',
array('buffer'=>false))
));*/
					       $string.=$this->Html->link($v['title'], '/'.$v['url']['controller'].'/'.$v['url']['action']);
					       $string .='</li>';
                        }
					    $string .='</ul>';
					}

                                    $string.= "</li>";
						}

                $string.="</ul>";
							echo $string;
						}

            echo '</li>';
					echo "</ul>";
        }
?>
</div>
</div>
<?php
function renameControllerTitle(&$menu=null, $rename_menu_title=null) {
     if(!empty($menu)) {
            foreach($menu as $key => $sub_menu) {
                if(array_key_exists($sub_menu['id'], $rename_menu_title)) {
                    $menu[$key]['title'] = $rename_menu_title[$sub_menu['id']];
                }
            if(!empty($sub_menu['children']))
                renameControllerTitle($menu[$key]['children'], $rename_menu_title);
            }
          }
}
?>
