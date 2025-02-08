<table> <tr><td>
<?php
//if(!empty($periodSettings)){
	if(isset($unrecorded_periods_array)){
		?><table><tr> <?php
			foreach($unrecorded_periods_array as $upk=>$upv){
				echo '<td>'.$this->Form->input('ClassPeriod.Selected.'.$upv['PeriodSetting']['id'],array('type'=>'checkbox',
					'value'=>$upv['PeriodSetting']['id'],'label'=>$upv['PeriodSetting']['period'].' ('.
					$this->Format->humanize_hour($upv['PeriodSetting']['hour']).')')).'</td>';
			}
		?></tr>
			<tr><td colspan="<?php echo count($unrecorded_periods_array); ?>">
			<?php echo $this->Form->Submit('Submit',
array('class'=>'tiny radius button bg-blue')); ?>
			</td></tr></table>
			</td></tr>
			<tr>
			<td><?php
	}
	if(isset($already_recorded_periods_array)) {
		?><div class="smallheading">Already Recorded Class Period Settings</div>
		<table style='border: #CCC solid 1px'>
		<tr><th style='border-right: #CCC solid 1px'>No.</th><th style='border-right: #CCC solid 1px'>Week Day
			</th><th style='border-right: #CCC solid 1px'>Periods</th><th style='border-right: #CCC solid 1px'>
			Action</th></tr>
		<?php
		$count = 1;
		foreach($already_recorded_periods_array as $arpk=>$arpv){
			$week_day_name = null;
			switch($selected_week_day){
				case 1: $week_day_name ="Sunday"; break;
				case 2: $week_day_name ="Monday"; break;
				case 3: $week_day_name ="Tuesday"; break;
				case 4: $week_day_name ="Wednesday"; break;
				case 5: $week_day_name ="Thursday"; break;
				case 6: $week_day_name ="Friday"; break;
				case 7: $week_day_name ="Saturday"; break;
				default : $week_day_name =null;
			}
			echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td><td style='border-right: #CCC solid 1px'>".
				$selected_week_day.' ('.$week_day_name.')'."</td><td style='border-right: #CCC solid 1px'>".
				$arpv['PeriodSetting']['period'].' ('.$this->Format->humanize_hour($arpv['PeriodSetting']['hour']).')'.
			"</td><td style='border-right: #CCC solid 1px'>".
			//$this->Html->link(__('View'), array('action' => 'view', $arpk)).'&nbsp;&nbsp;&nbsp;'. 
			$this->Html->link(__('Delete'), array('action' => 'delete', $arpk,"fromadd"),
				null, sprintf(__('Are you sure you want to delete # %s?'), $this->Format->humanize_hour($arpv['PeriodSetting']['hour']),"fromadd")).
			"</td></tr>";
		}
		?></table><?php
	}
	?>
	</td></tr></table> <?php
/*} else {
	$this->Session->setFlash('<span></span> Please set period setting first in ', 
		"session_flash_link", array(
		"class"=>'info-box info-message',
		"link_text" => " this page",
		"link_url" => array(
		"controller" => "periodSettings",
		"action" => "add",
		"admin" => false
		)
		));
}*/
?>
