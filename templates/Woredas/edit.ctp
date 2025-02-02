
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Edit Woreda: ' . (isset($this->data['Woreda']['name']) ? $this->data['Woreda']['name'] : '') . (isset($this->data['Woreda']['code']) ? '  (' . $this->data['Woreda']['code'] . ')' : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Html->script('amharictyping'); ?>
			<?= $this->Form->create('Woreda', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
			<div class="large-12 columns" style="margin-top: -30px;">
				<hr>
			</div>
			<div class="large-12 columns">
				<div class="large-6 columns">
					<?php
					echo $this->Form->hidden('id');
					echo $this->Form->input('zone_id', array('label' => 'Zone: ', 'style' => 'width:90%', 'required', 'empty' => '[ Select Zone ]'));
					echo $this->Form->input('name', array('style' => 'width:90%', 'placeholder' => 'Like: Arba Minch Zuria Woreda', 'required', 'pattern' => '[a-zA-Z]+', 'label' => 'Friendy Woreda Name: <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Friendy Woreda Name is required and must be a string.</small>'));
					echo $this->Form->input('woreda', array('style' => 'width:90%', 'placeholder' => 'Like: A/MINCH ZURIYA', 'required', 'pattern' => '[A-Z]+', 'label' => 'Standard Woreda Name (from MoE): <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Woreda Standard Name is required and must be a string in all CAPS.</small>'));
					echo $this->Form->input('code', array('style' => 'width:90%', 'placeholder' => 'Like: 1084', 'required', "type" => 'number', 'label' => 'Woreda Code (from MoE): <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Woreda code is required and must be a ony numbers.</small>'));
					?>
				</div>
				<div class="large-6 columns">
					<?php
					echo $this->Form->input('woreda_2nd_language', array('style' => 'width:90%', 'placeholder' => 'Like: አርባ ምንጭ ዙሪያ ወረዳ', 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);"));
					echo $this->Form->input('priority_order' , array('style' => 'width:90%')); 
					echo '<br>' . $this->Form->input('active', array('type'=>'checkbox')); 
					echo '<br>';
					?>
				</div>
			</div>
			<div class="large-12 columns">
				<hr>
				<?= $this->Form->end(array('label' => 'Save Changes', 'id' => 'SubmitID', 'name' => 'saveIt', 'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>

<script>

	var form_being_submitted = false;

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Saving Chnages, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Saving Chnages...';
		form_being_submitted = true;
		return true;
	};


	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>