<?php 
if (!empty($list_of_courses)) {
   
    echo "<table id='fieldsForm'><tbody>";
	echo "<tr><th style='padding:0'> S.No </th>";
	echo "<th style='padding:0'> Select </th>";
	echo "<th style='padding:0'> Select Course Type </th>";
	echo "<th style='padding:0'> Course Title </th>";
	echo "<th style='padding:0'> Course Code </th>";
   
	echo "<th style='padding:0'> Credit </th></tr>";
	$count=1;
	foreach ($list_of_courses as $key=>$list_of_course) {
		
	   
		// echo "<td>".$this->Form->checkbox('CourseRegistration.drop.' . $pv['id'])."</td>"; 
		 if ($list_of_course['GradeSubmitted']>0) {
		    echo "<tr><td>".$count++."</td><td>***</td>";
		 
		    echo "<td>&nbsp;</td>";
		 
		 } else {
		   
		    echo $this->Form->hidden('SectionSplitForPublishedCourses.'.$list_of_course['Course']['id'].'.published_course_id',array('value'=>
		$list_of_course['PublishedCourse']['id'])); 
		
		    echo "<tr><td>".$count++."</td><td>".$this->Form->checkbox('SectionSplitForPublishedCourses.selected.' . 
			$list_of_course['Course']['id'])."</td>";
		 
		    echo "<td>".$this->Form->input('SectionSplitForPublishedCourse.type.'.$list_of_course['Course']['id'],
		 array('label'=>false,'type'=>'select','options'=>$course_type_array[$key]))."</td>";
		 
		 }
		 echo "<td>".$list_of_course['Course']['course_title']."</td>";
		 echo "<td>".$list_of_course['Course']['course_code']."</td>";
		 echo "<td>".$list_of_course['Course']['credit']."</td></tr>";
	}
	echo "<tr><td colspan='6'>*** are courses you can not split because result entry has already began.</td></tr>";

	echo "<tr><td colspan='6'>".$this->Form->Submit('Split Selected Sections',array('div'=>false,'name'=>'split'))."</td></tr>";
	echo  "</table>";
} 
?>
