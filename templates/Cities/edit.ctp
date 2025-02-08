<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Edit City: ' . (isset($this->data['City']['name']) ? $this->data['City']['name'] : '') . (isset($this->data['City']['short']) ? '  (' . $this->data['City']['short'] . ')' : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Html->script('amharictyping'); ?>
			<?= $this->Form->create('City', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
			<div class="large-12 columns" style="margin-top: -30px;">
				<hr>
			</div>
			<div class="large-12 columns">
				<div class="large-6 columns">
					<?php
					echo $this->Form->hidden('id');
					echo $this->Form->input('country_id', array('style' => 'width:90%', 'id' => 'country_id_1', 'required', 'default' => COUNTRY_ID_OF_ETHIOPIA, 'empty' => '[ Select Country ]'));
					echo $this->Form->input('region_id', array('style' => 'width:90%', 'id' => 'region_id_1', 'required', 'empty' => '[ Select Region ]'));
					echo $this->Form->input('zone_id', array('style' => 'width:90%',  'id' => 'zone_id_1','required', 'empty' => '[ Select Zone ]'));
					echo $this->Form->input('name', array('style' => 'width:90%', 'placeholder' => 'Like: Arba Minch', 'required', 'pattern' => '[a-zA-Z]+', 'label' => 'City Name <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">City Name is required and must be a string.</small>'));
					?>
				</div>
				<div class="large-6 columns">
					<?php
					echo $this->Form->input('short', array('style' => 'width:90%', 'placeholder' => 'Like: AMH', 'pattern' => '[A-Z]+',  'label' => 'Short Name (from MoE): <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">City short name must be a single word in all CAPS.</small>'));
					echo $this->Form->input('city', array('style' => 'width:90%', 'placeholder' => 'Like: ARBA MINCH', 'pattern' => '[A-Z]+', 'label' => 'Standard City  Name (from MoE): <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">City Standard Name is required and must be a string in all CAPS.</small>'));
					echo $this->Form->input('city_2nd_language', array('style' => 'width:90%', 'placeholder' => 'Like: አርባ ምንጭ', 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);"));
					echo $this->Form->input('priority_order' , array('style' => 'width:90%')); 
					echo '<br>' . $this->Form->input('active', array('type'=>'checkbox')); 
					echo '<br>';
					?>
				</div>
			</div>
			<div class="large-12 columns">
				<hr>
				<?= $this->Form->end(array('label' => 'Save Changes', 'id' => 'SubmitID', 'name' => 'saveIt',  'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>

<script>

$('#country_id_1').change(function() {
		
		var countryId = $(this).val();

		$('#region_id_1').attr('disabled', true);
		$('#zone_id_1').attr('disabled', true);
		$('#woreda_id_1').attr('disabled', true);
		$('#city_id_1').attr('disabled', true);

		if (countryId) {
			$.ajax({
				url: '/students/get_regions/' + countryId,
				type: 'get',
				data: countryId,
				success: function(data, textStatus, xhr) {
					$('#region_id_1').attr('disabled', false);
					$('#region_id_1').empty();
					$('#region_id_1').append(data);

					$('#zone_id_1').empty().append('<option value="">[ Select Zone ]</option>');
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;

		} else {
			$('#region_id_1').empty().append('<option value="">[ Select Region ]</option>');
			$('#zone_id_1').empty().append('<option value="">[ Select Zone ]</option>');
		}
	});

	// Load zone options based on selected region
	$('#region_id_1').change(function() {
		
		var regionId = $(this).val();

		$('#zone_id_1').attr('disabled', true);

		if (regionId) {
			$.ajax({
				url: '/students/get_zones/'+ regionId,
				type: 'get',
				data: regionId,
				success: function(data, textStatus, xhr) {
					$('#zone_id_1').attr('disabled', false);
					$('#zone_id_1').empty();
					$('#zone_id_1').append(data);
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});

			return false;
			
		} else {
			$('#zone_id_1').empty().append('<option value="">[ Select Zone ]</option>');
		}
	});

	var form_being_submitted = false;

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Saving Changes, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Saving Changes...';
		form_being_submitted = true;
		return true;
	};


	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>