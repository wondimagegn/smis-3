<?php ?>

<div class="row">
<div class="large-12 columns">	

<?php 
echo $this->Form->create('ExamGrade',array('action'=>'data_entry_interface', "method"=>"POST"));
?>
    <?php 
       if (isset($student_section_exam_status) && !empty($student_section_exam_status)) {   
        	echo $this->element('student_basic');
        }
    ?> 
 <?php 
//get_published_add_course

 echo '<table>';
 echo '<tr><td>'.$this->Form->input('Student.college_id',array('label'=>'Select College/School/Center You want to Add Course.','empty'=>'--select college/school/center--','id'=>'college_id_1',
 'onchange'=>'updateDepartmentCollege(1)')).'</td></tr>'; 

 echo '<tr><td>'.$this->Form->input('Student.department_id',array('id'=>'department_id_1',
 'onchange'=>'updateSection(1)','empty'=>'--select--')).'</td></tr>';

 echo '<tr><td>'.$this->Form->input('Student.section_id',array('id'=>'section_id_1',
 'onchange'=>'updatePublishedCourse(1,"'.join(',',$addParamaters).'")')).'</td></tr>';

 echo '<tr><td>'.$this->Form->hidden('Student.studentnumber',array('value'=>$addParamaters['studentnumber'])).$this->Form->hidden('Student.semester',array('value'=>$addParamaters['semester'])).$this->Form->hidden('Student.acadamic_year',array('value'=>$addParamaters['academic_year'])).'</td></tr>';


echo '</table>';
 
 echo '<div id="get_published_add_courses_id_1"></div>';
 
 ?>
</div>
</div>
<a class="close-reveal-modal">&#215;</a>
