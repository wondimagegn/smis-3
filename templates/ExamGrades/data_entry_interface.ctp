<?php ?>
<script>
var image = new Image();
image.src = '/img/busy.gif';
var number_of_students = <?php echo (isset($publishedCourses['courses']) ? count($publishedCourses['courses']) : 0); ?>;
function check_uncheck(id) {
	var checked = ($('#'+id).attr("checked") == 'checked' ? true : false);
	
	for(i = 1; i <= number_of_students; i++) {
		$('#StudentSelection'+i).attr("checked", checked);
	}
}

$(document).ready(function () {
	$("#Section").change(function(){
		//serialize form data
		var s_id = $("#Section").val();
		window.location.replace("/exam_grades/<?php echo $this->action; ?>/"+s_id+"/"+$("#SemesterSelected").val());
	});
});

function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}

function toggleViewFullId(id) {
	if($('#'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Hide Filter');
		}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Display Filter');
		}
	$('#'+id).toggle("slow");
}

//Sub cat combo
function updateDepartmentCollege(id) {  
		     
            //serialize form data
            var formData = $("#college_id_"+id).val();
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled', true);
			$("#section_id_"+id).attr('disabled', true);
			$("#add_button_disable").attr('disabled',true);
			//get form action
            var formUrl = '/departments/get_department_combo/'+formData+'/'+1;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#department_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
						$("#department_id_"+id).empty();
						$("#department_id_"+id).append(data);
							//student lists
							var subCat = $("#department_id_"+id).val();
							$("#section_id_"+id).attr('disabled', true);	
							
							//get form action
							var formUrl = '/sections/get_sections_by_dept_data_entry/'+subCat;
							$.ajax({
								type: 'get',
								url: formUrl,
								data: subCat,
								success: function(data,textStatus,xhr){
										$("#section_id_"+id).attr('disabled', false);
										$("#add_button_disable").attr('disabled', false);
										
										$("#section_id_"+id).empty();
										$("#section_id_"+id).append(data);
										
								},
								error: function(xhr,textStatus,error){
										alert(textStatus);
								}
							});
							
					},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			
			return false;
      
}

//Sub cat combo
function updateSection(id) {
          
            //serialize form data
            var formData = $("#department_id_"+id).val();
			$("#section_id_"+id).attr('disabled', true);
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled',true);	
			$("#add_button_disable").attr('disabled',true);
					//get form action
			var formUrl = '/sections/get_sections_by_dept_data_entry/'+formData;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data,textStatus,xhr){
						$("#section_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
			            $("#department_id_"+id).attr('disabled',false);
			            $("#add_button_disable").attr('disabled',false);	
						$("#section_id_"+id).empty();
						$("#section_id_"+id).append(data);
						
				},
				error: function(xhr,textStatus,error){
						alert(textStatus);
				}
			});
			return false;
 }

  function updatePublishedCourse (id, addParams) {
           //serialize form data
            var formData = $("#section_id_"+id).val();
           
			$("#college_id_"+id).attr('disabled', true);
			$("#section_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled', true);
            $("#add_button_disable").attr('disabled',true);	
            $("#get_published_add_courses_id_"+id).empty().html('<img src="/img/busy.gif" class="displayed" >');
					
			//get form action
            var formUrl = '/examGrades/getPublishedAddCourses/'+formData+'/'+addParams;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#section_id_"+id).attr('disabled', false);
						$("#department_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
						$("#add_button_disable").attr('disabled',false);
						$("#get_published_add_courses_id_"+id).empty();
						$("#get_published_add_courses_id_"+id).append(data);
				},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
						$("#get_published_add_courses_id_"+id).empty();
					    $("#section_id_"+id).attr('disabled', false);
						$("#department_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
						$("#add_button_disable").attr('disabled',false);
                }
			});
			
			return false;
 }


</script>

<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="examGrades <?php echo $this->action; ?>">
<?php echo $this->Form->create('ExamGrade');?>
<div class="smallheading"><?php __('Student Data Entry Interface');?></div>

<p class="fs16">
                    <strong> Important Note: </strong> 
                    This tool will help you to enter student registration and grade. The system automatically identify the academic year and semester in which student not registred and grade not entered. The selected academic year and semester will be matched only if the student has registration and grade for that academic year and semester, and display those courses with corresponding grade.                    
</p>
<div onclick="toggleViewFullId('ListSection')"><?php 
	if (!empty($publishedCourses)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListSectionImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Display Filter</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListSectionImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Hide Filter</span><?php
		}
?></div>
<div id="ListSection" style="display:<?php echo (!empty($sections) ? 'none' : 'display'); ?>">
<table cellspacing="0" cellpadding="0" class="fs14">
	<tr>
		<td style="width:15%">Academic Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' =>$acyear_list, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
		<td style="width:15%">Semester:</td>
		<td style="width:50%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => (isset($semester_selected) ? $semester_selected : false))); 
		if(isset($semester_selected)) {
			echo $this->Form->input('semester_selected', array('id' => 'SemesterSelected', 'type' => 'hidden', 'value' => $semester_selected));
		}
		?></td>
	</tr>
	<tr>
		<td>Student Number:</td>
		<td>
		<?php echo $this->Form->input('studentnumber', 
array('id' => 'StudentNumber', 'class' => 'fs14','label' => false, 'type' => 'text')); ?>
		</td>
		<td colspan="2">
			<strong>
			<?php 
				if(!empty($student_academic_profile)) {
					echo $student_academic_profile['BasicInfo']['Student']['first_name'].' '.$student_academic_profile['BasicInfo']['Student']['middle_name'].' '. $student_academic_profile['BasicInfo']['Student']['last_name'].'('.$student_academic_profile['BasicInfo']['Department']['name'].')';
				 } 
				?>	
			</strong>
		</td>
	</tr>
	<tr>
		<td colspan="6">
		<?php echo $this->Form->submit(__('Get Courses', true), array('name' => 'listPublishedCourse', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
		</td>
	</tr>
</table>
</div>

<?php 
if(empty($publishedCourses)) {
?>
<p class="fs16">
   
                    
</p>
<?php 
}
else if(isset($publishedCourses) && !empty($publishedCourses)) {
	?>
	<div onclick="toggleViewFullId('Profile')"><?php 
	if (!empty($student_academic_profile)) {
		echo $this->Html->image('plus2.gif', array('id' => 'ListProfileImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListProfileTxt">Display Student Academic Profile</span><?php
		}
	else {
		echo $this->Html->image('minus2.gif', array('id' => 'ListProfileImg')); 
		?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListProfileTxt">Hide Student Academic Profile</span><?php
		}
?></div>
     <div id="Profile" style="display:<?php echo (!empty($student_academic_profile) ? 'none' : 'display'); ?>">
	<?php 
		
           echo $this->element('student_academic_profile');
	?>
     </div>
	<p class="fs13">Please select course/s and enter corresponding grade.</p>
	<table>
		<tr>
			<th style="width:10%">
<?php echo $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'div' => false, 'label' => false)); ?>Select All</th>
 			<th style="width:25%">Course Title</th>
			<th style="width:25%">Course Code</th>
			
			<th style="width:20%"><?php echo $student_academic_profile['Curriculum']['type_credit']=="ECTS Credit Point" ? "ECTS":"Credit" ?></th>
			<th style="width:10%">AY/Sem.</th>
			<th style="width:10%">Grade</th>
		</tr>
		<?php
		$st_count = 0;
		$checkBoxCount=0;
		// debug($publishedCourses);
		foreach($publishedCourses['courses'] as $key => $course) {
			$st_count++;
		   //	debug($course);
			?>
			<tr>
				<td><?php 

	 if(empty($course['Grade'])) {
			$checkBoxCount++;
			if($course['PublishedCourse']['prerequisiteFailed']) {
			
 
			} else {
				echo $this->Form->input('CourseRegistration.'.$st_count.'.gp', array('type' => 'checkbox','class'=>'checkbox1', 'label' => false, 'id' => 'StudentSelection'.$st_count));
					echo $this->Form->input('CourseRegistration.'.$st_count.'.student_id', 
array('type' => 'hidden', 'value' => $student_academic_profile['BasicInfo']['Student']['id']));	
            }	
	  }
				?></td>
				<td><?php echo $course['Course']['course_title']; ?></td>
	
				<td><?php echo $course['Course']['course_code']; ?></td>
		<td><?php echo $course['Course']['credit']; ?></td>
		<td><?php
			if(!empty($course['PublishedCourse']['academic_year'])) {
		 echo $course['PublishedCourse']['academic_year'].'/'.$course['PublishedCourse']['semester']; 
			} else {
		 echo $course['PublishedCourse']['academic_year'].'/'.$course['PublishedCourse']['semester']; 

			}
		?></td>
			
		<td>
		<?php 
if(empty($course['Grade'])) {

echo $this->Form->hidden('CourseRegistration.'.$st_count.'.academic_year', array('value'=>$course['PublishedCourse']['academic_year']));
if(isset($course['CourseRegistration'])) {
echo $this->Form->hidden('CourseRegistration.'.$st_count.'.id', array('value'=>$course['CourseRegistration']['id']));
}
echo $this->Form->hidden('CourseRegistration.'.$st_count.'.semester', array('value'=>$course['PublishedCourse']['semester']));

echo $this->Form->hidden('CourseRegistration.'.$st_count.'.year_level_id', array('value'=>$course['PublishedCourse']['year_level_id']));

echo $this->Form->hidden('CourseRegistration.'.$st_count.'.section_id', array('value'=>$course['PublishedCourse']['section_id']));


if(isset($course['Course']['grade_scale_id'])) {
echo $this->Form->hidden('CourseRegistration.'.$st_count.'.grade_scale_id', array('value'=>$course['Course']['grade_scale_id']));

} else if(isset($course['PublishedCourse']['grade_scale_id'])) {

echo $this->Form->hidden('CourseRegistration.'.$st_count.'.grade_scale_id', array('value'=>$course['PublishedCourse']['grade_scale_id']));

}


echo $this->Form->hidden('CourseRegistration.'.$st_count.'.published_course_id', array('value'=>$course['PublishedCourse']['id']));

echo $this->Form->input('CourseRegistration.'.$st_count.'.student_id', 
array('type' => 'hidden', 'value' => $student_academic_profile['BasicInfo']['Student']['id']));

}

?>
<?php 
 
$gradeList = array();
if(isset($course['Course']['GradeType']['Grade'])) {
   foreach($course['Course']['GradeType']['Grade'] as $key=>$value) {
	$gradeList[$value['grade']]=$value['grade'];
   } 
   $gradeList['NG']='NG';
		$gradeList['I']='I';
	  	$gradeList['W']='W';
        $gradeList['DO']='DO';
}

if(empty($course['Grade'])) {
   
echo $this->Form->input('CourseRegistration.'.$st_count.'.grade', array('label' => false,'type'=>'select','options'=>$gradeList,'empty'=>'select'));

} else {
	echo ''.$course['Grade']['grade'];
}

?>


		</td>
		
	</tr>
			<?php

			
		}
	 
	?>
     <tr>
		<td colspan="6">
			<?php 
			if($graduated>0){
			echo "Graduated student record, and archived!";
			} else {
		

  if(!empty($course['PublishedCourse']['academic_year'])) {
 echo $this->Html->link('Add Courses','#',array('data-animation'=>"fade",
'data-reveal-id'=>'myModalAdd','data-reveal-ajax'=>'/examGrades/getAddCoursesDataEntry/'.$student_academic_profile['BasicInfo']['Student']['id'].'/'.str_replace('/','-',$course['PublishedCourse']['academic_year']).'/'.$course['PublishedCourse']['semester']));
} else {
		
	echo $this->Html->link('Add Courses','#',array('data-animation'=>"fade",
'data-reveal-id'=>'myModalAdd','data-reveal-ajax'=>'/examGrades/getAddCoursesDataEntry/'.$student_academic_profile['BasicInfo']['Student']['id'].'/'.str_replace('/','-',$this->request->data['ExamGrade']['acadamic_year']).'/'.$this->request->data['ExamGrade']['semester']));

}
}
             ?>
		</td>
	</tr>

    <tr>
		<td colspan="6">
			<?php 
			if($graduated>0){
				echo "Graduated student record, and archived!";
			} else {
				if($checkBoxCount>0) {
				echo $this->Form->submit(__('Save ', true), array('name' => 'saveGrade','class'=>'tiny radius button bg-blue', 'div' => false)); 
				}

			}
?>
		</td>
	</tr>
	</table>
	
	<?php
	
}
?>
<?php echo $this->Form->end(); ?>
</div>
<div id="myModalAdd" class="reveal-modal" data-reveal>

</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
