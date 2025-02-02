<?php ?>
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

</script>
<?php echo $this->Form->create('CourseRegistration');?>
<div class="box">
     <div class="box-body">
       <div class="row">
	     <div class="large-12 columns">        
		  <div class="examResults index">			
				<div class="smallheading"><?php echo __('View exam result and grade. ');?></div>
				<div onclick="toggleViewFullId('ListPublishedCourse')">
				 <?php 
					if (!empty($publishedCourses)) {
						echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); 
						?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span><?php
						}
					else {
						echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); 
						?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span><?php
						}
				?>
			  </div>
		<div id="ListPublishedCourse" style="display:<?php echo (!empty($publishedCourses) ? 'none' : 'display'); ?>">
		<table cellspacing="0" cellpadding="0" class="fs14">
			<tr>
				<td style="width:15%">Academic Year:</td>
				<td style="width:25%"><?php echo $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($selected_acadamic_year) ? $selected_acadamic_year : (isset($academic_year) && !empty($academic_year) ? $academic_year : $defaultacademicyear)))); ?></td>
				<td style="width:15%">Semester:</td>
				<td style="width:55%"><?php echo $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:125px', 'label' => false, 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'default' => $semester)); ?></td>
			</tr>
			<tr>
				<td>Program:</td>
				<td><?php echo $this->Form->input('program_id', array('id' => 'Program', 
				'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $programs, 
				'default' => isset($program_id) ? $program_id :"" )); ?></td>
				<td>Program Type:</td>
				<td><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => $program_type_id)); ?></td>
			</tr>
			<tr>
				<td colspan="4">
				<?php echo $this->Form->submit(__('List Published Courses'), array('name' => 'listPublishedCourses', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
				</td>
			</tr>
		</table>
	</div>
		<?php
		if(!empty($publishedCourses)) {
		?>
		<table class="fs14">
			<tr>
				<td style="width:15%">Published Courses</td>
				<td colspan="3" style="width:85%">
		<?php
			echo $this->Form->input('published_course_id', array('id' => 'PublishedCourse', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $publishedCourses, 'default' => $published_course_combo_id));
		?>
				</td>
			</tr>
		</table>
		<?php
		}
		?>
	  </div> <!--- end of exam index class -->
  </div> <!--- end of large-12 columns -->
</div> <!--- end of row -->
</div> <!-- end of box-body -->
