<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-database-1"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Generate Database Backup'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<p style="text-align:justify;">
							<span class="fs16">This tool will enable you to generate database backup so that you can download and store in external device. Normally the system generates the backup dayly during midnight and you should use this tool only for exceptional cases which needs imediate backup before you perform critical tasks. <br> <u class="text-red">Please don't forget to store the backup to external backup device outside of the server room.</u></span>
						</p>
					</blockquote>
					<hr>
					<?= $this->Form->create('Backup'); ?>
					<?= $this->Form->submit(__('Generate Database Backup'), array('name' => 'generateDatabaseBackup', 'class' => 'tiny radius button bg-blue', 'style' => 'height:50px; font-size:18px', 'div' => false)); ?>
					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>