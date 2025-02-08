<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Edit Help Document'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>

				<?= $this->Form->create('Help', array('action' => 'edit', 'enctype' => 'multipart/form-data')); ?>

				<div class="row">
					<div class="large-4 columns">
						<?= $this->Form->input('id'); ?>
						<?= $this->Form->input('Help.title', array('label' => 'Title: ', 'style' => 'width: 90%;')); ?>
					</div>
					<div class="large-4 columns">
						<?= $this->Form->input('Help.document_release_date', array('label' => 'Document Realease Date: ', 'style' => 'width: 25%;')); ?>
					</div>
					<div class="large-2 columns">
						<?= $this->Form->input('Help.version', array('label' => 'Version: ', 'style' => 'width: 70%;')); ?>
					</div>
					<div class="large-2 columns">
						<?= $this->Form->input('order', array('label' => 'Order: ', 'style' => 'width: 70%;'));  ?>
					</div>
				</div>
				<div class="row">
					<div class="large-6 columns">
						<label>
						<h6 class='fs13 text-gray'>Target: </h6>
							<?= $this->Form->input('Help.target', array('label' => false, 'type' => 'select', 'multiple' => 'checkbox', 'options' => $roles)); ?>
						</label>
					</div>
					<div class="large-6 columns">
						<div style="margin-top: 20px; margin-bottom: 20px;">&nbsp;</div>

						<?php
						//debug($this->data); 
						$missing_attachment = 0;

						if (isset($this->data['Attachment']) && !empty($this->data['Attachment'])) {
							foreach ($this->data['Attachment'] as $ak => $av) {
								if ($av['group'] == 'attachment' && $av['model'] == 'Help') {
									if (!empty($av['dirname']) && !empty($av['basename'])) {
										if ($this->Media->file($av['dirname'] . DS . $av['basename'])) {
											echo $this->Media->embedAsObject($av['dirname'] . DS . $av['basename'], array('width'=>'420px','height'=>"594px"));
										} else {
											echo '<span class="rejected">Attachment not found</span>';
											$missing_attachment = 1;
										}
									}
									break;
								}
							}
						} else {
							echo $this->element('Media.attachments');
						} 
						
						if ($missing_attachment) {
							echo $this->element('Media.attachments');
						} ?>
				
						<?php //$this->element('attachments', array('plugin' => 'media', 'label' => false)); ?>
						<?php //echo $this->element('Media.attachments'); ?>
						
					</div>
				</div>

				<hr>
				<?= $this->Form->end(array('label' => 'Save Changes', 'class' => 'tiny radius button bg-blue')); ?>

			</div>
		</div>
	</div>
</div>
