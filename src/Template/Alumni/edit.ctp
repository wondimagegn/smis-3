<div class="alumni form">
<?php echo $this->Form->create('Alumnus'); ?>
	<fieldset>
		<legend><?php echo __('Edit Alumnus'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('full_name');
		echo $this->Form->input('father_name');
		echo $this->Form->input('region');
		echo $this->Form->input('woreda');
		echo $this->Form->input('kebele');
		echo $this->Form->input('housenumber');
		echo $this->Form->input('mobile');
		echo $this->Form->input('home_second_phone');
		echo $this->Form->input('email');
		echo $this->Form->input('studentnumber');
		echo $this->Form->input('sex');
		echo $this->Form->input('placeofbirth');
		echo $this->Form->input('fieldofstudy');
		echo $this->Form->input('age');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
