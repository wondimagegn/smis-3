<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?=  __('Announcements'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;">
					<hr>
					<?= $this->Form->Create('Page'); ?>
					<?php 
					if (isset($announcements) && !empty($announcements)) {
						foreach ($announcements as $k => $v) { ?>
							<article class="reading-nest">
								<h5><a href="#"><?= $v['Announcement']['headline']; ?></a></h5>
								<h6>Posted by: <a href="#"><?=  $v['User']['first_name'] . ' ' . $v['User']['last_name']; ?></a> on <?=  $this->Format->short_date($v['Announcement']['created']); ?></h6>
								<p><?=  $v['Announcement']['story']; ?></p>
							</article>
							<hr />
							<?php
						}
					} else { ?>
						<article class="reading-nest">
							<!-- <h5><a href="#">Recent Announcements</a></h5> -->
							<p class='centeralign_smallheading text-gray'>No recent announcements to show for now.</p>
						</article>
						<hr />
						<?php
					} ?>
				</div>
			</div>
		</div>
	</div>
</div>