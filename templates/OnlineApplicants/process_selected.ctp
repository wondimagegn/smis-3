<?php echo $this->Form->create('OnlineApplicant');?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<?php if (!isset($admitsearch)) { ?>
<div class="smallheading"> Select online admit students to entry process for SMIS . Please dont forget to generate student number, and admit them for department to see them for curriculum attachment and section placement. </div>
<table cellpadding="0" cellspacing="0"><tr> 
	
	<td> <?php 
			echo $this->Form->input('OnlineApplicant.academicyear',array('id'=>'academicyear',
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($defaultacademicyear)?$defaultacademicyear:'')); ?>
	</td>
    
<td> <?php 
			echo $this->Form->input('OnlineApplicant.program_id'); ?>
	</td>


</tr>
	<tr><td>
	   <?php 
	       if (!empty($college_level)) {
			echo $this->Form->input('OnlineApplicant.college_id',array('label'=>'Select College','type'=>'select','empty'=>'---Select College --'));
			 }
			 if (!empty($department_level)) {
			    echo $this->Form->input('OnlineApplicant.department_id',array('label'=>'Select Department','type'=>'select','empty'=>'---Select Department --'));
			 }
			
			 ?>  
	</td>
        
<td> <?php 
			echo $this->Form->input('OnlineApplicant.program_type_id'); ?>
	</td>


</tr>
    <tr><td>

<?php 
echo $this->Form->input('OnlineApplicant.name');
?>

</td>
<td>
<?php 
echo $this->Form->input('OnlineApplicant.limit',array('type'=>'number'));
?>

</td></tr>
	<tr><td><?php echo $this->Form->Submit('Continue',array('div'=>false,'name'=>'getonlineapplicant',
'class'=>'tiny radius button bg-blue')); ?> </td>	
</tr></table>
<?php } ?>
<?php 
    if (!empty($onlineApplicants)) {
?>
<table>
   <tr><th colspan=11 class="smallheading"><?php echo  __('Select List of student you want to batch admit.');?></th></tr>
	<tr>
	        
            <th><?php echo ('No.'); ?> </th>
            <th style="padding:0">
            <?php echo 'Select/ Unselect All <br/>'.$this->Form->checkbox("SelectAll", 
            array('id' => 'select-all','checked'=>'')); ?> </th> 
            <th><?php echo ('Full Name');?></th>
			<th><?php echo ('Gender');?></th>
			<th><?php echo ('Entrance Result');?></th>
		
			<th><?php echo ('Department');?></th>
			<th><?php echo ('Academic Year');?></th>
			
	</tr>
	<?php
	$i = 0;
	$serial_number=1;
	
	foreach ($onlineApplicants as $onlineApplicant):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
       
        <td><?php echo $serial_number++;?></td>
        <td><?php echo $this->Form->checkbox('OnlineApplicant.approve.' . $onlineApplicant['OnlineApplicant']['id'],array('class'=>'checkbox1')); ?>&nbsp;</td> 
       
        <td><?php echo $onlineApplicant['OnlineApplicant']['full_name']; ?>&nbsp;</td>
		<td><?php echo $onlineApplicant['OnlineApplicant']['gender']; ?>&nbsp;</td>
		<td><?php echo $onlineApplicant['OnlineApplicant']['entrance_result']; ?>&nbsp;</td>
		
		<td><?php echo $onlineApplicant['Department']['name']; ?>&nbsp;</td>
		
		<td><?php echo $onlineApplicant['OnlineApplicant']['academic_year']; ?>&nbsp;</td>

		
	</tr>
	
<?php 

endforeach; 
           
echo '<tr><td colspan=8>'.$this->Form->Submit('Process',array('div'=>false,'name'=>'processSelected',
'class'=>'tiny radius button bg-blue')).'</td></tr>';
?>
</table>
<?php 
    }
echo $this->Form->end();
?>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
