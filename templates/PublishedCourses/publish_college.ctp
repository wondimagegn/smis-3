<?php ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#publish_department_id").change(function(){
            //serialize form data
            var formData = $("#publish_department_id").val();
			$("#publish_curriculum_id").empty();
			//$("#publish_department_id").attr('disabled', true);
			$("#publish_curriculum_id").attr('disabled', true);
			$("#publish_department_id").attr('disabled', true);
			$("#disabled_publish").attr('disabled',true);
            var formUrl = '/curriculums/get_curriculum_combo/'+formData+'/'+'<?php echo PROGRAM_UNDEGRADUATE;?>'            
           
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
                        $("#disabled_publish").attr('disabled',false);
						$("#publish_curriculum_id").attr('disabled', false);
						$("#publish_department_id").attr('disabled', false);
						$("#publish_curriculum_id").empty();
						
						$("#publish_curriculum_id").append(data);
                },
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
            }); 
                
            return false;
        });
    });
</script>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<?php echo $this->Form->create('PublishedCourse');?>
<div class="publishedCourses form">
<?php 
   if (!isset($turn_off_search)) {
?>
<table cellpadding="0" cellspacing="0">
<?php 
echo "<tr><td colspan=2 class='smallheading'> Publish or Prepare Semester Courses For Department Unassigned Students.</td></tr>";
echo '<tr><td>'.$this->Form->input('PublishedCourse.academicyear',array(
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($defaultacademicyear)?$defaultacademicyear:'')).'</td>';
echo '<td>';
    echo  $this->Form->input('PublishedCourse.semester',array('options'=>array('I'=>'I','II'=>'II'),'empty'=>'--select semester--')).'</td>';
          ?>
<tr> 
	<?php 
            echo '<td>'.$this->Form->input('PublishedCourse.department_id',array('label'=>'Publish From  Department.','empty'=>"--Select Department--",'id'=>'publish_department_id')).'</td></tr>'; 
            ?>
    <tr> 
	<?php 
            echo '<td>'.$this->Form->input('PublishedCourse.curriculum_id',array('id'=>'publish_curriculum_id','label'=>'Curriculum.','empty'=>'--select curriculum--')).'</td></tr>'; 
            ?>
	<tr><td colspan=2><?php echo $this->Form->submit('Continue',array('name'=>'getsection','div'=>'false','id'=>'disabled_publish')); ?> </td>	
</tr></table>

<?php 

}
?> 

<table cellpadding="0" cellspacing="0">
<?php 

if (isset($turn_off_search) && !empty($sections)){
         
            echo '<tr><td class="smallheading"> Select section you want to publish/unpublish course</td></tr>'; 
            echo $this->Form->hidden('PublishedCourse.semester',array('value'=>$semester));
            echo $this->Form->hidden('PublishedCourse.program_id',array('value'=>$program_id));
            echo $this->Form->hidden('PublishedCourse.program_type_id',array('value'=>$program_type_id));
            echo $this->Form->hidden('PublishedCourse.academic_year',array('value'=>$academic_year));
            echo $this->Form->hidden('PublishedCourse.department_id',array('value'=>$department_id));
            echo $this->Form->hidden('PublishedCourse.curriculum_id',array('value'=>$curriculum_id));
            foreach($sections as $key=>$value) {
              
            echo "<tr><td>".$this->Form->input('Section.selected.'.$key, array('class'=>'candidatePublishCourse',
     'label'=>$value,'type'=>'checkbox','value'=>$key,'checked'=>isset($selectedsection) && in_array($key,$selectedsection)? 'checked':'')).'</td></tr>';
		
		    }
          // echo '<tr><td>'.$this->Form->submit('Next >>',array('name'=>'continuepublish','div'=>'false')).'</td></tr>'; 
            echo $this->Js->get("input.candidatePublishCourse")->event("change", 
            $this->Js->request(array('controller'=>'publishedCourses',
			'action'=>'publisheForUnassigned'), array(
						'update'=>"#candidate_published_course_list",
						'async' => true,
						'method' => 'post',
						'dataExpression'=>true,
						'data'=> $this->Js->serializeForm(array(
						'isForm' => false,
						'inline' => true
			))
		))
	);   
}

?>
</table>
<div id="candidate_published_course_list">
</div>
<?php
  echo $this->Form->end();
?>
</div>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<script type="text/javascript">
  
    $(document).ready(function() { 
  
 
        $(".candidatePublishCourse").each( function() {
                if ($(this).is(":checked")){
                  
                    $('#candidate_published_course_list').load('/publishedCourses/publisheForUnassigned/2');
                  
               }
        });
    });
</script>
