<?php ?>
<div class="box">
  <div class="box-body">
	<div class="row">
		<div class="large-12 columns">
		     <h2 class="box-title">
			<?php echo __('Downgrade year level of section');?>
		      </h2>
		</div>
		<div class="large-12 columns">
                <?php
	/*
    $this->Html->scriptBlock("
        jQuery(function($){
            $('downgradeSection').submit(function(event){
              $('downgradeSection').di
            });
        });
    ",array('inline'=>false));
	*/

echo $this->Form->create('Section');   
if($role_id == ROLE_DEPARTMENT){
	echo "<div class='centeralign_smallheading'> Downgrade Section</div>";
    echo "<div class='font'>".$college_name."</div>";
    echo "<div class='font'>"."Department of ".$department_name."</div>";
    echo "<div class='info-message info-box'><u><FONT COLOR=Red>Beware:</FONT> 
    Downgrade a given section if only necessary.</u><br/>- You are advice to use downgrade only if 
    you upgrade section by mistake.<br/>- To downgrade a given section, the section must not ever have 
    published course. <br/>- Here you get only potentially downgrade able section as options.</div>"

?>
<table cellpadding="0" cellspacing="0">
	<?php 
        if(!empty($yearLevels)){
        	$key = array_search('1st',$yearLevels);
        	if($key !=false){
        		unset($yearLevels[$key]);
        	}
        }
        echo '<tr><td width="250PX">'. $this->Form->input('Section.program_id',array('empty'=>"--Select Program--")).'</td>'; 
        echo '<td width="400PX">'. $this->Form->input('Section.program_type_id',array('empty'=>"--Select Program Type--")).'</td></tr>';  
         echo '<tr><td width="400PX">'. $this->Form->input('Section.academicyear',array('type'=>'select', 'options'=>$acyear_array_data,'empty'=>'--Select Academic Year--')).'</td>';   
        echo '<td width="250PX">'. $this->Form->input('Section.year_level_id',array('empty'=>'--Select Year Level--','type'=>'select','options'=>$yearLevels)).'</td></tr>'; 
        echo '<tr><td colspan="3">'. $this->Form->Submit('Search',array('name'=>'search','div'=>false)).'</td></tr>'; 
	?> 
</table>
<?php

if(!empty($formateddowngradableSections)){
	
/*
$this->Form->input('Section.downgrade',array('label'=>'Sections','type'=>'select','options'=>$formateddowngradableSections,'empty'=>'--Please Select a Section')).

*/
	echo '<table>';
	echo '<tr>';
		debug($formateddowngradableSections);	
		foreach($formateddowngradableSections as $k=>$v) {
echo '<td>'.$this->Form->input('Section.Downgradable_Selected.'.$k,array('class'=>'downgradableSelectedSection','type'=>'checkbox',
'value'=>$k,'label'=>$v)).'</td>';
}

'</tr>';


	echo '<tr><td>'.$this->Form->Submit('Downgrade',array('name'=>'downgrade','div'=>false,'class'=>'tiny radius button bg-blue',
	'onClick'=>'return confirm("Are you sure you want to downgrade selected section?")','id'=>'downgradeSection')).'</td></tr>';


	echo '</table>';
} else if(empty($formateddowngradableSections) && !($isbeforesearch)) { 
	echo "<div class='info-box info-message'><span></span> There is no section to upgradre in the search criteria </div>";
} 

} // close if department
$this->Form->end(); 
?>
		</div>
	  </div>
   </div>
</div>
