<?php ?>
<script type='text/javascript'>
 function updateSubCurriculum(id) {
            //serialize form data
            var formData = $("#department_id_"+id).val();
			$("#curriculum_id_"+id).empty();
			$("#curriculum_id_"+id).attr('disabled', true);
			$("#course_id_"+id).attr('disabled', true);
			$("#equivalent_submit_button").attr('disabled',true);
			//get form action
            var formUrl = '/curriculums/get_curriculum_combo/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#curriculum_id_"+id).attr('disabled', false);
						$("#curriculum_id_"+id).empty();
						$("#curriculum_id_"+id).append(data);
							//Items list
							var subCat = $("#curriculum_id_"+id).val();
							$("#course_id_"+id).empty();
							//get form action
							var formUrl ='/curriculums/get_courses/'+subCat;
							$.ajax({
								type: 'get',
								url: formUrl,
								data: subCat,
								success: function(data,textStatus,xhr){
										$("#equivalent_submit_button").attr('disabled',false);
										$("#course_id_"+id).attr('disabled', false);
										$("#course_id_"+id).empty();
										$("#course_id_"+id).append(data);
								},
								error: function(xhr,textStatus,error){
										alert(textStatus);
								}
							});
							//End of items list
				},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			
			return false;
}
//update course combo 
function updateCourse(id) {
            //serialize form data
            var subCat = $("#curriculum_id_"+id).val();
			$("#course_id_"+id).attr('disabled', true);
			$("#equivalent_submit_button").attr('disabled',true);
			$("#course_id_"+id).empty();
			//get form action
            var formUrl = '/curriculums/get_courses/'+subCat;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: subCat,
                success: function(data,textStatus,xhr){
                        $("#equivalent_submit_button").attr('disabled',false);
						$("#course_id_"+id).attr('disabled', false);
						$("#course_id_"+id).empty();
						$("#course_id_"+id).append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            });
			
            return false;
}
</script>
<div class="equivalentCourses form">
<?php echo $this->Form->create('EquivalentCourse');?>
<div class="smallheading"><?php echo __('Edit Map Equivalent Course'); ?></div>
	 <?php 
	     echo "<table>"; 
	    echo $this->Form->input('id');
	    echo '<tr><td>'.$this->Form->input('course_for_substitued_id',
	     array('id'=>'course_id_1','empty'=>'--select course--','label'=>'Course To be equivalent')).'</td></tr>';
	   echo '<tr><td>'.$this->Form->input('course_be_substitued_id',array('id'=>'course_id_2')).'</td></tr>';
	
	     
	 echo '</table>';
	 
echo $this->Form->submit('Map',array('id'=>'equivalent_submit_button','div'=>'false'));

?>
</div>
<?php 

?>
