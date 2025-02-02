<?php
if(isset($periods) && !empty($periods)){
		echo '<table><tr><td colspan="3" class="font"> Select Periods</td></tr><tr>';
		foreach($periods as $pk=>$pv){
			echo '<td>'.$this->Form->input('InstructorClassPeriodCourseConstraint.Selected.'.$pv['PeriodSetting']['period'],array('type'=>'checkbox',	'value'=>$pv['ClassPeriod']['id'],'label'=>$pv['PeriodSetting']['period'].' ('.$this->Format->humanize_hour($pv['PeriodSetting']['hour']).')')).'</td>';
		}
		echo '</tr></table>';
}
?>
