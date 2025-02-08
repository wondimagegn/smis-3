<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Zone'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Html->script('amharictyping'); ?>
			<?= $this->Form->create('Zone', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
			<div class="large-12 columns" style="margin-top: -30px;">
				<hr>
			</div>
			<div class="large-12 columns">
				<div class="large-6 columns">
					<?php
					echo $this->Form->input('region_id', array('style' => 'width:90%', 'required', 'empty' => '[ Select Region ]'));
					echo $this->Form->input('name', array('style' => 'width:90%', 'placeholder' => 'Like: Gamo Zone', 'required', 'pattern' => '[a-zA-Z]+', 'label' => 'Friendy Zone Name <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Friendy Zone Name is required and must be a string.</small>'));
					echo $this->Form->input('zone', array('style' => 'width:90%', 'placeholder' => 'Like: GAMO ZONE', 'required', 'pattern' => '[A-Z]+', 'label' => 'Standard Zone Name (from MoE): <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Zone Standard Name is required and must be a string in all CAPS.</small>'));
					echo $this->Form->input('short', array('style' => 'width:90%', 'placeholder' => 'Like: GAM', 'required', 'pattern' => '[A-Z]+',  'label' => 'Short Name (from MoE): <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Zone short name must be a single word in all CAPS.</small>'));
					
					?>
				</div>
				<div class="large-6 columns">
					<?php
					echo $this->Form->input('zone_2nd_language', array('style' => 'width:90%', 'placeholder' => 'Like: ጋሞ ዞን', 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);"));
					echo $this->Form->input('priority_order' , array('style' => 'width:90%')); 
					echo '<br>' . $this->Form->input('active', array('type'=>'checkbox', 'checked' =>'checked')); 
					echo '<br>';
					?>
				</div>
			</div>
			<div class="large-12 columns">
				<hr>
				<?= $this->Form->end(array('label' => 'Add Region', 'id' => 'SubmitID', 'name' => 'saveIt',  'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>

<script>

	var form_being_submitted = false;

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Adding Region, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Adding Region...';
		form_being_submitted = true;
		return true;
	};


	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>