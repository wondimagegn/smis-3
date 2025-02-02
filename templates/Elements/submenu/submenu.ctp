<div id="submenu">
<?php 
//debug($menu);
$url=$this->request->params['url'];

        $parent=$this->Session->read('active_menu_parent');
       // debug($parent);
        foreach($menu[$parent] as $m =>$f){
                    echo "<ul class='dropdown'>";
					echo '<li>';

					echo $this->Js->link($f['title'], '/'.$f['url']['controller'].'/'.$f['url']['action'], 
					array('update'=>'#ajax_div','evalScripts'=>true,'before'=>
					$this->Js->get('#busy-indicator')->effect('fadeOut',array('buffer'=>false)),
					'complete'=>$this->Js->get('#busy-indicator')->effect('fadeOut',
					array('buffer'=>false))
					));
					

					if(sizeof($f['children'])!=0){
							    //echo grandchildrenmenu($children);
							    //echo grandchildrenmenu($f['children']);
							    $children=$f['children'];
							    // $string='<div class="sidebarmenu">';
							$string = null;
		                    $string.='<ul>';
							    foreach($children as $ch=>$vch) {
							     
							                $string.="<li>";
							            
											 //$string .=$this->Html->link($vch['title'], '/'.$vch['url']['controller'].'/'.$vch['url']['action']);
											 $string .=$this->Js->link($vch['title'], '/'.$vch['url']['controller'].'/'.$vch['url']['action'],array('update'=>'#ajax_div','evalScripts'=>true,'before'=>
					$this->Js->get('#busy-indicator')->effect('fadeOut',array('buffer'=>false)),
					'complete'=>$this->Js->get('#busy-indicator')->effect('fadeOut',
					array('buffer'=>false))
					));
											if(sizeof($vch['children'])!=0) {
											    $string .='<ul>';
											    foreach($vch['children'] as $c => $v) {
											       $string .='<li>';
											       $string.=$this->Js->link($v['title'], '/'.$v['url']['controller'].'/'.$v['url']['action'],array('update'=>'#ajax_div','evalScripts'=>true,'before'=>
					$this->Js->get('#busy-indicator')->effect('fadeOut',array('buffer'=>false)),
					'complete'=>$this->Js->get('#busy-indicator')->effect('fadeOut',
					array('buffer'=>false))
					));
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
<?php 
?>
