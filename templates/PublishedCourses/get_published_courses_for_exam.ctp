<?php 
if ($this->Session->read('list_of_courses')) {
	$list_of_courses=$this->Session->read('list_of_courses');
	$formatted_list_of_courses_per_sections = array();
	foreach($list_of_courses as $lck =>$lcv){
		$formatted_list_of_courses_per_sections[$lcv['Section']['name']][] = $lcv;
	}
}
if (!empty($list_of_courses)) {
   
    echo "<table id='fieldsForm'><tbody>";

	foreach($formatted_list_of_courses_per_sections as $flck =>$formatted_list_of_courses){
		echo "<tr><td colspan='5' class='smallheading'>".'Section Name: '.$flck."</td></tr>";
		$count=1;
		$options=array();
		foreach ($formatted_list_of_courses as $list_of_course) {
			echo $this->Form->hidden('MergedSectionsExams.'.$list_of_course['PublishedCourse']['id'].'.published_course_id',
				array('value'=>$list_of_course['PublishedCourse']['id'])); 
			echo $this->Form->hidden('MergedSectionsExams.'.$list_of_course['PublishedCourse']['id'].'.section_id',
				array('value'=>$list_of_course['Section']['id'])); 

			$options[$list_of_course['PublishedCourse']['id']]= $count++.'.'." ".$list_of_course['Course']['course_title'].
			 " - ".$list_of_course['Course']['course_code']." - Chr.".$list_of_course['Course']['credit'];

		}
		$attributes=array('legend'=>false,'label'=>false,'separator'=>'<br/>');
		echo "<tr><td class='font'>".$this->Form->radio('MergedSectionsExams.selectedcourses.'.$flck,$options,$attributes)."</td></tr>";
	}
	echo "<tr><td colspan=5>". $this->Form->input('merged_section_name')."</td></tr>";
	echo "<tr><td colspan=5>".$this->Form->Submit('Merge Selected Sections',array('div'=>false,'name'=>'merge',

		'class'=>'tiny radius button bg-blue'))."</td></tr>";
	echo  "</table>";
} else {
	echo "<div class ='info-box info-message'> Please select section that have course for final exam.</div>";
}

?>
