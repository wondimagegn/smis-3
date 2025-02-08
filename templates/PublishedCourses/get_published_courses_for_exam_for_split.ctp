<?php 
if (!empty($list_of_courses)) {
   
    echo "<table id='fieldsForm'><tbody>";
	echo "<tr><th style='padding:0'> S.No </th>";
	echo "<th style='padding:0'> Select </th>";
	//echo "<th style='padding:0'> Select Course Type </th>";
	echo "<th style='padding:0'> Course Title </th>";
	echo "<th style='padding:0'> Course Code </th>";
   
	echo "<th style='padding:0'> Credit </th></tr>";
	$count=1;
	foreach ($list_of_courses as $key=>$list_of_course) {
		echo $this->Form->hidden('SectionSplitForExams.'.$list_of_course['Course']['id'].'.published_course_id',
			array('value'=>$list_of_course['PublishedCourse']['id'])); 
	   
		
		 echo "<tr><td>".$count++."</td><td>".$this->Form->checkbox('SectionSplitForExams.selected.' . 
			$list_of_course['Course']['id'])."</td>";
		
		 echo "<td>".$list_of_course['Course']['course_title']."</td>";
		 echo "<td>".$list_of_course['Course']['course_code']."</td>";
		 echo "<td>".$list_of_course['Course']['credit']."</td></tr>";
	}
	echo "<tr><td colspan='6'>".$this->Form->Submit('Split Selected Sections',array('div'=>false,'name'=>'split',

		'class'=>'tiny radius button bg-blue'))."</td></tr>";
	echo  "</table>";
} else {
	echo "<div class ='info-box info-message'> Please select section that have course for final exam.</div>";
} 
?>
