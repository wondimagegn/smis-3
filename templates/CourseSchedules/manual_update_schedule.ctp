<?php echo $this->Form->create('CourseSchedule');?>
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
<div class="font"><?php echo "College/Institute: ".$college_name?></div>

<div id="dialog-modal" title="Course Details"></div>
<p class="smallheading"><?php echo __('Manual Course Schedule'); ?> .</p>
<p class='fs16'>
    Important Note: Before updating course schedule manaully please auto generate schedule and update it as needed using this tool. 
 </p>
<table cellspacing="0" cellpadding="0" class="fs14">
		<tr>
			<td style="width:12%">Academic Year:</td>
			<td style="width:38%"><?php
			
			echo $this->Form->input('Search.academic_year',array('label' =>false,'type'=>'select',
		'options'=>$acyear_array_data,'empty'=>"--Select Academic Year--",'id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:200px'));?> </td>
			<td style="width:12%">Semester:</td>
			<td style="width:38%"><?php echo $this->Form->input('Search.semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:200px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'),'empty'=>'--select semester--')); ?></td>
			
		</tr>
		<tr>
			
		    <td style="width:12%">Program:</td>
			<td style="width:38%"><?php 
			 echo $this->Form->input('Search.program_id',array('empty'=>"--Select Program--",
			 'label' => false, 'class' => 'fs14','style'=>'width:200px'));
			
			?></td>
			<td style="width:12%">Program Type:</td>
			<td style="width:38%"><?php 
			echo $this->Form->input('Search.program_type_id',array('empty'=>"--Select Program Type--",
			'class' => 'fs14',  'style' => 'width:200px', 'label' => false));
			
			 ?></td>    
			
		</tr>
		<tr>
			<td style="width:8%">Department</td>
			<td style="width:60%"><?php 
			    echo $this->Form->input('Search.department_id',array('label'=>false,'id'=>'ajax_department_id', 'onchange'=>'getYearLevel()','style'=>'width:200px','options'=>$departments,'empty'=>'--Select Department--'));
			?></td>    
		    <td style="width:12%">YearLevel:</td>
			<td style="width:20%"><?php  echo $this->Form->input('Search.year_level_id',array('empty'=>"--Select year Level--",'class' => 'fs14','id'=>'ajax_year_level_id','style' => 'width:200px', 'label' => false)) ?></td>
			
			
		</tr>
		<tr>
		<td colspan='4'><?php echo $this->Form->Submit('Continue',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)); 
		?></td>
		</tr>
</table>
<?php 
 if(!empty($section_course_schedule)) {
	
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
			echo '<td style="border-right: #CCC solid 1px; width:80PX; background-color:#EBF3FB" >'.
			$this->Format->humanize_hour(($starting_hour+$i).$other).'</td>';
			
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
                          
							if(date("H:i:s",strtotime($wdcp_hour)) == date("H:i:s",strtotime((($starting_hour + $j).$other)))) {
								$count_td = count($wdcpv);
								$class_room = null;
								if(!empty($scsv[$course_schedule_key]['ClassRoom']['room_code'])){
									$class_room = $scsv[$course_schedule_key]['ClassRoom']['room_code'].' - '.$scsv[$course_schedule_key]['ClassRoom']['ClassRoomBlock']['Campus']['name'];
								} else {
									$class_room = "TBA";
								}
								
								echo '<td  style="border-right: #CCC solid 1px; text-align:center;" 
								 colspan="'.$count_td.'">'.
								 $scsv[$course_schedule_key]['PublishedCourse']['Course']['course_code'].
								 ' '.$scsv[$course_schedule_key]['CourseSchedule']['type'].
								 ', '.$class_room.', '.
								 $scsv[$course_schedule_key]['CourseSplitSection']['section_name'];
								/*
								 echo $this->Js->link(__('Change '),
								'/course_schedules/change_schedule/'.$scsv[$course_schedule_key]['CourseSchedule']['id'].
								'/'.$csv['ClassPeriod'][0]['id'].'/'.$scsv[$course_schedule_key]['CourseSchedule']['type'],
								array('update'=>'#ajax_schedule_'.$course_schedule_key.'_'.$scsk,'evalScripts'=>true));
*/
	echo $this->Html->link(
    'Change',
    '#',
   array('class'=>'jsview','data-animation'=>"fade",
'data-reveal-id'=>'myModal','data-reveal-ajax'=>'/course_schedules/change_schedule/'.$scsv[$course_schedule_key]['CourseSchedule']['id'].
								'/'.$csv['ClassPeriod'][0]['id'].'/'.$scsv[$course_schedule_key]['CourseSchedule']['type'])
);

								 echo '<table><tr><td  id="ajax_schedule_'.$course_schedule_key.'_'.$scsk.'" >';

								echo '</td></tr></table>';
								
								echo '</td>';
								
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
		
		echo '</table>';
		//debug($unschedule_published_courses);
		echo '<table style="border: #CCC double 3px ">';
	
		if (!empty($section_unscheduled_courses[$scsk])) {
		    echo '<tr>';
		      echo '<th colspan=4 class="smallheading" >Unscheduled Course Of '.$scsv[0]['Section']['name'].' section.</th>';
		    echo '</tr>';
		    echo '<th>Course</th><th>Description</th><th>Manual Schedule</th><th>&nbsp;</th>';
		    echo '</tr>';
		    
		    foreach ($section_unscheduled_courses[$scsk] as $ddd=>$ddv)  {
		                   
		                    echo '<tr>';
		                    echo '<td>';
		                    echo $ddv['PublishedCourse']['Course']['course_code_title'].'('.$ddv['PublishedCourse']['Course']['course_detail_hours'].')';
		                    echo '</td>';
		                     echo '<td>';
		                    echo $ddv['UnschedulePublishedCourse']['description'];
		                    echo '</td>';
		                    echo '<td>'.$this->Js->link(__('Schedule '),
								    '/course_schedules/manual_schedule_unscheduled/'.$ddv['UnschedulePublishedCourse']['published_course_id'].'/'.$ddv['UnschedulePublishedCourse']['type'],
								    array('update'=>'#ajax_schedule_'.$ddv['UnschedulePublishedCourse']['published_course_id'].'__'.$scsk,'evalScripts'=>true)).'</td>';
						    echo '<td  id="ajax_schedule_'.$ddv['UnschedulePublishedCourse']['published_course_id'].'__'.$scsk.'" ></td>';
		                    echo '</tr>';
		                    
		         
	       }
	    }
	  echo '</table>';	
	    
	}
}
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
