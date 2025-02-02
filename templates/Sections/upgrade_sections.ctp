<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">Upgrade Sections: (<?= (!empty($department_name) ? $department_name : (!empty($college_name) ? $college_name : '')); ?>)</span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('Section');  ?>

				<?php
				if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || ROLE_DEPARTMENT == $this->Session->read('Auth.User')['Role']['parent_id'] ) { ?>
					<div style="margin-top: -30px;">
						<?php
						if (empty($formatedSections)) { ?>
							<hr>
							<blockquote>
								<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
								<span style="text-align:justify;" class="fs14 text-gray">This tool will help you to upgrade sections to the next year level.<b style="text-decoration: underline;"><i> All published course grades for a section should be fully sumbitted in order to qualify for year level upgrade</i></b>.</span>
							</blockquote>
							<?php
						} ?>

						<hr>

						<div onclick="toggleViewFullId('ListPublishedCourse')">
							<?php
							if (!empty($formatedSections)) {
								echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
								<?php
							} else {
								echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
								<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
								<?php
							} ?>
						</div>

						<div id="ListPublishedCourse" style="display:<?= (!empty($formatedSections) ? 'none' : 'display'); ?>">
							<fieldset style="padding-bottom: 5px;padding-top: 5px;">
								<legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend>
								<div class="row">
									<div class="large-3 columns">
										<?= $this->Form->input('Section.academicyear', array('options' => $acyear_array_data, 'required', 'style' => 'width:90%')); ?>
									</div>
									<?php
									if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) { ?>
										<div class="large-3 columns">
											<?= $this->Form->input('Section.year_level_id', array('empty' => '[ All Year Levels ]', /* 'required', */ 'style' => 'width:90%')); ?>
										</div>
										<?php
									} ?>
									<div class="large-3 columns">
										<?= $this->Form->input('Section.program_id', array('empty' => "[ All Programs ]",  'style' => 'width:90%')); ?>
									</div>
									<div class="large-3 columns">
										<?= $this->Form->input('Section.program_type_id', array('empty' => "[ All Program Types ]", 'style' => 'width:90%')); ?>
									</div>
								</div>
							</fieldset>
							<?= $this->Form->Submit('Search', array('name' => 'search', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							<br>
						</div>
					</div>
					<hr>

					<?php
					$enableSubmitButton = 0;

					if (isset($formatedSections) && !empty($formatedSections)) { ?>

						<h6 id="validation-message_non_selected" class="text-red fs14"></h6>

						<?php
						//debug($formatedSections);
						foreach ($formatedSections as $fsk => $fsv) { ?>
							<h6 class="fs14 text-gray"><?= (!empty($this->data['Section']['program_id']) ? $programs[$this->data['Section']['program_id']] . ' ' : '') . (!empty($this->data['Section']['program_type_id']) ? ' ' . $program_types[$this->data['Section']['program_type_id']] . ' ' : '') . $fsk . ' year'; ?></h6>
							<div style="overflow-x:auto;">
								<table style="border: #CCC solid 2px" cellpadding="0" cellspacing="0" class="table">
									<?php
									if (isset($fsv['Upgradable']) && !empty($fsv['Upgradable'])) { ?>
										<thead>
											<tr>
												<td><h6 class="fs14 text-gray">Upgradeable Sections</h6></td>
											</tr>
										</thead>
										<tr>
											<td>
												<table cellpadding="0" cellspacing="0" class="table">
													<tbody>
														<?php
														foreach ($fsv['Upgradable'] as $ufsk => $ufsv) {
															$unqualified_count = 0;
															if (isset($unqualified_students_count[$ufsk]) && !empty($unqualified_students_count[$ufsk])) {
																$unqualified_count = count($unqualified_students_count[$ufsk]);
															} ?>
															<tr>
																<td class="vcenter" style="background-color: white;">
																	<div style="margin-left: 1%; margin-top: 1%;">
																		<?= $this->Form->input('Section.Upgradbale_Selected.' . $ufsk, array('class' => 'upgradableSelectedSection', 'type' => 'checkbox', 'value' => $ufsk, 'label' => $ufsv)); ?>
																		<?php
																		if ($unqualified_count != 0) {
																			echo ' (' . $this->HTML->link($unqualified_students_count[$ufsk] . ' unqualified students', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalUpgrade', 'data-reveal-ajax' => '/sections/get_modal_box/' . $ufsk )) . ')';
																		} ?>
																	</div>
																</td>
															</tr>
															<?php
														} ?>
													</tbody>
												</table>
											</td>
										</tr>
										<?php
										$enableSubmitButton++;
									}

									if (isset($fsv['Unupgradable']) && !empty($fsv['Unupgradable'])) { ?>
										<thead>
											<tr>
												<td class="font">The following list of sections do not qualify for year level upgrade</td>
											</tr>
										</thead>
										<tr>
											<td>
												<table cellpadding="0" cellspacing="0" class="table">
													<tbody>
														<?php
														foreach ($fsv['Unupgradable'] as $uufsk => $uufsv) { ?>
															<tr>
																<td style="background-color: white;" class="vcenter"><?= $uufsv; ?></td>
															</tr>
															<?php
														} ?>
													</tbody>
												</table>
											</td>
										</tr>
										<?php
									} ?>
								</table>
							</div>
							<hr>
							<?php
						} 

						if ($enableSubmitButton) { ?>
							<hr>
							<?= $this->Form->Submit('Upgrade Selected Sections', array('id' => 'upgradeSelected', 'name' => 'upgrade', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							<?php
						}

						if (isset($fsv['Unupgradable']) && !empty($fsv['Unupgradable'])) {  ?>
							<br>
							<?php
							if (isset($last_year_level_sections_count) && $last_year_level_sections_count) { ?>
								<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span><?= $last_year_level_sections_count ?> section(s) are in thier last year level according to section's attached curriculum or year levels available in your department and you can't upgrade these sections. You can update the section's attached curriculum course breakdown if you feel one or all of these sections need year level upgrade.</div>
								<?php
							} ?>
							<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Please check if all published course grades are sumbitted or check if there is a mass dropped/elective course in one of the semesters in <?= isset($this->request->data['Section']['academicyear']) && $this->request->data['Section']['academicyear'] ? $this->request->data['Section']['academicyear'] : '' ?> and unpublish the such courses from published courses if any.</div>
							<?php
						}
						
					} else if (empty($formatedSections) && !($isbeforesearch)) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>There is no section found to upgrade with the selected search criteria.</div>
						<?php
					}
				} ?>

				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="large-12 columns">
		<div id="myModalUpgrade" class="reveal-modal" data-reveal>

		</div>
	</div>
</div>

<script type="text/javascript">
	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none') {
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		} else {
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c' + obj.id).toggle("slow");
	}

	function toggleViewFullId(id) {
		if ($('#' + id).css("display") == 'none') {
			$('#' + id + 'Img').attr("src", '/img/minus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Hide Filter');
		} else {
			$('#' + id + 'Img').attr("src", '/img/plus2.gif');
			$('#' + id + 'Txt').empty();
			$('#' + id + 'Txt').append('Display Filter');
		}
		$('#' + id).toggle("slow");
	}

	var form_being_submitted = false;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#upgradeSelected').click(function() {
		
		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		if (!checkedOne) {
            alert('At least one section must be selected to upgrade year level.');
			validationMessageNonSelected.innerHTML = 'At least one section must be selected to upgrade year level.';
			return false;
		}

		if (form_being_submitted) {
			alert('Upgrading Selected Sections, please wait a moment...');
			$('#upgradeSelected').attr('disabled', true);
			return false;
		}

		//var confirmm = confirm('Are you sure you want to upgrade selected sections to the next year level?');

		if (!form_being_submitted) {
			$('#upgradeSelected').val('Upgrading Selected Sections...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}

	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}

</script>