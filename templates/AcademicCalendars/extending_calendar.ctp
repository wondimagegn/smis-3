<script>
function toggleViewFullId(id) {
	if($('#'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Hide Filter');
		}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Display Filter');
		}
	$('#'+id).toggle("slow");
}
</script>

<?php echo $this->Form->create('AcademicCalendar', array('action' => 'extending_calendar'));?> 
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
               
       <div 
       onclick="toggleViewFullId('ExtendCalendar')"><?php 
	if (!empty($academicCalendars)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ExtendCalendarImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ExtendCalendarTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ExtendCalendarImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ExtendCalendarTxt">Hide Filter</span><?php
		}
?></div>
 <div id="ExtendCalendar" style="display:<?php echo (!empty($academicCalendars) ? 'none' : 'display'); ?>">
 <div class="smallheading">Please select the academic year,semester, program and program type, you want to extend academic calendar.</div>
 	
 <table cellspacing="0" cellpadding="0" class="fs14">
	<tr>
		<td style="width:15%">Academic Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('Search.academic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:50%"><?php echo $this->Form->input('Search.semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); ?></td>
	</tr>
	<tr>
		<td>Program:</td>
		<td><?php echo $this->Form->input('Search.program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => (isset($program_id) ? $program_id : false))); ?></td>
		<td>Program Type:</td>
		<td><?php echo $this->Form->input('Search.program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programTypes, 'default' => (isset($program_type_id) ? $program_type_id : false))); ?></td>
	</tr>
	
	<tr>
		<td colspan="6">
		<?php  echo $this->Form->Submit(__('Continue'),array('div'=>false,
 'name'=>'searchbutton','class'=>'tiny radius button bg-blue')); ?>
		</td>
	</tr>
</table>
</div>

<?php

if(!empty($academicCalendars)){

echo "<table>";

echo "<tr><td>".$this->Form->input('ExtendingAcademicCalendar.academic_calendar_id',array('empty'=>'--select academic calendar--',
'style'=>'width:250px;','required'=>true))."</td></tr>";
echo "</table>";
		echo '<table>';
		echo '<tr><td>'.$this->Form->input('ExtendingAcademicCalendar.department_id',array('type'=>'select','style' => 'width:200px;height:auto;','required'=>true, 'multiple' => true,'options'=>$departments)).'</td><td>'.
		$this->Form->input('ExtendingAcademicCalendar.year_level_id',array('type'=>'select','style' => 'width:200px;height:auto;','required'=>true, 'multiple' => true,'options'=>$yearLevels)).'</td></tr>';
		/*
		echo '<tr><td>'.$this->Form->input('ExtendingAcademicCalendar.program_id',array('type'=>'select','style' => 'width:200px;height:auto;','required'=>true, 'multiple' => true,'options'=>$programs)).'</td><td>'.$this->Form->input('ExtendingAcademicCalendar.program_type_id',array('type'=>'select','style' => 'width:200px;height:auto;','required'=>true, 'multiple' => true,'options'=>$programTypes)).'</td></tr>';
		*/
			echo '<tr><td>'.$this->Form->input('ExtendingAcademicCalendar.activity_type',array('type'=>'select','options'=>$activity_types,'label'=>'Which actvity you would like to extend ?','style'=>'width:200px;')).'</td><td>'.
			$this->Form->input('ExtendingAcademicCalendar.days',array('type'=>'number','label'=>'How many days you would like to extend ?','required'=>true,'style'=>'width:70px;')).'</td></tr>';
		
		echo '</table>';
		//activity_types

 $count=0;

?>

	<?php 
	
 
echo '<table><tr><td>'.$this->Form->Submit(__('Extend'),array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'extend')).'</td></tr></table>';
	
}

 ?>

   
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<?php echo $this->Form->end();?>

