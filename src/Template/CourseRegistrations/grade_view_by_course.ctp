<?php echo $this->Form->create('CourseRegistration');?>
<script type="text/javascript">
function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}
function toggleViewFullId(id) {
	if($('#'+id).css("display") == 'none') {
		$('#'+id+'Img').attr("src", '/img/minus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Hide Filter');
		}
	else {
		$('#'+id+'Img').attr("src", '/img/plus2.gif');
		$('#'+id+'Txt').empty();
		$('#'+id+'Txt').append('Display Filter');
		}
	$('#'+id).toggle("slow");
}

function updateCourseList(id) {
	
            //serialize form data
			
			if(id=="d"){
			   var department_id = $("#department_id").val();
			} else if(id=="c") {
			   var college_id = $("#college_id").val();
			}
			var academic_year= $("#AcadamicYear").val().replace("/", "-");
			var semester=$("#Semester").val();
			var program_id=$("#Program").val();
			var program_type_id=$("#ProgramType").val();
            var formData = department_id+'~'+college_id+'~'+academic_year+'~'+semester+'~'+program_id+'~'+program_type_id;
			$("#department_id").attr('disabled', true);
			$("#college_id").attr('disabled', true);
			$("#AcadamicYear").attr('disabled', true);
		    $("#Semester").attr('disabled', true);
			$("#Program").attr('disabled', true);
			$("#ProgramType").attr('disabled', true);
			
			//get form action
            var formUrl = '/courseRegistrations/get_course_category_combo/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
				        $("#AcadamicYear").attr('disabled', false);
						$("#Semester").attr('disabled', false);
						$("#Program").attr('disabled',false);
						$("#ProgramType").attr('disabled',false);
					    $("#department_id").attr('disabled', false);
						$("#college_id").attr('disabled', false);
						if(id=="d"){
						  	$("#CourseId").empty();
					    	$("#CourseId").append('<option></option>');
							$("#CourseId").append(data);
						} else if(id=="c") {
						    $("#CourseId").empty();
					    	$("#CourseId").append('<option></option>');
							$("#CourseId").append(data);  
						}
						
					},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
		
			return false;
		
 }
 
function updateCourseListOnChangeofOtherField() {
	
            //serialize form data
			var formData='';
			var department_id=$("#department_id").val();
			var college_id= $("#college_id").val();
			var academic_year= $("#AcadamicYear").val().replace("/", "-");
			var semester=$("#Semester").val();
			var program_id=$("#Program").val();
			var program_type_id=$("#ProgramType").val();
            formData = department_id+'~'+college_id+'~'+academic_year+'~'+semester+'~'+program_id+'~'+program_type_id;
			$("#department_id").attr('disabled', true);
			$("#college_id").attr('disabled', true);
			$("#AcadamicYear").attr('disabled', true);
		    $("#Semester").attr('disabled', true);
			$("#Program").attr('disabled', true);
			$("#ProgramType").attr('disabled', true);
			
			//get form action
            var formUrl = '/courseRegistrations/get_course_category_combo/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
				        $("#AcadamicYear").attr('disabled', false);
						$("#Semester").attr('disabled', false);
						$("#Program").attr('disabled',false);
						$("#ProgramType").attr('disabled',false);
					    $("#department_id").attr('disabled', false);
						$("#college_id").attr('disabled', false);
						if(id=="d"){
						  	$("#CourseId").empty();
					    	$("#CourseId").append('<option></option>');
							$("#CourseId").append(data);
						} else if(id=="c") {
						    $("#CourseId").empty();
					    	$("#CourseId").append('<option></option>');
							$("#CourseId").append(data);  
						}
						
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
		  <div class="examResults index">			
				<div class="smallheading"><?php echo __('View exam result and grade. ');?></div>
				<div onclick="toggleViewFullId('ListPublishedCourse')">
				 <?php 
					if (!empty($studentExamGradeList)) {
						echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
						?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
						}
					else {
						echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
						?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
						}
				?>
			  </div>
		<div id="ListPublishedCourse" style="display:<?php echo (!empty($studentExamGradeList) ? 'none' : 'display'); ?>">
		<table cellspacing="0" cellpadding="0" class="fs14">
			<tr>
				<td style="width:15%">Academic Year:</td>
				<td style="width:25%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data,'onchange'=>'updateCourseListOnChangeofOtherField()')); ?></td>
				<td style="width:15%">Semester:</td>
				<td style="width:55%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'),
'onchange'=>'updateCourseListOnChangeofOtherField()')); ?></td>
			</tr>
			<tr>
				<td>Program:</td>
				<td><?php echo $this->Form->input('program_id', array('id' => 'Program', 
				'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 
				'default' => isset($program_id) ? $program_id :"",
'onchange'=>'updateCourseListOnChangeofOtherField()')); ?></td>
				<td>Program Type:</td>
				<td><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14','onchange'=>'updateCourseListOnChangeofOtherField()', 'label' => false, 'type' => 'select', 
'options' => $program_types)); ?></td>
			</tr>
			<tr>
					<?php if(!empty($departments)) { ?>
					<td>Department:</td>
					<td><?php
						
						 echo $this->Form->input('department_id', array('class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $departments, 'empty'=>'--Select Department--','onchange'=>'updateCourseList("d")','id'=>'department_id')); ?>
						
					</td>
				 <?php } ?>
				
				  <?php if(!empty($colleges)) { ?>
					<td>College:</td>
					<td><?php
						
						 echo $this->Form->input('college_id', array('id' => 'College', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $colleges, 'empty'=>'--Select Department--','onchange'=>'updateCourseList("c")','id'=>'college_id')); ?>
						
					</td>
				 <?php } ?>
			</tr>

			<tr>
					
					<td>Course:</td>
					<td><?php
						
						 echo $this->Form->input('course_id', array('id' => 'CourseId', 'class' => 'fs14', 'label' => false, 'type' => 'select')); ?>
							<?php echo $this->Form->hidden('page', array('value'=>1)); ?>
					</td>

				 <td>Sort By:</td>
					<td><?php
						
						echo $this->Form->input('sortby',array('div'=>false,'options'=>$sortOptions,'label'=>false));
 ?>
						
				</td>
				
			</tr>
			<tr>
				<td colspan="4">
				<?php echo $this->Form->submit(__('List  Courses'), array('name' => 'listStudentWithGrade', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
				</td>
			</tr>
		</table>
		</div>
<?php 
if(!empty($studentExamGradeList)) 
{
		echo $this->Form->submit(__('Export XLS', true), array('name' =>'viewPDF','class'=>'tiny radius button bg-blue', 'div' => false)); 
		?>
		
		<table cellpadding="0" cellspacing="0" class="student_list">
				<tr>
						
						<th style="width:4%">N<u>o</u></th>
						<th style="width:25%"><?php echo $this->Paginator->sort('Student.full_name','Student Name');?></th>
						<th style="width:15%"><?php echo $this->Paginator->sort('student_id','ID');?></th>
						<th style="width:10%"><?php echo $this->Paginator->sort('Student.gender','Sex');?></th>
			
						<th style="width:10%">Grade</th>
				</tr>
	<?php 
	$count = $this->Paginator->counter('{:start}');
	foreach ($studentExamGradeList as $lst) {
			
	?>
				<tr>
					    <td><?php echo $count++;?></td>
						<td>
							<?php
						echo $lst['Student']['full_name'];
			
						?>
						</td>
						<td>
					<?php
						echo $lst['Student']['studentnumber'];
			
						?>

					</td>
						<td>

				<?php
						echo $lst['Student']['gender'];
			
						?>
</td>
						<td>

				<?php
						if(!empty($lst['ExamGrade'])){
							echo $lst['ExamGrade'][0]['grade'];
						} else {
							echo '--';
						}
						?>
</td>

				</tr>

	<?php } ?>

		</table>
		<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>

<?php 

}
?>

	  </div> <!--- end of exam index class -->
  </div> <!--- end of large-12 columns -->
</div> <!--- end of row -->
</div> <!-- end of box-body -->
<?php 
$this->Form->end();
?>
