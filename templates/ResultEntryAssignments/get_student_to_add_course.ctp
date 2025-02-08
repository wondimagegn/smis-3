<?php ?>

<div class="row">
<div class="large-12 columns">	

<?php 
echo $this->Form->create('ResultEntryAssignment',array('action'=>'assign_result_entry', "method"=>"POST"));

echo "<table>";
/*
echo "<tr>";
	echo "<td>Section</td>";
	echo '<td>'.$publishedCourseDetail['Section']['name'].'By'.$publishedCourseDetail['GivenByDepartment']['name'].'</td>';
echo "</tr>";
*/
echo "<tr>";
	echo "<td>Course</td>";
	echo '<td>'.$publishedCourseDetail['Course']['course_title'].'('.$publishedCourseDetail['Course']['course_code'].')'.'In '.$publishedCourseDetail['PublishedCourse']['academic_year'].''.$publishedCourseDetail['PublishedCourse']['semester'].'</td>';
echo "</tr>";
/*
echo "<tr>";
	echo "<td>Instructor</td>";
	echo '<td>'.$publishedCourseDetail['CourseInstructorAssignment'][0]['Staff']['full_name'].'</td>';
echo "</tr>";
*/
echo "<tr>";
	echo "<td>Instructor</td>";
	echo '<td>'.$this->Form->input('ResultEntryAssignment.exam_published_course_id', array('id' => 'ExamPublishedCourse', 'label' => false, 'type' => 'select', 'class' => 'fs14', 'options' => $sectionsHaveSameCourses));
echo $this->Form->input('ResultEntryAssignment.studentnumber',array('label'=>'Student Number')).'</td>';
echo "</tr>";

echo "</table>";


echo $this->Form->submit(__('Assign Grade Entry'), array('name' => 'assignAddCourseGradeEntry', 'div' => false,'class'=>'tiny radius button bg-blue')); 
?>
  
</div>
</div>
<a class="close-reveal-modal">&#215;</a>
