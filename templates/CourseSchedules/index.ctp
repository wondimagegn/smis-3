<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
  //Get Department
function getDepartments() {
            //serialize form data
            var col = $("#ajax_college_id").val();
$("#ajax_department_id").attr('disabled', true);
$("#ajax_department_id").empty().html('<img src="/img/busy.gif" class="displayed" >');
$("#ajax_year_level_id").empty();
//get form action
            var formUrl = '/course_schedules/get_departments/'+col;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: col,
                success: function(data,textStatus,xhr){
$("#ajax_department_id").attr('disabled', false);
$("#ajax_department_id").empty();
$("#ajax_department_id").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
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
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="courseSchedules index">
<?php echo $this->Form->create('CourseSchedule');?>
<div class="smallheading"><?php echo __('View Course Schedules');?></div>
	<table cellpadding="0" cellspacing="0">
	
	<?php
		echo '<tr><td class="font"> Academic Year</td>'; 
		echo '<td>'.$this->Form->input('academic_year',array('label' => false,'type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academic_year)?$selected_academic_year:"", 'style'=>'width:150px','empty'=>"--Select Academic Year--")).'</td>';
		echo '<td class="font"> Program</td>'; 
        echo '<td>'. $this->Form->input('program_id',array('label'=>false,'style'=>'width:150px', 'selected'=>isset($selected_program)?$selected_program:"",'empty'=>"--Select Program--")).'</td>'; 
		echo '<td class="font"> Program Type</td>'; 
        echo '<td>'. $this->Form->input('program_type_id',array('label'=>false, 'selected'=>isset($selected_program_type)?$selected_program_type:"",'style'=>'width:150px','empty'=>"--Select Program Type--")).'</td></tr>'; 
        echo '<tr><td class="font"> College</td>'; 
		echo '<td>'. $this->Form->input('college_id',array('label'=>false,'id'=>'ajax_college_id', 'onchange'=>'getDepartments()','style'=>'width:150px','selected'=>isset($selected_college)?$selected_college:"",'options'=>$colleges,'empty'=>'--Select College--')).'</td>';
		echo '<td class="font"> Department</td>'; 
		echo '<td>'. $this->Form->input('department_id',array('label'=>false,'id'=>'ajax_department_id', 'onchange'=>'getYearLevel()','style'=>'width:150px','selected'=>isset($selected_department)?$selected_department:"",'options'=>$departments,'empty'=>'--Select Department--')).'</td>';
		echo '<td class="font"> Year Level</td>'; 
        echo '<td>'. $this->Form->input('year_level_id',array('label'=>false, 'id'=>'ajax_year_level_id','selected'=>isset($selected_year_level)?$selected_year_level:"",'options'=>$yearLevels,'style'=>'width:150px','empty'=>'--Select Year Level--')).'</td></tr>';  
        echo '<tr><td class="font"> Semester</td>'; 
        echo '<td>'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'), 'selected'=>isset($selected_semester)?$selected_semester:"", 'style'=>'width:150px','empty'=>'--select semester--')).'</td></tr>'; 
        echo '<tr><td colspan="6">'. $this->Form->Submit('Continue',array('name'=>'continue','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
		
	?> 
	</table>
<?php 
	if (isset($sections)) { 
		$dropdown_sections= array();
		foreach($sections as $sk=>$sv){
			$count = 1;
			$dropdown_sections[$sv['id']]= $sv['name'];
		}
		$dropdown_sections[10000] = "All";
		
		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr><td class="font">'.$this->Form->input('section_id',array('id'=>'ajax_section', 'type'=>'select','options'=>$dropdown_sections, 'empty'=>'---Please Select Section---','selected'=>isset($selected_section)?$selected_section:"")).'</td></tr>';
		echo '<tr><td>'.$this->Form->Submit('View',array('div'=>false,'name'=>'view','class'=>'tiny radius button bg-blue')).'</td></tr>';
		echo '</table>';
	}
	
 if(isset($section_course_schedule) && !empty($section_course_schedule)){
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



								echo '<td style="border-right: #CCC solid 1px; text-align:center;" colspan="'.$count_td.'">'.$this->Html->link($scsv[$course_schedule_key]['PublishedCourse']['Course']['course_code'].' ('.$scsv[$course_schedule_key]['CourseSchedule']['type'].', '.$class_room.', '.$scsv[$course_schedule_key]['CourseSplitSection']['section_name'].')','#',array('data-animation'=>"fade",
'data-reveal-id'=>'myModal','class'=>'jsview','data-reveal-ajax'=>"/courseSchedules/get_modal/".$scsv[$course_schedule_key]['PublishedCourse']['id'])).'</td>';

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
 echo $this->Form->end();
 
 ?> 
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
