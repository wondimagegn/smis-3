<?php 
?>
<script>
$(document).ready(function () {
	$("#PublishedCourse").change(function(){
		//serialize form data
		window.location.replace("/studentStatusPatterns/student_status_patterns/"+$("#PublishedCourse").val());
	});
});
</script>
<div class="examGrades manage_ng">
<?php echo $this->Form->create('StudentStatusPattern');?>
<div class="smallheading"><?php echo __('Status Regenerate');?></div>
<?php echo $this->element('publish_course_filter_by_dept'); ?>
<?php echo $this->Form->end(); ?>
</div>
