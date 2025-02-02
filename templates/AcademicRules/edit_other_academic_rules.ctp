<?php ?>
<div class="box"  >
     <div class="box-body" >
       <div class="row">
	  		<div class="large-12 columns">
	          <div class="otherAcademicRules form">
				<?php echo $this->Form->create('OtherAcademicRule'); ?>
				  <?php 
				  echo $this->Form->input('id');
				  ?>
			      <fieldset>
					<legend><?php echo __('Update Other Academic Rule'); ?></legend>
					<table cellspacing="0" cellpadding="0" class="fs13" >
					<tr>
						<td style="width:10%">Department:</td>
						<td style="width:25%"><?php echo $this->Form->input('department_id', array('id' => 'DepartmentId', 'class' => 'fs14','style' => 'width:300px', 'label' => false,'onchange'=>'updateCurriculum()', 'type' => 'select', 'options' => $departments, 'default' => $default_department_id)); ?></td>
						<td style="width:12%">Year Level:</td>
						<td style="width:53%"><?php echo $this->Form->input('year_level_id', array('id' => 'YearLevelId', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $yearLevels, 'default' => $default_year_level_id)); ?></td>
					</tr>
					<tr>
						<td style="width:10%">Program:</td>
						<td style="width:25%"><?php echo $this->Form->input('program_id', array('id' => 'ProgramId', 'class' => 'fs14', 'label' => false, 'type' => 'select','onchange'=>'updateCurriculum()', 'options' => $programs, 'default' => $default_program_id)); ?></td>
						
						<td style="width:12%">Program Type:</td>
						<td style="width:53%"><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramTypeId', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => $default_program_type_id)); ?></td>
					</tr>
					
	                <tr>
						<td style="width:10%">Curriculum:</td>
						<td style="width:25%"><?php echo $this->Form->input('curriculum_id', array('id' => 'CurriculumId','style'=>'width:250px;', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $curriculums)); ?></td>
						<td style="width:12%">Course Category:</td>
						<td style="width:53%"><?php echo $this->Form->input('course_category_id', array('id' => 'CourseCategoryId', 'class' => 'fs14', 'label' => false,'onchange'=>'updateCourseCategory()')); ?></td>
					</tr>
					
					<tr>
						<td style="width:10%">Academic Status:</td>
						<td style="width:25%"><?php echo $this->Form->input('academic_status_id', array('id' => 'AcademicStatusId', 'class' => 'fs14', 'label' => false)); ?></td>
						<td style="width:12%">Grade:</td>
						<td style="width:53%"><?php echo $this->Form->input('grade', array('id' => 'GradeId', 'class' => 'fs14', 'label' => false)); ?></td>
					</tr>
					<tr>
						<td style="width:10%">Number of courses:</td>
						<td style="width:25%"><?php echo $this->Form->input('number_courses', array('id' => 'NumberOfCourseId', 'class' => 'fs14', 'label' => false)); ?></td>
						
					</tr>
					
			      </table>

				
					</fieldset>
				<?php echo $this->Form->end(__('Submit')); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

 function updateCurriculum () {
           //serialize form data
            var formData = $("#DepartmentId").val()+'/'+$("#ProgramId").val();
         	 
			$("#CurriculumId").empty();
			$("#ProgramId").attr('disabled', true);
			$("#DepartmentId").attr('disabled', true);
						
			//DepartmentId ProgramId  ProgramTypeId
			
			//get form action
            var formUrl = '/curriculums/get_curriculum_combo/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#ProgramId").attr('disabled', false);
						$("#CurriculumId").attr('disabled', false);
						$("#DepartmentId").attr('disabled', false);
						
						$("#CurriculumId").empty();
						$("#CurriculumId").append(data);
						updateCourseCategory();
				},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			
			return false;
  }
  
  function updateCourseCategory() {
           
            //serialize form data
            var formData = $("#CurriculumId").val();
			$("#CurriculumId").attr('disabled', true);
			$("#CourseCategoryId").attr('disabled', true);
			
			//get form action
            var formUrl = '/curriculums/get_course_category_combo/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#CurriculumId").attr('disabled', false);
						$("#CourseCategoryId").attr('disabled', false);
			
						$("#CourseCategoryId").empty();
					    $("#CourseCategoryId").append('<option></option>');
						
						$("#CourseCategoryId").append(data);
					},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			return false;
}
</script>
