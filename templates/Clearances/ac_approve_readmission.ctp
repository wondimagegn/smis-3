<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<?php 
echo $this->Form->create('Readmission');
?>
<p class="smallheading">Please select academic year, department, program and semester for which you want to approve readmission application.</p>
	<table cellspacing="0" cellpadding="0" class="fs14">
		<tr>
			<td style="width:12%">Academic Year:</td>
			<td style="width:20%"><?php echo $this->Form->input('Search.academic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
			<td style="width:8%">Semester:</td>
			<td style="width:25%"><?php echo $this->Form->input('Search.semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
			
		</tr>
		<tr>
			<td style="width:12%">Department:</td>
			<td style="width:20%"><?php echo $this->Form->input('Search.department_id', 
			array('label' => false, 'class' => 'fs14',
			'options'=>$departments)); ?></td>
			<td style="width:8%">Program:</td>
			<td style="width:25%"><?php echo $this->Form->input('Search.program_id', array('class' => 'fs14',  'style' => 'width:125px', 'label' => false,'options'=>$programs)); ?></td>
			
		</tr>
			<tr>
			<td style="width:12%">Program Type:</td>
			<td style="width:20%"><?php echo $this->Form->input('Search.program_type_id', 
			array('label' => false, 'class' => 'fs14',
			'options'=>$programTypes)); ?></td>
			<td style="width:8%">&nbsp;</td>
			<td style="width:25%">&nbsp;</td>
			
		</tr>
		<tr>
		<td colspan='4'><?php echo $this->Form->submit(__('Filter Readmission Application'), array('name' => 'filterReadmission','class'=>'tiny radius button bg-blue', 'div' => false)); ?></td>
		</tr>
	</table>

  <div class="readmissions index">
<?php 
    if (!empty($readmissions)) {
    $options=array('1'=>'Accept','0'=>'Reject');
   $attributes=array('legend'=>false,'separator'=>"<br/>");
?>
	<div class="smallheading"><?php echo __('List of readmission applicant accepted by registrar and waiting your decision.');?></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('Full Name','student_id');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('Department','department_id');?></th>
			<th><?php echo $this->Paginator->sort('Program','program_id');?></th>
			<th><?php echo $this->Paginator->sort('GPA');?></th>
			<th><?php echo $this->Paginator->sort('academic_commision_approval');?></th>
			<th><?php echo $this->Paginator->sort('minute_number');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($readmissions as $readmission):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($readmission['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $readmission['Student']['id'])); 
			 echo $this->Form->hidden('Readmission.'.$start.'.id',array('label'=>false,'div'=>false,'value'=>$readmission['Readmission']['id'])); 
		     echo $this->Form->hidden('Readmission.'.$start.'.student_id',array('label'=>false,'div'=>false,'value'=>$readmission['Readmission']['student_id'])); 
			
			?>
			
		</td>
		<td><?php echo $readmission['Readmission']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $readmission['Readmission']['semester']; ?>&nbsp;</td>
		<td><?php echo $readmission['Student']['Department']['name']; ?>&nbsp;</td>
		<td><?php echo $readmission['Student']['Program']['name'];; ?>&nbsp;</td>
		<td><?php 
		    if ($readmission['Student']['StudentExamStatus'][0]['sgpa']==0 && 
		    $readmission['Student']['StudentExamStatus'][0]['sgpa']==0) {
		        echo 'SGPA:--';
		    } else {
		         echo 'SGPA:'.$readmission['Student']['StudentExamStatus'][0]['sgpa'].'<br/>';
		         echo 'CGPA:'.$readmission['Student']['StudentExamStatus'][0]['cgpa']; 
		    }
		    
		    ?> &nbsp;</td>
		
		<td><?php 
		      echo $this->Form->radio('Readmission.'.$start.'.registrar_approval',$options,$attributes)
		     
		    ?>
		    &nbsp;</td>
		    <td><?php 
		echo $this->Form->input('Readmission.'.$start.'.minute_number',array('label'=>false,'div'=>false)); 
		
		$start++;
		?>
		
		&nbsp;</td>
		
	
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	
	     echo $this->Form->submit('Accept/Reject Readmission',array('name'=>'saveIt','class'=>'tiny radius button bg-blue','div'=>'false')); 
	    
	   
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
<?php } ?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
