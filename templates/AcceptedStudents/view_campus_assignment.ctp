<?php echo $this->Form->create('AcceptedStudent', array('action' => 'view_campus_assignment'));?> 
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div>
<?php //if(!isset($show_list_generated)) { ?>
<div class="smallheading"><?php echo __('Export/Print Student Campus Assignments')?></div>
<table cellpadding="0" cellspacing="0"><tr> 
	<?php 
			echo '<tr><td colspan=2>'.$this->Form->input('AcceptedStudent.academicyear',array('id'=>'academicyear',
                'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
                'empty'=>"--Select Academic Year--",'selected'=>isset($selectedsacdemicyear)?$selectedsacdemicyear:'')).'</td><td>'.$this->Form->input('AcceptedStudent.sex',array('label'=>'Gender','required'=>false,'type'=>'select','empty'=>'All',
		        'options' => array('female' => 'Female', 'male' => 'Male'))).'</td></tr>';
		echo '<tr><td>'. $this->Form->input('AcceptedStudent.college_id').
'</td><td>'.$this->Form->input('AcceptedStudent.campus_id').'</td></tr>';

		
            echo '<tr><td>'. $this->Form->input('AcceptedStudent.program_id',array('empty'=>"--Select Program--")).'</td>'; 
            echo '<td>'. $this->Form->input('AcceptedStudent.program_type_id',array('empty'=>"--Select Program Type--")).'</td></tr>'; 
    ?>
		<tr><td colspan="2"><?php echo $this->Form->input('Search.limit',array('label'=>'Limit'));?></td></tr>
		<tr><td colspan="2";><?php echo $this->Form->submit('Search',array('name'=>'search','div'=>'false',
'class'=>'tiny radius button bg-blue')); ?> </td></tr>
	
</table>
<?php 
if(!empty($acceptedStudents)){
?>
	<table>
	<?php

		echo '<tr><td class="smallheading" colspan="2">College: '.$selected_college_name.'</td></tr>';
	
		echo '<tr><td class="smallheading" colspan="2">Department: '.$selected_campus_name.'</td></tr>';

		echo '<tr><td class="smallheading" colspan="2">Program: '.$selected_program_name.'</td></tr>';
		echo '<tr><td class="smallheading" colspan="2">Program Type: '.$selected_program_type_name.'</td></tr>';
		echo '<tr><td class="smallheading">Academic Year: '.$selectedsacdemicyear.'</td>';
		echo '<td style="text-align:right;">'.$this->Html->link($this->Html->image("/img/pdf_icon.gif",
		array("alt"=>"Print To Pdf")), 
				array('action' => 'print_students_number_pdf'),array('escape'=>false))."Print".  
			$this->Html->link($this->Html->image("/img/xls-icon.gif",array("alt"=>"Export TO Excel")),
			 array('action' => 
				'export_students_number_xls'),array('escape'=>false))."Export".'</td></tr>';
	?>
	</table>
	<table cellpadding=0 cellspacing=0 width="60%">
    <tbody>
	<tr>
            <th><?php echo "No";?></th>
			<th><?php echo $this->Paginator->sort('full_name',
			'Full Name');?></th>
			<th><?php echo $this->Paginator->sort('sex',"Sex");?></th>
			<th><?php echo $this->Paginator->sort('studentnumber',
			"Student Id");?></th>
			<th><?php echo $this->Paginator->sort('region_id',
			"Region");?></th>
			
	</tr>
	<?php
	$i = 0;
	$count =1;
	foreach ($acceptedStudents as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
	
		<td><?php echo $count++; ?>&nbsp;</td>
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
	    <td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['Region']['name']; ?>&nbsp;</td>
	
	</tr>
<?php endforeach; ?>
    </tbody>
	</table>
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
<?php 
} else if(empty($acceptedStudents) && !($isbeforesearch)){
    echo "<div class='info-box info-message'> <span></span> No Accepted students have student identification in the selected

	criteria. If you have students in these criteria, Please go to Generate Student Id page and generate student id before export/print</div>";
}
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<?php echo $this->Form->end();?>
