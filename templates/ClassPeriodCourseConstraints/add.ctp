<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';
//Get Class Period from a given week day
function getclassperiod() {
            //serialize form data
            var weekday = $("#ajax_weekday").val();
$("#ajax_periods").attr('disabled', true);
$("#ajax_periods").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/class_period_course_constraints/get_periods/'+weekday;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: weekday,
                success: function(data,textStatus,xhr){
$("#ajax_periods").attr('disabled', false);
$("#ajax_periods").empty();
$("#ajax_periods").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
            return false;
 }
   //Get course type
function getCourseType() {
            //serialize form data
            var dept = $("#ajax_course").val();
$("#ajax_type").attr('disabled', true);
$("#ajax_type").empty().html('<img src="/img/busy.gif" class="displayed" >');
//get form action
            var formUrl = '/class_period_course_constraints/get_course_types/'+dept;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: dept,
                success: function(data,textStatus,xhr){
$("#ajax_type").attr('disabled', false);
$("#ajax_type").empty();
$("#ajax_type").append(data);
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
             
<div class="classPeriodCourseConstraints form">
<?php echo $this->Form->create('ClassPeriodCourseConstraint');?>
<div class="smallheading"><?php echo __('Add Class Period Course Constraints'); ?></div>

<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academicyear',array('label' => false,'type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:"",'empty'=>"--Select Academic Year--", 'style'=>'width:150PX')).'</td>';
		echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label'=>false,'id'=>'ajax_program', 'selected'=>isset($selected_program)?$selected_program:"",'empty'=>"--Select Program--", 'style'=>'width:150PX')).'</td>'; 
        echo '<td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label'=>false,'id'=>'ajax_program_type', 'selected'=>isset($selected_program_type)? $selected_program_type:"",'empty'=>"--Select Program Type--", 'style'=>'width:150PX')).'</td></tr>'; 
		if($role_id == ROLE_COLLEGE) {
			echo '<tr><td class="font"> Department</td>';  
			echo '<td>'. $this->Form->input('department_id',array('label'=>false, 'id'=>'ajax_department_class_period_course_constraints','selected'=>isset($selected_department)?$selected_department:"",'empty'=>'Pre/(Unassign Freshman)','style'=>'width:150PX')).'</td>';
			echo '<td class="font"> Year Level</td>';
            echo '<td id="ajax_year_level_class_period_course_constraints">'. $this->Form->input(
				'year_level_id',array('label'=>false,'id'=>'ajax_year_level_cpcc','selected'=>isset($selected_year_level)? $selected_year_level:"",'empty'=>'All','style'=>'width:150PX')).'</td>';  

        } else {
        	echo '<tr><td class="font"> Year Level</td>';
			echo '<td>'. $this->Form->input('year_level_id',array('label'=>false,'selected'=>isset($selected_year_level)?$selected_year_level:"",'empty'=>'All','style'=>'width:150PX')).'</td>';
		}
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II','III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"", 'empty'=>'--select semester--','style'=>'width:150PX')).'</td></tr>'; 
		
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','div'=>false,'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
		
	?> 
</table>
<?php 
	if (isset($sections_array)) { 
		$dropdown_data_array= array();
		foreach($sections_array as $sak=>$sav){
			$count = 1;
			foreach($sav as $sk=>$sv){
				$dropdown_data_array[$sak][$sv['published_course_id']]= ($sv['course_title'].' ('.$sv['course_code'].' - Cr.'.$sv['credit'].'(LTL:'.$sv['credit_detail'].'))');
			}
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
		//unset($selected_published_course_id);
		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr><td class="font">'.$this->Form->input('courses',array('id'=>'ajax_course', 'onchange'=>'getCourseType()', 'type'=>'select','options'=>$dropdown_data_array, 'selected'=>!empty($selected_published_course_id)?$selected_published_course_id:"",'empty'=>'---Please Select Course---', 'style'=>'width:300PX')).'</td>';
		echo '<td class="font">'.$this->Form->input('week_day',array('id'=>'ajax_weekday','type'=>'select','onchange'=>'getclassperiod()','empty'=>'---Please Select Week Day---','options'=>$week_days_array,'selected'=>isset($selected_week_day)?$selected_week_day:"", 'style'=>'width:200PX')).'</td></tr>';

		//echo '<tr><td colspan="2"><div id="ajax_periods"> Periods</div></td></tr>';
		?>
		<tr><td colspan="2"><div id="ajax_periods">
		<?php
			if(isset($fromadd_periods) && !empty($fromadd_periods)){
				echo '<table><tr><td colspan="3" class="font"> Select Periods</td></tr><tr>';
				foreach($fromadd_periods as $pk=>$pv){
		
					echo '<td>'.$this->Form->input('ClassPeriodCourseConstraint.Selected.'.$pv['PeriodSetting']['period'],array('type'=>'checkbox','value'=>$pv['ClassPeriod']['id'],'label'=>$pv['PeriodSetting']['period'].' ('.$this->Format->humanize_hour($pv['PeriodSetting']['hour']).')')).'</td>';
				}
				echo '</tr></table>';
			}
			?>
		</div></td></tr> 
		<?php
		echo '<tr><td>'.$this->Form->input('type',array('id'=>'ajax_type', 'type'=>'select','options'=>$courseTypes,'empty'=>'---Please Select Course Type---')).'</td>';
		echo '<td>'.$this->Form->input('active',array('label'=>'Option','type'=>'select','options'=>array(1=>'Assign',0=>'Do Not Assign'))).'</td></tr>';
		echo '<tr><td colspan="2">'.$this->Form->Submit('Submit',array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'submit')).'</td></tr>';
		echo '</table>';

		if(isset($classPeriodCourseConstraints)) {
			echo '<div class="smallheading">Already Recorded Class Period Course Constraints</div>';
			echo "<table style='border: #CCC solid 1px'>";
			echo "<tr><th style='border-right: #CCC solid 1px'>No.</th>

				<th style='border-right: #CCC solid 1px'>Published Course</th>

				<th style='border-right: #CCC solid 1px'>Section</th>

				<th style='border-right: #CCC solid 1px'>Week Day</th>

				<th style='border-right: #CCC solid 1px'>Period</th>

				<th style='border-right: #CCC solid 1px'>Type</th>

				<th style='border-right: #CCC solid 1px'>Option</th>

				<th style='border-right: #CCC solid 1px'>Action</th></tr>";
			$count = 1;
			foreach($classPeriodCourseConstraints as $classPeriodCourseConstraint){
				$week_day_name = null;
				switch($classPeriodCourseConstraint['ClassPeriod']['week_day']){
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
				if($classPeriodCourseConstraint['ClassPeriodCourseConstraint']['active'] == 1){
					$active = "Assign";
				} else {
					$active = "Do Not Assign";
				}
				echo "<tr><td style='border-right: #CCC solid 1px'>".$count++."</td><td style='border-right: #CCC solid 1px'>".
					$this->Html->link($classPeriodCourseConstraint['PublishedCourse']['Course']['course_code_title'], array('controller' => 'published_courses', 'action' => 'view', $classPeriodCourseConstraint['PublishedCourse']['Course']['id'])).
					"</td><td style='border-right: #CCC solid 1px'>".
					$classPeriodCourseConstraint['PublishedCourse']['Section']['name'].
					"</td><td style='border-right: #CCC solid 1px'>".
					$week_day_name.' ('.$classPeriodCourseConstraint['ClassPeriod']['week_day'].')'.
					"</td><td style='border-right: #CCC solid 1px'>".
					$classPeriodCourseConstraint['ClassPeriod']['PeriodSetting']['period'] .' ('.
					$this->Format->humanize_hour($classPeriodCourseConstraint['ClassPeriod']['PeriodSetting']['hour']).')'.
					"</td><td style='border-right: #CCC solid 1px'>".
					$classPeriodCourseConstraint['ClassPeriodCourseConstraint']['type'].
					"</td><td style='border-right: #CCC solid 1px'>".$active.
				"</td><td style='border-right: #CCC solid 1px'>".
				//$this->Html->link(__('View'), array('action' => 'view', $classPeriodCourseConstraint['ClassPeriodCourseConstraint']['id'])).'&nbsp;&nbsp;&nbsp;'. 
				$this->Html->link(__('Delete'), array('action' => 'delete', $classPeriodCourseConstraint['ClassPeriodCourseConstraint']['id'],"fromadd"),
					null, sprintf(__('Are you sure you want to delete?'), $classPeriodCourseConstraint['ClassPeriodCourseConstraint']['id'],"fromadd")).
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
