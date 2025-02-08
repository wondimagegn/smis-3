<?php echo $this->Html->script('jquery-1.6.2.min'); ?>  
<?php echo $this->Html->script('jquery-selectall'); ?> 
<?php echo $this->Form->create('AcceptedStudent');?> 
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            	
<div>
<?php 
//only visible when the user clicks add reserved place
if(!isset($selected_academic_year)){
  echo '<table><tbody>';
	   echo '<tr><td>';  
		echo $this->Form->input('academicyear',array('id'=>'admissionyear',
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($selected)?$selected:''));
		echo '</td></tr>';
		
		 echo '<tr><td>';    
		    echo $this->Form->Submit('Submit',array('div'=>false,
 'name'=>'prepandacademicyear','class'=>'tiny radius button bg-blue'));
		 echo '</td></tr>';
		 echo '</tbody></table>';
} else {
   
?>

<?php 
if(!empty($acceptedStudents)){
?>
<div class="acceptedStudents index">
	
	<table  cellpadding="0" cellspacing="0">
	<tbody>
	    <tr>
	    
	    </tr>
	</tbody>
	</table>
	<table  cellpadding="0" cellspacing="0"> 
	<tr>
	
 <td>
  <?php echo $this->Form->Submit('Cancel Auto Placement',array('div'=>false,
 'name'=>'cancelplacement','class'=>'tiny radius button bg-blue'));?>
 </td>
	</tr>
    <tr>
            
            <th><?php echo $this->Paginator->sort('full_name');?></th>
			<th><?php echo $this->Paginator->sort('sex');?></th>
			<th><?php echo $this->Paginator->sort('studentnumber');?></th>

			<th><?php echo $this->Paginator->sort('EHEECE_total_results');?></th>
			<th><?php echo $this->Paginator->sort('department_id');?></th>
			<th><?php echo $this->Paginator->sort('program_type_id');?></th>
			<th><?php echo $this->Paginator->sort('academicyear');?></th>
			
			<th><?php echo $this->Paginator->sort('placementtype');?></th> 
		
	</tr>
	
	<?php
	
	$i = 0;
	foreach ($acceptedStudents as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
        <?php echo $this->Form->hidden('AcceptedStudent.'.$acceptedStudent['AcceptedStudent']['id'].'.id',array('value'=>$acceptedStudent['AcceptedStudent']['id'])); ?>
       
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
	
		<td><?php echo $acceptedStudent['AcceptedStudent']['EHEECE_total_results']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($acceptedStudent['Department']['name'], array('controller' => 'departments', 'action' => 'view', $acceptedStudent['Department']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($acceptedStudent['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $acceptedStudent['ProgramType']['id'])); ?>
		</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['academicyear']; ?>&nbsp;</td>
	
		
		<td><?php echo $acceptedStudent['AcceptedStudent']['placementtype']; ?>&nbsp;</td>
		
	</tr>
<?php endforeach; ?>  
	</table>
    <table cellpadding="0" cellspacing="0"><tbody>
        <tr><td><tr>
 
</tr>
    </tbody></table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<?php 
} else {
    echo "<div class='flash'>No Accepted students in the selected academic year</div>";
}

}
 echo $this->Js->writeBuffer(); // Write cached scripts
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
