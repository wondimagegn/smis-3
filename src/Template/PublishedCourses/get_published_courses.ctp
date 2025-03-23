<?php //echo $this->Form->create('MergedSectionsCourse');  ?>
<?php 

if ($this->Session->read('list_of_courses')) {
$list_of_courses=$this->Session->read('list_of_courses');
}
if (!empty($list_of_courses)) {
   
    echo "<table id='fieldsForm'><tbody>";
	echo "<tr><th style='padding:0'> S.No </th>";
	echo "<th style='padding:0'> Select </th>";
	echo "<th style='padding:0'> Course Title </th>";
	echo "<th style='padding:0'> Course Code </th>";
   
	echo "<th style='padding:0'> Credit </th></tr>";
	$count=1;
	foreach ($list_of_courses as $list_of_course) {
		echo $this->Form->hidden('MergedSectionsCourses.'.$list_of_course['Course']['id'].'.published_course_id',array('value'=>
		$list_of_course['PublishedCourse']['id'])); 
	   
		// echo "<td>".$this->Form->checkbox('CourseRegistration.drop.' . $pv['id'])."</td>"; 
		 echo "<tr><td>".$count++."</td><td>".$this->Form->checkbox('MergedSectionsCourses.selected.' . 
			$list_of_course['Course']['id'])."</td><td>".$list_of_course['Course']['course_title']."</td>";
		 echo "<td>".$list_of_course['Course']['course_code']."</td>";
		 echo "<td>".$list_of_course['Course']['credit']."</td></tr>";
	}
  //}
	echo "<tr><td colspan=5>". $this->Form->input('merged_section_name')."</td></tr>";
	echo "<tr><td colspan=5>".$this->Form->Submit('Merge Selected Sections',array('div'=>false,'name'=>'merge'))."</td></tr>";
	echo  "</table>";
} else {
	echo "<div class ='info-box info-message'> Please select section that have course for final exam.</div>";
} 

?>
