<?php echo $this->Form->create('AcceptedStudent', array('action' => 'approve_auto_placement'));?> 

<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="reservedPlaces form">
<table><tbody><tr><td width="100%">

	<?php if (!isset($auto_approve)) { ?>
	 <div class="smallheading">Please select the academic year, you want to accept auto placed students.</div>
	 <?php 
	        
	        echo '<div style="font-weight:bold">Academic Year:</div>';
			echo $this->Form->input('academicyear',array('id'=>'academicyear',
            'label' => false,'type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--"));
            
             echo $this->Form->Submit(__('Continue'),array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'searchbutton'));
                   
             }
             ?>
</td>
</tr>

<tr><td colspan=2>
<?php

if(!empty($autoplacedstudents)){
echo $this->Form->hidden('AcceptedStudent.academicyear',array('value'=>$selected_academicyear));

 $count=0;

?>
<table>
   <tr><th colspan=12 class="smallheading"><?php echo  __('List of students placed to '.$department_name.' by the college  with minute number '.$minute_number.' and  who 
   are not attached to the curriculum by the department.',true);?></th></tr>
	<tr>
	        
            <th><?php echo ('No.'); ?> </th>
            <th style="padding:0">
            <?php echo 'Select/ Unselect All <br/>'.$this->Form->checkbox("SelectAll", array('id' => 'select-all','checked'=>'')); ?> </th> 
            <th><?php echo ('Full Name');?></th>
			<th><?php echo ('Sex');?></th>
			<th><?php echo ('Student Number');?></th>
		
			<th><?php echo ('EHEECE Total Result');?></th>
			<th><?php echo ('Preference Order');?></th>
			<th><?php echo ('Department');?></th>
			<th><?php echo ('Academic Year');?></th>
			<th><?php echo ('Department approval');?></th>
			
			<th><?php echo ('Placement Type ');?></th>
			<th><?php echo ('Placement Based');?></th>
			
	</tr>
	<?php
	$i = 0;
	$serial_number=1;
	
	foreach ($autoplacedstudents as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
       
        <td><?php echo $serial_number++;?></td>
         <td ><?php echo $this->Form->checkbox('AcceptedStudent.approve.' . $acceptedStudent['AcceptedStudent']['id'],
         array('class'=>'checkbox1')); ?>&nbsp;</td> 
          <?php echo $this->Form->hidden('AcceptedStudent.'.$count.'.id',array('value'=>$acceptedStudent['AcceptedStudent']['id']));?>
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		
		<td><?php echo $acceptedStudent['AcceptedStudent']['EHEECE_total_results']; ?>&nbsp;</td>
		<td><?php 
		if(!empty($acceptedStudent['Preference'])){
		       foreach($acceptedStudent['Preference'] as $key=>$value){
		        if($value['department_id']==$acceptedStudent['Department']['id']){
	                	echo $value['preferences_order']; 
	                	break;
	        	}
		    }
		}
		?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Department']['name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['academicyear']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['Placement_Approved_By_Department']==1 ? 'Approved By Department':'Not Approved By Department'; ?>&nbsp;</td>
	
		<td><?php echo $acceptedStudent['AcceptedStudent']['placementtype']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['placement_based'] == 'C' ? 'Competitive' : 'Quota'; ?>&nbsp;</td>
	</tr>
	
<?php 
$count++;

endforeach; 

?>
	</table>

	<?php 
	if(!isset($turn_of_approve_button)){
echo "<table>";
     echo "<tr><td colspan='2' class='fs16'>Select the curriculum you want to attach the selected students</td>";
  
    echo "</tr>";
    echo "<tr><td style='width:13%'>Curriculum:</td><td style='width:37%'>".$this->Form->input('curriculum_id',array('label'=>false))
    ."</td>";
  
    echo "</tr>";
    echo '<tr><td colspan=2>'.$this->Form->Submit(__('Attached Selected Student'),array('div'=>false,
     'name'=>'approve')).'</td></tr>';
echo "</table>";
}

	
}/* else {
	echo "<div class='info-box info-message'> <span></span>There is no auto placement report  in the selected 
academic year.</div>";
} 
*/

 ?>
</td></tr>
    </tbody></table>
   
</td></tr>
</tbody></table>

</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<?php echo $this->Form->end();?>
