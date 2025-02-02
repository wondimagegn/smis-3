<script>
function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">

<div class="makeupExams index">
<?php echo $this->Form->create('MakeupExam');?>
<div class="smallheading"><?php echo __('View makeup exam assignment and suplementary exam result.');?></div>
<table cellspacing="0" cellpadding="0" class="fs14">
	<tr>
		<td style="width:15%">Acadamic Year:</td>
		<td style="width:25%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'type' => 'select', 'options' => $acyear_array_data, 'default' => $defaultacademicyear)); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:55%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'label' => false, 'options' => array('0' => 'Any Semster', 'I' => 'I', 'II' => 'II', 'III' => 'III'))); ?></td>
	</tr>
	<tr>
		<td>Program:</td>
		<td><?php echo $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs)); ?></td>
		<td>Program Type:</td>
		<td><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $program_types)); ?></td>
	</tr>
	<tr>
		<td colspan="4">
		<?php 
		echo $this->Form->submit(__('View Makeup Exams'), array( 'div' => false,'class'=>'tiny radius button bg-blue')); 
		?>

		</td>
	</tr>
</table>
<?php echo $this->Form->end(); ?>
<?php
if(isset($this->request->data)) {
	if(count($makeup_exams) > 0) {
	?>
	<table>
		<tr>
			<th style="width:5%"></th>
			<th style="width:17%">Student Name</th>
			<th style="width:10%">Student ID</th>
			<th style="width:15%">Minute Number</th>
			<th style="width:32%">Makeup Exam Taken for</th>
			<!--<th style="width:28%">Makeup Exam</th>-->
			<th style="width:13%">Grade</th>
			<th style="width:8%">Action</th>
		</tr>
	<?php
		$count = 1;
		foreach($makeup_exams as $key => $makeup_exam) {
			?>
			<tr>
				<td onclick="toggleView(this)" id="<?php echo $count; ?>"><?php echo $this->Html->image('plus2.gif', array('id' => 'i'.$count)); ?></td>
				<td><?php echo $makeup_exam['student_name']; ?></td>
				<td><?php echo $makeup_exam['student_id']; ?></td>
				<td><?php echo (isset($makeup_exam['ExamGradeChange']) ? $makeup_exam['ExamGradeChange']['minute_number'] : $makeup_exam['minute_number']); ?></td>
				<td><?php echo $makeup_exam['exam_for']; ?></td>
				<td><?php echo (isset($makeup_exam['ExamGradeChange']) ? '<span class="'.$makeup_exam['ExamGradeChange']['state'].'">'.$makeup_exam['ExamGradeChange']['grade'].'</span>' : '<span class="on-process">Not Submited</span>'); ?></td>
				<!--<td><?php echo ($makeup_exam['taken_exam'] ? $makeup_exam['taken_exam'] : '---'); ?></td>-->
				<td>
				<?php
					if(isset($makeup_exam['ExamGradeChange'])) {
						if($makeup_exam['ExamGradeChange']['initiated_by_department'] == 1 && $makeup_exam['ExamGradeChange']['registrar_approval'] == null)
							echo $this->Html->link(__('Delete'), array('controller' => 'exam_grade_changes', 'action' => 'delete', $makeup_exam['ExamGradeChange']['id']), null, sprintf(__('Are you sure you want to delete %s \'s makeup exam result?'), $makeup_exam['student_name']));
						else
							echo '---';
					}	
					//needs modification after modifying instructor makeup exam submision
					else if(empty($makeup_exam['ExamGrade']) && empty($makeup_exam['ExamResult']))
						echo $this->Html->link(__('Delete'), array('action' => 'delete', $makeup_exam['id']), null, sprintf(__('Are you sure you want to delete %s \'s makeup exam?'), $makeup_exam['student_name']));
					else
						echo '---';
				?>
				</td>
			</tr>
			<tr id="c<?php echo $count; ?>" style="display:none">
				<td>&nbsp;</td>
				<td colspan="6">
					<table>
						<tr>
							<td style="width:20%; font-weight:bold">Section Where Exam Taken:</td>
							<td style="width:80%"><?php
							echo ((isset($makeup_exam['section_exam_taken']) && !empty($makeup_exam['section_exam_taken']))? $makeup_exam['section_exam_taken'] : '---'); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Taken Exam:</td>
							<td><?php echo ((isset($makeup_exam['taken_exam']) && !empty($makeup_exam['taken_exam']))? $makeup_exam['taken_exam'] : '---'); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Date the Student Assigned:</td>
							<td><?php echo (isset($makeup_exam['created']) ? $this->Format->humanize_date($makeup_exam['created']) : '---'); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Date Grade Submitted:</td>
							<td><?php echo (isset($makeup_exam['ExamGradeChange']['created']) ? $this->Format->humanize_date($makeup_exam['ExamGradeChange']['created']) : '---'); ?></td>
						</tr>
						<tr>
							<td style="font-weight:bold">Grade Status:</td>
							<td <?php if(isset($makeup_exam['ExamGradeChange']['state'])) echo 'class="'.$makeup_exam['ExamGradeChange']['state'].'"'; ?>><?php echo (isset($makeup_exam['ExamGradeChange']['state']) ? $makeup_exam['ExamGradeChange']['description'] : '---'); ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<?php
		$count++;
		}
	?>
	</table>
	<?php
	}
	else {
		echo '<p class="fs14">There is no makeup exam taken for the selected criteria.</p>';
	}
}
?>
</div>            
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

