<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
 //Get year level
function getYearLevel() {
            //serialize form data
            var dept = $("#ajax_department_id").val();
$("#ajax_year_level_id").attr('disabled', true);
$("#ajax_year_level_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/course_schedules/get_year_levels/'+dept;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: dept,
                success: function(data,textStatus,xhr){
$("#ajax_year_level_id").attr('disabled', false);
$("#ajax_year_level_id").empty();
$("#ajax_year_level_id").append(data);
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
             
<div class="courseSchedules form">
<?php echo $this->Form->create('CourseSchedule');?>
<div class="smallheading"><?php echo __('Generate Course Schedule'); ?></div>

<div class="font"><?php echo "College/Institute: ".$college_name?></div>
<p class="fs13"><strong>Important Note:</strong> Re run the schedule for each independent program types and department by changing the schedule settings for best result. </p>

<table>
	    <div id="dialog-modal" title="Course Details"></div>
		<tr>
			<td style="width:10%">Academic Year:</td>
			<td style="width:24%"><?php echo $this->Form->input('acadamic_year', array('options' => $acyear_array_data, 'label' => false, 'style' => 'width:150px')); ?></td>
			<td style="width:8%">Semester:</td>
			<td style="width:25%"><?php echo $this->Form->input('semester', array('options' => array('I'=>'I','II'=>'II', 'III'=>'III'), 'label' => false, 'style' => 'width:150px')); ?></td>
			<td style="width:8%">Program:</td>
			<td style="width:25%"><?php echo $this->Form->input('program_id', array('options' => $programs, 'label' => false, 'style' => 'width:200px')); ?></td>
		</tr>
		<tr>
			<td>Program Type:</td>
			<td><?php echo $this->Form->input('program_type_id', array('options' => $programTypes, 'label' => false, 'style' => 'width:200px;height:auto;', 'multiple' => true)); ?></td>
			<td>Department:</td>
			<td><?php echo $this->Form->input('department_id', array('options' => $departments, 'label' => false, 'style' => 'width:200px;height:auto;', 'multiple' => true)); ?></td>
			<td>Year Level:</td>
			<td><?php echo $this->Form->input('year_level_id', array('options' => $yearLevels, 'label' => false, 'style' => 'width:200px;height:auto;', 'multiple' => true)); ?></td>
		</tr>
		
		<tr>
			<td colspan="6">
			<?php echo $this->Form->submit(__('Generate Course Schedule'), array('name' => 'generate','class'=>'tiny radius button bg-blue', 'id' => 'GenerateCourseSchedule', 'div' => false)); ?>
			<div id="GenerateExamScheduleInfo"></div>
			</td>
		</tr>
</table>


<?php if(!empty($section_course_schedule)) {
	//echo '<table><tr><td>'.$this->Form->Submit('Cancel Schedule',array('name'=>'cancel','div'=>false)).'</td></tr></table>';
	foreach($section_course_schedule as $scsk=>$scsv){
		echo '<table style="border: #CCC double 3px ">';
		echo '<tr><td class="smallheading" colspan="2">'.$scsv[0]['Section']['name'].'</td></tr>';
		echo '<tr><td><table style="border: #CCC solid 1px ">';
		$starting =$starting_and_ending_hour['starting'];
		$starting_hour = substr($starting,0,2);
		$other = substr($starting,2);
		$ending = $starting_and_ending_hour['ending'];
		$ending_hour = substr($ending,0,2);
		echo '<tr><td style="border-right: #CCC solid 1px; background-color:#C6A6C6"> Week Day/Periods</td>';
		$time_deference=($ending_hour - $starting_hour);
		$i=0;
		while($i<=$time_deference){
			echo '<td style="border-right: #CCC solid 1px; width:80PX; background-color:#EBF3FB" >'.$this->Format->humanize_hour(($starting_hour+$i).$other).'</td>';
			$i++;
		}
		echo '</tr>';
		for($week_day=1;$week_day<=7;$week_day++){
			$week_day_class_periods = array();
			foreach($scsv as $csk=>$csv){
				if($csv['ClassPeriod'][0]['week_day'] == $week_day){
					foreach($csv['ClassPeriod'] as $cpk=>$cpv){
						$week_day_class_periods[$csk][] = $cpv['PeriodSetting']['hour'];
					}
				}
			}
			//debug($week_day_class_periods);
			if(empty($week_day_class_periods)){
				echo '<tr>';
				switch ($week_day){
					case 1: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Sunday</td>';
							break;
					case 2: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Monday</td>';
							break;
					case 3: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Tuesday</td>';
							break;
					case 4: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Wednesday</td>';
							break;
					case 5: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Thursday</td>';
							break;
					case 6: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Friday</td>';
							break;
					case 7: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Saturday</td>';
							break;
				}
				for($i=$starting_hour;$i<=$ending_hour;$i++){
					echo '<td style="border-right: #CCC solid 1px; background-color:#899F47"></td>';
				}
				echo '</tr>';
			} else {
				echo '<tr>';
				switch ($week_day){
					case 1: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Sunday</td>';
							break;
					case 2: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Monday</td>';
							break;
					case 3: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Tuesday</td>';
							break;
					case 4: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Wednesday</td>';
							break;
					case 5: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Thursday</td>';
							break;
					case 6: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Friday</td>';
							break;
					case 7: echo '<td style="border-right: #CCC solid 1px;background-color:#EBF3FB"> Saturday</td>';
							break;
				}
				$j=0;
				while($j<=$time_deference){
					$scheduled = false;
					foreach($week_day_class_periods as $course_schedule_key=>$wdcpv){
						foreach($wdcpv as $wdcp_hour){

							if(date("H:i:s",strtotime($wdcp_hour)) == date("H:i:s",strtotime(($starting_hour + $j).$other))){
								$count_td = count($wdcpv);
								$class_room = null;
								if(!empty($scsv[$course_schedule_key]['ClassRoom']['room_code'])){
									$class_room = $scsv[$course_schedule_key]['ClassRoom']['room_code'].' - '.$scsv[$course_schedule_key]['ClassRoom']['ClassRoomBlock']['Campus']['name'];
								} else {
									$class_room = "TBA";
								}
								echo '<td style="border-right: #CCC solid 1px; text-align:center;" colspan="'.$count_td.'">'.$this->Html->link(
    $scsv[$course_schedule_key]['PublishedCourse']['Course']['course_code'].' ('.$scsv[$course_schedule_key]['CourseSchedule']['type'].', '.$class_room.', '.$scsv[$course_schedule_key]['CourseSplitSection']['section_name'].')',
    '#',
   array('class'=>'jsview','data-animation'=>"fade",
'data-reveal-id'=>'myModal','data-reveal-ajax'=>"/courseSchedules/get_modal/".$scsv[$course_schedule_key]['PublishedCourse']['id'])
).'</td>';
								$j = $j + $count_td;
								$scheduled = true;
								break 2;
							} 
						}
					}

					if($scheduled == false){
						echo '<td style="border-right: #CCC solid 1px; background-color:#899F47"></td>';
						$j++;
					}
				}
				echo '</tr>';
			}
		}
		echo '</table></td></tr>';
		echo '<tr><td> <div class="info-box info-message"><font color=RED><u>Note:</u></font><br/> -You can find course type, assigned class room and split section name if the orginal section splited for that course, respectively in the bracket.<br/> -You can view course details by clicking on each course code.</div></td></tr>';
		echo '</table>';
		
	}
}
?>
<?php echo $this->Form->end();?> 
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
