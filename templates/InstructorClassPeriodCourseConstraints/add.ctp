<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
//Get Class Period from a given week day
function getclassperiod() {
            //serialize form data
            var subCat = $("#ajax_weekday_icpcc").val();
$("#ajax_periods_icpcc").attr('disabled', true);
$("#ajax_periods_icpcc").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/instructor_class_period_course_constraints/get_periods/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
$("#ajax_periods_icpcc").attr('disabled', false);
$("#ajax_periods_icpcc").empty();
$("#ajax_periods_icpcc").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="instructorClassPeriodCourseConstraints form">
<?php echo $this->Form->create('InstructorClassPeriodCourseConstraint');?>
<div class="smallheading"><?php echo __('Add Instructor Class Period Constraints'); ?></div>
<div class="info-box info-message"><font color=RED><u>Beaware:</u></font><br/> - All instructor class periods, except you classified as occupied are free by default.</div>
<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academicyear',array('label' => false,'type'=>'select',
			'options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:"",'empty'=>"--Select Academic Year--",'style'=>'width:150PX')).'</td>';
		echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label'=>false,'selected'=>isset($selected_program)?$selected_program:"",'empty'=>"--Select Program--",'style'=>'width:150PX')).'</td>'; 
        echo '<td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label'=>false,'selected'=>isset($selected_program_type)?$selected_program_type:"",'empty'=>"--Select Program Type--",'style'=>'width:150PX')).'</td></tr>'; 
		if($role_id == ROLE_COLLEGE) { 
			echo '<tr><td class="font"> Department</td>'; 
			echo '<td>'. $this->Form->input('department_id',array('label'=>false,'id'=> 'ajax_department_instructor_class_period_course_constraints','selected'=>isset($selected_department)?$selected_department:"",'empty'=>'Pre/(Unassign Freshman)','style'=>'width:150PX')).'</td>';
            echo '<td class="font"> Year Level</td>';
            echo '<td id="ajax_year_level_instructor_class_period_course_constraints">'. $this->Form->input('year_level_id',array('label'=>false,'id'=>'ajax_year_level_icpcc','selected'=>isset($selected_year_level)?$selected_year_level:"",'empty'=>'All','style'=>'width:150PX')).'</td>';  

        } else {
        	echo '<tr><td class="font"> Year Level</td>';
			echo '<tr><td>'. $this->Form->input('year_level_id',array('label'=>false,'selected'=>isset($selected_year_level)?$selected_year_level:"",'empty'=>'All','style'=>'width:150PX')).'</td>';
		}
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"", 'empty'=>'--select semester--','style'=>'width:150PX')).'</td></tr>'; 
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search',
'class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
		
	?> 
</table>
<?php 
	if (isset($instructors)) { 
		$dropdown_data_array= array();
		foreach($instructors as $ik=>$iv){
			$dropdown_data_array[$iv['Staff']['id']]= ($iv['Staff']['Title']['title'].' '.$iv['Staff']['first_name'].' '.$iv['Staff']['middle_name'].' '.$iv['Staff']['last_name'].' ('.$iv['Staff']['Position']['position'].')');
		}
		$week_days_array = array();
		foreach($week_days as $wdsv){
			$week_day_name = null;
			switch($wdsv['ClassPeriod']['week_day']){
				case 1: $week_day_name ="Sunday"; break;
				case 2: $week_day_name ="Monday"; break;
				case 3: $week_day_name ="Tuesday"; break;
				case 4: $week_day_name ="Wednesday"; break;
				case 5: $week_day_name ="Thursday"; break;
				case 6: $week_day_name ="Friday"; break;
				case 7: $week_day_name ="Saturday"; break;
				default : $week_day_name =null;
			}
			$week_days_array[$wdsv['ClassPeriod']['week_day']] = $week_day_name.'('.$wdsv['ClassPeriod']['week_day'].')';
		}
		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr><td class="font" width="580PX">'.$this->Form->input('instructor',array('type'=>'select','empty'=>'---Please Select Instructor---','options'=>$dropdown_data_array)).'</td>';
		echo '<td class="font">'.$this->Form->input('week_day',array('id'=>'ajax_weekday_icpcc', 'type'=>'select','onchange'=>'getclassperiod()', 'empty'=>'---Please Select Week Day---','options'=>$week_days_array,'selected'=>isset($selected_week_day)?$selected_week_day:"")).'</td></tr>';
		?>
		<tr><td colspan="2"><div id="ajax_periods_icpcc">
		<?php
			if(isset($fromadd_periods) && !empty($fromadd_periods)){
				echo '<table><tr><td colspan="3" class="font"> Select Periods</td></tr><tr>';
				foreach($fromadd_periods as $pk=>$pv){
		
					echo '<td>'.$this->Form->input('InstructorClassPeriodCourseConstraint.Selected.'.$pv['PeriodSetting']['period'],array('type'=>'checkbox','value'=>$pv['ClassPeriod']['id'],'label'=>$pv['PeriodSetting']['period'].' ('.$this->Format->humanize_hour($pv['PeriodSetting']['hour']).')')).'</td>';
				}
				echo '</tr></table>';
			}
			?>
		</div></td></tr> 
		<?php
		echo '<td colspan="2">'.$this->Form->input('active',array('label'=>'Option','type'=>'select','options'=>array(1=>'Occupied',0=>'Free'))).'</td></tr>';
		echo '<tr><td colspan="2">'.$this->Form->Submit('Submit',array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'submit')).'</td></tr>';
		echo '</table>';

		if(isset($instructorClassPeriodCourseConstraints)) {
			echo '<div class="smallheading">Already Recorded Instructor Class Period Constraints</div>';
			echo "<table style='border: #CCC solid 1px'>";
			echo "<tr><th style='border-right: #CCC solid 1px'>No.</th>
						<th style='border-right: #CCC solid 1px'>Instructor</th>
						<th style='border-right: #CCC solid 1px'>Position</th>
						<th style='border-right: #CCC solid 1px'>Academic Year</th>
						<th style='border-right: #CCC solid 1px'>Semester</th>
						<th style='border-right: #CCC solid 1px'>Week Day</th>
						<th style='border-right: #CCC solid 1px'>Period</th>
						<th style='border-right: #CCC solid 1px'>Option</th>
						<th style='border-right: #CCC solid 1px'>Action</th></tr>";
			$count = 1;
			foreach($instructorClassPeriodCourseConstraints as $instructorClassPeriodCourseConstraint){
				$week_day_name = null;
				switch($instructorClassPeriodCourseConstraint['ClassPeriod']['week_day']){
					case 1: $week_day_name ="Sunday"; break;
					case 2: $week_day_name ="Monday"; break;
					case 3: $week_day_name ="Tuesday"; break;
					case 4: $week_day_name ="Wednesday"; break;
					case 5: $week_day_name ="Thursday"; break;
					case 6: $week_day_name ="Friday"; break;
					case 7: $week_day_name ="Saturday"; break;
					default : $week_day_name =null;
				}
				$active = null;
				if($instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['active'] == 1){
					$active = "Occupied";
				} else {
					$active = "Free";
				}
				echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td>
					<td style='border-right: #CCC solid 1px'>".$instructorClassPeriodCourseConstraint['Staff']['Title']['title'].' '.$instructorClassPeriodCourseConstraint['Staff']['first_name'].' '.$instructorClassPeriodCourseConstraint['Staff']['middle_name'].' '.$instructorClassPeriodCourseConstraint['Staff']['last_name']."</td>
					<td style='border-right: #CCC solid 1px'>".$instructorClassPeriodCourseConstraint['Staff']['Position']['position']."</td>
					<td style='border-right: #CCC solid 1px'>".$instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['academic_year']."</td>
					<td style='border-right: #CCC solid 1px'>".$instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['semester']."</td>
					<td style='border-right: #CCC solid 1px'>".$week_day_name.' ('.$instructorClassPeriodCourseConstraint['ClassPeriod']['week_day'].')'."</td>
					<td style='border-right: #CCC solid 1px'>".$instructorClassPeriodCourseConstraint['ClassPeriod']['PeriodSetting']['period'] .' ('.$this->Format->humanize_hour($instructorClassPeriodCourseConstraint['ClassPeriod']['PeriodSetting']['hour']).')'."</td>
					<td style='border-right: #CCC solid 1px'>".$active."</td>
					<td style='border-right: #CCC solid 1px'>".
				//$this->Html->link(__('View'), array('action' => 'view', $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['id'])).'&nbsp;&nbsp;&nbsp;'. 
				$this->Html->link(__('Delete'), array('action' => 'delete', $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['id'],"fromadd"),
					null, sprintf(__('Are you sure you want to delete?'), $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['id'],"fromadd")).
				"</td></tr>";
			}
			echo "</table>";
		}
	}
echo $this->Form->end();
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
