<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Drop Registered Courses'; ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<?php
				echo $this->Form->create('CourseDrop');
				if (isset($student_section_exam_status) && !empty($student_section_exam_status)) {
					echo $this->element('student_basic');
				}
				if (isset($coursesDrop)) {
					echo $this->element('course_drop_template');
				}
				echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>