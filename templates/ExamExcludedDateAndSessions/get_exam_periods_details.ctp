<?php
if(!empty($date_array)){
?>
		<table style='border: #CCC solid 1px'>
		<tr><th style='border-right: #CCC solid 1px'>S.N<u>o</u>.</th>
		<th style='border-right: #CCC solid 1px'>Date</th>
		<th style='border-right: #CCC solid 1px'>1st Session(Morning)</th>
		<th style='border-right: #CCC solid 1px'>2nd Session(Afternoon)</th>
		<th style='border-right: #CCC solid 1px'>3rd Session(Evening)</th></tr>
		<?php
		$count = 1;
	foreach($date_array as $dak=>$dav){
		echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td>
			<td style='border-right: #CCC solid 1px'>".$this->Format->short_date($dav).' ('.date("l",strtotime($dav)).')'."</td>";
		if(isset($excluded_session_by_date[$dav][1])){
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('ExamPeriod.Selected.'.$dak.'-1',array('type'=>'checkbox','value'=>$dak.'-1','label'=>false))."</td>";
		}
		if(isset($excluded_session_by_date[$dav][2])){
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('ExamPeriod.Selected.'.$dak.'-2',array('type'=>'checkbox','value'=>$dak.'-2','label'=>false))."</td>";
		}
		if(isset($excluded_session_by_date[$dav][3])){
			echo "<td style='border-right: #CCC solid 1px'> Excluded. </td></tr>";
		} else {
			echo "<td style='border-right: #CCC solid 1px'>".$this->Form->input('ExamPeriod.Selected.'.$dak.'-3',array('type'=>'checkbox','value'=>$dak.'-3','label'=>false))."</td></tr>";
		}
	}
	?> </table>
	<?php echo $this->Form->Submit('Submit', array('name'=>'submit','div'=>false,'class'=>'tiny radius button bg-blue'));?>
<?php
	if(isset($examExcludedDateAndSessions)) {
		?><div class="smallheading">Already Recorded Exam Excluded Dates and Sessions</div>
		<table style='border: #CCC solid 1px'>
		<tr><th style='border-right: #CCC solid 1px'>S.N<u>o</u>.</th>
			<th style='border-right: #CCC solid 1px'>Exam Date</th>
			<th style='border-right: #CCC solid 1px'>Session</th>
			<th style='border-right: #CCC solid 1px'>Action</th></tr>
		<?php
		$count = 1;
		foreach($examExcludedDateAndSessions as $eedsk=>$eedsv){
			$session_name =null;
			if($eedsv['ExamExcludedDateAndSession']['session']==1){
				$session_name = "1st";
			} else if($eedsv['ExamExcludedDateAndSession']['session']==2){
				$session_name = "2nd";
			} else if($eedsv['ExamExcludedDateAndSession']['session']==3){
				$session_name = "3rd";
			}
			echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td>
				<td style='border-right: #CCC solid 1px'>".$this->Format->short_date($eedsv['ExamExcludedDateAndSession']['excluded_date']).' ('.date("l",strtotime($eedsv['ExamExcludedDateAndSession']['excluded_date'])).')'."</td>
				<td style='border-right: #CCC solid 1px'>".$session_name.
			"</td><td style='border-right: #CCC solid 1px'>".
			$this->Html->link(__('Delete'), array('action' => 'delete', $eedsv['ExamExcludedDateAndSession']['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $eedsv['ExamExcludedDateAndSession']['id'],"fromadd")).
			"</td></tr>";
		}
		?></table><?php
	} 
}
?>
