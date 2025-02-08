<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Latest Released Help Document'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>

				<?= $this->Form->create('Help', array('action' => 'add', 'enctype' => 'multipart/form-data')); ?>
			
				<div class="row">
					<div class="large-4 columns">
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
						<?= $this->element('Media.attachments'); ?>
						<?php //echo $this->Form->input('Attachment.0.file', array('type' => 'file')); ?>
					</div>
				</div>

				<hr>
				<?= $this->Form->end(array('label' => 'Add Manual', 'class' => 'tiny radius button bg-blue')); ?>

			</div>
		</div>
	</div>
</div>