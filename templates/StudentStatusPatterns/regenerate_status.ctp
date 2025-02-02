<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Regenerate Status By Published Course'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div class="examGrades manage_ng">
					<?= $this->Form->create('StudentStatusPattern'); ?>
					<?= $this->element('publish_course_filter_by_dept'); ?>
					<?= $this->Form->end(); ?>
				</div>

			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$("#PublishedCourse").change(function() {
			//serialize form data
			window.location.replace("/studentStatusPatterns/regenerate_status/" + $("#PublishedCourse").val());
		});
	});
</script>