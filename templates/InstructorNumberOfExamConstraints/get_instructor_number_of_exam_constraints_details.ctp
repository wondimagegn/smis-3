<?php
if(isset($instructor_id)){
?>
	<table style='border: #CCC solid 1px'>
	<?php 
		echo "<tr><td clas='font'>".$this->Form->input('InstructorNumberOfExamConstraint.max_number_of_exam')."</td>";
		echo '<td>'. $this->Form->input('InstructorNumberOfExamConstraint.year_level_id',array('id'=>'yearlevel','type'=>'select', 'multiple'=>'checkbox')).'</td></tr>';
		echo "<tr><td colspan='2'>".$this->Form->Submit('Submit', array('name'=>'submit','div'=>false,'class'=>'tiny radius button bg-blue'))."</td></tr>";
		
		?>
	</table>
	<table style='border: #CCC solid 1px'>
		<tr><td colspan="8" class="centeralign_smallheading"><?php echo("Already Recorded Instructor Number of Exam Constraints.")?></td></tr>
		<tr><th style='border-right: #CCC solid 1px'>S.N<u>o</u>.</th>
		<th style='border-right: #CCC solid 1px'>Instructor</th>
		<th style='border-right: #CCC solid 1px'>Position</th>
		<th style='border-right: #CCC solid 1px'>Year Level</th>
		<th style='border-right: #CCC solid 1px'>Academic Year</th>
		<th style='border-right: #CCC solid 1px'>Semester</th>
		<th style='border-right: #CCC solid 1px'>Maximum Number of Exam</th>
		<th style='border-right: #CCC solid 1px'>Actions</th></tr>
		<?php
		$count = 1;
	foreach($instructorNumberOfExamConstraints as $ineck=>$inecv){
			echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td>";
			if(isset($inecv['StaffForExam'])){
				echo "<td style='border-right: #CCC solid 1px'>".$inecv['StaffForExam']['Staff']['Title']['title'].' '.$inecv['StaffForExam']['Staff']['full_name']."</td>";

				echo "<td style='border-right: #CCC solid 1px'>".$inecv['StaffForExam']['Staff']['Position']['position']."</td>";
			} else {
				echo "<td style='border-right: #CCC solid 1px'>".$inecv['Staff']['Title']['title'].' '.$inecv['Staff']['full_name']."</td>";

				echo "<td style='border-right: #CCC solid 1px'>".$inecv['Staff']['Position']['position']."</td>";
			}
				echo "<td style='border-right: #CCC solid 1px'>".$inecv['InstructorNumberOfExamConstraint']['year_level_id']."</td>";

				echo "<td style='border-right: #CCC solid 1px'>".$inecv['InstructorNumberOfExamConstraint']['academic_year']."</td>";
				echo "<td style='border-right: #CCC solid 1px'>".$inecv['InstructorNumberOfExamConstraint']['semester']."</td>";
				echo "<td style='border-right: #CCC solid 1px'>".$inecv['InstructorNumberOfExamConstraint']['max_number_of_exam']."</td>";
				echo "<td style='border-right: #CCC solid 1px'>".$this->Html->link(__('Delete'), array('action' => 'delete', $inecv['InstructorNumberOfExamConstraint']['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $inecv['InstructorNumberOfExamConstraint']['id'],"fromadd"))."</td></tr>";

		}
	?> </table>
<?php
}
?>
