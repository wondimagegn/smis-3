<?php echo $this->Html->script('jquery-1.6.2.min'); ?>  
<?php echo $this->Html->script('jquery-department_placement');?>
<?php echo $this->Form->create('AcceptedStudent', array('action' => 'auto_placement_approve_college'));?> 
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="smallheading">Auto Placement Approval</div>
<div class="reservedPlaces form">
<table><tbody><tr><td width="100%">
<table><tbody><tr> 
	
	<td>
	<?php if (!isset($auto_approve)) { ?>
	
	 <?php 
	        echo '<div style="font-weight:bold">Academic Year:</div>';
			echo $this->Form->input('AcceptedStudent.academicyear',array('id'=>'academicyear',
            'label' => false,'type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($selected_academicyear)?$selected_academicyear:''));
            
             echo $this->Form->Submit(array('label'=>__('Continue'),'class'=>'tiny radius button bg-blue'));
             
             }
             ?>
      
	</td></tr>
	
</tbody></table>
</td>
</tr>

<tr><td colspan=2>
<?php

if(!empty($autoplacedstudents)){
$summery=$autoplacedstudents['auto_summery'];
unset($autoplacedstudents['auto_summery']);
if(!empty($autoplacedstudents)){

echo $this->Form->hidden('AcceptedStudent.academicyear',array('value'=>$selected_academicyear));
if (!isset($minute_number)) {
echo "<table><tbody><tr><td style='width:30%'>".$this->Form->input('minute_number',
array('label'=>'Minute Number'))."</td><td>".$this->Form->Submit(__('Approve'),array('div'=>false,
 'name'=>'approve','class'=>'tiny radius button bg-blue'))."</td></tr></tbody></table>";
} else {
    echo "<table><tbody><tr><td style='width:30%' class='smallheading'> List of autoplaced students approved by minute number ".$minute_number."</td></tr></tbody></table>";
}
 

echo "<table><tbody>";
echo "<tr><th colspan=3> Summery of Auto Placement.</th></tr>";
 echo "<tr><th>Department</th><th>Competitive Assignment</th><th> Privilaged Quota Assignment</th>";
foreach ($summery as $sk=>$sv){
         echo "<tr><td>".$sk."</td><td>".$sv['C']."</td><td>".$sv['Q'].'</td>';
        
}
echo "</tbody></table>";

 $count=0;
foreach($autoplacedstudents as $key =>$data){

?>
<table>
 <tr><td colspan=11 class="headerfont"><?php echo $key ?></td></tr> 
	<tr>
           
            <th><?php echo ('Full Name');?></th>
			<th><?php echo ('Sex');?></th>
			<th><?php echo ('Student Number');?></th>
			<th><?php echo ('Assignment Type');?></th>
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
	
	foreach ($data as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
        <?php echo $this->Form->hidden('AcceptedStudent.'.$count.'.id',array('value'=>$acceptedStudent['AcceptedStudent']['id']));?>
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['assignment_type']; ?>&nbsp;</td>
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
		<td><?php echo isset($acceptedStudent['AcceptedStudent']['approval'])?'Approved By Department':'Not Approved By Department'; ?>&nbsp;</td>
	
		<td><?php echo $acceptedStudent['AcceptedStudent']['placementtype']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['placement_based'] == 'C' ? 'Competitive' : 'Quota'; ?>&nbsp;</td>
	</tr>
	
<?php 
$count++;

endforeach; 

?>
	</table>

	<?php 
	
	} 
  }
} 
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
