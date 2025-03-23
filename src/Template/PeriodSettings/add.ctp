<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="periodSettings form">
<?php echo $this->Form->create('PeriodSetting');?>
<div class="smallheading"><?php echo __('Add Period Setting'); ?></div>
<table cellpadding="0" cellspacing="0">
	<?php
		$period_array = array();
		for($i=1;$i<=15;$i++){
			$period_array[$i] = $i;
		}
		echo "<div class='font'>".$college_name."</div>";
		echo $this->Form->hidden('college_id',array('value'=>$college_id));
		echo '<tr><td>'.$this->Form->input('period',array('type'=>'select','options'=>$period_array)).'</td>';
		echo '<td>'.$this->Form->input('hour',array('label'=>'Starting Time')).'</td></tr>';
	?>
	</table>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
<?php if(isset($periodSettings)) {
            echo '<div class="smallheading">Already Recorded Period Settings</div>';
            echo "<table style='border: #CCC solid 1px'>";
            echo "<tr><th style='border-right: #CCC solid 1px'>S.N<u>o</u></th><th style='border-right: #CCC solid 1px'>Period

				</th><th style='border-right: #CCC solid 1px'>Starting Time</th><th style='border-right: #CCC solid 1px'>

				Action</th></tr>";
			$count = 1;
            foreach($periodSettings as $psk=>$psv){
				echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td><td style='border-right: #CCC solid 1px'>".
					$psv['PeriodSetting']['period']."</td><td style='border-right: #CCC solid 1px'>".
					$this->Format->humanize_hour($psv['PeriodSetting']['hour']).
				"</td><td style='border-right: #CCC solid 1px'>".$this->Html->link(__('Delete'), array('action' => 'delete', $psv['PeriodSetting']['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $psv['PeriodSetting']['hour'],"fromadd")).
				"</td></tr>";
            }
            echo "</table>";
    } ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
