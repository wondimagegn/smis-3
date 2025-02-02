<?php
if(isset($periods) && !empty($periods)){
		echo '<table><tr><td colspan="3" class="font"> Select Periods</td></tr><tr>';
		foreach($periods as $pk=>$pv){
			
			echo '<td>'.$this->Form->input('ClassRoomClassPeriodConstraint.Selected.'.$pv['PeriodSetting']['period'],array('type'=>
				'checkbox',	'value'=>$pv['ClassPeriod']['id'],'label'=>$pv['PeriodSetting']['period'].' ('.
				$this->Format->humanize_hour($pv['PeriodSetting']['hour']).')')).'</td>';
		}
		echo '</tr>';
} else {
	echo '<tr><td colspan="6" class="info-box info-message"> There is no class period in the selected program, program type and week day.</td></tr>';
} 
echo '</table>';
?>
