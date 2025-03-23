<div class="row">
	<div class="large-12 columns">
		<h6 class="text-gray">Select the targeet section to move the selected student: </h6>
		<div class="row">
            <div class="large-12 columns">
                <fieldset style="margin: 5px;">
                    <div class="large-6 columns">
						<?= $this->Form->create('Section', array('controller' => 'sections', 'action' => 'section_move_update', "method" => "POST")); ?>
						<?= $this->Form->input('Selected_section_id', array('label' => 'Target Section: ', 'id' => 'Selected_section_id', 'type' => 'select', 'options' => $sections, 'empty' => "[ Select Section ]", 'style' => 'width:90%;')); ?>
						<?= $this->Form->hidden('student_id', array('value' => $student_id)); ?>
						<?= $this->Form->hidden('previous_section_id', array('value' => $previous_section_id)); ?>
					</div>
					<div class="large-6 columns">
						<br>
						<?= $this->Form->end(array('label' => __('Submit'), 'class' => 'tiny radius button bg-blue')); ?>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
</div>
<a class="close-reveal-modal">&#215;</a>