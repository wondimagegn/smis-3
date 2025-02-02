<?php
if(!empty($exam_period_dates_array)){
?>
		<table style='border: #CCC solid 1px'>
		<tr><th style='border-right: #CCC solid 1px'>S.N<u>o</u>.</th>
		<th style='border-right: #CCC solid 1px'>Date</th>
		<th style='border-right: #CCC solid 1px'>1st Session(Morning)</th>
		<th style='border-right: #CCC solid 1px'>2nd Session(Afternoon)</th>
		<th style='border-right: #CCC solid 1px'>3rd Session(Evening)</th>
		<th style='border-right: #CCC solid 1px'>Option</th></tr>
		<?php
		$count = 1;
	foreach($exam_period_dates_array as $epdak=>$epdav){
		$option_select_count = 0;
		echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td>
			<td style='border-right: #CCC solid 1px'>".$this->Format->short_date($epdav).' ('.date("l",strtotime($epdav)).')'."</td>";
		if(isset($excluded_session_by_date[$epdav][1])){
			$option_select_count++;
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td>";
		} else if(isset($already_recorded_course_exam_constraints_by_date[$epdav][1])){
			$option_select_count++;
			$active = null;
			if($already_recorded_course_exam_constraints_by_date[$epdav][1]['active'] == 1){
				$active = "Assign";
			} else {
				$active = "Do Not Assign";
			}
			echo "<td style='border-right: #CCC solid 1px'>".$active.' ('.$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_course_exam_constraints_by_date[$epdav][1]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_course_exam_constraints_by_date[$epdav][1]['id'],"fromadd")).')'."</td>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('CourseExamConstraint.Selected.'.$epdak.'-1',array('type'=>'checkbox','value'=>$epdak.'-1', 'label'=>false))."</td>";
		}
		if(isset($excluded_session_by_date[$epdav][2])){
			$option_select_count++;
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td>";
		} else if(isset($already_recorded_course_exam_constraints_by_date[$epdav][2])){
			$option_select_count++;
			$active = null;
			if($already_recorded_course_exam_constraints_by_date[$epdav][2]['active'] == 1){
				$active = "Assign";
			} else {
				$active = "Do Not Assign";
			}
			echo "<td style='border-right: #CCC solid 1px'>".$active.' ('.$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_course_exam_constraints_by_date[$epdav][2]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_course_exam_constraints_by_date[$epdav][2]['id'],"fromadd")).')'."</td>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('CourseExamConstraint.Selected.'.$epdak.'-2',array('type'=>'checkbox','value'=>$epdak.'-2', 'label'=>false))."</td>";
		}
		if(isset($excluded_session_by_date[$epdav][3])){
			$option_select_count++;
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td>";
		} else if(isset($already_recorded_course_exam_constraints_by_date[$epdav][3])){
			$option_select_count++;
			$active = null;
			if($already_recorded_course_exam_constraints_by_date[$epdav][3]['active'] == 1){
				$active = "Assign";
			} else {
				$active = "Do Not Assign";
			}
			echo "<td style='border-right: #CCC solid 1px'>".$active.' ('.$this->Html->link(__('Delete'), array('action' => 'delete', $already_recorded_course_exam_constraints_by_date[$epdav][3]['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $already_recorded_course_exam_constraints_by_date[$epdav][3]['id'],"fromadd")).')'."</td>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('CourseExamConstraint.Selected.'.$epdak.'-3',array('type'=>'checkbox','value'=>$epdak.'-3', 'label'=>false))."</td>";
		}
		if($option_select_count == 3){
			echo "<td style='border-right: #CCC solid 1px'></td></tr>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('CourseExamConstraint.active.'.$epdak,array('label'=>false,'type'=>'select','options'=>array(1=>'Assign',0=>'Do Not Assign')))."</td></tr>";
		}
	}
	?> </table>
	<?php echo $this->Form->Submit('Submit', array('name'=>'submit','class'=>'tiny radius button bg-blue','div'=>false));?>
<?php
}
?>
