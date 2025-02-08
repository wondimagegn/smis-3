<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
function updateSubCategory() {
          
            var formData = $("#weekday").val() + " " + $("#program_id").val() + " " + $("#program_type_id").val();
			$("#already_recorded_setting_id").empty().html('<img src="'+image.src+'" class="displayed" />');
			//$("#already_recorded_setting_id").append("Loading...");
			
		    //get form action
                var formUrl = '/class_periods/get_already_recorded_periods/'+formData;
                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: formData,
                    success: function(data,textStatus,xhr){
				
						    $("#already_recorded_setting_id").empty();
						    $("#already_recorded_setting_id").append(data);
						   
				    },
                    error: function(xhr,textStatus,error){
                            alert(textStatus);
                    }
			    });
		
		return false;
}
</script>

<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="classPeriods form">
<?php echo $this->Form->create('ClassPeriod');?>
<div class="smallheading"><?php echo __('Add Class Period'); ?></div>
<table cellpadding="0" cellspacing="0" style="border: #CCC solid 1px">
	<?php
	echo "<div class='font'>".$college_name."</div>";
	echo $this->Form->hidden('college_id',array('value'=>$college_id));
		echo '<tr><td>'.$this->Form->input('program_id',array('id'=>'program_id')).'</td>';
		echo '<td>'.$this->Form->input('program_type_id',array('id'=>'program_type_id')).'</td>';
		echo '<td>'.$this->Form->input('week_day',array('id'=>'weekday','type'=>'select','onchange'=>'updateSubCategory()',
		'empty'=>'---Select Week Day---',
			'options'=>array(1=>'Sunday(1)',2=>'Monday(2)',3=>'Tuesday(3)',4=>'Wednesday(4)',5=>'Thursday(5)',
			6=>'Friday(6)',7=>'Saturday(7)'),'selected'=>isset($selected_week_day)?$selected_week_day:'')).'</td></tr>';
		
		?>
		<tr><td colspan="3">
		<div id='already_recorded_setting_id'>
		<table><tr><td>
		  <?php
				if(isset($unrecorded_periods_array_fromadd)){
					?><table><tr> <?php
					foreach($unrecorded_periods_array_fromadd as $upk=>$upv){
						echo '<td>'.$this->Form->input('ClassPeriod.Selected.'.$upv['PeriodSetting']['id'],array('type'=>'checkbox',
							'value'=>$upv['PeriodSetting']['id'],'label'=>$upv['PeriodSetting']['period'].' ('.
							$this->Format->humanize_hour($upv['PeriodSetting']['hour']).')')).'</td>';
					}
					?></tr>
					<tr><td colspan="<?php echo count($unrecorded_periods_array_fromadd); ?>">
					<?php echo $this->Form->Submit('Submit',array('class'=>'tiny radius button bg-blue')); ?>
					</td></tr></table>
					</td></tr>
					<tr>
					<td><?php
				}
				if(isset($already_recorded_periods_array_fromadd)) {
					?><div class="smallheading">Already Recorded Class Period Settings</div>
					<table style='border: #CCC solid 1px'>
					<tr><th style='border-right: #CCC solid 1px'>S.N<u>o</u>.</th><th style='border-right: #CCC solid 1px'>Week Day
						</th><th style='border-right: #CCC solid 1px'>Periods</th><th style='border-right: #CCC solid 1px'>
						Action</th></tr>
					<?php
					$count = 1;
					foreach($already_recorded_periods_array_fromadd as $arpk=>$arpv){
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
						/*$this->Html->link(__('View'), array('action' => 'view', $arpk)).'&nbsp;&nbsp;&nbsp;'.*/
						$this->Html->link(__('Delete'), array('action' => 'delete', $arpk,"fromadd"),
							null, sprintf(__('Are you sure you want to delete # %s?'), $arpk,"fromadd")).
						"</td></tr>";
					}
					?></table><?php
				} 
	?>
	</td></tr></table>
	</div>
	</td></tr></table>
<?php echo $this->Form->end();?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
