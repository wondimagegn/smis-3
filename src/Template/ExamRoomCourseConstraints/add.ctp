<?php ?>
<script type="text/javascript">
var image = new Image();
image.src = '/img/busy.gif';        
//Class Room Combo
    function updateclassroomcombo() {
            //serialize form data
            var formData = $("#ajax_class_room_block").val();
$("#ajax_class_room").empty();
$("#ajax_class_room").attr('disabled', true);
//get form action
            var formUrl = '/exam_room_course_constraints/get_class_rooms/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
$("#ajax_class_room").attr('disabled', false);
$("#ajax_class_room").empty();
$("#ajax_class_room").append(data);
				},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			return false;
}
</script> 
<div class="examRoomCourseConstraints form">
<?php echo $this->Form->create('ExamRoomCourseConstraint');?>
<div class="smallheading"><?php echo __('Add/Edit Exam Room Course Constraints'); ?></div>
<div class="font"><?php echo 'Institute/College: '.$college_name?></div>
<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academicyear',array('label' =>false, 'type'=>'select','options'=>$acyear_array_data,'selected'=>isset($selected_academicyear)?$selected_academicyear:"",'empty'=>"--Select Academic Year--",'style'=>'width:150PX')).'</td>';
        echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label' =>false,'selected'=>isset($selected_program)?$selected_program:"",'empty'=>"--Select Program--",'style'=>'width:150PX')).'</td>'; 
        echo '<td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label' =>false,'selected'=>isset($selected_program_type)?$selected_program_type:"",'empty'=>"--Select Program Type--",'style'=>'width:150PX')).'</td></tr>'; 
		if($role_id == ROLE_COLLEGE) {  
			echo '<tr><td class="font"> Department</td>';
			echo '<td>'. $this->Form->input('department_id',array('label' =>false, 'id'=>'ajax_department_exam_room_course_constraints','selected'=>isset($selected_department)?$selected_department:"",'empty'=>'Pre/(Unassign Freshman)','style'=>'width:150PX')).'</td>';
            echo '<td class="font"> Year Level</td>';
            echo '<td id="ajax_year_level_exam_room_course_constraints">'.$this->Form->input('year_level_id', array('label' =>false,'id'=>'ajax_year_level_ercc','selected'=>isset($selected_year_level)?$selected_year_level:"",'empty'=>'All','style'=>'width:150PX')).'</td>';  
        } else {
			echo '<tr><td class="font"> Year Level</td>';
			echo '<td>'. $this->Form->input('year_level_id',array('label' =>false,'selected'=>isset($selected_year_level)?$selected_year_level:"",'empty'=>'All','style'=>'width:150PX')).'</td>';
		}
		echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label' =>false,'options'=>array('I'=>'I','II'=>'II', 'III'=>'III'),'selected'=>isset($selected_semester)?$selected_semester:"",'empty'=>'--select semester--','style'=>'width:150PX')).'</td></tr>'; 
        echo '<tr><td colspan="6">'. $this->Form->Submit('Search',array('name'=>'search','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>'; 
		
	?> 
</table>
<?php 
	if (isset($sections_array)) { 
		$dropdown_course_data_array= array();
		foreach($sections_array as $sak=>$sav){
			$count = 1;
			foreach($sav as $sk=>$sv){
				$dropdown_course_data_array[$sak][$sv['published_course_id']]= ($sv['course_title'].' ('.$sv['course_code'].' - Cr.'.$sv['credit'].')');
			}
		}
		/*
		$dropdown_classroom_data_array = array();

		foreach($formated_classRooms as $fcrk=>$fcrv){
			foreach($fcrv as $crk=>$crv){
				$undergraduate = null;
				$postgraduate =null;
				$assign =null;
				if(!empty($crv[$undergraduate_program_name])){
					$undergraduate = "Undergraduate:";
					foreach($crv[$undergraduate_program_name] as $upnk=>$upnv){
						$undergraduate = $undergraduate .$upnv .', ';
					}
				}
				if(!empty($crv[$postgraduate_program_name])){
					if(!empty($undergraduate)){
						$postgraduate = "And Postgraduate:";
					} else {
						$postgraduate = " Postgraduate:";
					}
					foreach($crv[$postgraduate_program_name] as $ppnk=>$ppnv){
						$postgraduate = $postgraduate .$ppnv .', ';
					}
				}
				if(!empty($undergraduate) || !empty($postgraduate)){
					$assign = "Assign to - ";
				}
				$dropdown_classroom_data_array[$fcrk][$crv['id']] = ($crv['room_code'] .' ('.$assign.$undergraduate .' '.$postgraduate .')');
			}
		} */

		echo '<table cellpadding="0" cellspacing="0">';
		echo '<tr><td colspan="6" class="font">'.$this->Form->input('courses',array('type'=>'select','empty'=>'---Please Select Course---','options'=>$dropdown_course_data_array)).'</td></tr>';
		/*echo '<tr><td colspan="2" class="font">'.$this->Form->input('class_rooms',array('type'=>'select','empty'=>'---Please Select Class Room---','options'=>$dropdown_classroom_data_array)).'</td></tr>';*/
		echo '<tr><td class="font"> Class Room Block</td>';
		echo '<td>'.$this->Form->input('class_room_blocks',array('label' =>false, 'type'=>'select','id'=>'ajax_class_room_block','onchange'=>'updateclassroomcombo()', 'options'=>$formatted_class_room_blocks,'selected'=>isset($selected_class_room_block)?$selected_class_room_block:"", 'empty'=>"--Select Class Room Blocks--", 'style'=>'width:200PX')).'</td>';
		echo '<td class="font"> Class Room</td>';
	    echo '<td>'. $this->Form->input('class_rooms', array('label'=>false,'id'=>'ajax_class_room', 'onchange'=>'updateconstraints()','type'=>'select','options'=>$classRooms,'selected'=>isset($selected_class_rooms)?$selected_class_rooms:"",'empty'=>'---Select Class Rooms ---', 'style'=>'width:200PX')).'</td>';
		echo '<td class="font"> Option</td>';
		echo '<td>'.$this->Form->input('active',array('label'=>false,'type'=>'select','options'=>array(1=>'Assign',0=>'Do Not Assign'), 'style'=>'width:200PX')).'</td></tr>';
		echo '<tr><td colspan="6">'.$this->Form->Submit('Submit',array('div'=>false,'class'=>'tiny radius button bg-blue','name'=>'submit')).'</td></tr>';
		echo '</table>';
		
		if(isset($examRoomCourseConstraints)) {
			echo '<div class="smallheading">Already Recorded Exam Room Course Constraints</div>';
			echo "<table style='border: #CCC solid 1px'>";
			echo "<tr><th style='border-right: #CCC solid 1px'>S.N<u>o</u>.</th>
				<th style='border-right: #CCC solid 1px'>Course</th>
				<th style='border-right: #CCC solid 1px'>Section</th>
				<th style='border-right: #CCC solid 1px'>Exam Room</th>
				<th style='border-right: #CCC solid 1px'>Block</th>
				<th style='border-right: #CCC solid 1px'>Campus</th>
				<th style='border-right: #CCC solid 1px'>Option</th>
				<th style='border-right: #CCC solid 1px'>Action</th></tr>";
			$count = 1;
			foreach($examRoomCourseConstraints as $examRoomCourseConstraint){

				$active = null;
				if($examRoomCourseConstraint['ExamRoomCourseConstraint']['active'] == 1){
					$active = "Assign";
				} else {
					$active = "Do Not Assign";
				}
				echo "<tr><td style='border-right: #CCC solid 1px'>".$count++.
					"</td><td style='border-right: #CCC solid 1px'>".
					$this->Html->link($examRoomCourseConstraint['PublishedCourse']['Course']['course_code_title'], array('controller' => 'published_courses', 'action' => 'view', $examRoomCourseConstraint['PublishedCourse']['Course']['id'])).
					"</td><td style='border-right: #CCC solid 1px'>".
					$examRoomCourseConstraint['PublishedCourse']['Section']['name'].
					"</td><td style='border-right: #CCC solid 1px'>".
					$examRoomCourseConstraint['ClassRoom']['room_code'].
					"</td><td style='border-right: #CCC solid 1px'>".
					$examRoomCourseConstraint['ClassRoom']['ClassRoomBlock']['block_code'].
					"</td><td style='border-right: #CCC solid 1px'>".
					$examRoomCourseConstraint['ClassRoom']['ClassRoomBlock']['Campus']['name'].
					"</td><td style='border-right: #CCC solid 1px'>".$active.
				"</td><td style='border-right: #CCC solid 1px'>".
				$this->Html->link(__('Delete'), array('action' => 'delete', $examRoomCourseConstraint['ExamRoomCourseConstraint']['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $examRoomCourseConstraint['ExamRoomCourseConstraint']['id'],"fromadd")).
				"</td></tr>";
			}
			echo "</table>";
		}
	}

echo $this->Form->end();
?>
</div>
