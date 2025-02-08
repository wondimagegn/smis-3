<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Request for Clearance/Withdrawal'); ?></span>
		</div>
	</div>
    <div class="box-body">
    	<div class="row">
	  		<div class="large-12 columns">
				<div class="clearances form" style="margin-top: -30px;">
					<hr>

					<?php //echo $this->Form->create('Clearance',array('action' => 'add', 'type'=>'file'));?>
					<?= $this->Form->create('Clearance', array('name' => 'addClearance',  'id' => 'addClearance', 'type' => 'file', 'enctype' => 'multipart/form-data', /* 'data-abide', */)); ?>

					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<span style="text-align:justify;" class="fs15 text-black">The system will check if you have taken any properties from university and inform you to return the properties to concerned bodies before filling the clearnance.
							If you have not taken any properties from the university,  the system will forward your request to registrar for approval. <strong>The clearance will be final if the registrar confirmed your clearance as cleared.</strong>
						</span> 
						<ol class="fs14" style="padding-top:10px; margin-top:10px;">
							<li>If your request is withdrawal, the system will also process your clearnce too, please advise your department/advisor before filling withdrawl.</li>
							<li>Inorder to be considered in readmission application lists, registrar has to approve your clearnce first and accept your withdrawal.</li>
							<li>If Your request is clearnce, your clearnce application will accepted and considered cleared when registrar approved that your cleared else you have to contact the registrar.</li>
						</ol>
					</blockquote>
					<hr>

					<?php
					$yFrom = date('Y') - Configure::read('Calendar.clearanceWithdrawInPast');
					$yTo = date('Y') + Configure::read('Calendar.clearanceWithdrawInFuture');
					$options = array('clearance' => ' Clearance', 'withdraw' => ' Withdraw');
					$attributes = array('legend' => false, /* 'id' => 'requestType', */  /* 'label' => false, */ 'separator' => '<br/>'); 

					$showButton = 1;

					//debug(gethostbyaddr(gethostbyname(gethostname())));

					if (isset($there_is_pending_approval) && $there_is_pending_approval) {
						$showButton = 0; ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>You have a pending approval submitted on <?= $this->Time->format("M j, Y g:i A", $this->request->data['Clearance']['created'], NULL, NULL); ?>, wait until your request is approved or you can cancel your pending requests on <a href="<?= BASE_URL_HTTPS.'clearances'; ?>">Click here to view your requests</a></div>
						<?php
					} ;
					?>

					<div class="row">
						<div class="large-5 columns">
							<fieldset style="padding-bottom: 10px;">
								<legend>&nbsp;&nbsp; Clearance/Withdrawal Form &nbsp;&nbsp;</legend>
								<div class="row">
									<div class="large-12 columns">
										<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
										<label for="type">Request Type: </label><br>
										<?= $this->Form->hidden('student_id', array('value' => $student_section_exam_status['StudentBasicInfo']['id'])); ?>
										<?= $this->Form->radio('type',	$options,	$attributes); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<?=$this->Form->input('reason', array('id' => 'reason', 'pattern' => '[A-Za-z\s]+', 'after' => 'E.g. End of Academic Year, Graduation, Health Problem, Academic Dismissal, Social/Family/Personal Case', 'required', 'label' => 'Reason: ')); ?>
									</div>
								</div>
								<div class="row">
									<div class="large-12 columns">
										<br>
										<?=$this->Form->input('last_class_attended', array('label' => 'Last Date Class Attended: ',  'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo, 'style' => 'width:80px;', 'required')); ?>
										<?= $this->Form->hidden('request_date', array('value' => date('Y-m-d'))); ?>
									</div>
								</div>

								<?php
								//debug($this->data['Attachment']);
                                if (isset($this->data['Attachment']) && !empty($this->data['Attachment']) && isset($there_is_pending_approval) && $there_is_pending_approval) { ?>
                                    <div class="row">
										<div class="large-12 columns">
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <?php
												//debug($this->data['Attachment']);
                                                foreach ($this->data['Attachment'] as $cuk => $cuv) {
													if (isset($cuv['dirname']) && isset($cuv['basename'])) { ?>
														<tr>
															<td>PDF uploaded on: <?= $this->Time->format("M j, Y g:i A", $cuv['created'], NULL, NULL); ?></td>
														</tr>
														<tr>
															<td style="background-color: white;">
																<?php 
																if ($this->Media->file($cuv['dirname'] . DS . $cuv['basename'])) { ?>
																	<a href="<?= $this->Media->url($cuv['dirname'] . DS . $cuv['basename'], true); ?>" target=_blank>View Attachment</a><br>
																	<?= $cuv['basename']; ?> (<?= $size = $this->Number->toReadableSize($this->Media->size($this->Media->file($cuv['dirname'] . DS . $cuv['basename']))); ?>)
																	<?php // $this->Media->embed($this->Media->file($cuv['dirname'] . DS . $cuv['basename']), array('width' => '144', 'height' => '144'));  ?>
																	<?php
																} else { ?>
																	<span class=" text-red">Attachment not found or deleted</span>
																	<?php
																} ?>
															</td>
														</tr>
														<?php
													}
                                                } ?>
                                            </table>
                                        </div>
                                    </div>
                                    <?php
                                } else { ?>
									<div id="withdrawalAttachment" style="display:none;">
										<div class="row">
											<div class="large-12 columns">
												<?= $this->Form->input('Attachment.0.file', array('id' => 'attachmentPdf', 'type' => 'file', 'label' => 'Attach Supporting Documents (PDF only)', 'required' => ((REQUIRE_FILE_UPLOAD_FOR_WITHDRAWAL == 1  || REQUIRE_FILE_UPLOAD_FOR_CLEARANCE == 1) ? 'required' : false),  'accept' => 'application/pdf', /* 'accept' => '.pdf, .jpg, .jpeg, .png', */ /* 'multiple' => false */)); ?>
												<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Note: Incase of withdrawal, please attach supporting document for your withdrawal.</div>
											</div>
										</div>
									</div>
									<?php
								} ?>
								
								<?php //echo $this->element('attachments', array('plugin' => 'media', 'label' => 'Upload Supporting Document')); ?>
							</fieldset>
							
						</div>

						<div class="large-7 columns" style="padding-top: 25px;">
							<?= $this->element('student_basic'); ?>
						</div>
					</div>

					<?= ($showButton ? '<hr>' . $this->Form->Submit('Submit Request', array('name' => 'saveIt', 'id' => 'saveIt', 'class' => 'tiny radius button bg-blue')) : ''); ?>
					<?= $this->Form->end(); ?>
				</div>
	  		</div>
		</div>
    </div>
</div>

<script type="text/javascript">

	$('input[name="data[Clearance][type]"]').on('change', function() {
		if ($('#ClearanceTypeWithdraw').is(':checked')) {
			$('#withdrawalAttachment').show();
		} else if ($('#ClearanceTypeClearance').is(':checked')) {
			$('#withdrawalAttachment').hide();
		}
	});

	var empty_attachment = <?= (empty($this->data['Attachment']) ? 1 : 0) ?>;

	if ($("input[name='data[Clearance][type]']:checked").val() == 'withdraw' && empty_attachment) {
		$('#withdrawalAttachment').show();
	}

	var form_being_submitted = false;

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	/* $('#attachmentPdf').on('change', function() {
		if (this.files && this.files.length > 0) {
			alert('File is loaded: ' + this.files[0].name);
		} else {
			alert('No file selected');
		}
	}); */

	$('#saveIt').click(function() {
		
		var radios = document.querySelectorAll('input[type="radio"]');
		var checkedOne = Array.prototype.slice.call(radios).some(x => x.checked);

		var attachment_required = <?= ((REQUIRE_FILE_UPLOAD_FOR_WITHDRAWAL == 1 || REQUIRE_FILE_UPLOAD_FOR_CLEARANCE == 1) ? true : false); ?>;

		if (!checkedOne) {
			validationMessageNonSelected.innerHTML = 'Please select your request type!';
			return false;
		}

		if ($("#reason").val() == '') {
			validationMessageNonSelected.innerHTML = 'Please provide your reason!';
			alert('Please provide your reason!');
			$('#addClearance').find(':input[type="text"]').filter(function() { 
				return !this.value; 
			}).first().focus();
			return false;
		}

		var attachementPdf = $('#attachmentPdf');

		if (!attachementPdf.val() && attachment_required) {
			alert('Attachment is required and No file selected, please attach supporting documents!');
			$('#attachmentPdf').val().focus();
			return false;
		}

		if ($("#reason").val() == '') {
			validationMessageNonSelected.innerHTML = 'Please provide your reason!';
			alert('Please provide your reason!');
			$('#addClearance').find(':input[type="text"]').filter(function() { 
				return !this.value; 
			}).first().focus();
			return false;
		}

		var selectedValue = $("input[name='data[Clearance][type]']:checked").val();

		if (form_being_submitted) {
			alert('Submitting your ' + selectedValue + ' request, please wait a moment...');
			$('#saveIt').prop('diabled', true);
			return false;
		}

		//alert(selectedValue);

		var confirmm =  confirm('Are you sure you want to request a ' + selectedValue + ' request?');

		if (confirmm) {
			$('#saveIt').val('Submitting your ' + selectedValue + '...');
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
