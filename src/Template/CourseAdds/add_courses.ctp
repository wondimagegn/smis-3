<?php 
echo $this->Form->create('CourseAdd');
?>
<script type='text/javascript'>
  
//Sub cat combo
function updateDepartmentCollege(id) {

           
            //serialize form data
            var formData = $("#college_id_"+id).val();
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled', true);
			$("#section_id_"+id).attr('disabled', true);
			$("#add_button_disable").attr('disabled',true);
			//get form action
            var formUrl = '/departments/get_department_combo/'+formData;
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
							var formUrl = '/sections/get_sections_by_dept/'+subCat;
							$.ajax({
								/*
								type: 'get',
								url: formUrl,
								data: subCat,
								*/
								type: 'post',
        url: formUrl,
        data: $('form')
            .serialize(),

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
			var formUrl = '/sections/get_sections_by_dept/'+formData;
			$.ajax({
				/*
				type: 'get',
				url: formUrl,
				data: formData,
				*/
				type: 'post',
        url: formUrl,
        data: $('form')
            .serialize(),

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

  function updatePublishedCourse (id) {
           //serialize form data
            var formData = $("#section_id_"+id).val();

			$("#college_id_"+id).attr('disabled', true);
			$("#section_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled', true);
            $("#add_button_disable").attr('disabled',true);			
			//get form action
            var formUrl = '/courseAdds/get_published_add_courses/'+formData;
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
                }
			});
			
			return false;
 }
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
		
    <?php 
 
       if (isset($student_section_exam_status) && !empty($student_section_exam_status)) {   
        	echo $this->element('student_basic');
        }
    ?>
    
 <?php 
 echo "<table><tr>";
 echo "<td>";
 $button_visible=0;   
 if (!empty($ownDepartmentPublishedForAdd)) {
            
    echo "<div class='smallheading'> List of courses published as an add to your section.</div>";
            echo "<table id='fieldsForm'><tbody>";
           
            echo "<tr><th style='padding:0'> S.No </th>";
             echo "<th style='padding:0'> Select </th>";
            echo "<th style='padding:0'> Course Title </th>";
            echo "<th style='padding:0'> Course Code </th>";
            echo "<th style='padding:0'> Lecture hour </th>";
            echo "<th style='padding:0'> Tutorial hour </th>"; 
            echo "<th style='padding:0'> Credit </th></tr>";
            $count=0;
            
            foreach ($ownDepartmentPublishedForAdd as $pk=>$pv) {
                 if ($pv['already_added'] == 0) {
                     $button_visible++;
                     echo $this->Form->hidden('CourseAdd.'.$count.'.published_course_id',array('value'=>$pv['PublishedCourse']['id']));
                     echo $this->Form->hidden('CourseAdd.'.$count.'.academic_year',array('value'=>$pv['PublishedCourse']['academic_year']));
                     echo $this->Form->hidden('CourseAdd.'.$count.'.semester',
                     array('value'=>$pv['PublishedCourse']['semester']));
                     
                     echo $this->Form->hidden('CourseAdd.'.$count.'.student_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['id']));//$student_section['Student']['studentnumber'];
                     if (empty($pv['PublishedCourse']['year_level_id']) || $pv['PublishedCourse']['year_level_id']==0) {
                              echo $this->Form->hidden('CourseAdd.'.$count.'.year_level_id',array('value'=>0));
                             
                     
                     } else {
                           echo $this->Form->hidden('CourseAdd.'.$count.'.year_level_id',array('value'=>$pv['PublishedCourse']['year_level_id']));
                      
                     }
                     
                     echo "<tr><td>".++$count."</td><td>".$this->Form->checkbox('CourseAdd.add.' . $pv['PublishedCourse']['id'])."</td><td>".$pv['Course']['course_title']."</td>";
                 } else {
                       echo "<tr><td>".++$count."</td><td>***</td><td>".$pv['Course']['course_title']."</td>";
                 
                 }
                 echo "<td>".$pv['Course']['course_code']."</td>";
                 echo "<td>".$pv['Course']['lecture_hours']."</td>";
                 echo "<td>".$pv['Course']['tutorial_hours']."</td>";
                 echo "<td>".$pv['Course']['credit']."</td></tr>";
            }
          //}
            echo "<tr><td colspan=7>*** are courses which is already added.</td></tr>";
            echo  "</table>";
 }
 echo "</td>";
 echo "<td>";
 echo '<table>';
 
 echo '<tr><td>'.$this->Form->input('Student.college_id',array('label'=>'Select College You want to Add Course.','empty'=>'--select college--','id'=>'college_id_1',
 'onchange'=>'updateDepartmentCollege(1)')).'</td></tr>'; 
 echo '<tr><td>'.$this->Form->input('Student.department_id',array('id'=>'department_id_1',
 'onchange'=>'updateSection(1)')).'</td></tr>';
 echo '<tr><td>'.$this->Form->input('Student.section_id',array('id'=>'section_id_1',
 'onchange'=>'updatePublishedCourse(1)')).'</td></tr></table>';
 
 echo '<div id="get_published_add_courses_id_1"></div>';
 echo "</td>";
 echo "</tr>";
 echo "</table>";
//if ($button_visible>0) {
echo $this->Form->submit('Add Selected',array('id'=>'add_button_disable','class'=>'tiny radius button bg-blue','div'=>false,'name'=>'add'));
   // echo $this->Form->end('Add Selected',array('id'=>'add_button_disable'));
 //}
 ?>

<?php 

?>            
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
      </div> <!-- end of box-body -->
</div><!-- end of box -->

